<?php

namespace App\Http\Controllers\Web\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Instructor\StoreChapterRequest;
use App\Http\Requests\Instructor\StoreLessonRequest;
use App\Models\ContentUpdate;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Lesson;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CurriculumController extends Controller
{
    public function index(Course $course): View
    {
        $this->authorizeCourse($course);

        $curriculumSections = app(\App\Services\ContentUpdateService::class)->mergeCurriculumWithUpdates($course);

        $pendingContentUpdates = \App\Models\ContentUpdate::where('course_id', $course->id)
            ->whereIn('status', [\App\Models\ContentUpdate::STATUS_DRAFT, \App\Models\ContentUpdate::STATUS_PENDING, \App\Models\ContentUpdate::STATUS_REJECTED])
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
            app(\App\Services\ContentUpdateService::class)->recordPendingUpdate(
                \App\Models\ContentUpdate::TYPE_CHAPTER,
                \App\Models\ContentUpdate::ACTION_CREATE,
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
            $contentUpdate = \App\Models\ContentUpdate::find($sectionId);
            if ($contentUpdate && $contentUpdate->type === \App\Models\ContentUpdate::TYPE_CHAPTER) {
                $contentUpdate->update([
                    'payload' => array_merge($contentUpdate->payload ?? [], $validated),
                ]);

                return back()->with('success', 'Đã cập nhật bản nháp chương học.');
            }

            app(\App\Services\ContentUpdateService::class)->recordPendingUpdate(
                \App\Models\ContentUpdate::TYPE_CHAPTER,
                \App\Models\ContentUpdate::ACTION_UPDATE,
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
            $contentUpdate = \App\Models\ContentUpdate::find($sectionId);
            if ($contentUpdate && $contentUpdate->type === \App\Models\ContentUpdate::TYPE_CHAPTER) {
                $contentUpdate->delete();

                return back()->with('success', 'Đã xóa bản nháp chương học.');
            }

            app(\App\Services\ContentUpdateService::class)->recordPendingUpdate(
                \App\Models\ContentUpdate::TYPE_CHAPTER,
                \App\Models\ContentUpdate::ACTION_DELETE,
                $course->id,
                $sectionId,
                [],
                auth()->user()
            );

            return back()->with('success', 'Đã gửi yêu cầu xóa chương học. Yêu cầu sẽ áp dụng sau khi Admin duyệt.');
        }

        if ($sectionModel) {
            $sectionModel->lessons()->get()->each(fn (Lesson $lesson) => $this->deleteLessonFiles($lesson));
            $sectionModel->delete();
        }

        return back()->with('success', 'Đã xóa chương học.');
    }

    public function storeLesson(StoreLessonRequest $request, Course $course, $section): RedirectResponse
    {
        $sectionModel = $section instanceof CourseSection ? $section : CourseSection::find($section);
        $sectionId = $sectionModel ? $sectionModel->id : (int) (is_object($section) ? ($section->id ?? 0) : $section);

        if ($sectionModel) {
            $this->authorizeSection($course, $sectionModel);
        } else {
            $this->authorizeCourse($course);
        }

        $validated = $request->validated();
        $lessonData = $this->lessonData($validated);
        $lessonData = $this->storeLessonDocument($request, $lessonData);

        \Illuminate\Support\Facades\Log::info('[UPLOAD TRACE] START STORE FILE');
        $lessonData = $this->storeLessonVideo($request, $lessonData);
        \Illuminate\Support\Facades\Log::info('[UPLOAD TRACE] STORE FILE DONE', ['video_path' => $lessonData['video_path'] ?? null]);

        if ($course->isPublished()) {
            $payload = array_merge($lessonData, [
                'section_id' => $sectionId,
                'chapter_id' => null,
                'duration_seconds' => $lessonData['duration'] ?? 0,
                'is_preview' => $request->boolean('is_preview'),
                'sort_order' => $lessonData['sort_order'] ?? ($sectionModel ? $sectionModel->lessons()->count() : 0),
            ]);

            \Illuminate\Support\Facades\Log::info('[UPLOAD TRACE] SAVE DATABASE (ContentUpdate Draft)');
            $contentUpdate = app(\App\Services\ContentUpdateService::class)->recordPendingUpdate(
                \App\Models\ContentUpdate::TYPE_LESSON,
                \App\Models\ContentUpdate::ACTION_CREATE,
                $course->id,
                null,
                $payload,
                $request->user(),
                \App\Models\ContentUpdate::STATUS_DRAFT
            );

            if (($lessonData['type'] ?? null) === 'video') {
                if ($request->hasFile('video_file')) {
                    \Illuminate\Support\Facades\Log::info('[UPLOAD TRACE] DISPATCH HLS JOB (ContentUpdate Local)', ['content_update_id' => $contentUpdate->id]);
                    \App\Jobs\ConvertContentUpdateVideoToHLS::dispatch($contentUpdate);
                } elseif ($request->filled('s3_key')) {
                    $s3Key = (string) $request->input('s3_key');
                    if (app(\App\Services\AwsS3UploadService::class)->doesObjectExist($s3Key)) {
                        \Illuminate\Support\Facades\Log::info('[UPLOAD TRACE] DISPATCH HLS JOB (ContentUpdate S3 Object exists)', ['content_update_id' => $contentUpdate->id, 'key' => $s3Key]);
                        \App\Jobs\ConvertContentUpdateVideoToHLS::dispatch($contentUpdate);
                    } else {
                        \Illuminate\Support\Facades\Log::info('[UPLOAD TRACE] S3 Object still uploading in background. HLS will be dispatched on S3 complete.', ['content_update_id' => $contentUpdate->id, 'key' => $s3Key]);
                    }
                }
            }

            \Illuminate\Support\Facades\Log::info('[UPLOAD TRACE] RETURN RESPONSE');
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'lesson_id' => $contentUpdate->id,
                    'title' => $payload['title'] ?? 'Bài học',
                    'message' => 'Đã lưu bản nháp bài học mới.',
                ]);
            }

            return back()->with('success', 'Đã lưu bản nháp bài học mới. Video đang được xử lý HLS ngầm.');
        }

        \Illuminate\Support\Facades\Log::info('[UPLOAD TRACE] SAVE DATABASE (Lesson)');
        $lesson = Lesson::create([
            ...$lessonData,
            'course_id' => $course->id,
            'section_id' => $sectionId,
            'chapter_id' => null,
            'duration_seconds' => $lessonData['duration'] ?? 0,
            'is_preview' => $request->boolean('is_preview'),
            'sort_order' => $lessonData['sort_order'] ?? ($sectionModel ? $sectionModel->lessons()->count() : 0),
            'status' => $lessonData['status'] ?? 'draft',
        ]);

        if ($lesson->type === 'video') {
            if ($request->hasFile('video_file')) {
                \Illuminate\Support\Facades\Log::info('[UPLOAD TRACE] DISPATCH HLS JOB (Local video_file)', ['lesson_id' => $lesson->id]);
                \App\Jobs\ConvertVideoToHLS::dispatch($lesson);
            } elseif ($request->filled('s3_key')) {
                $s3Key = (string) $request->input('s3_key');
                if (app(\App\Services\AwsS3UploadService::class)->doesObjectExist($s3Key)) {
                    \Illuminate\Support\Facades\Log::info('[UPLOAD TRACE] DISPATCH HLS JOB (S3 Object already completed)', ['lesson_id' => $lesson->id, 'key' => $s3Key]);
                    \App\Jobs\ConvertVideoToHLS::dispatch($lesson);
                } else {
                    \Illuminate\Support\Facades\Log::info('[UPLOAD TRACE] S3 Object still uploading in background. HLS will be dispatched on S3 complete.', ['lesson_id' => $lesson->id, 'key' => $s3Key]);
                }
            }
        }

        $this->syncAssignment($lesson, $validated);

        if ($lesson->type === 'quiz') {
            \Illuminate\Support\Facades\Log::info('[UPLOAD TRACE] RETURN RESPONSE (Quiz)');
            return redirect()
                ->route('instructor.courses.lessons.quiz.show', [$course, $lesson])
                ->with('success', 'Đã tạo bài quiz. Bạn có thể thêm câu hỏi ngay bên dưới.');
        }

        \Illuminate\Support\Facades\Log::info('[UPLOAD TRACE] RETURN RESPONSE (Success)');
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'lesson_id' => $lesson->id,
                'title' => $lesson->title,
                'message' => 'Đã thêm bài học.',
            ]);
        }

        return back()->with('success', 'Đã thêm bài học. Video đang được xử lý ngầm, vui lòng đợi trong giây lát.');
    }

    public function updateLesson(StoreLessonRequest $request, Course $course, Lesson $lesson): RedirectResponse
    {
        $this->authorizeLesson($course, $lesson);

        $validated = $request->validated();
        $lessonData = $this->lessonData($validated);
        $lessonData = $this->storeLessonDocument($request, $lessonData, $lesson);

        \Illuminate\Support\Facades\Log::info('[UPLOAD TRACE] START STORE FILE (Update)');
        $lessonData = $this->storeLessonVideo($request, $lessonData, $lesson);
        \Illuminate\Support\Facades\Log::info('[UPLOAD TRACE] STORE FILE DONE (Update)', ['video_path' => $lessonData['video_path'] ?? null, 'original_video_key' => $lessonData['original_video_key'] ?? null]);

        if ($course->isPublished()) {
            $payload = array_merge($lessonData, [
                'duration_seconds' => $lessonData['duration'] ?? 0,
                'is_preview' => $request->boolean('is_preview'),
                'sort_order' => $lessonData['sort_order'] ?? $lesson->sort_order,
                'status' => $lessonData['status'] ?? 'draft',
            ]);

            \Illuminate\Support\Facades\Log::info('[UPLOAD TRACE] SAVE DATABASE (ContentUpdate Record)');
            $contentUpdate = app(\App\Services\ContentUpdateService::class)->recordPendingUpdate(
                \App\Models\ContentUpdate::TYPE_LESSON,
                \App\Models\ContentUpdate::ACTION_UPDATE,
                $course->id,
                $lesson->id,
                $payload,
                $request->user()
            );

            if (($lessonData['type'] ?? null) === 'video') {
                if ($request->hasFile('video_file')) {
                    \Illuminate\Support\Facades\Log::info('[UPLOAD TRACE] DISPATCH HLS JOB (ContentUpdate Update Local)', ['content_update_id' => $contentUpdate->id]);
                    \App\Jobs\ConvertContentUpdateVideoToHLS::dispatch($contentUpdate);
                } elseif ($request->filled('s3_key')) {
                    $s3Key = (string) $request->input('s3_key');
                    if (app(\App\Services\AwsS3UploadService::class)->doesObjectExist($s3Key)) {
                        \Illuminate\Support\Facades\Log::info('[UPLOAD TRACE] DISPATCH HLS JOB (ContentUpdate Update S3 Object exists)', ['content_update_id' => $contentUpdate->id, 'key' => $s3Key]);
                        \App\Jobs\ConvertContentUpdateVideoToHLS::dispatch($contentUpdate);
                    }
                }
            }

            \Illuminate\Support\Facades\Log::info('[UPLOAD TRACE] RETURN RESPONSE (Update ContentUpdate)');
            return back()->with('success', 'Đã lưu bản cập nhật nội dung bài học. Video đang được xử lý HLS ngầm.');
        }

        \Illuminate\Support\Facades\Log::info('[UPLOAD TRACE] SAVE DATABASE (Update Lesson)');
        $lesson->update([
            ...$lessonData,
            'duration_seconds' => $lessonData['duration'] ?? 0,
            'is_preview' => $request->boolean('is_preview'),
            'sort_order' => $lessonData['sort_order'] ?? $lesson->sort_order,
            'status' => $lessonData['status'] ?? 'draft',
        ]);

        if ($lesson->type === 'video') {
            if ($request->hasFile('video_file')) {
                \Illuminate\Support\Facades\Log::info('[UPLOAD TRACE] DISPATCH HLS JOB (Update Local video_file)', ['lesson_id' => $lesson->id]);
                \App\Jobs\ConvertVideoToHLS::dispatch($lesson);
            } elseif ($request->filled('s3_key')) {
                $s3Key = (string) $request->input('s3_key');
                if (app(\App\Services\AwsS3UploadService::class)->doesObjectExist($s3Key)) {
                    \Illuminate\Support\Facades\Log::info('[UPLOAD TRACE] DISPATCH HLS JOB (Update S3 Object exists)', ['lesson_id' => $lesson->id, 'key' => $s3Key]);
                    \App\Jobs\ConvertVideoToHLS::dispatch($lesson);
                } else {
                    \Illuminate\Support\Facades\Log::info('[UPLOAD TRACE] S3 Object still uploading in background. HLS will be dispatched on S3 complete.', ['lesson_id' => $lesson->id, 'key' => $s3Key]);
                }
            }
        }

        $lesson->refresh();
        $this->syncAssignment($lesson, $validated);

        if ($lesson->type === 'quiz') {
            \Illuminate\Support\Facades\Log::info('[UPLOAD TRACE] RETURN RESPONSE (Update Quiz)');
            return redirect()
                ->route('instructor.courses.lessons.quiz.show', [$course, $lesson])
                ->with('success', 'Đã cập nhật bài quiz. Bạn có thể quản lý câu hỏi tại đây.');
        }

        \Illuminate\Support\Facades\Log::info('[UPLOAD TRACE] RETURN RESPONSE (Update Success)');
        return back()->with('success', 'Đã cập nhật bài học. Nếu có video mới, video đang được xử lý ngầm.');
    }

    public function destroyLesson(Course $course, Lesson $lesson): RedirectResponse
    {
        $this->authorizeLesson($course, $lesson);

        if ($course->isPublished()) {
            app(\App\Services\ContentUpdateService::class)->recordPendingUpdate(
                \App\Models\ContentUpdate::TYPE_LESSON,
                \App\Models\ContentUpdate::ACTION_DELETE,
                $course->id,
                $lesson->id,
                [],
                auth()->user()
            );

            return back()->with('success', 'Đã gửi yêu cầu xóa bài học. Yêu cầu sẽ áp dụng sau khi Admin duyệt.');
        }

        $this->deleteLessonFiles($lesson);
        $lesson->delete();

        return back()->with('success', 'Đã xóa bài học.');
    }

    public function updateContentUpdate(StoreLessonRequest $request, Course $course, ContentUpdate $contentUpdate): RedirectResponse
    {
        $this->authorizeCourse($course);
        abort_unless((int) $contentUpdate->course_id === (int) $course->id, 403);

        $validated = $request->validated();
        $lessonData = $this->lessonData($validated);
        $lessonData = $this->storeLessonDocument($request, $lessonData);
        $lessonData = $this->storeLessonVideo($request, $lessonData);

        $existingPayload = $contentUpdate->payload ?? [];
        $newPayload = array_merge($existingPayload, $lessonData, [
            'duration_seconds' => $lessonData['duration'] ?? ($existingPayload['duration'] ?? 0),
            'is_preview' => $request->boolean('is_preview'),
            'sort_order' => $lessonData['sort_order'] ?? ($existingPayload['sort_order'] ?? 0),
            'status' => $lessonData['status'] ?? 'draft',
        ]);

        $contentUpdate->update([
            'payload' => $newPayload,
            'status' => ContentUpdate::STATUS_DRAFT,
        ]);

        if (($request->hasFile('video_file') || $request->filled('s3_key')) && ($lessonData['type'] ?? null) === 'video') {
            \App\Jobs\ConvertContentUpdateVideoToHLS::dispatch($contentUpdate);
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

        if (($validated['type'] ?? null) !== 'video') {
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

        if (! in_array($validated['type'] ?? null, ['video', 'document', 'assignment'], true)) {
            unset($validated['content']);
        }

        return $validated;
    }

    private function storeLessonDocument(Request $request, array $validated, ?Lesson $lesson = null): array
    {
        if (! in_array($validated['type'] ?? null, ['document', 'assignment'], true) || ! $request->hasFile('document_file')) {
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

        if (($validated['type'] ?? null) !== 'video') {
            return $validated;
        }

        // Ưu tiên S3 Multipart Direct Upload
        if ($request->filled('s3_key')) {
            $s3Key = (string) $request->input('s3_key');
            $originalName = (string) ($request->input('video_original_name') ?: basename($s3Key));
            $mime = (string) ($request->input('video_mime') ?: 'video/mp4');
            $size = $request->input('video_size') ? (int) $request->input('video_size') : 0;
            $s3Exists = app(\App\Services\AwsS3UploadService::class)->doesObjectExist($s3Key);

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

    private function syncAssignment(Lesson $lesson, array $validated): void
    {
        if ($lesson->type !== 'assignment') {
            return;
        }

        $lesson->loadMissing('assignment');
        $description = trim((string) ($validated['content'] ?? ''));
        $existing = $lesson->assignment;

        $lesson->assignment()->updateOrCreate(
            ['lesson_id' => $lesson->id],
            [
                'course_id' => $lesson->course_id,
                'title' => $lesson->title,
                'description' => $description !== '' ? $description : $lesson->title,
                'instructions' => $description !== '' ? $description : null,
                'max_score' => $validated['assignment_max_score'] ?? $existing?->max_score ?? 100,
                'passing_score' => $validated['assignment_passing_score'] ?? $existing?->passing_score ?? 70,
                'due_days' => $validated['assignment_due_days'] ?? $existing?->due_days,
                'is_required' => true,
                'allowed_file_types' => $existing?->allowed_file_types ?? 'pdf,doc,docx,ppt,pptx,xls,xlsx,txt,zip,rar',
                'maximum_file_size' => $existing?->maximum_file_size ?? 10240,
            ],
        );
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
        $hlsDir = 'lesson-hls/' . $lesson->id;
        if (Storage::disk('local')->exists($hlsDir)) {
            Storage::disk('local')->deleteDirectory($hlsDir);
        }
    }

    private function lessonTypes(): array
    {
        return [
            'video' => 'Video',
            'document' => 'Tài liệu',
            'quiz' => 'Quiz',
            'assignment' => 'Bài tập',
        ];
    }

    private function lessonStatuses(): array
    {
        return [
            'draft' => 'Nháp',
            'published' => 'Đã sẵn sàng',
        ];
    }

    public function getHlsStatus(Course $course): \Illuminate\Http\JsonResponse
    {
        $this->authorizeCourse($course);

        $lessons = Lesson::where('course_id', $course->id)
            ->where('type', 'video')
            ->select(['id', 'processing_status', 'upload_status', 'hls_manifest_key', 'video_path', 'original_video_key'])
            ->get();

        $updates = ContentUpdate::where('course_id', $course->id)
            ->where('type', ContentUpdate::TYPE_LESSON)
            ->get();

        $statuses = [];
        foreach ($lessons as $lesson) {
            $isReady = $lesson->isHlsReady();
            $isProcessing = $lesson->isProcessing();
            $isFailed = $lesson->hasFailedProcessing();

            $statuses['lesson_' . $lesson->id] = [
                'id' => $lesson->id,
                'processing_status' => $lesson->processing_status,
                'is_ready' => $isReady,
                'is_processing' => $isProcessing,
                'is_failed' => $isFailed,
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
            $isFailed = $pStatus === 'failed' && !$isReady;
            $isProcessing = in_array($pStatus, ['processing', 'pending'], true) && !$isReady;

            $key = $update->action === ContentUpdate::ACTION_CREATE 
                ? 'update_' . $update->id 
                : 'lesson_' . ($update->entity_id ?? $update->id);

            $statuses[$key] = [
                'id' => $update->entity_id ?? $update->id,
                'update_id' => $update->id,
                'processing_status' => $pStatus,
                'is_ready' => $isReady,
                'is_processing' => $isProcessing,
                'is_failed' => $isFailed,
                'status_message' => $isReady 
                    ? 'Video đã được xử lý bảo mật thành công.' 
                    : ($isFailed 
                        ? 'Video xử lý bảo mật thất bại.' 
                        : ($isProcessing 
                            ? 'Video đang trong quá trình xử lý bảo mật. Vui lòng chờ trong giây lát.' 
                            : 'Đang chờ xử lý')),
            ];
        }

        $hasIncompleteHls = $course->hasIncompleteHlsVideos();
        $hasFailed = false;
        $hasProcessing = false;

        foreach ($statuses as $st) {
            if ($st['is_failed']) {
                $hasFailed = true;
            } elseif ($st['is_processing'] || !$st['is_ready']) {
                $hasProcessing = true;
            }
        }

        $commonState = 'completed';
        $commonMessage = 'Video đã được xử lý bảo mật thành công. Bạn có thể bấm gửi duyệt.';

        if ($hasFailed) {
            $commonState = 'failed';
            $commonMessage = 'Video chưa xử lý hoàn tất. Vui lòng chờ quá trình xử lý video hoàn tất trước khi gửi duyệt.';
        } elseif ($hasProcessing || $hasIncompleteHls) {
            $commonState = 'processing';
            $commonMessage = 'Video đang trong quá trình xử lý bảo mật, xử lý xong bạn có thể bấm gửi duyệt.';
        }

        return response()->json([
            'statuses' => $statuses,
            'has_incomplete_hls' => $hasIncompleteHls,
            'can_submit' => !$hasIncompleteHls && !$hasFailed && !$hasProcessing,
            'common_state' => $commonState,
            'common_message' => $commonMessage,
        ]);
    }
}

