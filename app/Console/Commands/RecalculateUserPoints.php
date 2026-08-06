<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\UserPoint;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\QuizAttempt;
use App\Models\Discussion;
use App\Models\DiscussionReply;
use App\Models\Chapter;
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
    protected $description = 'Recalculate student points from historical learning activity';

    /**
     * Execute the console command.
     */
    public function handle(PointService $pointService)
    {
        $this->info('Starting points recalculation...');

        if ($this->option('fresh')) {
            $this->warn('Clearing existing points and badges...');
            DB::table('user_points')->truncate();
            DB::table('user_badges')->truncate();
        }

        // 1. Daily logins from activity logs
        $this->info('Recalculating daily logins...');
        $logins = DB::table('activity_logs')
            ->whereIn('action', ['login', 'admin_login'])
            ->orderBy('created_at', 'asc')
            ->get();

        foreach ($logins as $login) {
            $date = Carbon::parse($login->created_at)->toDateString();
            $alreadyAwarded = UserPoint::where('user_id', $login->user_id)
                ->where('source', 'daily_login')
                ->whereDate('created_at', $date)
                ->exists();

            if (!$alreadyAwarded) {
                UserPoint::create([
                    'user_id' => $login->user_id,
                    'points' => 5,
                    'type' => 'earn',
                    'source' => 'daily_login',
                    'description' => 'Đăng nhập mỗi ngày',
                    'created_at' => $login->created_at,
                    'updated_at' => $login->created_at,
                ]);
            }
        }

        // 2. Lesson completions (+10 points)
        $this->info('Recalculating lesson completions...');
        $progressRecords = LessonProgress::where('is_completed', true)->get();
        foreach ($progressRecords as $p) {
            $lesson = $p->lesson;
            if ($lesson) {
                $lessonTag = "lesson_id:{$lesson->id}";
                $alreadyAwarded = UserPoint::where('user_id', $p->user_id)
                    ->where('source', 'lesson_completed')
                    ->where('description', 'like', "%{$lessonTag}%")
                    ->exists();

                if (!$alreadyAwarded) {
                    UserPoint::create([
                        'user_id' => $p->user_id,
                        'points' => 10,
                        'type' => 'earn',
                        'source' => 'lesson_completed',
                        'description' => "Hoàn thành bài giảng: {$lesson->title} ({$lessonTag})",
                        'course_id' => $lesson->course_id,
                        'created_at' => $p->completed_at ?? $p->created_at,
                        'updated_at' => $p->completed_at ?? $p->updated_at,
                    ]);
                }
            }
        }

        // 3. Chapter completions (+30 points)
        $this->info('Recalculating chapter completions...');
        $chapters = Chapter::with('lessons')->get();
        foreach ($chapters as $chapter) {
            $lessonIds = $chapter->lessons->pluck('id');
            if ($lessonIds->isEmpty()) {
                continue;
            }

            $users = LessonProgress::whereIn('lesson_id', $lessonIds)
                ->where('is_completed', true)
                ->groupBy('user_id')
                ->havingRaw('COUNT(DISTINCT lesson_id) = ?', [$lessonIds->count()])
                ->pluck('user_id');

            foreach ($users as $userId) {
                $courseId = $chapter->lessons->first()->course_id;
                $chapterTag = "chapter_id:{$chapter->id}";
                
                $alreadyAwarded = UserPoint::where('user_id', $userId)
                    ->where('source', 'chapter_completed')
                    ->where('description', 'like', "%{$chapterTag}%")
                    ->exists();

                if (!$alreadyAwarded) {
                    UserPoint::create([
                        'user_id' => $userId,
                        'points' => 30,
                        'type' => 'earn',
                        'source' => 'chapter_completed',
                        'description' => "Hoàn thành chương học: {$chapter->title} ({$chapterTag})",
                        'course_id' => $courseId,
                        'created_at' => now(), // fallback to current date
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // 4. Course completions (+100 points)
        $this->info('Recalculating course completions...');
        $completedEnrollments = Enrollment::where('status', Enrollment::STATUS_COMPLETED)->get();
        foreach ($completedEnrollments as $enrollment) {
            $course = $enrollment->course;
            if ($course) {
                $courseTag = "course_id:{$course->id}";
                $alreadyAwarded = UserPoint::where('user_id', $enrollment->user_id)
                    ->where('source', 'course_completed')
                    ->where('description', 'like', "%{$courseTag}%")
                    ->exists();

                if (!$alreadyAwarded) {
                    UserPoint::create([
                        'user_id' => $enrollment->user_id,
                        'points' => 100,
                        'type' => 'earn',
                        'source' => 'course_completed',
                        'description' => "Hoàn thành khóa học: {$course->title} ({$courseTag})",
                        'course_id' => $enrollment->course_id,
                        'created_at' => $enrollment->completed_at ?? $enrollment->created_at,
                        'updated_at' => $enrollment->completed_at ?? $enrollment->updated_at,
                    ]);
                }
            }
        }

        // 5. Quiz attempts (pass score >= 80 -> 20pts, 100% -> 40pts)
        $this->info('Recalculating quiz attempts...');
        $quizAttempts = QuizAttempt::with('quiz.lesson')
            ->where('passed', true)
            ->whereNotNull('completed_at')
            ->orderBy('percent', 'asc') // perfect score overwrites high score
            ->get();

        foreach ($quizAttempts as $attempt) {
            if ($attempt->quiz) {
                $courseId = $attempt->quiz->lesson?->course_id;
                $pointService->awardQuizPoints(
                    $attempt->user_id,
                    $attempt->quiz,
                    (float)$attempt->percent,
                    $courseId ?? 0
                );
            }
        }

        // 6. Discussions (asked questions) (+10 points)
        $this->info('Recalculating discussions...');
        $discussions = Discussion::with('lesson')->get();
        foreach ($discussions as $disc) {
            $courseId = $disc->lesson?->course_id;
            $discTag = "discussion_id:{$disc->id}";
            $alreadyAwarded = UserPoint::where('user_id', $disc->user_id)
                ->where('source', 'ask_question')
                ->where('description', 'like', "%{$discTag}%")
                ->exists();

            if (!$alreadyAwarded) {
                UserPoint::create([
                    'user_id' => $disc->user_id,
                    'points' => 10,
                    'type' => 'earn',
                    'source' => 'ask_question',
                    'description' => "Đặt câu hỏi: {$disc->title} ({$discTag})",
                    'course_id' => $courseId,
                    'created_at' => $disc->created_at,
                    'updated_at' => $disc->updated_at,
                ]);
            }
        }

        // 7. Discussion replies marked as helpful (+20 points)
        $this->info('Recalculating helpful replies...');
        $replies = DiscussionReply::with('discussion.lesson')->where('is_helpful', true)->get();
        foreach ($replies as $reply) {
            $courseId = $reply->discussion?->lesson?->course_id;
            $replyTag = "reply_id:{$reply->id}";
            $alreadyAwarded = UserPoint::where('user_id', $reply->user_id)
                ->where('source', 'reply_marked_helpful')
                ->where('description', 'like', "%{$replyTag}%")
                ->exists();

            if (!$alreadyAwarded) {
                UserPoint::create([
                    'user_id' => $reply->user_id,
                    'points' => 20,
                    'type' => 'earn',
                    'source' => 'reply_marked_helpful',
                    'description' => "Câu trả lời được đánh dấu hữu ích trong thảo luận: {$reply->discussion?->title} ({$replyTag})",
                    'course_id' => $courseId,
                    'created_at' => $reply->created_at,
                    'updated_at' => $reply->updated_at,
                ]);
            }
        }

        // 8. Re-evaluate badges for all student users
        $this->info('Evaluating badges for all student users...');
        $students = User::where('role', 'student')->get();
        foreach ($students as $student) {
            $pointService->checkAndAwardBadges($student->id);
            // Also evaluate streak
            $pointService->checkAndAwardStreak($student->id);
        }

        $this->info('Recalculation complete!');
    }
}
