<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\InstructorProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstructorCourseCategoryAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_form_only_lists_registered_categories_and_auto_selects_the_only_one(): void
    {
        [$instructor, $allowed, $outside] = $this->instructorWithTeachingFields(1);

        $response = $this->actingAs($instructor)->get(route('instructor.courses.create'));

        $response->assertOk()
            ->assertSee($allowed->name)
            ->assertDontSee($outside->name)
            ->assertSee('value="'.$allowed->id.'" selected', false);
    }

    public function test_instructor_cannot_create_course_in_unregistered_category(): void
    {
        [$instructor, $allowed, $outside] = $this->instructorWithTeachingFields(1);

        $response = $this->actingAs($instructor)->from(route('instructor.courses.create'))->post(route('instructor.courses.store'), $this->coursePayload($outside));

        $response->assertRedirect(route('instructor.courses.create'))
            ->assertSessionHasErrors(['category_id' => 'Bạn không có quyền tạo khóa học thuộc ngành này.']);
        $this->assertDatabaseMissing('courses', ['title' => 'Khóa học bị từ chối']);
    }

    public function test_instructor_can_create_course_in_registered_category(): void
    {
        [$instructor, $allowed] = $this->instructorWithTeachingFields(1);

        $response = $this->actingAs($instructor)->post(route('instructor.courses.store'), $this->coursePayload($allowed, 'Khóa học hợp lệ'));

        $response->assertRedirect();
        $this->assertDatabaseHas('courses', ['instructor_id' => $instructor->id, 'category_id' => $allowed->id, 'title' => 'Khóa học hợp lệ']);
    }

    public function test_instructor_cannot_change_course_to_unregistered_category(): void
    {
        [$instructor, $allowed, $outside] = $this->instructorWithTeachingFields(1);
        $course = $this->course($instructor, $allowed);

        $response = $this->actingAs($instructor)->from(route('instructor.courses.edit', $course))->put(route('instructor.courses.update', $course), $this->coursePayload($outside, $course->title));

        $response->assertRedirect(route('instructor.courses.edit', $course))
            ->assertSessionHasErrors(['category_id' => 'Bạn không có quyền tạo khóa học thuộc ngành này.']);
        $this->assertDatabaseHas('courses', ['id' => $course->id, 'category_id' => $allowed->id]);
    }

    public function test_curriculum_actions_are_rejected_when_course_category_is_not_in_teaching_fields(): void
    {
        [$instructor, $allowed, $outside] = $this->instructorWithTeachingFields(1);
        $course = $this->course($instructor, $outside);
        $section = CourseSection::create(['course_id' => $course->id, 'title' => 'Chương 1', 'sort_order' => 0]);

        $this->actingAs($instructor)
            ->post(route('instructor.courses.sections.lessons.store', [$course, $section]), [
                'title' => 'Bài học không hợp lệ', 'type' => 'document', 'sort_order' => 0, 'status' => 'draft',
            ])
            ->assertForbidden();
    }

    public function test_s3_upload_is_rejected_when_course_category_is_not_in_teaching_fields(): void
    {
        [$instructor, $allowed, $outside] = $this->instructorWithTeachingFields(1);
        $course = $this->course($instructor, $outside);

        $this->actingAs($instructor)
            ->postJson(route('instructor.courses.s3.multipart.create', $course), ['filename' => 'bai-giang.mp4'])
            ->assertForbidden();
    }

    /** @return array{0: User, 1: Category, 2: Category} */
    private function instructorWithTeachingFields(int $fieldCount): array
    {
        $instructor = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'approved', 'email_verified_at' => now()]);
        $parent = Category::create(['name' => 'Công nghệ', 'slug' => 'cong-nghe', 'status' => true]);
        $allowed = Category::create(['name' => 'Phát triển Web', 'slug' => 'phat-trien-web', 'parent_id' => $parent->id, 'status' => true]);
        $outside = Category::create(['name' => 'Marketing', 'slug' => 'marketing', 'parent_id' => $parent->id, 'status' => true]);
        $profile = InstructorProfile::create(['user_id' => $instructor->id]);
        $profile->teachingCategories()->attach($allowed->id, ['is_primary' => true]);

        if ($fieldCount > 1) {
            $profile->teachingCategories()->attach($outside->id, ['is_primary' => false]);
        }

        return [$instructor, $allowed, $outside];
    }

    private function coursePayload(Category $category, string $title = 'Khóa học bị từ chối'): array
    {
        return ['title' => $title, 'category_id' => $category->id, 'price' => 100000, 'level' => 'beginner', 'language' => 'vi'];
    }

    private function course(User $instructor, Category $category): Course
    {
        return Course::create([
            'instructor_id' => $instructor->id, 'category_id' => $category->id, 'title' => 'Khóa học hiện có',
            'slug' => 'khoa-hoc-hien-co-'.uniqid(), 'price' => 100000, 'language' => 'vi',
            'level' => 'beginner', 'status' => Course::STATUS_DRAFT, 'is_published' => false,
        ]);
    }
}
