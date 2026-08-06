<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserPoint;
use App\Models\Badge;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\QuizAttempt;
use App\Models\Lesson;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PointService
{
    /**
     * Award points to a user.
     */
    public function awardPoints(int $userId, int $points, string $source, ?string $description = null, ?int $courseId = null): void
    {
        if ($points <= 0) {
            return;
        }

        DB::transaction(function () use ($userId, $points, $source, $description, $courseId) {
            // Create user point record
            UserPoint::create([
                'user_id' => $userId,
                'points' => $points,
                'type' => 'earn',
                'source' => $source,
                'description' => $description,
                'course_id' => $courseId,
            ]);

            // Check and award badges
            $this->checkAndAwardBadges($userId);
        });
    }

    /**
     * Check if user earned badges and unlock them.
     */
    public function checkAndAwardBadges(int $userId): void
    {
        $user = User::find($userId);
        if (!$user) {
            return;
        }
        
        // Sum total points of the user
        $totalPoints = UserPoint::where('user_id', $userId)->sum('points');

        // Get badges user doesn't have yet, but qualifies for
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

            // Create push notification for earning badge
            try {
                \App\Models\PushNotification::create([
                    'user_id' => $userId,
                    'title' => 'Nhận huy hiệu mới',
                    'message' => "Chúc mừng! Bạn đã đạt huy hiệu \"{$badge->name}\" nhờ tích lũy được {$badge->points_required} điểm.",
                    'type' => 'badge_earned',
                    'url' => route('leaderboard'),
                    'is_read' => false,
                ]);
            } catch (\Throwable $e) {
                // If route leaderboard is not defined yet, ignore or catch error
            }
        }
    }

    /**
     * Check daily login and award points.
     */
    public function awardDailyLoginPoints(int $userId): void
    {
        $today = Carbon::today();

        // Check if already awarded daily login today
        $alreadyAwarded = UserPoint::where('user_id', $userId)
            ->where('source', 'daily_login')
            ->whereDate('created_at', $today)
            ->exists();

        if (!$alreadyAwarded) {
            $this->awardPoints(
                $userId,
                5,
                'daily_login',
                'Đăng nhập mỗi ngày'
            );

            // Also check for 7-day learning streak
            $this->checkAndAwardStreak($userId);
        }
    }

    /**
     * Check and award 7-day learning streak bonus (+50 points).
     */
    public function checkAndAwardStreak(int $userId): void
    {
        // Check if streak was already awarded in the last 6 days
        $recentlyAwarded = UserPoint::where('user_id', $userId)
            ->where('source', 'learning_streak_7_days')
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->exists();

        if ($recentlyAwarded) {
            return;
        }

        // We check the last 7 calendar days (including today)
        $studyDays = collect();
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::today()->subDays($i);
            
            // Check if there was any progress update or completed lesson on this date
            $hasProgress = LessonProgress::where('user_id', $userId)
                ->where(function($q) use ($date) {
                    $q->whereDate('last_watched_at', $date)
                      ->orWhereDate('completed_at', $date)
                      ->orWhereDate('updated_at', $date);
                })
                ->exists();

            if ($hasProgress) {
                $studyDays->push($date->toDateString());
            }
        }

        // If the user has studied on 7 unique consecutive days
        if ($studyDays->unique()->count() === 7) {
            $this->awardPoints(
                $userId,
                50,
                'learning_streak_7_days',
                'Học tập liên tục 7 ngày'
            );
        }
    }

    /**
     * Award points for lesson completion.
     */
    public function awardLessonCompletionPoints(int $userId, int $lessonId): void
    {
        $lesson = Lesson::find($lessonId);
        if (!$lesson) {
            return;
        }

        $lessonTag = "lesson_id:{$lesson->id}";

        // Check if already awarded points for this lesson
        $alreadyAwarded = UserPoint::where('user_id', $userId)
            ->where('source', 'lesson_completed')
            ->where('description', 'like', "%{$lessonTag}%")
            ->exists();

        if ($alreadyAwarded) {
            return;
        }

        DB::transaction(function () use ($userId, $lesson, $lessonTag) {
            // Award +10 points for lesson completion
            $this->awardPoints(
                $userId,
                10,
                'lesson_completed',
                "Hoàn thành bài giảng: {$lesson->title} ({$lessonTag})",
                $lesson->course_id
            );

            // Check if all lessons in the same chapter are completed
            if ($lesson->chapter_id) {
                $chapter = $lesson->chapter;
                if ($chapter) {
                    $totalLessons = $chapter->lessons()->where('is_required', true)->count();
                    if ($totalLessons > 0) {
                        $completedLessons = LessonProgress::where('user_id', $userId)
                            ->whereIn('lesson_id', $chapter->lessons()->pluck('id'))
                            ->where('is_completed', true)
                            ->count();

                        if ($completedLessons === $totalLessons) {
                            $chapterTag = "chapter_id:{$chapter->id}";
                            $chapterAlreadyAwarded = UserPoint::where('user_id', $userId)
                                ->where('source', 'chapter_completed')
                                ->where('description', 'like', "%{$chapterTag}%")
                                ->exists();

                            if (!$chapterAlreadyAwarded) {
                                $this->awardPoints(
                                    $userId,
                                    30,
                                    'chapter_completed',
                                    "Hoàn thành chương học: {$chapter->title} ({$chapterTag})",
                                    $lesson->course_id
                                );
                            }
                        }
                    }
                }
            }

            // Check and award streak
            $this->checkAndAwardStreak($userId);
        });
    }

    /**
     * Award points for quiz performance.
     */
    public function awardQuizPoints(int $userId, \App\Models\Quiz $quiz, float $percent, int $courseId): void
    {
        $quizTag = "quiz_id:{$quiz->id}";
        
        $hasPerfect = UserPoint::where('user_id', $userId)
            ->where('source', 'quiz_passed_perfect')
            ->where('description', 'like', "%{$quizTag}%")
            ->exists();

        $hasHigh = UserPoint::where('user_id', $userId)
            ->where('source', 'quiz_passed_high')
            ->where('description', 'like', "%{$quizTag}%")
            ->exists();

        if ($percent == 100.0) {
            if (!$hasPerfect) {
                $pointsToAward = $hasHigh ? 20 : 40;
                $this->awardPoints(
                    $userId, 
                    $pointsToAward, 
                    'quiz_passed_perfect', 
                    "Đạt điểm tối đa Quiz: {$quiz->title} ({$quizTag})", 
                    $courseId
                );
            }
        } elseif ($percent >= 80.0) {
            if (!$hasHigh && !$hasPerfect) {
                $this->awardPoints(
                    $userId, 
                    20, 
                    'quiz_passed_high', 
                    "Vượt qua Quiz với điểm cao: {$quiz->title} ({$quizTag})", 
                    $courseId
                );
            }
        }

        // Check and award streak
        $this->checkAndAwardStreak($userId);
    }
}
