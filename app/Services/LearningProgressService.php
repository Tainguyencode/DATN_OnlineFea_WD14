<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use Illuminate\Support\Facades\DB;

class LearningProgressService
{
    public function recordLessonProgress(int $userId, Course $course, Lesson $lesson, int $watchedSeconds = 0, bool $completed = false): array
    {
        return DB::transaction(function () use ($userId, $course, $lesson, $watchedSeconds, $completed) {
            $enrollment = Enrollment::query()
                ->where('user_id', $userId)
                ->where('course_id', $course->id)
                ->where('status', 'active')
                ->lockForUpdate()
                ->firstOrFail();

            $lessonIds = $this->courseLessonIds($course);

            abort_unless($lessonIds->contains((int) $lesson->id), 404);

            $existing = DB::table('lesson_progress')
                ->where('user_id', $userId)
                ->where('lesson_id', $lesson->id)
                ->first();

            $watchedSeconds = max((int) ($existing->watched_seconds ?? 0), $watchedSeconds);
            $completed = (bool) ($existing->is_completed ?? false) || $completed;
            $completedAt = $existing?->completed_at;

            if ($completed && ! $completedAt) {
                $completedAt = now();
            }

            DB::table('lesson_progress')->updateOrInsert(
                [
                    'user_id' => $userId,
                    'lesson_id' => $lesson->id,
                ],
                [
                    'watched_seconds' => $watchedSeconds,
                    'is_completed' => $completed,
                    'completed_at' => $completedAt,
                    'created_at' => $existing?->created_at ?? now(),
                    'updated_at' => now(),
                ]
            );

            $completedLessons = DB::table('lesson_progress')
                ->where('user_id', $userId)
                ->whereIn('lesson_id', $lessonIds)
                ->where('is_completed', true)
                ->distinct('lesson_id')
                ->count('lesson_id');

            $totalLessons = $lessonIds->count();
            $progressPercent = $totalLessons > 0
                ? round(($completedLessons / $totalLessons) * 100, 2)
                : 0.0;

            $enrollment->update([
                'progress_percent' => $progressPercent,
                'completed_at' => $progressPercent >= 100
                    ? ($enrollment->completed_at ?? now())
                    : null,
            ]);

            return [
                'completed' => $completed,
                'watched_seconds' => $watchedSeconds,
                'completed_lessons' => $completedLessons,
                'total_lessons' => $totalLessons,
                'progress_percent' => $progressPercent,
            ];
        });
    }

    private function courseLessonIds(Course $course)
    {
        return Lesson::query()
            ->where('course_id', $course->id)
            ->orWhereHas('section', fn ($query) => $query->where('course_id', $course->id))
            ->orWhereHas('chapter', fn ($query) => $query->where('course_id', $course->id))
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();
    }
}
