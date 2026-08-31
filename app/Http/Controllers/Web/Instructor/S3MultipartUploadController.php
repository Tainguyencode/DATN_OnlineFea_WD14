<?php

namespace App\Http\Controllers\Web\Instructor;

use App\Http\Controllers\Controller;
use App\Jobs\ConvertContentUpdateVideoToHLS;
use App\Jobs\ConvertVideoToHLS;
use App\Models\ContentUpdate;
use App\Models\Course;
use App\Models\Lesson;
use App\Services\AwsS3UploadService;
use App\Services\ContentUpdateService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
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

        $maxVideoBytes = max(1, (int) config('video.upload.max_bytes'));
        $maxVideoMegabytes = (int) ceil($maxVideoBytes / 1048576);
        $validated = $request->validate([
            'filename' => ['required', 'string', 'max:255'],
            'content_type' => ['nullable', 'string', 'max:100'],
            'lesson_id' => [
                'nullable',
                'integer',
                Rule::exists('lessons', 'id')->where('course_id', $course->id),
            ],
            'file_size' => [
                'required',
                'integer',
                'min:1',
                'max:'.$maxVideoBytes,
            ],
            'key' => ['nullable', 'string', 'max:1024'],
        ], [
            'file_size.required' => 'Không xác định được dung lượng video.',
            'file_size.max' => "Dung lượng video tối đa là {$maxVideoMegabytes}MB.",
        ]);

        $filename = $validated['filename'];
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $allowedExtensions = (array) config('video.upload.allowed_extensions', []);

        if (! in_array($extension, $allowedExtensions, true)) {
            return response()->json([
                'message' => 'Định dạng video không được hỗ trợ. Vui lòng chọn tệp MP4, MOV, AVI, WEBM, hoặc MKV.',
            ], 422);
        }

        $contentType = $validated['content_type'] ?: 'video/'.($extension === 'mov' ? 'quicktime' : $extension);
        $lessonId = $validated['lesson_id'] ?? null;

        // Sinh hoặc sử dụng S3 object key bảo mật theo cấu trúc quy định
        $key = $validated['key'] ?? $this->s3Service->generateVideoObjectKey($course->id, $lessonId, $filename);
        if (! empty($validated['key'])) {
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
            'key' => ['required', 'string', 'max:1024'],
            'uploadId' => ['required', 'string', 'max:1024'],
            'partNumbers' => ['required', 'array', 'min:1', 'max:100'],
            'partNumbers.*' => ['required', 'integer', 'min:1', 'max:10000', 'distinct'],
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
            'key' => ['required', 'string', 'max:1024'],
            'uploadId' => ['required', 'string', 'max:1024'],
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
            'key' => ['required', 'string', 'max:1024'],
            'uploadId' => ['required', 'string', 'max:1024'],
            'parts' => ['required', 'array', 'min:1', 'max:10000'],
            'parts.*.PartNumber' => ['required', 'integer', 'min:1', 'max:10000', 'distinct'],
            'parts.*.ETag' => ['required', 'string', 'max:255'],
            'duration' => ['nullable', 'numeric', 'min:0'],
            'lesson_id' => [
                'nullable',
                'integer',
                Rule::exists('lessons', 'id')->where('course_id', $course->id),
            ],
        ]);

        $key = $validated['key'];
        $duration = (int) round((float) ($validated['duration'] ?? 0));
        $lessonId = $validated['lesson_id'] ?? null;
        $this->validateKeyPrefix($course, $key);

        try {
            $result = $this->s3Service->completeMultipartUpload($key, $validated['uploadId'], $validated['parts']);

            return DB::transaction(function () use ($course, $lessonId, $key, $duration, $result) {
                $course = Course::query()->lockForUpdate()->findOrFail($course->id);
                $this->authorizeCourse($course);
                $lesson = Lesson::where('course_id', $course->id)
                    ->when($lessonId, fn ($q) => $q->whereKey($lessonId), fn ($q) => $q->where('original_video_key', $key))
                    ->lockForUpdate()->first();
                $contentUpdate = ContentUpdate::where('type', ContentUpdate::TYPE_LESSON)
                    ->where('course_id', $course->id)
                    ->where('created_by', auth()->id())
                    ->where('status', ContentUpdate::STATUS_DRAFT)
                    ->where(function ($q) use ($key, $lessonId) {
                        $q->whereJsonContains('payload->original_video_key', $key);
                        if ($lessonId) {
                            $q->orWhere('entity_id', $lessonId);
                        }
                    })->latest()->lockForUpdate()->first();

                $videoData = [
                    'original_video_key' => $key,
                    'upload_status' => 'uploaded',
                    'processing_status' => 'pending',
                ];
                if ($duration > 0) {
                    $videoData += ['duration' => $duration, 'duration_seconds' => $duration];
                }

                if ($lesson && $course->isContentApproved()) {
                    // Existing approved lessons may only receive a review candidate.
                    $payload = array_merge($contentUpdate?->payload ?? [], $videoData, [
                        'hls_manifest_key' => null,
                        'video_path' => null,
                    ]);
                    $contentUpdate = app(ContentUpdateService::class)->recordPendingUpdate(
                        ContentUpdate::TYPE_LESSON, ContentUpdate::ACTION_UPDATE,
                        $course->id, $lesson->id, $payload, auth()->user()
                    );
                } elseif ($lesson) {
                    // Re-check after upload; approval may have changed while S3 was receiving parts.
                    abort_unless(in_array($course->status, [Course::STATUS_DRAFT, Course::STATUS_REJECTED], true), 409);
                    $lesson->update($videoData);
                    ConvertVideoToHLS::dispatch($lesson)->afterCommit();
                }

                if ($contentUpdate) {
                    $contentUpdate->update(['payload' => array_merge($contentUpdate->payload ?? [], $videoData)]);
                    ConvertContentUpdateVideoToHLS::dispatch($contentUpdate)->afterCommit();
                }

                return response()->json([
                    'status' => 'success',
                    'key' => $key,
                    'content_update_id' => $contentUpdate?->id,
                    'location' => $result['location'] ?? null,
                ]);
            });
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
            'key' => ['required', 'string', 'max:1024'],
            'uploadId' => ['required', 'string', 'max:1024'],
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
        if (! Str::startsWith($key, $expectedPrefix)) {
            abort(403, 'Đường dẫn S3 không hợp lệ hoặc không thuộc khóa học này.');
        }
    }
}
