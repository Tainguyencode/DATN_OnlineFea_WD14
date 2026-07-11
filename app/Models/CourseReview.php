<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseReview extends Model
{
    public const ACTION_APPROVED = 'approved';

    public const ACTION_NEED_REVISION = 'need_revision';

    public const ACTION_REJECTED = 'rejected';

    public const ACTIONS = [
        self::ACTION_APPROVED,
        self::ACTION_NEED_REVISION,
        self::ACTION_REJECTED,
    ];

    public const ACTION_LABELS = [
        self::ACTION_APPROVED => 'Đã duyệt',
        self::ACTION_NEED_REVISION => 'Yêu cầu chỉnh sửa',
        self::ACTION_REJECTED => 'Từ chối',
    ];

    protected $fillable = [
        'course_id',
        'reviewer_id',
        'action',
        'comment',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CourseReviewItem::class);
    }

    public function actionLabel(): string
    {
        return self::ACTION_LABELS[$this->action] ?? $this->action;
    }
}
