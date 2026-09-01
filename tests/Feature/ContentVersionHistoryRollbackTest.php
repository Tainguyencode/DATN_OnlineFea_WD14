<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\AssignmentVersion;
use App\Models\Category;
use App\Models\ContentUpdate;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\CourseSectionVersion;
use App\Models\CourseVersion;
use App\Models\Lesson;
use App\Models\LessonVersion;
use App\Models\Quiz;
use App\Models\QuizVersion;
use App\Models\Submission;
use App\Models\User;
use App\Services\ContentUpdateService;
use App\Services\ContentVersionComparisonService;
use App\Services\ContentVersionHistoryService;
use App\Services\ContentVersionRollbackService;
use App\Services\ContentVersionService;
use App\Services\CourseReviewService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ContentVersionHistoryRollbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_timeline_detail_and_compare_use_exact_immutable_snapshots(): void
    {
        [$instructor, $admin, $course, $section] = $this->context();
        [$v1, , $v3] = $this->threeCourseVersions($course, $instructor, $admin);
        $course->forceFill(['title' => 'LIVE ROW MUST NOT LEAK'])->save();

        $history = app(ContentVersionHistoryService::class);
        $timeline = $history->timeline($course->fresh(), ContentUpdate::TYPE_COURSE);
        $this->assertSame([3, 2, 1], $timeline->getCollection()->pluck('version_number')->all());
        $this->assertTrue($timeline->getCollection()->first()['is_current']);

        $detail = $history->detail($course, ContentUpdate::TYPE_COURSE, $history->resolve($course, ContentUpdate::TYPE_COURSE, $v1->id));
        $this->assertSame('Course A immutable', $detail['fields']['title']['value']);

        $fields = collect(app(ContentVersionComparisonService::class)->compare($course, ContentUpdate::TYPE_COURSE, $v1, $v3))->keyBy('key');
        $this->assertSame('Course A immutable', $fields['title']['old']);
        $this->assertSame('Course C current', $fields['title']['new']);

        $sectionV1 = $section->publishedVersion;
        [, $sectionV2] = $this->publishUpdate($course, $instructor, $admin, 'chapter', $section->id, ['sort_order' => 3]);
        $sectionFields = collect(app(ContentVersionComparisonService::class)->compare($course, 'chapter', $sectionV1, $sectionV2))->keyBy('key');
        $this->assertSame(1, $sectionFields['sort_order']['old']);
        $this->assertSame(3, $sectionFields['sort_order']['new']);
    }

    public function test_history_routes_render_labels_and_enforce_authorization_matrix(): void
    {
        [$instructor, $admin, $course] = $this->context();
        [$v1] = $this->threeCourseVersions($course, $instructor, $admin);
        $other = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'approved', 'is_active' => true, 'email_verified_at' => now()]);
        $student = User::factory()->create(['role' => 'student', 'is_active' => true, 'email_verified_at' => now()]);

        $this->get(route('instructor.courses.versions.index', $course))->assertRedirect();
        $this->actingAs($student)->withSession($this->twoFactor())->get(route('instructor.courses.versions.index', $course))->assertRedirect($student->dashboardUrl());
        $this->actingAs($other)->withSession($this->twoFactor())->get(route('instructor.courses.versions.index', $course))->assertForbidden();
        $this->actingAs($other)->withSession($this->twoFactor())->get(route('instructor.courses.versions.show', [$course, 'course', $v1->id]))->assertForbidden();
        $this->actingAs($other)->withSession($this->twoFactor())->get(route('instructor.courses.versions.compare', [$course, 'course', $v1->id]))->assertForbidden();
        $this->actingAs($instructor)->withSession($this->twoFactor())->get(route('instructor.courses.versions.index', $course))
            ->assertOk()->assertSee('V1')->assertSee('V2')->assertSee('V3')->assertSee('Đang xuất bản')->assertSee('Đã thay thế');

        $this->actingAs($admin)->withSession($this->twoFactor())->get(route('admin.courses.versions.index', $course))
            ->assertOk()->assertSee('V1')->assertSee('V3');
        $this->actingAs($student)->withSession($this->twoFactor())->get(route('admin.courses.versions.index', $course))->assertRedirect($student->dashboardUrl());
        $this->actingAs($admin)->withSession($this->twoFactor())->get(route('admin.courses.versions.show', [$course, 'course', $v1->id]))
            ->assertOk()->assertSee('Course A immutable');
        $this->actingAs($admin)->withSession($this->twoFactor())->get(route('admin.courses.versions.compare', [$course, 'course', $v1->id]))
            ->assertOk()->assertSee('Course A immutable')->assertSee('Course C current');
        $this->actingAs($admin)->withSession($this->twoFactor())->get(route('admin.courses.versions.show', [$course, 'course', 999999]))->assertNotFound();

        $before = ContentUpdate::count();
        $rollbackRoute = route('instructor.courses.versions.rollback.store', [$course, 'course', $v1->id]);
        $this->app['auth']->logout();
        $this->post($rollbackRoute, ['reason' => 'Guest cannot restore.'])->assertRedirect();
        $this->assertSame($before, ContentUpdate::count());
        $this->actingAs($student)->withSession($this->twoFactor())->post($rollbackRoute, ['reason' => 'Student cannot restore.'])->assertRedirect($student->dashboardUrl());
        $this->assertSame($before, ContentUpdate::count());
        $this->actingAs($other)->withSession($this->twoFactor())->post($rollbackRoute, ['reason' => 'Non-owner cannot restore.'])->assertForbidden();
        $this->assertSame($before, ContentUpdate::count());
        $this->actingAs($admin)->withSession($this->twoFactor())->post($rollbackRoute, ['reason' => 'Admin cannot bypass review.'])->assertRedirect($admin->dashboardUrl());
        $this->assertSame($before, ContentUpdate::count());
    }

    public function test_rollback_creates_draft_freezes_v4_and_approval_publishes_new_version(): void
    {
        [$instructor, $admin, $course] = $this->context();
        [$v1, , $v3] = $this->threeCourseVersions($course, $instructor, $admin);
        $rollback = app(ContentVersionRollbackService::class);

        $draft = $rollback->createDraft($course, 'course', $v1->id, $instructor, 'Khôi phục nội dung ổn định từ V1.');
        $duplicate = $rollback->createDraft($course, 'course', $v1->id, $instructor, 'Nhấn hai lần vẫn dùng cùng draft.');
        $this->assertSame($draft->id, $duplicate->id);
        $this->assertSame(ContentUpdate::STATUS_DRAFT, $draft->status);
        $this->assertSame($v3->id, $course->fresh()->published_version_id);
        $this->assertSame('Course C current', $course->fresh()->title);
        $this->assertSame(3, $course->versions()->count());

        app(CourseReviewService::class)->submitForReview($course->fresh(), $instructor);
        $draft->refresh();
        $v4 = CourseVersion::where('content_update_id', $draft->id)->sole();
        $this->assertSame(ContentUpdate::STATUS_PENDING, $draft->status);
        $this->assertSame(4, $v4->version_number);
        $this->assertSame($v1->id, $v4->source_version_id);
        $this->assertSame('Course A immutable', $v4->title);
        $this->assertSame(CourseVersion::STATUS_DRAFT, $v4->status);
        $this->assertSame($v3->id, $course->fresh()->published_version_id);

        $this->actingAs($admin)->withSession($this->twoFactor())->get(route('admin.content-updates.show', $draft))
            ->assertOk()->assertSee('Khôi phục phiên bản')->assertSee('Nguồn khôi phục: V1')->assertSee('V4');

        app(ContentUpdateService::class)->applyApprovedUpdate($draft, $admin);
        $this->assertSame(CourseVersion::STATUS_SUPERSEDED, $v3->fresh()->status);
        $this->assertSame(CourseVersion::STATUS_PUBLISHED, $v4->fresh()->status);
        $this->assertSame($v4->id, $course->fresh()->published_version_id);
        $this->assertSame('Course A immutable', $course->fresh()->title);
        $this->assertSame(CourseVersion::STATUS_SUPERSEDED, $v1->fresh()->status);

        try {
            app(ContentUpdateService::class)->applyApprovedUpdate($draft->fresh(), $admin);
            $this->fail('Rollback approval must remain terminal.');
        } catch (ValidationException) {
            $this->assertSame(4, $course->versions()->count());
            $this->assertSame($v4->id, $course->fresh()->published_version_id);
        }
    }

    public function test_rejected_rollback_preserves_current_and_revision_creates_v5(): void
    {
        [$instructor, $admin, $course] = $this->context();
        [$v1, , $v3] = $this->threeCourseVersions($course, $instructor, $admin);
        $draft = app(ContentVersionRollbackService::class)->createDraft($course, 'course', $v1->id, $instructor, 'Cần thử lại V1.');
        app(CourseReviewService::class)->submitForReview($course->fresh(), $instructor);
        $v4 = CourseVersion::where('content_update_id', $draft->id)->sole();

        app(ContentUpdateService::class)->rejectUpdate($draft->fresh(), $admin, 'Cần bổ sung lý do chi tiết hơn.');
        $this->assertSame(ContentUpdate::STATUS_REJECTED, $draft->fresh()->status);
        $this->assertSame(CourseVersion::STATUS_REJECTED, $v4->fresh()->status);
        $this->assertSame(CourseVersion::STATUS_PUBLISHED, $v3->fresh()->status);
        $this->assertSame($v3->id, $course->fresh()->published_version_id);
        $this->assertSame('Course A immutable', $v1->fresh()->title);

        $revision = app(ContentUpdateService::class)->createRevisionFromRejected($draft->fresh(), $instructor);
        $this->assertSame($v1->id, data_get($revision->metadata, 'source_version_id'));
        app(CourseReviewService::class)->submitForReview($course->fresh(), $instructor);
        $v5 = CourseVersion::where('content_update_id', $revision->id)->sole();
        $this->assertSame(5, $v5->version_number);
        $this->assertSame($v1->id, $v5->source_version_id);
        $this->assertNotSame($v4->id, $v5->id);
    }

    public function test_current_rejected_and_cross_course_sources_are_blocked_without_writes(): void
    {
        [$instructor, $admin, $course] = $this->context();
        [$v1, , $v3] = $this->threeCourseVersions($course, $instructor, $admin);
        $service = app(ContentVersionRollbackService::class);
        $before = ContentUpdate::count();

        foreach ([$v3] as $invalidSource) {
            try {
                $service->createDraft($course, 'course', $invalidSource->id, $instructor, 'Nguồn không hợp lệ.');
                $this->fail('Current version was accepted as rollback source.');
            } catch (ValidationException) {
                $this->assertSame($before, ContentUpdate::count());
            }
        }

        $rejected = CourseVersion::create([
            ...$v1->only(['course_id', 'title', 'slug', 'short_description', 'description', 'objectives', 'requirements', 'target_audience', 'category_id', 'level', 'language', 'price']),
            'version_number' => 4, 'status' => CourseVersion::STATUS_REJECTED,
        ]);
        try {
            $service->createDraft($course, 'course', $rejected->id, $instructor, 'Nguồn bị từ chối.');
            $this->fail('Rejected version was accepted as rollback source.');
        } catch (ValidationException) {
            $this->assertSame($before, ContentUpdate::count());
        }

        [, , $otherCourse] = $this->context('other');
        $otherV1 = $otherCourse->publishedVersion;
        $otherV1->forceFill(['status' => CourseVersion::STATUS_SUPERSEDED])->save();
        try {
            $service->createDraft($course, 'course', $otherV1->id, $instructor, 'Cross-course injection.');
            $this->fail('Cross-course source was accepted.');
        } catch (ValidationException) {
            $this->assertSame($before, ContentUpdate::count());
        }
    }

    public function test_course_section_and_lesson_rollbacks_do_not_recursively_change_children_or_assignment(): void
    {
        [$instructor, $admin, $course, $section, $lesson, $assignmentLesson, $assignment] = $this->context();
        $courseV1 = $course->publishedVersion;
        $sectionV1 = $section->publishedVersion;
        $lessonV1 = $lesson->publishedVersion;

        $this->publishUpdate($course, $instructor, $admin, 'course', $course->id, ['title' => 'Course metadata V2']);
        $this->publishUpdate($course, $instructor, $admin, 'chapter', $section->id, ['title' => 'Section V2']);
        $this->publishUpdate($course, $instructor, $admin, 'lesson', $lesson->id, ['title' => 'Lesson V2']);
        $sectionPointer = $section->fresh()->published_version_id;
        $lessonPointer = $lesson->fresh()->published_version_id;
        $assignmentPointer = $assignment->fresh()->published_version_id;

        $this->approveRollback($course, $instructor, $admin, 'course', $courseV1->id);
        $this->assertSame($sectionPointer, $section->fresh()->published_version_id);
        $this->assertSame($lessonPointer, $lesson->fresh()->published_version_id);

        $this->approveRollback($course, $instructor, $admin, 'chapter', $sectionV1->id);
        $this->assertSame($lessonPointer, $lesson->fresh()->published_version_id);

        $this->approveRollback($course, $instructor, $admin, 'lesson', $lessonV1->id);
        $this->assertSame($assignmentPointer, $assignment->fresh()->published_version_id);
        $this->assertSame($assignmentLesson->id, $assignment->fresh()->lesson_id);
    }

    public function test_assignment_rollback_preserves_submission_version_bindings(): void
    {
        [$instructor, $admin, $course, , , , $assignment] = $this->context();
        $studentA = User::factory()->create(['role' => 'student']);
        $studentB = User::factory()->create(['role' => 'student']);
        $studentC = User::factory()->create(['role' => 'student']);
        $v1 = $assignment->publishedVersion;
        $submissionA = Submission::create(['assignment_id' => $assignment->id, 'assignment_version_id' => $v1->id, 'user_id' => $studentA->id, 'attempt_number' => 1, 'allowed_attempts' => 1, 'status' => 'submitted']);

        [, $v2] = $this->publishUpdate($course, $instructor, $admin, 'assignment', $assignment->id, ['title' => 'Assignment V2', 'max_score' => 200, 'passing_score' => 120]);
        $submissionB = Submission::create(['assignment_id' => $assignment->id, 'assignment_version_id' => $v2->id, 'user_id' => $studentB->id, 'attempt_number' => 1, 'allowed_attempts' => 1, 'status' => 'submitted']);

        $v3 = $this->approveRollback($course, $instructor, $admin, 'assignment', $v1->id);
        $submissionC = Submission::create(['assignment_id' => $assignment->id, 'assignment_version_id' => $assignment->fresh()->published_version_id, 'user_id' => $studentC->id, 'attempt_number' => 1, 'allowed_attempts' => 1, 'status' => 'submitted']);

        $this->assertSame(3, $v3->version_number);
        $this->assertSame($v1->id, $v3->source_version_id);
        $this->assertSame($v1->id, $submissionA->fresh()->assignment_version_id);
        $this->assertSame($v2->id, $submissionB->fresh()->assignment_version_id);
        $this->assertSame($v3->id, $submissionC->fresh()->assignment_version_id);
        $this->assertSame(100, $v1->fresh()->max_score);
        $this->assertSame(200, $v2->fresh()->max_score);
        $this->assertSame(100, $v3->fresh()->max_score);
    }

    public function test_archived_lesson_history_remains_visible_but_rollback_is_disabled(): void
    {
        [$instructor, $admin, $course, , $lesson] = $this->context();
        $v1 = $lesson->publishedVersion;
        $this->publishUpdate($course, $instructor, $admin, 'lesson', $lesson->id, ['title' => 'Lesson V2 before archive']);
        $delete = ContentUpdate::create([
            'course_id' => $course->id, 'type' => 'lesson', 'action' => ContentUpdate::ACTION_DELETE,
            'entity_id' => $lesson->id, 'payload' => [], 'status' => ContentUpdate::STATUS_PENDING,
            'created_by' => $instructor->id, 'submitted_at' => now(),
        ]);
        app(ContentUpdateService::class)->applyApprovedUpdate($delete, $admin);

        $history = app(ContentVersionHistoryService::class);
        $timeline = $history->timeline($course, 'lesson');
        $this->assertCount(3, $timeline->getCollection());
        $this->assertCount(2, $timeline->getCollection()->where('entity_id', $lesson->id));
        $detail = $history->detail($course, 'lesson', $history->resolve($course, 'lesson', $v1->id));
        $this->assertTrue($detail['is_archived']);
        $this->assertFalse($detail['rollback_eligible']);

        $this->actingAs($instructor)->withSession($this->twoFactor())->get(route('instructor.courses.versions.show', [$course, 'lesson', $v1->id]))
            ->assertOk()->assertSee('Đã lưu trữ');
        $this->actingAs($instructor)->withSession($this->twoFactor())->get(route('instructor.courses.versions.rollback.confirm', [$course, 'lesson', $v1->id]))
            ->assertStatus(422);
    }

    public function test_quiz_history_is_read_only_and_uses_existing_quiz_versions(): void
    {
        [$instructor, , $course, $section] = $this->context();
        $lesson = Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Quiz history lesson',
            'type' => Lesson::TYPE_QUIZ,
            'sort_order' => 3,
            'status' => Lesson::STATUS_PUBLISHED,
        ]);
        $quiz = Quiz::create([
            'lesson_id' => $lesson->id,
            'title' => 'Quiz V2',
            'description' => 'Current quiz',
            'pass_score' => 80,
            'is_active' => true,
        ]);
        $v1 = $quiz->versions()->create([
            'version' => 1,
            'title' => 'Quiz V1',
            'description' => 'Historical quiz',
            'pass_score' => 70,
            'status' => QuizVersion::STATUS_SUPERSEDED,
            'created_by' => $instructor->id,
            'published_at' => now()->subDay(),
        ]);
        $v2 = $quiz->versions()->create([
            'version' => 2,
            'title' => 'Quiz V2',
            'description' => 'Current quiz',
            'pass_score' => 80,
            'status' => QuizVersion::STATUS_PUBLISHED,
            'created_by' => $instructor->id,
            'published_at' => now(),
        ]);
        $quiz->update(['current_published_version_id' => $v2->id]);

        $history = app(ContentVersionHistoryService::class);
        $timeline = $history->timeline($course, ContentUpdate::TYPE_QUIZ);
        $this->assertSame([2, 1], $timeline->getCollection()->pluck('version_number')->all());
        $this->assertTrue($timeline->getCollection()->first()['is_current']);
        $detail = $history->detail($course, ContentUpdate::TYPE_QUIZ, $history->resolve($course, ContentUpdate::TYPE_QUIZ, $v1->id));
        $this->assertFalse($detail['rollback_eligible']);
        $this->assertSame(0, $detail['fields']['question_count']['value']);
        $fields = collect(app(ContentVersionComparisonService::class)->compare($course, ContentUpdate::TYPE_QUIZ, $v1, $v2))->keyBy('key');
        $this->assertSame('Quiz V1', $fields['title']['old']);
        $this->assertSame('Quiz V2', $fields['title']['new']);

        $this->actingAs($instructor)->withSession($this->twoFactor())
            ->get(route('instructor.courses.versions.rollback.confirm', [$course, ContentUpdate::TYPE_QUIZ, $v1->id]))
            ->assertStatus(422);
    }

    public function test_malicious_rollback_fields_are_ignored_and_activation_failure_rolls_back(): void
    {
        [$instructor, $admin, $course, , $lesson] = $this->context();
        $v1 = $lesson->publishedVersion;
        [, $v2] = $this->publishUpdate($course, $instructor, $admin, 'lesson', $lesson->id, ['title' => 'Lesson V2 current']);

        $this->actingAs($instructor)->withSession($this->twoFactor())->post(route('instructor.courses.versions.rollback.store', [$course, 'lesson', $v1->id]), [
            'reason' => 'Khôi phục an toàn.',
            'version_number' => 999,
            'status' => 'published',
            'published_version_id' => $v1->id,
            'source_version_id' => $v2->id,
            'content_update_id' => 999,
            'created_by' => $admin->id,
        ])->assertRedirect();
        $draft = ContentUpdate::where('course_id', $course->id)->where('status', ContentUpdate::STATUS_DRAFT)->latest('id')->firstOrFail();
        $this->assertSame($v1->id, data_get($draft->metadata, 'source_version_id'));
        $this->assertArrayNotHasKey('version_number', $draft->payload);
        $this->assertSame($instructor->id, $draft->created_by);
        try {
            app(ContentUpdateService::class)->updateDraft($draft, ['title' => 'Injected after creation']);
            $this->fail('Rollback snapshot draft was mutable.');
        } catch (ValidationException) {
            $this->assertSame('Lesson V1', $draft->fresh()->payload['title']);
        }

        app(CourseReviewService::class)->submitForReview($course->fresh(), $instructor);
        $candidate = LessonVersion::where('content_update_id', $draft->id)->sole();
        $mock = \Mockery::mock(ContentVersionService::class)->makePartial();
        $mock->shouldReceive('publishLessonVersion')->once()->andThrow(ValidationException::withMessages(['version' => 'synthetic projection failure']));
        $this->app->instance(ContentVersionService::class, $mock);

        try {
            app(ContentUpdateService::class)->applyApprovedUpdate($draft->fresh(), $admin);
            $this->fail('Synthetic rollback failure did not abort activation.');
        } catch (ValidationException) {
            $this->assertSame(ContentUpdate::STATUS_PENDING, $draft->fresh()->status);
            $this->assertSame($v2->id, $lesson->fresh()->published_version_id);
            $this->assertSame(LessonVersion::STATUS_PUBLISHED, $v2->fresh()->status);
            $this->assertSame(LessonVersion::STATUS_DRAFT, $candidate->fresh()->status);
            $this->assertSame('Lesson V2 current', $lesson->fresh()->title);
        }
    }

    /** @return array{0: User, 1: User, 2: Course, 3: CourseSection, 4: Lesson, 5: Lesson, 6: Assignment} */
    private function context(string $suffix = ''): array
    {
        $instructor = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'approved', 'is_active' => true, 'email_verified_at' => now()]);
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true, 'email_verified_at' => now()]);
        $category = Category::create(['name' => 'Version History '.$suffix, 'slug' => 'version-history-'.($suffix ?: uniqid())]);
        $course = Course::create([
            'instructor_id' => $instructor->id, 'category_id' => $category->id,
            'title' => 'Course A immutable', 'slug' => 'course-history-'.($suffix ?: uniqid()),
            'short_description' => 'Initial', 'description' => 'Initial description',
            'status' => Course::STATUS_PUBLISHED, 'is_published' => true, 'published_at' => now(),
            'copyright_agreed' => true, 'price' => 100000, 'language' => 'vi', 'level' => 'beginner',
        ]);
        $section = CourseSection::create(['course_id' => $course->id, 'title' => 'Section V1', 'description' => 'Section history', 'sort_order' => 1]);
        $lesson = Lesson::create(['course_id' => $course->id, 'section_id' => $section->id, 'title' => 'Lesson V1', 'type' => Lesson::TYPE_DOCUMENT, 'content' => 'Lesson immutable A', 'document_file' => 'private/docs/v1.pdf', 'sort_order' => 1, 'status' => Lesson::STATUS_PUBLISHED]);
        $assignmentLesson = Lesson::create(['course_id' => $course->id, 'section_id' => $section->id, 'title' => 'Assignment Lesson', 'type' => Lesson::TYPE_ASSIGNMENT, 'content' => 'Instructions V1', 'sort_order' => 2, 'status' => Lesson::STATUS_PUBLISHED]);
        $assignment = Assignment::create(['course_id' => $course->id, 'lesson_id' => $assignmentLesson->id, 'title' => 'Assignment V1', 'description' => 'Contract V1', 'instructions' => 'Instructions V1', 'due_days' => 7, 'max_score' => 100, 'passing_score' => 70, 'is_required' => true]);
        app(ContentVersionService::class)->publishInitialCourseTree($course, $admin);

        return [$instructor, $admin, $course->fresh(), $section->fresh(), $lesson->fresh(), $assignmentLesson->fresh(), $assignment->fresh()];
    }

    /** @return array{0: CourseVersion, 1: CourseVersion, 2: CourseVersion} */
    private function threeCourseVersions(Course $course, User $instructor, User $admin): array
    {
        $v1 = $course->publishedVersion;
        [, $v2] = $this->publishUpdate($course, $instructor, $admin, 'course', $course->id, ['title' => 'Course B historical']);
        [, $v3] = $this->publishUpdate($course, $instructor, $admin, 'course', $course->id, ['title' => 'Course C current']);

        return [$v1->fresh(), $v2->fresh(), $v3->fresh()];
    }

    /** @return array{0: ContentUpdate, 1: CourseVersion|CourseSectionVersion|LessonVersion|AssignmentVersion} */
    private function publishUpdate(Course $course, User $instructor, User $admin, string $type, int $entityId, array $payload): array
    {
        $update = ContentUpdate::create([
            'course_id' => $course->id, 'type' => $type, 'action' => ContentUpdate::ACTION_UPDATE,
            'entity_id' => $entityId, 'payload' => $payload, 'status' => ContentUpdate::STATUS_PENDING,
            'created_by' => $instructor->id, 'submitted_at' => now(),
        ]);
        app(ContentVersionService::class)->materializeCandidate($update, $instructor);
        $class = match ($type) {
            'course' => CourseVersion::class,
            'chapter' => CourseSectionVersion::class,
            'lesson' => LessonVersion::class,
            'assignment' => AssignmentVersion::class,
        };
        $candidate = $class::where('content_update_id', $update->id)->firstOrFail();
        app(ContentUpdateService::class)->applyApprovedUpdate($update, $admin);

        return [$update->fresh(), $candidate->fresh()];
    }

    private function approveRollback(Course $course, User $instructor, User $admin, string $type, int $sourceVersionId): CourseVersion|CourseSectionVersion|LessonVersion|AssignmentVersion
    {
        $update = app(ContentVersionRollbackService::class)->createDraft($course->fresh(), $type, $sourceVersionId, $instructor, 'Khôi phục snapshot lịch sử đã xác minh.');
        app(CourseReviewService::class)->submitForReview($course->fresh(), $instructor);
        $class = match ($type) {
            'course' => CourseVersion::class,
            'chapter' => CourseSectionVersion::class,
            'lesson' => LessonVersion::class,
            'assignment' => AssignmentVersion::class,
        };
        $candidate = $class::where('content_update_id', $update->id)->firstOrFail();
        app(ContentUpdateService::class)->applyApprovedUpdate($update->fresh(), $admin);

        return $candidate->fresh();
    }

    /** @return array<string, int> */
    private function twoFactor(): array
    {
        return ['two_factor_passed_at' => now()->timestamp];
    }
}
