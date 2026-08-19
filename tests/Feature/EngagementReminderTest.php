<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\PushNotification;
use App\Models\User;
use App\Services\EngagementService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use App\Mail\LearningReminderMail;
use Tests\TestCase;

class EngagementReminderTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_user_learning_activity_records_timestamp_and_resets_stage(): void
    {
        $user = User::factory()->create([
            'last_learning_at' => null,
            'engagement_email_stage' => 3,
        ]);

        $service = app(EngagementService::class);
        $service->recordLearningActivity($user);

        $user->refresh();

        $this->assertNotNull($user->last_learning_at);
        $this->assertEquals(0, $user->engagement_email_stage);
    }

    public function test_reminder_command_sends_email_and_push_notification_for_stage_3_days(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'is_active' => true,
            'last_learning_at' => now()->subDays(4),
            'engagement_email_stage' => 0,
        ]);

        $course = $this->createCourse();

        Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'active',
            'progress_percent' => 85.0,
            'enrolled_at' => now()->subDays(10),
        ]);

        Artisan::call('engagement:process-reminders');

        $user->refresh();

        $this->assertEquals(1, $user->engagement_email_stage);
        $this->assertNotNull($user->last_engagement_sent_at);

        $this->assertDatabaseHas('push_notifications', [
            'user_id' => $user->id,
            'type' => 'learning_reminder',
            'message' => 'Bạn chỉ còn vài bài nữa là hoàn thành khóa học và nhận chứng chỉ.',
        ]);

        Mail::assertQueued(LearningReminderMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email) && str_contains($mail->reminderMessage, 'chứng chỉ');
        });
    }

    public function test_reminder_stages_progress_from_1_to_4_and_prevents_spam(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'is_active' => true,
            'last_learning_at' => now()->subDays(8),
            'engagement_email_stage' => 1,
        ]);

        $course = $this->createCourse();

        Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'active',
            'progress_percent' => 10.0,
            'enrolled_at' => now()->subDays(15),
        ]);

        Artisan::call('engagement:process-reminders');

        $user->refresh();
        $this->assertEquals(2, $user->engagement_email_stage);

        // Second run without time change should not send duplicate or jump stages
        Artisan::call('engagement:process-reminders');

        $user->refresh();
        $this->assertEquals(2, $user->engagement_email_stage);
    }

    private function createCourse(): Course
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $category = Category::create(['name' => 'Cat ' . uniqid(), 'slug' => 'cat-' . uniqid()]);

        return Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Khóa học Test ' . uniqid(),
            'slug' => 'khoa-hoc-test-' . uniqid(),
            'status' => 'published',
            'is_published' => true,
        ]);
    }
}
