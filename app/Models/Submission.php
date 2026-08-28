<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Submission extends Model
{
    protected $fillable = [
        'assignment_id',
        'user_id',
        'attempt_number',
        'allowed_attempts',
        'started_at',
        'file_path',
        'content',
        'score',
        'result',
        'feedback',
        'status',
        'submitted_at',
        'graded_at',
        'graded_by',
        'grading_history',
        'granted_by',
        'granted_at',
        'grant_reason',
    ];

    protected function casts(): array
    {
        return [
            'attempt_number' => 'integer',
            'allowed_attempts' => 'integer',
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
            'graded_at' => 'datetime',
            'granted_at' => 'datetime',
            'score' => 'integer',
            'grading_history' => 'array',
        ];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function gradedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    public function granter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'granted_by');
    }

    public function getDeadline(): ?\Carbon\Carbon
    {
        return $this->started_at ? $this->started_at->copy()->addHours(6) : null;
    }

    public function isExpired(): bool
    {
        if ($this->status === 'expired') {
            return true;
        }

        if ($this->started_at && ! $this->submitted_at) {
            $deadline = $this->getDeadline();

            return $deadline && now()->gt($deadline);
        }

        return false;
    }

    public function isPassed(): bool
    {
        return $this->result === 'pass';
    }

    public function isFailed(): bool
    {
        return $this->result === 'fail' || $this->isExpired();
    }

    public function isLate(): bool
    {
        if (! $this->submitted_at || ! $this->assignment?->due_date) {
            return false;
        }

        return $this->submitted_at->gt($this->assignment->due_date);
    }

    public function canRetake(): bool
    {
        if ($this->isPassed()) {
            return false;
        }

        // Chỉ cho phép làm lại nếu attempt hiện tại đã kết thúc (được chấm FAIL hoặc đã EXPIRED)
        $isFinished = ($this->status === 'graded' && $this->result === 'fail') || $this->isExpired();

        if (! $isFinished) {
            return false;
        }

        return $this->attempt_number < $this->allowed_attempts;
    }
}
