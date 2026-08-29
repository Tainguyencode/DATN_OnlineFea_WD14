<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Chapter;
use App\Models\ContentUpdate;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\User;
use App\Services\ContentUpdateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class Phase5AContentUpdateStateTest extends TestCase
{
    use RefreshDatabase;

    private function publishedCourse(): array
    {
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'approved',
            'is_active' => true,
        ]);
        $category = Category::create([
            'name' => 'Phase 5A '.uniqid(),
            'slug' => 'phase-5a-'.uniqid(),
        ]);
        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Published Phase 5A Course',
            'slug' => 'published-phase-5a-'.uniqid(),
            'short_description' => 'Short description',
            'description' => 'Detailed course description',
            'objectives' => 'Learn the workflow',
            'target_audience' => 'Instructors',
            'requirements' => 'None',
            'price' => 100000,
            'language' => 'vi',
            'level' => 'beginner',
            'status' => Course::STATUS_PUBLISHED,
            'is_published' => true,
        ]);
        $chapter = Chapter::create([
            'course_id' => $course->id,
            'title' => 'Legacy chapter',
            'sort_order' => 1,
        ]);

        return [$instructor, $course, $chapter];
    }

    private function contentUpdate(Course $course, User $instructor, array $overrides = []): ContentUpdate
    {
        return ContentUpdate::create(array_merge([
            'type' => ContentUpdate::TYPE_LESSON,
            'action' => ContentUpdate::ACTION_UPDATE,
            'course_id' => $course->id,
            'entity_id' => null,
            'payload' => ['title' => 'Original staged title'],
            'status' => ContentUpdate::STATUS_DRAFT,
            'created_by' => $instructor->id,
        ], $overrides));
    }

    public function test_draft_legacy_routes_keep_the_existing_direct_create_behavior(): void
    {
        [$instructor, $course, $chapter] = $this->publishedCourse();
        $course->update([
            'status' => Course::STATUS_DRAFT,
            'is_published' => false,
        ]);

        $this->actingAs($instructor)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->post(route('instructor.courses.chapters.store', $course), [
                'title' => 'Draft legacy chapter',
            ])
            ->assertRedirect();

        $this->actingAs($instructor)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->post(route('instructor.chapters.lessons.store', $chapter), [
                'title' => 'Draft legacy lesson',
                'type' => Lesson::TYPE_VIDEO,
                'video_url' => 'https://example.com/draft.mp4',
                'duration' => 60,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('chapters', [
            'course_id' => $course->id,
            'title' => 'Draft legacy chapter',
        ]);
        $this->assertDatabaseHas('lessons', [
            'course_id' => $course->id,
            'chapter_id' => $chapter->id,
            'title' => 'Draft legacy lesson',
        ]);
    }

    public function test_published_legacy_routes_stage_changes_without_mutating_legacy_tables(): void
    {
        [$instructor, $course, $chapter] = $this->publishedCourse();

        $this->actingAs($instructor)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->post(route('instructor.courses.chapters.store', $course), [
                'title' => 'Staged legacy chapter',
                'description' => 'A staged description',
            ])
            ->assertRedirect();

        $this->actingAs($instructor)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->post(route('instructor.chapters.lessons.store', $chapter), [
                'title' => 'Staged legacy lesson',
                'type' => Lesson::TYPE_VIDEO,
                'video_url' => 'https://example.com/staged.mp4',
                'duration' => 120,
            ])
            ->assertRedirect();

        $this->assertDatabaseMissing('chapters', [
            'course_id' => $course->id,
            'title' => 'Staged legacy chapter',
        ]);
        $this->assertDatabaseMissing('lessons', [
            'course_id' => $course->id,
            'title' => 'Staged legacy lesson',
        ]);
        $this->assertDatabaseHas('content_updates', [
            'course_id' => $course->id,
            'type' => ContentUpdate::TYPE_CHAPTER,
            'action' => ContentUpdate::ACTION_CREATE,
            'status' => ContentUpdate::STATUS_DRAFT,
        ]);
        $this->assertDatabaseHas('content_updates', [
            'course_id' => $course->id,
            'type' => ContentUpdate::TYPE_LESSON,
            'action' => ContentUpdate::ACTION_CREATE,
            'status' => ContentUpdate::STATUS_DRAFT,
        ]);
    }

    public function test_only_drafts_can_be_edited_or_deleted(): void
    {
        [$instructor, $course] = $this->publishedCourse();
        $service = app(ContentUpdateService::class);

        $draft = $this->contentUpdate($course, $instructor);
        $updated = $service->updateDraft($draft, ['title' => 'Updated draft title']);
        $this->assertSame('Updated draft title', $updated->payload['title']);
        $service->deleteDraft($updated);
        $this->assertDatabaseMissing('content_updates', ['id' => $draft->id]);

        $pending = $this->contentUpdate($course, $instructor, [
            'status' => ContentUpdate::STATUS_PENDING,
            'submitted_at' => now(),
        ]);
        $originalPayload = $pending->payload;

        try {
            $service->updateDraft($pending, ['title' => 'Must not change']);
            $this->fail('Pending payload mutation should be rejected.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('content_update', $exception->errors());
        }

        try {
            $service->deleteDraft($pending);
            $this->fail('Pending deletion should be rejected.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('content_update', $exception->errors());
        }

        $this->assertSame($originalPayload, $pending->fresh()->payload);
        $this->assertDatabaseHas('content_updates', ['id' => $pending->id, 'status' => ContentUpdate::STATUS_PENDING]);

        foreach ([ContentUpdate::STATUS_APPROVED, ContentUpdate::STATUS_REJECTED] as $terminalStatus) {
            $terminal = $this->contentUpdate($course, $instructor, ['status' => $terminalStatus]);

            try {
                $service->updateDraft($terminal, ['title' => 'Must remain immutable']);
                $this->fail("{$terminalStatus} payload mutation should be rejected.");
            } catch (ValidationException $exception) {
                $this->assertArrayHasKey('content_update', $exception->errors());
            }

            try {
                $service->deleteDraft($terminal);
                $this->fail("{$terminalStatus} deletion should be rejected.");
            } catch (ValidationException $exception) {
                $this->assertArrayHasKey('content_update', $exception->errors());
            }
        }
    }

    public function test_approval_and_rejection_require_pending_and_are_terminal(): void
    {
        [$instructor, $course] = $this->publishedCourse();
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $lesson = Lesson::create([
            'course_id' => $course->id,
            'title' => 'Published lesson',
            'type' => Lesson::TYPE_VIDEO,
            'video_url' => 'https://example.com/original.mp4',
            'duration_seconds' => 60,
            'status' => Lesson::STATUS_PUBLISHED,
        ]);
        $service = app(ContentUpdateService::class);

        $draft = $this->contentUpdate($course, $instructor, [
            'entity_id' => $lesson->id,
            'payload' => ['title' => 'Draft title'],
        ]);
        try {
            $service->applyApprovedUpdate($draft, $admin);
            $this->fail('Draft approval should be rejected.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('content_update', $exception->errors());
        }

        try {
            $service->rejectUpdate($draft, $admin, 'Draft cannot be rejected');
            $this->fail('Draft rejection should be rejected.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('content_update', $exception->errors());
        }

        $pending = $this->contentUpdate($course, $instructor, [
            'entity_id' => $lesson->id,
            'payload' => ['title' => 'Approved title'],
            'status' => ContentUpdate::STATUS_PENDING,
            'submitted_at' => now(),
        ]);
        $service->applyApprovedUpdate($pending, $admin);
        $this->assertSame('Approved title', $lesson->fresh()->title);
        $this->assertSame(ContentUpdate::STATUS_APPROVED, $pending->fresh()->status);

        try {
            $service->applyApprovedUpdate($pending, $admin);
            $this->fail('Approved non-quiz updates must be terminal.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('content_update', $exception->errors());
        }

        try {
            $service->rejectUpdate($pending, $admin, 'Approved update cannot be rejected');
            $this->fail('Approved non-quiz updates must be terminal.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('content_update', $exception->errors());
        }

        $rejected = $this->contentUpdate($course, $instructor, [
            'entity_id' => $lesson->id,
            'status' => ContentUpdate::STATUS_PENDING,
            'submitted_at' => now(),
        ]);
        $service->rejectUpdate($rejected, $admin, 'Needs more detail');
        $this->assertSame(ContentUpdate::STATUS_REJECTED, $rejected->fresh()->status);

        try {
            $service->rejectUpdate($rejected, $admin, 'Second decision');
            $this->fail('Rejected updates must be terminal.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('content_update', $exception->errors());
        }
    }

    public function test_instructor_http_routes_cannot_mutate_a_pending_update(): void
    {
        [$instructor, $course] = $this->publishedCourse();
        $pending = $this->contentUpdate($course, $instructor, [
            'status' => ContentUpdate::STATUS_PENDING,
            'submitted_at' => now(),
        ]);

        $this->actingAs($instructor)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->put(route('instructor.courses.content-updates.update', [$course, $pending]), [
                'title' => 'Attempted mutation',
            ])
            ->assertSessionHasErrors('content_update');

        $this->actingAs($instructor)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->delete(route('instructor.courses.content-updates.destroy', [$course, $pending]))
            ->assertSessionHasErrors('content_update');

        $this->assertSame('Original staged title', $pending->fresh()->payload['title']);
        $this->assertDatabaseHas('content_updates', [
            'id' => $pending->id,
            'status' => ContentUpdate::STATUS_PENDING,
        ]);
    }

    public function test_published_video_edit_keeps_live_source_until_approval(): void
    {
        [$instructor, $course] = $this->publishedCourse();
        $section = CourseSection::create([
            'course_id' => $course->id,
            'title' => 'Canonical section',
            'sort_order' => 1,
        ]);
        $lesson = Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Live video V1',
            'type' => Lesson::TYPE_VIDEO,
            'video_path' => 'lesson-videos-mp4/live-v1.mp4',
            'video_url' => null,
            'duration_seconds' => 120,
            'status' => Lesson::STATUS_PUBLISHED,
        ]);

        Storage::fake('local');
        Storage::disk('local')->put('lesson-videos-mp4/live-v1.mp4', 'live-v1');
        Queue::fake();

        $this->actingAs($instructor)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->put(route('instructor.courses.lessons.update', [$course, $lesson]), [
                'title' => 'Live video V2 draft',
                'type' => Lesson::TYPE_VIDEO,
                'video_file' => UploadedFile::fake()->create('video-v2.mp4', 100, 'video/mp4'),
                'duration' => 180,
            ])
            ->assertRedirect();

        Storage::disk('local')->assertExists('lesson-videos-mp4/live-v1.mp4');
        $this->assertSame('lesson-videos-mp4/live-v1.mp4', $lesson->fresh()->video_path);
        $this->assertDatabaseHas('content_updates', [
            'course_id' => $course->id,
            'type' => ContentUpdate::TYPE_LESSON,
            'action' => ContentUpdate::ACTION_UPDATE,
            'status' => ContentUpdate::STATUS_DRAFT,
        ]);
    }
}
