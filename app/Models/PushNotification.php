<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PushNotification extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'url',
        'is_read',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'read_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead(): void
    {
        if ($this->is_read) {
            return;
        }

        $this->forceFill([
            'is_read' => true,
            'read_at' => now(),
        ])->save();
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'announcement' => 'Thông báo chung',
            'badge_unlocked' => 'Huy hiệu',
            'discussion_reply' => 'Thảo luận',
            'new_discussion' => 'Thảo luận mới',
            'course_approved' => 'Khóa học duyệt',
            'course_rejected' => 'Khóa học từ chối',
            'new_enrollment' => 'Ghi danh mới',
            'order_paid' => 'Thanh toán',
            'certificate_issued' => 'Chứng chỉ',
            'course_review_created' => 'Đánh giá mới',
            'course_review_replied' => 'Phản hồi đánh giá',
            'course_review_moderated' => 'Kiểm duyệt đánh giá',
            'review_reply' => 'Phản hồi đánh giá',
            default => 'Thông báo',
        };
    }

    public function typeColor(): string
    {
        return match ($this->type) {
            'announcement' => 'bg-blue-100 text-blue-700',
            'badge_unlocked' => 'bg-amber-100 text-amber-700',
            'discussion_reply', 'new_discussion' => 'bg-violet-100 text-violet-700',
            'course_approved' => 'bg-emerald-100 text-emerald-700',
            'course_rejected' => 'bg-red-100 text-red-700',
            'new_enrollment' => 'bg-cyan-100 text-cyan-700',
            'order_paid' => 'bg-indigo-100 text-indigo-700',
            'certificate_issued' => 'bg-teal-100 text-teal-700',
            'course_review_created', 'course_review_replied' => 'bg-amber-100 text-amber-700',
            'course_review_moderated' => 'bg-fuchsia-100 text-fuchsia-700',
            'review_reply' => 'bg-emerald-100 text-emerald-700',
            default => 'bg-slate-100 text-slate-700',
        };
    }
}
