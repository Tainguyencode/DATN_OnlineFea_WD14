<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstructorCertificate;
use App\Models\User;
use App\Notifications\InstructorApprovedNotification;
use App\Notifications\InstructorRejectedNotification;
use App\Services\ActivityLogService;
use App\Services\CourseReviewService;
use App\Services\InstructorRequirementService;
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
            ->with(['instructorProfile.teachingCategories', 'instructorApplication', 'instructorCertificates', 'approver']);

        if ($status === 'new_updates') {
            $query->where('needs_admin_review', true);
        } elseif (in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $query->where('instructor_status', $status);
        }

        $applications = $query
            ->orderByDesc('needs_admin_review')
            ->orderByRaw("CASE WHEN instructor_status = 'pending' THEN 1 ELSE 2 END")
            ->orderByDesc('updated_at')
            ->paginate(15)
            ->withQueryString();

        $counts = [
            'all' => User::where('role', 'instructor')->count(),
            'new_updates' => User::where('role', 'instructor')->where('needs_admin_review', true)->count(),
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

        $hadNewUpdates = $user->needs_admin_review;

        if ($user->needs_admin_review) {
            $user->markAdminReviewed();
        }

        $user->load(['instructorProfile.category', 'instructorApplication', 'instructorCertificates.reviewer', 'instructorCertificates.requirement', 'approver']);

        $requirementData = app(InstructorRequirementService::class)->getRequirementsForInstructor($user);

        return view('admin.instructors.applications.show', [
            'application' => $user,
            'profile' => $user->instructorProfile,
            'certificates' => $user->instructorCertificates,
            'hadNewUpdates' => $hadNewUpdates,
            'requirementData' => $requirementData,
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
                'Content-Disposition' => 'inline; filename="'.basename($relativePath).'"',
            ]);
        }

        if (Storage::disk('public')->exists($relativePath)) {
            $filePath = Storage::disk('public')->path($relativePath);
            $mimeType = Storage::disk('public')->mimeType($relativePath) ?? 'application/pdf';

            return response()->file($filePath, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="'.basename($relativePath).'"',
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
                'Content-Disposition' => 'inline; filename="'.basename($certificate->original_name).'"',
            ]);
        }

        if (Storage::disk('public')->exists($relativePath)) {
            $filePath = Storage::disk('public')->path($relativePath);
            $mimeType = $certificate->mime_type ?: Storage::disk('public')->mimeType($relativePath) ?: 'application/pdf';

            return response()->file($filePath, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="'.basename($certificate->original_name).'"',
            ]);
        }

        abort(404, 'Tệp chứng chỉ không tồn tại trên hệ thống.');
    }

    public function approve(Request $request, User $user): RedirectResponse
    {
        if ($user->role !== 'instructor') {
            return back()->with('error', 'Người dùng không phải giảng viên.');
        }

        // Kiểm tra xem giảng viên đã nộp đầy đủ toàn bộ tài liệu bắt buộc của ngành chưa
        $completeness = app(InstructorRequirementService::class)->checkCanApproveInstructor($user);
        if (! $completeness['can_approve']) {
            $missingList = implode(', ', $completeness['missing_titles']);

            return back()->with('error', "Không thể duyệt hồ sơ. Giảng viên còn thiếu tài liệu bắt buộc của ngành: {$missingList}.");
        }

        $adminId = $request->user()->id;

        $user->update([
            'instructor_status' => 'approved',
            'needs_admin_review' => false,
            'admin_last_reviewed_at' => now(),
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
            $user->notify(new InstructorApprovedNotification);
            app(NotificationService::class)->send(
                $user,
                'Chúc mừng! Hồ sơ Giảng viên đã được phê duyệt',
                'Chúc mừng! Hồ sơ giảng viên của bạn đã được Admin chấp thuận. Bạn có thể bắt đầu tạo khóa học.',
                'instructor_approved',
                route('instructor.dashboard')
            );
        } catch (\Throwable $e) {
            Log::error('Gửi email Duyệt giảng viên thất bại: '.$e->getMessage());
        }

        // Tự động kích hoạt xuất bản toàn bộ các khóa học đã được Admin duyệt nội dung trước đó của giảng viên
        $publishedCoursesCount = app(CourseReviewService::class)->syncInstructorApprovedCourses($user);

        ActivityLogService::log($adminId, 'approve_instructor', User::class, $user->id, [
            'auto_published_courses_count' => $publishedCoursesCount,
        ], $request);

        $successMessage = 'Đã duyệt hồ sơ Giảng viên "'.$user->name.'" thành công!';
        if ($publishedCoursesCount > 0) {
            $successMessage .= " Đồng thời tự động kích hoạt xuất bản {$publishedCoursesCount} khóa học đã được duyệt trước đó.";
        }

        return redirect()->route('admin.instructors.applications.index')
            ->with('success', $successMessage);
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
            'needs_admin_review' => false,
            'admin_last_reviewed_at' => now(),
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
                'Hồ sơ đăng ký giảng viên của bạn chưa được duyệt: '.$reason,
                'instructor_rejected',
                route('instructor.pending')
            );
        } catch (\Throwable $e) {
            Log::error('Gửi email Từ chối giảng viên thất bại: '.$e->getMessage());
        }

        ActivityLogService::log($adminId, 'reject_instructor', User::class, $user->id, [
            'reason' => $reason,
        ], $request);

        return redirect()->route('admin.instructors.applications.index')
            ->with('success', 'Đã từ chối hồ sơ Giảng viên "'.$user->name.'".');
    }

    public function reviewDocument(Request $request, User $user, InstructorCertificate $certificate): RedirectResponse
    {
        if ($certificate->user_id !== $user->id) {
            abort(404, 'Tài liệu không thuộc về giảng viên này.');
        }

        $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'rejection_reason' => ['nullable', 'required_if:status,rejected', 'string', 'max:1000'],
        ], [
            'status.required' => 'Vui lòng chọn trạng thái xét duyệt.',
            'rejection_reason.required_if' => 'Vui lòng nhập lý do từ chối tài liệu.',
        ]);

        $status = $request->input('status');
        $reason = $request->input('rejection_reason');
        $adminId = $request->user()->id;

        $certificate->update([
            'status' => $status,
            'rejection_reason' => $status === 'rejected' ? $reason : null,
            'reviewed_at' => now(),
            'reviewed_by' => $adminId,
        ]);

        ActivityLogService::log($adminId, 'review_instructor_document', InstructorCertificate::class, $certificate->id, [
            'status' => $status,
            'reason' => $reason,
        ], $request);

        $docName = $certificate->title ?: $certificate->original_name;
        if ($status === 'rejected') {
            app(NotificationService::class)->send(
                $user,
                'Tài liệu minh chứng bị từ chối',
                "Tài liệu '{$docName}' của bạn đã bị từ chối: {$reason}. Vui lòng cập nhật tài liệu khác.",
                'instructor_document_rejected',
                route('instructor.profile')
            );
        } else {
            app(NotificationService::class)->send(
                $user,
                'Tài liệu minh chứng đã được duyệt',
                "Tài liệu '{$docName}' của bạn đã được Admin chấp thuận.",
                'instructor_document_approved',
                route('instructor.profile')
            );
        }

        return back()->with('success', 'Đã cập nhật trạng thái tài liệu "'.$docName.'" thành công.');
    }

    public function approveReactivation(Request $request, User $user): RedirectResponse
    {
        if ($user->role !== 'instructor') {
            return back()->with('error', 'Người dùng không phải giảng viên.');
        }

        $adminId = $request->user()->id;

        $user->unlockAccount('active', 'approved');

        ActivityLogService::log($adminId, 'approve_instructor_reactivation', User::class, $user->id, [], $request);

        try {
            app(NotificationService::class)->send(
                $user,
                'Tài khoản Giảng viên đã được mở khóa',
                'Chúc mừng! Yêu cầu cấp lại quyền giảng viên của bạn đã được Ban quản trị phê duyệt. Bạn có thể tiếp tục sử dụng đầy đủ các chức năng.',
                'instructor_reactivation_approved',
                route('instructor.dashboard')
            );
        } catch (\Throwable $e) {
            Log::error('Gửi thông báo mở khóa tài khoản giảng viên thất bại: '.$e->getMessage());
        }

        return back()->with('success', 'Đã phê duyệt mở khóa và cấp lại quyền giảng viên cho "'.$user->name.'".');
    }

    public function rejectReactivation(Request $request, User $user): RedirectResponse
    {
        if ($user->role !== 'instructor') {
            return back()->with('error', 'Người dùng không phải giảng viên.');
        }

        $request->validate([
            'notes' => ['required', 'string', 'max:1000'],
        ], [
            'notes.required' => 'Vui lòng nhập lý do từ chối yêu cầu cấp lại quyền.',
        ]);

        $notes = $request->input('notes');
        $adminId = $request->user()->id;

        $user->update([
            'reactivation_status' => 'rejected',
            'locked_reason' => $notes,
        ]);

        ActivityLogService::log($adminId, 'reject_instructor_reactivation', User::class, $user->id, [
            'reason' => $notes,
        ], $request);

        try {
            app(NotificationService::class)->send(
                $user,
                'Yêu cầu cấp lại quyền Giảng viên bị từ chối',
                'Yêu cầu mở khóa và cấp lại quyền giảng viên của bạn chưa được chấp thuận: '.$notes,
                'instructor_reactivation_rejected',
                route('instructor.profile')
            );
        } catch (\Throwable $e) {
            Log::error('Gửi thông báo từ chối cấp lại giảng viên thất bại: '.$e->getMessage());
        }

        return back()->with('success', 'Đã từ chối yêu cầu cấp lại quyền giảng viên cho "'.$user->name.'".');
    }
}
