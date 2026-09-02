<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Review;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $banner = [
            'title' => 'Học mọi lúc, mọi nơi',
            'subtitle' => 'Nền tảng học trực tuyến hàng đầu Việt Nam',
        ];

        $freeCourses = $this->homepageCourseQuery()
            ->whereRaw('COALESCE(discount_price, sale_price, price) <= 0')
            ->orderByDesc('rating_avg')
            ->orderByDesc('enrollment_count')
            ->orderByDesc('published_at')
            ->limit(4)
            ->get();

        $featuredCourses = $this->homepageCourseQuery()
            ->where('is_featured', true)
            ->orderByDesc('rating_avg')
            ->orderByDesc('updated_at')
            ->limit(8)
            ->get();

        // Preserve the current fallback when no published course has been explicitly featured.
        if ($featuredCourses->isEmpty()) {
            $featuredCourses = $this->homepageCourseQuery()
                ->orderByDesc('rating_avg')
                ->orderByDesc('enrollment_count')
                ->orderByDesc('published_at')
                ->limit(8)
                ->get();
        }

        $publishedCoursesCount = Course::query()
            ->published()
            ->selectRaw('COUNT(*)')
            ->join('categories as homepage_course_categories', 'homepage_course_categories.id', '=', 'courses.category_id')
            ->where(function (Builder $query): void {
                $query->whereColumn('courses.category_id', 'categories.id')
                    ->orWhereColumn('homepage_course_categories.parent_id', 'categories.id');
            });

        $categories = Category::query()
            ->select(['categories.id', 'categories.name', 'categories.slug', 'categories.description', 'categories.icon', 'categories.sort_order'])
            ->active()
            ->parent()
            ->selectSub($publishedCoursesCount, 'courses_count')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $stats = [
            'courses' => Course::published()->count(),
            'students' => Enrollment::distinct('user_id')->count('user_id'),
            'instructors' => User::where('role', 'instructor')
                ->where('instructor_status', 'approved')
                ->where('is_active', true)
                ->count(),
            'categories' => Category::active()->parent()->count(),
        ];

        $testimonials = Review::visible()
            ->select(['id', 'user_id', 'course_id', 'rating', 'comment', 'created_at'])
            ->whereNull('parent_id')
            ->whereNotNull('comment')
            ->whereRaw("TRIM(comment) <> ''")
            ->whereBetween('rating', [1, 5])
            ->whereHas('course', fn (Builder $query) => $query->published())
            ->whereHas('user')
            ->with(['user:id,name,avatar', 'course:id,title,slug'])
            ->latest()
            ->limit(3)
            ->get();

        $homepageCourseIds = $freeCourses
            ->pluck('id')
            ->merge($featuredCourses->pluck('id'))
            ->unique()
            ->values();
        $favoriteCourseIds = [];
        $enrolledCourseIds = [];

        if ($request->user()?->isStudent() && $homepageCourseIds->isNotEmpty()) {
            $favoriteCourseIds = Wishlist::query()
                ->where('user_id', $request->user()->id)
                ->whereIn('course_id', $homepageCourseIds)
                ->pluck('course_id')
                ->map(fn ($courseId) => (int) $courseId)
                ->all();

            $enrolledCourseIds = Enrollment::query()
                ->where('user_id', $request->user()->id)
                ->whereIn('course_id', $homepageCourseIds)
                ->withLearningAccess()
                ->pluck('course_id')
                ->map(fn ($courseId) => (int) $courseId)
                ->all();
        }

        return view('home', compact(
            'banner', 'categories', 'freeCourses', 'featuredCourses', 'testimonials', 'stats',
            'favoriteCourseIds', 'enrolledCourseIds'
        ));
    }

    private function homepageCourseQuery(): Builder
    {
        return Course::query()
            ->published()
            ->select([
                'courses.id',
                'courses.instructor_id',
                'courses.category_id',
                'courses.title',
                'courses.slug',
                'courses.thumbnail',
                'courses.price',
                'courses.discount_price',
                'courses.sale_price',
                'courses.level',
                'courses.rating_avg',
                'courses.rating_count',
                'courses.enrollment_count',
                'courses.is_featured',
                'courses.published_at',
                'courses.updated_at',
            ])
            ->with([
                'instructor:id,name,avatar',
                'category:id,parent_id,name,slug',
                'category.parent:id,name,slug',
            ])
            ->withCount('lessons');
    }
}
