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
                'started_at' => now(),
            ])->load('quizVersion');
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

        return app(QuizVersioningService::class)->projectVersion($attempt->quiz, $attempt->quizVersion);
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
}
