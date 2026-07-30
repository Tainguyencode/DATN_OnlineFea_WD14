<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class LessonNoteAccessService
{
    public function assertCanUse(User $user, Course $course, Lesson $lesson): void
    {
        abort_unless($user->isStudent(), 403);
        abort_unless($this->lessonBelongsToCourse($course, $lesson), 404);
        abort_unless($this->userHasLearningAccess($user, $course), 403);
    }

    public function userHasLearningAccess(User $user, Course|int $course): bool
    {
        $courseId = $course instanceof Course ? $course->id : $course;

        return Enrollment::query()
            ->where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->withLearningAccess()
            ->exists();
    }

    public function accessibleCourseIds(User $user): Collection
    {
        return Enrollment::query()
            ->where('user_id', $user->id)
            ->withLearningAccess()
            ->pluck('course_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();
    }

    public function lessonBelongsToCourse(Course $course, Lesson $lesson): bool
    {
        if ((int) $lesson->course_id === (int) $course->id) {
            return true;
        }

        if ($lesson->section_id && $lesson->section()->where('course_id', $course->id)->exists()) {
            return true;
        }

        return $lesson->chapter_id && $lesson->chapter()->where('course_id', $course->id)->exists();
    }

    public function lessonCourseId(Lesson $lesson): ?int
    {
        if ($lesson->course_id) {
            return (int) $lesson->course_id;
        }

        $lesson->loadMissing(['section:id,course_id', 'chapter:id,course_id']);

        return $lesson->section?->course_id
            ? (int) $lesson->section->course_id
            : ($lesson->chapter?->course_id ? (int) $lesson->chapter->course_id : null);
    }

    public function lessonDurationSeconds(Lesson $lesson): int
    {
        return max(0, (int) ($lesson->duration_seconds ?: $lesson->duration ?: 0));
    }

    public function scopeLessonsInCourses(Builder $query, Collection|array $courseIds): Builder
    {
        $ids = collect($courseIds)->map(fn ($id) => (int) $id)->filter()->unique()->values();

        if ($ids->isEmpty()) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function (Builder $lessonQuery) use ($ids) {
            $lessonQuery->whereIn('course_id', $ids)
                ->orWhereHas('section', fn (Builder $sectionQuery) => $sectionQuery->whereIn('course_id', $ids))
                ->orWhereHas('chapter', fn (Builder $chapterQuery) => $chapterQuery->whereIn('course_id', $ids));
        });
    }
}
