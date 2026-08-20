<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\VideoModeration;
use App\Services\GeminiService;
use App\Services\VideoFrameExtractor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AiModerationController extends Controller
{
    /**
     * Helper resolve Lesson model or draft ContentUpdate lesson and video path
     */
    private function resolveLessonAndVideoPath(string|int $lessonId): array
    {
        $useS3 = !empty(config('filesystems.disks.s3.key')) && !empty(config('filesystems.disks.s3.bucket'));

        if (str_starts_with((string)$lessonId, 'update_les_')) {
            $updateId = str_replace('update_les_', '', $lessonId);
            $update = \App\Models\ContentUpdate::find($updateId);
            if ($update) {
                $payload = $update->payload ?? [];
                $draftLesson = new Lesson([
                    'title' => $payload['title'] ?? 'Bài học nháp',
                    'type' => $payload['type'] ?? 'video',
                    'video_path' => $payload['video_path'] ?? null,
                    'original_video_key' => $payload['original_video_key'] ?? null,
                    'hls_manifest_key' => $payload['hls_manifest_key'] ?? null,
                ]);
                $draftLesson->id = $lessonId;
                return [$draftLesson, $payload['video_path'] ?? $payload['hls_manifest_key'] ?? $payload['original_video_key'] ?? null];
            }
        }

        $lesson = Lesson::find($lessonId);
        if ($lesson) {
            // Check if there is a pending or draft ContentUpdate for this lesson override
            $pendingUpdate = \App\Models\ContentUpdate::where('entity_id', $lesson->id)
                ->where('type', \App\Models\ContentUpdate::TYPE_LESSON)
                ->whereIn('status', [\App\Models\ContentUpdate::STATUS_DRAFT, \App\Models\ContentUpdate::STATUS_PENDING, \App\Models\ContentUpdate::STATUS_REJECTED])
                ->latest()
                ->first();

            if ($pendingUpdate) {
                $p = $pendingUpdate->payload ?? [];
                if (!empty($p['original_video_key']) || !empty($p['hls_manifest_key']) || !empty($p['video_path'])) {
                    return [$lesson, $p['video_path'] ?? $p['hls_manifest_key'] ?? $p['original_video_key']];
                }
            }

            return [$lesson, $lesson->video_path ?: ($lesson->hls_manifest_key ?: $lesson->original_video_key)];
        }

        // Fallback check if $lessonId is a numeric ContentUpdate ID
        $update = \App\Models\ContentUpdate::find($lessonId);
        if ($update && $update->type === \App\Models\ContentUpdate::TYPE_LESSON) {
            $payload = $update->payload ?? [];
            $draftLesson = new Lesson([
                'title' => $payload['title'] ?? 'Bài học nháp',
                'type' => $payload['type'] ?? 'video',
                'video_path' => $payload['video_path'] ?? null,
                'original_video_key' => $payload['original_video_key'] ?? null,
                'hls_manifest_key' => $payload['hls_manifest_key'] ?? null,
            ]);
            $draftLesson->id = 'update_les_' . $update->id;
            return [$draftLesson, $payload['video_path'] ?? $payload['hls_manifest_key'] ?? $payload['original_video_key'] ?? null];
        }

        return [null, null];
    }

    /**
     * Stream video bài học với hỗ trợ HTTP Range requests hoặc S3 stream.
     */
    public function streamVideo(Request $request, string|int $lessonId)
    {
        [$lesson, $videoPath] = $this->resolveLessonAndVideoPath($lessonId);

        if (empty($videoPath)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Bài học này không có video.'], 404);
            }
            abort(404, 'Bài học này không có video.');
        }

        // 1. Kiểm tra S3
        $useS3 = !empty(config('filesystems.disks.s3.key')) && !empty(config('filesystems.disks.s3.bucket'));
        if ($useS3) {
            $s3Key = $lesson?->original_video_key ?: $videoPath;
            if (\Illuminate\Support\Facades\Storage::disk('s3')->exists($s3Key)) {
                $stream = \Illuminate\Support\Facades\Storage::disk('s3')->readStream($s3Key);
                if ($stream) {
                    return response()->stream(function () use ($stream) {
                        fpassthru($stream);
                        if (is_resource($stream)) {
                            fclose($stream);
                        }
                    }, 200, [
                        'Content-Type' => 'video/mp4',
                        'Cache-Control' => 'no-store',
                    ]);
                }
            }
        }

        // 2. Local Disk
        $path = null;
        if (\Illuminate\Support\Facades\Storage::disk('local')->exists($videoPath)) {
            $path = \Illuminate\Support\Facades\Storage::disk('local')->path($videoPath);
        } elseif (\Illuminate\Support\Facades\Storage::disk('public')->exists($videoPath)) {
            $path = \Illuminate\Support\Facades\Storage::disk('public')->path($videoPath);
        }

        if (!$path || !file_exists($path)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'File video không tồn tại trên máy chủ.'], 404);
            }
            abort(404, 'File video không tồn tại trên máy chủ.');
        }

        return response()->file($path, [
            'Content-Type'  => 'video/mp4',
            'Cache-Control' => 'no-store',
        ]);
    }

    /**
     * Stream HLS Playlist for Admin & Instructor Review.
     * Tự động sinh Signed URL trực tiếp từ S3 cho từng segment .ts,
     * loại bỏ hoàn toàn việc proxy dữ liệu video qua PHP giúp phát ngay tức thì (< 1-3 giây).
     */
    public function streamHlsPlaylist(string|int $lessonId)
    {
        [$lesson, $videoPath] = $this->resolveLessonAndVideoPath($lessonId);

        $isUpdate = str_starts_with((string)$lessonId, 'update_les_');
        $rawId = $isUpdate ? str_replace('update_les_', '', $lessonId) : $lessonId;

        $cacheKey = 'hls_signed_playlist_' . $lessonId;
        $useS3 = !empty(config('filesystems.disks.s3.key')) && !empty(config('filesystems.disks.s3.bucket'));

        // Kiểm tra cache đã có playlist đã ký S3 chưa
        if ($useS3 && \Illuminate\Support\Facades\Cache::has($cacheKey)) {
            return response(\Illuminate\Support\Facades\Cache::get($cacheKey), 200, [
                'Content-Type' => 'application/vnd.apple.mpegurl; charset=utf-8',
                'Access-Control-Allow-Origin' => '*',
                'Cache-Control' => 'public, max-age=1800',
            ]);
        }

        $content = null;

        // 1. Kiểm tra S3
        if ($useS3) {
            $s3PlaylistKey = $isUpdate
                ? 'hls/updates/' . $rawId . '/playlist.m3u8'
                : 'hls/lessons/' . $rawId . '/playlist.m3u8';
            $s3MasterKey = $isUpdate
                ? 'hls/updates/' . $rawId . '/master.m3u8'
                : 'hls/lessons/' . $rawId . '/master.m3u8';

            $targetKey = null;
            try {
                $content = \Illuminate\Support\Facades\Storage::disk('s3')->get($s3PlaylistKey);
                $targetKey = $s3PlaylistKey;
            } catch (\Throwable) {
                try {
                    $content = \Illuminate\Support\Facades\Storage::disk('s3')->get($s3MasterKey);
                    $targetKey = $s3MasterKey;
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
                    if ($trimmed !== '' && !str_starts_with($trimmed, '#')) {
                        // Nếu là file segment .ts hoặc playlist con .m3u8
                        $segmentKey = $s3Dir . '/' . $trimmed;
                        try {
                            $signedUrl = \Illuminate\Support\Facades\Storage::disk('s3')->temporaryUrl($segmentKey, $expiresAt);
                            $signedLines[] = $signedUrl;
                        } catch (\Throwable $e) {
                            $signedLines[] = $line;
                        }
                    } else {
                        $signedLines[] = $line;
                    }
                }

                $signedContent = implode("\n", $signedLines);

                // Cache 6 giờ để tăng tốc tức thì
                \Illuminate\Support\Facades\Cache::put($cacheKey, $signedContent, now()->addHours(6));

                return response($signedContent, 200, [
                    'Content-Type' => 'application/vnd.apple.mpegurl; charset=utf-8',
                    'Access-Control-Allow-Origin' => '*',
                    'Cache-Control' => 'public, max-age=1800',
                ]);
            }
        }

        // 2. Fallback: Local
        if ($content === null) {
            $localDir = $isUpdate
                ? 'lesson-hls/update_' . $rawId
                : 'lesson-hls/' . $rawId;
            $localPlaylist = $localDir . '/playlist.m3u8';
            $localMaster = $localDir . '/master.m3u8';

            if (\Illuminate\Support\Facades\Storage::disk('local')->exists($localPlaylist)) {
                $content = \Illuminate\Support\Facades\Storage::disk('local')->get($localPlaylist);
            } elseif (\Illuminate\Support\Facades\Storage::disk('local')->exists($localMaster)) {
                $content = \Illuminate\Support\Facades\Storage::disk('local')->get($localMaster);
            }
        }

        if ($content === null) {
            abort(404, 'HLS Playlist not found.');
        }

        return response($content, 200, [
            'Content-Type' => 'application/vnd.apple.mpegurl; charset=utf-8',
            'Access-Control-Allow-Origin' => '*',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }

    /**
     * Stream HLS Segment (.ts) fallback for Local or direct redirect
     */
    public function streamHlsSegment(string|int $lessonId, $segment)
    {
        $isUpdate = str_starts_with((string)$lessonId, 'update_les_');
        $rawId = $isUpdate ? str_replace('update_les_', '', $lessonId) : $lessonId;

        // 1. Kiểm tra S3 -> Redirect trực tiếp đến S3 Signed URL thay vì proxy qua PHP
        $useS3 = !empty(config('filesystems.disks.s3.key')) && !empty(config('filesystems.disks.s3.bucket'));
        if ($useS3) {
            $s3SegmentKey = $isUpdate
                ? 'hls/updates/' . $rawId . '/' . $segment
                : 'hls/lessons/' . $rawId . '/' . $segment;

            if (\Illuminate\Support\Facades\Storage::disk('s3')->exists($s3SegmentKey)) {
                $signedUrl = \Illuminate\Support\Facades\Storage::disk('s3')->temporaryUrl($s3SegmentKey, now()->addHours(2));
                return redirect()->away($signedUrl);
            }
        }

        // 2. Fallback: Local
        $localSegment = $isUpdate
            ? 'lesson-hls/update_' . $rawId . '/' . $segment
            : 'lesson-hls/' . $rawId . '/' . $segment;

        if (\Illuminate\Support\Facades\Storage::disk('local')->exists($localSegment)) {
            $path = \Illuminate\Support\Facades\Storage::disk('local')->path($localSegment);
            return response()->file($path, [
                'Content-Type' => 'video/mp2t',
                'Access-Control-Allow-Origin' => '*',
                'Cache-Control' => 'public, max-age=3600',
            ]);
        }

        abort(404, 'Segment not found.');
    }

    /**
     * Bước 1: Cắt frame từ video của Lesson và trả về danh sách file để frontend xử lý.
     */
    public function extractFrames(string|int $lessonId, VideoFrameExtractor $extractor)
    {
        [$lesson, $videoPathRel] = $this->resolveLessonAndVideoPath($lessonId);

        if (!$videoPathRel) {
            return response()->json(['error' => 'Bài học này không có video hợp lệ.'], 400);
        }

        $videoPath = null;
        if (\Illuminate\Support\Facades\Storage::disk('local')->exists($videoPathRel)) {
            $videoPath = \Illuminate\Support\Facades\Storage::disk('local')->path($videoPathRel);
        } elseif (\Illuminate\Support\Facades\Storage::disk('public')->exists($videoPathRel)) {
            $videoPath = \Illuminate\Support\Facades\Storage::disk('public')->path($videoPathRel);
        }

        if (!$videoPath || !file_exists($videoPath)) {
            return response()->json(['error' => 'File video không tồn tại trên máy chủ.'], 404);
        }

        try {
            $frames = $extractor->extract($videoPath, 300, $lessonId);

            if (empty($frames)) {
                return response()->json(['error' => 'Không thể trích xuất hình ảnh từ video này.'], 422);
            }

            return response()->json([
                'frames' => $frames,
                'total' => count($frames),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Lỗi khi cắt frame: '.$e->getMessage()], 500);
        }
    }

    /**
     * Bước 2: Phân tích 1 frame duy nhất bằng AI.
     */
    public function analyzeFrame(Request $request, GeminiService $gemini)
    {
        $request->validate([
            'frame_path' => 'required|string',
            'timestamp' => 'required|numeric',
        ]);

        $framePath = $request->input('frame_path');
        $timestamp = $request->input('timestamp');

        if (! file_exists($framePath)) {
            return response()->json(['error' => 'Frame không tồn tại.'], 404);
        }

        $result = $gemini->analyzeImage($framePath);

        if (! isset($result['error'])) {
            $result['timestamp'] = $timestamp;
            $result['frame_path'] = $framePath;
        }

        return response()->json($result);
    }

    /**
     * Bước 3: Tổng hợp kết quả từ frontend, lưu DB và xóa ảnh rác.
     */
    public function saveResults(Request $request, string|int $lessonId)
    {
        $validated = $request->validate([
            'results' => 'present|array',
        ]);

        $results = $validated['results'];

        if (count($results) === 0) {
            return response()->json([
                'error' => 'Không có kết quả phân tích nào. API AI có thể đã hết quota hoặc tất cả frame đều thất bại.',
            ], 422);
        }

        $violence = false;
        $adult = false;
        $weapon = false;
        $tiktok_logo = false;
        $youtube_logo = false;
        $watermark = false;

        $copyrightRisk = 'none';
        $summary = '';
        $maxRiskValue = 0;
        $riskLevels = ['none' => 0, 'low' => 1, 'medium' => 2, 'high' => 3];

        foreach ($results as $result) {
            if (! empty($result['violence'])) $violence = true;
            if (! empty($result['adult'])) $adult = true;
            if (! empty($result['weapon'])) $weapon = true;
            if (! empty($result['tiktok_logo'])) $tiktok_logo = true;
            if (! empty($result['youtube_logo'])) $youtube_logo = true;
            if (! empty($result['watermark'])) $watermark = true;

            $currentRiskStr = strtolower($result['copyright_risk'] ?? 'none');
            $currentRiskValue = $riskLevels[$currentRiskStr] ?? 0;

            if ($currentRiskValue > $maxRiskValue) {
                $maxRiskValue = $currentRiskValue;
                $copyrightRisk = $currentRiskStr;
                $summary = $result['summary'] ?? '';
            } elseif ($currentRiskValue === $maxRiskValue && empty($summary)) {
                $summary = $result['summary'] ?? '';
            }
        }

        if (empty($summary)) {
            $signs = [];
            if ($tiktok_logo) $signs[] = 'logo TikTok';
            if ($youtube_logo) $signs[] = 'logo YouTube';
            if ($watermark) $signs[] = 'watermark';
            if ($violence) $signs[] = 'nội dung bạo lực';
            if ($adult) $signs[] = 'nội dung người lớn';
            if ($weapon) $signs[] = 'vũ khí';

            if (!empty($signs)) {
                $summary = 'AI phát hiện dấu hiệu cần kiểm tra: ' . implode(', ', $signs) . '. Gợi ý: Có thể chỉ là video minh họa, admin nên xem lại trước khi quyết định.';
            }
        }

        $isUpdate = str_starts_with((string)$lessonId, 'update_les_');
        $updateId = $isUpdate ? str_replace('update_les_', '', $lessonId) : null;
        $update = $updateId ? \App\Models\ContentUpdate::find($updateId) : null;
        $realLesson = (!$isUpdate && !$update) ? Lesson::find($lessonId) : null;

        $moderationData = [
            'violence' => $violence,
            'adult' => $adult,
            'weapon' => $weapon,
            'tiktok_logo' => $tiktok_logo,
            'youtube_logo' => $youtube_logo,
            'watermark' => $watermark,
            'copyright_risk' => $copyrightRisk,
            'summary' => $summary,
            'details' => $results,
        ];

        if ($realLesson) {
            $moderation = VideoModeration::updateOrCreate(
                ['lesson_id' => $realLesson->id],
                $moderationData
            );
        } else {
            $moderation = $moderationData;
        }

        if ($update) {
            $payload = $update->payload ?? [];
            $payload['ai_moderation'] = $moderationData;
            $update->payload = $payload;
            $update->save();
        }

        $lessonDir = storage_path('app/temp_frames/lesson_'.$lessonId);
        if (File::exists($lessonDir)) {
            File::deleteDirectory($lessonDir);
        }

        return response()->json([
            'success' => true,
            'moderation' => $moderation,
        ]);
    }
}
