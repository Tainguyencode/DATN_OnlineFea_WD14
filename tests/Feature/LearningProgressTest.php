<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\User;
use App\Services\LearningProgressService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LearningProgressTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_without_enrollment_cannot_update_progress(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->publishedCourse();
        $lesson = $course->lessons->first();

        $this->actingAs($student)
            ->postJson(route('courses.lessons.progress', [$course, $lesson]), [
                'watched_seconds' => 100,
                'duration_seconds' => 300,
            ])
            ->assertForbidden();
    }

    public function test_enrolled_student_can_update_progress(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->publishedCourse();
        $lesson = $course->lessons->first();

        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'active',
            'enrolled_at' => now(),
        ]);

        $response = $this->actingAs($student)
            ->postJson(route('courses.lessons.progress', [$course, $lesson]), [
                'watched_seconds' => 240,
                'duration_seconds' => 300,
            ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['lesson_progress', 'course_progress', 'lesson_completed', 'course_completed']);
    }

    public function test_progress_does_not_exceed_one_hundred_percent(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->publishedCourse();
        $lesson = $course->lessons->first();

        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'active',
            'enrolled_at' => now(),
        ]);

        $result = app(LearningProgressService::class)->recordLessonProgress(
            $student->id,
            $course,
            $lesson,
            9999,
            300,
            false,
        );

        $this->assertLessThanOrEqual(100, $result['lesson_progress']);
    }

    public function test_completed_enrollment_can_still_access_learning(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->publishedCourse();
        $lesson = $course->lessons->first();

        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => Enrollment::STATUS_COMPLETED,
            'progress_percent' => 100,
            'completed_at' => now(),
            'enrolled_at' => now()->subDays(7),
        ]);

        $this->actingAs($student)
            ->get(route('courses.lessons.show', [$course, $lesson]))
            ->assertOk();

        $this->actingAs($student)
            ->postJson(route('courses.lessons.progress', [$course, $lesson]), [
                'watched_seconds' => 120,
                'duration_seconds' => 300,
            ])
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    private function publishedCourse(): Course
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => Category::create(['name' => 'Test', 'slug' => 'test-'.uniqid()])->id,
            'title' => 'Published',
            'slug' => 'published-'.uniqid(),
            'short_description' => 'Short',
            'description' => 'Desc',
            'thumbnail' => 't.png',
            'price' => 0,
            'language' => 'vi',
            'status' => 'published',
            'is_published' => true,
        ]);

        $section = CourseSection::create(['course_id' => $course->id, 'title' => 'S1', 'sort_order' => 1]);
        Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'L1',
            'type' => 'video',
            'video_url' => 'https://example.com/v.mp4',
            'duration_seconds' => 300,
            'content' => 'content',
            'sort_order' => 1,
            'is_required' => true,
        ]);

        return $course->fresh(['lessons', 'courseSections.lessons']);
    }
}
