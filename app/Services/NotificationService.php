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
