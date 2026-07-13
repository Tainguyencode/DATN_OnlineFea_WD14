<?php

namespace App\Models;

use App\Enums\CourseReviewStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    protected $fillable = [
        'course_id',
        'reviewer_id',
        'submission_number',
        'status',
        'comment',
        'checklist_json',
        'submitted_at',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'submission_number' => 'integer',
            'checklist_json' => 'array',
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'status' => CourseReviewStatus::class,
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

    public function statusLabel(): string
    {
        return $this->status instanceof CourseReviewStatus
            ? $this->status->label()
            : (string) $this->status;
    }
}
