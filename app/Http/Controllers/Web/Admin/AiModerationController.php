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
     * Stream video bài học với hỗ trợ HTTP Range requests (cho phép seek).
     * Chỉ dùng cho trang Admin Review – giúp admin nhảy đến đoạn AI phát hiện.
     */
    public function streamVideo(Lesson $lesson)
    {
        if (empty($lesson->video_path)) {
            abort(404, 'Bài học này không có video.');
        }

        $path = null;
        if (\Illuminate\Support\Facades\Storage::disk('local')->exists($lesson->video_path)) {
            $path = \Illuminate\Support\Facades\Storage::disk('local')->path($lesson->video_path);
        } elseif (\Illuminate\Support\Facades\Storage::disk('public')->exists($lesson->video_path)) {
            $path = \Illuminate\Support\Facades\Storage::disk('public')->path($lesson->video_path);
        }

        if (!$path || !file_exists($path)) {
            abort(404, 'File video không tồn tại trên máy chủ.');
        }

        // response()->file() tự động xử lý HTTP Range requests
        // → browser có thể seek video đúng cách (seekable range đầy đủ)
        return response()->file($path, [
            'Content-Type'  => 'video/mp4',
            'Cache-Control' => 'no-store',
        ]);
    }

    /**
     * Stream HLS Playlist for Admin Review
     */
    public function streamHlsPlaylist(Lesson $lesson)
    {
        $hlsDir = 'lesson-hls/' . $lesson->id;
        $m3u8Path = $hlsDir . '/playlist.m3u8';

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
    public function streamHlsSegment(Lesson $lesson, $segment)
    {
        $segmentPath = 'lesson-hls/' . $lesson->id . '/' . $segment;

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
    public function extractFrames(Lesson $lesson, VideoFrameExtractor $extractor)
    {
        if ($lesson->type !== 'video' || empty($lesson->video_path)) {
            return response()->json(['error' => 'Bài học này không có video hợp lệ.'], 400);
        }

        $videoPath = null;
        if (\Illuminate\Support\Facades\Storage::disk('local')->exists($lesson->video_path)) {
            $videoPath = \Illuminate\Support\Facades\Storage::disk('local')->path($lesson->video_path);
        } elseif (\Illuminate\Support\Facades\Storage::disk('public')->exists($lesson->video_path)) {
            $videoPath = \Illuminate\Support\Facades\Storage::disk('public')->path($lesson->video_path);
        }

        if (!$videoPath || !file_exists($videoPath)) {
            return response()->json(['error' => 'File video không tồn tại trên máy chủ.'], 404);
        }

        try {
            // Cắt frame mỗi 30s, truyền vào lesson_id để lưu riêng thư mục
            $frames = $extractor->extract($videoPath, 30, $lesson->id);

            return response()->json([
                'frames' => $frames,
                'total' => count($frames),
            ]);
        } catch (\Exception $e) {
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

        // Gắn thêm thông tin thời điểm để front-end dễ gom
        if (! isset($result['error'])) {
            $result['timestamp'] = $timestamp;
            $result['frame_path'] = $framePath;
        }

        return response()->json($result);
    }

    /**
     * Bước 3: Tổng hợp kết quả từ frontend, lưu DB và xóa ảnh rác.
     */
    public function saveResults(Request $request, Lesson $lesson)
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
        $maxRiskValue = 0; // none=0, low=1, medium=2, high=3

        $riskLevels = ['none' => 0, 'low' => 1, 'medium' => 2, 'high' => 3];

        foreach ($results as $result) {
            if (! empty($result['violence'])) {
                $violence = true;
            }
            if (! empty($result['adult'])) {
                $adult = true;
            }
            if (! empty($result['weapon'])) {
                $weapon = true;
            }
            if (! empty($result['tiktok_logo'])) {
                $tiktok_logo = true;
            }
            if (! empty($result['youtube_logo'])) {
                $youtube_logo = true;
            }
            if (! empty($result['watermark'])) {
                $watermark = true;
            }

            // Xử lý mức nghi ngờ bản quyền
            $currentRiskStr = strtolower($result['copyright_risk'] ?? 'none');
            // Tương thích ngược: 'low' từ dữ liệu cũ giữ nguyên ý nghĩa
            $currentRiskValue = $riskLevels[$currentRiskStr] ?? 0;

            if ($currentRiskValue > $maxRiskValue) {
                $maxRiskValue = $currentRiskValue;
                $copyrightRisk = $currentRiskStr;
                $summary = $result['summary'] ?? '';
            } elseif ($currentRiskValue === $maxRiskValue && empty($summary)) {
                $summary = $result['summary'] ?? '';
            }
        }

        // Nếu không có summary từ AI, tự động tạo summary mô tả tổng hợp
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

        // Lưu vào DB
        $moderation = VideoModeration::updateOrCreate(
            ['lesson_id' => $lesson->id],
            [
                'violence' => $violence,
                'adult' => $adult,
                'weapon' => $weapon,
                'tiktok_logo' => $tiktok_logo,
                'youtube_logo' => $youtube_logo,
                'watermark' => $watermark,
                'copyright_risk' => $copyrightRisk,
                'summary' => $summary,
                'details' => $results,
            ]
        );

        // Xóa thư mục chứa frame của bài học này
        $lessonDir = storage_path('app/temp_frames/lesson_'.$lesson->id);
        if (File::exists($lessonDir)) {
            File::deleteDirectory($lessonDir);
        }

        return response()->json([
            'success' => true,
            'moderation' => $moderation,
        ]);
    }
}
