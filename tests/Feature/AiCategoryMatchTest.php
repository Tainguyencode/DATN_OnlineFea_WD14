<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use App\Models\VideoModeration;
use App\Services\GeminiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiCategoryMatchTest extends TestCase
{
    use RefreshDatabase;

    public function test_video_moderation_category_match_helpers(): void
    {
        $moderation = new VideoModeration([
            'violence' => false,
            'adult' => false,
            'weapon' => false,
            'details' => [
                'category_match' => [
                    'status' => 'Phù hợp',
                    'confidence' => 0.95,
                    'reason' => 'Nội dung video dạy Laravel Routing phù hợp với Phát triển Web.',
                    'detected_topics' => ['PHP', 'Laravel', 'Routing'],
                ],
            ],
        ]);

        $this->assertNotNull($moderation->categoryMatch());
        $this->assertEquals('Phù hợp', $moderation->categoryMatch()['status']);
        $this->assertEquals(0.95, $moderation->categoryMatch()['confidence']);
        $this->assertEquals(['PHP', 'Laravel', 'Routing'], $moderation->categoryMatch()['detected_topics']);

        $badge = $moderation->categoryMatchBadge();
        $this->assertEquals('Phù hợp', $badge['status']);
        $this->assertEquals('green', $badge['tone']);
        $this->assertEquals('🟢', $badge['emoji']);
    }

    public function test_category_match_api_endpoint_returns_prediction(): void
    {
        config(['services.gemini.api_key' => 'fake-gemini-api-key']);

        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $parentCat = Category::create(['name' => 'Công nghệ thông tin', 'slug' => 'it', 'status' => true]);
        $childCat = Category::create(['name' => 'Phát triển Web', 'slug' => 'web', 'parent_id' => $parentCat->id, 'status' => true]);

        $instructor = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'approved', 'is_active' => true]);
        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $childCat->id,
            'title' => 'Khóa học Laravel từ số 0',
            'slug' => 'khoa-hoc-laravel-tu-so-0',
            'price' => 200000,
            'language' => 'vi',
            'level' => 'beginner',
            'status' => Course::STATUS_DRAFT,
            'is_published' => false,
        ]);

        $lesson = Lesson::create([
            'course_id' => $course->id,
            'title' => 'Bài 1: Cài đặt và Routing trong Laravel',
            'type' => 'video',
            'sort_order' => 1,
            'is_published' => true,
        ]);

        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'status' => 'Phù hợp',
                                        'confidence' => 0.95,
                                        'reason' => 'Nội dung video nói về Laravel Routing, phù hợp với Phát triển Web.',
                                        'detected_topics' => ['PHP', 'Laravel', 'Routing'],
                                    ]),
                                ],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        // Tạo 1 file ảnh test
        $tempDir = storage_path('app/temp_frames/lesson_'.$lesson->id);
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }
        $testFrame = $tempDir.'/frame_0.jpg';
        file_put_contents($testFrame, 'fake-image-binary-data');

        $routeName = \Illuminate\Support\Facades\Route::has('admin.ai-moderation.category-match')
            ? 'admin.ai-moderation.category-match'
            : 'ai-moderation.category-match';

        $response = $this->actingAs($admin)->postJson(route($routeName, $lesson->id), [
            'frames' => [$testFrame],
        ]);

        $response->assertOk()
            ->assertJson([
                'status' => 'Phù hợp',
                'confidence' => 0.95,
                'detected_topics' => ['PHP', 'Laravel', 'Routing'],
            ]);

        // Dọn dẹp
        if (file_exists($testFrame)) {
            unlink($testFrame);
        }
        if (is_dir($tempDir)) {
            rmdir($tempDir);
        }
    }
}
