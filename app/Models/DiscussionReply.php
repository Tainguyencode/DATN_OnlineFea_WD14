<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class DiscussionReply extends Model
{
    protected $fillable = [
        'discussion_id',
        'reply_to_message_id',
        'lesson_id',
        'user_id',
        'content',
        'is_instructor_answer',
        'is_helpful',
        'is_recalled',
        'attachment_path',
        'attachment_name',
        'attachment_type',
    ];

    protected function casts(): array
    {
        return [
            'is_instructor_answer' => 'boolean',
            'is_helpful' => 'boolean',
            'is_recalled' => 'boolean',
        ];
    }

    public function discussion(): BelongsTo
    {
        return $this->belongsTo(Discussion::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(DiscussionReply::class, 'reply_to_message_id');
    }

    public function childReplies()
    {
        return $this->hasMany(DiscussionReply::class, 'reply_to_message_id');
    }

    public function attachmentUrl(): ?string
    {
        if (! $this->attachment_path) {
            return null;
        }

        if (str_starts_with($this->attachment_path, 'http://') || str_starts_with($this->attachment_path, 'https://')) {
            return $this->attachment_path;
        }

        if (Storage::disk('public')->exists($this->attachment_path)) {
            return Storage::disk('public')->url($this->attachment_path);
        }

        return null;
    }
}
