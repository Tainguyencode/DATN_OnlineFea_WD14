<?php

namespace Tests\Feature;

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

        $instructor = User::factory()->unverified()->create([
            'role' => 'instructor',
            'name' => 'Instructor Avatar',
            'username' => 'instructor_avatar',
            'phone' => '0912345678',
            'is_active' => true,
            'two_factor_enabled' => false,
        ]);

        $response = $this
            ->actingAs($instructor)
            ->from('/instructor/profile')
            ->put(route('instructor.profile.update'), [
                'name' => 'Instructor Avatar Updated',
                'username' => 'instructor_avatar',
                'phone' => '0912345678',
                'bio' => 'Updated instructor bio',
                'avatar' => UploadedFile::fake()->image('avatar.png', 120, 120),
            ]);

        $response->assertRedirect('/instructor/profile');

        $instructor->refresh();

        $this->assertSame('Instructor Avatar Updated', $instructor->name);
        $this->assertNotNull($instructor->avatar);
        Storage::disk('public')->assertExists($instructor->avatar);
    }
}
