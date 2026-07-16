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
     * Bước 1: Cắt frame từ video của Lesson và trả về danh sách file để frontend xử lý.
     */
    public function extractFrames(Lesson $lesson, VideoFrameExtractor $extractor)
    {
        if ($lesson->type !== 'video' || empty($lesson->video_path)) {
            return response()->json(['error' => 'Bài học này không có video hợp lệ.'], 400);
        }

        $videoPath = storage_path('app/public/'.$lesson->video_path);

        if (! file_exists($videoPath)) {
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

        $copyrightRisk = 'low';
        $summary = '';
        $maxRiskValue = 0; // low=0, medium=1, high=2

        $riskLevels = ['low' => 0, 'medium' => 1, 'high' => 2];

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

            // Xử lý bản quyền
            $currentRiskStr = strtolower($result['copyright_risk'] ?? 'low');
            $currentRiskValue = $riskLevels[$currentRiskStr] ?? 0;

            if ($currentRiskValue > $maxRiskValue) {
                $maxRiskValue = $currentRiskValue;
                $copyrightRisk = $currentRiskStr;
                $summary = $result['summary'] ?? '';
            } elseif ($currentRiskValue === $maxRiskValue && empty($summary)) {
                $summary = $result['summary'] ?? '';
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
