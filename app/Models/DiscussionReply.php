<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class DiscussionReply extends Model
{
    protected $fillable = [
        'discussion_id',
        'user_id',
        'content',
        'is_instructor_answer',
        'attachment_path',
        'attachment_name',
        'attachment_type',
    ];

    protected function casts(): array
    {
        return [
            'is_instructor_answer' => 'boolean',
        ];
    }

    public function discussion(): BelongsTo
    {
        return $this->belongsTo(Discussion::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attachmentUrl(): ?string
    {
        return $this->attachment_path ? Storage::disk('public')->url($this->attachment_path) : null;
    }
}
