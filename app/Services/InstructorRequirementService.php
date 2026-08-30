<?php

namespace App\Services;

use App\Models\Category;
use App\Models\InstructorCertificate;
use App\Models\InstructorDocumentRequirement;
use App\Models\InstructorProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
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
                $reqCerts = $certificates->filter(function ($cert) use ($req) {
                    if ($cert->requirement_id === $req->id) {
                        return true;
                    }

                    // Tương thích ngược với tài liệu cũ chưa gán requirement_id
                    if ($cert->requirement_id === null && $cert->document_type === $req->document_type) {
                        return true;
                    }

                    return false;
                });

                $approvedDocs = $reqCerts->where('status', 'approved');
                $pendingDocs = $reqCerts->where('status', 'pending');
                $rejectedDocs = $reqCerts->where('status', 'rejected');

                $status = 'missing'; // 'missing', 'pending', 'approved', 'rejected'
                if ($approvedDocs->isNotEmpty()) {
                    $status = 'approved';
                } elseif ($pendingDocs->isNotEmpty()) {
                    $status = 'pending';
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

    /**
     * Kiểm tra xem hồ sơ giảng viên có đủ điều kiện để Admin bấm Phê duyệt hay không.
     */
    public function checkCanApproveInstructor(User $instructor): array
    {
        $data = $this->getRequirementsForInstructor($instructor);
        $summary = $data['summary'];

        if (! $summary['has_category']) {
            return [
                'can_approve' => false,
                'reason' => 'Giảng viên chưa đăng ký Ngành / Lĩnh vực giảng dạy.',
                'missing_titles' => ['Ngành / Lĩnh vực giảng dạy'],
            ];
        }

        // Nếu có tài liệu bắt buộc nhưng giảng viên còn thiếu
        if ($summary['required_count'] > 0 && ! $summary['has_all_required_submitted']) {
            return [
                'can_approve' => false,
                'reason' => 'Giảng viên còn thiếu tài liệu bắt buộc của các ngành đăng ký.',
                'missing_titles' => $summary['missing_titles'],
            ];
        }

        return [
            'can_approve' => true,
            'reason' => null,
            'missing_titles' => [],
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
                ->whereNotNull('requirement_id')
                ->update(['requirement_id' => null]);
        } else {
            InstructorCertificate::where('user_id', $instructor->id)
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
