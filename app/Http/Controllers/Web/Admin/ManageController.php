<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Course;
use App\Models\HomepageSetting;
use App\Models\Order;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ManageController extends Controller
{
    public function pendingCourses(): View
    {
        $courses = Course::where('status', 'pending')
            ->with([
                'instructor:id,name,email',
                'category:id,name',
                'courseSections.lessons',
                'chapters.lessons',
            ])
            ->orderByDesc('submitted_at')
            ->orderBy('created_at')
            ->paginate(10);

        return view('admin.courses.pending', compact('courses'));
    }

    public function review(Course $course): View
    {
        $course->load([
            'instructor:id,name,email,avatar,bio',
            'category:id,name',
            'courseSections.lessons',
            'chapters.lessons',
        ]);

        $curriculumSections = $course->courseSections->isNotEmpty()
            ? $course->courseSections
            : $course->chapters;

        $totalLessons = $curriculumSections->sum(fn ($section) => $section->lessons->count());

        return view('admin.courses.review', compact('course', 'curriculumSections', 'totalLessons'));
    }

    public function approve(Request $request, Course $course): RedirectResponse
    {
        if ($course->status !== Course::STATUS_PENDING) {
            return back()->with('error', 'Chỉ khóa học đang chờ duyệt mới có thể được duyệt.');
        }

        $course->update([
            'status' => Course::STATUS_PUBLISHED,
            'is_published' => true,
            'published_at' => now(),
            'reject_reason' => null,
        ]);
        ActivityLogService::log(auth()->id(), 'approve_course', Course::class, $course->id, null, $request);

        return back()->with('success', "Đã duyệt khóa học \"{$course->title}\".");
    }

    public function reject(Request $request, Course $course): RedirectResponse
    {
        $validated = $request->validate(['reason' => 'required|string|max:1000']);

        if ($course->status !== Course::STATUS_PENDING) {
            return back()->with('error', 'Chỉ khóa học đang chờ duyệt mới có thể bị từ chối.');
        }

        $course->update([
            'status' => Course::STATUS_REJECTED,
            'is_published' => false,
            'reject_reason' => $validated['reason'],
        ]);
        ActivityLogService::log(auth()->id(), 'reject_course', Course::class, $course->id, null, $request);

        return back()->with('success', 'Đã từ chối khóa học.');
    }

    public function revenue(): View
    {
        $totalRevenue = Order::where('status', 'paid')->sum('total_amount');
        $totalOrders = Order::where('status', 'paid')->count();

        $monthExpr = DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', created_at)"
            : "DATE_FORMAT(created_at, '%Y-%m')";

        $monthly = Order::where('status', 'paid')
            ->selectRaw("{$monthExpr} as month, SUM(total_amount) as total, COUNT(*) as count")
            ->groupBy('month')
            ->orderByDesc('month')
            ->limit(12)
            ->get();

        return view('admin.revenue', compact('totalRevenue', 'totalOrders', 'monthly'));
    }

    public function activityLogs(): View
    {
        $logs = ActivityLog::with('user:id,name,email')
            ->orderByDesc('created_at')
            ->paginate(25);

        return view('admin.activity-logs', compact('logs'));
    }

    public function homepage(): View
    {
        $settings = HomepageSetting::pluck('value', 'key');

        return view('admin.homepage', compact('settings'));
    }

    public function updateHomepage(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'banner_title' => 'required|string|max:255',
            'banner_subtitle' => 'required|string|max:500',
            'announcement' => 'nullable|string|max:500',
        ]);

        HomepageSetting::updateOrCreate(
            ['key' => 'banner'],
            ['value' => ['title' => $validated['banner_title'], 'subtitle' => $validated['banner_subtitle']]]
        );

        if ($validated['announcement']) {
            HomepageSetting::updateOrCreate(['key' => 'announcement'], ['value' => $validated['announcement']]);
        }

        return back()->with('success', 'Cập nhật trang chủ thành công!');
    }
}
