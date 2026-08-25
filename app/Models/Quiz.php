<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    protected $fillable = [
        'lesson_id',
        'current_published_version_id',
        'current_draft_version_id',
        'title',
        'description',
        'pass_score',
        'time_limit_minutes',
        'max_attempts',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'pass_score' => 'integer',
            'time_limit_minutes' => 'integer',
            'max_attempts' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('sort_order')->orderBy('id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(QuizVersion::class)->orderBy('version');
    }

    public function currentPublishedVersion(): BelongsTo
    {
        return $this->belongsTo(QuizVersion::class, 'current_published_version_id');
    }

    public function currentDraftVersion(): BelongsTo
    {
        return $this->belongsTo(QuizVersion::class, 'current_draft_version_id');
    }

    public function publishedVersion(): BelongsTo
    {
        return $this->currentPublishedVersion();
    }

    public function authoringVersion(): BelongsTo
    {
        return $this->currentDraftVersion();
    }
}
