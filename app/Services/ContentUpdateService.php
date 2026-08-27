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
            $section = CourseSection::find($update->entity_id);
            if ($section) {
                $section->update(array_intersect_key($payload, array_flip(['title', 'description', 'sort_order'])));
            }
            $chapter = Chapter::find($update->entity_id);
            if ($chapter) {
                $chapter->update(array_intersect_key($payload, array_flip(['title', 'sort_order'])));
            }
        } elseif ($update->action === ContentUpdate::ACTION_DELETE && $update->entity_id) {
            app(HistoricalQuizDeletionGuard::class)->assertSectionCanBeHardDeleted($update->entity_id);
            CourseSection::destroy($update->entity_id);
            Chapter::destroy($update->entity_id);
        } elseif ($update->action === ContentUpdate::ACTION_REORDER) {
            $orders = $payload['chapter_orders'] ?? [];
            foreach ($orders as $order) {
                if (isset($order['id'], $order['sort_order'])) {
                    CourseSection::where('id', $order['id'])->update(['sort_order' => $order['sort_order']]);
                    Chapter::where('id', $order['id'])->update(['sort_order' => $order['sort_order']]);
                }
            }
        }
    }

    private function applyLessonUpdate(ContentUpdate $update, array $payload): void
    {
        if ($update->action === ContentUpdate::ACTION_CREATE) {
            $secId = $payload['section_id'] ?? null;

            if ($secId && ! CourseSection::where('id', $secId)->exists()) {
                $chapterUpdate = ContentUpdate::find($secId);
                if ($chapterUpdate && $chapterUpdate->entity_id) {
                    $secId = $chapterUpdate->entity_id;
                } else {
                    $firstSection = CourseSection::where('course_id', $update->course_id)->orderBy('sort_order')->first();
                    $secId = $firstSection ? $firstSection->id : null;
                }
                $payload['section_id'] = $secId;
            }

            $chapId = $payload['chapter_id'] ?? null;
            if ($chapId && ! Chapter::where('id', $chapId)->exists()) {
                if ($secId && Chapter::where('id', $secId)->exists()) {
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
            $lesson = Lesson::find($update->entity_id);
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
            app(HistoricalQuizDeletionGuard::class)->assertLessonCanBeHardDeleted($update->entity_id);
            Lesson::destroy($update->entity_id);
        } elseif ($update->action === ContentUpdate::ACTION_REORDER) {
            $orders = $payload['lesson_orders'] ?? [];
            foreach ($orders as $order) {
                if (isset($order['id'], $order['sort_order'])) {
                    Lesson::where('id', $order['id'])->update(['sort_order' => $order['sort_order']]);
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

        $activeUpdates = ContentUpdate::where('course_id', $course->id)
            ->whereIn('status', [ContentUpdate::STATUS_DRAFT, ContentUpdate::STATUS_PENDING, ContentUpdate::STATUS_REJECTED, ContentUpdate::STATUS_APPROVED])
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
            }
        }

        // Handle lesson updates
        $lessonUpdates = $activeUpdates->where('type', ContentUpdate::TYPE_LESSON);

        foreach ($lessonUpdates as $lUpdate) {
            $payload = $lUpdate->payload ?? [];
            $secId = $payload['section_id'] ?? $payload['chapter_id'] ?? null;

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
