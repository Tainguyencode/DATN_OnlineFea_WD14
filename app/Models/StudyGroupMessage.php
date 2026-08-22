<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class StudyGroupMessage extends Model
{
    protected $fillable = [
        'study_group_id',
        'user_id',
        'reply_to_message_id',
        'message',
        'is_recalled',
        'image_path',
        'message_type',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
    ];

    protected $appends = ['image_url', 'file_url'];

    protected function casts(): array
    {
        return [
            'study_group_id' => 'integer',
            'user_id' => 'integer',
            'reply_to_message_id' => 'integer',
            'is_recalled' => 'boolean',
            'file_size' => 'integer',
        ];
    }

    public function getImageUrlAttribute()
    {
        if ($this->is_recalled) {
            return null;
        }

        if ($this->image_path) {
            return Storage::disk('public')->url($this->image_path);
        }

        if ($this->message_type === 'image' && $this->file_path) {
            return route('study-groups.messages.download', [$this->study_group_id, $this->id]);
        }

        return null;
    }

    public function getFileUrlAttribute()
    {
        if ($this->is_recalled) {
            return null;
        }

        if (in_array($this->message_type, ['video', 'file', 'image']) && $this->file_path) {
            return route('study-groups.messages.download', [$this->study_group_id, $this->id]);
        }

        return null;
    }

    public function studyGroup(): BelongsTo
    {
        return $this->belongsTo(StudyGroup::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(StudyGroupMessage::class, 'reply_to_message_id');
    }
}
