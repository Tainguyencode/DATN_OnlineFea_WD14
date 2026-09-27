<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ContentUpdate;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\User;
use App\Services\ContentUpdateService;
use App\Services\ContentVersionService;
use App\Services\EnrollmentVersionService;
use App\Services\LessonVideoSourceService;
use App\Services\VideoTokenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VersionedVideoPlaybackTest extends TestCase
{
    use RefreshDatabase;

    private Lesson $lesson;

    private Enrollment $oldEnrollment;

    private Enrollment $newEnrollment;

    private array $directories;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        Storage::fake('s3');
        config(['filesystems.disks.s3.key' => null, 'filesystems.disks.s3.bucket' => 'playback-test']);
        $admin = User::factory()->create(['role' => 'admin']);
        $instructor = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'approved']);
        $category = Category::create(['name' => 'Playback', 'slug' => 'playback']);
        $instructor->instructorProfile()->create([])->teachingFields()->create([
            'category_id' => $category->id, 'approval_status' => 'approved',
        ]);
        $course = Course::create(['instructor_id' => $instructor->id, 'category_id' => $category->id,
            'title' => 'Playback', 'slug' => 'playback', 'price' => 0, 'status' => 'published', 'is_published' => true]);
        $section = CourseSection::create(['course_id' => $course->id, 'title' => 'Videos', 'sort_order' => 1]);
        $this->lesson = Lesson::create(['course_id' => $course->id, 'section_id' => $section->id,
            'title' => 'Video', 'type' => 'video', 'status' => 'published', 'sort_order' => 1, 'is_preview' => true]);
        $this->directories = [
            1 => ['local' => 'lesson-hls/'.$this->lesson->id, 's3' => 'hls/lessons/'.$this->lesson->id],
            2 => ['local' => 'lesson-hls/content-updates/test/versions/v2', 's3' => 'hls/content-updates/test/versions/v2'],
        ];
        $this->lesson->update($this->media(1));
        app(ContentVersionService::class)->publishInitialCourseTree($course, $admin);
        $this->oldEnrollment = app(EnrollmentVersionService::class)->firstOrCreate($course, User::factory()->create()->id);

        $update = ContentUpdate::create(['course_id' => $course->id, 'entity_id' => $this->lesson->id,
            'type' => 'lesson', 'action' => 'update', 'payload' => $this->media(2), 'status' => 'pending', 'created_by' => $admin->id]);
        app(ContentVersionService::class)->materializeCandidate($update, $admin);
        app(ContentUpdateService::class)->applyApprovedUpdate($update, $admin);
        $this->newEnrollment = app(EnrollmentVersionService::class)->firstOrCreate($course, User::factory()->create()->id);
        $this->lesson->refresh();

        foreach ($this->directories as $version => $disks) {
            foreach ($disks as $disk => $directory) {
                Storage::disk($disk)->put($directory.'/playlist.m3u8', "#EXTM3U\n#VERSION:{$version}\n#EXT-X-KEY:METHOD=AES-128,URI=\"enc.key\"\n#EXTINF:10,\nsegment_00000.ts\n#EXT-X-ENDLIST\n");
                Storage::disk($disk)->put($directory.'/master.m3u8', "#EXTM3U\nplaylist.m3u8\n");
                Storage::disk($disk)->put($directory.'/segment_00000.ts', 'VIDEO-V'.$version);
                Storage::disk($disk)->put($directory.'/enc.key', str_repeat((string) $version, 16));
            }
        }
    }

    private function media(int $version): array
    {
        return ['video_path' => $this->directories[$version]['local'].'/playlist.m3u8',
            'hls_manifest_key' => $this->directories[$version]['s3'].'/master.m3u8',
            'original_video_key' => 'original/v'.$version.'.mp4', 'duration_seconds' => $version * 100];
    }

    private function token(Enrollment $enrollment): string
    {
        return app(VideoTokenService::class)->generateToken($enrollment->user_id, $this->lesson->id);
    }

    private function parameters(string $token): array
    {
        return ['lesson' => $this->lesson->id, 'token' => $token];
    }

    public function test_local_playlist_segments_and_keys_follow_each_enrollment_release(): void
    {
        foreach ([1 => $this->oldEnrollment, 2 => $this->newEnrollment] as $version => $enrollment) {
            $token = $this->token($enrollment);
            $this->get(route('video.hls.playlist', $this->parameters($token)))
                ->assertOk()->assertSee('#VERSION:'.$version, false)
                ->assertSee('enc.key?token='.$token, false)->assertSee('segment_00000.ts?token='.$token, false);
            $this->get(route('video.hls.key', $this->parameters($token)))
                ->assertOk()->assertContent(str_repeat((string) $version, 16));
            $segment = $this->get(route('video.hls.segment', $this->parameters($token) + ['segment' => 'segment_00000.ts']))->assertOk();
            $this->assertSame('VIDEO-V'.$version, file_get_contents($segment->baseResponse->getFile()->getPathname()));
        }
        $this->assertSame($this->media(2)['hls_manifest_key'], $this->lesson->fresh()->hls_manifest_key);
    }

    public function test_s3_uses_version_directory_and_never_reuses_old_playlist_or_another_viewers_token(): void
    {
        config(['filesystems.disks.s3.key' => 'testing']);
        Storage::disk('s3')->buildTemporaryUrlsUsing(fn ($path) => 'https://media.example.test/'.$path.'?signed=1');
        Cache::put('hls_signed_playlist_student_'.$this->lesson->id.'_playlist', 'STALE-V1', 600);

        foreach ([1 => $this->oldEnrollment, 2 => $this->newEnrollment, 3 => $this->newEnrollment] as $iteration => $enrollment) {
            $version = min($iteration, 2);
            $token = $this->token($enrollment);
            $this->get(route('video.hls.playlist', $this->parameters($token)))
                ->assertOk()->assertSee('#VERSION:'.$version, false)->assertDontSee('STALE-V1')
                ->assertSee('https://media.example.test/'.$this->directories[$version]['s3'].'/segment_00000.ts?signed=1', false)
                ->assertSee('enc.key?token='.$token, false);
            $this->get(route('video.hls.segment', $this->parameters($token) + ['segment' => 'segment_00000.ts']))
                ->assertRedirect('https://media.example.test/'.$this->directories[$version]['s3'].'/segment_00000.ts?signed=1');
            $this->get(route('video.hls.key', $this->parameters($token)))
                ->assertOk()->assertContent(str_repeat((string) $version, 16));
        }
    }

    public function test_missing_v2_files_do_not_fall_back_to_v1(): void
    {
        Storage::disk('local')->deleteDirectory($this->directories[2]['local']);
        Storage::disk('s3')->deleteDirectory($this->directories[2]['s3']);
        config(['filesystems.disks.s3.key' => 'testing']);
        $parameters = $this->parameters($this->token($this->newEnrollment));
        $this->get(route('video.hls.playlist', $parameters))->assertNotFound();
        $this->get(route('video.hls.key', $parameters))->assertNotFound();
        $this->get(route('video.hls.segment', $parameters + ['segment' => 'segment_00000.ts']))->assertNotFound();
    }

    public function test_lesson_page_uses_pinned_video_duration_and_media_without_changing_live_lesson(): void
    {
        $this->actingAs($this->oldEnrollment->user)
            ->get(route('courses.lessons.show', [$this->lesson->course, $this->lesson]))
            ->assertOk()->assertViewHas('videoLesson', fn ($video) => $video->duration_seconds === 100
                && $video->hls_manifest_key === $this->media(1)['hls_manifest_key']);
        $this->assertSame(200, $this->lesson->fresh()->duration_seconds);
    }

    public function test_legacy_enrollment_uses_current_explicit_media_path(): void
    {
        $legacy = Enrollment::create(['user_id' => User::factory()->create()->id,
            'course_id' => $this->lesson->course_id, 'status' => 'active', 'enrolled_at' => now()]);
        $this->get(route('video.hls.playlist', $this->parameters($this->token($legacy))))
            ->assertOk()->assertSee('#VERSION:2', false);
    }

    public function test_pinned_viewer_cannot_read_video_outside_the_release(): void
    {
        $extra = Lesson::create(['course_id' => $this->lesson->course_id, 'section_id' => $this->lesson->section_id,
            'title' => 'New unpublished video', 'type' => 'video', 'status' => 'published', 'sort_order' => 2]);
        $token = app(VideoTokenService::class)->generateToken($this->oldEnrollment->user_id, $extra->id);
        $this->get(route('video.hls.playlist', ['lesson' => $extra->id, 'token' => $token]))->assertNotFound();
    }

    public function test_external_video_source_does_not_play_leftover_legacy_hls(): void
    {
        $video = clone $this->lesson;
        $video->forceFill(['video_url' => 'https://youtu.be/example', 'video_path' => null, 'hls_manifest_key' => null]);
        $this->assertFalse(app(LessonVideoSourceService::class)->hasHls($video));
        $this->assertSame(['s3' => null, 'local' => null], app(LessonVideoSourceService::class)->directories($video));
    }
}
