<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\User;
use App\Services\CourseSubmissionValidator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseVideoDurationReadinessTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_video_lesson_durations_do_not_count_toward_video_readiness(): void
    {
        [$course, $section] = $this->courseWithSection();

        foreach ([Lesson::TYPE_DOCUMENT, Lesson::TYPE_QUIZ, Lesson::TYPE_ASSIGNMENT] as $index => $type) {
            Lesson::create([
                'course_id' => $course->id,
                'section_id' => $section->id,
                'title' => 'Non-video lesson '.$index,
                'type' => $type,
                'duration' => 9999,
                'duration_seconds' => 9999,
                'sort_order' => $index,
                'status' => Lesson::STATUS_DRAFT,
            ]);
        }

        $this->assertSame(0, $course->totalVideoDurationSeconds());
        $this->assertFalse($this->videoDurationItem($course)['passed']);
    }

    public function test_video_duration_without_a_real_source_does_not_count_toward_readiness(): void
    {
        [$course, $section] = $this->courseWithSection();

        Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Source-less video',
            'type' => Lesson::TYPE_VIDEO,
            'duration' => Course::MIN_VIDEO_DURATION_MINUTES * 60,
            'duration_seconds' => Course::MIN_VIDEO_DURATION_MINUTES * 60,
            'sort_order' => 0,
            'status' => Lesson::STATUS_DRAFT,
        ]);

        $this->assertSame(0, $course->totalVideoDurationSeconds());
        $this->assertFalse($this->videoDurationItem($course)['passed']);
    }

    public function test_video_duration_with_a_real_source_counts_toward_readiness(): void
    {
        [$course, $section] = $this->courseWithSection();
        $duration = Course::MIN_VIDEO_DURATION_MINUTES * 60;

        Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Sourced video',
            'type' => Lesson::TYPE_VIDEO,
            'video_url' => 'https://example.com/lesson.mp4',
            'duration' => $duration,
            'duration_seconds' => $duration,
            'sort_order' => 0,
            'status' => Lesson::STATUS_DRAFT,
        ]);

        $this->assertSame($duration, $course->totalVideoDurationSeconds());
        $this->assertTrue($this->videoDurationItem($course)['passed']);
    }

    /**
     * @return array{key: string, label: string, passed: bool, message: string|null}
     */
    private function videoDurationItem(Course $course): array
    {
        return collect($course->submissionCheck()->items())
            ->firstWhere('key', CourseSubmissionValidator::KEY_VIDEO_DURATION);
    }

    /**
     * @return array{0: Course, 1: CourseSection}
     */
    private function courseWithSection(): array
    {
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'approved',
            'is_active' => true,
        ]);
        $category = Category::create([
            'name' => 'Readiness '.uniqid(),
            'slug' => 'readiness-'.uniqid(),
        ]);
        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Video readiness '.uniqid(),
            'slug' => 'video-readiness-'.uniqid(),
            'short_description' => 'Short description',
            'description' => 'Detailed description',
            'objectives' => 'Learning objectives',
            'target_audience' => 'Students',
            'requirements' => 'None',
            'price' => 100000,
            'language' => 'vi',
            'level' => 'beginner',
            'status' => Course::STATUS_DRAFT,
            'is_published' => false,
        ]);
        $section = CourseSection::create([
            'course_id' => $course->id,
            'title' => 'Section 1',
            'sort_order' => 0,
        ]);

        return [$course, $section];
    }
}
