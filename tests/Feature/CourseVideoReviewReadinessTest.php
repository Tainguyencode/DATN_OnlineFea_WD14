<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ContentUpdate;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\InstructorProfile;
use App\Models\Lesson;
use App\Models\User;
use App\Services\CourseSubmissionValidator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CourseVideoReviewReadinessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
    }

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
        $course->update(['thumbnail' => 'course-thumbnails/readiness.jpg']);
        $lesson->update(['duration' => 360, 'duration_seconds' => 360]);
        foreach (range(2, Course::MIN_LESSON_COUNT) as $index) {
            $extra = $this->readyVideo($course, $section, 'Ready video '.$index);
            $extra->update(['duration' => 360, 'duration_seconds' => 360]);
        }

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

    public function test_published_course_draft_create_lessons_use_completed_content_update_hls(): void
    {
        [$course, $section] = $this->publishedCourseWithSection();

        foreach (range(1, 4) as $index) {
            $lesson = $this->video($course, $section, 'Draft video '.$index);
            $this->lessonUpdate($course, $lesson, ContentUpdate::ACTION_CREATE, [
                'section_id' => $section->id,
                'title' => $lesson->title,
                'type' => Lesson::TYPE_VIDEO,
                'original_video_key' => "originals/drafts/{$index}.mp4",
                'upload_status' => 'uploaded',
                'processing_status' => 'completed',
                'hls_manifest_key' => "hls/updates/{$index}/master.m3u8",
            ]);
        }

        $this->assertSame([], $course->fresh()->videoReadinessBlockers());
    }

    public function test_one_processing_draft_create_video_blocks_published_course(): void
    {
        [$course, $section] = $this->publishedCourseWithSection();

        foreach (range(1, 4) as $index) {
            $lesson = $this->video($course, $section, 'Draft processing '.$index);
            $this->lessonUpdate($course, $lesson, ContentUpdate::ACTION_CREATE, [
                'section_id' => $section->id,
                'title' => $lesson->title,
                'type' => Lesson::TYPE_VIDEO,
                'original_video_key' => "originals/processing-drafts/{$index}.mp4",
                'upload_status' => 'uploaded',
                'processing_status' => $index === 4 ? 'processing' : 'completed',
                'hls_manifest_key' => $index === 4 ? null : "hls/processing-drafts/{$index}/master.m3u8",
            ]);
        }

        $blockers = $course->fresh()->videoReadinessBlockers();
        $this->assertCount(1, $blockers);
        $this->assertSame('processing', $blockers[0]['state']);
    }

    public function test_completed_replacement_video_uses_content_update_even_when_lesson_manifest_is_null(): void
    {
        [$course, $section] = $this->publishedCourseWithSection();
        $lesson = $this->video($course, $section, 'Published lesson without manifest', [
            'original_video_key' => 'originals/video-a.mp4',
            'upload_status' => 'uploaded',
            'processing_status' => 'completed',
            'status' => Lesson::STATUS_PUBLISHED,
        ]);
        $this->lessonUpdate($course, $lesson, ContentUpdate::ACTION_UPDATE, [
            'section_id' => $section->id,
            'title' => 'Replacement video B',
            'type' => Lesson::TYPE_VIDEO,
            'original_video_key' => 'originals/video-b.mp4',
            'upload_status' => 'uploaded',
            'processing_status' => 'completed',
            'hls_manifest_key' => 'hls/video-b/master.m3u8',
        ]);

        $this->assertNull($lesson->fresh()->hls_manifest_key);
        $this->assertSame([], $course->fresh()->videoReadinessBlockers());
    }

    public function test_old_ready_hls_does_not_hide_processing_or_failed_replacement(): void
    {
        [$course, $section] = $this->publishedCourseWithSection();
        $lesson = $this->readyVideo($course, $section, 'Published video A');
        $update = $this->lessonUpdate($course, $lesson, ContentUpdate::ACTION_UPDATE, [
            'section_id' => $section->id,
            'title' => 'Replacement video B',
            'type' => Lesson::TYPE_VIDEO,
            'original_video_key' => 'originals/replacement-b.mp4',
            'upload_status' => 'uploaded',
            'processing_status' => 'processing',
            'hls_manifest_key' => null,
        ]);

        $this->assertSame('processing', $course->fresh()->videoReadinessBlockers()[0]['state']);

        $update->update(['payload' => [
            ...$update->payload,
            'processing_status' => 'failed',
        ]]);

        $this->assertSame('failed', $course->fresh()->videoReadinessBlockers()[0]['state']);
    }

    public function test_completed_flag_without_current_draft_manifest_still_blocks_submission(): void
    {
        [$course, $section] = $this->publishedCourseWithSection();
        $lesson = $this->readyVideo($course, $section, 'Published source video');
        $this->lessonUpdate($course, $lesson, ContentUpdate::ACTION_UPDATE, [
            'section_id' => $section->id,
            'title' => 'Replacement without manifest',
            'type' => Lesson::TYPE_VIDEO,
            'original_video_key' => 'originals/replacement-no-manifest.mp4',
            'upload_status' => 'uploaded',
            'processing_status' => 'completed',
            'hls_manifest_key' => null,
        ]);

        $this->assertSame('processing', $course->fresh()->videoReadinessBlockers()[0]['state']);
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
        $profile = InstructorProfile::create(['user_id' => $instructor->id]);
        $profile->teachingCategories()->attach($category->id, ['is_primary' => true]);
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

    /** @return array{0: Course, 1: CourseSection} */
    private function publishedCourseWithSection(): array
    {
        [$course, $section] = $this->courseWithSection();
        $course->update([
            'status' => Course::STATUS_PUBLISHED,
            'is_published' => true,
            'published_at' => now(),
        ]);

        return [$course->fresh(), $section];
    }

    /** @param array<string, mixed> $payload */
    private function lessonUpdate(Course $course, Lesson $lesson, string $action, array $payload): ContentUpdate
    {
        return ContentUpdate::create([
            'course_id' => $course->id,
            'type' => ContentUpdate::TYPE_LESSON,
            'action' => $action,
            'entity_id' => $lesson->id,
            'status' => ContentUpdate::STATUS_DRAFT,
            'created_by' => $course->instructor_id,
            'payload' => $payload,
        ]);
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
