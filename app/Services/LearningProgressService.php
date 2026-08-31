<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LearningProgressService
{
    public function recordLessonProgress(
        int $userId,
        Course $course,
        Lesson $lesson,
        int $watchedSeconds = 0,
        ?int $durationSeconds = null,
        bool $forceCompleted = false,
        ?bool $statusOverride = null,
    ): array {
        return DB::transaction(function () use ($userId, $course, $lesson, $watchedSeconds, $durationSeconds, $forceCompleted, $statusOverride) {
            $enrollment = Enrollment::query()
                ->where('user_id', $userId)
                ->where('course_id', $course->id)
                ->withLearningAccess()
                ->lockForUpdate()
                ->firstOrFail();

            User::where('id', $userId)->update([
                'last_learning_at' => now(),
                'engagement_email_stage' => 0,
            ]);

            $lessonIds = $this->courseLessonIds($course);
            abort_unless($lessonIds->contains((int) $lesson->id), 404);

            $durationSeconds = max(0, (int) ($durationSeconds ?? $lesson->duration_seconds ?? $lesson->duration ?? 0));
            $threshold = $course->requiredVideoPercent();

            $existing = LessonProgress::query()
                ->where('user_id', $userId)
                ->where('lesson_id', $lesson->id)
                ->lockForUpdate()
                ->first();

            $previousWatched = (int) ($existing?->watched_seconds ?? 0);
            if ($lesson->type === Lesson::TYPE_DOCUMENT && $forceCompleted && ! $existing?->is_completed) {
                $startedAt = \Illuminate\Support\Facades\Cache::get('reading-start:'.$userId.':'.$lesson->id);
                abort_unless(is_numeric($startedAt) && now()->timestamp - (int) $startedAt >= 30,
                    422, 'Vui lòng mở bài đọc ít nhất 30 giây trước khi hoàn thành.');
            }
            $watchedSeconds = max($previousWatched, min($watchedSeconds, $durationSeconds > 0 ? $durationSeconds : $watchedSeconds));

            if ($lesson->type === Lesson::TYPE_QUIZ) {
                $quizPassed = $lesson->quiz?->hasPassedAttemptFor($userId) ?? false;
                $progressPercent = $quizPassed ? 100 : 0;
                $completed = $quizPassed;
            } else {
                $progressPercent = $durationSeconds > 0
                    ? min(100, round(($watchedSeconds / $durationSeconds) * 100, 2))
                    : ($forceCompleted ? 100 : (float) ($existing?->progress_percent ?? 0));

                $completed = $statusOverride !== null
                    ? $statusOverride
                    : ((bool) ($existing?->is_completed ?? false) || $forceCompleted || $progressPercent >= $threshold);
            }

            if ($completed) {
                $progressPercent = 100;
            }

            $completedAt = $completed ? ($existing?->completed_at ?? now()) : null;

            LessonProgress::updateOrCreate(
                ['user_id' => $userId, 'lesson_id' => $lesson->id],
                [
                    'course_id' => $course->id,
                    'watched_seconds' => $watchedSeconds,
                    'duration_seconds' => $durationSeconds,
                    'last_position_seconds' => $watchedSeconds,
                    'furthest_position_seconds' => $watchedSeconds,
                    'progress_percent' => $progressPercent,
                    'is_completed' => $completed,
                    'last_watched_at' => now(),
                    'completed_at' => $completedAt,
                ]
            );

            if ($completed && ! ($existing?->is_completed ?? false)) {
                app(PointService::class)->awardLessonCompletionPoints($userId, $lesson->id);
            }

            $requiredLessonIds = $this->requiredLessonIds($course);
            $completedLessons = LessonProgress::query()
                ->where('user_id', $userId)
                ->whereIn('lesson_id', $requiredLessonIds)
                ->where('is_completed', true)
                ->count();

            $totalRequired = $requiredLessonIds->count();
            $courseProgress = $totalRequired > 0
                ? min(100, round(($completedLessons / $totalRequired) * 100, 2))
                : 0.0;

            $enrollment->update([
                'progress_percent' => $courseProgress,
                'completed_lessons' => $completedLessons,
                'total_lessons' => $totalRequired,
                'last_accessed_at' => now(),
            ]);

            $completion = app(CourseCompletionService::class)->check($enrollment->fresh(), $userId);

            return [
                'success' => true,
                'completed' => $completed,
                'lesson_progress' => $progressPercent,
                'course_progress' => $courseProgress,
                'progress_percent' => $courseProgress,
                'lesson_completed' => $completed,
                'course_completed' => $completion['eligible'],
                'watched_seconds' => $watchedSeconds,
                'completed_lessons' => $completedLessons,
                'total_lessons' => $totalRequired,
                'completion' => $completion,
            ];
        });
    }

    /**
     * @param array{
     *     last_position_seconds?: int,
     *     furthest_position_seconds?: int,
     *     played_seconds?: int,
     *     watched_seconds?: int,
     *     video_duration_seconds?: int|float,
     *     duration_seconds?: int,
     *     client_updated_at?: string|null,
     *     completed?: bool
     * } $payload
     */
    public function recordVideoProgress(int $userId, Course $course, Lesson $lesson, array $payload): array
    {
        return DB::transaction(function () use ($userId, $course, $lesson, $payload) {
            $enrollment = Enrollment::query()
                ->where('user_id', $userId)
                ->where('course_id', $course->id)
                ->withLearningAccess()
                ->lockForUpdate()
                ->firstOrFail();

            User::where('id', $userId)->update([
                'last_learning_at' => now(),
                'engagement_email_stage' => 0,
            ]);

            $lessonIds = $this->courseLessonIds($course);
            abort_unless($lessonIds->contains((int) $lesson->id), 404);
            abort_unless($lesson->type === 'video', 422);

            $existing = LessonProgress::query()
                ->where('user_id', $userId)
                ->where('lesson_id', $lesson->id)
                ->lockForUpdate()
                ->first();

            $durationSeconds = $this->durationSeconds($lesson);

            $clientUpdatedAt = $this->clientUpdatedAt($payload['client_updated_at'] ?? null);
            if ($clientUpdatedAt?->isFuture()) {
                $clientUpdatedAt = now();
            }
            if (
                $existing?->last_client_updated_at
                && $clientUpdatedAt
                && $clientUpdatedAt->lt($existing->last_client_updated_at)
            ) {
                return $this->progressResponse($enrollment, $existing, false, true);
            }

            $lastPosition = $this->normalizeSeconds((int) ($payload['last_position_seconds'] ?? $payload['watched_seconds'] ?? 0), $durationSeconds);
            $clientFurthest = $this->normalizeSeconds((int) ($payload['furthest_position_seconds'] ?? 0), $durationSeconds);
            $playedSeconds = max(0, (int) ($payload['played_seconds'] ?? 0));

            $previousWatched = (int) ($existing?->watched_seconds ?? 0);
            $previousFurthest = (int) ($existing?->furthest_position_seconds ?? $existing?->watched_seconds ?? 0);
            $previousCompleted = (bool) ($existing?->is_completed ?? false);

            $serverElapsed = $this->serverElapsedSeconds($existing);
            $trustedPlayedSeconds = min($playedSeconds, $serverElapsed);

            $watchedSeconds = $this->normalizeSeconds($previousWatched + $trustedPlayedSeconds, $durationSeconds);

            $safeFurthestLimit = $durationSeconds > 0
                ? min($durationSeconds, max($previousFurthest, $previousFurthest + $trustedPlayedSeconds + 5))
                : max($previousFurthest, $clientFurthest);
            $furthestPosition = max($previousFurthest, min($clientFurthest, $safeFurthestLimit));

            $progressPercent = $durationSeconds > 0
                ? min(100, round(($watchedSeconds / $durationSeconds) * 100, 2))
                : (float) ($existing?->progress_percent ?? 0);

            $threshold = $course->requiredVideoPercent();
            // Đảm bảo học viên xem đủ thời lượng hoặc khi video kết thúc
            $completed = $previousCompleted || $progressPercent >= $threshold;
            if ($completed) {
                $progressPercent = 100;
                $watchedSeconds = max($watchedSeconds, $durationSeconds);
            }

            $completedAt = $existing?->completed_at;
            if ($completed && ! $completedAt) {
                $completedAt = now();
            }

            $progress = LessonProgress::updateOrCreate(
                ['user_id' => $userId, 'lesson_id' => $lesson->id],
                [
                    'course_id' => $course->id,
                    'watched_seconds' => $watchedSeconds,
                    'duration_seconds' => $durationSeconds,
                    'last_position_seconds' => $lastPosition,
                    'furthest_position_seconds' => $furthestPosition,
                    'progress_percent' => $progressPercent,
                    'is_completed' => $completed,
                    'last_watched_at' => now(),
                    'last_client_updated_at' => $clientUpdatedAt ?? now(),
                    'completed_at' => $completedAt,
                ]
            );

            if ($completed) {
                app(PointService::class)->awardLessonCompletionPoints($userId, $lesson->id);
            }

            return $this->refreshCourseProgress($enrollment, $userId, $course, $progress);
        });
    }

    public function courseLessonIds(Course $course): Collection
    {
        return Lesson::query()
            ->where(function ($query) use ($course) {
                $query->where('course_id', $course->id)
                    ->orWhereHas('section', fn ($q) => $q->where('course_id', $course->id))
                    ->orWhereHas('chapter', fn ($q) => $q->where('course_id', $course->id));
            })
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();
    }

    public function requiredLessonIds(Course $course): Collection
    {
        $ids = Lesson::query()
            ->where(function ($query) use ($course) {
                $query->where('course_id', $course->id)
                    ->orWhereHas('section', fn ($q) => $q->where('course_id', $course->id))
                    ->orWhereHas('chapter', fn ($q) => $q->where('course_id', $course->id));
            })
            ->where('is_required', true)
            ->where(function ($query) {
                $query->whereNull('status')->orWhere('status', 'published');
            })
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return $this->courseLessonIds($course);
        }

        return $ids;
    }

    /**
     * Reconcile quiz progress after a historical regrade without replaying quiz XP.
     * A previously completed lesson is never rolled back by an instructor correction.
     */
    public function reconcileQuizAfterRegrade(Quiz $quiz, int $userId): array
    {
        return DB::transaction(function () use ($quiz, $userId): array {
            $lesson = Lesson::query()->with(['course', 'section', 'chapter'])->findOrFail($quiz->lesson_id);
            $courseId = $lesson->course_id
                ?? $lesson->section?->course_id
                ?? $lesson->chapter?->course_id;

            $enrollment = Enrollment::query()
                ->where('user_id', $userId)
                ->where('course_id', $courseId)
                ->withLearningAccess()
                ->with('course')
                ->lockForUpdate()
                ->first();

            if (! $enrollment) {
                return [
                    'success' => false,
                    'skipped' => true,
                    'reason' => 'No learning enrollment found.',
                ];
            }

            $existing = LessonProgress::query()
                ->where('user_id', $userId)
                ->where('lesson_id', $lesson->id)
                ->lockForUpdate()
                ->first();
            $passed = $quiz->fresh()->hasPassedAttemptFor($userId);
            $completed = (bool) ($existing?->is_completed ?? false) || $passed;
            $progressPercent = $completed ? 100 : 0;
            $completedAt = $completed ? ($existing?->completed_at ?? now()) : null;

            $progress = LessonProgress::updateOrCreate(
                ['user_id' => $userId, 'lesson_id' => $lesson->id],
                [
                    'course_id' => $enrollment->course_id,
                    'watched_seconds' => (int) ($existing?->watched_seconds ?? 0),
                    'duration_seconds' => (int) ($existing?->duration_seconds ?? 0),
                    'last_position_seconds' => (int) ($existing?->last_position_seconds ?? 0),
                    'furthest_position_seconds' => (int) ($existing?->furthest_position_seconds ?? 0),
                    'progress_percent' => $progressPercent,
                    'is_completed' => $completed,
                    'last_watched_at' => now(),
                    'completed_at' => $completedAt,
                ],
            );

            $requiredLessonIds = $this->requiredLessonIds($enrollment->course);
            $completedLessons = LessonProgress::query()
                ->where('user_id', $userId)
                ->whereIn('lesson_id', $requiredLessonIds)
                ->where('is_completed', true)
                ->count();
            $totalRequired = $requiredLessonIds->count();
            $courseProgress = $totalRequired > 0
                ? min(100, round(($completedLessons / $totalRequired) * 100, 2))
                : 0.0;

            $enrollment->update([
                'progress_percent' => $courseProgress,
                'completed_lessons' => $completedLessons,
                'total_lessons' => $totalRequired,
                'last_accessed_at' => now(),
            ]);

            $completion = app(CourseCompletionService::class)->check($enrollment->fresh(), $userId);

            return [
                'success' => true,
                'reconciled' => true,
                'completed' => $completed,
                'lesson_progress' => $progressPercent,
                'course_progress' => $courseProgress,
                'progress_percent' => $courseProgress,
                'lesson_completed' => $completed,
                'course_completed' => $completion['eligible'],
                'completed_lessons' => $completedLessons,
                'total_lessons' => $totalRequired,
                'completion' => $completion,
            ];
        });
    }

    private function refreshCourseProgress(Enrollment $enrollment, int $userId, Course $course, LessonProgress $lessonProgress): array
    {
        $requiredLessonIds = $this->requiredLessonIds($course);
        $completedLessons = LessonProgress::query()
            ->where('user_id', $userId)
            ->whereIn('lesson_id', $requiredLessonIds)
            ->where('is_completed', true)
            ->count();

        $totalRequired = $requiredLessonIds->count();
        $courseProgress = $totalRequired > 0
            ? min(100, round(($completedLessons / $totalRequired) * 100, 2))
            : 0.0;

        $enrollment->update([
            'progress_percent' => $courseProgress,
            'completed_lessons' => $completedLessons,
            'total_lessons' => $totalRequired,
            'last_accessed_at' => now(),
        ]);

        $completion = app(CourseCompletionService::class)->check($enrollment->fresh(), $userId);

        return [
            'success' => true,
            'completed' => (bool) $lessonProgress->is_completed,
            'lesson_progress' => (float) $lessonProgress->progress_percent,
            'course_progress' => $courseProgress,
            'progress_percent' => $courseProgress,
            'lesson_completed' => (bool) $lessonProgress->is_completed,
            'course_completed' => $completion['eligible'],
            'watched_seconds' => (int) $lessonProgress->watched_seconds,
            'last_position_seconds' => (int) $lessonProgress->last_position_seconds,
            'furthest_position_seconds' => (int) $lessonProgress->furthest_position_seconds,
            'completed_lessons' => $completedLessons,
            'total_lessons' => $totalRequired,
            'completion' => $completion,
        ];
    }

    private function progressResponse(
        Enrollment $enrollment,
        LessonProgress $progress,
        bool $courseCompleted,
        bool $stale = false,
    ): array {
        return [
            'success' => ! $stale,
            'stale' => $stale,
            'completed' => (bool) $progress->is_completed,
            'lesson_progress' => (float) $progress->progress_percent,
            'course_progress' => (float) $enrollment->progress_percent,
            'progress_percent' => (float) $enrollment->progress_percent,
            'lesson_completed' => (bool) $progress->is_completed,
            'course_completed' => $courseCompleted,
            'watched_seconds' => (int) $progress->watched_seconds,
            'last_position_seconds' => (int) $progress->last_position_seconds,
            'furthest_position_seconds' => (int) $progress->furthest_position_seconds,
            'completed_lessons' => (int) $enrollment->completed_lessons,
            'total_lessons' => (int) $enrollment->total_lessons,
        ];
    }

    private function clientUpdatedAt(?string $value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    private function durationSeconds(Lesson $lesson): int
    {
        // Media metadata belongs to the transcoding pipeline, never the viewer.
        return max(0, (int) ($lesson->duration_seconds ?: $lesson->duration ?: 0));
    }

    private function normalizeSeconds(int $seconds, int $durationSeconds): int
    {
        $seconds = max(0, $seconds);

        return $durationSeconds > 0 ? min($seconds, $durationSeconds) : $seconds;
    }

    private function serverElapsedSeconds(?LessonProgress $existing): int
    {
        if (! $existing?->last_watched_at) {
            return 0;
        }

        $reference = now();

        return max(0, min(30, (int) $existing->last_watched_at->diffInSeconds($reference, false)));
    }
}
