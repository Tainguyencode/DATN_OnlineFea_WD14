<?php

namespace App\Http\Controllers\Web\Admin;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\InstructorApplication;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstructorApplicationController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->get('status', 'pending');

        $applications = InstructorApplication::with('user')
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.instructor-applications.index', compact('applications', 'status'));
    }

    public function approve(Request $request, InstructorApplication $application): RedirectResponse
    {
        if (! $application->isPending()) {
            return back()->with('error', 'Đơn này đã được xử lý.');
        }

        $application->update([
            'status' => 'approved',
            'reviewed_by' => auth('admin')->id(),
            'reviewed_at' => now(),
            'admin_notes' => $request->input('admin_notes'),
        ]);

        $application->user->update([
            'role' => UserRole::Instructor->value,
            'status' => UserStatus::Active->value,
            'is_active' => true,
        ]);

        ActivityLogService::log(
            auth('admin')->id(),
            'instructor_application_approved',
            InstructorApplication::class,
            $application->id,
            ['user_id' => $application->user_id],
            $request
        );

        return back()->with('success', 'Đã duyệt đơn đăng ký giảng viên.');
    }

    public function reject(Request $request, InstructorApplication $application): RedirectResponse
    {
        if (! $application->isPending()) {
            return back()->with('error', 'Đơn này đã được xử lý.');
        }

        $request->validate(['admin_notes' => 'nullable|string|max:1000']);

        $application->update([
            'status' => 'rejected',
            'reviewed_by' => auth('admin')->id(),
            'reviewed_at' => now(),
            'admin_notes' => $request->input('admin_notes'),
        ]);

        ActivityLogService::log(
            auth('admin')->id(),
            'instructor_application_rejected',
            InstructorApplication::class,
            $application->id,
            ['user_id' => $application->user_id],
            $request
        );

        return back()->with('success', 'Đã từ chối đơn đăng ký giảng viên.');
    }
}
