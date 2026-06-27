<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\Faq;
use App\Models\LearningPath;
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

        $featuredIds = [];

        $featuredCourses = Course::where('status', 'published')
            ->when(! empty($featuredIds), fn ($q) => $q->whereIn('id', $featuredIds))
            ->when(empty($featuredIds), fn ($q) => $q->where('is_featured', true))
            ->with(['instructor:id,name,avatar', 'category:id,name'])
            ->limit(4)
            ->get();

        if ($featuredCourses->isEmpty()) {
            $featuredCourses = Course::where('status', 'published')
                ->with(['instructor:id,name,avatar', 'category:id,name'])
                ->orderByDesc('rating_avg')
                ->limit(4)
                ->get();
        }

        $categories = Category::withCount(['courses' => fn ($q) => $q->where('status', 'published')])
            ->get();

        $query = Course::with(['instructor:id,name,avatar', 'category:id,name'])
            ->where('status', 'published');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('instructor', fn ($iq) => $iq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($level = $request->get('level')) {
            $query->where('level', $level);
        }

        if ($categoryId = $request->get('category')) {
            $query->where('category_id', $categoryId);
        }

        if ($minRating = $request->get('min_rating')) {
            $query->where('rating_avg', '>=', $minRating);
        }

        $sort = $request->get('sort', 'newest');
        match ($sort) {
            'price_asc' => $query->orderByRaw('COALESCE(sale_price, price) ASC'),
            'price_desc' => $query->orderByRaw('COALESCE(sale_price, price) DESC'),
            'rating' => $query->orderByDesc('rating_avg'),
            'popular' => $query->orderByDesc('enrollment_count'),
            default => $query->orderByDesc('published_at'),
        };

        $courses = $query->paginate(8)->withQueryString();

        $learningPaths = LearningPath::limit(3)->get();

        $faqs = Faq::where('is_active', true)->orderBy('sort_order')->limit(5)->get();

        $stats = [
            'courses' => Course::where('status', 'published')->count(),
            'students' => \App\Models\Enrollment::distinct('user_id')->count('user_id'),
            'instructors' => \App\Models\User::where('role', 'instructor')->count(),
        ];

        $badges = \App\Models\Badge::all();

        return view('home', compact(
            'banner', 'featuredCourses', 'categories', 'courses',
            'learningPaths', 'faqs', 'stats', 'badges'
        ));
    }
}
