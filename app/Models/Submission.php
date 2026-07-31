<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Submission extends Model
{
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
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'graded_at' => 'datetime',
            'score' => 'integer',
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

    public function isLate(): bool
    {
        if (!$this->submitted_at || !$this->assignment?->due_date) {
            return false;
        }
        return $this->submitted_at->gt($this->assignment->due_date);
    }
}
