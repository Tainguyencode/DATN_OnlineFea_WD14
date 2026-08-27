<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizVersion extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_SUPERSEDED = 'superseded';

    protected $fillable = [
        'quiz_id',
        'version',
        'title',
        'description',
        'pass_score',
        'time_limit_minutes',
        'max_attempts',
        'status',
        'created_by',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'version' => 'integer',
            'pass_score' => 'integer',
            'time_limit_minutes' => 'integer',
            'max_attempts' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questionMappings(): HasMany
    {
        return $this->hasMany(QuizVersionQuestion::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(
            QuizQuestion::class,
            'quiz_version_questions',
            'quiz_version_id',
            'question_id',
        )
            ->withPivot(['id', 'question_version_id', 'sort_order'])
            ->withTimestamps()
            ->orderByPivot('sort_order')
            ->orderBy('quiz_questions.id');
    }

    public function questionVersions(): BelongsToMany
    {
        return $this->belongsToMany(
            QuestionVersion::class,
            'quiz_version_questions',
            'quiz_version_id',
            'question_version_id',
        )
            ->withPivot(['id', 'question_id', 'sort_order'])
            ->withTimestamps()
            ->orderByPivot('sort_order')
            ->orderBy('question_versions.id');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }
}
