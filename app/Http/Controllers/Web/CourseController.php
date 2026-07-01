<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $categoryId = $request->query('category');
        $level = $request->query('level');
        $pricing = $request->query('pricing');

        $courses = $this->publishedCoursesQuery()
            ->with(['instructor:id,name,avatar', 'category:id,name,slug'])
            ->withCount(['lessons', 'courseSections'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->when($categoryId, fn ($query) => $query->where('category_id', $categoryId))
            ->when(in_array($level, ['beginner', 'intermediate', 'advanced'], true), fn ($query) => $query->where('level', $level))
            ->when($pricing === 'free', fn ($query) => $query->whereRaw('COALESCE(discount_price, sale_price, price) <= 0'))
            ->when($pricing === 'paid', fn ($query) => $query->whereRaw('COALESCE(discount_price, sale_price, price) > 0'))
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->paginate(9)
            ->withQueryString();

        $categories = Category::query()
            ->select('id', 'name')
            ->withCount([
                'courses' => fn ($query) => $query
                    ->where('status', Course::STATUS_PUBLISHED)
                    ->where('is_published', true),
            ])
            ->orderBy('name')
            ->get();

        $levelOptions = [
            'beginner' => 'Cơ bản',
            'intermediate' => 'Trung cấp',
            'advanced' => 'Nâng cao',
        ];

        return view('courses.index', compact(
            'courses',
            'categories',
            'levelOptions',
            'search',
            'categoryId',
            'level',
            'pricing'
        ));
    }

    public function show(string $slug): View
    {
        $course = $this->publishedCoursesQuery()
            ->where('slug', $slug)
            ->firstOrFail();

        $course->load([
            'instructor:id,name,avatar,bio',
            'category:id,name,slug',
            'courseSections.lessons' => fn ($q) => $q
                ->select('id', 'course_id', 'section_id', 'title', 'type', 'video_url', 'content', 'document_file', 'duration', 'duration_seconds', 'is_preview', 'sort_order')
                ->orderBy('sort_order'),
            'chapters.lessons' => fn ($q) => $q
                ->select('id', 'course_id', 'chapter_id', 'title', 'type', 'video_url', 'content', 'document_file', 'duration', 'duration_seconds', 'is_preview', 'sort_order')
                ->orderBy('sort_order'),
        ]);

        $relatedCourses = $this->publishedCoursesQuery()
            ->where('id', '!=', $course->id)
            ->when($course->category_id, fn ($query) => $query->where('category_id', $course->category_id))
            ->with(['instructor:id,name,avatar', 'category:id,name'])
            ->withCount('lessons')
            ->orderByDesc('rating_avg')
            ->orderByDesc('published_at')
            ->limit(4)
            ->get();

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
        $totalSections = $curriculumSections->count();
        $isEnrolled = auth()->check()
            && Enrollment::where('user_id', auth()->id())->where('course_id', $course->id)->exists();

        return view('courses.show', compact(
            'course',
            'curriculumSections',
            'relatedCourses',
            'reviews',
            'totalLessons',
            'previewLessons',
            'totalSections',
            'isEnrolled'
        ));
    }

    private function publishedCoursesQuery()
    {
        return Course::query()
            ->where('status', Course::STATUS_PUBLISHED)
            ->where('is_published', true);
    }
}
