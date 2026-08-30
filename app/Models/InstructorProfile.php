<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
            ->withPivot(['id', 'organization', 'position', 'specialty', 'experience', 'is_primary'])
            ->withTimestamps();
    }

    /**
     * Alias cho teachingCategories
     */
    public function teachingFields(): BelongsToMany
    {
        return $this->teachingCategories();
    }

    public function syncTeachingFields(array $fieldsData): void
    {
        $syncData = [];
        $isFirst = true;
        $primaryId = null;
        $primaryField = null;

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
}
