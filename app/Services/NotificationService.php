<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\PushNotification;
use App\Models\User;
use Illuminate\Support\Collection;

class NotificationService
{
    public function send(User $user, string $title, string $message, ?string $type = null, ?string $url = null): PushNotification
    {
        return PushNotification::create([
            'user_id' => $user->id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'url' => $url,
        ]);
    }

    /**
     * @param  Collection<int, User>|array<int, User>  $users
     */
    public function sendToMany(Collection|array $users, string $title, string $message, ?string $type = null, ?string $url = null): int
    {
        $users = $users instanceof Collection ? $users : collect($users);
        $now = now();
        $rows = $users
            ->unique('id')
            ->map(fn (User $user) => [
                'user_id' => $user->id,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'url' => $url,
                'is_read' => false,
                'read_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ])
            ->values()
            ->all();

        if ($rows === []) {
            return 0;
        }

        PushNotification::insert($rows);

        return count($rows);
    }

    public function sendByAudience(string $audience, string $title, string $message, ?string $url = null, ?int $courseId = null): int
    {
        $users = match ($audience) {
            'students' => User::query()->where('role', 'student')->where('is_active', true)->get(),
            'instructors' => User::query()->where('role', 'instructor')->where('is_active', true)->get(),
            'students_instructors' => User::query()->whereIn('role', ['student', 'instructor'])->where('is_active', true)->get(),
            'course' => $this->usersForCourse($courseId),
            default => User::query()->where('is_active', true)->get(),
        };

        return $this->sendToMany($users, $title, $message, 'announcement', $url);
    }

    public function unreadCount(User $user): int
    {
        return $user->pushNotifications()->where('is_read', false)->count();
    }

    public function markAsRead(PushNotification $notification, User $user): void
    {
        abort_unless($notification->user_id === $user->id, 403);

        $notification->markAsRead();
    }

    public function markAllAsRead(User $user): int
    {
        return $user->pushNotifications()
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Gửi thông báo đến toàn bộ học viên đang ghi danh (active) trong khóa học.
     * Không gửi cho Giảng viên hoặc Admin.
     */
    public function notifyEnrolledStudents(Course $course, string $title, string $message, string $type, ?string $url = null): int
    {
        $studentIds = Enrollment::query()
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->pluck('user_id');

        if ($studentIds->isEmpty()) {
            return 0;
        }

        $students = User::query()
            ->whereIn('id', $studentIds)
            ->where('role', 'student')
            ->where('id', '!=', $course->instructor_id)
            ->where('is_active', true)
            ->get();

        return $this->sendToMany($students, $title, $message, $type, $url);
    }

    /**
     * Bắn thông báo khi khóa học có bài học / video mới được phê duyệt.
     */
    public function notifyCourseLessonCreated(Course $course, \App\Models\Lesson $lesson): int
    {
        $title = 'Khóa học có bài học mới';
        $message = "Khóa học {$course->title} vừa có bài học mới: {$lesson->title}.";
        $url = route('courses.lessons.show', ['course' => $course, 'lesson' => $lesson]);

        return $this->notifyEnrolledStudents($course, $title, $message, 'course_lesson_created', $url);
    }

    /**
     * Bắn thông báo khi bài học / video được cập nhật nội dung và được phê duyệt.
     */
    public function notifyCourseLessonUpdated(Course $course, \App\Models\Lesson $lesson, bool $isVideoUpdated = false): int
    {
        $title = 'Bài học vừa được cập nhật';
        $message = $isVideoUpdated
            ? "Video bài học {$lesson->title} trong khóa học {$course->title} vừa được cập nhật."
            : "Bài học {$lesson->title} trong khóa học {$course->title} vừa được cập nhật.";
        $type = $isVideoUpdated ? 'course_video_updated' : 'course_lesson_updated';
        $url = route('courses.lessons.show', ['course' => $course, 'lesson' => $lesson]);

        return $this->notifyEnrolledStudents($course, $title, $message, $type, $url);
    }

    /**
     * @return Collection<int, User>
     */
    private function usersForCourse(?int $courseId): Collection
    {
        if (! $courseId) {
            return collect();
        }

        $course = Course::find($courseId);

        if (! $course) {
            return collect();
        }

        $studentIds = Enrollment::query()
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->pluck('user_id');

        return User::query()
            ->where(function ($query) use ($course, $studentIds) {
                $query->where('id', $course->instructor_id)
                    ->orWhereIn('id', $studentIds);
            })
            ->where('is_active', true)
            ->get();
    }
}
