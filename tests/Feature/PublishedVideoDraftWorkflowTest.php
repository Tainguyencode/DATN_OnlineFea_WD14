<?php

namespace Tests\Feature;

use App\Jobs\ConvertContentUpdateVideoToHLS;
use App\Models\Category;
use App\Models\ContentUpdate;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\LessonVersion;
use App\Models\User;
use App\Services\AwsS3UploadService;
use App\Services\ContentUpdateService;
use App\Services\ContentVersionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Mockery\MockInterface;
use Tests\TestCase;

class PublishedVideoDraftWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_duplicate_lesson_drafts_are_consolidated_and_draft_candidate_is_cleanly_deleted(): void
    {
        [$instructor, , $course, $lesson] = $this->publishedVideoCourse();
        $updates = app(ContentUpdateService::class);
        $updates->recordPendingUpdate(
            ContentUpdate::TYPE_LESSON,
            ContentUpdate::ACTION_UPDATE,
            $course->id,
            $lesson->id,
            ['title' => 'Older draft'],
            $instructor
        );
        $newest = $updates->recordPendingUpdate(
            ContentUpdate::TYPE_LESSON,
            ContentUpdate::ACTION_UPDATE,
            $course->id,
            $lesson->id,
            ['title' => 'Newest draft'],
            $instructor
        );

        $canonical = $updates->ensureLessonUpdateDraft($course, $lesson, $instructor);

        $this->assertSame($newest->id, $canonical->id);
        $this->assertSame('Newest draft', $canonical->payload['title']);
        $this->assertSame(1, ContentUpdate::query()
            ->where('course_id', $course->id)
            ->where('entity_id', $lesson->id)
            ->where('status', ContentUpdate::STATUS_DRAFT)
            ->count());
        $candidate = LessonVersion::query()->where('content_update_id', $canonical->id)->firstOrFail();
        $this->assertSame(2, $candidate->version_number);

        $updates->deleteDraft($canonical);

        $this->assertDatabaseMissing('content_updates', ['id' => $canonical->id]);
        $this->assertDatabaseMissing('lesson_versions', ['id' => $candidate->id]);
        $this->assertNull($lesson->fresh()->draft_version_id);
    }

    public function test_hls_job_refuses_to_mutate_a_submitted_candidate(): void
    {
        [$instructor, , $course, $lesson] = $this->publishedVideoCourse();
        $updates = app(ContentUpdateService::class);
        $versions = app(ContentVersionService::class);
        $draft = $updates->ensureLessonUpdateDraft($course, $lesson, $instructor);
        $draft = $updates->updateDraft($draft, [
            'original_video_key' => 'originals/submitted/video.mp4',
            'processing_status' => 'pending',
        ]);
        $candidate = $versions->prepareDraftCandidate($draft, $instructor);
        $draft->update(['status' => ContentUpdate::STATUS_PENDING, 'submitted_at' => now()]);
        $payloadBefore = $draft->fresh()->payload;
        $candidateBefore = $candidate->fresh()->getAttributes();

        try {
            (new ConvertContentUpdateVideoToHLS($draft->fresh()))->handle($updates, $versions);
            $this->fail('A submitted video candidate must not be processed again.');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('no longer an editable draft', $exception->getMessage());
        }

        $this->assertSame($payloadBefore, $draft->fresh()->payload);
        $this->assertSame($candidateBefore, $candidate->fresh()->getAttributes());
    }

    public function test_published_video_upload_reuses_v3_then_approval_allocates_v4_without_touching_live_media(): void
    {
        Queue::fake();
        Storage::fake('s3');
        [$instructor, $admin, $course, $lesson] = $this->publishedVideoCourse();
        $updates = app(ContentUpdateService::class);
        $versions = app(ContentVersionService::class);

        $v2Update = $updates->ensureLessonUpdateDraft($course, $lesson, $instructor);
        $v2Update = $updates->updateDraft($v2Update, [
            'title' => 'Published video V2',
            'type' => Lesson::TYPE_VIDEO,
            'original_video_key' => 'originals/history/v2.mp4',
            'hls_manifest_key' => 'hls/history/v2/master.m3u8',
            'video_path' => 'lesson-hls/history/v2/playlist.m3u8',
            'duration_seconds' => 222,
        ]);
        $versions->prepareDraftCandidate($v2Update, $instructor);
        $v2Update->update(['status' => ContentUpdate::STATUS_PENDING, 'submitted_at' => now()]);
        $updates->applyApprovedUpdate($v2Update, $admin);

        $lesson->refresh();
        $v2 = LessonVersion::query()->findOrFail($lesson->published_version_id);
        $this->assertSame(2, $v2->version_number);
        $this->assertSame('originals/history/v2.mp4', $lesson->original_video_key);

        $this->mock(AwsS3UploadService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('generateDraftVideoObjectKey')
                ->times(3)
                ->andReturnUsing(fn ($courseId, $lessonId, $updateId, $version, $filename): string => "originals/courses/{$courseId}/lessons/{$lessonId}/content-updates/{$updateId}/versions/v{$version}/{$filename}");
            $mock->shouldReceive('createMultipartUpload')->times(3)->andReturn('upload-a', 'upload-b', 'upload-c');
            $mock->shouldReceive('getBucket')->times(3)->andReturn('test-bucket');
            $mock->shouldReceive('completeMultipartUpload')->once()->andReturn(['location' => 'https://example.test/video-v3.mp4']);
        });

        $this->actingAs($instructor)->withSession(['two_factor_passed_at' => now()->timestamp]);

        $first = $this->postJson(route('instructor.courses.s3.multipart.create', $course), [
            'filename' => 'video-v3-a.mp4',
            'content_type' => 'video/mp4',
            'file_size' => 10_000,
            'lesson_id' => $lesson->id,
        ])->assertOk()->assertJsonPath('versionNumber', 3);

        $second = $this->postJson(route('instructor.courses.s3.multipart.create', $course), [
            'filename' => 'video-v3-b.mp4',
            'content_type' => 'video/mp4',
            'file_size' => 12_000,
            'lesson_id' => $lesson->id,
        ])->assertOk()->assertJsonPath('versionNumber', 3);

        $this->assertSame($first->json('contentUpdateId'), $second->json('contentUpdateId'));
        $this->assertStringContainsString(
            "/content-updates/{$second->json('contentUpdateId')}/versions/v3/",
            $second->json('key')
        );
        $this->assertSame(1, ContentUpdate::query()
            ->where('course_id', $course->id)
            ->where('entity_id', $lesson->id)
            ->where('status', ContentUpdate::STATUS_DRAFT)
            ->count());
        $this->assertSame(1, LessonVersion::query()
            ->where('lesson_id', $lesson->id)
            ->where('status', LessonVersion::STATUS_DRAFT)
            ->count());
        $this->assertSame('originals/history/v2.mp4', $lesson->fresh()->original_video_key);

        $this->get(route('instructor.courses.curriculum', $course))
            ->assertOk()
            ->assertSee('Đang chỉnh sửa bản nháp V3')
            ->assertSee('Video đang xuất bản (V2)');

        $this->postJson(route('instructor.courses.s3.multipart.complete', $course), [
            'key' => $second->json('key'),
            'uploadId' => 'upload-b',
            'parts' => [['PartNumber' => 1, 'ETag' => 'etag-v3']],
            'duration' => 333,
            'lesson_id' => $lesson->id,
            'content_update_id' => $second->json('contentUpdateId'),
        ])->assertOk()->assertJsonPath('versionNumber', 3);

        Queue::assertPushed(ConvertContentUpdateVideoToHLS::class, fn (ConvertContentUpdateVideoToHLS $job): bool => (int) $job->contentUpdate->id === (int) $second->json('contentUpdateId'));
        Storage::disk('s3')->put($second->json('key'), 'video-v3');

        $this->putJson(route('instructor.courses.lessons.update', [$course, $lesson]), [
            'title' => 'Video V3 draft',
            'type' => Lesson::TYPE_VIDEO,
            's3_key' => $second->json('key'),
            'video_original_name' => 'video-v3-b.mp4',
            'video_mime' => 'video/mp4',
            'video_size' => 12_000,
            'duration' => 333,
            'status' => Lesson::STATUS_PUBLISHED,
        ])->assertOk()
            ->assertJsonPath('content_update_id', $second->json('contentUpdateId'))
            ->assertJsonPath('version_number', 3);

        $this->assertSame(1, ContentUpdate::query()
            ->where('course_id', $course->id)
            ->where('entity_id', $lesson->id)
            ->where('status', ContentUpdate::STATUS_DRAFT)
            ->count());

        $v3Update = ContentUpdate::query()->findOrFail($second->json('contentUpdateId'));
        $v3CandidateId = LessonVersion::query()->where('content_update_id', $v3Update->id)->value('id');
        $v3Update = $updates->updateDraft($v3Update, [
            'upload_status' => 'uploaded',
            'processing_status' => 'completed',
            'video_path' => "lesson-hls/content-updates/{$v3Update->id}/lesson-versions/{$v3CandidateId}/playlist.m3u8",
            'hls_manifest_key' => "hls/content-updates/{$v3Update->id}/versions/v3/master.m3u8",
        ]);
        $v3 = $versions->prepareDraftCandidate($v3Update, $instructor);
        $v3Update->update(['status' => ContentUpdate::STATUS_PENDING, 'submitted_at' => now()]);

        $frozenV3Media = $v3->fresh()->only(['title', 'original_video_key', 'video_path', 'hls_manifest_key']);
        try {
            $versions->prepareDraftCandidate($v3Update->fresh(), $instructor);
            $this->fail('A pending candidate must not be mutable.');
        } catch (ValidationException) {
            $this->assertSame($frozenV3Media, $v3->fresh()->only(['title', 'original_video_key', 'video_path', 'hls_manifest_key']));
        }
        $updates->applyApprovedUpdate($v3Update, $admin);

        $lesson->refresh();
        $this->assertSame($v3->id, $lesson->published_version_id);
        $this->assertSame($second->json('key'), $lesson->original_video_key);
        $this->assertSame('originals/history/v2.mp4', $v2->fresh()->original_video_key);
        $this->assertSame($frozenV3Media, $v3->fresh()->only(['title', 'original_video_key', 'video_path', 'hls_manifest_key']));

        $third = $this->postJson(route('instructor.courses.s3.multipart.create', $course->fresh()), [
            'filename' => 'video-v4.mp4',
            'content_type' => 'video/mp4',
            'file_size' => 14_000,
            'lesson_id' => $lesson->id,
        ])->assertOk()->assertJsonPath('versionNumber', 4);

        $this->assertNotSame($second->json('contentUpdateId'), $third->json('contentUpdateId'));
        $this->assertSame(1, ContentUpdate::query()
            ->where('course_id', $course->id)
            ->where('entity_id', $lesson->id)
            ->where('status', ContentUpdate::STATUS_DRAFT)
            ->count());
        $this->assertSame(1, LessonVersion::query()
            ->where('lesson_id', $lesson->id)
            ->where('status', LessonVersion::STATUS_DRAFT)
            ->count());

        $v4Update = ContentUpdate::query()->findOrFail($third->json('contentUpdateId'));
        $v4Update->update(['status' => ContentUpdate::STATUS_PENDING, 'submitted_at' => now()]);
        $updates->rejectUpdate($v4Update, $admin, 'Video V4 cần chỉnh sửa lại.');

        $this->assertSame($v3->id, $lesson->fresh()->published_version_id);
        $this->assertSame($second->json('key'), $lesson->fresh()->original_video_key);
        $v4 = LessonVersion::query()->where('content_update_id', $v4Update->id)->firstOrFail();
        $this->assertSame(4, $v4->version_number);
        $this->assertSame(LessonVersion::STATUS_REJECTED, $v4->status);
    }

    /** @return array{User, User, Course, Lesson} */
    private function publishedVideoCourse(): array
    {
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'approved',
            'email_verified_at' => now(),
        ]);
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $category = Category::create(['name' => 'Video versions', 'slug' => 'video-versions-'.uniqid()]);
        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Published video course',
            'slug' => 'published-video-course-'.uniqid(),
            'short_description' => 'Published course',
            'description' => 'Published course description',
            'objectives' => 'Learn versioning',
            'target_audience' => 'Developers',
            'requirements' => 'None',
            'price' => 100_000,
            'language' => 'vi',
            'level' => 'beginner',
            'status' => Course::STATUS_PUBLISHED,
            'is_published' => true,
            'published_at' => now(),
        ]);
        $section = CourseSection::create([
            'course_id' => $course->id,
            'title' => 'Section 1',
            'sort_order' => 1,
        ]);
        $lesson = Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Published video V1',
            'type' => Lesson::TYPE_VIDEO,
            'original_video_key' => 'originals/history/v1.mp4',
            'hls_manifest_key' => 'hls/history/v1/master.m3u8',
            'video_path' => 'lesson-hls/history/v1/playlist.m3u8',
            'upload_status' => 'uploaded',
            'processing_status' => 'completed',
            'duration' => 111,
            'duration_seconds' => 111,
            'sort_order' => 1,
            'status' => Lesson::STATUS_PUBLISHED,
        ]);

        app(ContentVersionService::class)->publishInitialCourseTree($course, $admin);

        return [$instructor, $admin, $course->fresh(), $lesson->fresh()];
    }
}
