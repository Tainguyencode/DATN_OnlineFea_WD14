<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InstructorProfileAvatarTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (! in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('The pdo_sqlite driver is required for the in-memory database used by this test.');
        }

        parent::setUp();
    }

    public function test_unverified_instructor_can_update_avatar_from_instructor_profile_route(): void
    {
        Storage::fake('public');

        $instructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'approved',
            'email_verified_at' => now(),
            'name' => 'Instructor Avatar',
            'username' => 'instructor_avatar',
            'phone' => '0912345678',
            'is_active' => true,
            'two_factor_enabled' => false,
        ]);

        $category = Category::create(['name' => 'Development', 'slug' => 'development', 'status' => true]);

        $response = $this
            ->actingAs($instructor)
            ->from('/instructor/profile')
            ->put(route('instructor.profile.update'), [
                'name' => 'Instructor Avatar Updated',
                'username' => 'instructor_avatar',
                'phone' => '0912345678',
                'bio' => 'Updated instructor bio',
                'category_ids' => [$category->id],
                'teaching_fields' => [
                    ['category_id' => $category->id],
                ],
                'avatar' => UploadedFile::fake()->image('avatar.png', 120, 120),
            ]);

        $response->assertRedirect('/instructor/profile');

        $instructor->refresh();

        $this->assertSame('Instructor Avatar Updated', $instructor->name);
        $this->assertNotNull($instructor->avatar);
        Storage::disk('public')->assertExists($instructor->avatar);
    }

    public function test_generated_avatar_services_use_an_internal_data_uri_fallback(): void
    {
        $user = User::factory()->make([
            'name' => 'Nguyễn Văn Giảng',
            'avatar' => 'https://api.dicebear.com/7.x/adventurer/svg?seed=instructor',
        ]);

        $avatarUrl = $user->avatarUrl();

        $this->assertStringStartsWith('data:image/svg+xml;base64,', $avatarUrl);
        $this->assertStringContainsString('<svg', base64_decode(substr($avatarUrl, strlen('data:image/svg+xml;base64,')), true));
        $this->assertStringNotContainsString('dicebear.com', $avatarUrl);
    }

    public function test_real_remote_social_avatar_is_preserved(): void
    {
        $user = User::factory()->make([
            'avatar' => 'https://cdn.example.com/avatar.png',
        ]);

        $this->assertSame('https://cdn.example.com/avatar.png', $user->avatarUrl());
    }
}
