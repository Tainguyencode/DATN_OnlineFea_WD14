<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Faq;
use App\Models\LearningPath;
use App\Models\Review;
use App\Models\User;
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

        // 1. Khóa học nổi bật (STU-FE-04, STU-BE-01)
        $featuredCoursesQuery = Course::published()
            ->with(['instructor:id,name,avatar', 'category:id,parent_id,name,slug', 'category.parent:id,name,slug'])
            ->withCount('lessons');

        $featuredCourses = $this->withFavoriteState((clone $featuredCoursesQuery)->where('is_featured', true))
            ->orderByDesc('rating_avg')
            ->orderByDesc('updated_at')
            ->limit(8)
            ->get();

        // Fallback: nếu chưa có khóa học nào được đánh dấu is_featured, lấy các khóa có rating hoặc học viên cao nhất
        if ($featuredCourses->isEmpty()) {
            $featuredCourses = $this->withFavoriteState((clone $featuredCoursesQuery))
                ->orderByDesc('rating_avg')
                ->orderByDesc('enrollment_count')
                ->orderByDesc('published_at')
                ->limit(8)
                ->get();
        }

        // 2. Danh mục môn học (STU-FE-03, STU-BE-01)
        $categories = Category::query()
            ->active()
            ->parent()
            ->with([
                'children' => fn ($q) => $q
                    ->active()
                    ->withCount(['courses' => fn ($courseQuery) => $courseQuery->published()])
                    ->orderBy('sort_order')
                    ->orderBy('name'),
            ])
            ->withCount(['courses' => fn ($q) => $q->published()])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // 3. Số liệu thống kê thực tế (STU-FE-02, STU-BE-01)
        $stats = [
            'courses' => Course::published()->count(),
            'students' => Enrollment::distinct('user_id')->count('user_id'),
            'instructors' => User::where('role', 'instructor')
                ->where('instructor_status', 'approved')
                ->where('is_active', true)
                ->count(),
            'categories' => Category::active()->parent()->count(),
        ];

        // 4. Lộ trình học tập & FAQ
        $learningPaths = LearningPath::withCount('courses')->limit(3)->get();
        $faqs = Faq::where('is_active', true)->orderBy('sort_order')->limit(5)->get();

        // 5. Đánh giá thực tế của học viên đã được duyệt (STU-FE-06)
        $testimonials = Review::visible()
            ->whereNull('parent_id')
            ->where('rating', '>=', 4)
            ->with(['user:id,name,avatar', 'course:id,title,slug'])
            ->latest()
            ->limit(6)
            ->get();

        return view('home', compact(
            'banner', 'featuredCourses', 'categories',
            'learningPaths', 'faqs', 'stats', 'testimonials'
        ));
    }

    private function withFavoriteState($query)
    {
        if (! auth()->check() || ! auth()->user()->isStudent()) {
            return $query;
        }

        return $query->withExists([
            'wishlists as is_favorited' => fn ($favoriteQuery) => $favoriteQuery->where('user_id', auth()->id()),
        ]);
    }
}
