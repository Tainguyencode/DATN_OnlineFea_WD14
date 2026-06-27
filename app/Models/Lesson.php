<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lesson extends Model
{
    protected $fillable = [
        'chapter_id', 'title', 'content', 'type', 'video_url',
        'duration_seconds', 'is_preview', 'sort_order',
        'attachments', 'subtitles', 'ai_summary',
    ];

    protected function casts(): array
    {
        return [
            'is_preview' => 'boolean',
            'attachments' => 'array',
            'subtitles' => 'array',
        ];
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
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
