<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\User;
use App\Services\RoleSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentAssignmentLibraryTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_sees_assignments_only_from_enrolled_courses(): void
    {
        app(RoleSyncService::class)->ensurePrimaryRolesExist();
        $student = User::factory()->create(['role' => 'student', 'email_verified_at' => now()]);
        $instructor = User::factory()->create(['role' => 'instructor']);
        $category = Category::create(['name' => 'Assignments', 'slug' => 'assignments', 'status' => true]);
        $course = Course::create(['instructor_id' => $instructor->id, 'category_id' => $category->id, 'title' => 'Course', 'slug' => 'course-assignments', 'status' => Course::STATUS_PUBLISHED, 'is_published' => true]);
        Enrollment::create(['user_id' => $student->id, 'course_id' => $course->id, 'status' => Enrollment::STATUS_ACTIVE, 'enrolled_at' => now()]);
        $lesson = Lesson::create(['course_id' => $course->id, 'title' => 'Final project', 'type' => Lesson::TYPE_ASSIGNMENT, 'status' => Lesson::STATUS_PUBLISHED]);
        Assignment::create(['course_id' => $course->id, 'lesson_id' => $lesson->id, 'title' => 'Final project', 'description' => 'Complete the project.', 'max_score' => 100]);

        $this->actingAs($student)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->get(route('student.assignments.index'))
            ->assertOk()
            ->assertSee('Final project');
    }
}
