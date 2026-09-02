<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

class InstructorProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'phone',
        'organization',
        'position',
        'teaching_field',
        'specialty',
        'experience',
        'bio',
        'linkedin_url',
        'github_url',
        'website_url',
        'cv',
        'agree_information',
        'agree_terms',
    ];

    protected $casts = [
        'agree_information' => 'boolean',
        'agree_terms' => 'boolean',
    ];

    public function getHeadlineAttribute(): ?string
    {
        return $this->position ?: ($this->specialty ?: $this->teaching_field);
    }

    public function getSpecialtiesAttribute(): ?string
    {
        return $this->specialty;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function teachingCategories(): BelongsToMany
    {
        return $this->belongsToMany(
            Category::class,
            'instructor_profile_teaching_fields',
            'instructor_profile_id',
            'category_id'
        )
            ->withPivot([
                'id', 'organization', 'position', 'specialty', 'experience', 'is_primary',
                'approval_status', 'submitted_at', 'reviewed_at', 'reviewed_by',
                'rejection_reason', 'replace_of_teaching_field_id',
            ])
            ->withTimestamps();
    }

    /**
     * Alias cho teachingCategories
     */
    public function teachingFields(): HasMany
    {
        return $this->hasMany(InstructorTeachingField::class, 'instructor_profile_id');
    }

    public function approvedTeachingCategories(): BelongsToMany
    {
        return $this->teachingCategories()->wherePivot('approval_status', InstructorTeachingField::STATUS_APPROVED);
    }

    public function syncTeachingFields(array $fieldsData): void
    {
        $syncData = [];
        $isFirst = true;
        $primaryId = null;
        $primaryField = null;
        $bootstrapApprovalStatus = $this->user?->instructor_status === 'approved'
            ? InstructorTeachingField::STATUS_APPROVED
            : InstructorTeachingField::STATUS_DRAFT;

        foreach ($fieldsData as $field) {
            $catId = isset($field['category_id']) ? (int) $field['category_id'] : null;
            if (! $catId) {
                continue;
            }

            $isPrimary = $isFirst || ! empty($field['is_primary']);
            if ($isPrimary && $primaryId === null) {
                $primaryId = $catId;
                $primaryField = $field;
            }

            $syncData[$catId] = [
                'organization' => $field['organization'] ?? null,
                'position' => $field['position'] ?? null,
                'specialty' => $field['specialty'] ?? null,
                'experience' => $field['experience'] ?? null,
                'is_primary' => ($primaryId === $catId),
                // This legacy/bootstrap sync is used by migrations, seeders and
                // registration. Interactive profile edits use
                // saveTeachingFieldRequests(), which always creates reviewable drafts.
                'approval_status' => $bootstrapApprovalStatus,
            ];

            $isFirst = false;
        }

        if (empty($syncData)) {
            return;
        }

        $this->teachingCategories()->sync($syncData);

        // Giữ đồng bộ ngược lại cho trường đơn legacy
        if ($primaryId) {
            $firstCat = Category::find($primaryId);
            $this->update([
                'category_id' => $primaryId,
                'teaching_field' => $firstCat?->name ?? $this->teaching_field,
                'organization' => $primaryField['organization'] ?? $this->organization,
                'position' => $primaryField['position'] ?? $this->position,
                'specialty' => $primaryField['specialty'] ?? $this->specialty,
                'experience' => $primaryField['experience'] ?? $this->experience,
            ]);
        }
    }

    public function syncTeachingCategories(array $categoryIds, ?int $primaryId = null): void
    {
        $uniqueIds = array_values(array_unique(array_filter(array_map('intval', $categoryIds))));
        if (empty($uniqueIds)) {
            return;
        }

        if (! $primaryId || ! in_array($primaryId, $uniqueIds, true)) {
            $primaryId = $uniqueIds[0];
        }

        $fieldsData = [];
        foreach ($uniqueIds as $catId) {
            $fieldsData[] = [
                'category_id' => $catId,
                'is_primary' => ($catId === $primaryId),
            ];
        }

        $this->syncTeachingFields($fieldsData);
    }

    /**
     * Saves draft/rejected teaching-field requests without detaching an existing
     * approved field. An approved field whose category is changed creates a
     * separate replacement request instead of changing the effective field.
     *
     * @param  array<int, array<string, mixed>>  $fieldsData
     */
    public function saveTeachingFieldRequests(array $fieldsData): void
    {
        $submittedFieldIds = collect($fieldsData)->pluck('teaching_field_id')->filter()->map(fn ($id) => (int) $id)->all();
        $submittedCategoryIds = collect($fieldsData)->pluck('category_id')->filter()->map(fn ($id) => (int) $id)->all();
        $keptFieldIds = [];
        $omittedApprovedIds = $this->teachingFields()
            ->approved()
            ->whereNotIn('id', $submittedFieldIds ?: [0])
            ->whereNotIn('category_id', $submittedCategoryIds ?: [0])
            ->pluck('id')
            ->all();

        foreach ($fieldsData as $fieldData) {
            $categoryId = (int) ($fieldData['category_id'] ?? 0);
            if (! $categoryId) {
                continue;
            }

            $fieldId = (int) ($fieldData['teaching_field_id'] ?? 0);
            $existing = $fieldId
                ? $this->teachingFields()->whereKey($fieldId)->first()
                : null;
            if (! $existing) {
                // Backward-compatible category_ids payloads do not carry row ids.
                // Match an already-approved row by category so saving metadata
                // cannot create a draft that incorrectly replaces the same field.
                $existing = $this->teachingFields()
                    ->approved()
                    ->where('category_id', $categoryId)
                    ->first();
            }
            $attributes = [
                'organization' => $fieldData['organization'] ?? null,
                'position' => $fieldData['position'] ?? null,
                'specialty' => $fieldData['specialty'] ?? null,
                'experience' => $fieldData['experience'] ?? null,
            ];

            if ($existing?->approval_status === InstructorTeachingField::STATUS_PENDING) {
                $changed = (int) $existing->category_id !== $categoryId
                    || collect($attributes)->contains(fn ($value, $key) => (string) ($existing->{$key} ?? '') !== (string) ($value ?? ''));
                if ($changed) {
                    throw ValidationException::withMessages([
                        'teaching_fields' => 'Ngành đang được xét duyệt nên không thể chỉnh sửa. Vui lòng chờ Admin phản hồi.',
                    ]);
                }

                $keptFieldIds[] = (int) $existing->id;

                continue;
            }

            if ($existing?->isSuperseded()) {
                $keptFieldIds[] = (int) $existing->id;

                continue;
            }

            if ($existing?->isApproved() && (int) $existing->category_id === $categoryId) {
                // Professional metadata is safe to keep current for an already
                // approved teaching field; its authorization remains unchanged.
                $existing->update($attributes);
                $keptFieldIds[] = (int) $existing->id;

                continue;
            }

            if ($existing?->isEditable() && (int) $existing->category_id === $categoryId) {
                $existing->update(array_merge($attributes, [
                    'approval_status' => InstructorTeachingField::STATUS_DRAFT,
                    'submitted_at' => null,
                    'reviewed_at' => null,
                    'reviewed_by' => null,
                    'rejection_reason' => null,
                ]));
                $keptFieldIds[] = (int) $existing->id;

                continue;
            }

            // Superseded records are immutable history. They deliberately stay
            // visible in the profile but must never be turned into a new draft
            // merely because the profile form is saved again.
            $explicitReplacementId = (int) ($fieldData['replace_of_teaching_field_id'] ?? 0);
            if (! $existing && $explicitReplacementId) {
                $replacement = $this->teachingFields()->find($explicitReplacementId);
                if ($replacement?->approval_status === InstructorTeachingField::STATUS_PENDING) {
                    throw ValidationException::withMessages([
                        'teaching_fields' => 'Ngành đang được xét duyệt nên không thể thay thế.',
                    ]);
                }

                if ($replacement?->isEditable() && $this->user?->instructor_status !== 'approved') {
                    $replacement->update(array_merge($attributes, [
                        'category_id' => $categoryId,
                        'approval_status' => InstructorTeachingField::STATUS_DRAFT,
                        'submitted_at' => null,
                        'reviewed_at' => null,
                        'reviewed_by' => null,
                        'rejection_reason' => null,
                        'replace_of_teaching_field_id' => null,
                    ]));
                    $keptFieldIds[] = (int) $replacement->id;

                    continue;
                }
            }

            $candidate = $this->teachingFields()
                ->where('category_id', $categoryId)
                ->whereIn('approval_status', [InstructorTeachingField::STATUS_DRAFT, InstructorTeachingField::STATUS_PENDING, InstructorTeachingField::STATUS_REJECTED])
                ->first();

            if ($candidate && $candidate->approval_status === InstructorTeachingField::STATUS_PENDING) {
                throw ValidationException::withMessages([
                    'teaching_fields' => 'Ngành này đã có yêu cầu đang chờ xét duyệt.',
                ]);
            }

            $replacementId = $existing?->isApproved() ? $existing->id : null;
            if (! $replacementId && ! empty($fieldData['replace_of_teaching_field_id'])) {
                $replacement = $this->teachingFields()->approved()->find((int) $fieldData['replace_of_teaching_field_id']);
                $replacementId = $replacement?->id;
            }
            if (! $replacementId && count($omittedApprovedIds) === 1) {
                $replacementId = (int) $omittedApprovedIds[0];
            }

            $payload = array_merge($attributes, [
                'category_id' => $categoryId,
                // A legacy profile may not yet have a pivot row. Its persisted
                // primary category remains effective when the instructor was
                // already globally approved; every genuinely new category is draft.
                'approval_status' => ! $existing
                    && $this->teachingFields()->doesntExist()
                    && (int) $this->category_id === $categoryId
                    && $this->user?->instructor_status === 'approved'
                    ? InstructorTeachingField::STATUS_APPROVED
                    : InstructorTeachingField::STATUS_DRAFT,
                'submitted_at' => null,
                'reviewed_at' => null,
                'reviewed_by' => null,
                'rejection_reason' => null,
                'replace_of_teaching_field_id' => $replacementId,
                'is_primary' => false,
            ]);

            if ($candidate) {
                $candidate->update($payload);
                $keptFieldIds[] = (int) $candidate->id;
            } else {
                $created = $this->teachingFields()->create($payload);
                $keptFieldIds[] = (int) $created->id;
            }
        }

        $omittedDrafts = $this->teachingFields()
            ->where('approval_status', InstructorTeachingField::STATUS_DRAFT)
            ->when($keptFieldIds !== [], fn ($query) => $query->whereNotIn('id', $keptFieldIds))
            ->get();
        if ($omittedDrafts->contains(fn (InstructorTeachingField $field) => $field->certificates()->exists())) {
            throw ValidationException::withMessages([
                'teaching_fields' => 'Vui lòng xóa tài liệu nháp của ngành trước khi xóa ngành khỏi hồ sơ.',
            ]);
        }
        $this->teachingFields()->whereKey($omittedDrafts->pluck('id'))->delete();

        if ($this->user?->instructor_status !== 'approved') {
            $primary = $this->teachingFields()
                ->whereNotIn('approval_status', [InstructorTeachingField::STATUS_SUPERSEDED])
                ->orderBy('id')
                ->first();
            if ($primary) {
                $this->teachingFields()->update(['is_primary' => false]);
                $primary->update(['is_primary' => true]);
                $category = Category::find($primary->category_id);
                $this->update([
                    'category_id' => $primary->category_id,
                    'teaching_field' => $category?->name ?? $this->teaching_field,
                    'organization' => $primary->organization,
                    'position' => $primary->position,
                    'specialty' => $primary->specialty,
                    'experience' => $primary->experience,
                ]);
            }
        }
    }
}
