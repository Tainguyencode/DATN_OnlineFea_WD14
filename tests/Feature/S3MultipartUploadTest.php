<?php

namespace Tests\Feature;

use App\Jobs\ConvertVideoToHLS;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\User;
use App\Services\AwsS3UploadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;
use Tests\TestCase;

class S3MultipartUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_s3_multipart_routes(): void
    {
        $instructor = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'approved']);
        [$course] = $this->courseWithSection($instructor);

        $this->postJson(route('instructor.courses.s3.multipart.create', $course), [
            'filename' => 'video.mp4',
        ])->assertUnauthorized();
    }

    public function test_instructor_cannot_create_multipart_for_another_instructors_course(): void
    {
        $owner = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'approved', 'email_verified_at' => now()]);
        $other = $this->signInInstructor();
        [$course] = $this->courseWithSection($owner);

        $this->postJson(route('instructor.courses.s3.multipart.create', $course), [
            'filename' => 'video.mp4',
        ])->assertForbidden();
    }

    public function test_instructor_can_initialize_s3_multipart_upload(): void
    {
        $instructor = $this->signInInstructor();
        [$course] = $this->courseWithSection($instructor);

        $this->mock(AwsS3UploadService::class, function (MockInterface $mock) use ($course) {
            $mock->shouldReceive('generateVideoObjectKey')
                ->once()
                ->andReturn("originals/courses/{$course->id}/lessons/new/test-uuid.mp4");
            $mock->shouldReceive('createMultipartUpload')
                ->once()
                ->andReturn('test-upload-id-12345');
            $mock->shouldReceive('getBucket')
                ->once()
                ->andReturn('test-bucket');
        });

        $response = $this->postJson(route('instructor.courses.s3.multipart.create', $course), [
            'filename' => 'lecture_1.mp4',
            'content_type' => 'video/mp4',
            'file_size' => 150000000, // 150MB
        ]);

        $response->assertOk()
            ->assertJson([
                'uploadId' => 'test-upload-id-12345',
                'key' => "originals/courses/{$course->id}/lessons/new/test-uuid.mp4",
                'bucket' => 'test-bucket',
            ]);
    }

    public function test_instructor_can_sign_s3_parts(): void
    {
        $instructor = $this->signInInstructor();
        [$course] = $this->courseWithSection($instructor);
        $key = "originals/courses/{$course->id}/lessons/1/uuid.mp4";

        $this->mock(AwsS3UploadService::class, function (MockInterface $mock) use ($key) {
            $mock->shouldReceive('createPresignedPartUrl')
                ->with($key, 'upload-123', 1)
                ->once()
                ->andReturn('https://s3.amazonaws.com/test-bucket/part1-signed-url');
        });

        $response = $this->postJson(route('instructor.courses.s3.multipart.sign-part', $course), [
            'key' => $key,
            'uploadId' => 'upload-123',
            'partNumber' => 1,
        ]);

        $response->assertOk()
            ->assertJson([
                'url' => 'https://s3.amazonaws.com/test-bucket/part1-signed-url',
            ]);
    }

    public function test_store_lesson_with_s3_key_does_not_dispatch_hls_while_uploading(): void
    {
        Queue::fake();

        $instructor = $this->signInInstructor();
        [$course, $section] = $this->courseWithSection($instructor);
        $s3Key = "originals/courses/{$course->id}/lessons/new/test-video.mp4";

        $response = $this->post(route('instructor.courses.sections.lessons.store', [$course, $section]), [
            'title' => 'Bài học tải lên từ S3',
            'type' => 'video',
            's3_key' => $s3Key,
            'video_original_name' => 'bai_giang_1.mp4',
            'video_mime' => 'video/mp4',
            'video_size' => 143654912, // ~137MB
            'duration' => 1200,
            'sort_order' => 1,
            'status' => 'draft',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('lessons', [
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Bài học tải lên từ S3',
            'type' => 'video',
            'original_video_key' => $s3Key,
            'video_original_name' => 'bai_giang_1.mp4',
            'video_size' => 143654912,
            'upload_status' => 'pending',
            'processing_status' => 'pending',
            'duration_seconds' => 1200,
        ]);

        // Tuyệt đối không dispatch HLS khi upload chưa complete
        Queue::assertNotPushed(ConvertVideoToHLS::class);
    }

    public function test_s3_multipart_complete_dispatches_hls_job_for_lesson(): void
    {
        Queue::fake();

        $instructor = $this->signInInstructor();
        [$course, $section] = $this->courseWithSection($instructor);
        $s3Key = "originals/courses/{$course->id}/lessons/1/video.mp4";

        $lesson = Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Bài 1',
            'type' => 'video',
            'original_video_key' => $s3Key,
            'upload_status' => 'pending',
            'processing_status' => 'pending',
        ]);

        $this->mock(AwsS3UploadService::class, function (MockInterface $mock) use ($s3Key) {
            $mock->shouldReceive('completeMultipartUpload')
                ->once()
                ->andReturn([
                    'location' => "https://s3.amazonaws.com/test-bucket/{$s3Key}",
                    'key' => $s3Key,
                ]);
        });

        $response = $this->postJson(route('instructor.courses.s3.multipart.complete', $course), [
            'key' => $s3Key,
            'uploadId' => 'upl-123',
            'parts' => [
                ['PartNumber' => 1, 'ETag' => '"etag-1"'],
            ],
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('lessons', [
            'id' => $lesson->id,
            'upload_status' => 'uploaded',
        ]);

        Queue::assertPushed(ConvertVideoToHLS::class, function ($job) use ($lesson) {
            return $job->lesson->id === $lesson->id;
        });
    }

    public function test_legacy_video_player_falls_back_to_local_disk_when_s3_key_is_null(): void
    {
        Storage::fake('local');
        Storage::fake('s3');
        $instructor = $this->signInInstructor();
        [$course, $section] = $this->courseWithSection($instructor);

        $lesson = Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Bài học cũ',
            'type' => 'video',
            'video_path' => 'lesson-hls/100/playlist.m3u8',
            'is_preview' => true,
        ]);

        Storage::disk('local')->put('lesson-hls/'.$lesson->id.'/playlist.m3u8', "#EXTM3U\n#EXTINF:10.0,\nsegment0.ts\n");

        $tokenResponse = $this->getJson("/api/video/{$lesson->id}/token");
        $tokenResponse->assertOk();
        $token = $tokenResponse->json('token');

        $playlistResponse = $this->get("/api/video/hls/{$lesson->id}/playlist.m3u8?token={$token}");
        $playlistResponse->assertOk()
            ->assertSee('segment0.ts?token='.$token);
    }

    public function test_instructor_can_fetch_hls_status_for_course_lessons(): void
    {
        $instructor = $this->signInInstructor();
        [$course, $section] = $this->courseWithSection($instructor);

        $lessonProcessing = Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Bài đang xử lý',
            'type' => 'video',
            'original_video_key' => "originals/courses/{$course->id}/lessons/1/video1.mp4",
            'upload_status' => 'uploaded',
            'processing_status' => 'processing',
            'sort_order' => 1,
            'status' => 'draft',
        ]);

        $lessonCompleted = Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Bài đã hoàn tất',
            'type' => 'video',
            'original_video_key' => "originals/courses/{$course->id}/lessons/2/video2.mp4",
            'hls_manifest_key' => 'hls/lessons/2/master.m3u8',
            'upload_status' => 'uploaded',
            'processing_status' => 'completed',
            'sort_order' => 2,
            'status' => 'draft',
        ]);

        $response = $this->getJson(route('instructor.courses.hls-status', $course));

        $response->assertOk()
            ->assertJsonStructure(['statuses', 'can_submit', 'common_state', 'common_message'])
            ->assertJsonPath('can_submit', false)
            ->assertJsonPath('common_state', 'processing')
            ->assertJsonPath('common_message', 'Video đang trong quá trình xử lý bảo mật, xử lý xong bạn có thể bấm gửi duyệt.');
    }

    public function test_course_submit_is_rejected_when_video_hls_is_incomplete(): void
    {
        $instructor = $this->signInInstructor();
        [$course, $section] = $this->courseWithSection($instructor);

        Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Bài đang xử lý',
            'type' => 'video',
            'original_video_key' => "originals/courses/{$course->id}/lessons/1/video1.mp4",
            'upload_status' => 'uploaded',
            'processing_status' => 'processing',
            'sort_order' => 1,
            'status' => 'draft',
        ]);

        $course->update(['copyright_agreed' => true]);

        $response = $this->post(route('instructor.courses.submit', $course), [
            'copyright_agreed' => 1,
        ]);

        $response->assertSessionHas('error', 'Khóa học chưa thể gửi duyệt vì video vẫn đang được xử lý bảo mật.');
    }

    private function signInInstructor(?User $user = null): User
    {
        $user ??= User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'approved',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)->withSession(['two_factor_passed_at' => now()->timestamp]);

        return $user;
    }

    /**
     * @return array{0: Course, 1: CourseSection}
     */
    private function courseWithSection(User $instructor): array
    {
        $category = Category::create([
            'name' => 'Danh mục '.uniqid(),
            'slug' => 'category-'.uniqid(),
        ]);

        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Khóa học '.uniqid(),
            'slug' => 'course-'.uniqid(),
            'short_description' => 'Mô tả ngắn',
            'description' => 'Mô tả khóa học đủ dài',
            'objectives' => 'Mục tiêu học tập',
            'target_audience' => 'Học viên',
            'requirements' => 'Không yêu cầu',
            'price' => 100000,
            'language' => 'vi',
            'level' => 'beginner',
            'status' => Course::STATUS_DRAFT,
            'is_published' => false,
        ]);

        $section = CourseSection::create([
            'course_id' => $course->id,
            'title' => 'Chương 1',
            'sort_order' => 0,
        ]);

        return [$course, $section];
    }
}
