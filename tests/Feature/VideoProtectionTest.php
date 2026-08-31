<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\User;
use App\Services\VideoTokenService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class VideoProtectionTest extends TestCase
{
    use DatabaseTransactions;

    private string $localStorageRoot;

    protected function setUp(): void
    {
        parent::setUp();

        $this->localStorageRoot = storage_path('framework/testing/video-protection/'.Str::uuid());
        config(['filesystems.disks.local.root' => $this->localStorageRoot]);
        Storage::forgetDisk('local');
    }

    protected function tearDown(): void
    {
        Storage::forgetDisk('local');
        File::deleteDirectory($this->localStorageRoot);

        parent::tearDown();
    }

    public function test_unauthorized_user_cannot_access_hls_key(): void
    {
        $lesson = $this->createTestLesson();

        // Request key without token
        $response = $this->getJson(route('video.hls.key', ['lesson' => $lesson->id]));

        $response->assertStatus(404);
    }

    public function test_invalid_token_cannot_access_hls_key(): void
    {
        $lesson = $this->createTestLesson();

        // Request key with invalid token
        $response = $this->getJson(route('video.hls.key', [
            'lesson' => $lesson->id,
            'token' => 'invalid_token_123',
        ]));

        $response->assertStatus(404);
    }

    public function test_authorized_token_can_access_hls_key_when_key_exists(): void
    {
        $user = User::create([
            'name' => 'Test User '.uniqid(),
            'email' => 'user_'.uniqid().'@example.com',
            'password' => bcrypt('password'),
        ]);

        $lesson = $this->createTestLesson($user);

        $hlsDir = 'lesson-hls/'.$lesson->id;
        Storage::disk('local')->makeDirectory($hlsDir);

        $keyContent = random_bytes(16);
        Storage::disk('local')->put($hlsDir.'/enc.key', $keyContent);

        /** @var VideoTokenService $tokenService */
        $tokenService = app(VideoTokenService::class);
        $token = $tokenService->generateToken($user->id, $lesson->id);

        $response = $this->get(route('video.hls.key', [
            'lesson' => $lesson->id,
            'token' => $token,
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/octet-stream');
        $this->assertEquals($keyContent, $response->getContent());
    }

    public function test_playlist_replaces_key_uri_with_token(): void
    {
        $user = User::create([
            'name' => 'Test User '.uniqid(),
            'email' => 'user_'.uniqid().'@example.com',
            'password' => bcrypt('password'),
        ]);

        $lesson = $this->createTestLesson($user);

        $hlsDir = 'lesson-hls/'.$lesson->id;
        Storage::disk('local')->makeDirectory($hlsDir);

        $sampleM3u8 = "#EXTM3U\n"
            ."#EXT-X-VERSION:3\n"
            ."#EXT-X-KEY:METHOD=AES-128,URI=\"/api/video/hls/{$lesson->id}/enc.key\"\n"
            ."#EXTINF:10.000000,\n"
            ."segment0.ts\n";

        Storage::disk('local')->put($hlsDir.'/playlist.m3u8', $sampleM3u8);

        /** @var VideoTokenService $tokenService */
        $tokenService = app(VideoTokenService::class);
        $token = $tokenService->generateToken($user->id, $lesson->id);

        $response = $this->get(route('video.hls.playlist', [
            'lesson' => $lesson->id,
            'token' => $token,
        ]));

        $response->assertStatus(200);
        $content = $response->getContent();

        $this->assertStringContainsString('enc.key?token='.urlencode($token), $content);
        $this->assertStringContainsString('segment0.ts?token='.urlencode($token), $content);
    }

    private function createTestLesson(?User $instructor = null): Lesson
    {
        $instructor ??= User::create([
            'name' => 'Instructor '.uniqid(),
            'email' => 'inst_'.uniqid().'@example.com',
            'password' => bcrypt('password'),
            'role' => 'instructor',
            'instructor_status' => 'approved',
        ]);

        $category = Category::create([
            'name' => 'Cat '.uniqid(),
            'slug' => 'cat-'.uniqid(),
        ]);

        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Course '.uniqid(),
            'slug' => 'course-'.uniqid(),
            'short_description' => 'Desc',
            'description' => 'Full desc text',
            'objectives' => 'Objectives',
            'target_audience' => 'Audience',
            'requirements' => 'Requirements',
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

        return Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Lesson '.uniqid(),
            'type' => 'video',
            'sort_order' => 1,
            'status' => 'published',
            'upload_status' => 'uploaded',
            'processing_status' => 'completed',
        ]);
    }
}
