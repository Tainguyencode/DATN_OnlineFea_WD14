<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Category;
use App\Models\ContentUpdate;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\InstructorProfile;
use App\Models\Lesson;
use App\Models\User;
use App\Services\ContentUpdateService;
use App\Services\CourseReviewService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PendingUpdateUdemyWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function createPublishedCourseWithSection(): array
    {
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'approved',
            'is_active' => true,
        ]);
        $category = Category::create(['name' => 'Web Dev', 'slug' => 'web-dev', 'status' => true]);
        $profile = InstructorProfile::create(['user_id' => $instructor->id]);
        $profile->teachingCategories()->attach($category->id, ['is_primary' => true]);

        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Published Udemy Course',
            'slug' => 'published-udemy-course',
            'short_description' => 'Short desc',
            'description' => 'Detailed description of the course',
            'objectives' => 'Learn web dev',
            'target_audience' => 'Students',
            'requirements' => 'None',
            'price' => 100000,
            'language' => 'vi',
            'level' => 'beginner',
            'status' => Course::STATUS_PUBLISHED,
            'is_published' => true,
        ]);

        $section = CourseSection::create([
            'course_id' => $course->id,
            'title' => 'Chương 1: Căn bản',
            'sort_order' => 1,
        ]);

        $publishedLesson = Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Lesson 1 Published',
            'type' => 'video',
            'video_url' => 'https://example.com/l1.mp4',
            'duration_seconds' => 300,
            'sort_order' => 1,
            'status' => 'published',
        ]);

        return [$instructor, $course, $section, $publishedLesson];
    }

    public function test_instructor_adding_lesson_to_published_course_creates_content_update_draft_and_not_real_lesson(): void
    {
        [$instructor, $course, $section] = $this->createPublishedCourseWithSection();

        $this->actingAs($instructor)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->post(route('instructor.courses.sections.lessons.store', [$course, $section]), [
                'title' => 'Lesson 2 Draft Update',
                'type' => 'video',
                'video_url' => 'https://example.com/l2.mp4',
                'duration' => 600,
                'sort_order' => 2,
                'is_preview' => '0',
            ])
            ->assertRedirect();

        // Must NOT exist in lessons table
        $this->assertDatabaseMissing('lessons', [
            'course_id' => $course->id,
            'title' => 'Lesson 2 Draft Update',
        ]);

        // Must exist in content_updates table with status = draft
        $this->assertDatabaseHas('content_updates', [
            'course_id' => $course->id,
            'type' => ContentUpdate::TYPE_LESSON,
            'action' => ContentUpdate::ACTION_CREATE,
            'status' => ContentUpdate::STATUS_DRAFT,
            'created_by' => $instructor->id,
        ]);
    }

    public function test_instructor_can_edit_a_draft_lesson_content_update(): void
    {
        [$instructor, $course, $section] = $this->createPublishedCourseWithSection();
        $update = ContentUpdate::create([
            'course_id' => $course->id,
            'type' => ContentUpdate::TYPE_LESSON,
            'action' => ContentUpdate::ACTION_CREATE,
            'status' => ContentUpdate::STATUS_DRAFT,
            'created_by' => $instructor->id,
            'payload' => [
                'section_id' => $section->id,
                'title' => 'Tên cũ',
                'type' => Lesson::TYPE_VIDEO,
                'video_url' => 'https://example.com/old.mp4',
                'duration' => 60,
                'status' => Lesson::STATUS_DRAFT,
            ],
        ]);

        $this->actingAs($instructor)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->put(route('instructor.courses.content-updates.update', [$course, $update]), [
                'title' => 'Tên mới',
                'type' => Lesson::TYPE_VIDEO,
                'video_url' => 'https://example.com/old.mp4',
                'duration' => 90,
                'sort_order' => 1,
                'status' => Lesson::STATUS_DRAFT,
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertSame('Tên mới', $update->fresh()->payload['title']);
        $this->assertSame(90, $update->fresh()->payload['duration_seconds']);
    }

    public function test_pending_update_course_keeps_using_content_update_workflow(): void
    {
        [$instructor, $course, $section] = $this->createPublishedCourseWithSection();
        $course->update(['status' => Course::STATUS_PENDING_UPDATE]);

        $this->actingAs($instructor)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->post(route('instructor.courses.sections.lessons.store', [$course, $section]), [
                'title' => 'Additional pending update lesson',
                'type' => Lesson::TYPE_VIDEO,
                'video_url' => 'https://example.com/pending-update.mp4',
                'duration' => 300,
            ])
            ->assertRedirect();

        $this->assertDatabaseMissing('lessons', [
            'course_id' => $course->id,
            'title' => 'Additional pending update lesson',
        ]);
        $this->assertDatabaseHas('content_updates', [
            'course_id' => $course->id,
            'type' => ContentUpdate::TYPE_LESSON,
            'action' => ContentUpdate::ACTION_CREATE,
            'status' => ContentUpdate::STATUS_DRAFT,
        ]);
    }

    public function test_instructor_curriculum_renders_merged_lessons_with_draft_badge(): void
    {
        [$instructor, $course, $section, $publishedLesson] = $this->createPublishedCourseWithSection();

        ContentUpdate::create([
            'type' => ContentUpdate::TYPE_LESSON,
            'action' => ContentUpdate::ACTION_CREATE,
            'course_id' => $course->id,
            'entity_id' => null,
            'payload' => [
                'section_id' => $section->id,
                'title' => 'Lesson 2 In Draft Update',
                'type' => 'video',
                'duration' => 600,
                'sort_order' => 2,
            ],
            'status' => ContentUpdate::STATUS_DRAFT,
            'created_by' => $instructor->id,
        ]);

        $response = $this->actingAs($instructor)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->get(route('instructor.courses.curriculum', $course));

        $response->assertOk();
        $response->assertSee('Lesson 1 Published');
        $response->assertSee('Lesson 2 In Draft Update');
        $response->assertSee('Draft');
    }

    public function test_student_never_sees_draft_or_pending_lessons(): void
    {
        [$instructor, $course, $section, $publishedLesson] = $this->createPublishedCourseWithSection();

        ContentUpdate::create([
            'type' => ContentUpdate::TYPE_LESSON,
            'action' => ContentUpdate::ACTION_CREATE,
            'course_id' => $course->id,
            'entity_id' => null,
            'payload' => [
                'section_id' => $section->id,
                'title' => 'Secret Draft Lesson',
                'type' => 'video',
            ],
            'status' => ContentUpdate::STATUS_DRAFT,
            'created_by' => $instructor->id,
        ]);

        $studentLessons = Lesson::where('course_id', $course->id)->pluck('title')->toArray();

        $this->assertContains('Lesson 1 Published', $studentLessons);
        $this->assertNotContains('Secret Draft Lesson', $studentLessons);
    }

    public function test_admin_review_page_loads_without_404_and_displays_merged_draft_and_hls_processing_guard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        [$instructor, $course, $section] = $this->createPublishedCourseWithSection();

        ContentUpdate::create([
            'type' => ContentUpdate::TYPE_LESSON,
            'action' => ContentUpdate::ACTION_CREATE,
            'course_id' => $course->id,
            'entity_id' => null,
            'payload' => [
                'section_id' => $section->id,
                'title' => 'Lesson Pending Review',
                'type' => 'video',
                'video_path' => 'lesson-videos-mp4/raw_video.mp4',
                'processing_status' => 'processing',
            ],
            'status' => ContentUpdate::STATUS_PENDING,
            'created_by' => $instructor->id,
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->get(route('admin.courses.review', $course));

        $response->assertOk();
        $response->assertSee('Lesson Pending Review');
        $response->assertSee('Video file');
    }

    public function test_admin_approval_creates_real_lesson_row(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        [$instructor, $course, $section] = $this->createPublishedCourseWithSection();

        $update = ContentUpdate::create([
            'type' => ContentUpdate::TYPE_LESSON,
            'action' => ContentUpdate::ACTION_CREATE,
            'course_id' => $course->id,
            'entity_id' => null,
            'payload' => [
                'section_id' => $section->id,
                'title' => 'Approved Lesson',
                'type' => 'video',
                'duration' => 500,
                'sort_order' => 2,
            ],
            'status' => ContentUpdate::STATUS_PENDING,
            'created_by' => $instructor->id,
        ]);

        app(ContentUpdateService::class)->applyApprovedUpdate($update, $admin);

        $this->assertDatabaseHas('lessons', [
            'course_id' => $course->id,
            'title' => 'Approved Lesson',
        ]);

        $this->assertEquals(ContentUpdate::STATUS_APPROVED, $update->fresh()->status);
    }

    public function test_approved_chapter_update_is_not_merged_again_after_materialization(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        [$instructor, $course] = $this->createPublishedCourseWithSection();

        $update = ContentUpdate::create([
            'type' => ContentUpdate::TYPE_CHAPTER,
            'action' => ContentUpdate::ACTION_CREATE,
            'course_id' => $course->id,
            'entity_id' => null,
            'payload' => [
                'title' => 'Final project',
                'sort_order' => 2,
            ],
            'status' => ContentUpdate::STATUS_PENDING,
            'created_by' => $instructor->id,
        ]);

        app(ContentUpdateService::class)->applyApprovedUpdate($update, $admin);

        $sections = app(ContentUpdateService::class)->mergeCurriculumWithUpdates($course->fresh());

        $this->assertCount(2, $sections);
        $this->assertSame(1, $sections->where('title', 'Final project')->count());
        $this->assertSame($update->fresh()->entity_id, $sections->firstWhere('title', 'Final project')->id);
    }

    public function test_admin_approval_of_assignment_lesson_creates_assignment_atomically(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        [$instructor, $course, $section] = $this->createPublishedCourseWithSection();

        $this->actingAs($instructor)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->post(route('instructor.courses.sections.lessons.store', [$course, $section]), [
                'title' => 'Published assignment update',
                'type' => Lesson::TYPE_ASSIGNMENT,
                'content' => 'Build the final project.',
                'assignment_due_days' => 14,
                'assignment_max_score' => 120,
                'assignment_passing_score' => 80,
                'duration' => 900,
            ])
            ->assertRedirect();

        $update = ContentUpdate::query()
            ->where('course_id', $course->id)
            ->where('type', ContentUpdate::TYPE_LESSON)
            ->where('action', ContentUpdate::ACTION_CREATE)
            ->latest('id')
            ->firstOrFail();

        $course->update(['copyright_agreed' => true]);
        app(CourseReviewService::class)->submitForReview($course->fresh(), $instructor);

        $this->assertSame(ContentUpdate::STATUS_PENDING, $update->fresh()->status);

        app(ContentUpdateService::class)->applyApprovedUpdate($update, $admin);

        $lesson = Lesson::query()
            ->where('course_id', $course->id)
            ->where('title', 'Published assignment update')
            ->firstOrFail();

        $this->assertDatabaseHas('lessons', [
            'id' => $lesson->id,
            'course_id' => $course->id,
            'title' => 'Published assignment update',
        ]);

        $this->assertDatabaseHas('assignments', [
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
            'title' => 'Published assignment update',
            'description' => 'Build the final project.',
            'due_days' => 14,
            'max_score' => 120,
            'passing_score' => 80,
        ]);

        $this->assertSame(ContentUpdate::STATUS_APPROVED, $update->fresh()->status);
    }

    public function test_assignment_approval_rolls_back_lesson_assignment_and_status_when_assignment_persistence_fails(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        [$instructor, $course, $section] = $this->createPublishedCourseWithSection();

        $this->actingAs($instructor)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->post(route('instructor.courses.sections.lessons.store', [$course, $section]), [
                'title' => 'Assignment that must roll back',
                'type' => Lesson::TYPE_ASSIGNMENT,
                'content' => 'This write is intentionally failed.',
                'assignment_due_days' => 7,
                'assignment_max_score' => 100,
                'assignment_passing_score' => 70,
                'duration' => 600,
            ])
            ->assertRedirect();

        $update = ContentUpdate::query()
            ->where('course_id', $course->id)
            ->where('type', ContentUpdate::TYPE_LESSON)
            ->where('action', ContentUpdate::ACTION_CREATE)
            ->latest('id')
            ->firstOrFail();

        $course->update(['copyright_agreed' => true]);
        app(CourseReviewService::class)->submitForReview($course->fresh(), $instructor);
        $this->assertSame(ContentUpdate::STATUS_PENDING, $update->fresh()->status);

        Assignment::creating(static function (): void {
            throw new \RuntimeException('Simulated assignment persistence failure.');
        });

        try {
            app(ContentUpdateService::class)->applyApprovedUpdate($update, $admin);
            $this->fail('The simulated assignment failure was not raised.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('Simulated assignment persistence failure.', $exception->getMessage());
        } finally {
            Assignment::flushEventListeners();
        }

        $this->assertDatabaseMissing('lessons', [
            'course_id' => $course->id,
            'title' => 'Assignment that must roll back',
        ]);
        $this->assertDatabaseMissing('assignments', [
            'course_id' => $course->id,
            'title' => 'Assignment that must roll back',
        ]);
        $this->assertSame(ContentUpdate::STATUS_PENDING, $update->fresh()->status);
    }
}
