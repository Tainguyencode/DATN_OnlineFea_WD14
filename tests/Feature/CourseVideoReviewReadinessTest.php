<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ContentUpdate;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\User;
use App\Services\CourseSubmissionValidator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseVideoReviewReadinessTest extends TestCase
{
    use RefreshDatabase;

    public function test_one_processing_video_blocks_hls_review_readiness(): void
    {
        [$course, $section] = $this->courseWithSection();
        $this->video($course, $section, 'Processing video', [
            'original_video_key' => 'originals/processing.mp4',
            'upload_status' => 'uploaded',
            'processing_status' => 'processing',
        ]);

        $this->assertTrue($course->hasIncompleteHlsVideos());
        $this->assertSame('processing', $course->videoReadinessBlockers()[0]['state']);
    }

    public function test_all_hls_ready_videos_do_not_have_a_processing_blocker(): void
    {
        [$course, $section] = $this->courseWithSection();
        $this->readyVideo($course, $section, 'Ready video one');
        $this->readyVideo($course, $section, 'Ready video two');

        $this->assertFalse($course->hasIncompleteHlsVideos());
        $this->assertSame([], $course->videoReadinessBlockers());
    }

    public function test_ready_videos_in_multiple_sections_pass_the_course_level_hls_check(): void
    {
        [$course, $firstSection] = $this->courseWithSection();
        $secondSection = CourseSection::create([
            'course_id' => $course->id,
            'title' => 'Second section',
            'sort_order' => 1,
        ]);

        $this->readyVideo($course, $firstSection, 'First section video');
        $this->readyVideo($course, $secondSection, 'Second section video');

        $this->assertFalse($course->hasIncompleteHlsVideos());
        $this->assertSame([], $course->videoReadinessBlockers());
    }

    public function test_terminal_stale_content_update_does_not_override_a_current_ready_video(): void
    {
        [$course, $section] = $this->courseWithSection();
        $lesson = $this->readyVideo($course, $section, 'Current ready video');

        ContentUpdate::create([
            'course_id' => $course->id,
            'type' => ContentUpdate::TYPE_LESSON,
            'action' => ContentUpdate::ACTION_UPDATE,
            'entity_id' => $lesson->id,
            'status' => ContentUpdate::STATUS_REJECTED,
            'created_by' => $course->instructor_id,
            'payload' => [
                'type' => Lesson::TYPE_VIDEO,
                'title' => 'Superseded processing upload',
                'original_video_key' => 'originals/stale.mp4',
                'upload_status' => 'uploaded',
                'processing_status' => 'processing',
            ],
        ]);

        $this->assertFalse($course->hasIncompleteHlsVideos());
        $this->assertSame([], $course->videoReadinessBlockers());

        $this->actingAs($course->instructor)
            ->getJson(route('instructor.courses.hls-status', $course))
            ->assertOk()
            ->assertJsonPath('common_state', 'completed')
            ->assertJsonPath('can_submit', true);
    }

    public function test_video_shell_without_source_remains_a_submission_blocker(): void
    {
        [$course, $section] = $this->courseWithSection();
        $this->video($course, $section, 'Imported video shell');

        $this->assertFalse($course->hasIncompleteHlsVideos());
        $this->assertSame('missing_source', $course->videoReadinessBlockers()[0]['state']);

        $sourceCheck = collect($course->submissionCheck()->items())
            ->firstWhere('key', CourseSubmissionValidator::KEY_VIDEO_SOURCE);
        $this->assertFalse($sourceCheck['passed']);
    }

    public function test_manual_and_imported_video_lessons_use_the_same_ready_state(): void
    {
        [$course, $section] = $this->courseWithSection();
        $this->readyVideo($course, $section, 'Manual video');
        $importedShell = $this->video($course, $section, 'Imported video shell');

        $this->assertSame('missing_source', $course->videoReadinessBlockers()[0]['state']);

        $importedShell->update([
            'original_video_key' => 'originals/imported.mp4',
            'hls_manifest_key' => 'hls/imported/master.m3u8',
            'video_path' => 'lesson-hls/'.$importedShell->id.'/playlist.m3u8',
            'upload_status' => 'uploaded',
            'processing_status' => 'completed',
        ]);

        $this->assertFalse($course->hasIncompleteHlsVideos());
        $this->assertSame([], $course->videoReadinessBlockers());
    }

    /** @return array{0: Course, 1: CourseSection} */
    private function courseWithSection(): array
    {
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'approved',
            'email_verified_at' => now(),
        ]);
        $category = Category::create([
            'name' => 'Readiness '.uniqid(),
            'slug' => 'readiness-'.uniqid(),
            'status' => true,
        ]);
        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Video readiness '.uniqid(),
            'slug' => 'video-readiness-'.uniqid(),
            'short_description' => 'Short description',
            'description' => 'Detailed description',
            'objectives' => 'Learning objectives',
            'price' => 100000,
            'language' => 'vi',
            'status' => Course::STATUS_DRAFT,
            'is_published' => false,
        ]);
        $section = CourseSection::create([
            'course_id' => $course->id,
            'title' => 'First section',
            'sort_order' => 0,
        ]);

        return [$course, $section];
    }

    private function readyVideo(Course $course, CourseSection $section, string $title): Lesson
    {
        return $this->video($course, $section, $title, [
            'original_video_key' => 'originals/'.str($title)->slug().'.mp4',
            'hls_manifest_key' => 'hls/'.str($title)->slug().'/master.m3u8',
            'video_path' => 'lesson-hls/'.str($title)->slug().'/playlist.m3u8',
            'upload_status' => 'uploaded',
            'processing_status' => 'completed',
        ]);
    }

    /** @param array<string, mixed> $overrides */
    private function video(Course $course, CourseSection $section, string $title, array $overrides = []): Lesson
    {
        return Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => $title,
            'type' => Lesson::TYPE_VIDEO,
            'duration' => 60,
            'duration_seconds' => 60,
            'sort_order' => Lesson::where('course_id', $course->id)->count(),
            'status' => Lesson::STATUS_DRAFT,
            ...$overrides,
        ]);
    }
}
