<?php

namespace App\Models;

use App\Models\Concerns\HasImmutableVersionState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LessonVersion extends Model
{
    use HasImmutableVersionState;

    protected $fillable = ['lesson_id', 'version_number', 'status', 'content_update_id', 'source_version_id', 'section_id', 'legacy_chapter_id', 'title', 'type', 'content', 'document_file', 'video_url', 'video_path', 'original_video_key', 'hls_manifest_key', 'hls_playlist', 'hls_path', 'video_original_name', 'video_mime', 'video_size', 'duration_seconds', 'is_preview', 'is_required', 'sort_order', 'attachments', 'subtitles', 'created_by', 'published_by', 'published_at', 'superseded_at', 'rejected_at'];

    protected function casts(): array
    {
        return ['version_number' => 'integer', 'source_version_id' => 'integer', 'video_size' => 'integer', 'duration_seconds' => 'integer', 'is_preview' => 'boolean', 'is_required' => 'boolean', 'sort_order' => 'integer', 'attachments' => 'array', 'subtitles' => 'array', 'published_at' => 'datetime', 'superseded_at' => 'datetime', 'rejected_at' => 'datetime'];
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function contentUpdate(): BelongsTo
    {
        return $this->belongsTo(ContentUpdate::class);
    }

    public function sourceVersion(): BelongsTo
    {
        return $this->belongsTo(self::class, 'source_version_id');
    }

    public function derivedVersions(): HasMany
    {
        return $this->hasMany(self::class, 'source_version_id');
    }
}
