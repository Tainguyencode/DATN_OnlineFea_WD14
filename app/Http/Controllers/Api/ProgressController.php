<?php

namespace App\Http\Controllers\Api;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Services\LearningProgressService;
use App\Services\LessonNoteAccessService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * ProgressController
 *
 * Quản lý tiến độ học của user
 * - Đánh dấu lesson hoàn thành
 * - Lấy tiến độ khóa học
 * - Lấy danh sách lessons với trạng thái
 */
class ProgressController
{
    /**
     * Đánh dấu lesson là hoàn thành
     *
     * @return JsonResponse
     *
     * Các bước:
     * 1. Kiểm tra user đã đăng nhập
     * 2. Lấy thông tin lesson
     * 3. Kiểm tra user đã đăng ký khóa học chứa lesson này chưa
     * 4. Cập nhật hoặc tạo mới lesson_progress
     * 5. Nếu tất cả lessons hoàn thành → cập nhật enrollment (completed_at)
     */
    public function markLessonComplete(Request $request, int $lessonId): JsonResponse
    {
        // 1. Kiểm tra user đã đăng nhập
        $user = Auth::user();
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để thực hiện hành động này',
            ], 401);
        }

        abort_unless($user->isStudent(), 403);

        // 2. Lấy thông tin lesson
        $lesson = Lesson::find($lessonId);
        if (! $lesson) {
            return response()->json([
                'success' => false,
                'message' => 'Bài học không tồn tại',
            ], 404);
        }

        $access = app(LessonNoteAccessService::class);
        $courseId = $access->lessonCourseId($lesson);
        abort_unless($courseId, 404);
        $course = Course::findOrFail($courseId);
        $access->assertCanUse($user, $course, $lesson);

        // Video, quiz và bài tập phải hoàn thành qua luồng chuyên biệt để không thể bỏ qua
        // thời lượng xem, kết quả quiz hoặc điểm chấm bài.
        abort_unless($lesson->type === Lesson::TYPE_DOCUMENT, 422, 'Loại bài học này không thể đánh dấu hoàn thành thủ công.');

        $result = app(LearningProgressService::class)->recordLessonProgress(
            $user->id,
            $course,
            $lesson,
            0,
            0,
            true
        );

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật tiến độ học thành công',
            'data' => [
                'lesson_id' => $lessonId,
                'is_completed' => true,
                'course_completed' => $result['course_completed'],
                'course_progress' => $result['course_progress'],
            ],
        ], 200);
    }

    /**
     * Lấy tiến độ của user cho một khóa học
     *
     * @return JsonResponse
     *
     * Trả về:
     * - Tổng số lessons
     * - Số lessons đã hoàn thành
     * - Phần trăm tiến độ (%)
     * - Danh sách các lesson với trạng thái
     */
    public function getCourseProgress(int $courseId): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập',
            ], 401);
        }

        // Lấy course
        $course = Course::with('lessons')->find($courseId);
        if (! $course) {
            return response()->json([
                'success' => false,
                'message' => 'Khóa học không tồn tại',
            ], 404);
        }

        // Kiểm tra user có đăng ký khóa học không
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->first();

        if (! $enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa đăng ký khóa học này',
            ], 403);
        }

        // Lấy tổng số lessons
        $lessons = $course->lessons;
        $totalLessons = $lessons->count();

        if ($totalLessons === 0) {
            return response()->json([
                'success' => true,
                'message' => 'Khóa học này không có bài học',
                'data' => [
                    'course_id' => $courseId,
                    'course_name' => $course->title,
                    'total_lessons' => 0,
                    'completed_lessons' => 0,
                    'progress_percent' => 0,
                    'lessons' => [],
                ],
            ], 200);
        }

        // Lấy danh sách progress của user cho tất cả lessons
        $userProgress = LessonProgress::where('user_id', $user->id)
            ->whereIn('lesson_id', $lessons->pluck('id'))
            ->pluck('is_completed', 'lesson_id')
            ->toArray();

        // Tính số lessons đã hoàn thành
        $completedLessons = count(array_filter($userProgress, fn ($completed) => $completed === true));

        // Tính phần trăm tiến độ
        $progressPercent = ($completedLessons / $totalLessons) * 100;

        // Xây dựng danh sách lessons với trạng thái
        $lessonsList = $lessons->map(function ($lesson) use ($userProgress) {
            return [
                'id' => $lesson->id,
                'title' => $lesson->title,
                'type' => $lesson->type,
                'sort_order' => $lesson->sort_order,
                'is_completed' => $userProgress[$lesson->id] ?? false,
                'duration_seconds' => $lesson->duration_seconds ?? 0,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'message' => 'Lấy tiến độ học thành công',
            'data' => [
                'course_id' => $courseId,
                'course_name' => $course->title,
                'total_lessons' => $totalLessons,
                'completed_lessons' => $completedLessons,
                'progress_percent' => round($progressPercent, 2),
                'lessons' => $lessonsList,
                'enrollment' => [
                    'enrolled_at' => $enrollment->created_at,
                    'completed_at' => $enrollment->completed_at,
                    'status' => $enrollment->completed_at ? 'completed' : 'in_progress',
                ],
            ],
        ], 200);
    }

    /**
     * Lấy danh sách tất cả khóa học mà user đã đăng ký kèm tiến độ
     */
    public function getUserEnrollments(): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập',
            ], 401);
        }

        // Lấy tất cả enrollments của user với course info
        $enrollments = $user->enrollments()
            ->with('course.lessons')
            ->get()
            ->map(function ($enrollment) use ($user) {
                $course = $enrollment->course;
                $lessons = $course->lessons;
                $totalLessons = $lessons->count();

                // Lấy số lessons đã hoàn thành
                $completedLessons = LessonProgress::where('user_id', $user->id)
                    ->whereIn('lesson_id', $lessons->pluck('id'))
                    ->where('is_completed', true)
                    ->count();

                $progress = $totalLessons > 0 ? ($completedLessons / $totalLessons) * 100 : 0;

                return [
                    'enrollment_id' => $enrollment->id,
                    'course_id' => $course->id,
                    'course_title' => $course->title,
                    'course_thumbnail' => $course->thumbnail,
                    'instructor_name' => $course->instructor->name ?? 'Unknown',
                    'total_lessons' => $totalLessons,
                    'completed_lessons' => $completedLessons,
                    'progress_percent' => round($progress, 2),
                    'enrolled_at' => $enrollment->created_at,
                    'completed_at' => $enrollment->completed_at,
                    'status' => $enrollment->completed_at ? 'completed' : 'in_progress',
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách khóa học thành công',
            'data' => $enrollments,
        ], 200);
    }

    /**
     * Lấy chi tiết tiến độ của user cho một lesson cụ thể
     */
    public function getLessonProgress(int $lessonId): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập',
            ], 401);
        }

        $lesson = Lesson::find($lessonId);
        if (! $lesson) {
            return response()->json([
                'success' => false,
                'message' => 'Bài học không tồn tại',
            ], 404);
        }

        // Kiểm tra user có quyền xem lesson này không (đã đăng ký khóa học)
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $lesson->course_id)
            ->first();

        if (! $enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa có quyền xem bài học này',
            ], 403);
        }

        // Lấy progress của user cho lesson này
        $progress = LessonProgress::where('user_id', $user->id)
            ->where('lesson_id', $lessonId)
            ->first();

        if (! $progress) {
            // Chưa có progress, tạo record mới
            $progress = LessonProgress::create([
                'user_id' => $user->id,
                'lesson_id' => $lessonId,
                'is_completed' => false,
                'watched_seconds' => 0,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lấy tiến độ bài học thành công',
            'data' => [
                'lesson_id' => $lesson->id,
                'lesson_title' => $lesson->title,
                'lesson_type' => $lesson->type,
                'course_id' => $lesson->course_id,
                'course_title' => $lesson->course->title,
                'is_completed' => $progress->is_completed,
                'watched_seconds' => $progress->watched_seconds,
                'duration_seconds' => $lesson->duration_seconds ?? 0,
                'completed_at' => $progress->completed_at,
                'progress_percentage' => $lesson->duration_seconds > 0
                    ? round(($progress->watched_seconds / $lesson->duration_seconds) * 100, 2)
                    : 0,
            ],
        ], 200);
    }

    /**
     * Cập nhật số giây xem video (gọi định kỳ khi user xem video)
     */
    public function updateWatchedSeconds(Request $request, int $lessonId): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập',
            ], 401);
        }

        abort_unless($user->isStudent(), 403);

        $validated = $request->validate([
            'watched_seconds' => ['required', 'integer', 'min:0', 'max:86400'],
        ]);

        $lesson = Lesson::find($lessonId);
        if (! $lesson) {
            return response()->json([
                'success' => false,
                'message' => 'Bài học không tồn tại',
            ], 404);
        }

        abort_unless($lesson->type === Lesson::TYPE_VIDEO, 422);
        $access = app(LessonNoteAccessService::class);
        $courseId = $access->lessonCourseId($lesson);
        abort_unless($courseId, 404);
        $course = Course::findOrFail($courseId);
        $access->assertCanUse($user, $course, $lesson);

        $duration = $access->lessonDurationSeconds($lesson);
        abort_if($duration > 0 && $validated['watched_seconds'] > $duration, 422, 'Thời gian xem vượt quá thời lượng video.');

        $result = app(LearningProgressService::class)->recordVideoProgress($user->id, $course, $lesson, [
            'last_position_seconds' => $validated['watched_seconds'],
            'furthest_position_seconds' => $validated['watched_seconds'],
            'played_seconds' => 0,
            'duration_seconds' => $duration,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật tiến độ xem video thành công',
            'data' => [
                'lesson_id' => $lessonId,
                'watched_seconds' => $result['watched_seconds'],
                'duration_seconds' => $duration,
                'watch_percentage' => $duration > 0
                    ? round(($result['watched_seconds'] / $duration) * 100, 2)
                    : 0,
            ],
        ], 200);
    }
}
