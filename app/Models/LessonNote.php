<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LessonNote extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'lesson_id',
        'timestamp_seconds',
        'content',
    ];

    protected function casts(): array
    {
        return [
            'timestamp_seconds' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function timestampLabel(): ?string
    {
        if ($this->timestamp_seconds === null) {
            return null;
        }

        $seconds = max(0, (int) $this->timestamp_seconds);
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $remainingSeconds = $seconds % 60;

        return $hours > 0
            ? sprintf('%d:%02d:%02d', $hours, $minutes, $remainingSeconds)
            : sprintf('%d:%02d', $minutes, $remainingSeconds);
    }

    public function learningCourse(): ?Course
    {
        $lesson = $this->lesson;

        return $lesson?->course
            ?? $lesson?->section?->course
            ?? $lesson?->chapter?->course;
    }

    protected $appends = [
        'timestamp_label',
        'update_url',
        'delete_url',
    ];

    public function getTimestampLabelAttribute(): ?string
    {
        return $this->timestampLabel();
    }

    public function getUpdateUrlAttribute(): string
    {
        return route('lesson-notes.update', $this);
    }

    public function getDeleteUrlAttribute(): string
    {
        return route('lesson-notes.destroy', $this);
    }

    public function sectionTitle(): ?string
    {
        return $this->lesson?->section?->title
            ?? $this->lesson?->chapter?->title;
    }
}
