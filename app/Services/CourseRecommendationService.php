<?php

namespace App\Services;

use App\Models\Course;
use Illuminate\Database\Eloquent\Collection;

class CourseRecommendationService
{
    public function getRelatedCourses(Course $course, int $limit = 4): Collection
    {
        $query = Course::query()
            ->where('status', 'published')
            ->where('id', '!=', $course->id)
            ->with(['instructor:id,name,avatar', 'category:id,parent_id,name,slug', 'category.parent:id,name,slug']);

        if ($course->category_id) {
            $query->where('category_id', $course->category_id);
        } else {
            $query->where('level', $course->level);
        }

        $related = $query
            ->orderByDesc('rating_avg')
            ->orderByDesc('enrollment_count')
            ->limit($limit)
            ->get();

        if ($related->count() >= $limit) {
            return $related;
        }

        $excludeIds = $related->pluck('id')->push($course->id);

        $fallback = Course::query()
            ->where('status', 'published')
            ->whereNotIn('id', $excludeIds)
            ->with(['instructor:id,name,avatar', 'category:id,parent_id,name,slug', 'category.parent:id,name,slug'])
            ->orderByDesc('is_featured')
            ->orderByDesc('rating_avg')
            ->limit($limit - $related->count())
            ->get();

        return $related->concat($fallback);
    }
}
