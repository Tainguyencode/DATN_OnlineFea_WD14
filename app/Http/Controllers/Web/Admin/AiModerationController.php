<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ContentUpdate;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\VideoModeration;
use App\Services\GeminiService;
use App\Services\VideoFrameExtractor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AiModerationController extends Controller
{
    /**
     * Helper resolve Lesson model or draft ContentUpdate lesson and video path
     */
    private function resolveLessonAndVideoPath(string|int $lessonId, bool $preferOriginal = false): array
    {
        $useS3 = ! empty(config('filesystems.disks.s3.key')) && ! empty(config('filesystems.disks.s3.bucket'));

        if (str_starts_with((string) $lessonId, 'update_les_')) {
            $updateId = str_replace('update_les_', '', $lessonId);
            $update = ContentUpdate::find($updateId);
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

                return [$draftLesson, $preferOriginal
                    ? (($payload['original_video_key'] ?? null) ?: ($payload['video_path'] ?? null))
                    : ($payload['video_path'] ?? $payload['hls_manifest_key'] ?? $payload['original_video_key'] ?? null)];
            }
        }

        $lesson = Lesson::find($lessonId);
        if ($lesson) {
            // Check if there is a pending or draft ContentUpdate for this lesson override
            $pendingUpdate = ContentUpdate::where('entity_id', $lesson->id)
                ->where('type', ContentUpdate::TYPE_LESSON)
                ->whereIn('status', [ContentUpdate::STATUS_DRAFT, ContentUpdate::STATUS_PENDING, ContentUpdate::STATUS_REJECTED])
                ->latest()
                ->first();

            if ($pendingUpdate) {
                $p = $pendingUpdate->payload ?? [];
                if (! empty($p['original_video_key']) || ! empty($p['hls_manifest_key']) || ! empty($p['video_path'])) {
                    return [$lesson, $preferOriginal
                        ? (($p['original_video_key'] ?? null) ?: ($p['video_path'] ?? null))
                        : ($p['video_path'] ?? $p['hls_manifest_key'] ?? $p['original_video_key'])];
                }
            }

            return [$lesson, $preferOriginal
                ? ($lesson->original_video_key ?: $lesson->video_path)
                : ($lesson->video_path ?: ($lesson->hls_manifest_key ?: $lesson->original_video_key))];
        }

        // Fallback check if $lessonId is a numeric ContentUpdate ID
        $update = ContentUpdate::find($lessonId);
        if ($update && $update->type === ContentUpdate::TYPE_LESSON) {
            $payload = $update->payload ?? [];
            $draftLesson = new Lesson([
                'title' => $payload['title'] ?? 'Bài học nháp',
                'type' => $payload['type'] ?? 'video',
                'video_path' => $payload['video_path'] ?? null,
                'original_video_key' => $payload['original_video_key'] ?? null,
                'hls_manifest_key' => $payload['hls_manifest_key'] ?? null,
            ]);
            $draftLesson->id = 'update_les_'.$update->id;

            return [$draftLesson, $preferOriginal
                ? (($payload['original_video_key'] ?? null) ?: ($payload['video_path'] ?? null))
                : ($payload['video_path'] ?? $payload['hls_manifest_key'] ?? $payload['original_video_key'] ?? null)];
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
        $useS3 = ! empty(config('filesystems.disks.s3.key')) && ! empty(config('filesystems.disks.s3.bucket'));
        if ($useS3) {
            $s3Key = $lesson?->original_video_key ?: $videoPath;
            if (Storage::disk('s3')->exists($s3Key)) {
                $stream = Storage::disk('s3')->readStream($s3Key);
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
        if (Storage::disk('local')->exists($videoPath)) {
            $path = Storage::disk('local')->path($videoPath);
        } elseif (Storage::disk('public')->exists($videoPath)) {
            $path = Storage::disk('public')->path($videoPath);
        }

        if (! $path || ! file_exists($path)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'File video không tồn tại trên máy chủ.'], 404);
            }
            abort(404, 'File video không tồn tại trên máy chủ.');
        }

        return response()->file($path, [
            'Content-Type' => 'video/mp4',
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

        $isUpdate = str_starts_with((string) $lessonId, 'update_les_');
        $rawId = $isUpdate ? str_replace('update_les_', '', $lessonId) : $lessonId;

        $cacheKey = 'hls_signed_playlist_'.$lessonId;
        $useS3 = ! empty(config('filesystems.disks.s3.key')) && ! empty(config('filesystems.disks.s3.bucket'));

        // Kiểm tra cache đã có playlist đã ký S3 chưa
        if ($useS3 && Cache::has($cacheKey)) {
            return response(Cache::get($cacheKey), 200, [
                'Content-Type' => 'application/vnd.apple.mpegurl; charset=utf-8',
                'Access-Control-Allow-Origin' => '*',
                'Cache-Control' => 'public, max-age=1800',
            ]);
        }

        $content = null;

        // 1. Kiểm tra S3
        if ($useS3) {
            $s3PlaylistKey = $isUpdate
                ? 'hls/updates/'.$rawId.'/playlist.m3u8'
                : 'hls/lessons/'.$rawId.'/playlist.m3u8';
            $s3MasterKey = $isUpdate
                ? 'hls/updates/'.$rawId.'/master.m3u8'
                : 'hls/lessons/'.$rawId.'/master.m3u8';

            $targetKey = null;
            try {
                $content = Storage::disk('s3')->get($s3PlaylistKey);
                $targetKey = $s3PlaylistKey;
            } catch (\Throwable) {
                try {
                    $content = Storage::disk('s3')->get($s3MasterKey);
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
                    if ($trimmed !== '' && ! str_starts_with($trimmed, '#')) {
                        // Nếu là file segment .ts hoặc playlist con .m3u8
                        $segmentKey = $s3Dir.'/'.$trimmed;
                        try {
                            $signedUrl = Storage::disk('s3')->temporaryUrl($segmentKey, $expiresAt);
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
                Cache::put($cacheKey, $signedContent, now()->addHours(6));

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
                ? 'lesson-hls/update_'.$rawId
                : 'lesson-hls/'.$rawId;
            $localPlaylist = $localDir.'/playlist.m3u8';
            $localMaster = $localDir.'/master.m3u8';

            if (Storage::disk('local')->exists($localPlaylist)) {
                $content = Storage::disk('local')->get($localPlaylist);
            } elseif (Storage::disk('local')->exists($localMaster)) {
                $content = Storage::disk('local')->get($localMaster);
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
        $isUpdate = str_starts_with((string) $lessonId, 'update_les_');
        $rawId = $isUpdate ? str_replace('update_les_', '', $lessonId) : $lessonId;

        // 1. Kiểm tra S3 -> Redirect trực tiếp đến S3 Signed URL thay vì proxy qua PHP
        $useS3 = ! empty(config('filesystems.disks.s3.key')) && ! empty(config('filesystems.disks.s3.bucket'));
        if ($useS3) {
            $s3SegmentKey = $isUpdate
                ? 'hls/updates/'.$rawId.'/'.$segment
                : 'hls/lessons/'.$rawId.'/'.$segment;

            if (Storage::disk('s3')->exists($s3SegmentKey)) {
                $signedUrl = Storage::disk('s3')->temporaryUrl($s3SegmentKey, now()->addHours(2));

                return redirect()->away($signedUrl);
            }
        }

        // 2. Fallback: Local
        $localSegment = $isUpdate
            ? 'lesson-hls/update_'.$rawId.'/'.$segment
            : 'lesson-hls/'.$rawId.'/'.$segment;

        if (Storage::disk('local')->exists($localSegment)) {
            $path = Storage::disk('local')->path($localSegment);

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
        [$lesson, $videoPathRel] = $this->resolveLessonAndVideoPath($lessonId, true);

        if (! $videoPathRel) {
            return response()->json(['error' => 'Bài học này không có video hợp lệ.'], 400);
        }

        $videoPath = null;
        if (Storage::disk('local')->exists($videoPathRel)) {
            $videoPath = Storage::disk('local')->path($videoPathRel);
        } elseif (Storage::disk('public')->exists($videoPathRel)) {
            $videoPath = Storage::disk('public')->path($videoPathRel);
        }

        $temporaryVideo = null;
        try {
            if (! $videoPath && filled(config('filesystems.disks.s3.bucket'))) {
                // FFmpeg needs a local source; never treat an HLS manifest as the original upload.
                if (str_ends_with(strtolower($videoPathRel), '.m3u8')) {
                    return response()->json(['error' => 'Không tìm thấy video gốc để quét AI. Vui lòng tải lại video gốc.'], 404);
                }
                $source = Storage::disk('s3')->readStream($videoPathRel);
                if (! is_resource($source)) {
                    return response()->json(['error' => 'Không đọc được video gốc từ kho lưu trữ. Vui lòng thử lại.'], 404);
                }
                $destination = null;
                try {
                    $directory = storage_path('app/tmp_ai_video');
                    File::ensureDirectoryExists($directory);
                    $temporaryVideo = tempnam($directory, 'scan_');
                    if ($temporaryVideo === false) {
                        throw new \RuntimeException('Could not create moderation source file.');
                    }
                    $destination = fopen($temporaryVideo, 'wb');
                    if (! is_resource($destination) || stream_copy_to_stream($source, $destination) === false) {
                        throw new \RuntimeException('Could not download moderation source file.');
                    }
                } finally {
                    fclose($source);
                    if (is_resource($destination)) {
                        fclose($destination);
                    }
                }
                $videoPath = $temporaryVideo;
            }
            if (! $videoPath || ! is_file($videoPath)) {
                return response()->json(['error' => 'Không tìm thấy video gốc trên máy chủ hoặc kho lưu trữ.'], 404);
            }
            $frames = $extractor->extract($videoPath, 300, $lessonId);

            if (empty($frames)) {
                return response()->json(['error' => 'Không thể trích xuất hình ảnh từ video này.'], 422);
            }

            return response()->json([
                'frames' => $frames,
                'total' => count($frames),
            ]);
        } catch (\Throwable $e) {
            Log::error('Admin AI moderation frame extraction failed.', [
                'exception' => $e,
                'lesson_id' => (string) $lessonId,
            ]);

            return response()->json([
                'error' => 'Không thể trích xuất khung hình lúc này. Vui lòng thử lại.',
            ], 500);
        } finally {
            if ($temporaryVideo && is_file($temporaryVideo)) {
                unlink($temporaryVideo);
            }
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

        if (isset($result['error'])) {
            Log::warning('Admin AI moderation frame analysis failed.', [
                'frame_name' => basename((string) $framePath),
                'timestamp' => (float) $timestamp,
                'technical_error' => (string) $result['error'],
            ]);

            return response()->json([
                'error' => 'Không thể phân tích khung hình lúc này. Vui lòng thử lại.',
            ]);
        }

        unset($result['_model_used'], $result['_raw_text']);
        $result['timestamp'] = $timestamp;
        $result['frame_path'] = $framePath;

        return response()->json($result);
    }

    /**
     * Helper lấy context đầy đủ của bài học & khóa học để kiểm tra độ phù hợp danh mục.
     */
    private function resolveLessonContext(string|int $lessonId): array
    {
        $isUpdate = str_starts_with((string) $lessonId, 'update_les_');
        $updateId = $isUpdate ? str_replace('update_les_', '', $lessonId) : null;
        $update = $updateId ? ContentUpdate::find($updateId) : null;
        $lesson = (! $isUpdate && ! $update)
            ? Lesson::with(['course.category.parent', 'chapter.course.category.parent', 'section.course.category.parent'])->find($lessonId)
            : null;

        $course = null;
        $lessonTitle = '';
        $lessonContent = '';

        if ($lesson) {
            $lessonTitle = $lesson->title;
            $lessonContent = (string) ($lesson->content ?: ($lesson->description ?? ''));
            $course = $lesson->course ?: ($lesson->chapter?->course ?: $lesson->section?->course);
            if (! $course && $lesson->course_id) {
                $course = Course::with('category.parent')->find($lesson->course_id);
            }
        } elseif ($update) {
            $payload = $update->payload ?? [];
            $lessonTitle = $payload['title'] ?? 'Bài học';
            $lessonContent = (string) ($payload['content'] ?? ($payload['description'] ?? ''));
            if ($update->course_id) {
                $course = Course::with('category.parent')->find($update->course_id);
            }
        }

        $category = $course?->category;

        return [
            'category_name' => $category?->name ?? 'Không xác định',
            'parent_category_name' => $category?->parent?->name ?? '',
            'course_title' => $course?->title ?? '',
            'course_description' => (string) ($course?->short_description ?: ($course?->description ?? '')),
            'lesson_title' => $lessonTitle,
            'lesson_content' => $lessonContent,
        ];
    }

    /**
     * Bước 2b: Phân tích sự phù hợp giữa nội dung video và danh mục / ngành của khóa học.
     */
    public function checkCategoryMatch(Request $request, string|int $lessonId, GeminiService $gemini)
    {
        $framePaths = $request->input('frames', []);

        if (empty($framePaths)) {
            $lessonDir = storage_path('app/temp_frames/lesson_'.$lessonId);
            if (File::isDirectory($lessonDir)) {
                $framePaths = File::glob($lessonDir.'/*.jpg');
            }
        }

        if (empty($framePaths)) {
            return response()->json([
                'status' => 'Cần Admin kiểm tra',
                'confidence' => 0.5,
                'reason' => 'Không tìm thấy khung hình video để kiểm tra danh mục.',
                'detected_topics' => [],
            ]);
        }

        $context = $this->resolveLessonContext($lessonId);

        if ($request->filled('category_name')) {
            $context['category_name'] = $request->input('category_name');
        }
        if ($request->filled('course_title')) {
            $context['course_title'] = $request->input('course_title');
        }

        $result = $gemini->analyzeCategoryMatch($framePaths, $context);
        $result['category_name'] = $context['category_name'];

        return response()->json($result);
    }

    /**
     * Bước 3: Tổng hợp kết quả từ frontend, lưu DB và xóa ảnh rác.
     */
    public function saveResults(Request $request, string|int $lessonId)
    {
        $validated = $request->validate([
            'results' => 'present|array',
            'category_match' => 'nullable|array',
        ]);

        $results = $validated['results'];
        $categoryMatch = $validated['category_match'] ?? null;

        if (count($results) === 0) {
            return response()->json([
                'error' => 'Không có kết quả phân tích nào để lưu. Vui lòng thử quét lại.',
            ], 422);
        }

        // Nếu frontend chưa gửi category_match nhưng còn frame trong thư mục tạm, tự động chạy kiểm tra
        if (! is_array($categoryMatch) || empty($categoryMatch['status'])) {
            $lessonDir = storage_path('app/temp_frames/lesson_'.$lessonId);
            if (File::isDirectory($lessonDir)) {
                $frameFiles = File::glob($lessonDir.'/*.jpg');
                if (! empty($frameFiles)) {
                    $context = $this->resolveLessonContext($lessonId);
                    $categoryMatch = app(GeminiService::class)->analyzeCategoryMatch($frameFiles, $context);
                }
            }
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

        foreach ($results as $key => $result) {
            if ($key === 'category_match' || ! is_array($result)) {
                continue;
            }

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
            if ($tiktok_logo) {
                $signs[] = 'logo TikTok';
            }
            if ($youtube_logo) {
                $signs[] = 'logo YouTube';
            }
            if ($watermark) {
                $signs[] = 'watermark';
            }
            if ($violence) {
                $signs[] = 'nội dung bạo lực';
            }
            if ($adult) {
                $signs[] = 'nội dung người lớn';
            }
            if ($weapon) {
                $signs[] = 'vũ khí';
            }

            if (! empty($signs)) {
                $summary = 'AI phát hiện dấu hiệu cần kiểm tra: '.implode(', ', $signs).'. Gợi ý: Có thể chỉ là video minh họa, admin nên xem lại trước khi quyết định.';
            }
        }

        // Thêm category_match vào details
        if (is_array($categoryMatch) && ! empty($categoryMatch['status'])) {
            $results['category_match'] = $categoryMatch;
        }

        $isUpdate = str_starts_with((string) $lessonId, 'update_les_');
        $updateId = $isUpdate ? str_replace('update_les_', '', $lessonId) : null;
        $update = $updateId ? ContentUpdate::find($updateId) : null;
        $realLesson = (! $isUpdate && ! $update) ? Lesson::find($lessonId) : null;

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
