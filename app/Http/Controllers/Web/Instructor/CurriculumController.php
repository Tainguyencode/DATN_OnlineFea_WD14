<?php

namespace App\Http\Controllers\Web\Instructor;

use App\Exceptions\HistoricalQuizDeletionException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\StoreChapterRequest;
use App\Http\Requests\Instructor\StoreLessonRequest;
use App\Jobs\ConvertContentUpdateVideoToHLS;
use App\Jobs\ConvertVideoToHLS;
use App\Models\ContentUpdate;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Services\AwsS3UploadService;
use App\Services\ContentUpdateService;
use App\Services\CurriculumLessonService;
use App\Services\HistoricalQuizDeletionGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CurriculumController extends Controller
{
    public function index(Course $course): View
    {
        $this->authorizeCourse($course);

        $curriculumSections = app(ContentUpdateService::class)->mergeCurriculumWithUpdates($course);

        $pendingContentUpdates = ContentUpdate::where('course_id', $course->id)
            ->whereIn('status', [ContentUpdate::STATUS_DRAFT, ContentUpdate::STATUS_PENDING, ContentUpdate::STATUS_REJECTED])
            ->get();

        return view('instructor.courses.curriculum', [
            'course' => $course,
            'curriculumSections' => $curriculumSections,
            'pendingContentUpdates' => $pendingContentUpdates,
            'lessonTypes' => $this->lessonTypes(),
            'lessonStatuses' => $this->lessonStatuses(),
        ]);
    }

    public function storeSection(StoreChapterRequest $request, Course $course): RedirectResponse
    {
        $this->authorizeCourse($course);

        $validated = $request->validated();

        if ($course->isPublished()) {
            app(ContentUpdateService::class)->recordPendingUpdate(
                ContentUpdate::TYPE_CHAPTER,
                ContentUpdate::ACTION_CREATE,
                $course->id,
                null,
                array_merge($validated, ['sort_order' => $course->courseSections()->count()]),
                $request->user()
            );

            return back()->with('success', 'Đã lưu bản cập nhật chương học mới. Chương học sẽ xuất hiện sau khi Admin duyệt.');
        }

        CourseSection::create([
            ...$validated,
            'course_id' => $course->id,
            'sort_order' => $course->courseSections()->count(),
        ]);

        return back()->with('success', 'Đã thêm chương học.');
    }

    public function updateSection(StoreChapterRequest $request, Course $course, $section): RedirectResponse
    {
        $sectionModel = $section instanceof CourseSection ? $section : CourseSection::find($section);
        $sectionId = $sectionModel ? $sectionModel->id : (int) (is_object($section) ? ($section->id ?? 0) : $section);

        if ($sectionModel) {
            $this->authorizeSection($course, $sectionModel);
        } else {
            $this->authorizeCourse($course);
        }

        $validated = $request->validated();

        if ($course->isPublished()) {
            $contentUpdate = ContentUpdate::find($sectionId);
            if ($contentUpdate && $contentUpdate->type === ContentUpdate::TYPE_CHAPTER) {
                $contentUpdate->update([
                    'payload' => array_merge($contentUpdate->payload ?? [], $validated),
                ]);

                return back()->with('success', 'Đã cập nhật bản nháp chương học.');
            }

            app(ContentUpdateService::class)->recordPendingUpdate(
                ContentUpdate::TYPE_CHAPTER,
                ContentUpdate::ACTION_UPDATE,
                $course->id,
                $sectionId,
                $validated,
                $request->user()
            );

            return back()->with('success', 'Đã lưu bản cập nhật chương học. Thay đổi sẽ áp dụng sau khi Admin duyệt.');
        }

        if ($sectionModel) {
            $sectionModel->update($validated);
        }

        return back()->with('success', 'Đã cập nhật chương học.');
    }

    public function destroySection(Course $course, $section): RedirectResponse
    {
        $sectionModel = $section instanceof CourseSection ? $section : CourseSection::find($section);
        $sectionId = $sectionModel ? $sectionModel->id : (int) (is_object($section) ? ($section->id ?? 0) : $section);

        if ($sectionModel) {
            $this->authorizeSection($course, $sectionModel);
        } else {
            $this->authorizeCourse($course);
        }

        if ($course->isPublished()) {
            $contentUpdate = ContentUpdate::find($sectionId);
            if ($contentUpdate && $contentUpdate->type === ContentUpdate::TYPE_CHAPTER) {
                $contentUpdate->delete();

                return back()->with('success', 'Đã xóa bản nháp chương học.');
            }

            app(ContentUpdateService::class)->recordPendingUpdate(
                ContentUpdate::TYPE_CHAPTER,
                ContentUpdate::ACTION_DELETE,
                $course->id,
                $sectionId,
                [],
                auth()->user()
            );

            return back()->with('success', 'Đã gửi yêu cầu xóa chương học. Yêu cầu sẽ áp dụng sau khi Admin duyệt.');
        }

        if ($sectionModel) {
            try {
                app(HistoricalQuizDeletionGuard::class)->assertSectionCanBeHardDeleted($sectionModel);
            } catch (HistoricalQuizDeletionException $exception) {
                return back()->withErrors(['section' => $exception->getMessage()]);
            }

            $sectionModel->lessons()->get()->each(function (Lesson $lesson) {
                $this->deleteLessonFiles($lesson);
                $lesson->delete();
            });
            $sectionModel->delete();
        }

        return back()->with('success', 'Đã xóa chương học.');
    }

    public function storeLesson(
        StoreLessonRequest $request,
        Course $course,
        $section,
        CurriculumLessonService $lessonService
    ): RedirectResponse|JsonResponse {
        $sectionModel = $section instanceof CourseSection ? $section : CourseSection::find($section);
        $sectionId = $sectionModel ? $sectionModel->id : (int) (is_object($section) ? ($section->id ?? 0) : $section);

        if ($sectionModel) {
            $this->authorizeSection($course, $sectionModel);
        } else {
            $this->authorizeCourse($course);
        }

        abort_unless($lessonService->canCreateForManual($course), 403);

        $result = $lessonService->createForManual(
            $course,
            $sectionModel ?? $sectionId,
            $request->validated(),
            $request->user(),
        );

        if ($result instanceof ContentUpdate) {
            Log::info('[UPLOAD TRACE] RETURN RESPONSE (ContentUpdate)');

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'lesson_id' => $result->id,
                    'title' => $result->payload['title'] ?? 'Bài học',
                    'message' => 'Đã lưu bản nháp bài học mới.',
                ]);
            }

            return back()->with('success', 'Đã lưu bản nháp bài học mới. Video đang được xử lý HLS ngầm.');
        }

        $lesson = $result;

        if ($lesson->type === Lesson::TYPE_QUIZ) {
            Log::info('[UPLOAD TRACE] RETURN RESPONSE (Quiz)');

            return redirect()
                ->route('instructor.courses.lessons.quiz.show', [$course, $lesson])
                ->with('success', 'Đã tạo bài quiz. Bạn có thể thêm câu hỏi ngay bên dưới.');
        }

        Log::info('[UPLOAD TRACE] RETURN RESPONSE (Success)');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'lesson_id' => $lesson->id,
                'lesson' => [
                    'id' => $lesson->id,
                    'section_id' => $sectionId,
                    'title' => $lesson->title,
                    'type' => $lesson->type,
                    'sort_order' => $lesson->sort_order,
                    'duration' => (int) ($lesson->duration ?? $lesson->duration_seconds ?? 0),
                    'duration_formatted' => $this->formatDuration((int) ($lesson->duration ?? $lesson->duration_seconds ?? 0)),
                    'is_preview' => (bool) $lesson->is_preview,
                    'status' => $lesson->status,
                    'original_video_key' => $lesson->original_video_key,
                    'upload_status' => $lesson->upload_status,
                    'processing_status' => $lesson->processing_status,
                    'content' => $lesson->content,
                    'destroy_url' => route('instructor.courses.lessons.destroy', [$course, $lesson->id]),
                ],
                'title' => $lesson->title,
                'message' => 'Đã thêm bài học.',
            ]);
        }

        return back()->with('success', 'Đã thêm bài học. Video đang được xử lý ngầm, vui lòng đợi trong giây lát.');
    }

    private function formatDuration(int $seconds): string
    {
        if ($seconds <= 0) {
            return 'Chưa đặt';
        }

        $minutes = intdiv($seconds, 60);
        $remaining = $seconds % 60;

        return $minutes > 0 ? $minutes.' phút'.($remaining ? ' '.$remaining.' giây' : '') : $remaining.' giây';
    }

    public function updateLesson(StoreLessonRequest $request, Course $course, Lesson $lesson): RedirectResponse
    {
        $this->authorizeLesson($course, $lesson);

        $validated = $request->validated();
        $lessonData = $this->lessonData($validated);
        $lessonData = $this->storeLessonDocument($request, $lessonData, $lesson);

        Log::info('[UPLOAD TRACE] START STORE FILE (Update)');
        $lessonData = $this->storeLessonVideo($request, $lessonData, $lesson);
        Log::info('[UPLOAD TRACE] STORE FILE DONE (Update)', ['video_path' => $lessonData['video_path'] ?? null, 'original_video_key' => $lessonData['original_video_key'] ?? null]);

        if ($course->isPublished()) {
            $payload = array_merge($lessonData, array_intersect_key($validated, array_flip([
                'assignment_due_days',
                'assignment_max_score',
                'assignment_passing_score',
            ])), [
                'duration_seconds' => $lessonData['duration'] ?? 0,
                'is_preview' => $request->boolean('is_preview'),
                'sort_order' => $lessonData['sort_order'] ?? $lesson->sort_order,
                'status' => $lessonData['status'] ?? Lesson::STATUS_DRAFT,
            ]);

            Log::info('[UPLOAD TRACE] SAVE DATABASE (ContentUpdate Record)');
            $contentUpdate = app(ContentUpdateService::class)->recordPendingUpdate(
                ContentUpdate::TYPE_LESSON,
                ContentUpdate::ACTION_UPDATE,
                $course->id,
                $lesson->id,
                $payload,
                $request->user()
            );

            if (($lessonData['type'] ?? null) === Lesson::TYPE_VIDEO) {
                if ($request->hasFile('video_file')) {
                    Log::info('[UPLOAD TRACE] DISPATCH HLS JOB (ContentUpdate Update Local)', ['content_update_id' => $contentUpdate->id]);
                    ConvertContentUpdateVideoToHLS::dispatch($contentUpdate);
                } elseif ($request->filled('s3_key')) {
                    $s3Key = (string) $request->input('s3_key');
                    if (app(AwsS3UploadService::class)->doesObjectExist($s3Key)) {
                        Log::info('[UPLOAD TRACE] DISPATCH HLS JOB (ContentUpdate Update S3 Object exists)', ['content_update_id' => $contentUpdate->id, 'key' => $s3Key]);
                        ConvertContentUpdateVideoToHLS::dispatch($contentUpdate);
                    } else {
                        Log::info('[UPLOAD TRACE] S3 Object still uploading in background. HLS will be dispatched on S3 complete.', ['content_update_id' => $contentUpdate->id, 'key' => $s3Key]);
                    }
                }
            }

            Log::info('[UPLOAD TRACE] RETURN RESPONSE (Update ContentUpdate)');

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'content_update_id' => $contentUpdate->id,
                    'lesson_id' => $lesson->id,
                    'title' => $payload['title'] ?? $lesson->title,
                    'message' => 'Đã lưu bản cập nhật nội dung bài học.',
                ]);
            }

            return back()->with('success', 'Đã lưu bản cập nhật nội dung bài học. Video đang được xử lý HLS ngầm.');
        }

        Log::info('[UPLOAD TRACE] SAVE DATABASE (Update Lesson)');
        $lesson->update([
            ...$lessonData,
            'duration_seconds' => $lessonData['duration'] ?? 0,
            'is_preview' => $request->boolean('is_preview'),
            'sort_order' => $lessonData['sort_order'] ?? $lesson->sort_order,
            'status' => $lessonData['status'] ?? Lesson::STATUS_DRAFT,
        ]);

        if ($lesson->type === Lesson::TYPE_VIDEO) {
            if ($request->hasFile('video_file')) {
                Log::info('[UPLOAD TRACE] DISPATCH HLS JOB (Update Local video_file)', ['lesson_id' => $lesson->id]);
                ConvertVideoToHLS::dispatch($lesson);
            } elseif ($request->filled('s3_key')) {
                $s3Key = (string) $request->input('s3_key');
                if (app(AwsS3UploadService::class)->doesObjectExist($s3Key)) {
                    Log::info('[UPLOAD TRACE] DISPATCH HLS JOB (Update S3 Object exists)', ['lesson_id' => $lesson->id, 'key' => $s3Key]);
                    ConvertVideoToHLS::dispatch($lesson);
                } else {
                    Log::info('[UPLOAD TRACE] S3 Object still uploading in background. HLS will be dispatched on S3 complete.', ['lesson_id' => $lesson->id, 'key' => $s3Key]);
                }
            }
        }

        $lesson->refresh();
        app(CurriculumLessonService::class)->syncAssignment($lesson, $validated);

        if ($lesson->type === Lesson::TYPE_QUIZ) {
            Log::info('[UPLOAD TRACE] RETURN RESPONSE (Update Quiz)');

            return redirect()
                ->route('instructor.courses.lessons.quiz.show', [$course, $lesson])
                ->with('success', 'Đã cập nhật bài quiz. Bạn có thể quản lý câu hỏi tại đây.');
        }

        Log::info('[UPLOAD TRACE] RETURN RESPONSE (Update Success)');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'lesson_id' => $lesson->id,
                'title' => $lesson->title,
                'message' => 'Đã cập nhật bài học.',
            ]);
        }

        return back()->with('success', 'Đã cập nhật bài học. Nếu có video mới, video đang được xử lý ngầm.');
    }

    public function destroyLesson(Course $course, Lesson $lesson): RedirectResponse
    {
        $this->authorizeLesson($course, $lesson);

        if ($course->isPublished()) {
            app(ContentUpdateService::class)->recordPendingUpdate(
                ContentUpdate::TYPE_LESSON,
                ContentUpdate::ACTION_DELETE,
                $course->id,
                $lesson->id,
                [],
                auth()->user()
            );

            return back()->with('success', 'Đã gửi yêu cầu xóa bài học. Yêu cầu sẽ áp dụng sau khi Admin duyệt.');
        }

        try {
            app(HistoricalQuizDeletionGuard::class)->assertLessonCanBeHardDeleted($lesson);
        } catch (HistoricalQuizDeletionException $exception) {
            return back()->withErrors(['lesson' => $exception->getMessage()]);
        }

        $this->deleteLessonFiles($lesson);
        $lesson->delete();

        return back()->with('success', 'Đã xóa bài học.');
    }

    public function updateContentUpdate(StoreLessonRequest $request, Course $course, ContentUpdate $contentUpdate): RedirectResponse|JsonResponse
    {
        $this->authorizeCourse($course);
        abort_unless((int) $contentUpdate->course_id === (int) $course->id, 403);

        $validated = $request->validated();
        $lessonData = $this->lessonData($validated);
        $lessonData = $this->storeLessonDocument($request, $lessonData);
        $lessonData = $this->storeLessonVideo($request, $lessonData);

        $existingPayload = $contentUpdate->payload ?? [];
        $newPayload = array_merge($existingPayload, $lessonData, array_intersect_key($validated, array_flip([
            'assignment_due_days',
            'assignment_max_score',
            'assignment_passing_score',
        ])), [
            'duration_seconds' => $lessonData['duration'] ?? ($existingPayload['duration'] ?? 0),
            'is_preview' => $request->boolean('is_preview'),
            'sort_order' => $lessonData['sort_order'] ?? ($existingPayload['sort_order'] ?? 0),
            'status' => $lessonData['status'] ?? ($existingPayload['status'] ?? Lesson::STATUS_DRAFT),
        ]);

        $contentUpdate->update([
            'payload' => $newPayload,
            'summary' => $this->buildLessonUpdateSummary($existingPayload, $newPayload),
        ]);

        if (($lessonData['type'] ?? null) === Lesson::TYPE_VIDEO) {
            if ($request->hasFile('video_file')) {
                ConvertContentUpdateVideoToHLS::dispatch($contentUpdate);
            } elseif ($request->filled('s3_key')) {
                $s3Key = (string) $request->input('s3_key');
                if (app(AwsS3UploadService::class)->doesObjectExist($s3Key)) {
                    ConvertContentUpdateVideoToHLS::dispatch($contentUpdate);
                }
            }
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'content_update_id' => $contentUpdate->id,
                'message' => 'Đã cập nhật bản nháp bài học.',
            ]);
        }

        return back()->with('success', 'Đã cập nhật bản nháp bài học. Nếu có video mới, video đang được xử lý HLS ngầm.');
    }

    public function destroyContentUpdate(Course $course, ContentUpdate $contentUpdate): RedirectResponse
    {
        $this->authorizeCourse($course);
        abort_unless((int) $contentUpdate->course_id === (int) $course->id, 403);

        $contentUpdate->delete();

        return back()->with('success', 'Đã xóa bản nháp bài học.');
    }

    private function authorizeCourse(Course $course): void
    {
        abort_unless($course->isOwnedBy(auth()->user()), 403);
    }

    private function authorizeSection(Course $course, CourseSection $section): void
    {
        $this->authorizeCourse($course);
        abort_unless((int) $section->course_id === (int) $course->id, 403);
    }

    private function authorizeLesson(Course $course, Lesson $lesson): void
    {
        $this->authorizeCourse($course);
        abort_unless((int) $lesson->course_id === (int) $course->id, 403);
    }

    private function deleteLessonDocument(Lesson $lesson): void
    {
        if ($lesson->document_file) {
            Storage::disk('public')->delete($lesson->document_file);
        }
    }

    private function lessonData(array $validated): array
    {
        unset(
            $validated['video_file'],
            $validated['s3_key'],
            $validated['document_file'],
            $validated['assignment_due_days'],
            $validated['assignment_max_score'],
            $validated['assignment_passing_score']
        );

        if (($validated['type'] ?? null) !== Lesson::TYPE_VIDEO) {
            unset(
                $validated['video_url'],
                $validated['video_path'],
                $validated['original_video_key'],
                $validated['hls_manifest_key'],
                $validated['video_original_name'],
                $validated['video_mime'],
                $validated['video_size']
            );
        } else {
            $validated['video_size'] = (int) ($validated['video_size'] ?? 0);
        }

        if (! in_array($validated['type'] ?? null, [Lesson::TYPE_VIDEO, Lesson::TYPE_DOCUMENT, Lesson::TYPE_ASSIGNMENT], true)) {
            unset($validated['content']);
        }

        return $validated;
    }

    private function storeLessonDocument(Request $request, array $validated, ?Lesson $lesson = null): array
    {
        if (! in_array($validated['type'] ?? null, [Lesson::TYPE_DOCUMENT, Lesson::TYPE_ASSIGNMENT], true) || ! $request->hasFile('document_file')) {
            return $validated;
        }

        $path = $request->file('document_file')->store('lesson-documents', 'public');

        if ($lesson) {
            $this->deleteLessonDocument($lesson);
        }

        return [
            ...$validated,
            'document_file' => $path,
        ];
    }

    private function storeLessonVideo(Request $request, array $validated, ?Lesson $lesson = null): array
    {
        unset($validated['video_file'], $validated['s3_key']);

        if (($validated['type'] ?? null) !== Lesson::TYPE_VIDEO) {
            return $validated;
        }

        // Ưu tiên S3 Multipart Direct Upload
        if ($request->filled('s3_key')) {
            $s3Key = (string) $request->input('s3_key');
            $originalName = (string) ($request->input('video_original_name') ?: basename($s3Key));
            $mime = (string) ($request->input('video_mime') ?: 'video/mp4');
            $size = $request->input('video_size') ? (int) $request->input('video_size') : 0;
            $s3Exists = app(AwsS3UploadService::class)->doesObjectExist($s3Key);

            return [
                ...$validated,
                'original_video_key' => $s3Key,
                'video_original_name' => $originalName,
                'video_mime' => $mime,
                'video_size' => $size,
                'upload_status' => $s3Exists ? 'uploaded' : 'pending',
                'processing_status' => 'pending',
            ];
        }

        if (! $request->hasFile('video_file')) {
            return $validated;
        }

        $file = $request->file('video_file');
        // Store the original MP4 file in local disk so it's private
        $path = $file->store('lesson-videos-mp4', 'local');

        if ($lesson) {
            $this->deleteLessonVideo($lesson);
        }

        return [
            ...$validated,
            'video_path' => $path,
            'video_original_name' => $file->getClientOriginalName(),
            'video_mime' => $file->getClientMimeType(),
            'video_size' => $file->getSize(),
            'upload_status' => 'uploaded',
            'processing_status' => 'pending',
        ];
    }

    private function deleteLessonFiles(Lesson $lesson): void
    {
        $this->deleteLessonDocument($lesson);
        $this->deleteLessonVideo($lesson);
    }

    private function deleteLessonVideo(Lesson $lesson): void
    {
        if ($lesson->video_path) {
            Storage::disk('local')->delete($lesson->video_path);
        }

        // Delete HLS directory if exists
        $hlsDir = 'lesson-hls/'.$lesson->id;
        if (Storage::disk('local')->exists($hlsDir)) {
            Storage::disk('local')->deleteDirectory($hlsDir);
        }
    }

    private function lessonTypes(): array
    {
        return [
            Lesson::TYPE_VIDEO => 'Video',
            Lesson::TYPE_DOCUMENT => 'Tài liệu',
            Lesson::TYPE_QUIZ => 'Quiz',
            Lesson::TYPE_ASSIGNMENT => 'Bài tập',
        ];
    }

    private function lessonStatuses(): array
    {
        return [
            Lesson::STATUS_DRAFT => 'Nháp',
            Lesson::STATUS_PUBLISHED => 'Đã sẵn sàng',
        ];
    }

    public function getHlsStatus(Course $course): JsonResponse
    {
        $this->authorizeCourse($course);

        $lessons = Lesson::where('course_id', $course->id)
            ->where('type', 'video')
            ->select(['id', 'processing_status', 'upload_status', 'hls_manifest_key', 'video_path', 'original_video_key', 'duration', 'duration_seconds'])
            ->get();

        $updates = ContentUpdate::where('course_id', $course->id)
            ->where('type', ContentUpdate::TYPE_LESSON)
            ->get();

        $statuses = [];
        foreach ($lessons as $lesson) {
            $isReady = $lesson->isHlsReady();
            $isProcessing = $lesson->isProcessing();
            $isFailed = $lesson->hasFailedProcessing();
            $durationSec = (int) ($lesson->duration ?? $lesson->duration_seconds ?? 0);

            $statuses['lesson_'.$lesson->id] = [
                'id' => $lesson->id,
                'processing_status' => $lesson->processing_status,
                'is_ready' => $isReady,
                'is_processing' => $isProcessing,
                'is_failed' => $isFailed,
                'duration' => $durationSec,
                'duration_formatted' => $this->formatDuration($durationSec),
                'status_message' => $isReady
                    ? 'Video đã được xử lý bảo mật thành công.'
                    : ($isFailed
                        ? 'Video xử lý bảo mật thất bại.'
                        : ($isProcessing
                            ? 'Video đang trong quá trình xử lý bảo mật. Vui lòng chờ trong giây lát.'
                            : 'Đang chờ xử lý')),
            ];
        }

        foreach ($updates as $update) {
            $payload = $update->payload ?? [];
            $pStatus = $payload['processing_status'] ?? 'pending';
            $pManifest = $payload['hls_manifest_key'] ?? null;
            $isReady = $pStatus === 'completed' || filled($pManifest);
            $isFailed = $pStatus === 'failed' && ! $isReady;
            $isProcessing = in_array($pStatus, ['processing', 'pending'], true) && ! $isReady;
            $durationSec = (int) ($payload['duration'] ?? $payload['duration_seconds'] ?? 0);

            $key = $update->action === ContentUpdate::ACTION_CREATE
                ? 'update_'.$update->id
                : 'lesson_'.($update->entity_id ?? $update->id);

            $statuses[$key] = [
                'id' => $update->entity_id ?? $update->id,
                'update_id' => $update->id,
                'processing_status' => $pStatus,
                'is_ready' => $isReady,
                'is_processing' => $isProcessing,
                'is_failed' => $isFailed,
                'duration' => $durationSec,
                'duration_formatted' => $this->formatDuration($durationSec),
                'status_message' => $isReady
                    ? 'Video đã được xử lý bảo mật thành công.'
                    : ($isFailed
                        ? 'Video xử lý bảo mật thất bại.'
                        : ($isProcessing
                            ? 'Video đang trong quá trình xử lý bảo mật. Vui lòng chờ trong giây lát.'
                            : 'Đang chờ xử lý')),
            ];
        }

        $totalVideos = count($statuses);
        $hasIncompleteHls = $course->hasIncompleteHlsVideos();
        $hasFailed = false;
        $hasProcessing = false;

        foreach ($statuses as $st) {
            if ($st['is_failed']) {
                $hasFailed = true;
            } elseif ($st['is_processing'] || ! $st['is_ready']) {
                $hasProcessing = true;
            }
        }

        if ($totalVideos === 0) {
            $commonState = 'no_videos';
            $commonMessage = '';
        } elseif ($hasFailed) {
            $commonState = 'failed';
            $commonMessage = 'Video chưa xử lý hoàn tất. Vui lòng chờ quá trình xử lý video hoàn tất trước khi gửi duyệt.';
        } elseif ($hasProcessing || $hasIncompleteHls) {
            $commonState = 'processing';
            $commonMessage = 'Video đang trong quá trình xử lý bảo mật, xử lý xong bạn có thể bấm gửi duyệt.';
        } else {
            $commonState = 'completed';
            $commonMessage = 'Video đã được xử lý bảo mật thành công. Bạn có thể bấm gửi duyệt.';
        }

        return response()->json([
            'total_videos' => $totalVideos,
            'statuses' => $statuses,
            'has_incomplete_hls' => $hasIncompleteHls,
            'can_submit' => $totalVideos > 0 ? (! $hasIncompleteHls && ! $hasFailed && ! $hasProcessing) : true,
            'common_state' => $commonState,
            'common_message' => $commonMessage,
        ]);
    }
}
