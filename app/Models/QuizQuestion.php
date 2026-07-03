<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizQuestion extends Model
{
    public const TYPE_SINGLE = 'single';
    public const TYPE_MULTIPLE = 'multiple';
    public const TYPE_TRUE_FALSE = 'true_false';

    protected $fillable = [
        'quiz_id',
        'question',
        'type',
        'points',
        'explanation',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'points' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuizOption::class, 'quiz_question_id')->orderBy('sort_order')->orderBy('id');
    }

    public function attemptAnswers(): HasMany
    {
        return $this->hasMany(QuizAttemptAnswer::class, 'question_id');
    }

    public function getFormTypeAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_MULTIPLE => 'multiple_choice',
            self::TYPE_TRUE_FALSE => 'true_false',
            default => 'single_choice',
        };
    }

    public static function storageTypeFromRequest(string $type): string
    {
        return match ($type) {
            'multiple_choice', self::TYPE_MULTIPLE => self::TYPE_MULTIPLE,
            'true_false', self::TYPE_TRUE_FALSE => self::TYPE_TRUE_FALSE,
            default => self::TYPE_SINGLE,
        };
    }
}
