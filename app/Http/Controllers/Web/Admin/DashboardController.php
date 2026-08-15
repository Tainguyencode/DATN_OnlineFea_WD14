<?php

namespace App\Http\Controllers\Web\Admin;

use App\Enums\CourseStatus;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $currentMonth = now()->startOfMonth();

        $stats = [
            'revenue_total' => Order::where('status', 'paid')->sum('total_amount'),
            'revenue_month' => Order::where('status', 'paid')->where('created_at', '>=', $currentMonth)->sum('total_amount'),
            'orders_paid_count' => Order::where('status', 'paid')->count(),
            'users_total' => User::count(),
            'students_count' => User::where('role', 'student')->count(),
            'instructors_count' => User::where('role', 'instructor')->count(),
            'courses_published' => Course::where('status', Course::STATUS_PUBLISHED)->where('is_published', true)->count(),
            'courses_pending' => Course::whereIn('status', [Course::STATUS_SUBMITTED, CourseStatus::PendingReview->value])->count(),
            'enrollments_total' => Enrollment::count(),
            'enrollments_month' => Enrollment::where('created_at', '>=', $currentMonth)->count(),
        ];

        $recentLogs = ActivityLog::with('user:id,name')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        $pendingCourses = Course::whereIn('status', [Course::STATUS_SUBMITTED, CourseStatus::PendingReview->value])
            ->with(['instructor:id,name', 'category:id,name'])
            ->orderByDesc('submitted_at')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentLogs', 'pendingCourses'));
    }
}
