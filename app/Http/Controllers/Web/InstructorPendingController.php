<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\InstructorCertificate;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\InstructorRequirementService;
use App\Services\InstructorReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class InstructorPendingController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('home');
        }

        // Check if user was demoted to student due to expiry
        if ($user->role === 'student') {
            if ($user->instructor_status === 'expired') {
                return redirect()->route('student.dashboard')
                    ->with('error', 'Hồ sơ giảng viên của bạn đã hết hạn 7 ngày hoàn thiện và tài khoản đã được chuyển về Học viên.');
            }

            return redirect()->route('student.dashboard');
        }

        if ($user->role !== 'instructor') {
            return redirect()->route('home');
        }

        // Check 7-day deadline: if expired without submission -> demote to student
        if ($user->isInstructorDeadlineExpired()) {
            $user->demoteToStudentDueToExpiry();
            ActivityLogService::log($user->id, 'instructor_deadline_expired', User::class, $user->id, [
                'email_verified_at' => $user->email_verified_at,
            ], $request);

            return redirect()->route('student.dashboard')
                ->with('error', 'Đã quá thời hạn 7 ngày hoàn thiện hồ sơ kể từ khi xác thực email mà chưa gửi hồ sơ xét duyệt. Tài khoản của bạn đã được chuyển về Học viên.');
        }

        if ($user->instructor_status === 'approved') {
            return redirect()->route('instructor.dashboard');
        }

        $user->load(['instructorProfile', 'instructorApplication', 'instructorCertificates']);
        $certificates = $user->instructorCertificates;
        $certificatesCount = $certificates->count();

        // Determine the UI state (1 to 5)
        $state = 1;
        if ($user->instructor_status === 'approved') {
            $state = 5;
        } elseif ($user->instructor_status === 'rejected') {
            $state = 4;
        } elseif ($user->isGlobalReviewPending()) {
            $state = 3;
        } elseif ($certificatesCount > 0) {
            $state = 2;
        } else {
            $state = 1;
        }

        $deadlineAt = $user->instructor_deadline_at;
        $daysRemaining = $user->instructor_deadline_days_remaining;

        $requirementData = app(InstructorRequirementService::class)->getRequirementsForInstructor($user);
        $categories = Category::query()
            ->whereNull('parent_id')
            ->with(['children' => fn ($q) => $q->orderBy('name')])
            ->orderBy('name')
            ->get();

        return view('instructor.pending', [
            'user' => $user,
            'profile' => $user->instructorProfile,
            'application' => $user->instructorApplication,
            'certificates' => $certificates,
            'certificatesCount' => $certificatesCount,
            'state' => $state,
            'deadlineAt' => $deadlineAt,
            'daysRemaining' => $daysRemaining,
            'requirementData' => $requirementData,
            'categories' => $categories,
        ]);
    }

    public function uploadCertificate(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->isInstructorDeadlineExpired()) {
            $user->demoteToStudentDueToExpiry();

            return redirect()->route('student.dashboard')
                ->with('error', 'Đã quá thời hạn 7 ngày hoàn thiện hồ sơ. Tài khoản đã chuyển về Học viên.');
        }

        $request->validate([
            'requirement_id' => ['nullable', 'integer'],
            'title' => ['nullable', 'string', 'max:255'],
            'files' => ['nullable', 'array', 'max:10'],
            'files.*' => ['file', 'mimes:pdf,jpg,jpeg,png,webp,doc,docx', 'max:10240'],
            'file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp,doc,docx', 'max:10240'],
        ], [
            'files.*.mimes' => 'Tài liệu phải có định dạng: PDF, JPG, PNG, WEBP, DOC, DOCX.',
            'files.*.max' => 'Dung lượng mỗi tài liệu tối đa là 10MB.',
            'file.mimes' => 'Tài liệu phải có định dạng: PDF, JPG, PNG, WEBP, DOC, DOCX.',
            'file.max' => 'Dung lượng tài liệu tối đa là 10MB.',
        ]);

        $uploadedCount = 0;
        $files = [];

        if ($request->hasFile('files')) {
            $files = $request->file('files');
        } elseif ($request->hasFile('file')) {
            $files = [$request->file('file')];
        }

        if (empty($files)) {
            return back()->with('error', 'Vui lòng chọn ít nhất một tệp để tải lên.');
        }

        $requirementId = $request->input('requirement_id');
        $requirement = null;

        if ($requirementId) {
            $requirement = app(InstructorRequirementService::class)
                ->validateRequirementForInstructor($user, (int) $requirementId);
            $documentType = $requirement->document_type;
            $defaultTitle = $requirement->document_title;
        } else {
            $documentType = $request->input('document_type', 'certificate');
            $defaultTitle = null;
        }

        $customTitle = $request->input('title') ?: $defaultTitle;

        foreach ($files as $file) {
            if (! $file || ! $file->isValid()) {
                continue;
            }

            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension() ?: 'pdf';
            $mimeType = $file->getClientMimeType();
            $fileSize = $file->getSize();

            $storedPath = $file->storeAs(
                "instructor-certificates/{$user->id}",
                Str::uuid().'.'.$extension,
                'local'
            );

            InstructorCertificate::create([
                'user_id' => $user->id,
                'requirement_id' => $requirement?->id,
                'file_path' => $storedPath,
                'original_name' => $originalName,
                'mime_type' => $mimeType,
                'file_size' => $fileSize,
                'title' => $customTitle ?: pathinfo($originalName, PATHINFO_FILENAME),
                'document_type' => $documentType,
                'status' => 'draft',
                'uploaded_at' => now(),
            ]);

            $uploadedCount++;
        }

        ActivityLogService::log($user->id, 'upload_instructor_certificate', User::class, $user->id, [
            'uploaded_count' => $uploadedCount,
            'requirement_id' => $requirement?->id,
        ], $request);

        return redirect()->route('instructor.pending')
            ->with('success', "Đã tải lên {$uploadedCount} tài liệu thành công. Kết quả phân tích AI đã được cập nhật.");
    }

    public function deleteCertificate(Request $request, InstructorCertificate $certificate): RedirectResponse
    {
        $user = $request->user();

        if ($certificate->user_id !== $user->id) {
            abort(403, 'Bạn không có quyền xóa chứng chỉ này.');
        }

        if ($user->instructor_status === 'approved') {
            return back()->with('error', 'Hồ sơ đã được phê duyệt, không thể xóa chứng chỉ.');
        }

        if (Storage::disk('local')->exists($certificate->file_path)) {
            Storage::disk('local')->delete($certificate->file_path);
        }

        $certificate->delete();

        ActivityLogService::log($user->id, 'delete_instructor_certificate', InstructorCertificate::class, $certificate->id, [
            'file_name' => $certificate->original_name,
        ], $request);

        return redirect()->route('instructor.pending')
            ->with('success', 'Đã xóa chứng chỉ thành công.');
    }

    public function viewCertificate(Request $request, InstructorCertificate $certificate): BinaryFileResponse
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'Vui lòng đăng nhập.');
        }

        if (! $user->isAdmin() && $certificate->user_id !== $user->id) {
            abort(403, 'Bạn không có quyền truy cập chứng chỉ này.');
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

    public function submitForReview(Request $request, InstructorReviewService $reviewService): RedirectResponse
    {
        $user = $request->user();

        if ($user->isInstructorDeadlineExpired()) {
            $user->demoteToStudentDueToExpiry();

            return redirect()->route('student.dashboard')
                ->with('error', 'Đã quá thời hạn 7 ngày hoàn thiện hồ sơ. Tài khoản đã chuyển về Học viên.');
        }

        $result = $reviewService->submitGlobal($user);
        if (! $result['submitted']) {
            return back()->with('error', $result['error']);
        }

        ActivityLogService::log($user->id, 'submit_instructor_application', User::class, $user->id, [
            'certificates_count' => $result['certificates_count'],
        ], $request);

        return redirect()->route('instructor.pending')
            ->with('success', 'Hồ sơ xét duyệt giảng viên của bạn đã được gửi thành công! Ban quản trị sẽ tiến hành kiểm tra và phản hồi sớm.');
    }

    public function resubmit(Request $request, InstructorReviewService $reviewService): RedirectResponse
    {
        $user = $request->user();
        $result = $reviewService->submitGlobal($user);
        if (! $result['submitted']) {
            return back()->with('error', $result['error']);
        }

        ActivityLogService::log($user->id, 'resubmit_instructor_application', User::class, $user->id, [
            'certificates_count' => $result['certificates_count'],
        ], $request);

        return redirect()->route('instructor.pending')
            ->with('success', 'Hồ sơ của bạn đã được cập nhật và gửi lại cho Ban quản trị xét duyệt.');
    }
}
