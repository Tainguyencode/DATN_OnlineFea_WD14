<?php

namespace App\Http\Controllers\Web\Student;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Certificate;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $summary = Enrollment::query()
            ->where('user_id', $user->id)
            ->withLearningAccess()
            ->selectRaw('COUNT(*) as enrolled')
            ->selectRaw('SUM(CASE WHEN completed_at IS NULL AND progress_percent < 100 THEN 1 ELSE 0 END) as in_progress')
            ->selectRaw('SUM(CASE WHEN completed_at IS NOT NULL OR status = ? THEN 1 ELSE 0 END) as completed', [Enrollment::STATUS_COMPLETED])
            ->selectRaw('AVG(progress_percent) as average_progress')
            ->first();

        $continueLearning = Enrollment::query()
            ->where('user_id', $user->id)
            ->withLearningAccess()
            ->whereNull('completed_at')
            ->where('progress_percent', '<', 100)
            ->whereHas('course')
            ->with(['course.instructor:id,name', 'course.category:id,name', 'course.lessons:id,course_id,sort_order'])
            ->orderByDesc('last_accessed_at')
            ->orderByDesc('updated_at')
            ->limit(3)
            ->get();

        $stats = [
            'enrolled' => (int) ($summary?->enrolled ?? 0),
            'in_progress' => (int) ($summary?->in_progress ?? 0),
            'completed' => (int) ($summary?->completed ?? 0),
            'certificates' => Certificate::query()
                ->where('user_id', $user->id)
                ->whereNotNull('certificate_code')
                ->whereNotNull('issued_at')
                ->count(),
        ];

        $recentActivities = ActivityLog::query()
            ->where('user_id', $user->id)
            ->whereIn('action', [
                'enroll_course', 'complete_lesson', 'complete_course',
                'certificate_issued', 'payment_success', 'update_lesson_progress',
            ])
            ->latest()
            ->limit(5)
            ->get();

        return view('student.dashboard.overview.index', [
            'user' => $user,
            'emailVerified' => ! config('auth.email_verification_enabled', true) || $user->hasVerifiedEmail(),
            'stats' => $stats,
            'avgProgress' => (float) ($summary?->average_progress ?? 0),
            'continueLearning' => $continueLearning,
            'recentActivities' => $recentActivities,
        ]);
    }
}
