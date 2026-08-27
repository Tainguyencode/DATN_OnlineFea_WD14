<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'quiz_id',
        'quiz_version_id',
        'status',
        'score',
        'total_score',
        'percent',
        'passed',
        'answers',
        'presentation_order',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'integer',
            'total_score' => 'integer',
            'percent' => 'decimal:2',
            'passed' => 'boolean',
            'answers' => 'array',
            'presentation_order' => 'array',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function quizVersion(): BelongsTo
    {
        return $this->belongsTo(QuizVersion::class);
    }

    public function attemptAnswers(): HasMany
    {
        return $this->hasMany(QuizAttemptAnswer::class);
    }

    public function regrades(): HasMany
    {
        return $this->hasMany(QuizAttemptRegrade::class);
    }

    public function getIsPassedAttribute(): bool
    {
        return (bool) $this->passed;
    }
}
