<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizVersion;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class QuizAttemptService
{
    public function startOrResume(Course $course, Lesson $lesson, User $user): QuizAttempt
    {
        $this->assertAccess($course, $lesson, $user);

        return DB::transaction(function () use ($lesson, $user): QuizAttempt {
            $quiz = Quiz::query()->where('lesson_id', $lesson->id)->lockForUpdate()->firstOrFail();
            $this->assertActiveQuiz($quiz);

            $attempt = QuizAttempt::query()
                ->where('user_id', $user->id)
                ->where('quiz_id', $quiz->id)
                ->where('status', 'in_progress')
                ->lockForUpdate()
                ->with('quizVersion')
                ->first();

            if ($attempt) {
                return $attempt;
            }

            $version = $this->resolveVersion($quiz);
            $completedAttempts = QuizAttempt::query()
                ->where('user_id', $user->id)
                ->where('quiz_id', $quiz->id)
                ->where('status', 'completed')
                ->count();

            abort_if(
                $version->max_attempts !== null && $completedAttempts >= $version->max_attempts,
                422,
                'Ban da het so lan lam quiz nay.',
            );

            return QuizAttempt::create([
                'user_id' => $user->id,
                'quiz_id' => $quiz->id,
                'quiz_version_id' => $version->id,
                'status' => 'in_progress',
                'presentation_order' => app(QuizAttemptPresentationService::class)->createSnapshot($version),
                'started_at' => now(),
            ])->load('quizVersion');
        });
    }

    /**
     * @return array{attempt: QuizAttempt, graded: array<string, mixed>, completed_now: bool}
     */
    public function submit(Course $course, Lesson $lesson, User $user, int $attemptId, array $submittedAnswers): array
    {
        $this->assertAccess($course, $lesson, $user);

        return DB::transaction(function () use ($lesson, $user, $attemptId, $submittedAnswers): array {
            $attempt = QuizAttempt::query()->lockForUpdate()->findOrFail($attemptId);

            abort_unless((int) $attempt->user_id === (int) $user->id, 403);

            $quiz = Quiz::query()->findOrFail($attempt->quiz_id);
            abort_unless((int) $quiz->lesson_id === (int) $lesson->id, 404);

            abort_unless($attempt->quiz_version_id !== null, 409, 'Quiz attempt does not have a bound quiz version.');
            $version = QuizVersion::query()
                ->with('questionMappings.questionVersion.options')
                ->findOrFail($attempt->quiz_version_id);
            abort_unless((int) $version->quiz_id === (int) $quiz->id, 409, 'Quiz attempt has an invalid quiz version.');

            $attempt->setRelation('quiz', $quiz);
            $attempt->setRelation('quizVersion', $version);

            if ($attempt->status === 'completed') {
                return [
                    'attempt' => $attempt->load('attemptAnswers'),
                    'graded' => $this->completedGrade($attempt, $version),
                    'completed_now' => false,
                ];
            }

            abort_unless($attempt->status === 'in_progress', 409, 'Quiz attempt is no longer in progress.');

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
                'status' => 'completed',
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

    public function findInProgress(Course $course, Lesson $lesson, User $user): ?QuizAttempt
    {
        $this->assertAccess($course, $lesson, $user);

        $quiz = Quiz::query()->where('lesson_id', $lesson->id)->first();
        if (! $quiz) {
            return null;
        }

        return QuizAttempt::query()
            ->where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->where('status', 'in_progress')
            ->with(['quiz', 'quizVersion'])
            ->first();
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

        return max(0, now()->diffInSeconds($attempt->started_at->copy()->addMinutes($minutes), false));
    }

    public function completedAttemptsCount(Quiz $quiz, User $user): int
    {
        return $quiz->attempts()
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();
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
        $attempt->loadMissing('attemptAnswers');
        $attemptAnswers = $attempt->attemptAnswers->groupBy('question_id');
        $answers = [];
        $questions = [];

        foreach ($version->questionMappings as $mapping) {
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
