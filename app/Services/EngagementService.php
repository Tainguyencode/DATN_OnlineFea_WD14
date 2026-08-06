<?php

namespace App\Services;

use App\Mail\LearningReminderMail;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EngagementService
{
    public function __construct(
        private NotificationService $notificationService,
        private LearningPlayerService $playerService
    ) {}

    /**
     * Cập nhật thời gian học gần nhất và reset stage về 0 khi user học lại
     */
    public function recordLearningActivity(User $user): void
    {
        if (! $user->exists) {
            return;
        }

        $user->update([
            'last_learning_at' => now(),
            'engagement_email_stage' => 0,
        ]);
    }

    /**
     * Chạy logic kiểm tra mốc và gửi nhắc nhở học tập
     */
    public function processReminders(): int
    {
        $stages = [
            ['days' => 3, 'current' => 0, 'target' => 1],
            ['days' => 7, 'current' => 1, 'target' => 2],
            ['days' => 14, 'current' => 2, 'target' => 3],
            ['days' => 30, 'current' => 3, 'target' => 4],
        ];

        $totalSent = 0;

        foreach ($stages as $stageInfo) {
            $threshold = now()->subDays($stageInfo['days']);

            $users = User::query()
                ->where('is_active', true)
                ->whereNotNull('last_learning_at')
                ->where('last_learning_at', '<', $threshold)
                ->where('engagement_email_stage', $stageInfo['current'])
                ->get();

            foreach ($users as $user) {
                $sent = $this->sendReminderForUser($user, $stageInfo['target']);
                if ($sent) {
                    $totalSent++;
                }
            }
        }

        return $totalSent;
    }

    /**
     * Gửi email và push notification cho 1 user
     */
    private function sendReminderForUser(User $user, int $targetStage): bool
    {
        return DB::transaction(function () use ($user, $targetStage) {
            $reminderData = $this->buildReminderContent($user);

            // Tạo Push Notification qua NotificationService
            $this->notificationService->send(
                $user,
                'Nhắc nhở học tập',
                $reminderData['message'],
                'learning_reminder',
                $reminderData['url']
            );

            // Gửi Mail nhắc nhở
            try {
                if ($user->email) {
                    Mail::to($user->email)->send(new LearningReminderMail(
                        $user,
                        $reminderData['message'],
                        $reminderData['course_title'],
                        $reminderData['url']
                    ));
                }
            } catch (\Throwable $e) {
                Log::error("Lỗi khi gửi email nhắc nhở học tập tới user ID {$user->id}: {$e->getMessage()}");
            }

            // Cập nhật stage và ngày gửi gần nhất
            $user->update([
                'engagement_email_stage' => $targetStage,
                'last_engagement_sent_at' => now(),
            ]);

            return true;
        });
    }

    /**
     * Xây dựng nội dung nhắc nhở dựa theo khóa học gần nhất và tiến độ của user
     *
     * @return array{message: string, course_title: ?string, url: ?string}
     */
    public function buildReminderContent(User $user): array
    {
        /** @var Enrollment|null $enrollment */
        $enrollment = Enrollment::query()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->orderByDesc('last_accessed_at')
            ->orderByDesc('updated_at')
            ->first();

        if (! $enrollment) {
            $enrollment = Enrollment::query()
                ->where('user_id', $user->id)
                ->orderByDesc('last_accessed_at')
                ->first();
        }

        if (! $enrollment || ! $enrollment->course) {
            return [
                'message' => 'Hãy tiếp tục bài học tiếp theo. Chỉ cần 15 phút hôm nay cũng giúp bạn tiến bộ.',
                'course_title' => null,
                'url' => route('courses.index'),
            ];
        }

        $course = $enrollment->course;
        $progress = (float) $enrollment->progress_percent;
        $url = null;
        $message = '';

        if ($progress >= 80.0) {
            $message = 'Bạn chỉ còn vài bài nữa là hoàn thành khóa học và nhận chứng chỉ.';
            $url = $this->findNextLessonUrl($user, $course) ?? $course->learningEntryUrl() ?? route('courses.show', $course->slug);
        } elseif ($progress < 20.0) {
            $message = 'Hãy tiếp tục bài học tiếp theo. Chỉ cần 15 phút hôm nay cũng giúp bạn tiến bộ.';
            $url = $this->findNextLessonUrl($user, $course) ?? $course->learningEntryUrl() ?? route('courses.show', $course->slug);
        } else {
            $nextLesson = $this->findNextUncompletedLesson($user, $course);
            if ($nextLesson) {
                $message = "Bài học tiếp theo của bạn là: {$nextLesson->title}.";
                $url = route('courses.lessons.show', [$course, $nextLesson]);
            } else {
                $message = "Bài học tiếp theo của bạn đang chờ đón trong khóa học.";
                $url = $course->learningEntryUrl() ?? route('courses.show', $course->slug);
            }
        }

        return [
            'message' => $message,
            'course_title' => $course->title,
            'url' => $url,
        ];
    }

    /**
     * Tìm bài học chưa hoàn thành tiếp theo trong khóa học
     */
    private function findNextUncompletedLesson(User $user, $course): ?Lesson
    {
        $sections = $this->playerService->curriculumSections($course);
        $orderedLessons = $this->playerService->orderedLessons($sections);

        if ($orderedLessons->isEmpty()) {
            return null;
        }

        $completedLessonIds = LessonProgress::query()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('is_completed', true)
            ->pluck('lesson_id')
            ->all();

        foreach ($orderedLessons as $lesson) {
            if (! in_array($lesson->id, $completedLessonIds, true)) {
                return $lesson;
            }
        }

        return null;
    }

    private function findNextLessonUrl(User $user, $course): ?string
    {
        $nextLesson = $this->findNextUncompletedLesson($user, $course);
        if ($nextLesson) {
            return route('courses.lessons.show', [$course, $nextLesson]);
        }

        return null;
    }
}
