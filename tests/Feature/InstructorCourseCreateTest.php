<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstructorCourseCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_instructor_can_create_course_without_language_field_in_form(): void
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $parentCategory = Category::create(['name' => 'IT', 'slug' => 'it', 'status' => true]);
        $category = Category::create(['name' => 'Web', 'slug' => 'web', 'parent_id' => $parentCategory->id, 'status' => true]);

        $response = $this->actingAs($instructor)->post(route('instructor.courses.store'), [
            'title' => 'Khóa học mới test',
            'category_id' => $category->id,
            'description' => 'Mô tả khóa học đủ dài để lưu vào database.',
            'price' => 199000,
            'discount_price' => 99000,
            'level' => 'beginner',
        ]);

        $response->assertRedirect();

        $course = Course::where('title', 'Khóa học mới test')->first();
        $this->assertNotNull($course);
        $this->assertSame('vi', $course->language);
        $this->assertSame('draft', $course->status);
        $this->assertEquals(99000, (float) $course->sale_price);
    }
}
