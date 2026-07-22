<?php

use App\Http\Controllers\Api\ProgressController;
use Illuminate\Support\Facades\Route;

/**
 * API Routes
 *
 * Tất cả routes trong file này sẽ có prefix: /api
 * Xem app/Http/Kernel.php hoặc routes/web.php để biết cách kích hoạt
 */
Route::middleware(['web', 'auth'])->group(function () {
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

    // HLS Token API (Requires Auth & Enrolled)
    // Rate limit: 20 tokens per minute
    Route::middleware('throttle:20,1')->get('/video/{lesson}/token', [\App\Http\Controllers\Web\Student\VideoPlayerController::class, 'getToken'])
        ->name('video.token');

    // Update Progress API (Called every 10s by frontend)
    // Rate limit: 20 requests per minute
    Route::middleware('throttle:20,1')->post('/video/{lesson}/progress', [\App\Http\Controllers\Web\Student\VideoPlayerController::class, 'updateProgress'])
        ->name('video.progress');

    // Get Progress API
    Route::get('/video/{lesson}/progress', [\App\Http\Controllers\Web\Student\VideoPlayerController::class, 'getProgress'])
        ->name('video.progress.get');
});

// Session check endpoint (Called every 10-15s by frontend)
// Bọc trong middleware web để SingleSessionMiddleware có thể bắt được session id.
Route::middleware(['web'])->get('/session/check', function (Illuminate\Http\Request $request) {
    if (!Illuminate\Support\Facades\Auth::check()) {
        return response()->json(['active' => false, 'message' => 'Chưa đăng nhập.']);
    }
    // SingleSessionMiddleware will intercept and return active=false if invalid.
    // If it reaches here, the session is active.
    return response()->json(['active' => true]);
})->name('session.check');

// HLS Streaming API (No auth:sanctum required, protected by ?token=)
// Rate limit: 300 requests per minute to allow downloading many .ts segments
Route::middleware('throttle:300,1')->group(function () {
    Route::get('/video/hls/{lesson}/playlist.m3u8', [\App\Http\Controllers\Web\Student\VideoPlayerController::class, 'playlist'])
        ->name('video.hls.playlist');
        
    Route::get('/video/hls/{lesson}/{segment}', [\App\Http\Controllers\Web\Student\VideoPlayerController::class, 'segment'])
        ->where('segment', '.*\.ts$')
        ->name('video.hls.segment');
});
