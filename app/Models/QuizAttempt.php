<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizAttempt extends Model
{
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_TERMINATED = 'terminated';
    public const STATUS_EXPIRED = 'expired';

    public const REASON_SUBMITTED = 'submitted';
    public const REASON_TAB_SWITCH = 'tab_switch';
    public const REASON_WINDOW_BLUR = 'window_blur';
    public const REASON_FULLSCREEN_EXIT = 'fullscreen_exit';
    public const REASON_PAGE_EXIT = 'page_exit';
    public const REASON_TIME_EXPIRED = 'time_expired';

    protected $fillable = [
        'user_id',
        'quiz_id',
        'quiz_version_id',
        'status',
        'termination_reason',
        'remaining_seconds',
        'score',
        'total_score',
        'percent',
        'passed',
        'answers',
        'presentation_order',
        'started_at',
        'completed_at',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'integer',
            'total_score' => 'integer',
            'percent' => 'decimal:2',
            'passed' => 'boolean',
            'answers' => 'array',
            'presentation_order' => 'array',
            'remaining_seconds' => 'integer',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function quizVersion(): BelongsTo
    {
        return $this->belongsTo(QuizVersion::class);
    }

    public function attemptAnswers(): HasMany
    {
        return $this->hasMany(QuizAttemptAnswer::class);
    }

    public function regrades(): HasMany
    {
        return $this->hasMany(QuizAttemptRegrade::class);
    }

    public function getIsPassedAttribute(): bool
    {
        return (bool) $this->passed;
    }

    public function isTerminated(): bool
    {
        return $this->status === self::STATUS_TERMINATED;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isFinalized(): bool
    {
        return in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_TERMINATED, self::STATUS_EXPIRED], true);
    }

    public function getTerminationReasonLabel(): string
    {
        return match ($this->termination_reason) {
            self::REASON_TAB_SWITCH => 'Chuyển sang tab khác trong khi làm bài',
            self::REASON_WINDOW_BLUR => 'Rời khỏi cửa sổ bài làm (mất focus)',
            self::REASON_FULLSCREEN_EXIT => 'Thoát chế độ toàn màn hình (Fullscreen)',
            self::REASON_PAGE_EXIT => 'Thoát khỏi trang làm bài',
            self::REASON_TIME_EXPIRED => 'Hết thời gian làm bài',
            self::REASON_SUBMITTED => 'Đã nộp bài',
            default => 'Vi phạm quy định làm bài kiểm tra',
        };
    }
}
