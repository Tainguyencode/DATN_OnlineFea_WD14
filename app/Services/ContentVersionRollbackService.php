<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\AssignmentVersion;
use App\Models\ContentUpdate;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\CourseSectionVersion;
use App\Models\CourseVersion;
use App\Models\Lesson;
use App\Models\LessonVersion;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ContentVersionRollbackService
{
    public function createDraft(Course $course, string $type, int $sourceVersionId, User $actor, string $reason): ContentUpdate
    {
        abort_unless($course->isOwnedBy($actor), 403);
        Validator::make(['reason' => $reason], ['reason' => ['required', 'string', 'min:5', 'max:1000']])->validate();

        return DB::transaction(function () use ($course, $type, $sourceVersionId, $actor, $reason): ContentUpdate {
            $course = Course::query()->lockForUpdate()->findOrFail($course->id);
            abort_if($course->status === Course::STATUS_ARCHIVED, 422, 'Không thể tạo yêu cầu khôi phục cho khóa học đã lưu trữ.');

            [$identity, $source, $foreignKey] = $this->resolveEligibleSource($course, $type, $sourceVersionId);
            $entityId = (int) $identity->id;

            $existing = ContentUpdate::query()
                ->where('course_id', $course->id)
                ->where('type', $type)
                ->where('action', ContentUpdate::ACTION_UPDATE)
                ->where('entity_id', $entityId)
                ->where('created_by', $actor->id)
                ->where('status', ContentUpdate::STATUS_DRAFT)
                ->lockForUpdate()
                ->get()
                ->first(fn (ContentUpdate $update): bool => ($update->metadata['operation_origin'] ?? null) === 'rollback'
                    && (int) ($update->metadata['source_version_id'] ?? 0) === (int) $source->id);

            if ($existing) {
                return $existing;
            }

            return ContentUpdate::create([
                'course_id' => $course->id,
                'type' => $type,
                'action' => ContentUpdate::ACTION_UPDATE,
                'entity_id' => $entityId,
                'payload' => $this->snapshot($type, $source),
                'metadata' => [
                    'operation_origin' => 'rollback',
                    'source_version_type' => $type,
                    'source_version_id' => (int) $source->id,
                    'source_version_number' => (int) $source->version_number,
                    'rollback_reason' => trim($reason),
                    'identity_foreign_key' => $foreignKey,
                ],
                'status' => ContentUpdate::STATUS_DRAFT,
                'created_by' => $actor->id,
            ]);
        });
    }

    /** @return array{0: Model, 1: Model, 2: string} */
    private function resolveEligibleSource(Course $course, string $type, int $sourceVersionId): array
    {
        [$identity, $source, $foreignKey, $publishedPointer] = match ($type) {
            ContentUpdate::TYPE_COURSE => [
                $course,
                CourseVersion::query()->where('course_id', $course->id)->lockForUpdate()->find($sourceVersionId),
                'course_id',
                $course->published_version_id,
            ],
            ContentUpdate::TYPE_CHAPTER => $this->sectionSource($course, $sourceVersionId),
            ContentUpdate::TYPE_LESSON => $this->lessonSource($course, $sourceVersionId),
            ContentUpdate::TYPE_ASSIGNMENT => $this->assignmentSource($course, $sourceVersionId),
            default => throw ValidationException::withMessages(['version' => 'Loại nội dung không hỗ trợ khôi phục phiên bản.']),
        };

        if (! $source || ! $source->isSuperseded()) {
            throw ValidationException::withMessages(['version' => 'Chỉ phiên bản đã từng xuất bản và đã được thay thế mới có thể khôi phục.']);
        }
        if ((int) $publishedPointer === (int) $source->id) {
            throw ValidationException::withMessages(['version' => 'Không thể khôi phục từ phiên bản đang xuất bản.']);
        }

        return [$identity, $source, $foreignKey];
    }

    /** @return array{0: CourseSection, 1: ?CourseSectionVersion, 2: string, 3: mixed} */
    private function sectionSource(Course $course, int $sourceVersionId): array
    {
        $source = CourseSectionVersion::query()
            ->whereHas('section', fn ($query) => $query->withoutGlobalScopes()->where('course_id', $course->id))
            ->lockForUpdate()
            ->find($sourceVersionId);
        $section = $source ? CourseSection::withoutGlobalScopes()->findOrFail($source->course_section_id) : null;
        abort_if($section?->archived_at, 422, 'Khôi phục identity chương đã lưu trữ được hoãn sang giai đoạn sau.');

        return [$section ?? new CourseSection, $source, 'course_section_id', $section?->published_version_id];
    }

    /** @return array{0: Lesson, 1: ?LessonVersion, 2: string, 3: mixed} */
    private function lessonSource(Course $course, int $sourceVersionId): array
    {
        $source = LessonVersion::query()
            ->whereHas('lesson', fn ($query) => $query->withoutGlobalScopes()->where('course_id', $course->id))
            ->lockForUpdate()
            ->find($sourceVersionId);
        $lesson = $source ? Lesson::withoutGlobalScopes()->findOrFail($source->lesson_id) : null;
        abort_if($lesson?->archived_at, 422, 'Khôi phục identity bài học đã lưu trữ được hoãn sang giai đoạn sau.');

        return [$lesson ?? new Lesson, $source, 'lesson_id', $lesson?->published_version_id];
    }

    /** @return array{0: Assignment, 1: ?AssignmentVersion, 2: string, 3: mixed} */
    private function assignmentSource(Course $course, int $sourceVersionId): array
    {
        $source = AssignmentVersion::query()
            ->whereHas('assignment', fn ($query) => $query->where('course_id', $course->id))
            ->lockForUpdate()
            ->find($sourceVersionId);
        $assignment = $source ? Assignment::query()->findOrFail($source->assignment_id) : null;
        $lesson = $assignment ? Lesson::withoutGlobalScopes()->find($assignment->lesson_id) : null;
        abort_if($lesson?->archived_at, 422, 'Khôi phục bài tập thuộc bài học đã lưu trữ được hoãn sang giai đoạn sau.');

        return [$assignment ?? new Assignment, $source, 'assignment_id', $assignment?->published_version_id];
    }

    /** @return array<string, mixed> */
    private function snapshot(string $type, Model $source): array
    {
        $fields = match ($type) {
            ContentUpdate::TYPE_COURSE => ['title', 'slug', 'short_description', 'description', 'objectives', 'requirements', 'target_audience', 'category_id', 'level', 'language', 'price', 'discount_price', 'sale_price', 'thumbnail', 'preview_video', 'tags'],
            ContentUpdate::TYPE_CHAPTER => ['title', 'description', 'sort_order'],
            ContentUpdate::TYPE_LESSON => ['section_id', 'legacy_chapter_id', 'title', 'type', 'content', 'document_file', 'video_url', 'video_path', 'original_video_key', 'hls_manifest_key', 'hls_playlist', 'hls_path', 'video_original_name', 'video_mime', 'video_size', 'duration_seconds', 'is_preview', 'is_required', 'sort_order', 'attachments', 'subtitles'],
            ContentUpdate::TYPE_ASSIGNMENT => ['title', 'description', 'instructions', 'due_date', 'due_days', 'max_score', 'passing_score', 'is_required', 'allowed_file_types', 'maximum_file_size'],
            default => [],
        };

        $snapshot = array_intersect_key($source->getAttributes(), array_flip($fields));
        if ($type === ContentUpdate::TYPE_LESSON) {
            $snapshot['chapter_id'] = $snapshot['legacy_chapter_id'] ?? null;
            unset($snapshot['legacy_chapter_id']);
        }

        return $snapshot;
    }
}
