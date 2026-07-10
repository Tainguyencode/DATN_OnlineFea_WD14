<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    public const STATUS_ACTIVE = 'active';

    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'user_id', 'course_id', 'order_id', 'status', 'progress_percent',
        'completed_lessons', 'total_lessons',
        'enrolled_at', 'completed_at', 'last_accessed_at',
    ];

    protected function casts(): array
    {
        return [
            'progress_percent' => 'decimal:2',
            'completed_lessons' => 'integer',
            'total_lessons' => 'integer',
            'enrolled_at' => 'datetime',
            'completed_at' => 'datetime',
            'last_accessed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function scopeWithLearningAccess($query)
    {
        return $query->whereIn('status', [self::STATUS_ACTIVE, self::STATUS_COMPLETED]);
    }

    public function hasLearningAccess(): bool
    {
        return in_array($this->status, [self::STATUS_ACTIVE, self::STATUS_COMPLETED], true);
    }

    public function isCourseCompleted(): bool
    {
        return $this->completed_at !== null;
    }
}
