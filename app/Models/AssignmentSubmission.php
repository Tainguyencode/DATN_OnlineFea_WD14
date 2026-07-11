<?php

namespace App\Models;

use App\Enums\SubmissionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentSubmission extends Model
{
    protected $table = 'submissions';

    protected $fillable = [
        'assignment_id',
        'user_id',
        'file_path',
        'content',
        'score',
        'feedback',
        'status',
        'submitted_at',
        'graded_at',
        'graded_by',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'integer',
            'submitted_at' => 'datetime',
            'graded_at' => 'datetime',
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
        if ($this->score === null) {
            return false;
        }

        return $this->score >= ($this->assignment?->passing_score ?? 70);
    }
}
