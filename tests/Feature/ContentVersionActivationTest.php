<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Category;
use App\Models\ContentUpdate;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\CourseSectionVersion;
use App\Models\Lesson;
use App\Models\LessonVersion;
use App\Models\User;
use App\Services\ContentUpdateService;
use App\Services\ContentVersionService;
use App\Services\CourseReviewService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ContentVersionActivationTest extends TestCase
{
    use RefreshDatabase;

    private function publishedCourse(): array
    {
        $instructor = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'approved']);
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Version activation', 'slug' => 'version-activation']);
        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Course V1',
            'slug' => 'stable-course-url',
            'short_description' => 'V1',
            'description' => 'Description V1',
            'objectives' => 'Objectives',
            'price' => 100000,
            'language' => 'vi',
            'level' => 'beginner',
            'status' => Course::STATUS_PUBLISHED,
            'is_published' => true,
            'published_at' => now(),
        ]);
        $section = CourseSection::create(['course_id' => $course->id, 'title' => 'Section V1', 'sort_order' => 1]);

        return [$instructor, $admin, $course, $section];
    }

    private function pending(Course $course, User $instructor, string $type, ?int $entityId, array $payload): ContentUpdate
    {
        return ContentUpdate::create([
            'course_id' => $course->id,
            'type' => $type,
            'action' => ContentUpdate::ACTION_UPDATE,
            'entity_id' => $entityId,
            'payload' => $payload,
            'status' => ContentUpdate::STATUS_PENDING,
            'created_by' => $instructor->id,
            'submitted_at' => now(),
        ]);
    }

    public function test_course_candidate_is_frozen_then_atomically_activated_as_compatibility_projection(): void
    {
        [$instructor, $admin, $course] = $this->publishedCourse();
        $versions = app(ContentVersionService::class);
        $v1 = $versions->createInitialCourseVersion($course, $instructor);
        $update = $this->pending($course, $instructor, ContentUpdate::TYPE_COURSE, $course->id, [
            'title' => 'Course V2', 'description' => 'Description V2', 'price' => 120000,
        ]);

        $versions->materializeCandidate($update, $instructor);
        $candidate = $course->fresh()->draftVersion;
        $this->assertSame(2, $candidate->version_number);
        $this->assertSame($update->id, $candidate->content_update_id);
        $this->assertSame('Course V1', $course->fresh()->title);
        $this->assertSame($v1->id, $course->fresh()->published_version_id);

        app(ContentUpdateService::class)->applyApprovedUpdate($update, $admin);

        $course->refresh();
        $this->assertSame($candidate->id, $course->published_version_id);
        $this->assertNull($course->draft_version_id);
        $this->assertSame('Course V2', $course->title);
        $this->assertSame('stable-course-url', $course->slug);
        $this->assertSame('superseded', $v1->fresh()->status);
        $this->assertSame('published', $candidate->fresh()->status);
        $this->assertSame('Course V1', $v1->fresh()->title);
    }

    public function test_rejected_candidate_stays_historical_and_live_projection_remains_v1(): void
    {
        [$instructor, $admin, $course] = $this->publishedCourse();
        $versions = app(ContentVersionService::class);
        $v1 = $versions->createInitialCourseVersion($course, $instructor);
        $update = $this->pending($course, $instructor, ContentUpdate::TYPE_COURSE, $course->id, ['title' => 'Rejected V2']);
        $versions->materializeCandidate($update, $instructor);
        $candidate = $course->fresh()->draftVersion;

        app(ContentUpdateService::class)->rejectUpdate($update, $admin, 'Needs more detail');

        $this->assertSame($v1->id, $course->fresh()->published_version_id);
        $this->assertNull($course->fresh()->draft_version_id);
        $this->assertSame('Course V1', $course->fresh()->title);
        $this->assertSame('published', $v1->fresh()->status);
        $this->assertSame('rejected', $candidate->fresh()->status);
        $this->assertNotNull($candidate->fresh()->rejected_at);
    }

    public function test_lesson_and_assignment_candidates_activate_together_without_losing_v1_media_or_contract(): void
    {
        [$instructor, $admin, $course, $section] = $this->publishedCourse();
        $lesson = Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Assignment V1',
            'type' => Lesson::TYPE_ASSIGNMENT,
            'content' => 'Instructions V1',
            'document_file' => 'documents/v1.pdf',
            'duration_seconds' => 60,
            'status' => Lesson::STATUS_PUBLISHED,
        ]);
        $assignment = Assignment::create([
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
            'title' => 'Assignment V1',
            'description' => 'Assignment V1 description',
            'instructions' => 'Instructions V1',
            'due_days' => 7,
            'max_score' => 100,
            'passing_score' => 70,
        ]);
        $versions = app(ContentVersionService::class);
        $lessonV1 = $versions->createInitialLessonVersion($lesson, $instructor);
        $assignmentV1 = $versions->createInitialAssignmentVersion($assignment, $instructor);
        $update = $this->pending($course, $instructor, ContentUpdate::TYPE_LESSON, $lesson->id, [
            'title' => 'Assignment V2', 'content' => 'Instructions V2', 'document_file' => 'documents/v2.pdf',
            'assignment_due_days' => 14, 'assignment_max_score' => 120, 'assignment_passing_score' => 80,
        ]);

        $versions->materializeCandidate($update, $instructor);
        $lessonV2 = $lesson->fresh()->draftVersion;
        $assignmentV2 = $assignment->fresh()->draftVersion;
        $this->assertSame('documents/v1.pdf', $lesson->fresh()->document_file);

        app(ContentUpdateService::class)->applyApprovedUpdate($update, $admin);

        $this->assertSame($lessonV2->id, $lesson->fresh()->published_version_id);
        $this->assertSame($assignmentV2->id, $assignment->fresh()->published_version_id);
        $this->assertSame('documents/v2.pdf', $lesson->fresh()->document_file);
        $this->assertSame(120, $assignment->fresh()->max_score);
        $this->assertSame(80, $assignment->fresh()->passing_score);
        $this->assertSame('documents/v1.pdf', $lessonV1->fresh()->document_file);
        $this->assertSame(100, $assignmentV1->fresh()->max_score);
        $this->assertSame('superseded', $lessonV1->fresh()->status);
        $this->assertSame('superseded', $assignmentV1->fresh()->status);
    }

    public function test_initial_tree_publication_sets_v1_pointers_for_draft_and_import_compatible_content(): void
    {
        [$instructor, $admin, $course, $section] = $this->publishedCourse();
        $course->update(['status' => Course::STATUS_DRAFT, 'is_published' => false]);
        $lesson = Lesson::create(['course_id' => $course->id, 'section_id' => $section->id, 'title' => 'Draft assignment', 'type' => Lesson::TYPE_ASSIGNMENT, 'status' => Lesson::STATUS_DRAFT]);
        $assignment = Assignment::create(['course_id' => $course->id, 'lesson_id' => $lesson->id, 'title' => 'Draft assignment', 'description' => 'Draft assignment description']);

        app(ContentVersionService::class)->publishInitialCourseTree($course, $admin);

        $this->assertNotNull($course->fresh()->published_version_id);
        $this->assertNotNull($section->fresh()->published_version_id);
        $this->assertNotNull($lesson->fresh()->published_version_id);
        $this->assertNotNull($assignment->fresh()->published_version_id);
    }

    public function test_approved_lesson_delete_archives_identity_and_retains_historical_v1(): void
    {
        [$instructor, $admin, $course, $section] = $this->publishedCourse();
        $lesson = Lesson::create(['course_id' => $course->id, 'section_id' => $section->id, 'title' => 'Delete me', 'type' => Lesson::TYPE_DOCUMENT, 'content' => 'V1', 'status' => Lesson::STATUS_PUBLISHED]);
        $v1 = app(ContentVersionService::class)->createInitialLessonVersion($lesson, $instructor);
        $update = ContentUpdate::create([
            'course_id' => $course->id, 'type' => ContentUpdate::TYPE_LESSON, 'action' => ContentUpdate::ACTION_DELETE,
            'entity_id' => $lesson->id, 'payload' => [], 'status' => ContentUpdate::STATUS_PENDING,
            'created_by' => $instructor->id, 'submitted_at' => now(),
        ]);

        app(ContentUpdateService::class)->applyApprovedUpdate($update, $admin);

        $this->assertNull(Lesson::query()->find($lesson->id));
        $this->assertNotNull(Lesson::withoutGlobalScopes()->find($lesson->id)->archived_at);
        $this->assertSame('published', $v1->fresh()->status);
    }

    public function test_section_and_lesson_reorder_freezes_batch_candidates_and_preserves_v1_order(): void
    {
        [$instructor, $admin, $course, $section] = $this->publishedCourse();
        $secondSection = CourseSection::create(['course_id' => $course->id, 'title' => 'Section B', 'sort_order' => 2]);
        $thirdSection = CourseSection::create(['course_id' => $course->id, 'title' => 'Section C', 'sort_order' => 3]);
        $lessons = collect(['A', 'B', 'C'])->map(fn (string $title, int $index) => Lesson::create([
            'course_id' => $course->id, 'section_id' => $section->id, 'title' => $title,
            'type' => Lesson::TYPE_DOCUMENT, 'sort_order' => $index + 1, 'status' => Lesson::STATUS_PUBLISHED,
        ]));
        $versions = app(ContentVersionService::class);
        $sectionV1 = collect([$section, $secondSection, $thirdSection])->mapWithKeys(fn (CourseSection $model) => [$model->id => $versions->createInitialSectionVersion($model, $instructor)]);
        $lessonV1 = $lessons->mapWithKeys(fn (Lesson $model) => [$model->id => $versions->createInitialLessonVersion($model, $instructor)]);
        $sectionUpdate = ContentUpdate::create([
            'course_id' => $course->id, 'type' => ContentUpdate::TYPE_CHAPTER, 'action' => ContentUpdate::ACTION_REORDER,
            'payload' => ['section_orders' => [['id' => $thirdSection->id, 'sort_order' => 1], ['id' => $section->id, 'sort_order' => 2], ['id' => $secondSection->id, 'sort_order' => 3]]],
            'status' => ContentUpdate::STATUS_PENDING, 'created_by' => $instructor->id, 'submitted_at' => now(),
        ]);
        $lessonUpdate = ContentUpdate::create([
            'course_id' => $course->id, 'type' => ContentUpdate::TYPE_LESSON, 'action' => ContentUpdate::ACTION_REORDER,
            'payload' => ['lesson_orders' => [['id' => $lessons[2]->id, 'section_id' => $secondSection->id, 'sort_order' => 1], ['id' => $lessons[0]->id, 'sort_order' => 2], ['id' => $lessons[1]->id, 'sort_order' => 3]]],
            'status' => ContentUpdate::STATUS_PENDING, 'created_by' => $instructor->id, 'submitted_at' => now(),
        ]);

        $versions->materializeCandidate($sectionUpdate, $instructor);
        $versions->materializeCandidate($lessonUpdate, $instructor);
        $this->assertSame(3, CourseSectionVersion::where('content_update_id', $sectionUpdate->id)->count());
        $this->assertSame(3, LessonVersion::where('content_update_id', $lessonUpdate->id)->count());
        $this->assertSame(1, $section->fresh()->sort_order);
        $this->assertSame(1, $lessons[0]->fresh()->sort_order);

        app(ContentUpdateService::class)->applyApprovedUpdate($sectionUpdate, $admin);
        app(ContentUpdateService::class)->applyApprovedUpdate($lessonUpdate, $admin);

        $this->assertSame(2, $section->fresh()->sort_order);
        $this->assertSame(3, $secondSection->fresh()->sort_order);
        $this->assertSame(1, $thirdSection->fresh()->sort_order);
        $this->assertSame(2, $lessons[0]->fresh()->sort_order);
        $this->assertSame(3, $lessons[1]->fresh()->sort_order);
        $this->assertSame(1, $lessons[2]->fresh()->sort_order);
        $this->assertSame($secondSection->id, $lessons[2]->fresh()->section_id);
        $this->assertSame(1, $sectionV1[$section->id]->fresh()->sort_order);
        $this->assertSame(2, $sectionV1[$secondSection->id]->fresh()->sort_order);
        $this->assertSame(3, $sectionV1[$thirdSection->id]->fresh()->sort_order);
        $this->assertSame(1, $lessonV1[$lessons[0]->id]->fresh()->sort_order);
        $this->assertSame(2, $lessonV1[$lessons[1]->id]->fresh()->sort_order);
        $this->assertSame(3, $lessonV1[$lessons[2]->id]->fresh()->sort_order);
        $this->assertSame($section->id, $lessonV1[$lessons[2]->id]->fresh()->section_id);
    }

    public function test_rejected_reorder_keeps_old_order_and_revision_creates_new_candidates(): void
    {
        [$instructor, $admin, $course, $section] = $this->publishedCourse();
        $lessons = collect(['A', 'B', 'C'])->map(fn (string $title, int $index) => Lesson::create([
            'course_id' => $course->id, 'section_id' => $section->id, 'title' => $title,
            'type' => Lesson::TYPE_DOCUMENT, 'sort_order' => $index + 1, 'status' => Lesson::STATUS_PUBLISHED,
        ]));
        $versions = app(ContentVersionService::class);
        $lessons->each(fn (Lesson $lesson) => $versions->createInitialLessonVersion($lesson, $instructor));
        $payload = ['lesson_orders' => [['id' => $lessons[2]->id, 'sort_order' => 1], ['id' => $lessons[0]->id, 'sort_order' => 2], ['id' => $lessons[1]->id, 'sort_order' => 3]]];
        $rejected = ContentUpdate::create([
            'course_id' => $course->id, 'type' => ContentUpdate::TYPE_LESSON, 'action' => ContentUpdate::ACTION_REORDER,
            'payload' => $payload, 'status' => ContentUpdate::STATUS_PENDING, 'created_by' => $instructor->id, 'submitted_at' => now(),
        ]);

        $versions->materializeCandidate($rejected, $instructor);
        $candidateIds = LessonVersion::where('content_update_id', $rejected->id)->pluck('id')->all();
        app(ContentUpdateService::class)->rejectUpdate($rejected, $admin, 'Hãy điều chỉnh thứ tự bài học.');

        $this->assertSame(ContentUpdate::STATUS_REJECTED, $rejected->fresh()->status);
        $this->assertSame([1, 2, 3], $lessons->map(fn (Lesson $lesson) => (int) $lesson->fresh()->sort_order)->all());
        $this->assertSame(['rejected'], LessonVersion::whereIn('id', $candidateIds)->pluck('status')->unique()->values()->all());

        $revision = app(ContentUpdateService::class)->createRevisionFromRejected($rejected, $instructor);
        $course->update(['thumbnail' => 'thumbnail.jpg', 'copyright_agreed' => true]);
        $course->category()->update(['status' => true]);
        Lesson::create(['course_id' => $course->id, 'section_id' => $section->id, 'title' => 'D', 'type' => Lesson::TYPE_DOCUMENT, 'sort_order' => 4, 'status' => Lesson::STATUS_PUBLISHED]);
        Lesson::create(['course_id' => $course->id, 'section_id' => $section->id, 'title' => 'E', 'type' => Lesson::TYPE_DOCUMENT, 'sort_order' => 5, 'status' => Lesson::STATUS_PUBLISHED]);
        Lesson::create(['course_id' => $course->id, 'section_id' => $section->id, 'title' => 'Video', 'type' => Lesson::TYPE_VIDEO, 'video_url' => 'https://example.com/video.mp4', 'duration_seconds' => 1800, 'sort_order' => 6, 'status' => Lesson::STATUS_PUBLISHED]);
        app(CourseReviewService::class)->submitForReview($course->fresh(), $instructor);
        $versions->materializeCandidate($revision, $instructor);
        $revisionIds = LessonVersion::where('content_update_id', $revision->id)->pluck('id')->all();

        $this->assertNotSame($candidateIds, $revisionIds);
        $this->assertSame(['draft'], LessonVersion::whereIn('id', $revisionIds)->pluck('status')->unique()->values()->all());
    }

    public function test_reorder_activation_rolls_back_the_entire_batch_on_candidate_failure(): void
    {
        [$instructor, $admin, $course, $section] = $this->publishedCourse();
        $lessons = collect(['A', 'B', 'C'])->map(fn (string $title, int $index) => Lesson::create([
            'course_id' => $course->id, 'section_id' => $section->id, 'title' => $title,
            'type' => Lesson::TYPE_DOCUMENT, 'sort_order' => $index + 1, 'status' => Lesson::STATUS_PUBLISHED,
        ]));
        $versions = app(ContentVersionService::class);
        $v1 = $lessons->mapWithKeys(fn (Lesson $lesson) => [$lesson->id => $versions->createInitialLessonVersion($lesson, $instructor)]);
        $update = ContentUpdate::create([
            'course_id' => $course->id, 'type' => ContentUpdate::TYPE_LESSON, 'action' => ContentUpdate::ACTION_REORDER,
            'payload' => ['lesson_orders' => [['id' => $lessons[2]->id, 'sort_order' => 1], ['id' => $lessons[0]->id, 'sort_order' => 2], ['id' => $lessons[1]->id, 'sort_order' => 3]]],
            'status' => ContentUpdate::STATUS_PENDING, 'created_by' => $instructor->id, 'submitted_at' => now(),
        ]);
        $versions->materializeCandidate($update, $instructor);
        $versionService = \Mockery::mock(ContentVersionService::class)->makePartial();
        $versionService->shouldReceive('publishLessonVersion')->once()->passthru();
        $versionService->shouldReceive('publishLessonVersion')->once()->andThrow(
            ValidationException::withMessages(['version' => 'synthetic reorder activation failure'])
        );
        $this->app->instance(ContentVersionService::class, $versionService);

        try {
            app(ContentUpdateService::class)->applyApprovedUpdate($update, $admin);
            $this->fail('Reorder activation should fail when a candidate pointer is invalid.');
        } catch (ValidationException) {
            $this->assertSame(ContentUpdate::STATUS_PENDING, $update->fresh()->status);
            $this->assertSame($v1->map(fn ($version) => $version->id)->values()->all(), $lessons->map(fn (Lesson $lesson) => $lesson->fresh()->published_version_id)->values()->all());
            $this->assertSame([1, 2, 3], $lessons->map(fn (Lesson $lesson) => (int) $lesson->fresh()->sort_order)->all());
            $this->assertSame(['draft'], LessonVersion::where('content_update_id', $update->id)->pluck('status')->unique()->values()->all());
        }
    }

    public function test_reorder_double_approval_does_not_create_a_second_version_set(): void
    {
        [$instructor, $admin, $course, $section] = $this->publishedCourse();
        $lessons = collect(['A', 'B'])->map(fn (string $title, int $index) => Lesson::create([
            'course_id' => $course->id, 'section_id' => $section->id, 'title' => $title,
            'type' => Lesson::TYPE_DOCUMENT, 'sort_order' => $index + 1, 'status' => Lesson::STATUS_PUBLISHED,
        ]));
        $versions = app(ContentVersionService::class);
        $lessons->each(fn (Lesson $lesson) => $versions->createInitialLessonVersion($lesson, $instructor));
        $update = ContentUpdate::create([
            'course_id' => $course->id, 'type' => ContentUpdate::TYPE_LESSON, 'action' => ContentUpdate::ACTION_REORDER,
            'payload' => ['lesson_orders' => [['id' => $lessons[1]->id, 'sort_order' => 1], ['id' => $lessons[0]->id, 'sort_order' => 2]]],
            'status' => ContentUpdate::STATUS_PENDING, 'created_by' => $instructor->id, 'submitted_at' => now(),
        ]);
        $versions->materializeCandidate($update, $instructor);
        app(ContentUpdateService::class)->applyApprovedUpdate($update, $admin);
        $versionCount = LessonVersion::whereIn('lesson_id', $lessons->pluck('id'))->count();

        try {
            app(ContentUpdateService::class)->applyApprovedUpdate($update->fresh(), $admin);
            $this->fail('A terminal reorder must not be approved twice.');
        } catch (ValidationException) {
            $this->assertSame($versionCount, LessonVersion::whereIn('lesson_id', $lessons->pluck('id'))->count());
            $this->assertSame(ContentUpdate::STATUS_APPROVED, $update->fresh()->status);
        }
    }
}
