<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAttemptRegrade extends Model
{
    protected $fillable = [
        'quiz_attempt_id',
        'invalidation_id',
        'original_score',
        'original_total_score',
        'original_percent',
        'original_passed',
        'recalculated_score',
        'recalculated_total_score',
        'recalculated_percent',
        'recalculated_passed',
        'effective_score',
        'effective_total_score',
        'effective_percent',
        'effective_passed',
        'regraded_at',
    ];

    protected function casts(): array
    {
        return [
            'original_score' => 'integer',
            'original_total_score' => 'integer',
            'original_percent' => 'decimal:2',
            'original_passed' => 'boolean',
            'recalculated_score' => 'integer',
            'recalculated_total_score' => 'integer',
            'recalculated_percent' => 'decimal:2',
            'recalculated_passed' => 'boolean',
            'effective_score' => 'integer',
            'effective_total_score' => 'integer',
            'effective_percent' => 'decimal:2',
            'effective_passed' => 'boolean',
            'regraded_at' => 'datetime',
        ];
    }

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(QuizAttempt::class, 'quiz_attempt_id');
    }

    public function invalidation(): BelongsTo
    {
        return $this->belongsTo(QuizVersionQuestionInvalidation::class, 'invalidation_id');
    }
}
