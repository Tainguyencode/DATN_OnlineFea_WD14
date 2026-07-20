<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\StudyGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudyGroupTest extends TestCase
{
    use RefreshDatabase;

    private function createCourseWithEnrollment(User $student): Course
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $category = Category::create(['name' => 'IT', 'slug' => 'it-' . uniqid()]);
        
        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Laravel Advanced',
            'slug' => 'laravel-advanced-' . uniqid(),
            'short_description' => 'Short desc',
            'description' => 'Detailed desc',
            'thumbnail' => 'laravel.png',
            'price' => 299.99,
            'language' => 'vi',
            'status' => 'published',
            'is_published' => true,
        ]);

        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => Enrollment::STATUS_ACTIVE,
            'enrolled_at' => now(),
        ]);

        return $course;
    }

    public function test_student_without_enrollment_cannot_create_study_group(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        
        $instructor = User::factory()->create(['role' => 'instructor']);
        $category = Category::create(['name' => 'IT', 'slug' => 'it-' . uniqid()]);
        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Laravel Advanced',
            'slug' => 'laravel-advanced-' . uniqid(),
            'short_description' => 'Short desc',
            'description' => 'Detailed desc',
            'thumbnail' => 'laravel.png',
            'price' => 299.99,
            'language' => 'vi',
            'status' => 'published',
            'is_published' => true,
        ]);

        $response = $this->actingAs($student)
            ->postJson(route('study-groups.store'), [
                'course_id' => $course->id,
                'name' => 'Laravel Study Team',
                'description' => 'Learn together',
                'max_members' => 5
            ]);

        $response->assertStatus(403);
    }

    public function test_enrolled_student_can_create_study_group(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->createCourseWithEnrollment($student);

        $response = $this->actingAs($student)
            ->postJson(route('study-groups.store'), [
                'course_id' => $course->id,
                'name' => 'Laravel Study Team',
                'description' => 'Learn together',
                'max_members' => 5
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['success', 'message', 'data']);

        $this->assertDatabaseHas('study_groups', [
            'course_id' => $course->id,
            'creator_id' => $student->id,
            'name' => 'Laravel Study Team'
        ]);

        // Creator should automatically be added as moderator
        $studyGroup = StudyGroup::first();
        $this->assertTrue($studyGroup->hasMember($student->id));
        $this->assertEquals('moderator', $studyGroup->members->first()->pivot->role);
    }

    public function test_only_creator_or_admin_can_update_study_group(): void
    {
        $student1 = User::factory()->create(['role' => 'student']);
        $student2 = User::factory()->create(['role' => 'student']);
        $course = $this->createCourseWithEnrollment($student1);
        
        // Enroll student2 as well so they can do activities
        Enrollment::create([
            'user_id' => $student2->id,
            'course_id' => $course->id,
            'status' => Enrollment::STATUS_ACTIVE,
            'enrolled_at' => now(),
        ]);

        $studyGroup = StudyGroup::create([
            'course_id' => $course->id,
            'creator_id' => $student1->id,
            'name' => 'Original Team Name',
            'max_members' => 10,
        ]);
        $studyGroup->members()->attach($student1->id, ['role' => 'moderator']);

        // Student2 (non-creator) tries to edit
        $response = $this->actingAs($student2)
            ->putJson(route('study-groups.update', $studyGroup), [
                'name' => 'Hijacked Team Name',
                'max_members' => 10,
            ]);
        $response->assertStatus(403);

        // Creator edits group
        $response = $this->actingAs($student1)
            ->putJson(route('study-groups.update', $studyGroup), [
                'name' => 'Updated Team Name',
                'max_members' => 8,
            ]);
        $response->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('study_groups', [
            'id' => $studyGroup->id,
            'name' => 'Updated Team Name',
            'max_members' => 8,
        ]);
    }

    public function test_only_creator_or_admin_can_delete_study_group(): void
    {
        $student1 = User::factory()->create(['role' => 'student']);
        $student2 = User::factory()->create(['role' => 'student']);
        $course = $this->createCourseWithEnrollment($student1);

        $studyGroup = StudyGroup::create([
            'course_id' => $course->id,
            'creator_id' => $student1->id,
            'name' => 'Team to Delete',
            'max_members' => 10,
        ]);

        // Non-creator tries to delete
        $response = $this->actingAs($student2)
            ->deleteJson(route('study-groups.destroy', $studyGroup));
        $response->assertStatus(403);

        // Creator deletes
        $response = $this->actingAs($student1)
            ->deleteJson(route('study-groups.destroy', $studyGroup));
        $response->assertOk();

        $this->assertDatabaseMissing('study_groups', ['id' => $studyGroup->id]);
    }

    public function test_student_can_join_and_leave_study_group(): void
    {
        $student1 = User::factory()->create(['role' => 'student']);
        $student2 = User::factory()->create(['role' => 'student']);
        $course = $this->createCourseWithEnrollment($student1);

        // Enroll student 2
        Enrollment::create([
            'user_id' => $student2->id,
            'course_id' => $course->id,
            'status' => Enrollment::STATUS_ACTIVE,
            'enrolled_at' => now(),
        ]);

        $studyGroup = StudyGroup::create([
            'course_id' => $course->id,
            'creator_id' => $student1->id,
            'name' => 'Active Team',
            'max_members' => 2,
        ]);
        $studyGroup->members()->attach($student1->id, ['role' => 'moderator']);

        // Student2 joins group
        $response = $this->actingAs($student2)
            ->postJson(route('study-groups.join', $studyGroup));
        $response->assertOk()
            ->assertJsonPath('success', true);

        $this->assertTrue($studyGroup->hasMember($student2->id));

        // Student2 leaves group
        $response = $this->actingAs($student2)
            ->postJson(route('study-groups.leave', $studyGroup));
        $response->assertOk();

        $this->assertFalse($studyGroup->hasMember($student2->id));
    }

    public function test_joining_full_group_fails_max_members_validation(): void
    {
        $student1 = User::factory()->create(['role' => 'student']);
        $student2 = User::factory()->create(['role' => 'student']);
        $course = $this->createCourseWithEnrollment($student1);

        Enrollment::create([
            'user_id' => $student2->id,
            'course_id' => $course->id,
            'status' => Enrollment::STATUS_ACTIVE,
            'enrolled_at' => now(),
        ]);

        $studyGroup = StudyGroup::create([
            'course_id' => $course->id,
            'creator_id' => $student1->id,
            'name' => 'Full Team',
            'max_members' => 1, // Only 1 member allowed
        ]);
        // Add creator
        $studyGroup->members()->attach($student1->id, ['role' => 'moderator']);

        // Student2 tries to join but group is full
        $response = $this->actingAs($student2)
            ->postJson(route('study-groups.join', $studyGroup));

        $response->assertStatus(400)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Nhóm học tập đã đạt số lượng thành viên tối đa.');
    }

    public function test_creator_cannot_leave_via_leave_endpoint(): void
    {
        $student = User::factory()->create(['role' => 'student']);
        $course = $this->createCourseWithEnrollment($student);

        $studyGroup = StudyGroup::create([
            'course_id' => $course->id,
            'creator_id' => $student->id,
            'name' => 'Creator Team',
            'max_members' => 5,
        ]);
        $studyGroup->members()->attach($student->id, ['role' => 'moderator']);

        $response = $this->actingAs($student)
            ->postJson(route('study-groups.leave', $studyGroup));

        $response->assertStatus(400)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Người tạo nhóm không thể rời nhóm. Hãy xóa nhóm nếu muốn giải tán.');
    }

    public function test_can_list_members(): void
    {
        $student1 = User::factory()->create(['role' => 'student']);
        $student2 = User::factory()->create(['role' => 'student']);
        $course = $this->createCourseWithEnrollment($student1);

        $studyGroup = StudyGroup::create([
            'course_id' => $course->id,
            'creator_id' => $student1->id,
            'name' => 'List Member Team',
            'max_members' => 5,
        ]);
        $studyGroup->members()->attach($student1->id, ['role' => 'moderator']);
        $studyGroup->members()->attach($student2->id, ['role' => 'member']);

        $response = $this->actingAs($student1)
            ->getJson(route('study-groups.members', $studyGroup));

        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }
}
