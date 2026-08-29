<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\AssignmentVersion;
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
 * Foundation-only service for immutable snapshots. Existing web flows still
 * read and write the live identity rows until Phase 5C2 activates versions.
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

    /** @param array<string, mixed> $changes */
    public function updateDraft(Model $version, array $changes): Model
    {
        return DB::transaction(function () use ($version, $changes): Model {
            $locked = $version::query()->lockForUpdate()->findOrFail($version->id);
            if (! $locked->isDraft()) {
                throw ValidationException::withMessages(['version' => 'Chỉ phiên bản nháp mới có thể chỉnh sửa.']);
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
        unset($data['id'], $data['version_number'], $data['status'], $data['created_by'], $data['published_by'], $data['published_at'], $data['superseded_at'], $data['created_at'], $data['updated_at']);
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
                return $versionClass::query()->findOrFail($identity->{$draftPointer});
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

    /** @param array<int, string> $keys @return array<string, mixed> */
    private function only(Model $model, array $keys): array
    {
        return array_intersect_key($model->getAttributes(), array_flip($keys));
    }
}
