<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizVersionQuestionInvalidation extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'quiz_version_question_id',
        'status',
        'requested_by',
        'invalidated_by',
        'reviewed_by',
        'invalidated_at',
        'reviewed_at',
        'reason',
        'rejection_reason',
        'regrade_started_at',
        'regrade_completed_at',
    ];

    protected function casts(): array
    {
        return [
            'invalidated_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'regrade_started_at' => 'datetime',
            'regrade_completed_at' => 'datetime',
        ];
    }

    public function mapping(): BelongsTo
    {
        return $this->belongsTo(QuizVersionQuestion::class, 'quiz_version_question_id');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function invalidatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invalidated_by');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function regrades(): HasMany
    {
        return $this->hasMany(QuizAttemptRegrade::class, 'invalidation_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }
}
