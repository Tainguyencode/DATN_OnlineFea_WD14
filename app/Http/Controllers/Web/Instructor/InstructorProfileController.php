<?php

namespace App\Http\Controllers\Web\Instructor;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\InstructorCertificate;
use App\Models\InstructorProfile;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\AuthService;
use App\Services\InstructorRequirementService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class InstructorProfileController extends Controller
{
    public function show(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $user->load(['instructorProfile.category', 'instructorApplication', 'instructorCertificates.requirement']);

        $profile = $user->instructorProfile ?? new InstructorProfile(['user_id' => $user->id]);
        $certificates = $user->instructorCertificates;
        $certificatesCount = $certificates->count();

        $sessions = DB::table('sessions')
            ->where('user_id', $user->id)
            ->orderByDesc('last_activity')
            ->get();

        $activityLogs = ActivityLog::where('user_id', $user->id)
            ->latest()
            ->limit(12)
            ->get();

        $documentTypes = InstructorCertificate::documentTypeLabels();

        $deadlineAt = $user->instructor_deadline_at;
        $daysRemaining = $user->instructor_deadline_days_remaining;
        $cooldownDaysRemaining = $user->reactivationCooldownDaysRemaining();
        $canRequestReactivation = $user->canRequestReactivation();

        $requirementData = app(InstructorRequirementService::class)->getRequirementsForInstructor($user);
        $categories = Category::query()
            ->whereNull('parent_id')
            ->with(['children' => fn ($q) => $q->orderBy('name')])
            ->orderBy('name')
            ->get();
        $teachingFields = $user->instructorProfile
            ? $user->instructorProfile->teachingCategories()->get()->map(function ($cat) {
                return [
                    'category_id' => (int) $cat->id,
                    'category_name' => $cat->name,
                    'organization' => $cat->pivot->organization ?? '',
                    'position' => $cat->pivot->position ?? '',
                    'specialty' => $cat->pivot->specialty ?? '',
                    'experience' => $cat->pivot->experience ?? '',
                    'is_primary' => (bool) $cat->pivot->is_primary,
                ];
            })->values()->all()
            : [];

        if (empty($teachingFields) && $profile) {
            $catId = $profile->category_id ?: ($categories->first()?->children->first()?->id ?? $categories->first()?->id);
            if ($catId) {
                $teachingFields[] = [
                    'category_id' => (int) $catId,
                    'category_name' => optional(Category::find($catId))->name ?? '',
                    'organization' => $profile->organization ?? '',
                    'position' => $profile->position ?? '',
                    'specialty' => $profile->specialty ?? '',
                    'experience' => $profile->experience ?? '',
                    'is_primary' => true,
                ];
            }
        }

        $selectedCategoryIds = array_column($teachingFields, 'category_id');

        return view('instructor.profile', [
            'user' => $user,
            'profile' => $profile,
            'certificates' => $certificates,
            'certificatesCount' => $certificatesCount,
            'sessions' => $sessions,
            'activityLogs' => $activityLogs,
            'documentTypes' => $documentTypes,
            'deadlineAt' => $deadlineAt,
            'daysRemaining' => $daysRemaining,
            'cooldownDaysRemaining' => $cooldownDaysRemaining,
            'canRequestReactivation' => $canRequestReactivation,
            'requirementData' => $requirementData,
            'categories' => $categories,
            'selectedCategoryIds' => $selectedCategoryIds,
            'teachingFields' => $teachingFields,
        ]);
    }

    public function update(Request $request, AuthService $authService): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        // Chuẩn hóa teaching_fields nếu gửi qua dạng form dynamic hoặc legacy
        if ($request->has('teaching_fields') && is_array($request->input('teaching_fields'))) {
            $rawFields = $request->input('teaching_fields');
            $cleanFields = [];
            $seenCats = [];
            foreach ($rawFields as $field) {
                $cId = isset($field['category_id']) ? (int) $field['category_id'] : null;
                if ($cId && ! in_array($cId, $seenCats, true)) {
                    $seenCats[] = $cId;
                    $cleanFields[] = [
                        'category_id' => $cId,
                        'organization' => $field['organization'] ?? null,
                        'position' => $field['position'] ?? null,
                        'specialty' => $field['specialty'] ?? null,
                        'experience' => $field['experience'] ?? null,
                    ];
                }
            }
            $request->merge([
                'teaching_fields' => $cleanFields,
                'category_ids' => $seenCats,
            ]);
        } elseif ($request->has('category_ids') || $request->filled('category_id')) {
            $catIds = $request->input('category_ids', [$request->input('category_id')]);
            $cleanFields = [];
            $seenCats = [];
            foreach ((array) $catIds as $cId) {
                $intId = (int) $cId;
                if ($intId && ! in_array($intId, $seenCats, true)) {
                    $seenCats[] = $intId;
                    $cleanFields[] = [
                        'category_id' => $intId,
                        'organization' => $request->input('organization'),
                        'position' => $request->input('position'),
                        'specialty' => $request->input('specialty'),
                        'experience' => $request->input('experience'),
                    ];
                }
            }
            $request->merge([
                'teaching_fields' => $cleanFields,
                'category_ids' => $seenCats,
            ]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'alpha_dash:ascii', 'min:3', 'max:32', 'unique:users,username,'.$user->id],
            'phone' => ['nullable', 'string', 'regex:/^[0-9+\-\s().]{8,20}$/', 'unique:users,phone,'.$user->id],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'category_ids' => ['required', 'array', 'min:1'],
            'category_ids.*' => ['integer', 'distinct', 'exists:categories,id'],
            'teaching_fields' => ['required', 'array', 'min:1'],
            'teaching_fields.*.category_id' => ['required', 'integer', 'exists:categories,id'],
            'teaching_fields.*.organization' => ['nullable', 'string', 'max:255'],
            'teaching_fields.*.position' => ['nullable', 'string', 'max:255'],
            'teaching_fields.*.specialty' => ['nullable', 'string', 'max:255'],
            'teaching_fields.*.experience' => ['nullable', 'string', 'max:3000'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'bank_account_name' => ['nullable', 'string', 'max:100'],
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'username.required' => 'Vui lòng nhập tên đăng nhập.',
            'username.min' => 'Tên đăng nhập phải có ít nhất :min ký tự.',
            'username.unique' => 'Tên đăng nhập này đã tồn tại.',
            'phone.unique' => 'Số điện thoại này đã được sử dụng.',
            'avatar.image' => 'Ảnh đại diện phải là tập tin hình ảnh.',
            'avatar.max' => 'Kích thước ảnh đại diện tối đa là 2MB.',
            'category_ids.required' => 'Vui lòng chọn ít nhất một ngành / lĩnh vực giảng dạy.',
            'category_ids.min' => 'Vui lòng chọn ít nhất một ngành / lĩnh vực giảng dạy.',
            'category_ids.*.exists' => 'Ngành / lĩnh vực giảng dạy đã chọn không hợp lệ.',
            'teaching_fields.required' => 'Vui lòng cấu hình ít nhất một khối ngành giảng dạy.',
            'teaching_fields.min' => 'Vui lòng cấu hình ít nhất một khối ngành giảng dạy.',
        ]);

        if ($request->hasFile('avatar')) {
            $oldAvatar = $user->avatar;
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
            $authService->deleteAvatar($oldAvatar);
        } else {
            unset($validated['avatar']);
        }

        $userUpdates = [
            'name' => $validated['name'],
            'username' => $validated['username'],
            'phone' => $validated['phone'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'avatar' => $validated['avatar'] ?? $user->avatar,
            'bank_name' => $validated['bank_name'] ?? null,
            'bank_account_number' => $validated['bank_account_number'] ?? null,
            'bank_account_name' => $validated['bank_account_name'] ?? null,
            'needs_admin_review' => true,
        ];

        if ($user->instructor_status === 'rejected') {
            $userUpdates['instructor_status'] = 'pending';
            $userUpdates['rejected_reason'] = null;
        }

        $user->update($userUpdates);

        $categoryIds = array_map('intval', $validated['category_ids']);
        app(InstructorRequirementService::class)->handleCategoriesSync($user, $categoryIds);

        $profile = InstructorProfile::where('user_id', $user->id)->first() ?? new InstructorProfile(['user_id' => $user->id]);
        $profile->fill([
            'phone' => $validated['phone'] ?? $profile->phone ?? '',
            'bio' => $validated['bio'] ?? $profile->bio ?? '',
        ])->save();

        // Đồng bộ các khối ngành giảng dạy chi tiết
        $profile->syncTeachingFields($validated['teaching_fields']);

        ActivityLogService::log($user->id, 'update_instructor_profile', User::class, $user->id, null, $request);

        try {
            app(NotificationService::class)->notifyAdmins(
                'Giảng viên cập nhật hồ sơ',
                "Giảng viên {$user->name} ({$user->email}) vừa cập nhật thông tin hồ sơ và đang chờ xét duyệt.",
                'instructor_profile_updated',
                route('admin.instructors.applications.show', $user)
            );
        } catch (\Throwable $e) {
            Log::error('Gửi thông báo cập nhật hồ sơ giảng viên cho admin thất bại: '.$e->getMessage());
        }

        return back()->with('success', 'Cập nhật thông tin hồ sơ thành công.');
    }

    public function uploadDocument(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $request->validate([
            'requirement_id' => ['nullable', 'integer'],
            'document_type' => ['nullable', 'string', 'in:certificate,degree,employment_contract,transcript,employment_confirmation,other'],
            'title' => ['nullable', 'string', 'max:255'],
            'source_type' => ['nullable', 'in:file,url'],
            'document_url' => ['nullable', 'url', 'max:2048', 'regex:/^https?:\/\//i'],
            'files' => ['nullable', 'array', 'max:10'],
            'files.*' => ['file', 'mimes:pdf,jpg,jpeg,png,webp,doc,docx', 'max:10240'],
            'file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp,doc,docx', 'max:10240'],
        ], [
            'files.*.mimes' => 'Tài liệu phải có định dạng: PDF, JPG, PNG, WEBP, DOC, DOCX.',
            'files.*.max' => 'Dung lượng mỗi tài liệu tối đa là 10MB.',
            'file.mimes' => 'Tài liệu phải có định dạng: PDF, JPG, PNG, WEBP, DOC, DOCX.',
            'file.max' => 'Dung lượng tài liệu tối đa là 10MB.',
        ]);

        // Keep legacy upload endpoints working while requiring the new UI to state its intent.
        $sourceType = $request->input('source_type') ?: ($request->hasFile('files') || $request->hasFile('file') ? 'file' : null);
        if (! $sourceType) {
            return back()->withErrors(['source_type' => 'Vui lòng chọn phương thức nộp tài liệu.'])->withInput();
        }
        $files = [];
        if ($sourceType === 'file' && $request->hasFile('files')) {
            $files = $request->file('files');
        } elseif ($sourceType === 'file' && $request->hasFile('file')) {
            $files = [$request->file('file')];
        }

        if ($sourceType === 'file' && empty($files)) {
            return back()->with('error', 'Vui lòng chọn ít nhất một tệp để tải lên.');
        }

        if ($sourceType === 'url' && ! $request->filled('document_url')) {
            return back()->withErrors(['document_url' => 'Vui lòng nhập URL tài liệu hợp lệ.'])->withInput();
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
        $uploadedCount = 0;

        if ($sourceType === 'url') {
            $documentUrl = $request->input('document_url');
            InstructorCertificate::create([
                'user_id' => $user->id,
                'requirement_id' => $requirement?->id,
                'source_type' => 'url',
                'document_url' => $documentUrl,
                'title' => $customTitle ?: $defaultTitle ?: 'Tài liệu liên kết',
                'document_type' => $documentType,
                'status' => 'pending',
                'uploaded_at' => now(),
            ]);
            $uploadedCount = 1;
        }

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
                'source_type' => 'file',
                'file_path' => $storedPath,
                'original_name' => $originalName,
                'mime_type' => $mimeType,
                'file_size' => $fileSize,
                'title' => $customTitle ?: pathinfo($originalName, PATHINFO_FILENAME),
                'document_type' => $documentType,
                'status' => 'pending',
                'uploaded_at' => now(),
            ]);

            $uploadedCount++;
        }

        $userUpdates = ['needs_admin_review' => true];
        if ($user->instructor_status === 'rejected') {
            $userUpdates['instructor_status'] = 'pending';
            $userUpdates['rejected_reason'] = null;
        }
        $user->update($userUpdates);

        ActivityLogService::log($user->id, 'upload_instructor_document', User::class, $user->id, [
            'document_type' => $documentType,
            'requirement_id' => $requirement?->id,
            'uploaded_count' => $uploadedCount,
            'source_type' => $sourceType,
        ], $request);

        try {
            app(NotificationService::class)->notifyAdmins(
                'Giảng viên nộp tài liệu minh chứng',
                "Giảng viên {$user->name} ({$user->email}) vừa nộp {$uploadedCount} tài liệu minh chứng mới.",
                'instructor_documents_uploaded',
                route('admin.instructors.applications.show', $user)
            );
        } catch (\Throwable $e) {
            Log::error('Gửi thông báo nộp tài liệu giảng viên cho admin thất bại: '.$e->getMessage());
        }

        return back()->with('active_tab', 'documents')->with('success', "Đã nộp {$uploadedCount} tài liệu minh chứng thành công. Hồ sơ đang chờ Ban quản trị kiểm duyệt.");
    }

    public function deleteDocument(Request $request, InstructorCertificate $certificate): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($certificate->user_id !== $user->id) {
            abort(403, 'Bạn không có quyền xóa tài liệu này.');
        }

        if ($certificate->status === 'approved') {
            return back()->with('active_tab', 'documents')->with('error', 'Tài liệu đã được phê duyệt, không thể xóa.');
        }

        if (! $certificate->isUrlSource() && $certificate->file_path && Storage::disk('local')->exists($certificate->file_path)) {
            Storage::disk('local')->delete($certificate->file_path);
        }

        $certificate->delete();

        $user->update(['needs_admin_review' => true]);

        ActivityLogService::log($user->id, 'delete_instructor_document', InstructorCertificate::class, $certificate->id, [
            'file_name' => $certificate->original_name,
        ], $request);

        return back()->with('active_tab', 'documents')->with('success', 'Đã xóa tài liệu thành công.');
    }

    public function updateDocumentUrl(Request $request, InstructorCertificate $certificate): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        abort_unless($certificate->user_id === $user->id && $certificate->isUrlSource(), 403);

        if ($certificate->isApproved()) {
            return back()->with('active_tab', 'documents')->with('error', 'Tài liệu đã được phê duyệt, không thể sửa liên kết.');
        }

        $validated = $request->validate([
            'document_url' => ['required', 'url', 'max:2048', 'regex:/^https?:\/\//i'],
        ], [
            'document_url.url' => 'URL tài liệu không hợp lệ.',
            'document_url.regex' => 'Chỉ chấp nhận URL bắt đầu bằng http:// hoặc https://.',
        ]);

        $certificate->update([
            'document_url' => $validated['document_url'],
            'status' => 'pending',
            'rejection_reason' => null,
            'reviewed_at' => null,
            'reviewed_by' => null,
        ]);

        $user->update(['needs_admin_review' => true]);

        ActivityLogService::log($user->id, 'update_instructor_document_url', InstructorCertificate::class, $certificate->id, [], $request);

        return back()->with('active_tab', 'documents')->with('success', 'Đã cập nhật liên kết tài liệu và chuyển về trạng thái chờ duyệt.');
    }

    public function viewDocument(Request $request, InstructorCertificate $certificate): BinaryFileResponse|RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if (! $user) {
            abort(403, 'Vui lòng đăng nhập.');
        }

        if (! $user->isAdmin() && $certificate->user_id !== $user->id) {
            abort(403, 'Bạn không có quyền truy cập tài liệu này.');
        }

        if ($certificate->isUrlSource()) {
            abort_unless(filled($certificate->document_url), 404, 'URL tài liệu không tồn tại.');

            return redirect()->away($certificate->document_url);
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

        abort(404, 'Tệp tài liệu không tồn tại trên hệ thống.');
    }

    public function submitForReview(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $certsCount = $user->instructorCertificates()->count();
        if ($certsCount === 0) {
            return back()->with('error', 'Vui lòng bổ sung ít nhất một chứng chỉ/tài liệu trước khi gửi xét duyệt hồ sơ.');
        }

        $user->update([
            'submitted_for_review_at' => now(),
            'instructor_status' => 'pending',
            'needs_admin_review' => true,
            'rejected_reason' => null,
        ]);

        if ($user->instructorApplication) {
            $user->instructorApplication->update([
                'status' => 'pending',
                'admin_notes' => null,
            ]);
        }

        ActivityLogService::log($user->id, 'submit_instructor_application', User::class, $user->id, [
            'certificates_count' => $certsCount,
        ], $request);

        try {
            app(NotificationService::class)->notifyAdmins(
                'Hồ sơ Giảng viên mới cần duyệt',
                "Giảng viên {$user->name} ({$user->email}) vừa gửi hồ sơ xét duyệt với {$certsCount} tài liệu.",
                'instructor_application_submitted',
                route('admin.instructors.applications.show', $user)
            );
        } catch (\Throwable $e) {
            Log::error('Gửi thông báo nộp hồ sơ giảng viên cho admin thất bại: '.$e->getMessage());
        }

        return back()->with('active_tab', 'documents')->with('success', 'Hồ sơ xét duyệt giảng viên đã được gửi thành công! Ban quản trị sẽ tiến hành kiểm tra và phản hồi sớm.');
    }

    public function requestReactivation(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if (! $user->isLocked()) {
            return back()->with('info', 'Tài khoản của bạn đang hoạt động bình thường.');
        }

        if (! $user->canRequestReactivation()) {
            $days = $user->reactivationCooldownDaysRemaining();

            return back()->with('error', "Bạn cần chờ thêm {$days} ngày nữa mới có thể gửi yêu cầu cấp lại quyền giảng viên.");
        }

        $request->validate([
            'reason' => ['required', 'string', 'min:10', 'max:2000'],
        ], [
            'reason.required' => 'Vui lòng nhập lý do / giải trình đề nghị cấp lại quyền giảng viên.',
            'reason.min' => 'Nội dung giải trình phải có ít nhất :min ký tự.',
            'reason.max' => 'Nội dung giải trình không vượt quá :max ký tự.',
        ]);

        $user->update([
            'reactivation_requested_at' => now(),
            'reactivation_status' => 'pending',
            'reactivation_reason' => $request->input('reason'),
            'needs_admin_review' => true,
        ]);

        ActivityLogService::log($user->id, 'request_instructor_reactivation', User::class, $user->id, [
            'reason' => $request->input('reason'),
        ], $request);

        try {
            app(NotificationService::class)->notifyAdmins(
                'Yêu cầu cấp lại quyền Giảng viên',
                "Giảng viên {$user->name} ({$user->email}) vừa gửi yêu cầu mở khóa và cấp lại quyền giảng viên.",
                'instructor_reactivation_requested',
                route('admin.instructors.applications.show', $user)
            );
        } catch (\Throwable $e) {
            Log::error('Gửi thông báo yêu cầu cấp lại quyền giảng viên cho admin thất bại: '.$e->getMessage());
        }

        return back()->with('success', 'Yêu cầu cấp lại quyền giảng viên đã được gửi tới Ban quản trị. Vui lòng chờ xét duyệt.');
    }
}
