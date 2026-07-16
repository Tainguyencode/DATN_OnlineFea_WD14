<?php

use App\Http\Controllers\Api\ProgressController;
use Illuminate\Support\Facades\Route;

/**
 * API Routes
 *
 * Tất cả routes trong file này sẽ có prefix: /api
 * Xem app/Http/Kernel.php hoặc routes/web.php để biết cách kích hoạt
 */
Route::middleware('auth:sanctum')->group(function () {
    // ============================================
    // LEARNING PROGRESS ROUTES
    // ============================================

    /**
     * POST /api/lesson/{id}/complete
     * Đánh dấu lesson là hoàn thành
     *
     * Body:
     * {
     *   "watched_seconds": 1200 (optional)
     * }
     */
    Route::post('/lesson/{id}/complete', [ProgressController::class, 'markLessonComplete'])
        ->name('lesson.complete');

    /**
     * GET /api/course/{id}/progress
     * Lấy tiến độ học của user cho một khóa học
     *
     * Response:
     * {
     *   "success": true,
     *   "data": {
     *     "course_id": 1,
     *     "course_name": "Laravel từ Zero đến Hero",
     *     "total_lessons": 5,
     *     "completed_lessons": 3,
     *     "progress_percent": 60,
     *     "lessons": [
     *       {
     *         "id": 1,
     *         "title": "Bài 1",
     *         "type": "video",
     *         "is_completed": true,
     *         "duration_seconds": 900
     *       }
     *     ]
     *   }
     * }
     */
    Route::get('/course/{id}/progress', [ProgressController::class, 'getCourseProgress'])
        ->name('course.progress');

    /**
     * GET /api/lesson/{id}/progress
     * Lấy chi tiết tiến độ của user cho một lesson cụ thể
     */
    Route::get('/lesson/{id}/progress', [ProgressController::class, 'getLessonProgress'])
        ->name('lesson.progress');

    /**
     * POST /api/lesson/{id}/watch
     * Cập nhật số giây xem video (gọi định kỳ)
     *
     * Body:
     * {
     *   "watched_seconds": 600
     * }
     */
    Route::post('/lesson/{id}/watch', [ProgressController::class, 'updateWatchedSeconds'])
        ->name('lesson.watch');

    /**
     * GET /api/my-enrollments
     * Lấy danh sách tất cả khóa học mà user đã đăng ký kèm tiến độ
     */
    Route::get('/my-enrollments', [ProgressController::class, 'getUserEnrollments'])
        ->name('user.enrollments');
});
