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
        $stats = [
            'users' => User::count(),
            'students' => User::where('role', 'student')->count(),
            'instructors' => User::where('role', 'instructor')->count(),
            'courses' => Course::where('status', Course::STATUS_PUBLISHED)->where('is_published', true)->count(),
            'pending' => Course::whereIn('status', [Course::STATUS_SUBMITTED, CourseStatus::PendingReview->value])->count(),
            'revenue' => Order::where('status', 'paid')->sum('total_amount'),
            'enrollments' => Enrollment::count(),
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
