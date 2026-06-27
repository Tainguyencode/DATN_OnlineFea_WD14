<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Review;
use App\Services\CourseRecommendationService;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function show(Course $course): View
    {
        if ($course->status !== 'published') {
            abort(404);
        }

        $course->load([
            'instructor:id,name,avatar,bio',
            'category:id,name,slug',
            'chapters.lessons' => fn ($q) => $q->select('id', 'chapter_id', 'title', 'type', 'duration_seconds', 'is_preview', 'sort_order'),
        ]);

        $relatedCourses = app(CourseRecommendationService::class)->getRelatedCourses($course, 4);

        $reviews = Review::where('course_id', $course->id)
            ->with('user:id,name,avatar')
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        $totalLessons = $course->chapters->sum(fn ($c) => $c->lessons->count());
        $previewLessons = $course->chapters->flatMap->lessons->where('is_preview', true)->count();

        return view('courses.show', compact('course', 'relatedCourses', 'reviews', 'totalLessons', 'previewLessons'));
    }
}
