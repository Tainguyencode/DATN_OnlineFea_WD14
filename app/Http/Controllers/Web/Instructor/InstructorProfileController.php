<?php

namespace App\Http\Controllers\Web\Instructor;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\InstructorCertificate;
use App\Models\InstructorProfile;
use App\Models\InstructorTeachingField;
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
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class InstructorProfileController extends Controller
{
    public function show(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $user->load(['instructorProfile.category', 'instructorApplication', 'instructorCertificates.requirement', 'instructorCertificates.teachingField']);

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

        $requirementService = app(InstructorRequirementService::class);
        $requirementData = $requirementService->getRequirementsForInstructor($user);
        $submitEligibility = $requirementService->getSubmitEligibility($user);
        $categories = Category::query()
            ->whereNull('parent_id')
            ->with(['children' => fn ($q) => $q->orderBy('name')])
            ->orderBy('name')
            ->get();
        $teachingFieldRecords = $user->instructorProfile
            ? $user->instructorProfile->teachingFields()->with('category.parent')->orderBy('id')->get()
            : collect();
        $teachingFields = $teachingFieldRecords->map(function (InstructorTeachingField $field) {
            return [
                'teaching_field_id' => $field->id,
                'category_id' => (int) $field->category_id,
                'category_name' => $field->category?->name ?? '',
                'organization' => $field->organization ?? '',
                'position' => $field->position ?? '',
                'specialty' => $field->specialty ?? '',
                'experience' => $field->experience ?? '',
                'is_primary' => (bool) $field->is_primary,
                'approval_status' => $field->approval_status,
                'rejection_reason' => $field->rejection_reason,
                'replace_of_teaching_field_id' => $field->replace_of_teaching_field_id,
            ];
        })->values()->all();

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
        $teachingFieldRequirementData = $teachingFieldRecords
            ->mapWithKeys(fn (InstructorTeachingField $field) => [$field->id => $requirementService->getTeachingFieldRequirementData($field)]);

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
            'submitEligibility' => $submitEligibility,
            'categories' => $categories,
            'selectedCategoryIds' => $selectedCategoryIds,
            'teachingFields' => $teachingFields,
            'teachingFieldRecords' => $teachingFieldRecords,
            'teachingFieldRequirementData' => $teachingFieldRequirementData,
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
                        'teaching_field_id' => isset($field['teaching_field_id']) ? (int) $field['teaching_field_id'] : null,
                        'replace_of_teaching_field_id' => isset($field['replace_of_teaching_field_id']) ? (int) $field['replace_of_teaching_field_id'] : null,
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
            'teaching_fields.*.teaching_field_id' => ['nullable', 'integer'],
            'teaching_fields.*.replace_of_teaching_field_id' => ['nullable', 'integer'],
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
        ];

        $user->update($userUpdates);

        $profile = InstructorProfile::where('user_id', $user->id)->first() ?? new InstructorProfile(['user_id' => $user->id]);
        $profile->fill([
            'phone' => $validated['phone'] ?? $profile->phone ?? '',
            'bio' => $validated['bio'] ?? $profile->bio ?? '',
        ])->save();

        $teachingFieldStateBefore = $profile->teachingFields()
            ->orderBy('id')
            ->get(['id', 'category_id', 'approval_status'])
            ->map(fn (InstructorTeachingField $field) => [
                'id' => (int) $field->id,
                'category_id' => (int) $field->category_id,
                'approval_status' => $field->approval_status,
            ])
            ->all();

        // Đồng bộ các khối ngành giảng dạy chi tiết
        $profile->saveTeachingFieldRequests($validated['teaching_fields']);

        $teachingFieldStateAfter = $profile->teachingFields()
            ->orderBy('id')
            ->get(['id', 'category_id', 'approval_status'])
            ->map(fn (InstructorTeachingField $field) => [
                'id' => (int) $field->id,
                'category_id' => (int) $field->category_id,
                'approval_status' => $field->approval_status,
            ])
            ->all();
        $hasTeachingFieldRequestChanges = $user->instructor_status === 'approved'
            && $teachingFieldStateBefore !== $teachingFieldStateAfter
            && collect($teachingFieldStateAfter)->contains(
                fn (array $field) => $field['approval_status'] === InstructorTeachingField::STATUS_DRAFT
            );

        ActivityLogService::log($user->id, 'update_instructor_profile', User::class, $user->id, null, $request);

        $successMessage = $hasTeachingFieldRequestChanges
            ? 'Đã lưu yêu cầu thay đổi ngành giảng dạy. Vui lòng hoàn thiện hồ sơ và gửi Admin xét duyệt.'
            : 'Cập nhật thông tin hồ sơ thành công.';

        return back()->with('success', $successMessage);
    }

    public function uploadDocument(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $request->validate([
            'requirement_id' => ['nullable', 'integer'],
            'instructor_teaching_field_id' => ['nullable', 'integer'],
            'document_type' => ['nullable', 'string', 'in:certificate,degree,employment_contract,transcript,employment_confirmation,portfolio,other'],
            'title' => ['nullable', 'string', 'max:255'],
            'source_type' => ['nullable', 'in:file,url'],
            'document_url' => ['nullable', 'url', 'max:2048', 'regex:/^https?:\/\//i'],
            'files' => ['nullable', 'array', 'max:10'],
            'files.*' => ['file', 'mimes:pdf,jpg,jpeg,png,webp,doc,docx,mp4,mov,webm', 'max:51200'],
            'file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp,doc,docx,mp4,mov,webm', 'max:51200'],
        ], [
            'files.*.mimes' => 'Tài liệu phải có định dạng: PDF, JPG, PNG, WEBP, DOC, DOCX, MP4, MOV, WEBM.',
            'files.*.max' => 'Dung lượng mỗi tài liệu hoặc video tối đa là 50MB.',
            'file.mimes' => 'Tài liệu phải có định dạng: PDF, JPG, PNG, WEBP, DOC, DOCX, MP4, MOV, WEBM.',
            'file.max' => 'Dung lượng tài liệu hoặc video tối đa là 50MB.',
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

        $requirementId = $request->integer('requirement_id') ?: null;
        $teachingFieldId = $request->integer('instructor_teaching_field_id') ?: null;
        $requirement = null;
        $teachingField = null;
        $documentType = $request->input('document_type', 'certificate');
        $customTitle = $request->input('title');
        $uploadedCount = 0;
        $storedPaths = [];

        try {
            DB::transaction(function () use ($user, $sourceType, $files, $request, $requirementId, $teachingFieldId, &$requirement, &$teachingField, &$documentType, $customTitle, &$uploadedCount, &$storedPaths): void {
                $lockedUser = User::query()->lockForUpdate()->findOrFail($user->id);

                if ($teachingFieldId) {
                    $teachingField = InstructorTeachingField::query()->lockForUpdate()->findOrFail($teachingFieldId);
                    $teachingField->loadMissing(['profile', 'category.parent']);
                    abort_unless((int) $teachingField->profile?->user_id === (int) $lockedUser->id && $teachingField->acceptsDocumentUploads(), 403);

                    if (! $requirementId) {
                        throw ValidationException::withMessages([
                            'requirement_id' => 'Vui lòng chọn đúng yêu cầu tài liệu của ngành.',
                        ]);
                    }
                }

                $defaultTitle = null;
                if ($requirementId) {
                    $requirement = $teachingField
                        ? app(InstructorRequirementService::class)->validateRequirementForTeachingField($lockedUser, $teachingField, $requirementId)
                        : app(InstructorRequirementService::class)->validateRequirementForInstructor($lockedUser, $requirementId);
                    $documentType = $requirement->document_type;
                    $defaultTitle = $requirement->document_title;

                    if ($teachingField?->isApproved()
                        && $teachingField->certificates()
                            ->where('requirement_id', $requirement->id)
                            ->whereIn('status', ['draft', 'pending', 'approved'])
                            ->exists()) {
                        throw ValidationException::withMessages([
                            'requirement_id' => 'Yêu cầu này đã có tài liệu hợp lệ hoặc đang chờ xử lý. Vui lòng thay thế bản nháp hiện có thay vì tải thêm.',
                        ]);
                    }
                }

                if ($sourceType === 'url') {
                    InstructorCertificate::create([
                        'user_id' => $lockedUser->id,
                        'requirement_id' => $requirement?->id,
                        'instructor_teaching_field_id' => $teachingField?->id,
                        'source_type' => 'url',
                        'document_url' => $request->input('document_url'),
                        'title' => $customTitle ?: $defaultTitle ?: 'Tài liệu liên kết',
                        'document_type' => $documentType,
                        'status' => 'draft',
                        'uploaded_at' => now(),
                    ]);
                    $uploadedCount = 1;

                    return;
                }

                foreach ($files as $file) {
                    if (! $file || ! $file->isValid()) {
                        continue;
                    }

                    $originalName = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension() ?: 'pdf';
                    $storedPath = $file->storeAs(
                        "instructor-certificates/{$lockedUser->id}",
                        Str::uuid().'.'.$extension,
                        'local'
                    );
                    if (! $storedPath) {
                        throw new \RuntimeException('Không thể lưu tài liệu vào bộ nhớ cục bộ.');
                    }
                    $storedPaths[] = $storedPath;

                    InstructorCertificate::create([
                        'user_id' => $lockedUser->id,
                        'requirement_id' => $requirement?->id,
                        'source_type' => 'file',
                        'instructor_teaching_field_id' => $teachingField?->id,
                        'file_path' => $storedPath,
                        'original_name' => $originalName,
                        'mime_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                        'title' => $customTitle ?: pathinfo($originalName, PATHINFO_FILENAME),
                        'document_type' => $documentType,
                        'status' => 'draft',
                        'uploaded_at' => now(),
                    ]);

                    $uploadedCount++;
                }
            });
        } catch (\Throwable $e) {
            foreach ($storedPaths as $storedPath) {
                Storage::disk('local')->delete($storedPath);
            }
            throw $e;
        }

        ActivityLogService::log($user->id, 'upload_instructor_document', User::class, $user->id, [
            'document_type' => $documentType,
            'requirement_id' => $requirement?->id,
            'instructor_teaching_field_id' => $teachingField?->id,
            'uploaded_count' => $uploadedCount,
            'source_type' => $sourceType,
        ], $request);

        return back()->with('active_tab', 'documents')->with('success', "Đã tải lên {$uploadedCount} tài liệu minh chứng - Chưa gửi xét duyệt.");
    }

    public function replaceDocument(Request $request, InstructorCertificate $certificate): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        if ($certificate->user_id !== $user->id) {
            abort(403, 'Bạn không có quyền thay thế tài liệu này.');
        }
        if (! $certificate->isDraft() || $certificate->isUrlSource()) {
            return back()->with('active_tab', 'documents')->with('error', 'Chỉ có thể thay thế tài liệu chưa gửi xét duyệt.');
        }

        $request->validate([
            'file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp,doc,docx,mp4,mov,webm', 'max:51200'],
            'title' => ['nullable', 'string', 'max:255'],
        ], [
            'file.mimes' => 'Tài liệu phải có định dạng: PDF, JPG, PNG, WEBP, DOC, DOCX, MP4, MOV, WEBM.',
            'file.max' => 'Dung lượng tài liệu hoặc video tối đa là 50MB.',
        ]);
        if (! $request->hasFile('file') && ! $request->has('title')) {
            return back()->with('active_tab', 'documents')->with('error', 'Vui lòng chọn tệp mới hoặc cập nhật tiêu đề.');
        }

        $newPath = null;
        $oldPath = null;
        try {
            $updates = [];
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $extension = $file->getClientOriginalExtension() ?: 'pdf';
                $newPath = $file->storeAs("instructor-certificates/{$user->id}", Str::uuid().'.'.$extension, 'local');
                $updates = [
                    'file_path' => $newPath,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                ];
            }
            if ($request->has('title')) {
                $updates['title'] = $request->input('title') ?: ($updates['original_name'] ?? $certificate->original_name);
            }

            DB::transaction(function () use ($user, $certificate, $updates, &$oldPath) {
                User::query()->lockForUpdate()->findOrFail($user->id);
                $locked = InstructorCertificate::query()->lockForUpdate()->findOrFail($certificate->id);
                abort_unless((int) $locked->user_id === (int) $user->id, 403);
                if (! $locked->isDraft() || $locked->isUrlSource()) {
                    abort(409, 'Tài liệu không còn ở trạng thái chưa gửi.');
                }
                $oldPath = $locked->file_path;
                $locked->update($updates);
            });
        } catch (\Throwable $e) {
            if ($newPath) {
                Storage::disk('local')->delete($newPath);
            }
            throw $e;
        }

        if ($newPath && $oldPath && Storage::disk('local')->exists($oldPath)) {
            Storage::disk('local')->delete($oldPath);
        }

        return back()->with('active_tab', 'documents')->with('success', 'Đã cập nhật tài liệu chưa gửi.');
    }

    public function deleteDocument(Request $request, InstructorCertificate $certificate): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($certificate->user_id !== $user->id) {
            abort(403, 'Bạn không có quyền xóa tài liệu này.');
        }

        if (! $certificate->isDraft()) {
            return back()->with('active_tab', 'documents')->with('error', 'Chỉ có thể xóa tài liệu chưa gửi xét duyệt.');
        }

        $filePath = null;
        $fileName = null;
        DB::transaction(function () use ($user, $certificate, &$filePath, &$fileName): void {
            User::query()->lockForUpdate()->findOrFail($user->id);
            $locked = InstructorCertificate::query()->lockForUpdate()->findOrFail($certificate->id);
            abort_unless((int) $locked->user_id === (int) $user->id, 403);
            if (! $locked->isDraft()) {
                abort(409, 'Tài liệu không còn ở trạng thái chưa gửi.');
            }
            $filePath = $locked->isUrlSource() ? null : $locked->file_path;
            $fileName = $locked->original_name;
            $locked->delete();
        });

        if ($filePath && Storage::disk('local')->exists($filePath) && ! Storage::disk('local')->delete($filePath)) {
            Log::warning('Không thể xóa tệp tài liệu giảng viên sau khi xóa bản ghi.', ['path' => $filePath]);
        }

        ActivityLogService::log($user->id, 'delete_instructor_document', InstructorCertificate::class, $certificate->id, [
            'file_name' => $fileName,
        ], $request);

        return back()->with('active_tab', 'documents')->with('success', 'Đã xóa tài liệu thành công.');
    }

    public function updateDocumentUrl(Request $request, InstructorCertificate $certificate): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        abort_unless($certificate->user_id === $user->id && $certificate->isUrlSource(), 403);

        if (! $certificate->isDraft()) {
            return back()->with('active_tab', 'documents')->with('error', 'Chỉ có thể sửa liên kết chưa gửi xét duyệt.');
        }

        $validated = $request->validate([
            'document_url' => ['required', 'url', 'max:2048', 'regex:/^https?:\/\//i'],
        ], [
            'document_url.url' => 'URL tài liệu không hợp lệ.',
            'document_url.regex' => 'Chỉ chấp nhận URL bắt đầu bằng http:// hoặc https://.',
        ]);

        DB::transaction(function () use ($user, $certificate, $validated): void {
            User::query()->lockForUpdate()->findOrFail($user->id);
            $locked = InstructorCertificate::query()->lockForUpdate()->findOrFail($certificate->id);
            abort_unless((int) $locked->user_id === (int) $user->id && $locked->isUrlSource(), 403);
            if (! $locked->isDraft()) {
                abort(409, 'Tài liệu không còn ở trạng thái chưa gửi.');
            }
            $locked->update(['document_url' => $validated['document_url']]);
        });

        ActivityLogService::log($user->id, 'update_instructor_document_url', InstructorCertificate::class, $certificate->id, [], $request);

        return back()->with('active_tab', 'documents')->with('success', 'Đã cập nhật liên kết tài liệu chưa gửi.');
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

        if ($user->instructor_status === 'approved') {
            return back()->with('active_tab', 'documents')->with('error', 'Hãy gửi xét duyệt riêng cho ngành chưa được duyệt.');
        }

        $requirements = app(InstructorRequirementService::class);
        $result = DB::transaction(function () use ($user, $requirements): array {
            $lockedUser = User::query()->lockForUpdate()->findOrFail($user->id);
            if ($lockedUser->instructor_status === 'approved') {
                return ['submitted' => false, 'error' => 'Hãy gửi xét duyệt riêng cho ngành chưa được duyệt.'];
            }

            $lockedCertificates = InstructorCertificate::query()
                ->where('user_id', $lockedUser->id)
                ->lockForUpdate()
                ->get();
            $lockedUser->setRelation('instructorCertificates', $lockedCertificates);
            $eligibility = $requirements->getSubmitEligibility($lockedUser);
            if (! $eligibility['can_submit']) {
                return ['submitted' => false, 'error' => $this->submitEligibilityError($eligibility)];
            }
            if ($lockedUser->instructor_status === 'pending' && $lockedUser->submitted_for_review_at) {
                return ['submitted' => false, 'error' => 'Hồ sơ đã được gửi xét duyệt trước đó.'];
            }

            $certsCount = $lockedCertificates->count();
            $requirements->promoteDraftCertificatesForReview($lockedUser);
            $lockedUser->update([
                'submitted_for_review_at' => now(),
                'instructor_status' => 'pending',
                'needs_admin_review' => true,
                'rejected_reason' => null,
            ]);

            if ($lockedUser->instructorApplication) {
                $lockedUser->instructorApplication->update([
                    'status' => 'pending',
                    'admin_notes' => null,
                ]);
            }

            return ['submitted' => true, 'certificates_count' => $certsCount];
        });

        if (! $result['submitted']) {
            return back()->with('active_tab', 'documents')->with('error', $result['error']);
        }

        $certsCount = $result['certificates_count'];

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

    public function submitTeachingFieldForReview(Request $request, InstructorTeachingField $teachingField): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $teachingField->loadMissing(['profile', 'category']);
        abort_unless((int) $teachingField->profile?->user_id === (int) $user->id, 403);
        if (! $teachingField->isEditable()) {
            return back()->with('active_tab', 'documents')->with('error', 'Ngành này không ở trạng thái có thể gửi xét duyệt.');
        }

        $requirements = app(InstructorRequirementService::class);
        $result = DB::transaction(function () use ($user, $teachingField, $requirements): array {
            User::query()->lockForUpdate()->findOrFail($user->id);
            $locked = InstructorTeachingField::query()->lockForUpdate()->findOrFail($teachingField->id);
            $locked->loadMissing(['profile', 'category.parent']);
            abort_unless((int) $locked->profile?->user_id === (int) $user->id, 403);
            if (! $locked->isEditable()) {
                return ['submitted' => false, 'error' => 'Ngành không còn ở trạng thái có thể gửi.'];
            }

            $lockedCertificates = InstructorCertificate::query()
                ->where('user_id', $user->id)
                ->where('instructor_teaching_field_id', $locked->id)
                ->lockForUpdate()
                ->get();
            $locked->setRelation('certificates', $lockedCertificates);
            $eligibility = $requirements->getTeachingFieldSubmitEligibility($locked);
            if (! $eligibility['can_submit']) {
                return [
                    'submitted' => false,
                    'error' => 'Ngành này còn thiếu tài liệu bắt buộc: '.implode(', ', $eligibility['missing_titles']),
                ];
            }

            $requirements->promoteDraftCertificatesForTeachingField($locked);
            $locked->update([
                'approval_status' => InstructorTeachingField::STATUS_PENDING,
                'submitted_at' => now(),
                'reviewed_at' => null,
                'reviewed_by' => null,
                'rejection_reason' => null,
            ]);

            return ['submitted' => true];
        });

        if (! $result['submitted']) {
            return back()->with('active_tab', 'documents')->with('error', $result['error']);
        }

        try {
            app(NotificationService::class)->notifyAdmins(
                'Yêu cầu duyệt ngành giảng dạy',
                "Giảng viên {$user->name} vừa gửi ngành {$teachingField->category?->name} để xét duyệt.",
                'instructor_teaching_field_submitted',
                route('admin.instructors.teaching-fields.index')
            );
        } catch (\Throwable $e) {
            Log::warning('Không thể gửi thông báo duyệt ngành: '.$e->getMessage());
        }

        return back()->with('active_tab', 'documents')->with('success', 'Đã gửi xét duyệt ngành này. Quyền tạo khóa học chỉ được cấp sau khi Admin phê duyệt.');
    }

    public function submitTeachingFieldSupplement(Request $request, InstructorTeachingField $teachingField): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $requirements = app(InstructorRequirementService::class);
        $result = DB::transaction(function () use ($teachingField, $requirements, $user): array {
            $lockedUser = User::query()->lockForUpdate()->findOrFail($user->id);
            $locked = InstructorTeachingField::query()
                ->with(['profile', 'category.parent'])
                ->lockForUpdate()
                ->findOrFail($teachingField->id);
            abort_unless((int) $locked->profile?->user_id === (int) $lockedUser->id, 403);

            if (! $locked->isApproved()) {
                return ['submitted' => false, 'error' => 'Chỉ ngành đã được duyệt mới dùng luồng gửi bổ sung hồ sơ.'];
            }

            $allowedRequirementIds = $requirements->getRequirementsForTeachingField($locked)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();
            $drafts = InstructorCertificate::query()
                ->where('user_id', $lockedUser->id)
                ->where('instructor_teaching_field_id', $locked->id)
                ->where('status', 'draft')
                ->lockForUpdate()
                ->get();

            if ($drafts->isEmpty()) {
                throw ValidationException::withMessages([
                    'documents' => 'Ngành này chưa có tài liệu bổ sung ở trạng thái nháp để gửi.',
                ]);
            }

            if ($drafts->contains(fn (InstructorCertificate $certificate) => ! in_array((int) $certificate->requirement_id, $allowedRequirementIds, true))) {
                throw ValidationException::withMessages([
                    'documents' => 'Có tài liệu nháp không còn thuộc yêu cầu hiện hành của ngành. Vui lòng kiểm tra lại trước khi gửi.',
                ]);
            }

            return [
                'submitted' => true,
                'certificates_count' => $requirements->promoteDraftCertificatesForTeachingField($locked),
            ];
        });

        if (! $result['submitted']) {
            return back()->with('active_tab', 'documents')->with('error', $result['error']);
        }

        $submittedCount = $result['certificates_count'];

        ActivityLogService::log($user->id, 'submit_instructor_document_supplement', InstructorTeachingField::class, $teachingField->id, [
            'certificates_count' => $submittedCount,
        ], $request);

        try {
            app(NotificationService::class)->notifyAdmins(
                'Hồ sơ ngành đã duyệt cần xét duyệt bổ sung',
                "Giảng viên {$user->name} vừa gửi {$submittedCount} tài liệu bổ sung cho ngành {$teachingField->category?->name}.",
                'instructor_document_supplement_submitted',
                route('admin.instructors.applications.show', $user)
            );
        } catch (\Throwable $e) {
            Log::warning('Không thể gửi thông báo duyệt tài liệu bổ sung: '.$e->getMessage());
        }

        return back()->with('active_tab', 'documents')->with('success', 'Đã gửi bổ sung hồ sơ ngành này. Các tài liệu đang chờ Admin duyệt.');
    }

    /** @param array{missing_count: int, missing_titles: array<int, string>, reason: ?string} $eligibility */
    private function submitEligibilityError(array $eligibility): string
    {
        if ($eligibility['missing_count'] > 0) {
            $titles = implode(', ', $eligibility['missing_titles']);

            return "Hồ sơ chưa đủ điều kiện gửi xét duyệt. Còn thiếu {$eligibility['missing_count']} tài liệu bắt buộc: {$titles}.";
        }

        return $eligibility['reason'] ?? 'Hồ sơ chưa đủ điều kiện gửi xét duyệt.';
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
