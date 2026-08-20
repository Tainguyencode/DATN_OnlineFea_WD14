<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstructorCertificate;
use App\Models\User;
use App\Notifications\InstructorApprovedNotification;
use App\Notifications\InstructorRejectedNotification;
use App\Services\ActivityLogService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class InstructorApplicationController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', 'all');

        $query = User::where('role', 'instructor')
            ->with(['instructorProfile', 'instructorApplication', 'instructorCertificates', 'approver']);

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

        $user->load(['instructorProfile', 'instructorApplication', 'instructorCertificates.reviewer', 'approver']);

        return view('admin.instructors.applications.show', [
            'application' => $user,
            'profile' => $user->instructorProfile,
            'certificates' => $user->instructorCertificates,
        ]);
    }

    public function viewCertificate(Request $request, User $user): BinaryFileResponse
    {
        if (! $request->user()?->isAdmin() && $request->user()?->id !== $user->id) {
            abort(403, 'Bạn không có quyền truy cập tài liệu này.');
        }

        // Find the latest certificate from instructorCertificates or fallback to instructorApplication
        $firstCert = $user->instructorCertificates()->first();
        if ($firstCert) {
            return $this->viewCertificateItem($request, $firstCert);
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

    public function viewCertificateItem(Request $request, InstructorCertificate $certificate): BinaryFileResponse
    {
        if (! $request->user()?->isAdmin() && $request->user()?->id !== $certificate->user_id) {
            abort(403, 'Bạn không có quyền truy cập tài liệu này.');
        }

        $relativePath = $certificate->file_path;

        if (Storage::disk('local')->exists($relativePath)) {
            $filePath = Storage::disk('local')->path($relativePath);
            $mimeType = $certificate->mime_type ?: Storage::disk('local')->mimeType($relativePath) ?: 'application/pdf';

            return response()->file($filePath, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . basename($certificate->original_name) . '"',
            ]);
        }

        if (Storage::disk('public')->exists($relativePath)) {
            $filePath = Storage::disk('public')->path($relativePath);
            $mimeType = $certificate->mime_type ?: Storage::disk('public')->mimeType($relativePath) ?: 'application/pdf';

            return response()->file($filePath, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . basename($certificate->original_name) . '"',
            ]);
        }

        abort(404, 'Tệp chứng chỉ không tồn tại trên hệ thống.');
    }

    public function approve(Request $request, User $user): RedirectResponse
    {
        if ($user->role !== 'instructor') {
            return back()->with('error', 'Người dùng không phải giảng viên.');
        }

        $adminId = $request->user()->id;

        $user->update([
            'instructor_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $adminId,
            'rejected_reason' => null,
        ]);

        // Mark all pending certificates as approved
        $user->instructorCertificates()->where('status', 'pending')->update([
            'status' => 'approved',
            'reviewed_at' => now(),
            'reviewed_by' => $adminId,
        ]);

        if ($user->instructorApplication) {
            $user->instructorApplication->update([
                'status' => 'approved',
                'reviewed_at' => now(),
                'reviewed_by' => $adminId,
            ]);
        }

        try {
            $user->notify(new InstructorApprovedNotification());
            app(NotificationService::class)->send(
                $user,
                'Chúc mừng! Hồ sơ Giảng viên đã được phê duyệt',
                'Chúc mừng! Hồ sơ giảng viên của bạn đã được Admin chấp thuận. Bạn có thể bắt đầu tạo khóa học.',
                'instructor_approved',
                route('instructor.dashboard')
            );
        } catch (\Throwable $e) {
            Log::error('Gửi email Duyệt giảng viên thất bại: ' . $e->getMessage());
        }

        ActivityLogService::log($adminId, 'approve_instructor', User::class, $user->id, [], $request);

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
        $adminId = $request->user()->id;

        $user->update([
            'instructor_status' => 'rejected',
            'rejected_reason' => $reason,
        ]);

        if ($user->instructorApplication) {
            $user->instructorApplication->update([
                'status' => 'rejected',
                'admin_notes' => $reason,
                'reviewed_at' => now(),
                'reviewed_by' => $adminId,
            ]);
        }

        try {
            $user->notify(new InstructorRejectedNotification($reason));
            app(NotificationService::class)->send(
                $user,
                'Thông báo về hồ sơ Giảng viên',
                'Hồ sơ đăng ký giảng viên của bạn chưa được duyệt: ' . $reason,
                'instructor_rejected',
                route('instructor.pending')
            );
        } catch (\Throwable $e) {
            Log::error('Gửi email Từ chối giảng viên thất bại: ' . $e->getMessage());
        }

        ActivityLogService::log($adminId, 'reject_instructor', User::class, $user->id, [
            'reason' => $reason,
        ], $request);

        return redirect()->route('admin.instructors.applications.index')
            ->with('success', 'Đã từ chối hồ sơ Giảng viên "' . $user->name . '".');
    }
}
