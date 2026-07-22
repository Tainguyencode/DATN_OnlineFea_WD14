<?php

namespace App\Http\Controllers\Web\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\VideoAccessLog;
use App\Models\VideoWatchHistory;
use App\Services\SecurityAlertService;
use App\Services\VideoTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use hisorange\BrowserDetect\Parser as Browser; // If using hisorange/browser-detect, otherwise basic parsing

class VideoPlayerController extends Controller
{
    public function __construct(
        private VideoTokenService $tokenService,
        private SecurityAlertService $alertService
    ) {}

    /**
     * Tạo token để xem video
     */
    public function getToken(Request $request, Lesson $lesson)
    {
        $user = $request->user();

        // Kiểm tra quyền truy cập (đã mua khóa học)
        $hasAccess = Enrollment::where('user_id', $user->id)
            ->where('course_id', $lesson->course_id)
            ->withLearningAccess()
            ->exists();

        if (!$hasAccess && !$lesson->is_preview && !$user->hasRole('admin') && $lesson->course->instructor_id !== $user->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $token = $this->tokenService->generateToken($user->id, $lesson->id);

        // Ghi log truy cập video (tạo log mới nếu chưa có trong session xem hiện tại)
        VideoAccessLog::create([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'browser' => $this->getBrowserName($request->userAgent()),
            'platform' => $this->getPlatformName($request->userAgent()),
            'device' => $this->getDeviceType($request->userAgent()),
            'watch_started_at' => now(),
        ]);

        return response()->json(['token' => $token]);
    }

    /**
     * Lấy playlist m3u8
     */
    public function playlist(Request $request, Lesson $lesson)
    {
        $token = $request->query('token');

        if (!$token || !$this->tokenService->verifyToken($token, $lesson->id)) {
            $this->alertService->logAlert('TOKEN_INVALID', null, [
                'token' => $token,
                'lesson_id' => $lesson->id
            ]);
            abort(403, 'Invalid or expired token.');
        }

        $hlsDir = 'lesson-hls/' . $lesson->id;
        $m3u8Path = $hlsDir . '/playlist.m3u8';

        if (!Storage::disk('local')->exists($m3u8Path)) {
            abort(404, 'Playlist not found.');
        }

        $content = Storage::disk('local')->get($m3u8Path);

        // Chèn token vào các file .ts
        $lines = explode("\n", $content);
        foreach ($lines as &$line) {
            $line = trim($line);
            if ($line && !str_starts_with($line, '#')) {
                $line .= '?token=' . urlencode($token);
            }
        }
        $modifiedContent = implode("\n", $lines);

        return response($modifiedContent, 200, [
            'Content-Type' => 'application/vnd.apple.mpegurl',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }

    /**
     * Trả về file .ts
     */
    public function segment(Request $request, Lesson $lesson, $segment)
    {
        $token = $request->query('token');

        if (!$token || !$this->tokenService->verifyToken($token, $lesson->id)) {
            abort(403, 'Invalid or expired token.');
        }

        $segmentPath = 'lesson-hls/' . $lesson->id . '/' . $segment;

        if (!Storage::disk('local')->exists($segmentPath)) {
            abort(404, 'Segment not found.');
        }

        $path = Storage::disk('local')->path($segmentPath);

        return response()->file($path, [
            'Content-Type' => 'video/MP2T',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * Cập nhật tiến trình xem video (được gọi từ client mỗi 10s)
     */
    public function updateProgress(Request $request, Lesson $lesson)
    {
        $user = $request->user();
        $currentTime = $request->input('current_time', 0);
        
        // Cập nhật hoặc tạo mới history
        VideoWatchHistory::updateOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id],
            [
                'course_id' => $lesson->course_id,
                'current_time' => $currentTime,
            ]
        );

        // Update video access log watch_ended_at and duration
        $log = VideoAccessLog::where('user_id', $user->id)
            ->where('lesson_id', $lesson->id)
            ->orderByDesc('id')
            ->first();
            
        if ($log && $log->watch_started_at) {
            $log->update([
                'watch_ended_at' => now(),
                'watch_duration' => now()->diffInSeconds($log->watch_started_at)
            ]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Lấy tiến trình đang xem (current_time)
     */
    public function getProgress(Request $request, Lesson $lesson)
    {
        $user = $request->user();
        $history = VideoWatchHistory::where('user_id', $user->id)
            ->where('lesson_id', $lesson->id)
            ->first();

        return response()->json([
            'current_time' => $history ? $history->current_time : 0
        ]);
    }

    private function getBrowserName($userAgent)
    {
        if (strpos($userAgent, 'Firefox') !== false) return 'Firefox';
        if (strpos($userAgent, 'Chrome') !== false) return 'Chrome';
        if (strpos($userAgent, 'Safari') !== false) return 'Safari';
        if (strpos($userAgent, 'Edge') !== false) return 'Edge';
        return 'Unknown';
    }

    private function getPlatformName($userAgent)
    {
        if (strpos($userAgent, 'Windows') !== false) return 'Windows';
        if (strpos($userAgent, 'Mac') !== false) return 'macOS';
        if (strpos($userAgent, 'Linux') !== false) return 'Linux';
        if (strpos($userAgent, 'Android') !== false) return 'Android';
        if (strpos($userAgent, 'iPhone') !== false || strpos($userAgent, 'iPad') !== false) return 'iOS';
        return 'Unknown';
    }

    private function getDeviceType($userAgent)
    {
        if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', strtolower($userAgent))) {
            return 'Tablet';
        }
        if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', strtolower($userAgent))) {
            return 'Mobile';
        }
        return 'Desktop';
    }
}
