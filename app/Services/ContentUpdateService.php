<?php

namespace App\Services;

use App\Models\Chapter;
use App\Models\ContentUpdate;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Support\Facades\DB;

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
            $payload = $update->payload ?? [];

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
            }

            $update->update([
                'status' => ContentUpdate::STATUS_APPROVED,
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
            ]);

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
            $lesson = Lesson::create(array_merge([
                'course_id' => $update->course_id,
                'content_version' => 1,
            ], array_intersect_key($payload, array_flip([
                'section_id', 'chapter_id', 'title', 'type', 'video_url',
                'video_path', 'video_original_name', 'video_mime', 'video_size',
                'content', 'document_file', 'duration', 'duration_seconds',
                'is_preview', 'is_required', 'sort_order', 'status', 'attachments',
            ]))));

            $oldHlsDir = 'lesson-hls/update_' . $update->id;
            $newHlsDir = 'lesson-hls/' . $lesson->id;
            if (\Illuminate\Support\Facades\Storage::disk('local')->exists($oldHlsDir)) {
                if (\Illuminate\Support\Facades\Storage::disk('local')->exists($newHlsDir)) {
                    \Illuminate\Support\Facades\Storage::disk('local')->deleteDirectory($newHlsDir);
                }
                \Illuminate\Support\Facades\Storage::disk('local')->move($oldHlsDir, $newHlsDir);
                $lesson->update(['video_path' => $newHlsDir . '/playlist.m3u8']);
            } elseif ($lesson->type === 'video' && $lesson->video_path && \Illuminate\Support\Str::endsWith($lesson->video_path, '.mp4')) {
                \App\Jobs\ConvertVideoToHLS::dispatch($lesson);
            }
        } elseif ($update->action === ContentUpdate::ACTION_UPDATE && $update->entity_id) {
            $lesson = Lesson::find($update->entity_id);
            if ($lesson) {
                $hasMediaChange = isset($payload['video_path']) || isset($payload['video_url']) || isset($payload['document_file']);
                
                $updateData = array_intersect_key($payload, array_flip([
                    'section_id', 'chapter_id', 'title', 'type', 'video_url',
                    'video_path', 'video_original_name', 'video_mime', 'video_size',
                    'content', 'document_file', 'duration', 'duration_seconds',
                    'is_preview', 'is_required', 'sort_order', 'status', 'attachments',
                ]));

                if ($hasMediaChange) {
                    $updateData['content_version'] = (int) $lesson->content_version + 1;
                }

                $lesson->update($updateData);

                $oldHlsDir = 'lesson-hls/update_' . $update->id;
                $newHlsDir = 'lesson-hls/' . $lesson->id;
                if (\Illuminate\Support\Facades\Storage::disk('local')->exists($oldHlsDir)) {
                    if (\Illuminate\Support\Facades\Storage::disk('local')->exists($newHlsDir)) {
                        \Illuminate\Support\Facades\Storage::disk('local')->deleteDirectory($newHlsDir);
                    }
                    \Illuminate\Support\Facades\Storage::disk('local')->move($oldHlsDir, $newHlsDir);
                    $lesson->update(['video_path' => $newHlsDir . '/playlist.m3u8']);
                } elseif ($lesson->type === 'video' && $lesson->video_path && \Illuminate\Support\Str::endsWith($lesson->video_path, '.mp4')) {
                    \App\Jobs\ConvertVideoToHLS::dispatch($lesson);
                }
            }
        } elseif ($update->action === ContentUpdate::ACTION_DELETE && $update->entity_id) {
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
    public function mergeCurriculumWithUpdates(Course $course): \Illuminate\Support\Collection
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
                    'type' => $payload['type'] ?? 'video',
                    'video_url' => $payload['video_url'] ?? null,
                    'video_path' => $payload['video_path'] ?? null,
                    'video_original_name' => $payload['video_original_name'] ?? null,
                    'video_mime' => $payload['video_mime'] ?? null,
                    'video_size' => $payload['video_size'] ?? null,
                    'content' => $payload['content'] ?? null,
                    'document_file' => $payload['document_file'] ?? null,
                    'duration' => $payload['duration'] ?? $payload['duration_seconds'] ?? 0,
                    'duration_seconds' => $payload['duration_seconds'] ?? $payload['duration'] ?? 0,
                    'is_preview' => !empty($payload['is_preview']),
                    'sort_order' => $payload['sort_order'] ?? 999,
                    'status' => $payload['status'] ?? 'draft',
                ]);
                $draftLesson->id = $lUpdate->id;
                $draftLesson->draft_update = $lUpdate;
                $draftLesson->update_status = $lUpdate->status;
                $draftLesson->is_draft_create = true;

                if (!empty($payload['ai_moderation']) && is_array($payload['ai_moderation'])) {
                    $draftLesson->setRelation('videoModeration', new \App\Models\VideoModeration($payload['ai_moderation']));
                }

                // Attach to matching section
                $matchedSection = $sections->first(function ($s) use ($secId, $lUpdate) {
                    if ($secId && (string)$s->id === (string)$secId) {
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
                            if (in_array($key, ['title', 'type', 'video_url', 'video_path', 'content', 'document_file', 'duration', 'duration_seconds', 'is_preview', 'sort_order', 'status'], true)) {
                                $existingLesson->{$key} = $val;
                            }
                        }
                        $existingLesson->draft_update = $lUpdate;
                        $existingLesson->update_status = $lUpdate->status;
                        $existingLesson->is_draft_update = true;

                        if (!empty($payload['ai_moderation']) && is_array($payload['ai_moderation'])) {
                            $existingLesson->setRelation('videoModeration', new \App\Models\VideoModeration($payload['ai_moderation']));
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
