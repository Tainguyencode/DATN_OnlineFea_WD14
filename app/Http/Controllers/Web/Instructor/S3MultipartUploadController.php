<?php

namespace App\Http\Controllers\Web\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\AwsS3UploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class S3MultipartUploadController extends Controller
{
    public function __construct(
        private AwsS3UploadService $s3Service
    ) {}

    /**
     * Khởi tạo Multipart Upload session trên S3
     */
    public function create(Request $request, Course $course): JsonResponse
    {
        $this->authorizeCourse($course);

        $validated = $request->validate([
            'filename' => ['required', 'string'],
            'content_type' => ['nullable', 'string'],
            'lesson_id' => ['nullable', 'integer'],
            'file_size' => ['nullable', 'integer', 'min:1'],
            'key' => ['nullable', 'string'],
        ]);

        $filename = $validated['filename'];
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $allowedExtensions = ['mp4', 'mov', 'avi', 'webm', 'mkv', 'm4v'];

        if (!in_array($extension, $allowedExtensions, true)) {
            return response()->json([
                'message' => 'Định dạng video không được hỗ trợ. Vui lòng chọn tệp MP4, MOV, AVI, WEBM, hoặc MKV.',
            ], 422);
        }

        $contentType = $validated['content_type'] ?: 'video/' . ($extension === 'mov' ? 'quicktime' : $extension);
        $lessonId = $validated['lesson_id'] ?? null;

        // Sinh hoặc sử dụng S3 object key bảo mật theo cấu trúc quy định
        $key = $validated['key'] ?? $this->s3Service->generateVideoObjectKey($course->id, $lessonId, $filename);
        if (!empty($validated['key'])) {
            $this->validateKeyPrefix($course, $key);
        }

        try {
            $uploadId = $this->s3Service->createMultipartUpload($key, $contentType);

            return response()->json([
                'uploadId' => $uploadId,
                'key' => $key,
                'bucket' => $this->s3Service->getBucket(),
            ]);
        } catch (Throwable $e) {
            Log::error('S3 multipart upload initialization failed.', [
                'exception' => $e,
                'course_id' => $course->id,
                'lesson_id' => $lessonId,
            ]);

            return response()->json([
                'message' => 'Không thể bắt đầu tải video lên lúc này. Vui lòng thử lại.',
            ], 500);
        }
    }

    /**
     * Ký presigned URL cho một hoặc nhiều Part
     */
    public function batchSign(Request $request, Course $course): JsonResponse
    {
        $this->authorizeCourse($course);

        $validated = $request->validate([
            'key' => ['required', 'string'],
            'uploadId' => ['required', 'string'],
            'partNumbers' => ['required', 'array', 'min:1', 'max:100'],
            'partNumbers.*' => ['required', 'integer', 'min:1', 'max:10000'],
        ]);

        $key = $validated['key'];
        $this->validateKeyPrefix($course, $key);

        $uploadId = $validated['uploadId'];
        $partNumbers = $validated['partNumbers'];

        try {
            $presignedUrls = [];
            foreach ($partNumbers as $partNumber) {
                $presignedUrls[$partNumber] = $this->s3Service->createPresignedPartUrl($key, $uploadId, (int) $partNumber);
            }

            return response()->json([
                'presignedUrls' => $presignedUrls,
            ]);
        } catch (Throwable $e) {
            Log::error('S3 multipart batch signing failed.', [
                'exception' => $e,
                'course_id' => $course->id,
                'part_count' => count($partNumbers),
            ]);

            return response()->json([
                'message' => 'Không thể chuẩn bị tải video lên lúc này. Vui lòng thử lại.',
            ], 500);
        }
    }

    /**
     * Ký presigned URL cho một Part đơn lẻ (phù hợp với @uppy/aws-s3-multipart)
     */
    public function signPart(Request $request, Course $course): JsonResponse
    {
        $this->authorizeCourse($course);

        $validated = $request->validate([
            'key' => ['required', 'string'],
            'uploadId' => ['required', 'string'],
            'partNumber' => ['required', 'integer', 'min:1', 'max:10000'],
        ]);

        $key = $validated['key'];
        $this->validateKeyPrefix($course, $key);

        try {
            $url = $this->s3Service->createPresignedPartUrl($key, $validated['uploadId'], (int) $validated['partNumber']);

            return response()->json([
                'url' => $url,
            ]);
        } catch (Throwable $e) {
            Log::error('S3 multipart part signing failed.', [
                'exception' => $e,
                'course_id' => $course->id,
                'part_number' => (int) $validated['partNumber'],
            ]);

            return response()->json([
                'message' => 'Không thể chuẩn bị tải video lên lúc này. Vui lòng thử lại.',
            ], 500);
        }
    }

    /**
     * Hoàn tất quá trình Multipart Upload
     */
    public function complete(Request $request, Course $course): JsonResponse
    {
        $this->authorizeCourse($course);

        $validated = $request->validate([
            'key' => ['required', 'string'],
            'uploadId' => ['required', 'string'],
            'parts' => ['required', 'array', 'min:1'],
            'parts.*.PartNumber' => ['required', 'integer', 'min:1'],
            'parts.*.ETag' => ['required', 'string'],
        ]);

        $key = $validated['key'];
        $this->validateKeyPrefix($course, $key);

        try {
            $result = $this->s3Service->completeMultipartUpload($key, $validated['uploadId'], $validated['parts']);

            // Kích hoạt ConvertVideoToHLS nếu lesson đã được tạo/lưu trong DB
            $lesson = \App\Models\Lesson::where('original_video_key', $key)->first();
            if ($lesson && ! $lesson->isHlsReady()) {
                $lesson->update([
                    'upload_status' => 'uploaded',
                    'processing_status' => 'pending',
                ]);
                \Illuminate\Support\Facades\Log::info('[S3 MULTIPART COMPLETE] DISPATCH HLS JOB for Lesson', ['lesson_id' => $lesson->id, 'key' => $key]);
                \App\Jobs\ConvertVideoToHLS::dispatch($lesson);
            }

            // Kích hoạt ConvertContentUpdateVideoToHLS nếu có ContentUpdate draft tương ứng
            $contentUpdate = \App\Models\ContentUpdate::where('type', \App\Models\ContentUpdate::TYPE_LESSON)
                ->where('course_id', $course->id)
                ->where('status', \App\Models\ContentUpdate::STATUS_DRAFT)
                ->whereJsonContains('payload->original_video_key', $key)
                ->latest()
                ->first();

            if ($contentUpdate) {
                \Illuminate\Support\Facades\Log::info('[S3 MULTIPART COMPLETE] DISPATCH HLS JOB for ContentUpdate', ['content_update_id' => $contentUpdate->id, 'key' => $key]);
                \App\Jobs\ConvertContentUpdateVideoToHLS::dispatch($contentUpdate);
            }

            return response()->json([
                'status' => 'success',
                'key' => $key,
                'location' => $result['location'] ?? null,
            ]);
        } catch (Throwable $e) {
            Log::error('S3 multipart upload completion failed.', [
                'exception' => $e,
                'course_id' => $course->id,
                'part_count' => count($validated['parts']),
            ]);

            return response()->json([
                'message' => 'Không thể hoàn tất tải video lên lúc này. Vui lòng thử lại.',
            ], 500);
        }
    }

    /**
     * Hủy Multipart Upload khi có lỗi hoặc người dùng bấm cancel
     */
    public function abort(Request $request, Course $course): JsonResponse
    {
        $this->authorizeCourse($course);

        $validated = $request->validate([
            'key' => ['required', 'string'],
            'uploadId' => ['required', 'string'],
        ]);

        $key = $validated['key'];
        $this->validateKeyPrefix($course, $key);

        $this->s3Service->abortMultipartUpload($key, $validated['uploadId']);

        return response()->json([
            'status' => 'aborted',
        ]);
    }

    private function authorizeCourse(Course $course): void
    {
        abort_unless($course->isOwnedBy(auth()->user()), 403, 'Bạn không có quyền quản trị khóa học này.');
    }

    private function validateKeyPrefix(Course $course, string $key): void
    {
        $expectedPrefix = "originals/courses/{$course->id}/";
        if (!Str::startsWith($key, $expectedPrefix)) {
            abort(403, 'Đường dẫn S3 không hợp lệ hoặc không thuộc khóa học này.');
        }
    }
}
