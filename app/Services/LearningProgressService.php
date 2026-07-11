<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
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
    ): array {
        return DB::transaction(function () use ($userId, $course, $lesson, $watchedSeconds, $durationSeconds, $forceCompleted) {
            $enrollment = Enrollment::query()
                ->where('user_id', $userId)
                ->where('course_id', $course->id)
                ->withLearningAccess()
                ->lockForUpdate()
                ->firstOrFail();

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
            $watchedSeconds = max($previousWatched, min($watchedSeconds, $durationSeconds > 0 ? $durationSeconds : $watchedSeconds));

            $progressPercent = $durationSeconds > 0
                ? min(100, round(($watchedSeconds / $durationSeconds) * 100, 2))
                : ($forceCompleted ? 100 : (float) ($existing?->progress_percent ?? 0));

            $completed = (bool) ($existing?->is_completed ?? false)
                || $forceCompleted
                || $progressPercent >= $threshold;

            $completedAt = $existing?->completed_at;
            if ($completed && ! $completedAt) {
                $completedAt = now();
            }

            LessonProgress::updateOrCreate(
                ['user_id' => $userId, 'lesson_id' => $lesson->id],
                [
                    'course_id' => $course->id,
                    'watched_seconds' => $watchedSeconds,
                    'duration_seconds' => $durationSeconds,
                    'progress_percent' => $progressPercent,
                    'is_completed' => $completed,
                    'last_watched_at' => now(),
                    'completed_at' => $completedAt,
                ]
            );

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
        return Lesson::query()
            ->where(function ($query) use ($course) {
                $query->where('course_id', $course->id)
                    ->orWhereHas('section', fn ($q) => $q->where('course_id', $course->id))
                    ->orWhereHas('chapter', fn ($q) => $q->where('course_id', $course->id));
            })
            ->where('is_required', true)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();
    }
}
