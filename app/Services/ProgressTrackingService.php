<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * ProgressService
 * 
 * Service class để xử lý logic liên quan đến tiến độ học
 * Giúp tái sử dụng code và dễ bảo trì
 * 
 * Sử dụng:
 * $service = new ProgressService();
 * $progress = $service->getCourseProgress($userId, $courseId);
 */
class ProgressService
{
    /**
     * Lấy tiến độ của user cho một course
     * 
     * @param int $userId
     * @param int $courseId
     * @return array
     */
    public function getCourseProgress(int $userId, int $courseId): array
    {
        $course = Course::with('lessons')->find($courseId);
        if (!$course) {
            return [
                'success' => false,
                'message' => 'Khóa học không tồn tại'
            ];
        }

        // Kiểm tra user đã đăng ký
        $enrollment = Enrollment::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->first();

        if (!$enrollment) {
            return [
                'success' => false,
                'message' => 'User chưa đăng ký khóa học này'
            ];
        }

        $lessons = $course->lessons;
        $totalLessons = $lessons->count();

        if ($totalLessons === 0) {
            return [
                'success' => true,
                'data' => [
                    'total_lessons' => 0,
                    'completed_lessons' => 0,
                    'progress_percent' => 0,
                    'lessons' => []
                ]
            ];
        }

        // Lấy danh sách progress
        $userProgress = LessonProgress::where('user_id', $userId)
            ->whereIn('lesson_id', $lessons->pluck('id'))
            ->pluck('is_completed', 'lesson_id')
            ->toArray();

        $completedLessons = count(array_filter($userProgress, fn ($c) => $c === true));
        $progressPercent = ($completedLessons / $totalLessons) * 100;

        return [
            'success' => true,
            'data' => [
                'course_id' => $courseId,
                'total_lessons' => $totalLessons,
                'completed_lessons' => $completedLessons,
                'progress_percent' => round($progressPercent, 2),
                'lessons' => $lessons->map(function ($lesson) use ($userProgress) {
                    return [
                        'id' => $lesson->id,
                        'title' => $lesson->title,
                        'is_completed' => $userProgress[$lesson->id] ?? false
                    ];
                })->values()
            ]
        ];
    }

    /**
     * Kiểm tra user đã hoàn thành course chưa
     * 
     * @param int $userId
     * @param int $courseId
     * @return bool
     */
    public function isCourseCompleted(int $userId, int $courseId): bool
    {
        $course = Course::find($courseId);
        if (!$course) {
            return false;
        }

        $lessons = $course->lessons;
        $totalLessons = $lessons->count();

        if ($totalLessons === 0) {
            return false;
        }

        $completedLessons = LessonProgress::where('user_id', $userId)
            ->whereIn('lesson_id', $lessons->pluck('id'))
            ->where('is_completed', true)
            ->count();

        return $completedLessons === $totalLessons;
    }

    /**
     * Lấy số phút xem của user cho một course
     * 
     * @param int $userId
     * @param int $courseId
     * @return int Số phút
     */
    public function getCourseWatchTime(int $userId, int $courseId): int
    {
        $course = Course::find($courseId);
        if (!$course) {
            return 0;
        }

        $totalSeconds = LessonProgress::where('user_id', $userId)
            ->whereIn('lesson_id', $course->lessons->pluck('id'))
            ->sum('watched_seconds');

        return intdiv($totalSeconds, 60);
    }

    /**
     * Lấy tất cả courses mà user chưa hoàn thành
     * 
     * @param int $userId
     * @return Collection
     */
    public function getIncompleteEnrollments(int $userId): Collection
    {
        return Enrollment::where('user_id', $userId)
            ->whereNull('completed_at')
            ->with('course.lessons')
            ->get();
    }

    /**
     * Lấy tất cả courses mà user đã hoàn thành
     * 
     * @param int $userId
     * @return Collection
     */
    public function getCompletedEnrollments(int $userId): Collection
    {
        return Enrollment::where('user_id', $userId)
            ->whereNotNull('completed_at')
            ->with('course.lessons')
            ->get();
    }

    /**
     * Reset tiến độ của user cho một course
     * (Admin function)
     * 
     * @param int $userId
     * @param int $courseId
     * @return bool
     */
    public function resetCourseProgress(int $userId, int $courseId): bool
    {
        $course = Course::find($courseId);
        if (!$course) {
            return false;
        }

        LessonProgress::where('user_id', $userId)
            ->whereIn('lesson_id', $course->lessons->pluck('id'))
            ->delete();

        // Reset enrollment
        Enrollment::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->update([
                'progress_percent' => 0,
                'completed_at' => null
            ]);

        return true;
    }

    /**
     * Lấy số lessons mà user đã xem
     * 
     * @param int $userId
     * @param int $courseId
     * @return int
     */
    public function getViewedLessonsCount(int $userId, int $courseId): int
    {
        $course = Course::find($courseId);
        if (!$course) {
            return 0;
        }

        return LessonProgress::where('user_id', $userId)
            ->whereIn('lesson_id', $course->lessons->pluck('id'))
            ->where('watched_seconds', '>', 0)
            ->count();
    }

    /**
     * Lấy danh sách lessons của user cần review
     * (Lessons mà user đã xem 80%+ nhưng chưa hoàn thành)
     * 
     * @param int $userId
     * @param int $courseId
     * @return Collection
     */
    public function getReviewNeededLessons(int $userId, int $courseId): Collection
    {
        $course = Course::with('lessons')->find($courseId);
        if (!$course) {
            return collect([]);
        }

        return $course->lessons
            ->filter(function ($lesson) use ($userId) {
                $progress = LessonProgress::where('user_id', $userId)
                    ->where('lesson_id', $lesson->id)
                    ->first();

                if (!$progress || $progress->is_completed) {
                    return false;
                }

                $watchPercent = $lesson->duration_seconds > 0
                    ? ($progress->watched_seconds / $lesson->duration_seconds) * 100
                    : 0;

                return $watchPercent >= 80;
            });
    }

    /**
     * Tính average score của tất cả courses của user
     * 
     * @param int $userId
     * @return float
     */
    public function getUserAverageProgress(int $userId): float
    {
        $enrollments = Enrollment::where('user_id', $userId)->get();

        if ($enrollments->isEmpty()) {
            return 0;
        }

        $totalProgress = $enrollments->sum('progress_percent');
        return round($totalProgress / count($enrollments), 2);
    }

    /**
     * Lấy top 5 users học nhiều nhất trong course
     * 
     * @param int $courseId
     * @return Collection
     */
    public function getTopLearnersInCourse(int $courseId): Collection
    {
        return Enrollment::where('course_id', $courseId)
            ->with('user')
            ->orderByDesc('progress_percent')
            ->limit(5)
            ->get()
            ->map(fn ($enrollment) => [
                'user_id' => $enrollment->user->id,
                'user_name' => $enrollment->user->name,
                'progress' => $enrollment->progress_percent,
                'completed_at' => $enrollment->completed_at
            ]);
    }
}
