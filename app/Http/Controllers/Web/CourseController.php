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
            'courseSections.lessons' => fn ($q) => $q
                ->select('id', 'course_id', 'section_id', 'title', 'type', 'duration', 'duration_seconds', 'is_preview', 'sort_order')
                ->orderBy('sort_order'),
            'chapters.lessons' => fn ($q) => $q->select('id', 'chapter_id', 'title', 'type', 'duration_seconds', 'is_preview', 'sort_order'),
        ]);

        $relatedCourses = app(CourseRecommendationService::class)->getRelatedCourses($course, 4);

        $reviews = Review::where('course_id', $course->id)
            ->with('user:id,name,avatar')
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        $curriculumSections = $course->courseSections->isNotEmpty()
            ? $course->courseSections
            : $course->chapters;
        $totalLessons = $curriculumSections->sum(fn ($section) => $section->lessons->count());
        $previewLessons = $curriculumSections->flatMap->lessons->where('is_preview', true)->count();

        return view('courses.show', compact(
            'course',
            'curriculumSections',
            'relatedCourses',
            'reviews',
            'totalLessons',
            'previewLessons'
        ));
    }
}
