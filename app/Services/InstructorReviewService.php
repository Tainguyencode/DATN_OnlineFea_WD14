<?php

namespace App\Services;

use App\Models\InstructorApplication;
use App\Models\InstructorCertificate;
use App\Models\InstructorProfile;
use App\Models\InstructorTeachingField;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InstructorReviewService
{
    public function __construct(
        private readonly InstructorRequirementService $requirements,
        private readonly NotificationService $notifications,
    ) {}

    /**
     * @return array{submitted: bool, duplicate?: bool, resubmission?: bool, certificates_count?: int, error?: string}
     */
    public function submitGlobal(User $instructor): array
    {
        $result = DB::transaction(function () use ($instructor): array {
            $user = User::query()->lockForUpdate()->findOrFail($instructor->id);

            if ($user->isGlobalReviewPending()) {
                return [
                    'submitted' => false,
                    'duplicate' => true,
                    'error' => 'Hồ sơ đã được gửi xét duyệt trước đó.',
                ];
            }

            $isResubmission = $user->canResubmitInstructorReview();
            if (! $user->canSubmitInitialInstructorReview() && ! $isResubmission) {
                return [
                    'submitted' => false,
                    'error' => $user->isApprovedInstructor()
                        ? 'Hãy gửi xét duyệt riêng cho ngành chưa được duyệt.'
                        : 'Hồ sơ không ở trạng thái cho phép gửi xét duyệt.',
                ];
            }

            $profile = InstructorProfile::query()
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();
            if (! $profile) {
                return ['submitted' => false, 'error' => 'Không tìm thấy hồ sơ giảng viên.'];
            }

            $fields = $profile->teachingFields()->orderBy('id')->lockForUpdate()->get();
            $certificates = InstructorCertificate::query()
                ->where('user_id', $user->id)
                ->orderBy('id')
                ->lockForUpdate()
                ->get();
            $application = InstructorApplication::query()
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            $profile->unsetRelation('teachingCategories');
            $profile->setRelation('teachingFields', $fields);
            $user->setRelation('instructorProfile', $profile);
            $user->setRelation('instructorCertificates', $certificates);

            $eligibility = $this->requirements->getSubmitEligibility($user);
            if (! $eligibility['can_submit']) {
                return [
                    'submitted' => false,
                    'error' => $this->eligibilityError($eligibility),
                ];
            }

            $promotedCount = $this->requirements->promoteDraftCertificatesForReview($user);
            $submittedAt = now();
            $user->update([
                'submitted_for_review_at' => $submittedAt,
                'instructor_status' => 'pending',
                'needs_admin_review' => true,
                'rejected_reason' => null,
            ]);

            $applicationValues = [
                'expertise' => $profile->specialty,
                'experience' => $profile->experience,
                'introduction' => $profile->bio,
                'cv_path' => $profile->cv,
                'bank_name' => $user->bank_name,
                'bank_account_number' => $user->bank_account_number,
                'bank_account_name' => $user->bank_account_name,
                'status' => 'pending',
                'admin_notes' => null,
                'reviewed_by' => null,
                'reviewed_at' => null,
            ];

            if ($application) {
                $application->update($applicationValues);
            } else {
                InstructorApplication::query()->create(array_merge(
                    ['user_id' => $user->id],
                    $applicationValues,
                ));
            }

            return [
                'submitted' => true,
                'resubmission' => $isResubmission,
                'certificates_count' => $certificates->count(),
                'promoted_count' => $promotedCount,
            ];
        }, 3);

        if ($result['submitted']) {
            $this->notifyGlobalSubmission($instructor->fresh(), (bool) ($result['resubmission'] ?? false), (int) $result['certificates_count']);
        }

        return $result;
    }

    /** @return array{submitted: bool, duplicate?: bool, resubmission?: bool, error?: string} */
    public function submitTeachingField(User $instructor, InstructorTeachingField $teachingField): array
    {
        $result = DB::transaction(function () use ($instructor, $teachingField): array {
            $user = User::query()->lockForUpdate()->findOrFail($instructor->id);
            if (! $user->isApprovedInstructor()) {
                return ['submitted' => false, 'error' => 'Chỉ giảng viên đã được phê duyệt mới có thể gửi xét duyệt ngành độc lập.'];
            }

            $profile = InstructorProfile::query()->where('user_id', $user->id)->lockForUpdate()->firstOrFail();
            $fields = $profile->teachingFields()->orderBy('id')->lockForUpdate()->get();
            $field = $fields->firstWhere('id', $teachingField->id);
            abort_unless($field, 403);

            if ($field->approval_status === InstructorTeachingField::STATUS_PENDING) {
                return ['submitted' => false, 'duplicate' => true, 'error' => 'Ngành này đã được gửi xét duyệt trước đó.'];
            }

            if (! $field->isEditable()) {
                return ['submitted' => false, 'error' => 'Ngành này không ở trạng thái có thể gửi xét duyệt.'];
            }

            $wasRejected = $field->approval_status === InstructorTeachingField::STATUS_REJECTED;
            $certificates = InstructorCertificate::query()
                ->where('user_id', $user->id)
                ->where('instructor_teaching_field_id', $field->id)
                ->orderBy('id')
                ->lockForUpdate()
                ->get();
            $field->loadMissing('category.parent');
            $field->setRelation('profile', $profile);
            $field->setRelation('certificates', $certificates);

            $eligibility = $this->requirements->getTeachingFieldSubmitEligibility($field);
            if (! $eligibility['can_submit']) {
                return [
                    'submitted' => false,
                    'error' => 'Ngành này còn thiếu tài liệu bắt buộc: '.implode(', ', $eligibility['missing_titles']),
                ];
            }

            $this->requirements->promoteDraftCertificatesForTeachingField($field);
            $field->update([
                'approval_status' => InstructorTeachingField::STATUS_PENDING,
                'submitted_at' => now(),
                'reviewed_at' => null,
                'reviewed_by' => null,
                'rejection_reason' => null,
            ]);

            return ['submitted' => true, 'resubmission' => $wasRejected];
        }, 3);

        if ($result['submitted']) {
            $this->notifyTeachingFieldSubmission($instructor->fresh(), $teachingField->fresh('category'), (bool) $result['resubmission']);
        }

        return $result;
    }

    /** @return array{submitted: bool, duplicate?: bool, certificates_count?: int, error?: string} */
    public function submitTeachingFieldSupplement(User $instructor, InstructorTeachingField $teachingField): array
    {
        $result = DB::transaction(function () use ($instructor, $teachingField): array {
            $user = User::query()->lockForUpdate()->findOrFail($instructor->id);
            if (! $user->isApprovedInstructor()) {
                return ['submitted' => false, 'error' => 'Chỉ giảng viên đã được phê duyệt mới có thể gửi hồ sơ bổ sung.'];
            }

            $profile = InstructorProfile::query()->where('user_id', $user->id)->lockForUpdate()->firstOrFail();
            $fields = $profile->teachingFields()->orderBy('id')->lockForUpdate()->get();
            $field = $fields->firstWhere('id', $teachingField->id);
            abort_unless($field, 403);

            if (! $field->isApproved()) {
                return ['submitted' => false, 'error' => 'Chỉ ngành đã được duyệt mới dùng luồng gửi bổ sung hồ sơ.'];
            }

            $certificates = InstructorCertificate::query()
                ->where('user_id', $user->id)
                ->where('instructor_teaching_field_id', $field->id)
                ->orderBy('id')
                ->lockForUpdate()
                ->get();
            $drafts = $certificates->where('status', 'draft');
            if ($drafts->isEmpty()) {
                if ($certificates->contains('status', 'pending')) {
                    return ['submitted' => false, 'duplicate' => true, 'error' => 'Các tài liệu bổ sung đã được gửi xét duyệt trước đó.'];
                }

                return ['submitted' => false, 'error' => 'Ngành này chưa có tài liệu bổ sung ở trạng thái nháp để gửi.'];
            }

            $field->loadMissing('category.parent');
            $field->setRelation('profile', $profile);
            $field->setRelation('certificates', $certificates);
            $allowedRequirementIds = $this->requirements->getRequirementsForTeachingField($field)
                ->pluck('id')->map(fn ($id) => (int) $id)->all();
            if ($drafts->contains(fn (InstructorCertificate $certificate) => ! in_array((int) $certificate->requirement_id, $allowedRequirementIds, true))) {
                return ['submitted' => false, 'error' => 'Có tài liệu nháp không còn thuộc yêu cầu hiện hành của ngành.'];
            }

            $count = $this->requirements->promoteDraftCertificatesForTeachingField($field);

            return ['submitted' => true, 'certificates_count' => $count];
        }, 3);

        if ($result['submitted']) {
            $this->notifySupplementSubmission($instructor->fresh(), $teachingField->fresh('category'), (int) $result['certificates_count']);
        }

        return $result;
    }

    /** @param array{missing_count: int, missing_titles: array<int, string>, reason: ?string} $eligibility */
    private function eligibilityError(array $eligibility): string
    {
        if ($eligibility['missing_count'] > 0) {
            return 'Hồ sơ chưa đủ điều kiện gửi xét duyệt. Còn thiếu: '.implode(', ', $eligibility['missing_titles']).'.';
        }

        return $eligibility['reason'] ?? 'Hồ sơ chưa đủ điều kiện gửi xét duyệt.';
    }

    private function notifyGlobalSubmission(User $user, bool $resubmission, int $certificateCount): void
    {
        try {
            $this->notifications->notifyAdmins(
                $resubmission ? 'Hồ sơ Giảng viên nộp lại sau khi chỉnh sửa' : 'Hồ sơ Giảng viên mới cần duyệt',
                $resubmission
                    ? "Giảng viên {$user->name} ({$user->email}) vừa gửi lại hồ sơ xét duyệt."
                    : "Giảng viên {$user->name} ({$user->email}) vừa gửi hồ sơ xét duyệt với {$certificateCount} tài liệu.",
                $resubmission ? 'instructor_application_resubmitted' : 'instructor_application_submitted',
                route('admin.instructors.applications.show', $user),
            );
        } catch (\Throwable $exception) {
            Log::warning('Không thể gửi thông báo hồ sơ giảng viên.', ['error' => $exception->getMessage()]);
        }
    }

    private function notifyTeachingFieldSubmission(User $user, InstructorTeachingField $field, bool $resubmission): void
    {
        try {
            $this->notifications->notifyAdmins(
                $resubmission ? 'Ngành giảng dạy được gửi xét duyệt lại' : 'Yêu cầu duyệt ngành giảng dạy',
                "Giảng viên {$user->name} vừa gửi ngành {$field->category?->name} để xét duyệt.",
                $resubmission ? 'instructor_teaching_field_resubmitted' : 'instructor_teaching_field_submitted',
                route('admin.instructors.teaching-fields.index'),
            );
        } catch (\Throwable $exception) {
            Log::warning('Không thể gửi thông báo duyệt ngành.', ['error' => $exception->getMessage()]);
        }
    }

    private function notifySupplementSubmission(User $user, InstructorTeachingField $field, int $count): void
    {
        try {
            $this->notifications->notifyAdmins(
                'Hồ sơ ngành đã duyệt cần xét duyệt bổ sung',
                "Giảng viên {$user->name} vừa gửi {$count} tài liệu bổ sung cho ngành {$field->category?->name}.",
                'instructor_document_supplement_submitted',
                route('admin.instructors.supplements.index'),
            );
        } catch (\Throwable $exception) {
            Log::warning('Không thể gửi thông báo duyệt tài liệu bổ sung.', ['error' => $exception->getMessage()]);
        }
    }
}
