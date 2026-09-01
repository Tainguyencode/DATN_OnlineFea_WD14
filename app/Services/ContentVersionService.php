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
use Illuminate\Validation\ValidationException;

/**
 * The published pointer is authoritative. Live identity rows are maintained as
 * a compatibility projection for existing routes and views.
 */
class ContentVersionService
{
    public function createInitialCourseVersion(Course $course, ?User $actor = null): CourseVersion
    {
        return DB::transaction(function () use ($course, $actor): CourseVersion {
            $course = Course::query()->lockForUpdate()->findOrFail($course->id);
            if ($course->published_version_id) {
                return CourseVersion::query()->findOrFail($course->published_version_id);
            }
            $version = $course->versions()->create([...$this->courseSnapshot($course), 'version_number' => $this->nextNumber($course->versions()), 'status' => CourseVersion::STATUS_PUBLISHED, 'created_by' => $actor?->id ?? $course->instructor_id, 'published_by' => $actor?->id, 'published_at' => $course->published_at ?? now()]);
            $course->forceFill(['published_version_id' => $version->id])->save();

            return $version->fresh();
        });
    }

    public function createInitialSectionVersion(CourseSection $section, ?User $actor = null): CourseSectionVersion
    {
        return DB::transaction(function () use ($section, $actor): CourseSectionVersion {
            $section = CourseSection::query()->lockForUpdate()->findOrFail($section->id);
            if ($section->published_version_id) {
                return CourseSectionVersion::query()->findOrFail($section->published_version_id);
            }
            $version = $section->versions()->create([...$this->sectionSnapshot($section), 'version_number' => $this->nextNumber($section->versions()), 'status' => CourseSectionVersion::STATUS_PUBLISHED, 'created_by' => $actor?->id, 'published_by' => $actor?->id, 'published_at' => now()]);
            $section->forceFill(['published_version_id' => $version->id])->save();

            return $version->fresh();
        });
    }

    public function createInitialLessonVersion(Lesson $lesson, ?User $actor = null): LessonVersion
    {
        return DB::transaction(function () use ($lesson, $actor): LessonVersion {
            $lesson = Lesson::query()->lockForUpdate()->findOrFail($lesson->id);
            if ($lesson->published_version_id) {
                return LessonVersion::query()->findOrFail($lesson->published_version_id);
            }
            $version = $lesson->versions()->create([...$this->lessonSnapshot($lesson), 'version_number' => $this->nextNumber($lesson->versions()), 'status' => LessonVersion::STATUS_PUBLISHED, 'created_by' => $actor?->id, 'published_by' => $actor?->id, 'published_at' => now()]);
            $lesson->forceFill(['published_version_id' => $version->id])->save();

            return $version->fresh();
        });
    }

    public function createInitialAssignmentVersion(Assignment $assignment, ?User $actor = null): AssignmentVersion
    {
        return DB::transaction(function () use ($assignment, $actor): AssignmentVersion {
            $assignment = Assignment::query()->lockForUpdate()->findOrFail($assignment->id);
            if ($assignment->published_version_id) {
                return AssignmentVersion::query()->findOrFail($assignment->published_version_id);
            }
            $version = $assignment->versions()->create([...$this->assignmentSnapshot($assignment), 'version_number' => $this->nextNumber($assignment->versions()), 'status' => AssignmentVersion::STATUS_PUBLISHED, 'created_by' => $actor?->id, 'published_by' => $actor?->id, 'published_at' => now()]);
            $assignment->forceFill(['published_version_id' => $version->id])->save();

            return $version->fresh();
        });
    }

    public function cloneCourseVersion(Course $course, ?User $actor = null): CourseVersion
    {
        return $this->cloneVersion($course, CourseVersion::class, 'published_version_id', 'draft_version_id', $actor, fn (CourseVersion $version) => $this->withoutIdentity($version, ['course_id']));
    }

    public function cloneSectionVersion(CourseSection $section, ?User $actor = null): CourseSectionVersion
    {
        return $this->cloneVersion($section, CourseSectionVersion::class, 'published_version_id', 'draft_version_id', $actor, fn (CourseSectionVersion $version) => $this->withoutIdentity($version, ['course_section_id']));
    }

    public function cloneLessonVersion(Lesson $lesson, ?User $actor = null): LessonVersion
    {
        return $this->cloneVersion($lesson, LessonVersion::class, 'published_version_id', 'draft_version_id', $actor, fn (LessonVersion $version) => $this->withoutIdentity($version, ['lesson_id']));
    }

    public function cloneAssignmentVersion(Assignment $assignment, ?User $actor = null): AssignmentVersion
    {
        return $this->cloneVersion($assignment, AssignmentVersion::class, 'published_version_id', 'draft_version_id', $actor, fn (AssignmentVersion $version) => $this->withoutIdentity($version, ['assignment_id']));
    }

    /**
     * Freeze the exact non-Quiz proposal that an administrator will review.
     * This deliberately runs at the draft -> pending boundary, never per save.
     */
    public function materializeCandidate(ContentUpdate $update, User $actor): void
    {
        if (! in_array($update->action, [ContentUpdate::ACTION_UPDATE, ContentUpdate::ACTION_REORDER], true)) {
            return;
        }

        DB::transaction(function () use ($update, $actor): void {
            $update = ContentUpdate::query()->lockForUpdate()->findOrFail($update->id);
            if (! $update->isPending()) {
                throw ValidationException::withMessages(['version' => 'Chỉ thay đổi đang chờ duyệt mới có thể đóng băng phiên bản đề xuất.']);
            }

            $payload = $update->payload ?? [];
            if ($update->action === ContentUpdate::ACTION_REORDER) {
                $this->materializeReorderCandidates($update, $payload, $actor);

                return;
            }
            match ($update->type) {
                ContentUpdate::TYPE_COURSE => $this->materializeCourseCandidate($update, $payload, $actor),
                ContentUpdate::TYPE_CHAPTER => $this->materializeSectionCandidate($update, $payload, $actor),
                ContentUpdate::TYPE_LESSON => $this->materializeLessonCandidate($update, $payload, $actor),
                ContentUpdate::TYPE_ASSIGNMENT => $this->materializeAssignmentCandidate($update, $payload, $actor),
                default => null,
            };
        });
    }

    /**
     * Create or refresh the mutable candidate while its ContentUpdate is still
     * an authoring draft. Once submitted, callers must use materializeCandidate
     * only for legacy repair; pending and terminal candidates stay immutable.
     */
    public function prepareDraftCandidate(ContentUpdate $update, User $actor): ?Model
    {
        if (! in_array($update->action, [ContentUpdate::ACTION_UPDATE, ContentUpdate::ACTION_REORDER], true)) {
            return null;
        }

        return DB::transaction(function () use ($update, $actor): ?Model {
            $update = ContentUpdate::query()->lockForUpdate()->findOrFail($update->id);
            if (! $update->isDraft()) {
                throw ValidationException::withMessages(['version' => 'Chỉ bản cập nhật nháp mới có thể chuẩn bị ứng viên phiên bản.']);
            }

            $payload = $update->payload ?? [];
            if ($update->action === ContentUpdate::ACTION_REORDER) {
                $this->materializeReorderCandidates($update, $payload, $actor);

                return null;
            }

            match ($update->type) {
                ContentUpdate::TYPE_COURSE => $this->materializeCourseCandidate($update, $payload, $actor),
                ContentUpdate::TYPE_CHAPTER => $this->materializeSectionCandidate($update, $payload, $actor),
                ContentUpdate::TYPE_LESSON => $this->materializeLessonCandidate($update, $payload, $actor),
                ContentUpdate::TYPE_ASSIGNMENT => $this->materializeAssignmentCandidate($update, $payload, $actor),
                default => null,
            };

            return match ($update->type) {
                ContentUpdate::TYPE_COURSE => CourseVersion::query()->where('content_update_id', $update->id)->first(),
                ContentUpdate::TYPE_CHAPTER => CourseSectionVersion::query()->where('content_update_id', $update->id)->first(),
                ContentUpdate::TYPE_LESSON => LessonVersion::query()->where('content_update_id', $update->id)->where('lesson_id', $update->entity_id)->first(),
                ContentUpdate::TYPE_ASSIGNMENT => AssignmentVersion::query()->where('content_update_id', $update->id)->first(),
                default => null,
            };
        });
    }

    /** Remove only mutable candidates when their authoring draft is deleted. */
    public function discardDraftCandidates(ContentUpdate $update): void
    {
        DB::transaction(function () use ($update): void {
            $update = ContentUpdate::query()->lockForUpdate()->findOrFail($update->id);
            if (! $update->isDraft()) {
                throw ValidationException::withMessages(['version' => 'Chỉ ứng viên của bản cập nhật nháp mới có thể bị xóa.']);
            }

            foreach ([
                [CourseVersion::class, Course::class, 'course_id'],
                [CourseSectionVersion::class, CourseSection::class, 'course_section_id'],
                [LessonVersion::class, Lesson::class, 'lesson_id'],
                [AssignmentVersion::class, Assignment::class, 'assignment_id'],
            ] as [$versionClass, $identityClass, $foreignKey]) {
                $candidates = $versionClass::query()
                    ->where('content_update_id', $update->id)
                    ->lockForUpdate()
                    ->get();

                foreach ($candidates as $candidate) {
                    if (! $candidate->isDraft()) {
                        throw ValidationException::withMessages(['version' => 'Ứng viên đã kết thúc phải được giữ lại trong lịch sử.']);
                    }

                    $identity = $identityClass::query()->lockForUpdate()->findOrFail($candidate->{$foreignKey});
                    if ((int) $identity->draft_version_id === (int) $candidate->id) {
                        $identity->forceFill(['draft_version_id' => null])->save();
                    }
                    $candidate->delete();
                }
            }
        });
    }

    /** Activate every candidate tied to one approved ContentUpdate. */
    public function activateCandidates(ContentUpdate $update, User $admin): void
    {
        if (! in_array($update->action, [ContentUpdate::ACTION_UPDATE, ContentUpdate::ACTION_REORDER], true)) {
            return;
        }

        // Pending records created before the submit-boundary integration (or
        // legacy records imported directly into the table) are repaired from
        // their already immutable payload before activation. New production
        // submissions always materialize at draft -> pending. Reorders are
        // stricter: approval must never derive candidates from mutable rows.
        if (! $this->hasCandidate($update)) {
            if ($update->action === ContentUpdate::ACTION_REORDER) {
                throw ValidationException::withMessages([
                    'version' => 'Thay đổi sắp xếp chưa được đóng băng ở thời điểm gửi duyệt.',
                ]);
            }
            $this->materializeCandidate($update, $update->creator()->first() ?? $admin);
        }

        if ($update->action === ContentUpdate::ACTION_REORDER) {
            $this->activateReorderCandidates($update, $admin);

            return;
        }

        match ($update->type) {
            ContentUpdate::TYPE_COURSE => $this->publishCourseVersion($this->candidate(CourseVersion::class, $update), $admin),
            ContentUpdate::TYPE_CHAPTER => $this->publishSectionVersion($this->candidate(CourseSectionVersion::class, $update), $admin),
            ContentUpdate::TYPE_LESSON => $this->activateLessonCandidates($update, $admin),
            ContentUpdate::TYPE_ASSIGNMENT => $this->publishAssignmentVersion($this->candidate(AssignmentVersion::class, $update), $admin),
            default => null,
        };
    }

    /** Keep rejected candidates immutable and traceable without making them live. */
    public function rejectCandidates(ContentUpdate $update): void
    {
        DB::transaction(function () use ($update): void {
            foreach ([
                [CourseVersion::class, Course::class, 'course_id'],
                [CourseSectionVersion::class, CourseSection::class, 'course_section_id'],
                [LessonVersion::class, Lesson::class, 'lesson_id'],
                [AssignmentVersion::class, Assignment::class, 'assignment_id'],
            ] as [$versionClass, $identityClass, $foreignKey]) {
                $versions = $versionClass::query()->where('content_update_id', $update->id)->lockForUpdate()->get();
                foreach ($versions as $version) {
                    $identity = $identityClass::query()->lockForUpdate()->findOrFail($version->{$foreignKey});
                    if ((int) $identity->draft_version_id === (int) $version->id) {
                        $identity->forceFill(['draft_version_id' => null])->save();
                    }
                    if ($version->isDraft()) {
                        $version->forceFill(['status' => $versionClass::STATUS_REJECTED, 'rejected_at' => now()])->save();
                    }
                }
            }
        });
    }

    /** Create initial V1 snapshots when a draft/imported course is first approved. */
    public function publishInitialCourseTree(Course $course, User $actor): void
    {
        DB::transaction(function () use ($course, $actor): void {
            $course = Course::query()->lockForUpdate()->findOrFail($course->id);
            $this->createInitialCourseVersion($course, $actor);
            $course->load(['courseSections.lessons.assignment']);
            foreach ($course->courseSections as $section) {
                $this->createInitialSectionVersion($section, $actor);
                foreach ($section->lessons as $lesson) {
                    $this->createInitialLessonVersion($lesson, $actor);
                    if ($lesson->assignment) {
                        $this->createInitialAssignmentVersion($lesson->assignment, $actor);
                    }
                }
            }
        });
    }

    public function publishCourseVersion(CourseVersion $candidate, User $admin): CourseVersion
    {
        return $this->publishVersion($candidate, Course::class, 'course_id', 'published_version_id', 'draft_version_id', $admin,
            fn (CourseVersion $version): array => $this->only($version, ['title', 'slug', 'short_description', 'description', 'objectives', 'requirements', 'target_audience', 'category_id', 'level', 'language', 'price', 'discount_price', 'sale_price', 'thumbnail', 'preview_video', 'tags']));
    }

    public function publishSectionVersion(CourseSectionVersion $candidate, User $admin): CourseSectionVersion
    {
        return $this->publishVersion($candidate, CourseSection::class, 'course_section_id', 'published_version_id', 'draft_version_id', $admin,
            fn (CourseSectionVersion $version): array => $this->only($version, ['title', 'description', 'sort_order']));
    }

    public function publishLessonVersion(LessonVersion $candidate, User $admin): LessonVersion
    {
        return $this->publishVersion($candidate, Lesson::class, 'lesson_id', 'published_version_id', 'draft_version_id', $admin,
            fn (LessonVersion $version): array => [
                ...$this->only($version, ['section_id', 'title', 'type', 'content', 'document_file', 'video_url', 'video_path', 'original_video_key', 'hls_manifest_key', 'hls_playlist', 'hls_path', 'video_original_name', 'video_mime', 'video_size', 'duration_seconds', 'is_preview', 'is_required', 'sort_order']),
                'chapter_id' => $version->legacy_chapter_id,
            ]);
    }

    public function publishAssignmentVersion(AssignmentVersion $candidate, User $admin): AssignmentVersion
    {
        return $this->publishVersion($candidate, Assignment::class, 'assignment_id', 'published_version_id', 'draft_version_id', $admin,
            fn (AssignmentVersion $version): array => $this->only($version, ['title', 'description', 'instructions', 'due_date', 'due_days', 'max_score', 'passing_score', 'is_required', 'allowed_file_types', 'maximum_file_size']));
    }

    /** @param array<string, mixed> $changes */
    public function updateDraft(Model $version, array $changes): Model
    {
        return DB::transaction(function () use ($version, $changes): Model {
            $locked = $version::query()->lockForUpdate()->findOrFail($version->id);
            if (! $locked->isDraft()) {
                throw ValidationException::withMessages(['version' => 'Chỉ phiên bản nháp mới có thể chỉnh sửa.']);
            }
            if ($locked->content_update_id && ! ContentUpdate::query()->find($locked->content_update_id)?->isDraft()) {
                throw ValidationException::withMessages(['version' => 'Phiên bản đang chờ duyệt không thể chỉnh sửa.']);
            }
            $locked->fill($changes)->save();

            return $locked->fresh();
        });
    }

    /** @return array<string, mixed> */
    public function courseSnapshot(Course $course): array
    {
        return $this->only($course, ['title', 'slug', 'short_description', 'description', 'objectives', 'requirements', 'target_audience', 'category_id', 'level', 'language', 'price', 'discount_price', 'sale_price', 'thumbnail', 'preview_video', 'tags']);
    }

    /** @return array<string, mixed> */
    public function sectionSnapshot(CourseSection $section): array
    {
        return $this->only($section, ['title', 'description', 'sort_order']);
    }

    /** @return array<string, mixed> */
    public function lessonSnapshot(Lesson $lesson): array
    {
        $data = $this->only($lesson, ['section_id', 'chapter_id', 'title', 'type', 'content', 'document_file', 'video_url', 'video_path', 'original_video_key', 'hls_manifest_key', 'hls_playlist', 'hls_path', 'video_original_name', 'video_mime', 'video_size', 'duration_seconds', 'is_preview', 'is_required', 'sort_order', 'attachments', 'subtitles']);
        $data['legacy_chapter_id'] = $data['chapter_id'] ?? null;
        unset($data['chapter_id']);

        return $data;
    }

    /** @return array<string, mixed> */
    public function assignmentSnapshot(Assignment $assignment): array
    {
        return $this->only($assignment, ['title', 'description', 'instructions', 'due_date', 'due_days', 'max_score', 'passing_score', 'is_required', 'allowed_file_types', 'maximum_file_size']);
    }

    /** @param array<int, string> $except @return array<string, mixed> */
    private function withoutIdentity(Model $version, array $except): array
    {
        $data = $version->getAttributes();
        unset($data['id'], $data['version_number'], $data['status'], $data['content_update_id'], $data['source_version_id'], $data['created_by'], $data['published_by'], $data['published_at'], $data['superseded_at'], $data['rejected_at'], $data['created_at'], $data['updated_at']);
        foreach ($except as $key) {
            unset($data[$key]);
        }

        return $data;
    }

    /** @param callable(Model): array<string, mixed> $snapshot */
    private function cloneVersion(Model $identity, string $versionClass, string $publishedPointer, string $draftPointer, ?User $actor, callable $snapshot): Model
    {
        return DB::transaction(function () use ($identity, $versionClass, $publishedPointer, $draftPointer, $actor, $snapshot): Model {
            $identity = $identity::query()->lockForUpdate()->findOrFail($identity->id);
            if ($identity->{$draftPointer}) {
                $draft = $versionClass::query()->lockForUpdate()->findOrFail($identity->{$draftPointer});
                $update = $draft->content_update_id
                    ? ContentUpdate::query()->lockForUpdate()->find($draft->content_update_id)
                    : null;
                if ($draft->isDraft() && (! $update || in_array($update->status, [ContentUpdate::STATUS_DRAFT, ContentUpdate::STATUS_PENDING], true))) {
                    return $draft;
                }

                $identity->forceFill([$draftPointer => null])->save();
            }
            $published = $identity->{$publishedPointer} ? $versionClass::query()->lockForUpdate()->findOrFail($identity->{$publishedPointer}) : null;
            if (! $published || ! $published->isPublished()) {
                throw ValidationException::withMessages(['version' => 'Không có phiên bản đã xuất bản hợp lệ để tạo bản nháp.']);
            }
            $draft = $identity->versions()->create([...$snapshot($published), 'version_number' => $this->nextNumber($identity->versions()), 'status' => $versionClass::STATUS_DRAFT, 'created_by' => $actor?->id]);
            $identity->forceFill([$draftPointer => $draft->id])->save();

            return $draft->fresh();
        });
    }

    private function nextNumber($relation): int
    {
        return ((int) $relation->lockForUpdate()->max('version_number')) + 1;
    }

    /** @param array<string, mixed> $payload */
    private function materializeCourseCandidate(ContentUpdate $update, array $payload, User $actor): void
    {
        $course = Course::query()->lockForUpdate()->findOrFail($update->course_id);
        $this->createInitialCourseVersion($course, $actor);
        $course->refresh();
        $source = $this->rollbackSource($update, CourseVersion::class, 'course_id', $course->id);
        $candidate = $this->candidateFor($course, CourseVersion::class, 'course_id', $update, $actor, fn (CourseVersion $version) => $this->withoutIdentity($version, ['course_id']), $source);
        $candidate->fill(array_intersect_key($payload, array_flip(['title', 'slug', 'short_description', 'description', 'objectives', 'requirements', 'target_audience', 'category_id', 'level', 'language', 'price', 'discount_price', 'sale_price', 'thumbnail', 'preview_video', 'tags'])))->save();
    }

    /** @param array<string, mixed> $payload */
    private function materializeSectionCandidate(ContentUpdate $update, array $payload, User $actor): void
    {
        $section = CourseSection::query()->where('course_id', $update->course_id)->lockForUpdate()->findOrFail($update->entity_id);
        $this->createInitialSectionVersion($section, $actor);
        $section->refresh();
        $source = $this->rollbackSource($update, CourseSectionVersion::class, 'course_section_id', $section->id);
        $candidate = $this->candidateFor($section, CourseSectionVersion::class, 'course_section_id', $update, $actor, fn (CourseSectionVersion $version) => $this->withoutIdentity($version, ['course_section_id']), $source);
        $candidate->fill(array_intersect_key($payload, array_flip(['title', 'description', 'sort_order'])))->save();
    }

    /** @param array<string, mixed> $payload */
    private function materializeLessonCandidate(ContentUpdate $update, array $payload, User $actor): void
    {
        $lesson = Lesson::query()->where('course_id', $update->course_id)->lockForUpdate()->findOrFail($update->entity_id);
        $this->createInitialLessonVersion($lesson, $actor);
        $lesson->refresh();
        $source = $this->rollbackSource($update, LessonVersion::class, 'lesson_id', $lesson->id);
        $candidate = $this->candidateFor($lesson, LessonVersion::class, 'lesson_id', $update, $actor, fn (LessonVersion $version) => $this->withoutIdentity($version, ['lesson_id']), $source);
        $changes = array_intersect_key($payload, array_flip(['section_id', 'title', 'type', 'content', 'document_file', 'video_url', 'video_path', 'original_video_key', 'hls_manifest_key', 'hls_playlist', 'hls_path', 'video_original_name', 'video_mime', 'video_size', 'duration_seconds', 'is_preview', 'is_required', 'sort_order', 'attachments', 'subtitles']));
        if (array_key_exists('duration', $payload)) {
            $changes['duration_seconds'] = (int) $payload['duration'];
        }
        if (array_key_exists('chapter_id', $payload)) {
            $changes['legacy_chapter_id'] = $payload['chapter_id'];
        }
        $candidate->fill($changes)->save();

        $assignment = $lesson->assignment;
        if (($update->metadata['operation_origin'] ?? null) !== 'rollback'
            && $assignment
            && ($lesson->type === Lesson::TYPE_ASSIGNMENT || ($payload['type'] ?? null) === Lesson::TYPE_ASSIGNMENT)) {
            $this->createInitialAssignmentVersion($assignment, $actor);
            $assignment->refresh();
            $assignmentCandidate = $this->candidateFor($assignment, AssignmentVersion::class, 'assignment_id', $update, $actor, fn (AssignmentVersion $version) => $this->withoutIdentity($version, ['assignment_id']));
            $assignmentChanges = array_filter([
                'title' => $payload['title'] ?? null,
                'description' => $payload['content'] ?? null,
                'instructions' => $payload['content'] ?? null,
                'due_days' => $payload['assignment_due_days'] ?? null,
                'max_score' => $payload['assignment_max_score'] ?? null,
                'passing_score' => $payload['assignment_passing_score'] ?? null,
            ], fn ($value): bool => $value !== null);
            $assignmentCandidate->fill($assignmentChanges)->save();
        }
    }

    /** @param array<string, mixed> $payload */
    private function materializeAssignmentCandidate(ContentUpdate $update, array $payload, User $actor): void
    {
        $assignment = Assignment::query()->where('course_id', $update->course_id)->lockForUpdate()->findOrFail($update->entity_id);
        $this->createInitialAssignmentVersion($assignment, $actor);
        $assignment->refresh();
        $source = $this->rollbackSource($update, AssignmentVersion::class, 'assignment_id', $assignment->id);
        $candidate = $this->candidateFor($assignment, AssignmentVersion::class, 'assignment_id', $update, $actor, fn (AssignmentVersion $version) => $this->withoutIdentity($version, ['assignment_id']), $source);
        $candidate->fill(array_intersect_key($payload, array_flip([
            'title', 'description', 'instructions', 'due_date', 'due_days', 'max_score',
            'passing_score', 'is_required', 'allowed_file_types', 'maximum_file_size',
        ])))->save();
    }

    /** @template T of Model @param class-string<T> $versionClass @return T */
    private function candidate(string $versionClass, ContentUpdate $update): Model
    {
        return $versionClass::query()->where('content_update_id', $update->id)->lockForUpdate()->firstOrFail();
    }

    private function hasCandidate(ContentUpdate $update): bool
    {
        return match ($update->type) {
            ContentUpdate::TYPE_COURSE => CourseVersion::query()->where('content_update_id', $update->id)->exists(),
            ContentUpdate::TYPE_CHAPTER => CourseSectionVersion::query()->where('content_update_id', $update->id)->exists(),
            ContentUpdate::TYPE_LESSON => LessonVersion::query()->where('content_update_id', $update->id)->exists(),
            ContentUpdate::TYPE_ASSIGNMENT => AssignmentVersion::query()->where('content_update_id', $update->id)->exists(),
            default => false,
        };
    }

    /** @param array<string, mixed> $payload */
    private function materializeReorderCandidates(ContentUpdate $update, array $payload, User $actor): void
    {
        $orders = $update->type === ContentUpdate::TYPE_CHAPTER
            ? ($payload['chapter_orders'] ?? $payload['section_orders'] ?? [])
            : ($payload['lesson_orders'] ?? []);
        foreach ($orders as $order) {
            if (! is_array($order) || ! isset($order['sort_order'])) {
                continue;
            }
            if ($update->type === ContentUpdate::TYPE_CHAPTER) {
                $section = CourseSection::query()->where('course_id', $update->course_id)->lockForUpdate()->find($order['section_id'] ?? $order['id'] ?? null);
                if (! $section || (int) $section->sort_order === (int) $order['sort_order']) {
                    continue;
                }
                $this->createInitialSectionVersion($section, $actor);
                $section->refresh();
                $candidate = $this->candidateFor($section, CourseSectionVersion::class, 'course_section_id', $update, $actor, fn (CourseSectionVersion $version) => $this->withoutIdentity($version, ['course_section_id']));
                $candidate->forceFill(['sort_order' => (int) $order['sort_order']])->save();
            } else {
                $lesson = Lesson::query()->where('course_id', $update->course_id)->lockForUpdate()->find($order['lesson_id'] ?? $order['id'] ?? null);
                if (! $lesson) {
                    continue;
                }
                $newSectionId = $order['section_id'] ?? $lesson->section_id;
                if (! CourseSection::query()->where('course_id', $update->course_id)->whereKey($newSectionId)->exists()) {
                    throw ValidationException::withMessages(['version' => 'Bài học chỉ có thể được chuyển trong cùng khóa học.']);
                }
                if ((int) $lesson->sort_order === (int) $order['sort_order'] && (int) $lesson->section_id === (int) $newSectionId) {
                    continue;
                }
                $this->createInitialLessonVersion($lesson, $actor);
                $lesson->refresh();
                $candidate = $this->candidateFor($lesson, LessonVersion::class, 'lesson_id', $update, $actor, fn (LessonVersion $version) => $this->withoutIdentity($version, ['lesson_id']));
                $candidate->forceFill(['sort_order' => (int) $order['sort_order'], 'section_id' => $newSectionId])->save();
            }
        }
    }

    private function activateReorderCandidates(ContentUpdate $update, User $admin): void
    {
        if ($update->type === ContentUpdate::TYPE_CHAPTER) {
            CourseSectionVersion::query()->where('content_update_id', $update->id)->lockForUpdate()->get()
                ->each(fn (CourseSectionVersion $version) => $this->publishSectionVersion($version, $admin));
        } elseif ($update->type === ContentUpdate::TYPE_LESSON) {
            LessonVersion::query()->where('content_update_id', $update->id)->lockForUpdate()->get()
                ->each(fn (LessonVersion $version) => $this->publishLessonVersion($version, $admin));
        }
    }

    /** @template T of Model @param class-string<T> $versionClass @return T */
    private function candidateFor(Model $identity, string $versionClass, string $foreignKey, ContentUpdate $update, User $actor, callable $snapshot, ?Model $source = null): Model
    {
        $existing = $versionClass::query()
            ->where('content_update_id', $update->id)
            ->where($foreignKey, $identity->id)
            ->lockForUpdate()
            ->first();
        if ($existing) {
            if (! $existing->isDraft() || (int) $identity->draft_version_id !== (int) $existing->id) {
                throw ValidationException::withMessages(['version' => 'Ứng viên của lần cập nhật này đã kết thúc và không thể được tái sử dụng.']);
            }

            return $existing;
        }
        if ($identity->draft_version_id) {
            $draft = $versionClass::query()->lockForUpdate()->findOrFail($identity->draft_version_id);
            $draftUpdate = $draft->content_update_id
                ? ContentUpdate::query()->lockForUpdate()->find($draft->content_update_id)
                : null;
            if (! $draft->isDraft() || ($draftUpdate && in_array($draftUpdate->status, [ContentUpdate::STATUS_APPROVED, ContentUpdate::STATUS_REJECTED], true))) {
                $identity->forceFill(['draft_version_id' => null])->save();
            } elseif ((int) $draft->content_update_id !== (int) $update->id) {
                throw ValidationException::withMessages(['version' => 'Đã có một phiên bản đề xuất khác đang hoạt động cho nội dung này.']);
            } else {
                return $draft;
            }
        }
        $published = $versionClass::query()->lockForUpdate()->findOrFail($identity->published_version_id);
        $basis = $source ?? $published;
        $candidate = $identity->versions()->create([
            ...$snapshot($basis),
            'version_number' => $this->nextNumber($identity->versions()),
            'status' => $versionClass::STATUS_DRAFT,
            'content_update_id' => $update->id,
            'source_version_id' => $source?->id,
            'created_by' => $actor->id,
        ]);
        $identity->forceFill(['draft_version_id' => $candidate->id])->save();

        return $candidate;
    }

    private function rollbackSource(ContentUpdate $update, string $versionClass, string $foreignKey, int $identityId): ?Model
    {
        $metadata = $update->metadata ?? [];
        if (($metadata['operation_origin'] ?? null) !== 'rollback') {
            return null;
        }

        $sourceId = (int) ($metadata['source_version_id'] ?? 0);
        $source = $versionClass::query()
            ->where($foreignKey, $identityId)
            ->lockForUpdate()
            ->find($sourceId);
        if (! $source || ! $source->isSuperseded()) {
            throw ValidationException::withMessages([
                'version' => 'Phiên bản nguồn khôi phục không còn hợp lệ cho nội dung này.',
            ]);
        }

        return $source;
    }

    private function activateLessonCandidates(ContentUpdate $update, User $admin): void
    {
        $lesson = $this->candidate(LessonVersion::class, $update);
        $assignment = AssignmentVersion::query()->where('content_update_id', $update->id)->lockForUpdate()->first();
        $this->publishLessonVersion($lesson, $admin);
        if ($assignment) {
            $this->publishAssignmentVersion($assignment, $admin);
        }
    }

    /** @template T of Model @param class-string<T> $identityClass @return T */
    private function publishVersion(Model $candidate, string $identityClass, string $foreignKey, string $publishedPointer, string $draftPointer, User $admin, callable $projection): Model
    {
        return DB::transaction(function () use ($candidate, $identityClass, $foreignKey, $publishedPointer, $draftPointer, $admin, $projection): Model {
            $candidate = $candidate::query()->lockForUpdate()->findOrFail($candidate->id);
            $identity = $identityClass::query()->lockForUpdate()->findOrFail($candidate->{$foreignKey});
            if (! $candidate->isDraft() || (int) $identity->{$draftPointer} !== (int) $candidate->id) {
                throw ValidationException::withMessages(['version' => 'Ứng viên phiên bản không còn hợp lệ để xuất bản.']);
            }
            $previous = $identity->{$publishedPointer}
                ? $candidate::query()->lockForUpdate()->findOrFail($identity->{$publishedPointer})
                : null;
            if ($previous && ! $previous->isPublished()) {
                throw ValidationException::withMessages(['version' => 'Con trỏ phiên bản xuất bản hiện tại không hợp lệ.']);
            }
            $previous?->forceFill(['status' => $candidate::STATUS_SUPERSEDED, 'superseded_at' => now()])->save();
            $candidate->forceFill(['status' => $candidate::STATUS_PUBLISHED, 'published_by' => $admin->id, 'published_at' => now(), 'rejected_at' => null])->save();
            $identity->forceFill([
                $publishedPointer => $candidate->id,
                $draftPointer => null,
                ...$projection($candidate),
            ])->save();

            return $candidate->fresh();
        });
    }

    /** @param array<int, string> $keys @return array<string, mixed> */
    private function only(Model $model, array $keys): array
    {
        return array_intersect_key($model->getAttributes(), array_flip($keys));
    }
}
