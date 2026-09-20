<?php

namespace App\Http\Controllers\Web\Instructor;

use App\Enums\CourseStatus;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\OrderItem;
use App\Models\Review;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user() ?? auth()->user();
        $userId = $user?->id;

        $currentYear = (int) now()->year;
        $earliestItemYear = OrderItem::query()
            ->whereHas('order', fn (Builder $q) => $q->where('status', 'paid'))
            ->whereHas('course', fn (Builder $q) => $q->where('instructor_id', $userId))
            ->min('created_at');

        $minYear = $earliestItemYear ? (int) Carbon::parse($earliestItemYear)->year : $currentYear;
        if ($minYear > $currentYear) {
            $minYear = $currentYear;
        }

        $availableYears = range($currentYear, $minYear);

        $rawYear = $request->input('year');
        if ($request->has('year') && is_numeric($rawYear)) {
            $year = (int) $rawYear;
            if ($year < 1970 || $year > $currentYear + 1) {
                $year = $currentYear;
            }
        } else {
            $year = $currentYear;
        }

        $requestedPeriod = $request->string('period', '6')->toString();
        $period = in_array($requestedPeriod, ['6', '12', 'all'], true)
            ? ($requestedPeriod === 'all' ? 'all' : (int) $requestedPeriod)
            : 6;
        $courseId = $request->integer('course_id') ?: null;
        $status = in_array($request->string('status')->toString(), CourseStatus::values(), true)
            ? $request->string('status')->toString()
            : null;
        $periodStart = $period === 'all' ? null : now()->startOfMonth()->subMonths($period - 1);

        $applyCourseFilters = static function (Builder $query) use ($userId, $courseId, $status): void {
            $query
                ->where('instructor_id', $userId)
                ->when($courseId, fn (Builder $q) => $q->whereKey($courseId))
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
            ->whereYear('created_at', $year)
            ->whereHas('order', fn (Builder $q) => $q->where('status', 'paid'))
            ->whereHas('course', $applyCourseFilters);

        $stats = [
            'revenue' => (float) (clone $paidItemQuery)->sum('instructor_earning'),
            'courses' => (clone $courseQuery)->count(),
            'published' => (clone $courseQuery)->where('status', CourseStatus::Published->value)->count(),
            'students' => (clone $enrollmentQuery)->whereIn('status', [Enrollment::STATUS_ACTIVE, Enrollment::STATUS_COMPLETED])->distinct()->count('user_id'),
            'enrollments' => (clone $enrollmentQuery)->whereIn('status', [Enrollment::STATUS_ACTIVE, Enrollment::STATUS_COMPLETED])->count(),
            'reviews' => (clone $reviewQuery)->count(),
            'average_rating' => round((float) ((clone $reviewQuery)->avg('rating') ?? 0), 1),
            'helpful_reviews' => (int) (clone $reviewQuery)->sum('helpful_count'),
        ];

        $yearlyRevenueByMonth = OrderItem::query()
            ->selectRaw('MONTH(order_items.created_at) as month, SUM(order_items.instructor_earning) as total_revenue')
            ->whereYear('order_items.created_at', $year)
            ->whereHas('order', fn (Builder $q) => $q->where('status', 'paid'))
            ->whereHas('course', $applyCourseFilters)
            ->groupByRaw('MONTH(order_items.created_at)')
            ->pluck('total_revenue', 'month');

        $yearlyEnrollmentsByMonth = Enrollment::query()
            ->selectRaw('MONTH(enrollments.created_at) as month, COUNT(*) as total_enrollments')
            ->whereYear('enrollments.created_at', $year)
            ->whereIn('status', [Enrollment::STATUS_ACTIVE, Enrollment::STATUS_COMPLETED])
            ->whereHas('course', $applyCourseFilters)
            ->groupByRaw('MONTH(enrollments.created_at)')
            ->pluck('total_enrollments', 'month');

        $monthlyAnalytics = collect(range(1, 12))->map(function (int $m) use ($year, $yearlyRevenueByMonth, $yearlyEnrollmentsByMonth): array {
            return [
                'label' => 'T'.$m,
                'full_label' => sprintf('Tháng %d/%d', $m, $year),
                'month' => $m,
                'revenue' => (float) ($yearlyRevenueByMonth->get($m) ?? 0),
                'enrollments' => (int) ($yearlyEnrollmentsByMonth->get($m) ?? 0),
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
            ->with('category:id,name')
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

        return view('instructor.dashboard', [
            'stats' => $stats,
            'monthlyAnalytics' => $monthlyAnalytics,
            'courseStatuses' => $courseStatuses,
            'ratingDistribution' => $ratingDistribution,
            'topCourses' => $topCourses,
            'courseOptions' => Course::query()->where('instructor_id', $user->id)->orderBy('title')->get(['id', 'title']),
            'availableYears' => $availableYears,
            'filters' => compact('year', 'period', 'courseId', 'status'),
        ]);
    }
}
