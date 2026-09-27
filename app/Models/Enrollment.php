<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class Enrollment extends Model
{
    public const STATUS_ACTIVE = 'active';

    public const STATUS_COMPLETED = 'completed';

    protected static function booted(): void
    {
        static::updating(function (self $enrollment): void {
            $stored = self::findOrFail($enrollment->id);
            if ($stored->course_version_id !== null
                && (($enrollment->isDirty('course_version_id') && (int) $stored->course_version_id !== (int) $enrollment->course_version_id)
                    || $enrollment->isDirty('course_id'))) {
                throw ValidationException::withMessages(['course_version' => 'Enrollment release pin cannot be changed.']);
            }
        });
    }

    public function courseVersion(): BelongsTo
    {
        return $this->belongsTo(CourseVersion::class);
    }

    protected $fillable = [
        'user_id', 'course_id', 'course_version_id', 'order_id', 'status', 'progress_percent',
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
