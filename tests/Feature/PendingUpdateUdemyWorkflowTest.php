<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ContentUpdate;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\User;
use App\Services\ContentUpdateService;
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
        $category = Category::create(['name' => 'Web Dev', 'slug' => 'web-dev']);

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
            ],
            'status' => ContentUpdate::STATUS_PENDING,
            'created_by' => $instructor->id,
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->get(route('admin.courses.review', $course));

        $response->assertOk();
        $response->assertSee('Lesson Pending Review');
        $response->assertSee('Đang xử lý HLS');
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
}
