<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPoint extends Model
{
    protected $fillable = [
        'user_id',
        'points',
        'type',
        'source',
        'description',
        'course_id',
        'reference_id',
        'reason',
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'points' => 'integer',
            'user_id' => 'integer',
            'course_id' => 'integer',
            'reference_id' => 'integer',
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

    /**
     * Nhãn nguồn cộng điểm thân thiện
     */
    public function getSourceLabelAttribute(): string
    {
        return match ($this->source) {
            'lesson_completed' => 'Hoàn thành bài học',
            'quiz_completed' => 'Hoàn thành bài kiểm tra',
            'quiz_score_bonus_90', 'quiz_passed_perfect' => 'Thưởng Quiz xuất sắc (≥90%)',
            'quiz_score_bonus_80', 'quiz_passed_high' => 'Thưởng Quiz loại giỏi (≥80%)',
            'course_completed' => 'Hoàn thành khóa học',
            'review_created' => 'Đánh giá khóa học',
            'discussion_created' => 'Hỏi đáp & Thảo luận',
            'streak_bonus_3' => 'Chuỗi học 3 ngày (Streak)',
            'streak_bonus_7' => 'Chuỗi học 7 ngày (Streak)',
            'streak_bonus_30' => 'Chuỗi học 30 ngày (Streak)',
            'leaderboard_reward' => 'Thưởng Bảng xếp hạng',
            default => 'Thưởng hoạt động học tập',
        };
    }

    /**
     * Nhóm loại hoạt động để lọc (lesson, quiz, community, streak, other)
     */
    public function getCategoryAttribute(): string
    {
        return match ($this->source) {
            'lesson_completed' => 'lesson',
            'quiz_completed', 'quiz_score_bonus_90', 'quiz_score_bonus_80', 'quiz_passed_perfect', 'quiz_passed_high' => 'quiz',
            'discussion_created', 'review_created' => 'community',
            'streak_bonus_3', 'streak_bonus_7', 'streak_bonus_30' => 'streak',
            default => 'other',
        };
    }

    /**
     * Icon đại diện
     */
    public function getSourceIconAttribute(): string
    {
        return match ($this->source) {
            'lesson_completed' => '📚',
            'quiz_completed', 'quiz_score_bonus_90', 'quiz_score_bonus_80', 'quiz_passed_perfect', 'quiz_passed_high' => '📝',
            'course_completed' => '🎓',
            'review_created' => '⭐',
            'discussion_created' => '💬',
            'streak_bonus_3', 'streak_bonus_7', 'streak_bonus_30' => '🔥',
            'leaderboard_reward' => '🏆',
            default => '✨',
        };
    }

    /**
     * Mô tả làm sạch không chứa id kỹ thuật
     */
    public function getCleanDescriptionAttribute(): string
    {
        if (empty($this->description)) {
            return $this->source_label;
        }

        // Loại bỏ chuỗi như (lesson_id:123), (quiz_id:45), (course_id:6)
        $clean = preg_replace('/\s*\([a-z_]+:\d+\)/i', '', $this->description);
        return trim((string) $clean) ?: $this->source_label;
    }
}
