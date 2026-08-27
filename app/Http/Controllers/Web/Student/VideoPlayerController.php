<?php

namespace App\Http\Controllers\Web\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\VideoAccessLog;
use App\Models\VideoWatchHistory;
use App\Services\LearningProgressService;
use App\Services\SecurityAlertService;
use App\Services\VideoTokenService;
use hisorange\BrowserDetect\Parser as Browser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

 // If using hisorange/browser-detect, otherwise basic parsing

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
        $course = $this->courseForLesson($lesson);
        $hasAccess = $course && Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->withLearningAccess()
            ->exists();

        if (! $hasAccess && ! $lesson->is_preview && ! $user->isAdmin() && (! $course || (int) $course->instructor_id !== (int) $user->id)) {
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
     * Lấy playlist m3u8 (master.m3u8 hoặc playlist.m3u8)
     */
    public function playlist(Request $request, Lesson $lesson)
    {
        $token = $request->query('token');

        if (! $token || ! $this->tokenService->verifyToken($token, $lesson->id)) {
            $this->alertService->logAlert('TOKEN_INVALID', null, [
                'token' => $token,
                'lesson_id' => $lesson->id,
            ]);

            return response('Not found', 404, ['Access-Control-Allow-Origin' => '*']);
        }

        if ($lesson->isProcessing() && ! $lesson->isHlsReady()) {
            return response('Video is processing', 404, [
                'Access-Control-Allow-Origin' => '*',
                'X-Video-Status' => 'processing',
            ]);
        }

        $content = null;
        $isPlaylistRequest = str_ends_with($request->path(), 'playlist.m3u8');
        $useS3 = ! empty(config('filesystems.disks.s3.key')) && ! empty(config('filesystems.disks.s3.bucket'));
        $cacheKey = 'hls_signed_playlist_student_'.$lesson->id.'_'.($isPlaylistRequest ? 'playlist' : 'master');

        // 1. Kiểm tra S3 HLS Manifest với Direct Signed URLs
        if ($useS3) {
            if (Cache::has($cacheKey)) {
                return response(Cache::get($cacheKey), 200, [
                    'Content-Type' => 'application/vnd.apple.mpegurl; charset=utf-8',
                    'Access-Control-Allow-Origin' => '*',
                    'Cache-Control' => 'public, max-age=1800',
                ]);
            }

            try {
                $s3ManifestKey = $lesson->hls_manifest_key ?: ('hls/lessons/'.$lesson->id.'/master.m3u8');
                $s3PlaylistKey = 'hls/lessons/'.$lesson->id.'/playlist.m3u8';
                $targetKey = null;

                try {
                    $content = Storage::disk('s3')->get($s3PlaylistKey);
                    $targetKey = $s3PlaylistKey;
                } catch (\Throwable) {
                    try {
                        $content = Storage::disk('s3')->get($s3ManifestKey);
                        $targetKey = $s3ManifestKey;
                    } catch (\Throwable) {
                        $content = null;
                    }
                }

                if ($content !== null && $targetKey !== null) {
                    $s3Dir = dirname($targetKey);
                    $lines = explode("\n", $content);
                    $signedLines = [];
                    $expiresAt = now()->addHours(12);

                    foreach ($lines as $line) {
                        $trimmed = trim($line);
                        if ($trimmed !== '' && ! str_starts_with($trimmed, '#')) {
                            if (str_ends_with($trimmed, '.ts')) {
                                // Ký trực tiếp S3 URL cho file segment .ts
                                $segmentKey = $s3Dir.'/'.$trimmed;
                                try {
                                    $signedLines[] = Storage::disk('s3')->temporaryUrl($segmentKey, $expiresAt);
                                } catch (\Throwable $e) {
                                    $separator = str_contains($line, '?') ? '&' : '?';
                                    $signedLines[] = $line.$separator.'token='.urlencode($token);
                                }
                            } else {
                                // Nếu là file con .m3u8
                                $separator = str_contains($line, '?') ? '&' : '?';
                                $signedLines[] = $line.$separator.'token='.urlencode($token);
                            }
                        } else {
                            $signedLines[] = $line;
                        }
                    }

                    $signedContent = implode("\n", $signedLines);
                    Cache::put($cacheKey, $signedContent, now()->addHours(6));

                    return response($signedContent, 200, [
                        'Content-Type' => 'application/vnd.apple.mpegurl; charset=utf-8',
                        'Access-Control-Allow-Origin' => '*',
                        'Cache-Control' => 'public, max-age=1800',
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning('S3 HLS read error: '.$e->getMessage());
            }
        }

        // 2. Fallback: Local Disk
        if ($content === null) {
            $hlsDir = 'lesson-hls/'.$lesson->id;
            $localMaster = $hlsDir.'/master.m3u8';
            $localPlaylist = $hlsDir.'/playlist.m3u8';

            if ($isPlaylistRequest) {
                if (Storage::disk('local')->exists($localPlaylist)) {
                    $content = Storage::disk('local')->get($localPlaylist);
                } elseif (Storage::disk('local')->exists($localMaster)) {
                    $content = Storage::disk('local')->get($localMaster);
                }
            } else {
                if (Storage::disk('local')->exists($localMaster)) {
                    $content = Storage::disk('local')->get($localMaster);
                } elseif (Storage::disk('local')->exists($localPlaylist)) {
                    $content = Storage::disk('local')->get($localPlaylist);
                }
            }
        }

        if ($content === null) {
            return response('Not found', 404, ['Access-Control-Allow-Origin' => '*']);
        }

        // Chèn token vào các dòng URI (.m3u8 và .ts) cho local disk
        $lines = explode("\n", $content);
        foreach ($lines as &$line) {
            $line = trim($line);
            if ($line && ! str_starts_with($line, '#')) {
                $line .= '?token='.urlencode($token);
            } elseif (str_contains($line, 'URI=')) {
                $line = preg_replace('/URI="([^"]+)"/', 'URI="$1?token='.urlencode($token).'"', $line);
            }
        }
        $modifiedContent = implode("\n", $lines);

        return response($modifiedContent, 200, [
            'Content-Type' => 'application/vnd.apple.mpegurl; charset=utf-8',
            'Access-Control-Allow-Origin' => '*',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }

    /**
     * Trả về file enc.key cho HLS AES-128
     */
    public function key(Request $request, Lesson $lesson)
    {
        $token = $request->query('token');

        if (! $token || ! $this->tokenService->verifyToken($token, $lesson->id)) {
            return response('Not found', 404);
        }

        $keyPath = 'lesson-hls/'.$lesson->id.'/enc.key';

        if (! Storage::disk('local')->exists($keyPath)) {
            return response('Not found', 404);
        }

        $content = Storage::disk('local')->get($keyPath);

        return response($content, 200, [
            'Content-Type' => 'application/octet-stream',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }

    /**
     * Trả về file .ts
     */
    public function segment(Request $request, Lesson $lesson, $segment)
    {
        $token = $request->query('token');

        if (! $token || ! $this->tokenService->verifyToken($token, $lesson->id)) {
            return response('Not found', 404, ['Access-Control-Allow-Origin' => '*']);
        }

        // 1. Kiểm tra S3 -> Redirect trực tiếp đến S3 Signed URL
        $useS3 = ! empty(config('filesystems.disks.s3.key')) && ! empty(config('filesystems.disks.s3.bucket'));
        if ($useS3) {
            try {
                $s3SegmentKey = 'hls/lessons/'.$lesson->id.'/'.$segment;
                if (Storage::disk('s3')->exists($s3SegmentKey)) {
                    $signedUrl = Storage::disk('s3')->temporaryUrl($s3SegmentKey, now()->addHours(2));

                    return redirect()->away($signedUrl);
                }
            } catch (\Throwable $e) {
                Log::warning('S3 segment read error: '.$e->getMessage());
            }
        }

        // 2. Fallback: Local Disk
        $segmentPath = 'lesson-hls/'.$lesson->id.'/'.$segment;
        if (Storage::disk('local')->exists($segmentPath)) {
            $path = Storage::disk('local')->path($segmentPath);

            return response()->file($path, [
                'Content-Type' => 'video/mp2t',
                'Access-Control-Allow-Origin' => '*',
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }

        return response('Not found', 404, ['Access-Control-Allow-Origin' => '*']);
    }

    /**
     * Cập nhật tiến trình xem video (được gọi từ client mỗi 10s)
     */
    public function updateProgress(Request $request, Lesson $lesson, LearningProgressService $progressService)
    {
        $user = $request->user();
        $course = $this->courseForLesson($lesson);
        abort_unless($course, 404);

        $validated = $request->validate([
            'current_time' => ['nullable', 'integer', 'min:0'],
            'last_position_seconds' => ['nullable', 'integer', 'min:0'],
            'furthest_position_seconds' => ['nullable', 'integer', 'min:0'],
            'played_seconds' => ['nullable', 'integer', 'min:0', 'max:86400'],
            'video_duration_seconds' => ['nullable', 'numeric', 'min:0'],
            'client_updated_at' => ['nullable', 'date'],
        ]);

        $currentTime = (int) ($validated['last_position_seconds'] ?? $validated['current_time'] ?? 0);
        $progress = $progressService->recordVideoProgress($user->id, $course, $lesson, [
            'last_position_seconds' => $currentTime,
            'furthest_position_seconds' => $validated['furthest_position_seconds'] ?? $currentTime,
            'played_seconds' => $validated['played_seconds'] ?? 0,
            'video_duration_seconds' => $validated['video_duration_seconds'] ?? null,
            'client_updated_at' => $validated['client_updated_at'] ?? null,
        ]);

        if ($progress['stale'] ?? false) {
            return response()->json($progress, 409);
        }

        // Cập nhật hoặc tạo mới history
        VideoWatchHistory::updateOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id],
            [
                'course_id' => $course->id,
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
                'watch_duration' => max(0, now()->timestamp - $log->watch_started_at->timestamp),
            ]);
        }

        return response()->json($progress);
    }

    /**
     * Lấy tiến trình đang xem (current_time)
     */
    public function getProgress(Request $request, Lesson $lesson)
    {
        $user = $request->user();
        $progress = LessonProgress::query()
            ->where('user_id', $user->id)
            ->where('lesson_id', $lesson->id)
            ->first();

        $history = VideoWatchHistory::where('user_id', $user->id)
            ->where('lesson_id', $lesson->id)
            ->first();

        return response()->json([
            'current_time' => (int) ($progress?->last_position_seconds ?? $history?->current_time ?? 0),
            'last_position_seconds' => (int) ($progress?->last_position_seconds ?? $history?->current_time ?? 0),
            'furthest_position_seconds' => (int) ($progress?->furthest_position_seconds ?? 0),
            'watched_seconds' => (int) ($progress?->watched_seconds ?? 0),
            'progress_percent' => (float) ($progress?->progress_percent ?? 0),
            'is_completed' => (bool) ($progress?->is_completed ?? false),
        ]);
    }

    private function courseForLesson(Lesson $lesson): ?Course
    {
        return $lesson->course
            ?? $lesson->section?->course
            ?? $lesson->chapter?->course;
    }

    private function getBrowserName($userAgent)
    {
        if (strpos($userAgent, 'Firefox') !== false) {
            return 'Firefox';
        }
        if (strpos($userAgent, 'Chrome') !== false) {
            return 'Chrome';
        }
        if (strpos($userAgent, 'Safari') !== false) {
            return 'Safari';
        }
        if (strpos($userAgent, 'Edge') !== false) {
            return 'Edge';
        }

        return 'Unknown';
    }

    private function getPlatformName($userAgent)
    {
        if (strpos($userAgent, 'Windows') !== false) {
            return 'Windows';
        }
        if (strpos($userAgent, 'Mac') !== false) {
            return 'macOS';
        }
        if (strpos($userAgent, 'Linux') !== false) {
            return 'Linux';
        }
        if (strpos($userAgent, 'Android') !== false) {
            return 'Android';
        }
        if (strpos($userAgent, 'iPhone') !== false || strpos($userAgent, 'iPad') !== false) {
            return 'iOS';
        }

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
