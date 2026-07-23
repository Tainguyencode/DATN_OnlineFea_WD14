<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lesson extends Model
{
    protected $fillable = [
        'course_id', 'section_id', 'chapter_id', 'title', 'type',
        'video_url', 'video_path', 'video_original_name', 'video_mime',
        'video_size', 'content', 'document_file', 'duration',
        'duration_seconds', 'is_preview', 'is_required', 'sort_order', 'status',
        'attachments', 'subtitles', 'ai_summary',
    ];

    protected function casts(): array
    {
        return [
            'is_preview' => 'boolean',
            'is_required' => 'boolean',
            'video_size' => 'integer',
            'attachments' => 'array',
            'subtitles' => 'array',
        ];
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'section_id');
    }

    public function quiz(): HasOne
    {
        return $this->hasOne(Quiz::class);
    }

    public function assignment(): HasOne
    {
        return $this->hasOne(Assignment::class);
    }

    public function discussions(): HasMany
    {
        return $this->hasMany(Discussion::class);
    }

    public function videoNotes(): HasMany
    {
        return $this->hasMany(VideoNote::class);
    }

    public function videoModeration(): HasOne
    {
        return $this->hasOne(VideoModeration::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function aiSummaries(): HasMany
    {
        return $this->hasMany(AiSummary::class);
    }

    public function aiSummary(): HasOne
    {
        return $this->hasOne(AiSummary::class)->where('language', 'vi');
    }

    public function lessonAiSummary(): HasOne
    {
        return $this->hasOne(LessonAiSummary::class);
    }

    public function aiChatMessages(): HasMany
    {
        return $this->hasMany(AiChatMessage::class);
    }
}
