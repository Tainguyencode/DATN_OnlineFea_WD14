<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\InstructorProfile;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseSubmissionUxTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_ready_videos_with_a_missing_thumbnail_return_the_actionable_validator_error(): void
    {
        $course = $this->readyCourse(['thumbnail' => null]);

        $response = $this->actingAs($course->instructor)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->post(route('instructor.courses.submit', $course), ['copyright_agreed' => 1]);

        $response->assertRedirect()
            ->assertSessionHas('error', 'Chưa thể gửi duyệt: Thiếu thumbnail.')
            ->assertSessionHasErrors(['submission' => 'Thiếu thumbnail']);
        $this->assertSame(Course::STATUS_DRAFT, $course->fresh()->status);
    }

    public function test_a_preview_lesson_is_not_a_submission_requirement(): void
    {
        $course = $this->readyCourse();

        $this->assertSame(0, $course->lessons()->where('is_preview', true)->count());
        $this->assertTrue($course->submissionCheck()->passes());
    }

    public function test_multiple_missing_requirements_are_all_returned_by_the_validator(): void
    {
        $course = $this->readyCourse([
            'thumbnail' => null,
            'description' => null,
            'objectives' => null,
        ]);

        $this->assertSame([
            'Thiếu thumbnail',
            'Thiếu mô tả chi tiết',
            'Thiếu mục tiêu khóa học',
        ], $course->submissionCheck()->errorMessages());
    }

    public function test_all_submission_requirements_satisfied_allows_review_submission(): void
    {
        $course = $this->readyCourse();

        $response = $this->actingAs($course->instructor)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->post(route('instructor.courses.submit', $course), ['copyright_agreed' => 1]);

        $response->assertRedirect(route('instructor.courses.index'));
        $this->assertSame(Course::STATUS_PENDING, $course->fresh()->status);
    }

    /** @param array<string, mixed> $overrides */
    private function readyCourse(array $overrides = []): Course
    {
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'approved',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $parent = Category::create([
            'name' => 'Parent '.uniqid(),
            'slug' => 'parent-'.uniqid(),
            'status' => true,
        ]);
        $category = Category::create([
            'parent_id' => $parent->id,
            'name' => 'Child '.uniqid(),
            'slug' => 'child-'.uniqid(),
            'status' => true,
        ]);
        $profile = InstructorProfile::create(['user_id' => $instructor->id]);
        $profile->teachingCategories()->attach($category->id, ['is_primary' => true]);
        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Submission ready '.uniqid(),
            'slug' => 'submission-ready-'.uniqid(),
            'short_description' => 'Short description',
            'description' => 'Detailed description',
            'objectives' => 'Learning objectives',
            'thumbnail' => 'course-thumbnails/ready.jpg',
            'price' => 100000,
            'language' => 'vi',
            'level' => 'beginner',
            'status' => Course::STATUS_DRAFT,
            'is_published' => false,
            ...$overrides,
        ]);
        $section = CourseSection::create([
            'course_id' => $course->id,
            'title' => 'Ready section',
            'sort_order' => 0,
        ]);

        foreach (range(1, Course::MIN_LESSON_COUNT) as $index) {
            Lesson::create([
                'course_id' => $course->id,
                'section_id' => $section->id,
                'title' => 'Ready video '.$index,
                'type' => Lesson::TYPE_VIDEO,
                'original_video_key' => "originals/ready-{$index}.mp4",
                'hls_manifest_key' => "hls/ready-{$index}/master.m3u8",
                'video_path' => "lesson-hls/ready-{$index}/playlist.m3u8",
                'upload_status' => 'uploaded',
                'processing_status' => 'completed',
                'duration' => 360,
                'duration_seconds' => 360,
                'sort_order' => $index - 1,
                'status' => Lesson::STATUS_PUBLISHED,
            ]);
        }

        return $course;
    }
}
