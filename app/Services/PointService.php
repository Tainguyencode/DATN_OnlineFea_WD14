<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserPoint;
use App\Models\Badge;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\QuizAttempt;
use App\Models\Lesson;
use App\Models\Course;
use App\Models\PushNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PointService
{
    /**
     * Award points to a user with anti-duplicate support and timestamp preservation.
     */
    public function awardPoints(
        int $userId,
        int $points,
        string $source,
        ?string $description = null,
        ?int $courseId = null,
        ?int $referenceId = null,
        mixed $createdAt = null
    ): void {
        if ($points <= 0) {
            return;
        }

        $timestamp = $createdAt ? Carbon::parse($createdAt) : now();

        DB::transaction(function () use ($userId, $points, $source, $description, $courseId, $referenceId, $timestamp) {
            $userPoint = new UserPoint([
                'user_id' => $userId,
                'points' => $points,
                'type' => 'earn',
                'source' => $source,
                'description' => $description,
                'course_id' => $courseId,
                'reference_id' => $referenceId,
            ]);
            $userPoint->created_at = $timestamp;
            $userPoint->updated_at = $timestamp;
            $userPoint->save();

            $this->checkAndAwardBadges($userId);
        });
    }

    /**
     * Check if user earned badges and unlock them (with notification deduplication).
     */
    public function checkAndAwardBadges(int $userId): void
    {
        $user = User::find($userId);
        if (!$user) {
            return;
        }

        $totalPoints = $this->getUserTotalPoints($userId);

        $earnedBadgeIds = DB::table('user_badges')
            ->where('user_id', $userId)
            ->pluck('badge_id')
            ->toArray();

        $unlockedBadges = Badge::whereNotIn('id', $earnedBadgeIds)
            ->where('points_required', '<=', $totalPoints)
            ->get();

        foreach ($unlockedBadges as $badge) {
            DB::table('user_badges')->insert([
                'user_id' => $userId,
                'badge_id' => $badge->id,
                'earned_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Deduplicate notifications so user gets 1 notification per badge earned
            $hasNotified = PushNotification::where('user_id', $userId)
                ->where('type', 'badge_earned')
                ->where('message', 'like', "%\"{$badge->name}\"%")
                ->exists();

            if (!$hasNotified) {
                try {
                    PushNotification::create([
                        'user_id' => $userId,
                        'title' => 'Nhận huy hiệu mới',
                        'message' => "Chúc mừng! Bạn đã đạt huy hiệu \"{$badge->name}\" nhờ tích lũy được {$badge->points_required} điểm.",
                        'type' => 'badge_earned',
                        'url' => route('leaderboard'),
                        'is_read' => false,
                    ]);
                } catch (\Throwable $e) {
                    // Ignore route resolution exception
                }
            }
        }
    }

    /**
     * Award points for completing 1 lesson based on video duration:
     * - Video < 30 mins (< 1800s): +10 XP
     * - Video 30 - < 60 mins (1800s - 3599s): +15 XP
     * - Video >= 60 mins (>= 3600s): +20 XP
     * Max once per lesson.
     */
    public function awardLessonCompletionPoints(int $userId, int $lessonId, mixed $createdAt = null): void
    {
        $lesson = Lesson::find($lessonId);
        if (!$lesson) {
            return;
        }

        $lessonTag = "lesson_id:{$lesson->id}";

        $alreadyAwarded = UserPoint::where('user_id', $userId)
            ->where('source', 'lesson_completed')
            ->where(function ($q) use ($lessonId, $lessonTag) {
                $q->where('reference_id', $lessonId)
                  ->orWhere('description', 'like', "%{$lessonTag}%");
            })
            ->exists();

        if ($alreadyAwarded) {
            return;
        }

        $durationSeconds = (int) ($lesson->duration_seconds ?: $lesson->duration ?: 0);

        if ($durationSeconds >= 3600) {
            $points = 20;
        } elseif ($durationSeconds >= 1800) {
            $points = 15;
        } else {
            $points = 10;
        }

        $this->awardPoints(
            $userId,
            $points,
            'lesson_completed',
            "Hoàn thành bài học: {$lesson->title} ({$lessonTag})",
            $lesson->course_id,
            $lessonId,
            $createdAt
        );

        $this->checkAndAwardStreak($userId);
    }

    /**
     * Award points for quiz completion and quiz score bonuses.
     */
    public function awardQuizPoints(int $userId, \App\Models\Quiz $quiz, float $percent, int $courseId, mixed $createdAt = null): void
    {
        $quizTag = "quiz_id:{$quiz->id}";

        // 1. Completion XP (+10 XP)
        $hasCompleted = UserPoint::where('user_id', $userId)
            ->where('source', 'quiz_completed')
            ->where(function ($q) use ($quiz, $quizTag) {
                $q->where('reference_id', $quiz->id)
                  ->orWhere('description', 'like', "%{$quizTag}%");
            })
            ->exists();

        if (!$hasCompleted) {
            $this->awardPoints(
                $userId,
                10,
                'quiz_completed',
                "Hoàn thành quiz: {$quiz->title} ({$quizTag})",
                $courseId,
                $quiz->id,
                $createdAt
            );
        }

        // 2. Score Bonus XP
        $has90Bonus = UserPoint::where('user_id', $userId)
            ->whereIn('source', ['quiz_score_bonus_90', 'quiz_passed_perfect'])
            ->where(function ($q) use ($quiz, $quizTag) {
                $q->where('reference_id', $quiz->id)
                  ->orWhere('description', 'like', "%{$quizTag}%");
            })
            ->exists();

        $has80Bonus = UserPoint::where('user_id', $userId)
            ->whereIn('source', ['quiz_score_bonus_80', 'quiz_passed_high'])
            ->where(function ($q) use ($quiz, $quizTag) {
                $q->where('reference_id', $quiz->id)
                  ->orWhere('description', 'like', "%{$quizTag}%");
            })
            ->exists();

        if ($percent >= 90.0) {
            if (!$has90Bonus) {
                $bonusToAward = $has80Bonus ? 10 : 20;
                $this->awardPoints(
                    $userId,
                    $bonusToAward,
                    'quiz_score_bonus_90',
                    "Thưởng quiz đạt từ 90%: {$quiz->title} ({$quizTag})",
                    $courseId,
                    $quiz->id,
                    $createdAt
                );
            }
        } elseif ($percent >= 80.0) {
            if (!$has80Bonus && !$has90Bonus) {
                $this->awardPoints(
                    $userId,
                    10,
                    'quiz_score_bonus_80',
                    "Thưởng quiz đạt từ 80%: {$quiz->title} ({$quizTag})",
                    $courseId,
                    $quiz->id,
                    $createdAt
                );
            }
        }

        $this->checkAndAwardStreak($userId);
    }

    /**
     * Award points for completing a course (+50 XP, max once per course).
     */
    public function awardCourseCompletionPoints(int $userId, int $courseId, mixed $createdAt = null): void
    {
        $course = Course::find($courseId);
        if (!$course) {
            return;
        }

        $courseTag = "course_id:{$course->id}";

        $alreadyAwarded = UserPoint::where('user_id', $userId)
            ->where('source', 'course_completed')
            ->where(function ($q) use ($courseId, $courseTag) {
                $q->where('reference_id', $courseId)
                  ->orWhere('course_id', $courseId)
                  ->orWhere('description', 'like', "%{$courseTag}%");
            })
            ->exists();

        if ($alreadyAwarded) {
            return;
        }

        $this->awardPoints(
            $userId,
            50,
            'course_completed',
            "Hoàn thành khóa học: {$course->title} ({$courseTag})",
            $courseId,
            $courseId,
            $createdAt
        );

        $this->checkAndAwardStreak($userId);
    }

    /**
     * Award points for course review (+5 XP, max once per course per user).
     */
    public function awardReviewPoints(int $userId, int $courseId, ?int $reviewId = null, mixed $createdAt = null): void
    {
        $course = Course::find($courseId);
        $courseTitle = $course ? $course->title : "Khóa học #{$courseId}";

        $alreadyAwarded = UserPoint::where('user_id', $userId)
            ->where('source', 'review_created')
            ->where('course_id', $courseId)
            ->exists();

        if ($alreadyAwarded) {
            return;
        }

        $this->awardPoints(
            $userId,
            5,
            'review_created',
            "Đánh giá khóa học: {$courseTitle} (course_id:{$courseId})",
            $courseId,
            $reviewId,
            $createdAt
        );
    }

    /**
     * Award points for discussion (+2 XP, capped at max 10 XP daily from discussion).
     */
    public function awardDiscussionPoints(int $userId, int $courseId, int $discussionId, mixed $createdAt = null): void
    {
        $timestamp = $createdAt ? Carbon::parse($createdAt) : now();

        $alreadyAwarded = UserPoint::where('user_id', $userId)
            ->where('source', 'discussion_created')
            ->where('reference_id', $discussionId)
            ->exists();

        if ($alreadyAwarded) {
            return;
        }

        $todayEarned = UserPoint::where('user_id', $userId)
            ->where('source', 'discussion_created')
            ->whereDate('created_at', $timestamp->toDateString())
            ->sum('points');

        if ($todayEarned >= 10) {
            return;
        }

        $this->awardPoints(
            $userId,
            2,
            'discussion_created',
            "Thảo luận bài học (discussion_id:{$discussionId})",
            $courseId,
            $discussionId,
            $timestamp
        );
    }

    /**
     * Award daily login.
     */
    public function awardDailyLoginPoints(int $userId): void
    {
        $this->checkAndAwardStreak($userId);
    }

    /**
     * Calculate user learning streak and award milestone bonuses.
     */
    public function checkAndAwardStreak(int $userId): void
    {
        $streakDays = $this->getUserStreakDays($userId);

        if ($streakDays >= 3) {
            $this->awardStreakMilestone($userId, 3, 20, 'streak_bonus_3', 'Streak 3 ngày liên tiếp');
        }
        if ($streakDays >= 7) {
            $this->awardStreakMilestone($userId, 7, 50, 'streak_bonus_7', 'Streak 7 ngày liên tiếp');
        }
        if ($streakDays >= 30) {
            $this->awardStreakMilestone($userId, 30, 150, 'streak_bonus_30', 'Streak 30 ngày liên tiếp');
        }
    }

    protected function awardStreakMilestone(int $userId, int $daysMilestone, int $points, string $source, string $desc): void
    {
        $recentlyAwarded = UserPoint::where('user_id', $userId)
            ->where('source', $source)
            ->where('created_at', '>=', now()->subDays($daysMilestone - 1)->startOfDay())
            ->exists();

        if (!$recentlyAwarded) {
            $this->awardPoints(
                $userId,
                $points,
                $source,
                "Đạt mốc {$desc}",
                null,
                $daysMilestone
            );
        }
    }

    /**
     * Calculate user streak days.
     * Counts the total number of unique days where the user logged in AND studied at least 1 lesson/quiz.
     * Days without learning activity do not break the streak count; learning on a new day continues to increment it.
     */
    public function getUserStreakDays(int $userId): int
    {
        // 1. Collect dates where the user actually had lesson/quiz learning activity
        $lessonDates = DB::table('lesson_progress')
            ->where('user_id', $userId)
            ->whereNotNull('updated_at')
            ->selectRaw('DATE(updated_at) as activity_date')
            ->pluck('activity_date');

        $lessonCompletedDates = DB::table('lesson_progress')
            ->where('user_id', $userId)
            ->whereNotNull('completed_at')
            ->selectRaw('DATE(completed_at) as activity_date')
            ->pluck('activity_date');

        $quizDates = DB::table('quiz_attempts')
            ->where('user_id', $userId)
            ->whereNotNull('created_at')
            ->selectRaw('DATE(created_at) as activity_date')
            ->pluck('activity_date');

        $learningPointDates = DB::table('user_points')
            ->where('user_id', $userId)
            ->whereIn('source', [
                'lesson_completed',
                'lesson_progress',
                'quiz_completed',
                'quiz_score_bonus_90',
                'quiz_score_bonus_80',
                'course_completed',
                'assignment_submitted',
            ])
            ->whereNotNull('created_at')
            ->selectRaw('DATE(created_at) as activity_date')
            ->pluck('activity_date');

        // Combine all valid unique learning activity dates
        $learningDates = $lessonDates
            ->concat($lessonCompletedDates)
            ->concat($quizDates)
            ->concat($learningPointDates)
            ->filter()
            ->map(fn($d) => Carbon::parse($d)->toDateString())
            ->unique();

        return $learningDates->count();
    }

    /**
     * Get Total XP for a user (all-time, never reset).
     */
    public function getUserTotalPoints(int $userId): int
    {
        return (int) UserPoint::where('user_id', $userId)->sum('points');
    }

    /**
     * Get Weekly XP for a user (Mon 00:00:00 to Sun 23:59:59).
     */
    public function getUserWeeklyPoints(int $userId): int
    {
        return (int) UserPoint::where('user_id', $userId)
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('points');
    }

    /**
     * Get Monthly XP for a user (1st to last day of current month).
     */
    public function getUserMonthlyPoints(int $userId): int
    {
        return (int) UserPoint::where('user_id', $userId)
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('points');
    }

    /**
     * Get user rank for a given period ('week', 'month', 'all').
     */
    public function getUserRank(int $userId, string $period = 'week'): int
    {
        $userPoints = match ($period) {
            'week' => $this->getUserWeeklyPoints($userId),
            'month' => $this->getUserMonthlyPoints($userId),
            default => $this->getUserTotalPoints($userId),
        };

        $dateConstraint = match ($period) {
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            default => null,
        };

        $higherUsersCount = DB::table('user_points')
            ->select('user_id', DB::raw('SUM(points) as period_points'))
            ->when($dateConstraint, fn($q) => $q->where('created_at', '>=', $dateConstraint))
            ->groupBy('user_id')
            ->having('period_points', '>', $userPoints)
            ->get()
            ->count();

        return $higherUsersCount + 1;
    }
}
