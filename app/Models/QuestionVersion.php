<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuestionVersion extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    protected $fillable = [
        'question_id',
        'version',
        'question',
        'image_path',
        'type',
        'points',
        'explanation',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'version' => 'integer',
            'points' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    public function questionIdentity(): BelongsTo
    {
        return $this->belongsTo(QuizQuestion::class, 'question_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuizOption::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function quizVersionMappings(): HasMany
    {
        return $this->hasMany(QuizVersionQuestion::class);
    }

    public function quizVersions(): BelongsToMany
    {
        return $this->belongsToMany(
            QuizVersion::class,
            'quiz_version_questions',
            'question_version_id',
            'quiz_version_id',
        )
            ->withPivot(['id', 'question_id', 'sort_order'])
            ->withTimestamps();
    }

    public function attemptAnswers(): HasMany
    {
        return $this->hasMany(QuizAttemptAnswer::class);
    }
}
