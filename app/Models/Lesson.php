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
        'duration_seconds', 'is_preview', 'sort_order', 'status',
        'attachments', 'subtitles', 'ai_summary',
    ];

    protected function casts(): array
    {
        return [
            'is_preview' => 'boolean',
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
}
