<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class Discussion extends Model
{
    protected $fillable = [
        'course_id',
        'lesson_id',
        'user_id',
        'title',
        'content',
        'is_resolved',
        'is_recalled',
        'last_message_at',
        'last_message_user_id',
        'attachment_path',
        'attachment_name',
        'attachment_type',
    ];

    protected function casts(): array
    {
        return [
            'is_resolved' => 'boolean',
            'is_recalled' => 'boolean',
            'last_message_at' => 'datetime',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(DiscussionReply::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(DiscussionParticipant::class);
    }

    public function lastReply(): HasOne
    {
        return $this->hasOne(DiscussionReply::class)->latestOfMany('created_at');
    }

    public function lastMessageUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_message_user_id');
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

    public function instructor(): ?User
    {
        return $this->course?->instructor ?? $this->lesson?->course?->instructor;
    }

    public function needsReply(): bool
    {
        $replies = $this->relationLoaded('replies') ? $this->replies : $this->replies()->get();

        if ($replies->isEmpty()) {
            return true;
        }

        $lastInstructorReply = $replies->where('is_instructor_answer', true)->sortByDesc('created_at')->first();
        if (! $lastInstructorReply) {
            return true;
        }

        $lastStudentReply = $replies->where('is_instructor_answer', false)->sortByDesc('created_at')->first();
        if ($lastStudentReply && $lastStudentReply->created_at > $lastInstructorReply->created_at) {
            return true;
        }

        return false;
    }

    public function isAnswered(): bool
    {
        return ! $this->needsReply();
    }
}
