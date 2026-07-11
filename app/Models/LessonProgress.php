<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * LessonProgress Model
 * 
 * Theo dõi tiến độ học của user cho từng lesson
 * - Kiểm tra bài học đã hoàn thành chưa
 * - Lưu thời gian xem video (watched_seconds)
 * - Ghi nhận thời điểm hoàn thành (completed_at)
 */
class LessonProgress extends Model
{
    protected $table = 'lesson_progress';

    protected $fillable = [
        'user_id',
        'lesson_id',
        'watched_seconds',
        'is_completed',
        'completed_at',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'watched_seconds' => 'integer',
        'completed_at' => 'datetime',
    ];

    /**
     * Relationship: LessonProgress thuộc về một User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: LessonProgress thuộc về một Lesson
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Scope: Lấy lesson progress đã hoàn thành
     */
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    /**
     * Scope: Lấy lesson progress chưa hoàn thành
     */
    public function scopeIncomplete($query)
    {
        return $query->where('is_completed', false);
    }
}
