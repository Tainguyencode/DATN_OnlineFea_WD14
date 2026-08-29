<?php

namespace App\Services;

use App\Jobs\ConvertVideoToHLS;
use App\Models\Chapter;
use App\Models\ContentUpdate;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\QuestionVersion;
use App\Models\Quiz;
use App\Models\QuizVersion;
use App\Models\User;
use App\Models\VideoModeration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ContentUpdateService
{
    /**
     * Tạo một record ContentUpdate ở trạng thái draft (mặc định) hoặc pending.
     */
    public function recordPendingUpdate(
        string $type,
        string $action,
        int $courseId,
        ?int $entityId,
        array $payload,
        User $user,
        string $status = ContentUpdate::STATUS_DRAFT
    ): ContentUpdate {
        if (! in_array($status, [ContentUpdate::STATUS_DRAFT, ContentUpdate::STATUS_PENDING], true)) {
            throw ValidationException::withMessages([
                'content_update' => 'Bản cập nhật mới chỉ có thể bắt đầu ở trạng thái nháp hoặc chờ duyệt.',
            ]);
        }

        return DB::transaction(function () use ($type, $action, $courseId, $entityId, $payload, $user, $status): ContentUpdate {
            Course::query()->lockForUpdate()->findOrFail($courseId);

            return ContentUpdate::create([
                'type' => $type,
                'action' => $action,
                'course_id' => $courseId,
                'entity_id' => $entityId,
                'payload' => $payload,
                'status' => $status,
                'created_by' => $user->id,
                'submitted_at' => $status === ContentUpdate::STATUS_PENDING ? now() : null,
            ]);
        });
    }

    /**
     * Update a staged payload while it is still a draft.
     *
     * Once an update has been submitted, its payload is immutable. This
     * boundary is deliberately server-side so UI state cannot be bypassed.
     *
     * @param  array<string, mixed>  $changes
     */
    public function updateDraft(ContentUpdate $update, array $changes): ContentUpdate
    {
        return DB::transaction(function () use ($update, $changes): ContentUpdate {
            $locked = ContentUpdate::query()->lockForUpdate()->findOrFail($update->id);
            $this->assertDraft($locked, 'Thay đổi đã được gửi duyệt và không thể chỉnh sửa.');

            $locked->update([
                'payload' => array_merge($locked->payload ?? [], $changes),
            ]);

            return $locked->fresh();
        });
    }

    /**
     * Delete a staged draft and return its payload so callers can clean up
     * temporary files only after the state transition succeeds.
     *
     * @return array<string, mixed>
     */
    public function deleteDraft(ContentUpdate $update): array
    {
        return DB::transaction(function () use ($update): array {
            $locked = ContentUpdate::query()->lockForUpdate()->findOrFail($update->id);
            $this->assertDraft($locked, 'Thay đổi đã được gửi duyệt và không thể xóa.');
            $payload = $locked->payload ?? [];
            $locked->delete();

            return $payload;
        });
    }

    /**
     * Creates (or reuses) the one active revision for a rejected proposal.
     * The rejected record remains immutable audit history.
     */
    public function createRevisionFromRejected(ContentUpdate $rejected, User $actor): ContentUpdate
    {
        return DB::transaction(function () use ($rejected, $actor): ContentUpdate {
            $rejected = ContentUpdate::query()->lockForUpdate()->findOrFail($rejected->id);
            if (! $rejected->isRejected() || (int) $rejected->created_by !== (int) $actor->id) {
                throw ValidationException::withMessages([
                    'content_update' => 'Chỉ tác giả mới có thể tạo bản chỉnh sửa từ thay đổi đã bị từ chối.',
                ]);
            }

            $existing = ContentUpdate::query()
                ->where('course_id', $rejected->course_id)
                ->where('type', $rejected->type)
                ->where('action', $rejected->action)
                ->where('entity_id', $rejected->entity_id)
                ->where('created_by', $actor->id)
                ->where('status', ContentUpdate::STATUS_DRAFT)
                ->lockForUpdate()
                ->latest('id')
                ->first();

            if ($existing) {
                return $existing;
            }

            return ContentUpdate::create([
                'type' => $rejected->type,
                'action' => $rejected->action,
                'course_id' => $rejected->course_id,
                'entity_id' => $rejected->entity_id,
                'payload' => $rejected->payload ?? [],
                'status' => ContentUpdate::STATUS_DRAFT,
                'created_by' => $actor->id,
            ]);
        });
    }

    /**
     * Phê duyệt một ContentUpdate và áp dụng dữ liệu vào DB thật.
     * BẮT BUỘC CHẠY TRONG DB::transaction()
     */
    public function applyApprovedUpdate(ContentUpdate $update, User $admin): void
    {
        DB::transaction(function () use ($update, $admin) {
            $update = ContentUpdate::query()->lockForUpdate()->findOrFail($update->id);
            $payload = $update->payload ?? [];

            // A second approval is only idempotent for an already activated
            // quiz candidate. Every other terminal state must be immutable.
            if (! $update->isPending() && ! ($update->isApproved() && $update->type === ContentUpdate::TYPE_QUIZ)) {
                throw ValidationException::withMessages([
                    'content_update' => 'Chỉ thay đổi đang chờ duyệt mới có thể được phê duyệt.',
                ]);
            }

            if ($update->type === ContentUpdate::TYPE_QUIZ) {
                $this->approveQuizCandidate($update, $payload, $admin);
            } else {
                switch ($update->type) {
                    case ContentUpdate::TYPE_COURSE:
                        $this->applyCourseUpdate($update, $payload);
                        break;

                    case ContentUpdate::TYPE_CHAPTER:
                        $this->applyChapterUpdate($update, $payload);
                        break;

                    case ContentUpdate::TYPE_LESSON:
                        $this->applyLessonUpdate($update, $payload);
                        break;

                    default:
                        abort(422, 'Loại cập nhật nội dung không được hỗ trợ.');
                }

                $update->update([
                    'status' => ContentUpdate::STATUS_APPROVED,
                    'reviewed_by' => $admin->id,
                    'reviewed_at' => now(),
                ]);
            }

            // Kiểm tra xem khóa học còn bản cập nhật pending nào khác không, nếu không thì đưa trạng thái về published
            $remainingPending = ContentUpdate::where('course_id', $update->course_id)
                ->where('status', ContentUpdate::STATUS_PENDING)
                ->exists();

            if (! $remainingPending) {
                Course::where('id', $update->course_id)->update([
                    'status' => Course::STATUS_PUBLISHED,
                    'is_published' => true,
                ]);
            }
        });
    }

    /**
     * Từ chối một ContentUpdate kèm lý do.
     */
    public function rejectUpdate(ContentUpdate $update, User $admin, string $reason): void
    {
        DB::transaction(function () use ($update, $admin, $reason) {
            $update = ContentUpdate::query()->lockForUpdate()->findOrFail($update->id);
            if (! $update->isPending()) {
                throw ValidationException::withMessages([
                    'content_update' => 'Chỉ thay đổi đang chờ duyệt mới có thể bị từ chối.',
                ]);
            }

            $update->update([
                'status' => ContentUpdate::STATUS_REJECTED,
                'rejection_reason' => $reason,
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
            ]);

            // Nếu khóa học không còn pending update nào nữa thì chuyển status về rejected_update
            $remainingPending = ContentUpdate::where('course_id', $update->course_id)
                ->where('status', ContentUpdate::STATUS_PENDING)
                ->exists();

            if (! $remainingPending) {
                $course = Course::find($update->course_id);
                if ($course && $course->is_published) {
                    $course->update(['status' => Course::STATUS_REJECTED_UPDATE]);
                }
            }
        });
    }

    private function assertDraft(ContentUpdate $update, string $message): void
    {
        if (! $update->isDraft()) {
            throw ValidationException::withMessages(['content_update' => $message]);
        }
    }

    private function applyCourseUpdate(ContentUpdate $update, array $payload): void
    {
        $course = Course::find($update->course_id);
        if ($course) {
            $course->update(array_intersect_key($payload, array_flip([
                'title', 'short_description', 'description', 'objectives',
                'thumbnail', 'preview_video', 'price', 'discount_price',
                'level', 'language', 'category_id',
            ])));
        }
    }

    private function approveQuizCandidate(ContentUpdate $update, array $payload, User $admin): void
    {
        $quizId = (int) ($payload['quiz_id'] ?? 0);
        $versionId = (int) ($payload['quiz_version_id'] ?? 0);
        $quiz = Quiz::query()->lockForUpdate()->with('lesson')->find($quizId);
        $version = QuizVersion::query()->lockForUpdate()->find($versionId);

        abort_unless($quiz && $version, 422, 'Không tìm thấy ứng viên Quiz cần duyệt.');
        abort_unless((int) $update->entity_id === $quizId, 422, 'ContentUpdate không khớp Quiz ứng viên.');
        abort_unless((int) $quiz->lesson?->course_id === (int) $update->course_id, 422, 'Quiz không thuộc khóa học của ContentUpdate.');

        $versioning = app(QuizVersioningService::class);
        $versioning->assertVersionBelongsToQuiz($quiz, $version);

        if ($update->isApproved()) {
            $this->activateApprovedQuizCandidate($update, $payload, $quiz, $version);

            return;
        }

        abort_unless($update->isPending(), 422, 'Chỉ ContentUpdate đang chờ duyệt mới có thể kích hoạt Quiz.');
        abort_unless((int) $quiz->current_draft_version_id === $versionId, 422, 'Ứng viên không còn là bản nháp Quiz hiện tại.');
        abort_unless($version->status === QuizVersion::STATUS_DRAFT, 422, 'Ứng viên Quiz không còn ở trạng thái bản nháp.');

        $validation = app(QuizContentService::class)->validateQuizVersion($version);
        abort_unless($validation['is_complete'], 422, implode(' ', $validation['errors']));

        $update->update([
            'status' => ContentUpdate::STATUS_APPROVED,
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
        ]);

        $this->activateApprovedQuizCandidate($update, $payload, $quiz, $version);
    }

    private function activateApprovedQuizCandidate(ContentUpdate $update, array $payload, Quiz $quiz, QuizVersion $candidate): void
    {
        abort_unless($update->isApproved(), 422, 'Quiz candidate must be approved before activation.');

        if ((int) $quiz->current_published_version_id === (int) $candidate->id
            && $candidate->status === QuizVersion::STATUS_PUBLISHED
            && $quiz->current_draft_version_id === null) {
            return;
        }

        abort_unless((int) $quiz->current_draft_version_id === (int) $candidate->id, 422, 'Ứng viên không còn là bản nháp Quiz hiện tại.');
        abort_unless($candidate->status === QuizVersion::STATUS_DRAFT, 422, 'Ứng viên Quiz không còn ở trạng thái bản nháp.');

        $validation = app(QuizContentService::class)->validateQuizVersion($candidate);
        abort_unless($validation['is_complete'], 422, implode(' ', $validation['errors']));

        $current = $quiz->current_published_version_id
            ? QuizVersion::query()->lockForUpdate()->findOrFail($quiz->current_published_version_id)
            : null;
        if ($current) {
            abort_unless((int) $current->quiz_id === (int) $quiz->id, 422, 'Published Quiz pointer is invalid.');
            abort_unless($current->status === QuizVersion::STATUS_PUBLISHED, 422, 'Published Quiz pointer must reference a published version.');
        }

        $mappings = $candidate->questionMappings()->lockForUpdate()->get();
        QuestionVersion::query()
            ->whereIn('id', $mappings->pluck('question_version_id'))
            ->where('status', QuestionVersion::STATUS_DRAFT)
            ->update([
                'status' => QuestionVersion::STATUS_PUBLISHED,
                'published_at' => now(),
            ]);

        $current?->update(['status' => QuizVersion::STATUS_SUPERSEDED]);
        $candidate->update([
            'status' => QuizVersion::STATUS_PUBLISHED,
            'published_at' => now(),
        ]);

        $quizUpdates = [
            'current_published_version_id' => $candidate->id,
            'current_draft_version_id' => null,
        ];
        if (array_key_exists('desired_is_active', $payload)) {
            $quizUpdates['is_active'] = (bool) $payload['desired_is_active'];
        }
        $quiz->update($quizUpdates);

        $update->update([
            'payload' => [
                ...$payload,
                'activation_deferred' => false,
                'activated_at' => now()->toIso8601String(),
            ],
        ]);
    }

    private function applyChapterUpdate(ContentUpdate $update, array $payload): void
    {
        if ($update->action === ContentUpdate::ACTION_CREATE) {
            $section = CourseSection::create([
                'course_id' => $update->course_id,
                'title' => $payload['title'] ?? 'Chương mới',
                'description' => $payload['description'] ?? null,
                'sort_order' => $payload['sort_order'] ?? 0,
            ]);
            // Tạo thêm record trong chapters nếu hệ thống dùng cả 2
            Chapter::create([
                'course_id' => $update->course_id,
                'title' => $payload['title'] ?? 'Chương mới',
                'sort_order' => $payload['sort_order'] ?? 0,
            ]);

            $update->update(['entity_id' => $section->id]);
        } elseif ($update->action === ContentUpdate::ACTION_UPDATE && $update->entity_id) {
            $section = CourseSection::query()
                ->where('course_id', $update->course_id)
                ->find($update->entity_id);
            if ($section) {
                $section->update(array_intersect_key($payload, array_flip(['title', 'description', 'sort_order'])));
            }

            // Legacy chapters and new course sections have independent IDs.
            // Prefer an explicit mapping captured when the update was staged;
            // otherwise derive legacy chapter IDs from lessons in this course.
            $chapterQuery = Chapter::query()->where('course_id', $update->course_id);
            $legacyChapterId = $payload['legacy_chapter_id'] ?? null;
            if ($legacyChapterId) {
                $chapterQuery->whereKey($legacyChapterId);
            } elseif ($section) {
                $chapterIds = Lesson::query()
                    ->where('course_id', $update->course_id)
                    ->where('section_id', $section->id)
                    ->whereNotNull('chapter_id')
                    ->pluck('chapter_id');
                if ($chapterIds->isNotEmpty()) {
                    $chapterQuery->whereIn('id', $chapterIds->all());
                } else {
                    $chapterQuery->whereRaw('1 = 0');
                }
            } else {
                $chapterQuery->whereRaw('1 = 0');
            }

            $chapter = $chapterQuery->first();
            if ($chapter) {
                $chapter->update(array_intersect_key($payload, array_flip(['title', 'sort_order'])));
            }
        } elseif ($update->action === ContentUpdate::ACTION_DELETE && $update->entity_id) {
            $section = CourseSection::query()
                ->where('course_id', $update->course_id)
                ->find($update->entity_id);
            $legacyChapterId = $payload['legacy_chapter_id'] ?? null;
            if (! $legacyChapterId && $section) {
                $legacyChapterId = Lesson::query()
                    ->where('course_id', $update->course_id)
                    ->where('section_id', $section->id)
                    ->whereNotNull('chapter_id')
                    ->value('chapter_id');
            }

            if ($section) {
                app(HistoricalQuizDeletionGuard::class)->assertSectionCanBeHardDeleted($section);
                $section->delete();
            }

            if ($legacyChapterId) {
                Chapter::query()
                    ->where('course_id', $update->course_id)
                    ->whereKey($legacyChapterId)
                    ->delete();
            }
        } elseif ($update->action === ContentUpdate::ACTION_REORDER) {
            $orders = $payload['chapter_orders'] ?? [];
            foreach ($orders as $order) {
                if (isset($order['sort_order'])) {
                    $sectionId = $order['section_id'] ?? $order['id'] ?? null;
                    $chapterId = $order['chapter_id'] ?? $order['legacy_chapter_id'] ?? null;

                    if ($sectionId) {
                        CourseSection::where('course_id', $update->course_id)
                            ->where('id', $sectionId)
                            ->update(['sort_order' => $order['sort_order']]);
                    }
                    if ($chapterId) {
                        Chapter::where('course_id', $update->course_id)
                            ->where('id', $chapterId)
                            ->update(['sort_order' => $order['sort_order']]);
                    }
                }
            }
        }
    }

    private function applyLessonUpdate(ContentUpdate $update, array $payload): void
    {
        if ($update->action === ContentUpdate::ACTION_CREATE) {
            $secId = $payload['section_id'] ?? null;

            if ($secId && ! CourseSection::where('course_id', $update->course_id)->where('id', $secId)->exists()) {
                $chapterUpdate = ContentUpdate::query()
                    ->whereKey($secId)
                    ->where('course_id', $update->course_id)
                    ->where('type', ContentUpdate::TYPE_CHAPTER)
                    ->first();
                if ($chapterUpdate && $chapterUpdate->entity_id) {
                    $secId = $chapterUpdate->entity_id;
                } else {
                    $firstSection = CourseSection::where('course_id', $update->course_id)->orderBy('sort_order')->first();
                    $secId = $firstSection ? $firstSection->id : null;
                }
                $payload['section_id'] = $secId;
            }

            $chapId = $payload['chapter_id'] ?? null;
            if (! $secId && $chapId) {
                $secId = Lesson::query()
                    ->where('course_id', $update->course_id)
                    ->where('chapter_id', $chapId)
                    ->whereNotNull('section_id')
                    ->value('section_id');
                if ($secId) {
                    $payload['section_id'] = $secId;
                }
            }
            if ($chapId && ! Chapter::where('course_id', $update->course_id)->where('id', $chapId)->exists()) {
                if ($secId && Chapter::where('course_id', $update->course_id)->where('id', $secId)->exists()) {
                    $chapId = $secId;
                } else {
                    $matchingChapter = Chapter::where('course_id', $update->course_id)->orderBy('sort_order')->first();
                    $chapId = $matchingChapter ? $matchingChapter->id : null;
                }
                $payload['chapter_id'] = $chapId;
            }

            $lesson = Lesson::create(array_merge([
                'course_id' => $update->course_id,
                'content_version' => 1,
            ], array_intersect_key($payload, array_flip([
                'section_id', 'chapter_id', 'title', 'type', 'video_url',
                'video_path', 'original_video_key', 'hls_manifest_key',
                'upload_status', 'processing_status',
                'video_original_name', 'video_mime', 'video_size',
                'content', 'document_file', 'duration', 'duration_seconds',
                'is_preview', 'is_required', 'sort_order', 'status', 'attachments',
            ]))));

            app(CurriculumLessonService::class)->syncAssignment($lesson, $payload);

            $oldHlsDir = 'lesson-hls/update_'.$update->id;
            $newHlsDir = 'lesson-hls/'.$lesson->id;
            if (Storage::disk('local')->exists($oldHlsDir)) {
                if (Storage::disk('local')->exists($newHlsDir)) {
                    Storage::disk('local')->deleteDirectory($newHlsDir);
                }
                Storage::disk('local')->move($oldHlsDir, $newHlsDir);
                $lesson->update(['video_path' => $newHlsDir.'/playlist.m3u8']);
            } elseif ($lesson->type === Lesson::TYPE_VIDEO && ($lesson->original_video_key || ($lesson->video_path && Str::endsWith($lesson->video_path, '.mp4')))) {
                ConvertVideoToHLS::dispatch($lesson);
            }

            if (! empty($payload['ai_moderation']) && is_array($payload['ai_moderation'])) {
                VideoModeration::updateOrCreate(
                    ['lesson_id' => $lesson->id],
                    $payload['ai_moderation']
                );
            }

            // Gửi thông báo cho toàn bộ học viên đang ghi danh nếu khóa học đã xuất bản
            $course = Course::find($update->course_id);
            if ($course && ($course->is_published || $course->status === Course::STATUS_PUBLISHED || $course->status === Course::STATUS_PENDING_UPDATE)) {
                $isHlsVideo = $lesson->type === Lesson::TYPE_VIDEO && ($lesson->original_video_key || $lesson->video_path);
                $isHlsReady = ! $isHlsVideo || $lesson->processing_status === 'completed' || $lesson->isHlsReady();
                if ($isHlsReady) {
                    app(NotificationService::class)->notifyCourseLessonCreated($course, $lesson);
                }
            }
        } elseif ($update->action === ContentUpdate::ACTION_UPDATE && $update->entity_id) {
            $lesson = Lesson::query()
                ->where('course_id', $update->course_id)
                ->find($update->entity_id);
            if ($lesson) {
                $hasMediaChange = isset($payload['video_path']) || isset($payload['original_video_key']) || isset($payload['video_url']) || isset($payload['document_file']);

                $updateData = array_intersect_key($payload, array_flip([
                    'section_id', 'chapter_id', 'title', 'type', 'video_url',
                    'video_path', 'original_video_key', 'hls_manifest_key',
                    'upload_status', 'processing_status',
                    'video_original_name', 'video_mime', 'video_size',
                    'content', 'document_file', 'duration', 'duration_seconds',
                    'is_preview', 'is_required', 'sort_order', 'status', 'attachments',
                ]));

                if ($hasMediaChange) {
                    $updateData['content_version'] = (int) $lesson->content_version + 1;
                }

                $lesson->update($updateData);
                app(CurriculumLessonService::class)->syncAssignment($lesson, $payload);

                if (! empty($payload['ai_moderation']) && is_array($payload['ai_moderation'])) {
                    VideoModeration::updateOrCreate(
                        ['lesson_id' => $lesson->id],
                        $payload['ai_moderation']
                    );
                } elseif ($hasMediaChange) {
                    $lesson->videoModeration()?->delete();
                }

                $oldHlsDir = 'lesson-hls/update_'.$update->id;
                $newHlsDir = 'lesson-hls/'.$lesson->id;
                if (Storage::disk('local')->exists($oldHlsDir)) {
                    if (Storage::disk('local')->exists($newHlsDir)) {
                        Storage::disk('local')->deleteDirectory($newHlsDir);
                    }
                    Storage::disk('local')->move($oldHlsDir, $newHlsDir);
                    $lesson->update(['video_path' => $newHlsDir.'/playlist.m3u8']);
                } elseif ($lesson->type === Lesson::TYPE_VIDEO && ($lesson->original_video_key || ($lesson->video_path && Str::endsWith($lesson->video_path, '.mp4')))) {
                    ConvertVideoToHLS::dispatch($lesson);
                }

                // Gửi thông báo cho toàn bộ học viên đang ghi danh nếu khóa học đã xuất bản
                $course = Course::find($update->course_id);
                if ($course && ($course->is_published || $course->status === Course::STATUS_PUBLISHED || $course->status === Course::STATUS_PENDING_UPDATE)) {
                    $isVideoChange = isset($payload['video_path']) || isset($payload['original_video_key']) || isset($payload['video_url']);
                    $isHlsVideo = $lesson->type === Lesson::TYPE_VIDEO && ($lesson->original_video_key || $lesson->video_path);
                    $isHlsReady = ! $isHlsVideo || $lesson->processing_status === 'completed' || $lesson->isHlsReady();
                    if ($isHlsReady) {
                        app(NotificationService::class)->notifyCourseLessonUpdated($course, $lesson, $isVideoChange);
                    }
                }
            }
        } elseif ($update->action === ContentUpdate::ACTION_DELETE && $update->entity_id) {
            $lesson = Lesson::query()
                ->where('course_id', $update->course_id)
                ->find($update->entity_id);
            if ($lesson) {
                app(HistoricalQuizDeletionGuard::class)->assertLessonCanBeHardDeleted($lesson);
                $lesson->delete();
            }
        } elseif ($update->action === ContentUpdate::ACTION_REORDER) {
            $orders = $payload['lesson_orders'] ?? [];
            foreach ($orders as $order) {
                if (isset($order['id'], $order['sort_order'])) {
                    Lesson::where('course_id', $update->course_id)
                        ->where('id', $order['id'])
                        ->update(['sort_order' => $order['sort_order']]);
                }
            }
        }
    }

    /**
     * Merge published course sections and lessons with active ContentUpdate records (draft, pending, rejected).
     */
    public function mergeCurriculumWithUpdates(Course $course): Collection
    {
        $course->load([
            'courseSections.lessons' => fn ($q) => $q->orderBy('sort_order')->with(['videoModeration', 'assignment']),
            'chapters.lessons' => fn ($q) => $q->orderBy('sort_order')->with(['videoModeration', 'assignment']),
        ]);

        $sections = $course->courseSections->isNotEmpty()
            ? $course->courseSections
            : $course->chapters;

        // Only draft/pending records are candidate curriculum. Approved and rejected
        // records are history: the former has already been applied to the real lesson
        // and the latter must not override a later accepted/re-uploaded video.
        $activeUpdates = ContentUpdate::where('course_id', $course->id)
            ->whereIn('status', [ContentUpdate::STATUS_DRAFT, ContentUpdate::STATUS_PENDING])
            ->orderBy('id')
            ->get();

        if ($activeUpdates->isEmpty()) {
            return $sections;
        }

        // Handle section updates if any
        $chapterUpdates = $activeUpdates->where('type', ContentUpdate::TYPE_CHAPTER);
        foreach ($chapterUpdates as $cUpdate) {
            if ($cUpdate->action === ContentUpdate::ACTION_CREATE) {
                $payload = $cUpdate->payload ?? [];
                $newSection = new CourseSection([
                    'course_id' => $course->id,
                    'title' => $payload['title'] ?? 'Chương mới (Nháp)',
                    'description' => $payload['description'] ?? null,
                    'sort_order' => $payload['sort_order'] ?? $sections->count(),
                ]);
                $newSection->id = $cUpdate->id;
                $newSection->setRelation('lessons', collect());
                $newSection->draft_update = $cUpdate;
                $newSection->update_status = $cUpdate->status;
                $sections->push($newSection);
            } elseif ($cUpdate->entity_id) {
                $section = $sections->first(fn ($candidate) => (string) $candidate->id === (string) $cUpdate->entity_id);

                if ($section) {
                    $payload = $cUpdate->payload ?? [];
                    if ($cUpdate->action === ContentUpdate::ACTION_UPDATE) {
                        foreach ($payload as $key => $value) {
                            if (in_array($key, ['title', 'description', 'sort_order'], true)) {
                                $section->{$key} = $value;
                            }
                        }
                    } elseif ($cUpdate->action === ContentUpdate::ACTION_DELETE) {
                        $section->is_pending_deletion = true;
                    }

                    $section->draft_update = $cUpdate;
                    $section->update_status = $cUpdate->status;
                }
            }
        }

        // Handle lesson updates
        $lessonUpdates = $activeUpdates->where('type', ContentUpdate::TYPE_LESSON);

        foreach ($lessonUpdates as $lUpdate) {
            $payload = $lUpdate->payload ?? [];
            $secId = $payload['section_id'] ?? null;
            if (! $secId && ! empty($payload['chapter_id'])) {
                $secId = Lesson::query()
                    ->where('course_id', $course->id)
                    ->where('chapter_id', $payload['chapter_id'])
                    ->whereNotNull('section_id')
                    ->value('section_id');
            }
            $secId ??= $payload['chapter_id'] ?? null;

            if ($lUpdate->action === ContentUpdate::ACTION_CREATE) {
                $draftLesson = new Lesson([
                    'course_id' => $course->id,
                    'section_id' => $secId,
                    'chapter_id' => $secId,
                    'title' => $payload['title'] ?? 'Bài học mới',
                    'type' => $payload['type'] ?? Lesson::TYPE_VIDEO,
                    'video_url' => $payload['video_url'] ?? null,
                    'video_path' => $payload['video_path'] ?? null,
                    'original_video_key' => $payload['original_video_key'] ?? null,
                    'hls_manifest_key' => $payload['hls_manifest_key'] ?? null,
                    'upload_status' => $payload['upload_status'] ?? 'pending',
                    'processing_status' => $payload['processing_status'] ?? 'pending',
                    'video_original_name' => $payload['video_original_name'] ?? null,
                    'video_mime' => $payload['video_mime'] ?? null,
                    'video_size' => $payload['video_size'] ?? null,
                    'content' => $payload['content'] ?? null,
                    'document_file' => $payload['document_file'] ?? null,
                    'duration' => $payload['duration'] ?? $payload['duration_seconds'] ?? 0,
                    'duration_seconds' => $payload['duration_seconds'] ?? $payload['duration'] ?? 0,
                    'is_preview' => ! empty($payload['is_preview']),
                    'sort_order' => $payload['sort_order'] ?? 999,
                    'status' => $payload['status'] ?? Lesson::STATUS_DRAFT,
                ]);
                $draftLesson->id = $lUpdate->id;
                $draftLesson->draft_update = $lUpdate;
                $draftLesson->update_status = $lUpdate->status;
                $draftLesson->is_draft_create = true;

                if (! empty($payload['ai_moderation']) && is_array($payload['ai_moderation'])) {
                    $draftLesson->setRelation('videoModeration', new VideoModeration($payload['ai_moderation']));
                } else {
                    $draftLesson->setRelation('videoModeration', null);
                }

                // Attach to matching section
                $matchedSection = $sections->first(function ($s) use ($secId) {
                    if ($secId && (string) $s->id === (string) $secId) {
                        return true;
                    }
                    if (isset($s->draft_update) && $s->draft_update->id == $secId) {
                        return true;
                    }

                    return false;
                }) ?? $sections->first();

                if ($matchedSection) {
                    $matchedSection->lessons->push($draftLesson);
                }
            } elseif ($lUpdate->action === ContentUpdate::ACTION_UPDATE && $lUpdate->entity_id) {
                // Override existing lesson
                foreach ($sections as $section) {
                    $existingLesson = $section->lessons->first(fn ($l) => (string) $l->id === (string) $lUpdate->entity_id);
                    if ($existingLesson) {
                        foreach ($payload as $key => $val) {
                            if (in_array($key, [
                                'title', 'type', 'video_url', 'video_path', 'original_video_key',
                                'hls_manifest_key', 'upload_status', 'processing_status',
                                'video_original_name', 'video_mime', 'video_size',
                                'content', 'document_file', 'duration', 'duration_seconds',
                                'is_preview', 'sort_order', 'status',
                            ], true)) {
                                $existingLesson->{$key} = $val;
                            }
                        }
                        $existingLesson->draft_update = $lUpdate;
                        $existingLesson->update_status = $lUpdate->status;
                        $existingLesson->is_draft_update = true;

                        if (! empty($payload['ai_moderation']) && is_array($payload['ai_moderation'])) {
                            $existingLesson->setRelation('videoModeration', new VideoModeration($payload['ai_moderation']));
                        } else {
                            $existingLesson->setRelation('videoModeration', null);
                        }
                        break;
                    }
                }
            } elseif ($lUpdate->action === ContentUpdate::ACTION_DELETE && $lUpdate->entity_id) {
                foreach ($sections as $section) {
                    $existingLesson = $section->lessons->firstWhere('id', $lUpdate->entity_id);
                    if ($existingLesson) {
                        $existingLesson->draft_update = $lUpdate;
                        $existingLesson->update_status = $lUpdate->status;
                        $existingLesson->is_pending_deletion = true;
                        break;
                    }
                }
            }
        }

        // Sort lessons inside each section
        foreach ($sections as $section) {
            $sorted = $section->lessons->sortBy('sort_order')->values();
            $section->setRelation('lessons', $sorted);
        }

        return $sections->sortBy('sort_order')->values();
    }
}
