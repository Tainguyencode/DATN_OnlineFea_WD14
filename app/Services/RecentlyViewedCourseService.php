<?php

namespace App\Services;

use App\Models\Course;
use App\Models\RecentlyViewedCourse;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

class RecentlyViewedCourseService
{
    public const MAX_PER_USER = 50;

    public function record(?User $user, ?Course $course): void
    {
        if (! $this->canRecord($user, $course)) {
            return;
        }

        try {
            RecentlyViewedCourse::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                ],
                [
                    'last_viewed_at' => now(),
                ]
            );

            $this->pruneForUser($user->id);
        } catch (Throwable $exception) {
            Log::warning('Could not record recently viewed course.', [
                'user_id' => $user?->id,
                'course_id' => $course?->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    public function queryVisibleForUser(User|int $user): Builder
    {
        return $this->withVisibleCourseRelations(
            RecentlyViewedCourse::query()
                ->forUser($user)
                ->whereHas('course', fn (Builder $query) => $this->publishedCourseConstraint($query))
                ->recentFirst()
        );
    }

    public function latestVisibleForUser(User|int $user, int $limit = 6): Collection
    {
        return $this->queryVisibleForUser($user)
            ->limit($limit)
            ->get();
    }

    private function canRecord(?User $user, ?Course $course): bool
    {
        if (! $user || ! $user->isStudent() || ! $course || ! $course->exists) {
            return false;
        }

        return $course->isPublished();
    }

    private function withVisibleCourseRelations(Builder $query): Builder
    {
        return $query->with([
            'course' => fn ($courseQuery) => $courseQuery
                ->with([
                    'instructor:id,name,avatar',
                    'category:id,parent_id,name,slug',
                    'category.parent:id,name,slug',
                    'courseSections.lessons' => fn ($lessonQuery) => $lessonQuery
                        ->select('id', 'course_id', 'section_id', 'chapter_id', 'title', 'sort_order')
                        ->orderBy('sort_order'),
                    'chapters.lessons' => fn ($lessonQuery) => $lessonQuery
                        ->select('id', 'course_id', 'chapter_id', 'section_id', 'title', 'sort_order')
                        ->orderBy('sort_order'),
                ])
                ->withCount('lessons'),
        ]);
    }

    private function publishedCourseConstraint(Builder $query): Builder
    {
        return $query
            ->where('status', Course::STATUS_PUBLISHED)
            ->where('is_published', true);
    }

    private function pruneForUser(int $userId): void
    {
        $idsToKeep = RecentlyViewedCourse::query()
            ->forUser($userId)
            ->recentFirst()
            ->limit(self::MAX_PER_USER)
            ->pluck('id');

        if ($idsToKeep->isEmpty()) {
            return;
        }

        RecentlyViewedCourse::query()
            ->forUser($userId)
            ->whereNotIn('id', $idsToKeep)
            ->delete();
    }
}
