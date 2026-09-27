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
use App\Models\Quiz;
use App\Models\QuizVersion;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CourseReleaseService
{
    public const RULES = ['required_video_percent', 'required_lesson_percent', 'minimum_quiz_score', 'require_all_quizzes', 'require_all_assignments', 'certificate_enabled'];

    public function ruleSnapshot(Course $course): array
    {
        $rules = [];
        foreach (self::RULES as $field) {
            $rules[$field] = $course->{$field} ?? config('course.default_'.$field);
        }

        return $rules;
    }

    /**
     * Give a published legacy course a forward-only release. Historical versions
     * remain untouched; the new release describes the curriculum from now on.
     */
    public function ensureCurrentRelease(Course $course, ?User $actor = null): CourseVersion
    {
        return DB::transaction(function () use ($course, $actor): CourseVersion {
            $course = app(CourseReleaseLock::class)->course($course->id);
            $current = $course->published_version_id
                ? CourseVersion::query()->lockForUpdate()->find($course->published_version_id)
                : null;

            if ($current && (int) $current->course_id === (int) $course->id
                && $current->isPublished() && $current->published_at && $current->manifest_built_at) {
                return $current;
            }
            if ($current && ((int) $current->course_id !== (int) $course->id || ! $current->isPublished())) {
                $this->invalid('Invalid published course pointer.');
            }

            $state = $this->capture($course, $actor);
            $metadata = $current
                ? app(ContentVersionService::class)->withoutIdentity($current, ['course_id'])
                : app(ContentVersionService::class)->courseSnapshot($course);
            foreach ($this->ruleSnapshot($course) as $field => $value) {
                $metadata[$field] ??= $value;
            }
            $publisherId = $actor?->id ?? $course->instructor_id;
            $release = $course->versions()->create(array_merge($metadata, [
                'version_number' => ((int) $course->versions()->max('version_number')) + 1,
                'status' => CourseVersion::STATUS_DRAFT,
                'source_version_id' => $current?->id,
                'created_by' => $publisherId,
            ]));
            $this->seal($release, $state);
            $current?->forceFill([
                'status' => CourseVersion::STATUS_SUPERSEDED,
                'superseded_at' => now(),
            ])->save();
            $release->forceFill([
                'status' => CourseVersion::STATUS_PUBLISHED,
                'published_by' => $publisherId,
                'published_at' => now(),
            ])->save();
            $course->forceFill(['published_version_id' => $release->id])->save();

            return $release->fresh();
        });
    }

    /** Capture before any approved candidate is projected into mutable identities. */
    public function capture(Course $course, ?User $actor = null): array
    {
        if (DB::transactionLevel() < 1) {
            throw new \LogicException('Release construction requires the course transaction.');
        }
        $course = app(CourseReleaseLock::class)->course($course->id);
        $current = $course->published_version_id ? CourseVersion::query()->lockForUpdate()->findOrFail($course->published_version_id) : null;
        if ($current && ((int) $current->course_id !== (int) $course->id || ! $current->isPublished())) {
            $this->invalid('Invalid published course pointer.');
        }
        if ($current?->manifest_built_at) {
            return [
                'sections' => $current->sectionMappings()->lockForUpdate()->get()->mapWithKeys(fn ($item) => [$item->course_section_id => $item->only(['course_section_id', 'course_section_version_id', 'sort_order'])])->all(),
                'lessons' => $current->lessonMappings()->lockForUpdate()->get()->mapWithKeys(fn ($item) => [$item->lesson_id => $item->only(['course_section_id', 'lesson_id', 'lesson_version_id', 'sort_order', 'is_required', 'quiz_version_id', 'assignment_version_id'])])->all(),
            ];
        }

        // Initial approval / forward-only legacy baseline. Never attach this to
        // an old metadata snapshot or claim that it describes a past enrollment.
        $state = ['sections' => [], 'lessons' => []];
        $versions = app(ContentVersionService::class);
        foreach (CourseSection::where('course_id', $course->id)->orderBy('id')->get() as $section) {
            $version = $versions->createInitialSectionVersion($section, $actor);
            $this->assertChild($version, 'course_section_id', $section->id);
            $state['sections'][$section->id] = $this->sectionItem($version);
        }
        foreach (Lesson::where('course_id', $course->id)->orderBy('id')->with(['quiz', 'assignment'])->get() as $lesson) {
            if (ContentUpdate::where('course_id', $course->id)->where('type', 'lesson')->where('action', 'create')
                ->where('entity_id', $lesson->id)->whereIn('status', ['draft', 'pending'])->exists()) {
                continue;
            }
            // Authoring can hold new, unapproved identities outside the release.
            if ($current && ! $lesson->published_version_id && $lesson->status === Lesson::STATUS_DRAFT) {
                continue;
            }
            $version = $versions->createInitialLessonVersion($lesson, $actor);
            $this->assertChild($version, 'lesson_id', $lesson->id);
            $state['lessons'][$lesson->id] = $this->lessonItem($version, $lesson, $actor);
        }

        return $state;
    }

    /** Uses frozen versions/payload; unchanged membership comes only from capture(). */
    public function applyApprovedChange(array $state, ContentUpdate $update, User $actor): array
    {
        if ($update->action === ContentUpdate::ACTION_DELETE) {
            if ($update->type === ContentUpdate::TYPE_CHAPTER) {
                unset($state['sections'][$update->entity_id]);
                $state['lessons'] = array_filter($state['lessons'], fn ($item) => (int) $item['course_section_id'] !== (int) $update->entity_id);
            } elseif ($update->type === ContentUpdate::TYPE_LESSON) {
                unset($state['lessons'][$update->entity_id]);
            }

            return $state;
        }

        if ($update->action === ContentUpdate::ACTION_CREATE) {
            if ($update->type === ContentUpdate::TYPE_CHAPTER) {
                $section = CourseSection::where('course_id', $update->course_id)->findOrFail($update->entity_id);
                $state['sections'][$section->id] = $this->sectionItem($section->publishedVersion);
            } elseif ($update->type === ContentUpdate::TYPE_LESSON) {
                $lesson = Lesson::where('course_id', $update->course_id)->findOrFail($update->entity_id);
                $state['lessons'][$lesson->id] = $this->lessonItem($lesson->publishedVersion, $lesson, $actor);
            }
        }
        foreach (CourseSectionVersion::where('content_update_id', $update->id)->get() as $version) {
            $state['sections'][$version->course_section_id] = $this->sectionItem($version);
        }
        foreach (LessonVersion::where('content_update_id', $update->id)->get() as $version) {
            $old = $state['lessons'][$version->lesson_id] ?? null;
            if (! $old) {
                $this->invalid('Approved lesson is not a member of the current release.');
            }
            $state['lessons'][$version->lesson_id] = array_merge($old, [
                'lesson_version_id' => $version->id, 'course_section_id' => $version->section_id,
                'sort_order' => $version->sort_order, 'is_required' => $version->is_required,
                'quiz_version_id' => $version->type === Lesson::TYPE_QUIZ ? $old['quiz_version_id'] : null,
                'assignment_version_id' => $version->type === Lesson::TYPE_ASSIGNMENT ? $old['assignment_version_id'] : null,
            ]);
        }
        foreach (AssignmentVersion::where('content_update_id', $update->id)->with('assignment')->get() as $version) {
            $lessonId = $version->assignment->lesson_id;
            if (isset($state['lessons'][$lessonId])) {
                $state['lessons'][$lessonId]['assignment_version_id'] = $version->id;
            }
        }
        if ($update->type === ContentUpdate::TYPE_QUIZ) {
            $quizVersion = QuizVersion::with('quiz')->findOrFail($update->payload['quiz_version_id']);
            $lessonId = $quizVersion->quiz->lesson_id;
            if (! isset($state['lessons'][$lessonId])) {
                $this->invalid('Approved quiz is not a member of the current release.');
            }
            $state['lessons'][$lessonId]['quiz_version_id'] = $quizVersion->id;
        }

        return $state;
    }

    /** Called under the same course lock as child activation. */
    public function publishCurriculumChange(Course $course, ContentUpdate $update, User $actor, array $state): CourseVersion
    {
        $course = app(CourseReleaseLock::class)->course($course->id);
        $current = $course->published_version_id ? CourseVersion::query()->lockForUpdate()->findOrFail($course->published_version_id) : null;
        $metadata = $current
            ? app(ContentVersionService::class)->withoutIdentity($current, ['course_id'])
            : app(ContentVersionService::class)->courseSnapshot($course);
        unset($metadata['manifest_built_at']);
        foreach ($this->ruleSnapshot($course) as $field => $value) {
            $metadata[$field] ??= $value;
        }
        $release = $course->versions()->create(array_merge($metadata, [
            'version_number' => ((int) $course->versions()->max('version_number')) + 1,
            'status' => 'draft', 'content_update_id' => $update->id, 'created_by' => $actor->id,
        ]));
        $this->seal($release, $state);
        $current?->forceFill(['status' => 'superseded', 'superseded_at' => now()])->save();
        $release->forceFill(['status' => 'published', 'published_at' => now(), 'published_by' => $actor->id])->save();
        // Do not clear an unrelated pending metadata candidate.
        $course->forceFill(['published_version_id' => $release->id])->save();

        return $release;
    }

    public function seal(CourseVersion $release, array $state): void
    {
        if (! $release->isDraft() || $release->manifest_built_at) {
            $this->invalid('Only an unsealed draft can receive a manifest.');
        }
        foreach ($state['sections'] as $item) {
            $version = CourseSectionVersion::query()->lockForUpdate()->findOrFail($item['course_section_version_id']);
            $this->assertChild($version, 'course_section_id', $item['course_section_id']);
            if (! CourseSection::withoutGlobalScope('not_archived')->where('course_id', $release->course_id)->whereKey($item['course_section_id'])->lockForUpdate()->exists()) {
                $this->invalid('Section belongs to a different course.');
            }
            $release->sectionMappings()->create($item);
        }
        foreach ($state['lessons'] as $item) {
            $version = LessonVersion::query()->lockForUpdate()->findOrFail($item['lesson_version_id']);
            $lesson = Lesson::withoutGlobalScope('not_archived')->where('course_id', $release->course_id)->lockForUpdate()->findOrFail($item['lesson_id']);
            $this->assertChild($version, 'lesson_id', $lesson->id);
            if ($item['course_section_id'] && ! isset($state['sections'][$item['course_section_id']])) {
                $this->invalid('Lesson section is absent from the release.');
            }
            if ($item['quiz_version_id']) {
                $quiz = QuizVersion::query()->lockForUpdate()->findOrFail($item['quiz_version_id']);
                $quizIdentity = Quiz::query()->lockForUpdate()->findOrFail($quiz->quiz_id);
                if ((int) $quizIdentity->lesson_id !== (int) $lesson->id || ! in_array($quiz->status, ['published', 'superseded'], true)) {
                    $this->invalid('Invalid quiz release mapping.');
                }
            } elseif ($version->type === Lesson::TYPE_QUIZ) {
                $this->invalid('Quiz lesson has no published quiz version.');
            }
            if ($item['assignment_version_id']) {
                $assignment = AssignmentVersion::query()->lockForUpdate()->findOrFail($item['assignment_version_id']);
                $assignmentIdentity = Assignment::query()->lockForUpdate()->findOrFail($assignment->assignment_id);
                if ((int) $assignmentIdentity->lesson_id !== (int) $lesson->id || ! in_array($assignment->status, ['published', 'superseded'], true)) {
                    $this->invalid('Invalid assignment release mapping.');
                }
            } elseif ($version->type === Lesson::TYPE_ASSIGNMENT) {
                $this->invalid('Assignment lesson has no published assignment version.');
            }
            $release->lessonMappings()->create($item);
        }
        $release->forceFill(['manifest_built_at' => now()])->save();
    }

    private function sectionItem(CourseSectionVersion $version): array
    {
        return ['course_section_id' => $version->course_section_id, 'course_section_version_id' => $version->id, 'sort_order' => $version->sort_order];
    }

    private function lessonItem(LessonVersion $version, Lesson $lesson, ?User $actor): array
    {
        $quizId = null;
        $assignmentId = null;
        if ($version->type === Lesson::TYPE_QUIZ && $lesson->quiz) {
            $quizId = app(QuizVersioningService::class)->currentPublished($lesson->quiz)->id;
        }
        if ($version->type === Lesson::TYPE_ASSIGNMENT && $lesson->assignment) {
            $assignment = app(ContentVersionService::class)->createInitialAssignmentVersion($lesson->assignment, $actor);
            $this->assertChild($assignment, 'assignment_id', $lesson->assignment->id);
            $assignmentId = $assignment->id;
        }

        return ['course_section_id' => $version->section_id, 'lesson_id' => $lesson->id, 'lesson_version_id' => $version->id,
            'sort_order' => $version->sort_order, 'is_required' => $version->is_required,
            'quiz_version_id' => $quizId, 'assignment_version_id' => $assignmentId];
    }

    private function assertChild($version, string $key, int $id): void
    {
        if ((int) $version->{$key} !== $id || ! in_array($version->status, ['published', 'superseded'], true)) {
            $this->invalid('Invalid published child version pointer.');
        }
    }

    private function invalid(string $message): never
    {
        throw ValidationException::withMessages(['release' => $message]);
    }
}
