<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\UserPoint;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\QuizAttempt;
use App\Models\Discussion;
use App\Models\Review;
use App\Services\PointService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class RecalculateUserPoints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaderboard:recalculate {--fresh : Clear existing points and badges first}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate student XP points preserving original activity timestamps';

    /**
     * Execute the console command.
     */
    public function handle(PointService $pointService)
    {
        $this->info('Starting XP points recalculation (preserving historical timestamps)...');

        if ($this->option('fresh')) {
            $this->warn('Clearing existing points and badges...');
            DB::table('user_points')->truncate();
            DB::table('user_badges')->truncate();
        }

        // 1. Lesson completions (based on duration: 10/15/20 XP)
        $this->info('Recalculating lesson completions (based on duration: 10/15/20 XP)...');
        $progressRecords = LessonProgress::where('is_completed', true)->get();
        foreach ($progressRecords as $p) {
            $lesson = $p->lesson;
            if ($lesson) {
                $createdAt = $p->completed_at ?? $p->updated_at ?? $p->created_at;
                $pointService->awardLessonCompletionPoints($p->user_id, $lesson->id, $createdAt);
            }
        }

        // 2. Course completions (+50 XP)
        $this->info('Recalculating course completions (+50 XP)...');
        $completedEnrollments = Enrollment::where('status', Enrollment::STATUS_COMPLETED)->get();
        foreach ($completedEnrollments as $enrollment) {
            $course = $enrollment->course;
            if ($course) {
                $createdAt = $enrollment->completed_at ?? $enrollment->updated_at ?? $enrollment->created_at;
                $pointService->awardCourseCompletionPoints($enrollment->user_id, $course->id, $createdAt);
            }
        }

        // 3. Quiz attempts (+10 XP completion, +10 XP for >=80%, +20 XP for >=90%)
        $this->info('Recalculating quiz attempts (+10 XP completion, score bonus)...');
        $quizAttempts = QuizAttempt::with('quiz.lesson')
            ->whereNotNull('completed_at')
            ->orderBy('created_at', 'asc')
            ->get();

        foreach ($quizAttempts as $attempt) {
            if ($attempt->quiz) {
                $courseId = $attempt->quiz->lesson?->course_id ?? 0;
                $createdAt = $attempt->completed_at ?? $attempt->created_at;
                $pointService->awardQuizPoints(
                    $attempt->user_id,
                    $attempt->quiz,
                    (float) $attempt->percent,
                    $courseId,
                    $createdAt
                );
            }
        }

        // 4. Course Reviews (+5 XP)
        $this->info('Recalculating course reviews (+5 XP)...');
        $reviews = Review::whereNull('parent_id')->get();
        foreach ($reviews as $rev) {
            $pointService->awardReviewPoints($rev->user_id, $rev->course_id, $rev->id, $rev->created_at);
        }

        // 5. Discussions (+2 XP, max 10 XP/day)
        $this->info('Recalculating discussions (+2 XP, max 10 XP/day)...');
        $discussions = Discussion::with('lesson')->orderBy('created_at', 'asc')->get();
        foreach ($discussions as $disc) {
            $courseId = $disc->lesson?->course_id ?? 0;
            $pointService->awardDiscussionPoints($disc->user_id, $courseId, $disc->id, $disc->created_at);
        }

        // 6. Badges & Streaks
        $this->info('Evaluating badges and streaks for all students...');
        $students = User::where('role', 'student')->get();
        foreach ($students as $student) {
            $pointService->checkAndAwardBadges($student->id);
            $pointService->checkAndAwardStreak($student->id);
        }

        // Clean up any duplicate push notifications for badges
        try {
            DB::statement("DELETE n1 FROM push_notifications n1 INNER JOIN push_notifications n2 WHERE n1.id > n2.id AND n1.user_id = n2.user_id AND n1.type = 'badge_earned' AND n1.message = n2.message");
        } catch (\Throwable $e) {
            // Ignore if notification table structure varies
        }

        $this->info('XP points recalculation complete!');
    }
}
