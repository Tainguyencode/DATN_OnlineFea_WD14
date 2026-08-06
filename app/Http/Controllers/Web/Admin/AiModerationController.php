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
        $lesson = Lesson::find($lessonId);
        if ($lesson) {
            return [$lesson, $lesson->video_path];
        }

        if (str_starts_with((string)$lessonId, 'update_les_')) {
            $updateId = str_replace('update_les_', '', $lessonId);
            $update = \App\Models\ContentUpdate::find($updateId);
            if ($update) {
                $payload = $update->payload ?? [];
                $draftLesson = new Lesson([
                    'title' => $payload['title'] ?? 'Bài học nháp',
                    'type' => $payload['type'] ?? 'video',
                    'video_path' => $payload['video_path'] ?? null,
                ]);
                $draftLesson->id = $lessonId;
                return [$draftLesson, $payload['video_path'] ?? null];
            }
        }

        return [null, null];
    }

    /**
     * Stream video bài học với hỗ trợ HTTP Range requests (cho phép seek).
     * Chỉ dùng cho trang Admin Review – giúp admin nhảy đến đoạn AI phát hiện.
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
     * Stream HLS Playlist for Admin Review
     */
    public function streamHlsPlaylist(string|int $lessonId)
    {
        [$lesson, $videoPath] = $this->resolveLessonAndVideoPath($lessonId);

        if (!$videoPath) {
            $videoPath = str_starts_with((string)$lessonId, 'update_les_')
                ? 'lesson-hls/update_' . str_replace('update_les_', '', $lessonId) . '/playlist.m3u8'
                : 'lesson-hls/' . $lessonId . '/playlist.m3u8';
        }

        $m3u8Path = \Illuminate\Support\Str::endsWith($videoPath, 'playlist.m3u8')
            ? $videoPath
            : dirname($videoPath) . '/playlist.m3u8';

        if (!\Illuminate\Support\Facades\Storage::disk('local')->exists($m3u8Path)) {
            abort(404, 'HLS Playlist not found.');
        }

        $content = \Illuminate\Support\Facades\Storage::disk('local')->get($m3u8Path);

        return response($content, 200, [
            'Content-Type' => 'application/vnd.apple.mpegurl',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }

    /**
     * Stream HLS Segment (.ts) for Admin Review
     */
    public function streamHlsSegment(string|int $lessonId, $segment)
    {
        [$lesson, $videoPath] = $this->resolveLessonAndVideoPath($lessonId);

        if ($videoPath) {
            $hlsDir = \Illuminate\Support\Str::endsWith($videoPath, 'playlist.m3u8') ? dirname($videoPath) : $videoPath;
            $segmentPath = $hlsDir . '/' . $segment;
        } else {
            $segmentPath = str_starts_with((string)$lessonId, 'update_les_')
                ? 'lesson-hls/update_' . str_replace('update_les_', '', $lessonId) . '/' . $segment
                : 'lesson-hls/' . $lessonId . '/' . $segment;
        }

        if (!\Illuminate\Support\Facades\Storage::disk('local')->exists($segmentPath)) {
            abort(404, 'Segment not found.');
        }

        $path = \Illuminate\Support\Facades\Storage::disk('local')->path($segmentPath);

        return response()->file($path, [
            'Content-Type' => 'video/MP2T',
            'Cache-Control' => 'no-store',
        ]);
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
            $frames = $extractor->extract($videoPath, 30, $lessonId);

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

        $realLesson = Lesson::find($lessonId);
        $update = null;

        if (!$realLesson && str_starts_with((string)$lessonId, 'update_les_')) {
            $updateId = str_replace('update_les_', '', $lessonId);
            $update = \App\Models\ContentUpdate::find($updateId);
        } elseif (!$realLesson) {
            $update = \App\Models\ContentUpdate::find($lessonId);
        }

        if (!$realLesson && $update && $update->entity_id) {
            $realLesson = Lesson::find($update->entity_id);
        }

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
