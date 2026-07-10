<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonProgress extends Model
{
    protected $table = 'lesson_progress';

    protected $fillable = [
        'user_id',
        'course_id',
        'lesson_id',
        'watched_seconds',
        'duration_seconds',
        'progress_percent',
        'is_completed',
        'last_watched_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'watched_seconds' => 'integer',
            'duration_seconds' => 'integer',
            'progress_percent' => 'decimal:2',
            'is_completed' => 'boolean',
            'last_watched_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
