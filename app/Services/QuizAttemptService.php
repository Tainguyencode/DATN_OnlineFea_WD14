<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizVersion;
use App\Models\QuizVersionQuestionInvalidation;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class QuizAttemptService
{
    public const REVIEW_MODE_RESTRICTED = 'restricted';

    public const REVIEW_MODE_FULL = 'full';

    private const FINALIZED_STATUSES = [
        QuizAttempt::STATUS_COMPLETED,
        QuizAttempt::STATUS_TERMINATED,
        QuizAttempt::STATUS_EXPIRED,
    ];

    public function startOrResume(Course $course, Lesson $lesson, User $user): QuizAttempt
    {
        $this->assertAccess($course, $lesson, $user);

        return DB::transaction(function () use ($lesson, $user): QuizAttempt {
            $quiz = Quiz::query()->where('lesson_id', $lesson->id)->lockForUpdate()->firstOrFail();
            $this->assertActiveQuiz($quiz);

            $attempt = QuizAttempt::query()
                ->where('user_id', $user->id)
                ->where('quiz_id', $quiz->id)
                ->where('status', QuizAttempt::STATUS_IN_PROGRESS)
                ->lockForUpdate()
                ->with('quizVersion')
                ->first();

            if ($attempt && ! $this->expireIfDue($attempt)) {
                return $attempt;
            }

            $version = $this->resolveVersion($quiz);
            $seed = bin2hex(random_bytes(16));
            $questionIds = $version->questionMappings()->pluck('question_id')
                ->sortBy(fn ($questionId) => hash('sha256', $seed.':question:'.$questionId))
                ->values();
            if ($version->question_count) {
                $questionIds = $questionIds->take(min($version->question_count, $questionIds->count()));
            }
            $availability = $this->attemptAvailability($quiz, $user, $version);

            abort_if(
                ! $availability['has_remaining_attempts'],
                422,
                'Bạn đã hết số lần làm bài kiểm tra này.',
            );

            return QuizAttempt::create([
                'user_id' => $user->id,
                'quiz_id' => $quiz->id,
                'quiz_version_id' => $version->id,
                'random_seed' => $seed,
                'question_ids' => $questionIds->map(fn ($id) => (int) $id)->all(),
                'status' => QuizAttempt::STATUS_IN_PROGRESS,
                'presentation_order' => app(QuizAttemptPresentationService::class)->createSnapshot($version),
                'started_at' => now(),
            ])->load('quizVersion');
        });
    }

    /**
     * @return array{attempt: QuizAttempt, graded: array<string, mixed>, completed_now: bool}
     */
    public function submit(Course $course, Lesson $lesson, User $user, int $attemptId, array $submittedAnswers, ?int $remainingSeconds = null): array
    {
        $this->assertAccess($course, $lesson, $user);

        return DB::transaction(function () use ($lesson, $user, $attemptId, $submittedAnswers, $remainingSeconds): array {
            $attempt = QuizAttempt::query()->lockForUpdate()->findOrFail($attemptId);

            abort_unless((int) $attempt->user_id === (int) $user->id, 403);

            $quiz = Quiz::query()->findOrFail($attempt->quiz_id);
            abort_unless((int) $quiz->lesson_id === (int) $lesson->id, 404);

            abort_unless($attempt->quiz_version_id !== null, 409, 'Quiz attempt does not have a bound quiz version.');
            $version = QuizVersion::query()
                ->with('questionMappings.questionVersion.options', 'questionMappings.invalidations')
                ->findOrFail($attempt->quiz_version_id);
            abort_unless((int) $version->quiz_id === (int) $quiz->id, 409, 'Quiz attempt has an invalid quiz version.');

            $attempt->setRelation('quiz', $quiz);
            $attempt->setRelation('quizVersion', $version);

            $this->expireIfDue($attempt);
            if ($attempt->isFinalized()) {
                return [
                    'attempt' => $attempt->load('attemptAnswers'),
                    'graded' => $this->completedGrade($attempt, $version),
                    'completed_now' => false,
                ];
            }

            abort_unless($attempt->status === QuizAttempt::STATUS_IN_PROGRESS, 409, 'Quiz attempt is no longer in progress.');

            $graded = app(QuizService::class)->grade($attempt, $submittedAnswers);

            foreach ($graded['questions'] as $questionId => $result) {
                $answerData = [
                    'question_id' => (int) $questionId,
                    'question_version_id' => (int) $result['question_version_id'],
                    'is_correct' => (bool) $result['is_correct'],
                ];

                if ($result['selected_ids'] === []) {
                    $attempt->attemptAnswers()->create([...$answerData, 'answer_id' => null]);

                    continue;
                }

                foreach ($result['selected_ids'] as $answerId) {
                    $attempt->attemptAnswers()->create([...$answerData, 'answer_id' => $answerId]);
                }
            }

            $attempt->update([
                'status' => QuizAttempt::STATUS_COMPLETED,
                'termination_reason' => QuizAttempt::REASON_SUBMITTED,
                'remaining_seconds' => $remainingSeconds !== null ? max(0, $remainingSeconds) : $this->remainingTime($attempt),
                'score' => $graded['score'],
                'total_score' => $graded['total_score'],
                'percent' => $graded['percent'],
                'passed' => $graded['passed'],
                'answers' => $graded['answers'],
                'completed_at' => now(),
            ]);

            return [
                'attempt' => $attempt->fresh(['quiz', 'quizVersion', 'attemptAnswers']),
                'graded' => $graded,
                'completed_now' => true,
            ];
        });
    }

    /**
     * Terminate an in-progress attempt due to proctoring violations (tab switch, window blur, fullscreen exit...)
     *
     * @return array{attempt: QuizAttempt, graded: array<string, mixed>, completed_now: bool}
     */
    public function terminate(
        Course $course,
        Lesson $lesson,
        User $user,
        int $attemptId,
        string $reason,
        array $answers = [],
        ?int $remainingSeconds = null,
        ?string $ip = null,
        ?string $userAgent = null
    ): array {
        $this->assertAccess($course, $lesson, $user);

        return DB::transaction(function () use ($lesson, $user, $attemptId, $reason, $answers, $remainingSeconds, $ip, $userAgent): array {
            $attempt = QuizAttempt::query()->lockForUpdate()->findOrFail($attemptId);

            abort_unless((int) $attempt->user_id === (int) $user->id, 403);

            $quiz = Quiz::query()->findOrFail($attempt->quiz_id);
            abort_unless((int) $quiz->lesson_id === (int) $lesson->id, 404);

            abort_unless($attempt->quiz_version_id !== null, 409, 'Quiz attempt does not have a bound quiz version.');
            $version = QuizVersion::query()
                ->with('questionMappings.questionVersion.options', 'questionMappings.invalidations')
                ->findOrFail($attempt->quiz_version_id);

            $attempt->setRelation('quiz', $quiz);
            $attempt->setRelation('quizVersion', $version);

            $this->expireIfDue($attempt);
            if ($attempt->isFinalized()) {
                return [
                    'attempt' => $attempt->load('attemptAnswers'),
                    'graded' => $this->completedGrade($attempt, $version),
                    'completed_now' => false,
                ];
            }

            // Grade current answers up to termination point
            $answersToGrade = ! empty($answers) ? $answers : ($attempt->answers ?? []);
            $graded = app(QuizService::class)->grade($attempt, $answersToGrade);

            foreach ($graded['questions'] as $questionId => $result) {
                $answerData = [
                    'question_id' => (int) $questionId,
                    'question_version_id' => (int) $result['question_version_id'],
                    'is_correct' => (bool) $result['is_correct'],
                ];

                if ($result['selected_ids'] === []) {
                    $attempt->attemptAnswers()->create([...$answerData, 'answer_id' => null]);

                    continue;
                }

                foreach ($result['selected_ids'] as $answerId) {
                    $attempt->attemptAnswers()->create([...$answerData, 'answer_id' => $answerId]);
                }
            }

            $attempt->update([
                'status' => QuizAttempt::STATUS_TERMINATED,
                'termination_reason' => $reason,
                'remaining_seconds' => $remainingSeconds !== null ? max(0, $remainingSeconds) : $this->remainingTime($attempt),
                'score' => $graded['score'],
                'total_score' => $graded['total_score'],
                'percent' => $graded['percent'],
                'passed' => $graded['passed'],
                'answers' => $graded['answers'],
                'completed_at' => now(),
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);

            return [
                'attempt' => $attempt->fresh(['quiz', 'quizVersion', 'attemptAnswers']),
                'graded' => $graded,
                'completed_now' => true,
            ];
        });
    }

    public function saveProgress(Course $course, Lesson $lesson, User $user, int $attemptId, array $answers, ?int $remainingSeconds = null): QuizAttempt
    {
        $this->assertAccess($course, $lesson, $user);

        return DB::transaction(function () use ($lesson, $user, $attemptId, $answers, $remainingSeconds): QuizAttempt {
            $attempt = QuizAttempt::query()->lockForUpdate()->findOrFail($attemptId);
            abort_unless((int) $attempt->user_id === (int) $user->id, 403);
            abort_unless($attempt->quiz()->where('lesson_id', $lesson->id)->exists(), 404);
            if ($this->expireIfDue($attempt)) {
                return $attempt;
            }
            abort_unless($attempt->status === QuizAttempt::STATUS_IN_PROGRESS, 409, 'Quiz attempt is no longer in progress.');

            $attempt->update([
                'answers' => $answers,
                'remaining_seconds' => $this->remainingTime($attempt),
            ]);

            return $attempt;
        });
    }

    public function findInProgress(Course $course, Lesson $lesson, User $user): ?QuizAttempt
    {
        $this->assertAccess($course, $lesson, $user);

        $quiz = Quiz::query()->where('lesson_id', $lesson->id)->first();
        if (! $quiz) {
            return null;
        }

        $attempt = QuizAttempt::query()
            ->where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->where('status', QuizAttempt::STATUS_IN_PROGRESS)
            ->with(['quiz', 'quizVersion'])
            ->first();

        if ($attempt && $attempt->quizVersion?->time_limit_minutes && $this->remainingTime($attempt) === 0) {
            $attempt->update([
                'status' => QuizAttempt::STATUS_EXPIRED,
                'termination_reason' => QuizAttempt::REASON_TIME_EXPIRED,
                'remaining_seconds' => 0,
                'completed_at' => now(),
            ]);

            return null;
        }

        return $attempt;
    }

    public function resolveVersion(Quiz $quiz): QuizVersion
    {
        $this->assertActiveQuiz($quiz);

        return app(QuizVersioningService::class)->currentPublished($quiz);
    }

    public function projectQuiz(QuizAttempt $attempt): Quiz
    {
        $attempt->loadMissing(['quiz', 'quizVersion']);
        abort_unless($attempt->quiz && $attempt->quizVersion, 404);

        $projected = app(QuizVersioningService::class)->projectVersion($attempt->quiz, $attempt->quizVersion);

        return app(QuizAttemptPresentationService::class)->projectQuiz($projected, $attempt);
    }

    public function remainingTime(QuizAttempt $attempt): ?int
    {
        $attempt->loadMissing('quizVersion');
        $minutes = $attempt->quizVersion?->time_limit_minutes;

        if (! $minutes || ! $attempt->started_at) {
            return null;
        }

        return max(0, (int) now()->diffInSeconds($attempt->started_at->copy()->addMinutes($minutes), false));
    }

    private function expireIfDue(QuizAttempt $attempt): bool
    {
        if ($attempt->status !== QuizAttempt::STATUS_IN_PROGRESS || $this->remainingTime($attempt) !== 0) {
            return false;
        }

        $attempt->update([
            'status' => QuizAttempt::STATUS_EXPIRED,
            'termination_reason' => QuizAttempt::REASON_TIME_EXPIRED,
            'remaining_seconds' => 0,
            'score' => 0,
            'total_score' => 0,
            'percent' => 0,
            'passed' => false,
            'completed_at' => now(),
        ]);

        return true;
    }

    public function completedAttemptsCount(Quiz $quiz, User $user): int
    {
        return $quiz->attempts()
            ->where('user_id', $user->id)
            ->whereIn('status', self::FINALIZED_STATUSES)
            ->count();
    }

    /**
     * Canonical attempt-cap state. A new attempt always uses the current
     * published version; an existing in-progress attempt remains resumable.
     *
     * @return array{
     *     max_attempts: ?int,
     *     attempts_used: int,
     *     remaining_attempts: ?int,
     *     has_in_progress_attempt: bool,
     *     has_remaining_attempts: bool
     * }
     */
    public function attemptAvailability(Quiz $quiz, User $user, ?QuizVersion $currentVersion = null): array
    {
        $currentVersion ??= app(QuizVersioningService::class)->currentPublished($quiz);
        abort_unless((int) $currentVersion->quiz_id === (int) $quiz->id, 409, 'Quiz version does not belong to this quiz.');

        $attemptsUsed = $this->completedAttemptsCount($quiz, $user);
        $hasInProgressAttempt = $quiz->attempts()
            ->where('user_id', $user->id)
            ->where('status', QuizAttempt::STATUS_IN_PROGRESS)
            ->exists();
        $maxAttempts = $currentVersion->max_attempts !== null
            ? (int) $currentVersion->max_attempts
            : null;
        $remainingAttempts = $maxAttempts === null
            ? null
            : max(0, $maxAttempts - $attemptsUsed);

        return [
            'max_attempts' => $maxAttempts,
            'attempts_used' => $attemptsUsed,
            'remaining_attempts' => $remainingAttempts,
            'has_in_progress_attempt' => $hasInProgressAttempt,
            'has_remaining_attempts' => $hasInProgressAttempt
                || $maxAttempts === null
                || $attemptsUsed < $maxAttempts,
        ];
    }

    /**
     * @return array{
     *     review_mode: 'restricted'|'full',
     *     review_restriction_reason: null|'attempts_remaining'|'abnormal_end',
     *     has_remaining_attempts: bool,
     *     max_attempts: ?int,
     *     attempts_used: ?int,
     *     remaining_attempts: ?int,
     *     has_in_progress_attempt: bool
     * }
     */
    public function reviewPolicy(QuizAttempt $attempt, User $viewer): array
    {
        $attempt->loadMissing('quiz');
        abort_unless($attempt->quiz, 404);

        if ($viewer->isAdmin() || $viewer->isInstructor()) {
            return [
                'review_mode' => self::REVIEW_MODE_FULL,
                'review_restriction_reason' => null,
                'has_remaining_attempts' => false,
                'max_attempts' => null,
                'attempts_used' => null,
                'remaining_attempts' => null,
                'has_in_progress_attempt' => false,
            ];
        }

        abort_unless($viewer->isStudent() && (int) $attempt->user_id === (int) $viewer->id, 403);
        abort_if($attempt->status === QuizAttempt::STATUS_IN_PROGRESS, 409, 'Không thể xem lại khi lượt làm bài vẫn đang diễn ra.');
        abort_unless($attempt->isFinalized(), 409, 'Lượt làm bài chưa ở trạng thái có thể xem lại.');

        $availability = $this->attemptAvailability($attempt->quiz, $viewer);
        $isNormallySubmitted = $attempt->status === QuizAttempt::STATUS_COMPLETED
            && $attempt->termination_reason === QuizAttempt::REASON_SUBMITTED;
        $isFullReview = $isNormallySubmitted && ! $availability['has_remaining_attempts'];

        return [
            ...$availability,
            'review_mode' => $isFullReview
                ? self::REVIEW_MODE_FULL
                : self::REVIEW_MODE_RESTRICTED,
            'review_restriction_reason' => $isFullReview
                ? null
                : ($isNormallySubmitted ? 'attempts_remaining' : 'abnormal_end'),
        ];
    }

    public function assertAccess(Course $course, Lesson $lesson, User $user): void
    {
        abort_unless($user->isStudent(), 403);
        abort_unless((int) $lesson->course_id === (int) $course->id, 404);
        abort_unless($lesson->type === Lesson::TYPE_QUIZ, 404);
        abort_unless($course->isPublished(), 404);
        abort_unless(
            Enrollment::query()
                ->where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->withLearningAccess()
                ->exists(),
            403,
        );
    }

    private function assertActiveQuiz(Quiz $quiz): void
    {
        abort_unless(app(QuizContentService::class)->isEffectivelyActive($quiz), 404);
    }

    /** @return array<string, mixed> */
    private function completedGrade(QuizAttempt $attempt, QuizVersion $version): array
    {
        $attempt->loadMissing('attemptAnswers', 'quizVersion.questionMappings.invalidations');
        $attemptAnswers = $attempt->attemptAnswers->groupBy('question_id');
        $answers = [];
        $questions = [];

        $allowedQuestionIds = collect($attempt->question_ids ?? [])->map(fn ($id) => (int) $id);
        $mappings = $version->questionMappings
            ->when($allowedQuestionIds->isNotEmpty(), fn ($items) => $items->whereIn('question_id', $allowedQuestionIds));

        foreach ($mappings as $mapping) {
            $questionId = (int) $mapping->question_id;
            $questionVersion = $mapping->questionVersion;
            abort_unless($questionVersion && (int) $questionVersion->question_id === $questionId, 409, 'Quiz version has an invalid question composition.');

            $selectedIds = $attemptAnswers->get($questionId, collect())
                ->pluck('answer_id')
                ->filter(fn ($answerId) => $answerId !== null)
                ->map(fn ($answerId) => (int) $answerId)
                ->unique()
                ->values()
                ->all();
            $correctIds = $questionVersion->options
                ->where('is_correct', true)
                ->pluck('id')
                ->map(fn ($answerId) => (int) $answerId)
                ->values()
                ->all();

            $answers[$questionId] = $selectedIds;
            $questions[$questionId] = [
                'question_version_id' => (int) $questionVersion->id,
                'selected_ids' => $selectedIds,
                'correct_ids' => $correctIds,
                'is_correct' => $attemptAnswers->get($questionId, collect())->contains('is_correct', true),
                'is_excluded' => $mapping->invalidations
                    ->contains('status', QuizVersionQuestionInvalidation::STATUS_ACTIVE),
            ];
        }

        return [
            'score' => (int) $attempt->score,
            'total_score' => (int) $attempt->total_score,
            'percent' => (float) $attempt->percent,
            'passed' => (bool) $attempt->passed,
            'answers' => $answers,
            'questions' => $questions,
        ];
    }
}
