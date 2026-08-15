<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\InstructorApprovedNotification;
use App\Notifications\InstructorRejectedNotification;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class InstructorApplicationController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', 'all');

        $query = User::where('role', 'instructor')
            ->with(['instructorProfile', 'instructorApplication', 'approver']);

        if (in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $query->where('instructor_status', $status);
        }

        $applications = $query->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $counts = [
            'all' => User::where('role', 'instructor')->count(),
            'pending' => User::where('role', 'instructor')->where('instructor_status', 'pending')->count(),
            'approved' => User::where('role', 'instructor')->where('instructor_status', 'approved')->count(),
            'rejected' => User::where('role', 'instructor')->where('instructor_status', 'rejected')->count(),
        ];

        return view('admin.instructors.applications.index', [
            'applications' => $applications,
            'status' => $status,
            'counts' => $counts,
        ]);
    }

    public function show(User $user): View|RedirectResponse
    {
        if ($user->role !== 'instructor') {
            return redirect()->route('admin.instructors.applications.index')
                ->with('error', 'Người dùng này không phải là Giảng viên.');
        }

        $user->load(['instructorProfile', 'instructorApplication', 'approver']);

        return view('admin.instructors.applications.show', [
            'application' => $user,
            'profile' => $user->instructorProfile,
        ]);
    }

    public function viewCertificate(Request $request, User $user): BinaryFileResponse
    {
        if (! $request->user()?->isAdmin() && $request->user()?->id !== $user->id) {
            abort(403, 'Bạn không có quyền truy cập tài liệu này.');
        }

        $user->load(['instructorApplication', 'instructorProfile']);

        $relativePath = $user->instructorApplication?->certificate_path;

        if (! $relativePath) {
            abort(404, 'Ứng viên chưa tải lên chứng chỉ.');
        }

        if (Storage::disk('local')->exists($relativePath)) {
            $filePath = Storage::disk('local')->path($relativePath);
            $mimeType = Storage::disk('local')->mimeType($relativePath) ?? 'application/pdf';

            return response()->file($filePath, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . basename($relativePath) . '"',
            ]);
        }

        if (Storage::disk('public')->exists($relativePath)) {
            $filePath = Storage::disk('public')->path($relativePath);
            $mimeType = Storage::disk('public')->mimeType($relativePath) ?? 'application/pdf';

            return response()->file($filePath, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . basename($relativePath) . '"',
            ]);
        }

        abort(404, 'Tệp chứng chỉ không tồn tại trên hệ thống.');
    }

    public function approve(Request $request, User $user): RedirectResponse
    {
        if ($user->role !== 'instructor') {
            return back()->with('error', 'Người dùng không phải giảng viên.');
        }

        $user->update([
            'instructor_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $request->user()->id,
            'rejected_reason' => null,
        ]);

        try {
            $user->notify(new InstructorApprovedNotification());
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Gửi email Duyệt giảng viên thất bại: ' . $e->getMessage());
        }

        ActivityLogService::log($request->user()->id, 'approve_instructor', User::class, $user->id, [], $request);

        return redirect()->route('admin.instructors.applications.index')
            ->with('success', 'Đã duyệt hồ sơ Giảng viên "' . $user->name . '" thành công!');
    }

    public function reject(Request $request, User $user): RedirectResponse
    {
        if ($user->role !== 'instructor') {
            return back()->with('error', 'Người dùng không phải giảng viên.');
        }

        $request->validate([
            'rejected_reason' => ['required', 'string', 'max:1000'],
        ], [
            'rejected_reason.required' => 'Vui lòng nhập lý do từ chối hồ sơ.',
            'rejected_reason.max' => 'Lý do từ chối không quá 1000 ký tự.',
        ]);

        $reason = $request->input('rejected_reason');

        $user->update([
            'instructor_status' => 'rejected',
            'rejected_reason' => $reason,
        ]);

        try {
            $user->notify(new InstructorRejectedNotification($reason));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Gửi email Từ chối giảng viên thất bại: ' . $e->getMessage());
        }

        ActivityLogService::log($request->user()->id, 'reject_instructor', User::class, $user->id, [
            'reason' => $reason,
        ], $request);

        return redirect()->route('admin.instructors.applications.index')
            ->with('success', 'Đã từ chối hồ sơ Giảng viên "' . $user->name . '".');
    }
}
