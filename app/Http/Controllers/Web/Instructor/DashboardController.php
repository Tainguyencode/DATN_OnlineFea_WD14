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
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $period = in_array((int) $request->input('period', 6), [6, 12], true)
            ? (int) $request->input('period', 6)
            : 6;
        $courseId = $request->integer('course_id') ?: null;
        $status = in_array($request->string('status')->toString(), CourseStatus::values(), true)
            ? $request->string('status')->toString()
            : null;
        $periodStart = now()->startOfMonth()->subMonths($period - 1);

        $applyCourseFilters = static function (Builder $query) use ($user, $courseId, $status): void {
            $query
                ->where('instructor_id', $user->id)
                ->when($courseId, fn (Builder $q) => $q->whereKey($courseId))
                ->when($status, fn (Builder $q) => $q->where('status', $status));
        };

        $courseQuery = Course::query();
        $applyCourseFilters($courseQuery);
        $enrollmentQuery = Enrollment::query()
            ->where('created_at', '>=', $periodStart)
            ->whereHas('course', $applyCourseFilters);
        $reviewQuery = Review::query()
            ->whereNull('parent_id')
            ->whereHas('course', $applyCourseFilters);
        $paidItemQuery = OrderItem::query()
            ->where('created_at', '>=', $periodStart)
            ->whereHas('order', fn (Builder $q) => $q->where('status', 'paid'))
            ->whereHas('course', $applyCourseFilters);

        $stats = [
            'revenue' => (float) (clone $paidItemQuery)->sum('instructor_earning'),
            'courses' => (clone $courseQuery)->count(),
            'published' => (clone $courseQuery)->where('status', CourseStatus::Published->value)->count(),
            'students' => (clone $enrollmentQuery)->where('status', 'active')->distinct()->count('user_id'),
            'enrollments' => (clone $enrollmentQuery)->count(),
            'reviews' => (clone $reviewQuery)->count(),
            'average_rating' => round((float) ((clone $reviewQuery)->avg('rating') ?? 0), 1),
            'helpful_reviews' => (int) (clone $reviewQuery)->sum('helpful_count'),
        ];

        $monthlyAnalytics = collect(range($period - 1, 0))->map(function (int $monthsAgo) use ($applyCourseFilters): array {
            $start = now()->startOfMonth()->subMonths($monthsAgo);
            $end = $start->copy()->endOfMonth();
            $items = OrderItem::query()
                ->whereBetween('created_at', [$start, $end])
                ->whereHas('order', fn (Builder $q) => $q->where('status', 'paid'))
                ->whereHas('course', $applyCourseFilters);

            return [
                'label' => 'T'.$start->month,
                'full_label' => $start->format('m/Y'),
                'revenue' => (float) (clone $items)->sum('instructor_earning'),
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
            ];
        })->filter(fn (array $item) => $item['value'] > 0)->values();

        $ratingDistribution = collect(range(1, 5))->map(fn (int $rating): array => [
            'label' => $rating.' sao',
            'value' => (clone $reviewQuery)->where('rating', $rating)->count(),
        ]);

        $topCourses = (clone $courseQuery)
            ->with('category:id,name')
            ->withCount([
                'enrollments as period_enrollments_count' => fn (Builder $q) => $q->where('created_at', '>=', $periodStart),
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
            'filters' => compact('period', 'courseId', 'status'),
        ]);
    }
}
