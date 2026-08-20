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
        'video_url', 'video_path', 'original_video_key', 'hls_manifest_key',
        'upload_status', 'processing_status',
        'video_original_name', 'video_mime',
        'video_size', 'content', 'document_file', 'duration',
        'duration_seconds', 'is_preview', 'is_required', 'sort_order', 'status',
        'attachments', 'subtitles', 'ai_summary', 'content_version',
    ];

    protected function casts(): array
    {
        return [
            'is_preview' => 'boolean',
            'is_required' => 'boolean',
            'video_size' => 'integer',
            'content_version' => 'integer',
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

    public function comments(): HasMany
    {
        return $this->hasMany(LessonComment::class);
    }

    public function videoNotes(): HasMany
    {
        return $this->hasMany(VideoNote::class);
    }

    public function lessonNotes(): HasMany
    {
        return $this->hasMany(LessonNote::class);
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

    /**
     * Kiểm tra video đã sẵn sàng phát HLS hay chưa (S3 hoặc local)
     */
    public function isHlsReady(): bool
    {
        if ($this->processing_status === 'completed' && filled($this->hls_manifest_key)) {
            return true;
        }

        if (filled($this->hls_manifest_key)) {
            return true;
        }

        if (filled($this->video_path) && str_ends_with($this->video_path, '.m3u8')) {
            return true;
        }

        return false;
    }

    /**
     * Kiểm tra video có đang trong quá trình chuyển đổi HLS hay không
     */
    public function isProcessing(): bool
    {
        if ($this->isHlsReady()) {
            return false;
        }

        if (in_array($this->processing_status, ['processing', 'pending'], true)) {
            return true;
        }

        return (filled($this->original_video_key) || filled($this->video_path))
            && $this->type === 'video'
            && empty($this->video_url)
            && $this->processing_status !== 'failed';
    }

    /**
     * Kiểm tra quá trình xử lý video có bị thất bại hay không
     */
    public function hasFailedProcessing(): bool
    {
        return $this->processing_status === 'failed' && ! $this->isHlsReady();
    }
}
