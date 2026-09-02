<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InstructorTeachingField extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_SUPERSEDED = 'superseded';

    protected $table = 'instructor_profile_teaching_fields';

    protected $fillable = [
        'instructor_profile_id', 'category_id', 'organization', 'position', 'specialty', 'experience',
        'is_primary', 'approval_status', 'submitted_at', 'reviewed_at', 'reviewed_by',
        'rejection_reason', 'replace_of_teaching_field_id',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(InstructorProfile::class, 'instructor_profile_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function replacedField(): BelongsTo
    {
        return $this->belongsTo(self::class, 'replace_of_teaching_field_id');
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(InstructorCertificate::class, 'instructor_teaching_field_id');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('approval_status', self::STATUS_APPROVED);
    }

    public function isEditable(): bool
    {
        return in_array($this->approval_status, [self::STATUS_DRAFT, self::STATUS_REJECTED], true);
    }

    public function acceptsDocumentUploads(): bool
    {
        return in_array($this->approval_status, [
            self::STATUS_DRAFT,
            self::STATUS_REJECTED,
            self::STATUS_APPROVED,
        ], true);
    }

    public function isApproved(): bool
    {
        return $this->approval_status === self::STATUS_APPROVED;
    }

    public function isSuperseded(): bool
    {
        return $this->approval_status === self::STATUS_SUPERSEDED;
    }
}
