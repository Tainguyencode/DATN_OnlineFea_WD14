<?php

namespace App\Http\Controllers\Web\Admin;

use App\Enums\CourseStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\OrderItem;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $requestedPeriod = $request->string('period', '6')->toString();
        $period = in_array($requestedPeriod, ['6', '12', 'all'], true)
            ? ($requestedPeriod === 'all' ? 'all' : (int) $requestedPeriod)
            : 6;
        $categoryId = $request->integer('category_id') ?: null;
        $instructorId = $request->integer('instructor_id') ?: null;
        $status = in_array($request->string('status')->toString(), CourseStatus::values(), true)
            ? $request->string('status')->toString()
            : null;
        $periodStart = $period === 'all' ? null : now()->startOfMonth()->subMonths($period - 1);

        $applyCourseFilters = static function (Builder $query) use ($categoryId, $instructorId, $status): void {
            $query
                ->when($categoryId, fn (Builder $q) => $q->where('category_id', $categoryId))
                ->when($instructorId, fn (Builder $q) => $q->where('instructor_id', $instructorId))
                ->when($status, fn (Builder $q) => $q->where('status', $status));
        };

        $courseQuery = Course::query();
        $applyCourseFilters($courseQuery);

        $enrollmentQuery = Enrollment::query()
            ->when($periodStart, fn (Builder $q) => $q->where('created_at', '>=', $periodStart))
            ->whereHas('course', $applyCourseFilters);
        $reviewQuery = Review::query()
            ->whereNull('parent_id')
            ->whereHas('course', $applyCourseFilters);
        $paidItemQuery = OrderItem::query()
            ->when($periodStart, fn (Builder $q) => $q->where('created_at', '>=', $periodStart))
            ->whereHas('order', fn (Builder $q) => $q->where('status', 'paid'))
            ->whereHas('course', $applyCourseFilters);

        $stats = [
            'revenue' => (float) (clone $paidItemQuery)->sum('price'),
            'orders' => (clone $paidItemQuery)->distinct()->count('order_id'),
            'courses' => (clone $courseQuery)->count(),
            'enrollments' => (clone $enrollmentQuery)->count(),
            'reviews' => (clone $reviewQuery)->count(),
            'helpful_reviews' => (int) (clone $reviewQuery)->sum('helpful_count'),
            'average_rating' => round((float) ((clone $reviewQuery)->avg('rating') ?? 0), 1),
            'students' => (clone $enrollmentQuery)->distinct()->count('user_id'),
        ];

        $firstActivityAt = collect([
            Enrollment::query()->whereHas('course', $applyCourseFilters)->min('created_at'),
            OrderItem::query()
                ->whereHas('order', fn (Builder $q) => $q->where('status', 'paid'))
                ->whereHas('course', $applyCourseFilters)
                ->min('created_at'),
        ])->filter()->sort()->first();
        $analyticsStart = $periodStart
            ?? ($firstActivityAt ? Carbon::parse($firstActivityAt)->startOfMonth() : now()->startOfMonth());
        $monthCount = (int) $analyticsStart->diffInMonths(now()->startOfMonth()) + 1;

        $monthlyAnalytics = collect(range($monthCount - 1, 0))->map(function (int $monthsAgo) use ($applyCourseFilters): array {
            $start = now()->startOfMonth()->subMonths($monthsAgo);
            $end = $start->copy()->endOfMonth();
            $items = OrderItem::query()
                ->whereBetween('created_at', [$start, $end])
                ->whereHas('order', fn (Builder $q) => $q->where('status', 'paid'))
                ->whereHas('course', $applyCourseFilters);

            return [
                'label' => 'T'.$start->month,
                'full_label' => $start->format('m/Y'),
                'revenue' => (float) (clone $items)->sum('price'),
                'orders' => (clone $items)->distinct()->count('order_id'),
                'enrollments' => Enrollment::query()
                    ->whereBetween('created_at', [$start, $end])
                    ->whereHas('course', $applyCourseFilters)
                    ->count(),
            ];
        })->values();

        $courseStatuses = collect(CourseStatus::cases())->map(function (CourseStatus $courseStatus) use ($courseQuery): array {
            return [
                'label' => $courseStatus->label(),
                'value' => (clone $courseQuery)->where('status', $courseStatus->value)->count(),
                'color' => $courseStatus->chartColor(),
            ];
        })->filter(fn (array $item) => $item['value'] > 0)->values();

        $ratingDistribution = collect(range(1, 5))->map(fn (int $rating): array => [
            'label' => $rating.' sao',
            'value' => (clone $reviewQuery)->where('rating', $rating)->count(),
        ]);

        $topCourses = (clone $courseQuery)
            ->with(['category:id,name', 'instructor:id,name'])
            ->withCount([
                'enrollments as period_enrollments_count' => fn (Builder $q) => $q->when(
                    $periodStart,
                    fn (Builder $enrollments) => $enrollments->where('created_at', '>=', $periodStart)
                ),
                'reviews',
            ])
            ->withAvg('reviews', 'rating')
            ->orderByDesc('period_enrollments_count')
            ->limit(8)
            ->get();

        return view('admin.dashboard', [
            'stats' => $stats,
            'monthlyAnalytics' => $monthlyAnalytics,
            'courseStatuses' => $courseStatuses,
            'ratingDistribution' => $ratingDistribution,
            'topCourses' => $topCourses,
            'categories' => Category::query()->where('status', true)->orderBy('name')->get(['id', 'name']),
            'instructors' => User::query()->where('role', 'instructor')->orderBy('name')->get(['id', 'name']),
            'filters' => compact('period', 'categoryId', 'instructorId', 'status'),
        ]);
    }
}
