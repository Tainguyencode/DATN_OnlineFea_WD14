<?php

namespace App\Http\Controllers\Web\Instructor;

use App\Http\Controllers\Controller;
use App\Jobs\ConvertContentUpdateVideoToHLS;
use App\Jobs\ConvertVideoToHLS;
use App\Models\ContentUpdate;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonVersion;
use App\Services\AwsS3UploadService;
use App\Services\ContentUpdateService;
use App\Services\ContentVersionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
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

        if (! in_array($extension, $allowedExtensions, true)) {
            return response()->json([
                'message' => 'Định dạng video không được hỗ trợ. Vui lòng chọn tệp MP4, MOV, AVI, WEBM, hoặc MKV.',
            ], 422);
        }

        $contentType = $validated['content_type'] ?: 'video/'.($extension === 'mov' ? 'quicktime' : $extension);
        $lessonId = $validated['lesson_id'] ?? null;

        $lesson = $lessonId
            ? Lesson::query()->where('course_id', $course->id)->find($lessonId)
            : null;
        if ($lessonId && ! $lesson) {
            throw ValidationException::withMessages(['lesson_id' => 'Bài học không thuộc khóa học này.']);
        }

        $contentUpdate = null;
        $candidate = null;
        if ($course->isPublished() && $lesson) {
            $contentUpdate = app(ContentUpdateService::class)->ensureLessonUpdateDraft($course, $lesson, $request->user());
            $candidate = LessonVersion::query()
                ->where('content_update_id', $contentUpdate->id)
                ->where('lesson_id', $lesson->id)
                ->firstOrFail();
        }

        // Sinh hoặc sử dụng S3 object key bảo mật theo cấu trúc quy định
        $key = $validated['key'] ?? ($candidate
            ? $this->s3Service->generateDraftVideoObjectKey($course->id, $lesson->id, $contentUpdate->id, $candidate->version_number, $filename)
            : $this->s3Service->generateVideoObjectKey($course->id, $lessonId, $filename));
        if (! empty($validated['key'])) {
            $this->validateKeyPrefix($course, $key);
        }

        try {
            $uploadId = $this->s3Service->createMultipartUpload($key, $contentType);

            if ($contentUpdate && $candidate) {
                $contentUpdate = app(ContentUpdateService::class)->updateDraft($contentUpdate, [
                    'original_video_key' => $key,
                    'upload_status' => 'pending',
                    'processing_status' => 'pending',
                ]);
                app(ContentVersionService::class)->prepareDraftCandidate($contentUpdate, $request->user());
            }

            return response()->json([
                'uploadId' => $uploadId,
                'key' => $key,
                'bucket' => $this->s3Service->getBucket(),
                'contentUpdateId' => $contentUpdate?->id,
                'versionNumber' => $candidate?->version_number,
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
            'duration' => ['nullable', 'numeric', 'min:0'],
            'lesson_id' => ['nullable', 'integer'],
            'content_update_id' => ['nullable', 'integer'],
        ]);

        $key = $validated['key'];
        $duration = (int) round((float) ($validated['duration'] ?? 0));
        $lessonId = $validated['lesson_id'] ?? null;
        $this->validateKeyPrefix($course, $key);

        try {
            // Resolve and validate the immutable draft identity before S3 is
            // completed. A rejected request must not leave an orphaned object.
            $lesson = $lessonId
                ? Lesson::query()->where('course_id', $course->id)->find($lessonId)
                : null;
            if (! $lesson) {
                $lesson = Lesson::where('course_id', $course->id)
                    ->where('original_video_key', $key)
                    ->first();
            }

            $contentUpdate = null;
            $candidate = null;
            if ($course->isPublished()) {
                if (! $lesson) {
                    throw ValidationException::withMessages([
                        'lesson_id' => 'Hãy lưu bài học trước khi tải video cho khóa học đã xuất bản.',
                    ]);
                }

                $requestedUpdateId = (int) ($validated['content_update_id'] ?? 0);
                $contentUpdate = $requestedUpdateId
                    ? ContentUpdate::query()
                        ->whereKey($requestedUpdateId)
                        ->where('course_id', $course->id)
                        ->where('type', ContentUpdate::TYPE_LESSON)
                        ->where('action', ContentUpdate::ACTION_UPDATE)
                        ->where('entity_id', $lesson->id)
                        ->where('created_by', $request->user()->id)
                        ->where('status', ContentUpdate::STATUS_DRAFT)
                        ->first()
                    : null;
                $contentUpdate ??= app(ContentUpdateService::class)->ensureLessonUpdateDraft($course, $lesson, $request->user());
                $candidate = LessonVersion::query()
                    ->where('content_update_id', $contentUpdate->id)
                    ->where('lesson_id', $lesson->id)
                    ->where('status', LessonVersion::STATUS_DRAFT)
                    ->firstOrFail();

                $expectedPrefix = "originals/courses/{$course->id}/lessons/{$lesson->id}/content-updates/{$contentUpdate->id}/versions/v{$candidate->version_number}/";
                if (! Str::startsWith($key, $expectedPrefix)) {
                    throw ValidationException::withMessages([
                        'key' => 'Video không thuộc đúng bản nháp bài học đang chỉnh sửa.',
                    ]);
                }
            }

            $result = $this->s3Service->completeMultipartUpload($key, $validated['uploadId'], $validated['parts']);

            if ($contentUpdate && $candidate) {
                $payload = $contentUpdate->payload ?? [];
                $changes = [
                    'original_video_key' => $key,
                    'upload_status' => 'uploaded',
                ];
                if ($duration > 0 && empty($payload['duration']) && empty($payload['duration_seconds'])) {
                    $changes['duration'] = $duration;
                    $changes['duration_seconds'] = $duration;
                }
                $contentUpdate = app(ContentUpdateService::class)->updateDraft($contentUpdate, $changes);
                app(ContentVersionService::class)->prepareDraftCandidate($contentUpdate, $request->user());
                Log::info('[S3 MULTIPART COMPLETE] DISPATCH HLS JOB for ContentUpdate', ['content_update_id' => $contentUpdate->id, 'key' => $key]);
                ConvertContentUpdateVideoToHLS::dispatch($contentUpdate);

                return response()->json([
                    'status' => 'success',
                    'key' => $key,
                    'location' => $result['location'] ?? null,
                    'contentUpdateId' => $contentUpdate->id,
                    'versionNumber' => $candidate->version_number,
                ]);
            }

            if ($lesson) {
                $lessonUpdateData = [
                    'original_video_key' => $key,
                    'upload_status' => 'uploaded',
                    'processing_status' => 'pending',
                ];
                if ($duration > 0 && ($lesson->duration <= 0 || $lesson->duration_seconds <= 0)) {
                    $lessonUpdateData['duration'] = $duration;
                    $lessonUpdateData['duration_seconds'] = $duration;
                }
                $lesson->update($lessonUpdateData);
                Log::info('[S3 MULTIPART COMPLETE] DISPATCH HLS JOB for Lesson', ['lesson_id' => $lesson->id, 'key' => $key, 'duration' => $duration]);
                ConvertVideoToHLS::dispatch($lesson);
            }

            return response()->json([
                'status' => 'success',
                'key' => $key,
                'location' => $result['location'] ?? null,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => $e->validator->errors()->first() ?: $e->getMessage(),
            ], 422);
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
        if (! Str::startsWith($key, $expectedPrefix)) {
            abort(403, 'Đường dẫn S3 không hợp lệ hoặc không thuộc khóa học này.');
        }
    }
}
