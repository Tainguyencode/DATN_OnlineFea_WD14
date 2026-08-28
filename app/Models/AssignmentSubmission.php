<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentSubmission extends Model
{
    protected $table = 'submissions';

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
        'granted_by',
        'granted_at',
        'grant_reason',
    ];

    protected function casts(): array
    {
        return [
            'attempt_number' => 'integer',
            'allowed_attempts' => 'integer',
            'score' => 'integer',
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
            'graded_at' => 'datetime',
            'granted_at' => 'datetime',
        ];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    public function isPassing(): bool
    {
        if ($this->result !== null) {
            return $this->result === 'pass';
        }

        if ($this->score === null) {
            return false;
        }

        return $this->score >= ($this->assignment?->passing_score ?? 70);
    }
}
