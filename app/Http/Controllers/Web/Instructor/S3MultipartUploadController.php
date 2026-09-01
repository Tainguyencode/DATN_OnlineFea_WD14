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
use App\Services\InstructorCourseCategoryAccess;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class S3MultipartUploadController extends Controller
{
    public function __construct(
        private AwsS3UploadService $s3Service,
        private InstructorCourseCategoryAccess $courseCategoryAccess,
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
            if ((int) $contentUpdate->created_by !== (int) $request->user()->id) {
                throw ValidationException::withMessages([
                    'content_update_id' => 'Bản nháp video không thuộc giảng viên đang thao tác.',
                ]);
            }
            $candidate = LessonVersion::query()
                ->where('content_update_id', $contentUpdate->id)
                ->where('lesson_id', $lesson->id)
                ->where('status', LessonVersion::STATUS_DRAFT)
                ->firstOrFail();
            if ((int) $lesson->fresh()->draft_version_id !== (int) $candidate->id) {
                throw ValidationException::withMessages([
                    'content_update_id' => 'Phiên bản nháp video không khớp với bài học.',
                ]);
            }
        }

        // Sinh hoặc sử dụng S3 object key bảo mật theo cấu trúc quy định
        $key = $validated['key'] ?? ($candidate
            ? $this->s3Service->generateDraftVideoObjectKey($course->id, $lesson->id, $contentUpdate->id, $candidate->version_number, $filename)
            : $this->s3Service->generateVideoObjectKey($course->id, $lessonId, $filename));
        if ($contentUpdate && $candidate && ! Str::startsWith($key, $this->draftVideoKeyPrefix($course, $lesson, $contentUpdate, $candidate))) {
            throw ValidationException::withMessages([
                'key' => 'Video không thuộc đúng bản nháp bài học đang chỉnh sửa.',
            ]);
        }
        if (! $candidate && ! empty($validated['key'])) {
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
                $preparedCandidate = app(ContentVersionService::class)->prepareDraftCandidate($contentUpdate, $request->user());
                if (! $preparedCandidate || (int) $preparedCandidate->id !== (int) $candidate->id) {
                    throw ValidationException::withMessages([
                        'content_update_id' => 'Phiên bản nháp video đã thay đổi khi khởi tạo tải lên.',
                    ]);
                }
            }

            return response()->json([
                'uploadId' => $uploadId,
                'key' => $key,
                'status' => 'initialized',
                'bucket' => $this->s3Service->getBucket(),
                'contentUpdateId' => $contentUpdate?->id,
                'versionNumber' => $candidate?->version_number,
            ]);
        } catch (Throwable $e) {
            Log::error('S3 Multipart Upload create failed: '.$e->getMessage(), [
                'course_id' => $course->id,
                'key' => $key,
            ]);

            return response()->json([
                'message' => 'Không thể khởi tạo phiên tải lên S3. Vui lòng thử lại sau.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Lấy Presigned URL cho từng Part
     */
    public function getPartUrl(Request $request, Course $course): JsonResponse
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
            $presignedUrl = $this->s3Service->createPresignedPartUrl(
                $key,
                $validated['uploadId'],
                $validated['partNumber']
            );

            return response()->json([
                'url' => $presignedUrl,
                'partNumber' => $validated['partNumber'],
            ]);
        } catch (Throwable $e) {
            Log::error('S3 Multipart Upload sign part failed: '.$e->getMessage(), [
                'course_id' => $course->id,
                'key' => $key,
                'partNumber' => $validated['partNumber'],
            ]);

            return response()->json([
                'message' => 'Không thể tạo URL tải lên cho part '.$validated['partNumber'],
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Batch lấy Presigned URLs cho nhiều Parts cùng một lúc
     */
    public function batchSignParts(Request $request, Course $course): JsonResponse
    {
        $this->authorizeCourse($course);

        $validated = $request->validate([
            'key' => ['required', 'string', 'max:1024'],
            'uploadId' => ['required', 'string', 'max:1024'],
            'partNumbers' => ['required', 'array', 'min:1', 'max:1000'],
            'partNumbers.*' => ['integer', 'min:1', 'max:10000'],
        ]);

        $key = $validated['key'];
        $this->validateKeyPrefix($course, $key);

        $urls = [];
        try {
            foreach ($validated['partNumbers'] as $partNumber) {
                $urls[$partNumber] = $this->s3Service->createPresignedPartUrl(
                    $key,
                    $validated['uploadId'],
                    $partNumber
                );
            }

            return response()->json([
                'urls' => $urls,
            ]);
        } catch (Throwable $e) {
            Log::error('S3 Multipart Upload batch sign parts failed: '.$e->getMessage(), [
                'course_id' => $course->id,
                'key' => $key,
            ]);

            return response()->json([
                'message' => 'Không thể tạo URLs tải lên cho danh sách parts.',
                'error' => $e->getMessage(),
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
            'content_update_id' => ['nullable', 'integer'],
        ]);

        $key = $validated['key'];
        $duration = (int) round((float) ($validated['duration'] ?? 0));
        $lessonId = $validated['lesson_id'] ?? null;
        $this->validateKeyPrefix($course, $key);

        try {
            return DB::transaction(function () use ($request, $course, $validated, $lessonId, $key, $duration) {
                $course = Course::query()->lockForUpdate()->findOrFail($course->id);
                $this->authorizeCourse($course);

                // Resolve and lock the exact lesson before completing the S3 upload.
                $lesson = $lessonId
                    ? Lesson::query()->where('course_id', $course->id)->lockForUpdate()->find($lessonId)
                    : null;
                if (! $lesson) {
                    $lesson = Lesson::where('course_id', $course->id)
                        ->when($lessonId, fn ($q) => $q->whereKey($lessonId), fn ($q) => $q->where('original_video_key', $key))
                        ->lockForUpdate()->first();
                }

                $contentUpdate = null;
                $candidate = null;
                if ($course->isContentApproved()) {
                    if (! $lesson) {
                        throw ValidationException::withMessages([
                            'lesson_id' => 'Hãy lưu bài học trước khi tải video cho khóa học đã xuất bản.',
                        ]);
                    }

                    $requestedUpdateId = (int) ($validated['content_update_id'] ?? 0);
                    if ($requestedUpdateId <= 0) {
                        throw ValidationException::withMessages([
                            'content_update_id' => 'Thiếu định danh bản nháp video đang tải lên.',
                        ]);
                    }

                    $contentUpdate = ContentUpdate::query()
                        ->whereKey($requestedUpdateId)
                        ->where('course_id', $course->id)
                        ->where('type', ContentUpdate::TYPE_LESSON)
                        ->where('action', ContentUpdate::ACTION_UPDATE)
                        ->where('entity_id', $lesson->id)
                        ->where('created_by', $request->user()->id)
                        ->where('status', ContentUpdate::STATUS_DRAFT)
                        ->lockForUpdate()
                        ->first();
                    if (! $contentUpdate) {
                        throw ValidationException::withMessages([
                            'content_update_id' => 'Bản nháp video không hợp lệ hoặc không còn có thể chỉnh sửa.',
                        ]);
                    }

                    $candidate = LessonVersion::query()
                        ->where('content_update_id', $contentUpdate->id)
                        ->where('lesson_id', $lesson->id)
                        ->where('status', LessonVersion::STATUS_DRAFT)
                        ->lockForUpdate()
                        ->first();
                    if (! $candidate || (int) $lesson->draft_version_id !== (int) $candidate->id) {
                        throw ValidationException::withMessages([
                            'content_update_id' => 'Phiên bản nháp video không khớp với bài học đang chỉnh sửa.',
                        ]);
                    }

                    if (! Str::startsWith($key, $this->draftVideoKeyPrefix($course, $lesson, $contentUpdate, $candidate))) {
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
                    $preparedCandidate = app(ContentVersionService::class)->prepareDraftCandidate($contentUpdate, $request->user());
                    if (! $preparedCandidate || (int) $preparedCandidate->id !== (int) $candidate->id) {
                        throw ValidationException::withMessages([
                            'content_update_id' => 'Phiên bản nháp video đã thay đổi trong khi hoàn tất tải lên.',
                        ]);
                    }
                    Log::info('[S3 MULTIPART COMPLETE] DISPATCH HLS JOB for ContentUpdate', ['content_update_id' => $contentUpdate->id, 'lesson_version_id' => $candidate->id, 'key' => $key]);
                    ConvertContentUpdateVideoToHLS::dispatch($contentUpdate)->afterCommit();

                    return response()->json([
                        'status' => 'success',
                        'key' => $key,
                        'location' => $result['location'] ?? null,
                        'content_update_id' => $contentUpdate->id,
                        'contentUpdateId' => $contentUpdate->id,
                        'versionNumber' => $candidate->version_number,
                    ]);
                }

                if ($lesson) {
                    $videoData = [
                        'original_video_key' => $key,
                        'upload_status' => 'uploaded',
                        'processing_status' => 'pending',
                    ];
                    if ($duration > 0) {
                        $videoData += ['duration' => $duration, 'duration_seconds' => $duration];
                    }

                    // Re-check after upload; approval may have changed while S3 was receiving parts.
                    abort_unless(in_array($course->status, [Course::STATUS_DRAFT, Course::STATUS_REJECTED], true), 409);
                    $lesson->update($videoData);
                    ConvertVideoToHLS::dispatch($lesson)->afterCommit();
                }

                return response()->json([
                    'status' => 'success',
                    'key' => $key,
                    'content_update_id' => null,
                    'location' => $result['location'] ?? null,
                ]);
            });
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
        abort_unless(
            $this->courseCategoryAccess->canManageCourse(auth()->user(), $course),
            403,
            'Bạn không có quyền chỉnh sửa nội dung khóa học này.'
        );
    }

    private function validateKeyPrefix(Course $course, string $key): void
    {
        $expectedPrefix = "originals/courses/{$course->id}/";
        if (! Str::startsWith($key, $expectedPrefix)) {
            abort(403, 'Đường dẫn S3 không hợp lệ hoặc không thuộc khóa học này.');
        }
    }

    private function draftVideoKeyPrefix(
        Course $course,
        Lesson $lesson,
        ContentUpdate $contentUpdate,
        LessonVersion $candidate
    ): string {
        return "originals/courses/{$course->id}/lessons/{$lesson->id}/content-updates/{$contentUpdate->id}/versions/v{$candidate->version_number}/";
    }
}
