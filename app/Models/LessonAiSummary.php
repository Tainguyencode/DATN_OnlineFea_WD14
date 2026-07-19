<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonAiSummary extends Model
{
    protected $fillable = [
        'lesson_id',
        'summary',
        'key_points',
        'source_hash',
        'model',
        'generated_at',
    ];

    protected function casts(): array
    {
        return [
            'key_points' => 'array',
            'generated_at' => 'datetime',
        ];
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
