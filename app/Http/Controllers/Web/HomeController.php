<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Faq;
use App\Models\LearningPath;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $selectedCategory = $this->resolveCategoryFilter($request->get('category'));
        $banner = [
            'title' => 'Học mọi lúc, mọi nơi',
            'subtitle' => 'Nền tảng học trực tuyến hàng đầu Việt Nam',
        ];

        $featuredIds = [];

        $featuredCourses = $this->withFavoriteState(Course::where('status', Course::STATUS_PUBLISHED)
            ->where('is_published', true)
            ->when(! empty($featuredIds), fn ($q) => $q->whereIn('id', $featuredIds))
            ->when(empty($featuredIds), fn ($q) => $q->where('is_featured', true))
            ->with(['instructor:id,name,avatar', 'category:id,parent_id,name,slug', 'category.parent:id,name,slug'])
            ->withCount('lessons'))
            ->limit(4)
            ->get();

        if ($featuredCourses->isEmpty()) {
            $featuredCourses = $this->withFavoriteState(Course::where('status', Course::STATUS_PUBLISHED)
                ->where('is_published', true)
                ->with(['instructor:id,name,avatar', 'category:id,parent_id,name,slug', 'category.parent:id,name,slug'])
                ->withCount('lessons'))
                ->orderByDesc('rating_avg')
                ->limit(4)
                ->get();
        }

        $categories = Category::query()
            ->active()
            ->parent()
            ->with([
                'children' => fn ($q) => $q
                    ->active()
                    ->withCount(['courses' => fn ($courseQuery) => $courseQuery
                        ->where('status', Course::STATUS_PUBLISHED)
                        ->where('is_published', true)])
                    ->orderBy('sort_order')
                    ->orderBy('name'),
            ])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $query = $this->withFavoriteState(Course::with(['instructor:id,name,avatar', 'category:id,parent_id,name,slug', 'category.parent:id,name,slug'])
            ->withCount('lessons')
            ->where('status', Course::STATUS_PUBLISHED)
            ->where('is_published', true));

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

        if ($selectedCategory) {
            if ($selectedCategory->parent_id) {
                $query->where('category_id', $selectedCategory->id);
            } else {
                $query->whereIn('category_id', $selectedCategory->children()->active()->pluck('id'));
            }
        }

        if ($minRating = $request->get('min_rating')) {
            $query->where('rating_avg', '>=', $minRating);
        }

        $sort = $request->get('sort', 'newest');
        match ($sort) {
            'price_asc' => $query->orderByRaw('COALESCE(discount_price, sale_price, price) ASC'),
            'price_desc' => $query->orderByRaw('COALESCE(discount_price, sale_price, price) DESC'),
            'rating' => $query->orderByDesc('rating_avg'),
            'popular' => $query->orderByDesc('enrollment_count'),
            default => $query->orderByDesc('published_at'),
        };

        $courses = $query->paginate(8)->withQueryString();

        $learningPaths = LearningPath::limit(3)->get();

        $faqs = Faq::where('is_active', true)->orderBy('sort_order')->limit(5)->get();

        $stats = [
            'courses' => Course::where('status', Course::STATUS_PUBLISHED)->where('is_published', true)->count(),
            'students' => Enrollment::distinct('user_id')->count('user_id'),
            'instructors' => User::where('role', 'instructor')->count(),
        ];

        $badges = Badge::all();

        return view('home', compact(
            'banner', 'featuredCourses', 'categories', 'courses', 'selectedCategory',
            'learningPaths', 'faqs', 'stats', 'badges'
        ));
    }

    private function resolveCategoryFilter(mixed $value): ?Category
    {
        if (! filled($value)) {
            return null;
        }

        $category = Category::query()
            ->active()
            ->with('parent:id,name,slug,status')
            ->when(
                is_numeric($value),
                fn ($query) => $query->whereKey((int) $value),
                fn ($query) => $query->where('slug', (string) $value),
            )
            ->first();

        if ($category?->parent_id && ! $category->parent?->status) {
            return null;
        }

        return $category;
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
