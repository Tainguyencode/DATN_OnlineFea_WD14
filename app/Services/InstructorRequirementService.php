<?php

namespace App\Services;

use App\Models\Category;
use App\Models\InstructorCertificate;
use App\Models\InstructorDocumentRequirement;
use App\Models\InstructorProfile;
use App\Models\InstructorTeachingField;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class InstructorRequirementService
{
    /** @var array<int, Collection<int, InstructorDocumentRequirement>> */
    private array $activeRequirementsByCategory = [];

    /**
     * Lấy toàn bộ danh sách yêu cầu tài liệu theo các ngành của giảng viên và trạng thái đáp ứng.
     */
    public function getRequirementsForInstructor(User $instructor): array
    {
        $categories = $instructor->getTeachingCategories();
        $primaryCategory = $instructor->getTeachingCategory();

        if ($categories->isEmpty()) {
            return [
                'category' => null,
                'categories' => new Collection,
                'requirements' => [],
                'categories_requirements' => [],
                'summary' => [
                    'has_category' => false,
                    'total_requirements' => 0,
                    'required_count' => 0,
                    'optional_count' => 0,
                    'required_submitted_count' => 0,
                    'required_approved_count' => 0,
                    'required_missing_count' => 0,
                    'required_rejected_count' => 0,
                    'has_all_required_submitted' => false,
                    'can_approve' => false,
                    'missing_titles' => [],
                ],
                'unassigned_certificates' => $instructor->instructorCertificates()->whereNull('requirement_id')->get(),
            ];
        }

        $certificates = $instructor->relationLoaded('instructorCertificates')
            ? $instructor->instructorCertificates
            : $instructor->instructorCertificates()->get();
        $teachingFieldCategoryIds = $instructor->instructorProfile
            ? $instructor->instructorProfile->teachingFields()
                ->pluck('category_id', 'id')
                ->map(fn ($categoryId) => (int) $categoryId)
            : collect();

        $allFlatRequirements = [];
        $categoriesRequirements = [];
        $totalRequiredCount = 0;
        $totalOptionalCount = 0;
        $totalSubmittedCount = 0;
        $totalApprovedCount = 0;
        $totalMissingCount = 0;
        $totalRejectedCount = 0;
        $allMissingTitles = [];
        $seenRequirementIds = [];

        foreach ($categories as $cat) {
            // Lấy requirements của category (hoặc thừa kế từ parent nếu category con chưa cấu hình riêng)
            $catRequirements = $this->activeRequirementsForCategory($cat->id);

            if ($catRequirements->isEmpty() && $cat->parent_id) {
                $catRequirements = $this->activeRequirementsForCategory($cat->parent_id);
            }

            $catProcessed = [];
            $catRequiredCount = 0;
            $catOptionalCount = 0;
            $catSubmittedCount = 0;
            $catApprovedCount = 0;
            $catMissingCount = 0;
            $catRejectedCount = 0;
            $catMissingTitles = [];

            foreach ($catRequirements as $req) {
                // Tìm tài liệu nộp cho requirement này
                $reqCerts = $certificates->filter(function ($cert) use ($req, $cat, $teachingFieldCategoryIds) {
                    $matchesRequirement = $cert->requirement_id === $req->id
                        // Tương thích ngược với tài liệu cũ chưa gán requirement_id.
                        || ($cert->requirement_id === null && $cert->status !== 'draft' && $cert->document_type === $req->document_type);
                    if (! $matchesRequirement) {
                        return false;
                    }

                    // Tài liệu legacy không gắn ngành vẫn giữ hành vi cũ. Tài liệu
                    // mới đã gắn ngành chỉ được đáp ứng đúng ngành đó, kể cả khi
                    // nhiều ngành con cùng kế thừa một requirement của ngành cha.
                    if (! $cert->instructor_teaching_field_id) {
                        return true;
                    }

                    return $teachingFieldCategoryIds->get((int) $cert->instructor_teaching_field_id) === (int) $cat->id;
                });

                $approvedDocs = $reqCerts->where('status', 'approved');
                $pendingDocs = $reqCerts->where('status', 'pending');
                $draftDocs = $reqCerts->where('status', 'draft');
                $rejectedDocs = $reqCerts->where('status', 'rejected');

                $status = 'missing'; // 'missing', 'pending', 'approved', 'rejected'
                if ($approvedDocs->isNotEmpty()) {
                    $status = 'approved';
                } elseif ($pendingDocs->isNotEmpty()) {
                    $status = 'pending';
                } elseif ($draftDocs->isNotEmpty()) {
                    $status = 'draft';
                } elseif ($rejectedDocs->isNotEmpty()) {
                    $status = 'rejected';
                }

                $isFulfilled = in_array($status, ['approved', 'pending'], true);

                if ($req->is_required) {
                    $catRequiredCount++;
                    if ($status === 'approved') {
                        $catApprovedCount++;
                        $catSubmittedCount++;
                    } elseif ($status === 'pending') {
                        $catSubmittedCount++;
                    } elseif ($status === 'rejected') {
                        $catRejectedCount++;
                        $catMissingTitles[] = "[{$cat->name}] {$req->document_title} (Bị từ chối)";
                    } else {
                        $catMissingCount++;
                        $catMissingTitles[] = "[{$cat->name}] {$req->document_title}";
                    }
                } else {
                    $catOptionalCount++;
                }

                $itemData = [
                    'requirement' => $req,
                    'category' => $cat,
                    'status' => $status,
                    'is_fulfilled' => $isFulfilled,
                    'documents' => $reqCerts->values(),
                    'approved_count' => $approvedDocs->count(),
                    'pending_count' => $pendingDocs->count(),
                    'draft_count' => $draftDocs->count(),
                    'rejected_count' => $rejectedDocs->count(),
                    'total_documents_count' => $reqCerts->count(),
                ];

                $catProcessed[] = $itemData;

                if (! in_array($req->id, $seenRequirementIds, true)) {
                    $seenRequirementIds[] = $req->id;
                    $allFlatRequirements[] = $itemData;
                }
            }

            $catHasAllRequiredSubmitted = ($catRequiredCount > 0)
                ? ($catMissingCount === 0 && $catRejectedCount === 0)
                : true;

            $categoriesRequirements[] = [
                'category' => $cat,
                'requirements' => $catProcessed,
                'summary' => [
                    'total_requirements' => count($catProcessed),
                    'required_count' => $catRequiredCount,
                    'optional_count' => $catOptionalCount,
                    'required_submitted_count' => $catSubmittedCount,
                    'required_approved_count' => $catApprovedCount,
                    'required_missing_count' => $catMissingCount,
                    'required_rejected_count' => $catRejectedCount,
                    'has_all_required_submitted' => $catHasAllRequiredSubmitted,
                    'missing_titles' => $catMissingTitles,
                ],
            ];

            $totalRequiredCount += $catRequiredCount;
            $totalOptionalCount += $catOptionalCount;
            $totalSubmittedCount += $catSubmittedCount;
            $totalApprovedCount += $catApprovedCount;
            $totalMissingCount += $catMissingCount;
            $totalRejectedCount += $catRejectedCount;
            $allMissingTitles = array_merge($allMissingTitles, $catMissingTitles);
        }

        $hasAllRequiredSubmitted = ($totalRequiredCount > 0)
            ? ($totalMissingCount === 0 && $totalRejectedCount === 0)
            : true;

        $canApprove = $hasAllRequiredSubmitted && ($totalRequiredCount === 0 || $totalSubmittedCount >= $totalRequiredCount);

        // Lấy các tài liệu không thuộc requirement nào
        $assignedDocIds = collect($allFlatRequirements)->flatMap(fn ($r) => $r['documents']->pluck('id'))->all();
        $unassignedCertificates = $certificates->reject(fn ($c) => in_array($c->id, $assignedDocIds, true))->values();

        return [
            'category' => $primaryCategory,
            'categories' => $categories,
            'requirements' => $allFlatRequirements,
            'categories_requirements' => $categoriesRequirements,
            'summary' => [
                'has_category' => true,
                'total_requirements' => count($allFlatRequirements),
                'required_count' => $totalRequiredCount,
                'optional_count' => $totalOptionalCount,
                'required_submitted_count' => $totalSubmittedCount,
                'required_approved_count' => $totalApprovedCount,
                'required_missing_count' => $totalMissingCount,
                'required_rejected_count' => $totalRejectedCount,
                'has_all_required_submitted' => $hasAllRequiredSubmitted,
                'can_approve' => $canApprove,
                'missing_titles' => $allMissingTitles,
            ],
            'unassigned_certificates' => $unassignedCertificates,
        ];
    }

    /**
     * Cache requirement theo ngành trong cùng request để trang danh sách không lặp query
     * khi nhiều giảng viên đăng ký cùng một ngành.
     *
     * @return Collection<int, InstructorDocumentRequirement>
     */
    private function activeRequirementsForCategory(int $categoryId): Collection
    {
        return $this->activeRequirementsByCategory[$categoryId] ??= InstructorDocumentRequirement::query()
            ->where('category_id', $categoryId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Kiểm tra tính hợp lệ của requirement khi giảng viên upload tài liệu.
     * Ngăn chặn gian lận upload tài liệu của ngành khác.
     */
    public function validateRequirementForInstructor(User $instructor, int $requirementId): InstructorDocumentRequirement
    {
        $requirement = InstructorDocumentRequirement::query()
            ->where('id', $requirementId)
            ->where('is_active', true)
            ->first();

        if (! $requirement) {
            throw ValidationException::withMessages([
                'requirement_id' => 'Yêu cầu tài liệu không tồn tại hoặc đã bị vô hiệu hóa.',
            ]);
        }

        $categories = $instructor->getTeachingCategories();
        if ($categories->isEmpty()) {
            throw ValidationException::withMessages([
                'requirement_id' => 'Bạn chưa chọn ngành / lĩnh vực giảng dạy trong hồ sơ.',
            ]);
        }

        $allowedCategoryIds = [];
        foreach ($categories as $cat) {
            $allowedCategoryIds[] = $cat->id;
            if ($cat->parent_id) {
                $allowedCategoryIds[] = $cat->parent_id;
            }
        }
        $allowedCategoryIds = array_unique($allowedCategoryIds);

        if (! in_array($requirement->category_id, $allowedCategoryIds, true)) {
            throw ValidationException::withMessages([
                'requirement_id' => 'Tài liệu này không thuộc nhóm hồ sơ yêu cầu cho bất kỳ ngành nào bạn đã đăng ký.',
            ]);
        }

        return $requirement;
    }

    public function validateRequirementForTeachingField(User $instructor, InstructorTeachingField $field, int $requirementId): InstructorDocumentRequirement
    {
        if ((int) $field->profile?->user_id !== (int) $instructor->id || ! $field->acceptsDocumentUploads()) {
            throw ValidationException::withMessages([
                'instructor_teaching_field_id' => 'Ngành này không ở trạng thái cho phép bổ sung tài liệu.',
            ]);
        }

        $requirement = InstructorDocumentRequirement::query()
            ->whereKey($requirementId)
            ->where('is_active', true)
            ->first();
        if (! $requirement) {
            throw ValidationException::withMessages(['requirement_id' => 'Yêu cầu tài liệu không tồn tại hoặc đã bị vô hiệu hóa.']);
        }

        $allowedCategoryIds = [(int) $field->category_id];
        if ($field->category?->parent_id) {
            $allowedCategoryIds[] = (int) $field->category->parent_id;
        }
        if (! in_array((int) $requirement->category_id, $allowedCategoryIds, true)) {
            throw ValidationException::withMessages(['requirement_id' => 'Tài liệu không thuộc requirement của ngành đang yêu cầu duyệt.']);
        }

        return $requirement;
    }

    /** @return Collection<int, InstructorDocumentRequirement> */
    public function getRequirementsForTeachingField(InstructorTeachingField $field): Collection
    {
        $requirements = $this->activeRequirementsForCategory((int) $field->category_id);

        if ($requirements->isEmpty() && $field->category?->parent_id) {
            $requirements = $this->activeRequirementsForCategory((int) $field->category->parent_id);
        }

        return $requirements;
    }

    /** @return array{requirements: array<int, array<string, mixed>>, summary: array<string, mixed>} */
    public function getTeachingFieldRequirementData(InstructorTeachingField $field): array
    {
        $field->loadMissing(['category.parent', 'certificates.requirement']);
        $requirements = $this->getRequirementsForTeachingField($field);
        $certificates = $field->certificates;
        $items = [];
        $requiredCount = 0;
        $missingTitles = [];

        foreach ($requirements as $requirement) {
            $documents = $certificates->where('requirement_id', $requirement->id)->values();
            $status = $documents->contains(fn (InstructorCertificate $certificate) => in_array($certificate->status, ['draft', 'pending', 'approved'], true))
                ? ($documents->contains('status', 'approved') ? 'approved' : ($documents->contains('status', 'pending') ? 'pending' : 'draft'))
                : ($documents->contains('status', 'rejected') ? 'rejected' : 'missing');
            if ($requirement->is_required) {
                $requiredCount++;
                if (! in_array($status, ['draft', 'pending', 'approved'], true)) {
                    $missingTitles[] = $requirement->document_title;
                }
            }
            $items[] = compact('requirement', 'documents', 'status');
        }

        return [
            'requirements' => $items,
            'summary' => [
                'required_count' => $requiredCount,
                'missing_count' => count($missingTitles),
                'missing_titles' => $missingTitles,
                'can_submit' => $missingTitles === [],
            ],
        ];
    }

    /** @return array{required_count: int, submitted_count: int, missing_count: int, missing_titles: array<int, string>, can_submit: bool, reason: ?string} */
    public function getTeachingFieldSubmitEligibility(InstructorTeachingField $field): array
    {
        $data = $this->getTeachingFieldRequirementData($field);
        $summary = $data['summary'];

        return [
            'required_count' => $summary['required_count'],
            'submitted_count' => $summary['required_count'] - $summary['missing_count'],
            'missing_count' => $summary['missing_count'],
            'missing_titles' => $summary['missing_titles'],
            'can_submit' => $summary['can_submit'],
            'reason' => $summary['can_submit'] ? null : 'Ngành này còn thiếu tài liệu bắt buộc.',
        ];
    }

    /** @return array{required_count: int, submitted_count: int, missing_count: int, missing_titles: array<int, string>, can_submit: bool, reason: ?string} */
    public function getTeachingFieldAdminApprovalEligibility(InstructorTeachingField $field): array
    {
        $field->loadMissing(['category.parent', 'certificates']);
        $required = $this->getRequirementsForTeachingField($field)->where('is_required', true);
        $missingTitles = [];

        foreach ($required as $requirement) {
            $hasReviewableDocument = $field->certificates->contains(
                fn (InstructorCertificate $certificate) => (int) $certificate->requirement_id === (int) $requirement->id
                    && in_array($certificate->status, ['pending', 'approved'], true)
            );
            if (! $hasReviewableDocument) {
                $missingTitles[] = $requirement->document_title;
            }
        }

        $missingCount = count($missingTitles);

        return [
            'required_count' => $required->count(),
            'submitted_count' => $required->count() - $missingCount,
            'missing_count' => $missingCount,
            'missing_titles' => $missingTitles,
            'can_submit' => $missingCount === 0,
            'reason' => $missingCount === 0 ? null : 'Ngành còn thiếu tài liệu đã gửi để Admin xét duyệt.',
        ];
    }

    public function promoteDraftCertificatesForTeachingField(InstructorTeachingField $field): int
    {
        $requirementIds = $this->getRequirementsForTeachingField($field)->pluck('id')->all();
        if ($requirementIds === []) {
            return 0;
        }

        return InstructorCertificate::query()
            ->where('user_id', $field->profile->user_id)
            ->where('instructor_teaching_field_id', $field->id)
            ->where('status', 'draft')
            ->whereIn('requirement_id', $requirementIds)
            ->update(['status' => 'pending']);
    }

    /**
     * Kiểm tra xem hồ sơ giảng viên có đủ điều kiện để Admin bấm Phê duyệt hay không.
     */
    public function checkCanApproveInstructor(User $instructor): array
    {
        $eligibility = $this->getAdminApprovalEligibility($instructor);

        return [
            'can_approve' => $eligibility['can_submit'],
            'reason' => $eligibility['reason'],
            'missing_titles' => $eligibility['missing_titles'],
        ];
    }

    /**
     * Canonical eligibility for an instructor to submit their profile for review.
     * Pending and approved evidence fulfil a required document; rejected-only
     * evidence does not. Optional requirements never block submission.
     *
     * @return array{
     *     required_count: int,
     *     submitted_count: int,
     *     missing_count: int,
     *     missing_titles: array<int, string>,
     *     can_submit: bool,
     *     reason: ?string
     * }
     */
    public function getSubmitEligibility(User $instructor): array
    {
        return $this->getEligibilityForStatuses($instructor, ['draft', 'pending', 'approved']);
    }

    /** Draft evidence must be submitted before it can satisfy an admin approval. */
    public function getAdminApprovalEligibility(User $instructor): array
    {
        return $this->getEligibilityForStatuses($instructor, ['pending', 'approved']);
    }

    /** @return array<int, int> */
    public function getCurrentRequirementIds(User $instructor): array
    {
        return collect($this->getRequirementsForInstructor($instructor)['requirements'])
            ->map(fn (array $item) => (int) $item['requirement']->id)
            ->unique()
            ->values()
            ->all();
    }

    public function promoteDraftCertificatesForReview(User $instructor): int
    {
        $requirementIds = $this->getCurrentRequirementIds($instructor);

        if ($requirementIds === []) {
            return 0;
        }

        return InstructorCertificate::query()
            ->where('user_id', $instructor->id)
            ->where('status', 'draft')
            ->whereNotNull('requirement_id')
            ->whereIn('requirement_id', $requirementIds)
            ->update(['status' => 'pending']);
    }

    public function categoryHasPendingReview(int $categoryId): bool
    {
        $categoryIds = Category::query()
            ->whereKey($categoryId)
            ->orWhere('parent_id', $categoryId)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if ($categoryIds === []) {
            return false;
        }

        if (InstructorTeachingField::query()
            ->whereIn('category_id', $categoryIds)
            ->where('approval_status', InstructorTeachingField::STATUS_PENDING)
            ->exists()) {
            return true;
        }

        if (User::query()->pendingInstructorReview()
            ->whereHas('instructorProfile.teachingFields', fn ($query) => $query->whereIn('category_id', $categoryIds))
            ->exists()) {
            return true;
        }

        return InstructorCertificate::query()
            ->where('status', 'pending')
            ->whereHas('teachingField', fn ($query) => $query->whereIn('category_id', $categoryIds))
            ->exists();
    }

    /** @param array<int, string> $fulfillingStatuses */
    private function getEligibilityForStatuses(User $instructor, array $fulfillingStatuses): array
    {
        $profile = $instructor->relationLoaded('instructorProfile')
            ? $instructor->instructorProfile
            : $instructor->instructorProfile()->first();
        $missingProfileItems = [];

        if (! $instructor->hasVerifiedEmail()) {
            $missingProfileItems[] = 'Email chưa được xác minh';
        }

        if (! $profile?->cv || ! Storage::disk('public')->exists($profile->cv)) {
            $missingProfileItems[] = 'CV';
        }

        if ($missingProfileItems !== []) {
            return [
                'required_count' => count($missingProfileItems),
                'submitted_count' => 0,
                'missing_count' => count($missingProfileItems),
                'missing_titles' => $missingProfileItems,
                'can_submit' => false,
                'reason' => 'Vui lòng xác minh email và tải lên CV hợp lệ trước khi gửi xét duyệt.',
            ];
        }

        $requirements = $this->getRequirementsForInstructor($instructor);
        $summary = $requirements['summary'];

        if (! $summary['has_category']) {
            return [
                'required_count' => 0,
                'submitted_count' => 0,
                'missing_count' => 0,
                'missing_titles' => ['Ngành / Lĩnh vực giảng dạy'],
                'can_submit' => false,
                'reason' => 'Vui lòng chọn ít nhất một Ngành / Lĩnh vực giảng dạy trước khi gửi xét duyệt.',
            ];
        }

        $submittedCount = 0;
        $missingTitles = [];
        // The flat list de-duplicates requirement ids. Eligibility must not:
        // sibling fields may inherit the same requirement, and each field needs
        // its own field-scoped evidence.
        foreach ($requirements['categories_requirements'] as $categoryGroup) {
            foreach ($categoryGroup['requirements'] as $item) {
                if (! $item['requirement']->is_required) {
                    continue;
                }

                $hasEligibleDocument = $item['documents']->contains(
                    fn (InstructorCertificate $certificate) => in_array($certificate->status, $fulfillingStatuses, true)
                );
                if ($hasEligibleDocument) {
                    $submittedCount++;

                    continue;
                }

                $missingTitles[] = "[{$item['category']->name}] {$item['requirement']->document_title}";
            }
        }

        $missingCount = count($missingTitles);
        if ($missingCount > 0) {
            return [
                'required_count' => $summary['required_count'],
                'submitted_count' => $submittedCount,
                'missing_count' => $missingCount,
                'missing_titles' => $missingTitles,
                'can_submit' => false,
                'reason' => 'Giảng viên còn thiếu tài liệu bắt buộc của các ngành đăng ký.',
            ];
        }

        return [
            'required_count' => $summary['required_count'],
            'submitted_count' => $submittedCount,
            'missing_count' => 0,
            'missing_titles' => [],
            'can_submit' => true,
            'reason' => null,
        ];
    }

    /**
     * Xử lý đồng bộ danh sách ngành giảng dạy (Multi-select).
     */
    public function handleCategoriesSync(User $instructor, array $newCategoryIds): void
    {
        $uniqueIds = array_values(array_unique(array_filter(array_map('intval', $newCategoryIds))));
        if (empty($uniqueIds)) {
            return;
        }

        $profile = InstructorProfile::where('user_id', $instructor->id)->first() ?? new InstructorProfile(['user_id' => $instructor->id]);
        if (! $profile->exists) {
            $profile->save();
        }

        $profile->syncTeachingCategories($uniqueIds);

        // Lấy tất cả requirement id hợp lệ của các ngành hiện tại
        $categories = Category::whereIn('id', $uniqueIds)->get();
        $allAllowedCatIds = [];
        foreach ($categories as $c) {
            $allAllowedCatIds[] = $c->id;
            if ($c->parent_id) {
                $allAllowedCatIds[] = $c->parent_id;
            }
        }
        $allAllowedCatIds = array_unique($allAllowedCatIds);

        $validReqIds = InstructorDocumentRequirement::whereIn('category_id', $allAllowedCatIds)
            ->pluck('id')
            ->all();

        // Gỡ requirement_id của các chứng chỉ không còn thuộc bất kỳ ngành nào đang chọn
        if (empty($validReqIds)) {
            InstructorCertificate::where('user_id', $instructor->id)
                ->where('status', 'draft')
                ->whereNotNull('requirement_id')
                ->update(['requirement_id' => null]);
        } else {
            InstructorCertificate::where('user_id', $instructor->id)
                ->where('status', 'draft')
                ->whereNotNull('requirement_id')
                ->whereNotIn('requirement_id', $validReqIds)
                ->update(['requirement_id' => null]);
        }
    }

    /**
     * Backward-compatibility wrapper cho single category change.
     */
    public function handleCategoryChange(User $instructor, int $newCategoryId): void
    {
        $this->handleCategoriesSync($instructor, [$newCategoryId]);
    }
}
