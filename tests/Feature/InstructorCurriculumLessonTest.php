<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InstructorCurriculumLessonTest extends TestCase
{
    use RefreshDatabase;

    public function test_lesson_form_starts_with_common_fields_and_type_panels_are_exclusive(): void
    {
        $instructor = $this->signInInstructor();
        [$course] = $this->courseWithSection($instructor);

        $response = $this->get(route('instructor.courses.curriculum', $course));

        $response->assertOk();
        $response->assertSee('data-lesson-form', false);
        $response->assertSee('data-initial-type="none"', false);
        $response->assertSee("x-show=\"selectedType === 'video'\"", false);
        $response->assertSee("x-show=\"selectedType === 'document'\"", false);
        $response->assertSee("x-show=\"selectedType === 'quiz'\"", false);
        $response->assertSee("x-show=\"selectedType === 'assignment'\"", false);
        $response->assertSee("x-bind:disabled=\"selectedType !== 'video'\"", false);
        $response->assertSee("x-bind:disabled=\"selectedType !== 'document'\"", false);
        $response->assertSee("x-bind:disabled=\"selectedType !== 'quiz'\"", false);
        $response->assertSee("x-bind:disabled=\"selectedType !== 'assignment'\"", false);
    }

    public function test_video_lesson_can_be_created_with_only_video_fields(): void
    {
        $instructor = $this->signInInstructor();
        [$course, $section] = $this->courseWithSection($instructor);

        $this->post(route('instructor.courses.sections.lessons.store', [$course, $section]), [
            'title' => 'Video mở đầu',
            'type' => 'video',
            'video_url' => 'https://example.com/video.mp4',
            'duration' => 300,
            'sort_order' => 1,
            'status' => 'published',
            'is_preview' => '1',
        ])->assertRedirect();

        $this->assertDatabaseHas('lessons', [
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Video mở đầu',
            'type' => 'video',
            'video_url' => 'https://example.com/video.mp4',
            'duration_seconds' => 300,
            'is_preview' => true,
            'status' => 'published',
        ]);
    }

    public function test_document_lesson_can_be_created_without_video_data(): void
    {
        $instructor = $this->signInInstructor();
        [$course, $section] = $this->courseWithSection($instructor);

        $this->post(route('instructor.courses.sections.lessons.store', [$course, $section]), [
            'title' => 'Tài liệu cài đặt',
            'type' => 'document',
            'content' => 'Các bước cài đặt môi trường.',
            'duration' => 120,
            'sort_order' => 2,
            'status' => 'draft',
        ])->assertRedirect();

        $lesson = Lesson::query()->where('title', 'Tài liệu cài đặt')->firstOrFail();

        $this->assertSame('document', $lesson->type);
        $this->assertSame('Các bước cài đặt môi trường.', $lesson->content);
        $this->assertNull($lesson->video_url);
        $this->assertNull($lesson->video_path);
    }

    public function test_quiz_lesson_redirects_to_existing_quiz_manager(): void
    {
        $instructor = $this->signInInstructor();
        [$course, $section] = $this->courseWithSection($instructor);

        $response = $this->post(route('instructor.courses.sections.lessons.store', [$course, $section]), [
            'title' => 'Quiz cuối chương',
            'type' => 'quiz',
            'duration' => 60,
            'sort_order' => 3,
            'status' => 'draft',
        ]);

        $lesson = Lesson::query()->where('title', 'Quiz cuối chương')->firstOrFail();

        $response->assertRedirect(route('instructor.courses.lessons.quiz.show', [$course, $lesson]));
        $this->assertSame('quiz', $lesson->type);
        $this->assertNull($lesson->content);

        $this->get(route('instructor.courses.lessons.quiz.show', [$course, $lesson]))->assertOk();
        $this->assertDatabaseHas('quizzes', [
            'lesson_id' => $lesson->id,
            'title' => 'Quiz cuối chương',
        ]);
    }

    public function test_assignment_lesson_creates_assignment_record_from_existing_schema(): void
    {
        $instructor = $this->signInInstructor();
        [$course, $section] = $this->courseWithSection($instructor);

        $this->post(route('instructor.courses.sections.lessons.store', [$course, $section]), [
            'title' => 'Bài tập Laravel',
            'type' => 'assignment',
            'content' => 'Xây dựng CRUD cho khóa học.',
            'assignment_due_days' => 7,
            'assignment_max_score' => 100,
            'assignment_passing_score' => 60,
            'duration' => 180,
            'sort_order' => 4,
            'status' => 'published',
        ])->assertRedirect();

        $lesson = Lesson::query()->where('title', 'Bài tập Laravel')->firstOrFail();

        $this->assertDatabaseHas('assignments', [
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
            'title' => 'Bài tập Laravel',
            'description' => 'Xây dựng CRUD cho khóa học.',
            'due_days' => 7,
            'max_score' => 100,
            'passing_score' => 60,
        ]);
    }

    public function test_validation_depends_on_selected_lesson_type_and_keeps_old_type(): void
    {
        $instructor = $this->signInInstructor();
        [$course, $section] = $this->courseWithSection($instructor);
        $errorBag = 'storeLesson_'.$section->id;

        $this
            ->from(route('instructor.courses.curriculum', $course))
            ->post(route('instructor.courses.sections.lessons.store', [$course, $section]), [
                'title' => 'Video thiếu nội dung',
                'type' => 'video',
                'duration' => 90,
                'sort_order' => 5,
                'status' => 'draft',
            ])
            ->assertRedirect(route('instructor.courses.curriculum', $course))
            ->assertSessionHasErrors(['video_url'], null, $errorBag)
            ->assertSessionHasInput('type', 'video');

        $this
            ->from(route('instructor.courses.curriculum', $course))
            ->post(route('instructor.courses.sections.lessons.store', [$course, $section]), [
                'title' => 'Tài liệu gửi sai field',
                'type' => 'document',
                'video_url' => 'https://example.com/should-not-send.mp4',
                'duration' => 90,
                'sort_order' => 6,
                'status' => 'draft',
            ])
            ->assertRedirect(route('instructor.courses.curriculum', $course))
            ->assertSessionHasErrors(['video_url', 'content'], null, $errorBag)
            ->assertSessionHasInput('type', 'document');
    }

    public function test_edit_lesson_form_opens_current_type_panel(): void
    {
        $instructor = $this->signInInstructor();
        [$course, $section] = $this->courseWithSection($instructor);
        Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Quiz đã có',
            'type' => 'quiz',
            'sort_order' => 1,
            'status' => 'draft',
        ]);

        $this->get(route('instructor.courses.curriculum', $course))
            ->assertOk()
            ->assertSee('data-initial-type="quiz"', false);
    }

    public function test_update_document_lesson_keeps_existing_file_when_no_new_file_is_uploaded(): void
    {
        Storage::fake('public');

        $instructor = $this->signInInstructor();
        [$course, $section] = $this->courseWithSection($instructor);
        Storage::disk('public')->put('lesson-documents/current.pdf', 'old file');

        $lesson = Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Tài liệu cũ',
            'type' => 'document',
            'content' => 'Nội dung cũ',
            'document_file' => 'lesson-documents/current.pdf',
            'duration' => 60,
            'duration_seconds' => 60,
            'sort_order' => 1,
            'status' => 'draft',
        ]);

        $this->put(route('instructor.courses.lessons.update', [$course, $lesson]), [
            'title' => 'Tài liệu đã cập nhật',
            'type' => 'document',
            'content' => 'Nội dung mới',
            'duration' => 90,
            'sort_order' => 1,
            'status' => 'published',
        ])->assertRedirect();

        $lesson->refresh();

        $this->assertSame('lesson-documents/current.pdf', $lesson->document_file);
        Storage::disk('public')->assertExists('lesson-documents/current.pdf');
    }

    public function test_instructor_cannot_modify_other_instructors_curriculum(): void
    {
        $owner = User::factory()->create(['role' => 'instructor']);
        $other = $this->signInInstructor();
        [$course, $section] = $this->courseWithSection($owner);

        $this->actingAs($other)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->post(route('instructor.courses.sections.lessons.store', [$course, $section]), [
                'title' => 'Không được phép',
                'type' => 'video',
                'video_url' => 'https://example.com/video.mp4',
                'duration' => 60,
                'sort_order' => 1,
                'status' => 'draft',
            ])
            ->assertForbidden();
    }

    private function signInInstructor(?User $user = null): User
    {
        $user ??= User::factory()->create(['role' => 'instructor']);

        $this->actingAs($user)->withSession(['two_factor_passed_at' => now()->timestamp]);

        return $user;
    }

    /**
     * @return array{0: Course, 1: CourseSection}
     */
    private function courseWithSection(User $instructor): array
    {
        $category = Category::create([
            'name' => 'Danh mục '.uniqid(),
            'slug' => 'category-'.uniqid(),
        ]);

        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Khóa học '.uniqid(),
            'slug' => 'course-'.uniqid(),
            'short_description' => 'Mô tả ngắn',
            'description' => 'Mô tả khóa học đủ dài',
            'objectives' => 'Mục tiêu học tập',
            'target_audience' => 'Học viên',
            'requirements' => 'Không yêu cầu',
            'price' => 100000,
            'language' => 'vi',
            'level' => 'beginner',
            'status' => Course::STATUS_DRAFT,
            'is_published' => false,
        ]);

        $section = CourseSection::create([
            'course_id' => $course->id,
            'title' => 'Chương 1',
            'sort_order' => 0,
        ]);

        return [$course, $section];
    }
}
