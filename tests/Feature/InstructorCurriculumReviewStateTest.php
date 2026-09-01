<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ContentUpdate;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\CourseVersion;
use App\Models\InstructorProfile;
use App\Models\Lesson;
use App\Models\LessonVersion;
use App\Models\User;
use App\Services\ContentUpdateDiffService;
use App\Services\ContentUpdateService;
use App\Services\ContentVersionService;
use App\Services\CourseReviewService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstructorCurriculumReviewStateTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_course_without_active_updates_does_not_render_submit_button(): void
    {
        [$instructor, $course] = $this->publishedReadyCourse();
        $state = app(ContentUpdateService::class)->instructorReviewState($course, $instructor);
        $this->assertFalse($state['hasDraftUpdates']);
        $this->assertFalse($state['hasPendingUpdates']);
        $this->assertFalse($state['canSubmitCourse']);

        $response = $this->curriculum($instructor, $course)->assertOk();
        $this->assertStringContainsString('V2', $response->getContent());
        $this->assertStringNotContainsString('id="curriculum-submit-review-btn"', $response->getContent());
        $this->assertStringNotContainsString('id="curriculum-pending-review-state"', $response->getContent());
    }

    public function test_ready_draft_update_renders_submit_button_but_pending_update_does_not(): void
    {
        [$instructor, $course] = $this->publishedReadyCourse();
        $draft = $this->draftUpdate($course, $instructor);

        $response = $this->curriculum($instructor, $course)->assertOk();
        $this->assertStringContainsString('id="curriculum-submit-review-btn"', $response->getContent());

        $draft->update(['status' => ContentUpdate::STATUS_PENDING, 'submitted_at' => now()]);

        $response = $this->curriculum($instructor, $course->fresh())->assertOk();
        $this->assertStringNotContainsString('id="curriculum-submit-review-btn"', $response->getContent());
        $this->assertStringContainsString('id="curriculum-pending-review-state"', $response->getContent());
    }

    public function test_approval_clears_submit_button_and_next_edit_restores_it(): void
    {
        [$instructor, $course, $admin] = $this->publishedReadyCourse();
        $draft = $this->draftUpdate($course, $instructor);

        app(CourseReviewService::class)->submitForReview($course->fresh(), $instructor);
        $this->assertSame(ContentUpdate::STATUS_PENDING, $draft->fresh()->status);

        app(CourseReviewService::class)->approve(
            $course->fresh(),
            $admin,
            collect(config('course.admin_review_checklist'))->mapWithKeys(fn ($label, $key) => [$key => true])->all(),
        );

        $course->refresh();
        $this->assertSame(Course::STATUS_PUBLISHED, $course->status);
        $this->assertNull($course->draft_version_id);
        $this->assertSame(ContentUpdate::STATUS_APPROVED, $draft->fresh()->status);

        $response = $this->curriculum($instructor, $course)->assertOk();
        $this->assertStringContainsString('V3', $response->getContent());
        $this->assertStringNotContainsString('id="curriculum-submit-review-btn"', $response->getContent());

        $next = app(ContentUpdateService::class)->recordPendingUpdate(
            ContentUpdate::TYPE_COURSE,
            ContentUpdate::ACTION_UPDATE,
            $course->id,
            $course->id,
            ['title' => 'Published course V4 candidate'],
            $instructor,
        );

        $this->assertSame(ContentUpdate::STATUS_DRAFT, $next->status);
        $nextVersion = app(ContentVersionService::class)->prepareDraftCandidate($next, $instructor);
        $this->assertSame(4, $nextVersion?->version_number);
        $state = app(ContentUpdateService::class)->instructorReviewState($course->fresh(), $instructor);
        $this->assertTrue($state['hasDraftUpdates']);
        $this->assertTrue($state['canSubmitUpdates'], json_encode([
            'status' => $course->fresh()->status,
            'submission' => $course->fresh()->submissionCheck()->items(),
            'blockers' => $state['videoReadinessBlockers'],
        ]));
        $response = $this->curriculum($instructor, $course->fresh())->assertOk();
        $this->assertStringContainsString('id="curriculum-submit-review-btn"', $response->getContent());
    }

    public function test_course_metadata_no_op_is_ignored_and_saved_draft_hydrates_edit_form(): void
    {
        [$instructor, $course] = $this->publishedReadyCourse();

        $initial = $this->edit($instructor, $course)->assertOk();
        $initial->assertDontSee('Gửi duyệt cập nhật');
        $this->assertStringContainsString('data-course-save disabled', $initial->getContent());

        $this->putCourse($instructor, $course, $this->coursePayload($course))
            ->assertRedirect();
        $this->assertDatabaseCount('content_updates', 0);

        $this->putCourse($instructor, $course, $this->coursePayload($course, ['title' => 'New Title']))
            ->assertRedirect()
            ->assertSessionHas('success');

        $draft = ContentUpdate::query()->where('course_id', $course->id)->sole();
        $this->assertSame(ContentUpdate::STATUS_DRAFT, $draft->status);
        $this->assertSame('New Title', $draft->payload['title']);
        $this->assertSame('New Title', CourseVersion::query()->where('content_update_id', $draft->id)->value('title'));

        $edit = $this->edit($instructor, $course->fresh())->assertOk();
        $edit->assertSee('value="New Title"', false)
            ->assertSee('Published curriculum state')
            ->assertSee('Gửi duyệt cập nhật')
            ->assertSee('1 thay đổi đang lưu nháp');
        $this->get(route('courses.show', $course->slug))
            ->assertOk()
            ->assertSee('Published curriculum state')
            ->assertDontSee('New Title');
    }

    public function test_ajax_lesson_save_returns_and_renders_fresh_canonical_review_state(): void
    {
        [$instructor, $course] = $this->publishedReadyCourse();
        $section = $course->courseSections()->firstOrFail();

        $response = $this->actingAs($instructor)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->postJson(route('instructor.courses.sections.lessons.store', [$course, $section]), [
                'title' => 'Queued document lesson',
                'type' => Lesson::TYPE_DOCUMENT,
                'content' => 'Document content',
                'duration' => 60,
            ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('draftCount', 1)
            ->assertJsonPath('pendingCount', 0)
            ->assertJsonPath('canSubmit', true)
            ->assertJsonPath('reviewState.hasDraftUpdates', true)
            ->assertJsonPath('reviewState.canSubmitCourse', true);

        $this->assertStringContainsString('id="curriculum-submit-review-btn"', $response->json('reviewStateHtml'));
        $this->assertStringContainsString('1 thay đổi mới đang lưu nháp', $response->json('reviewStateHtml'));
    }

    public function test_submit_is_idempotent_and_pending_course_proposal_stays_visible_and_read_only(): void
    {
        [$instructor, $course] = $this->publishedReadyCourse();
        $this->putCourse($instructor, $course, $this->coursePayload($course, ['title' => 'Pending New Title']))
            ->assertRedirect();

        $first = $this->actingAs($instructor)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->post(route('instructor.courses.submit', $course), ['copyright_agreed' => '1'])
            ->assertRedirect()
            ->assertSessionHas('success', 'Đã gửi 1 thay đổi để Admin duyệt.');

        $second = $this->actingAs($instructor)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->post(route('instructor.courses.submit', $course->fresh()), ['copyright_agreed' => '1'])
            ->assertRedirect()
            ->assertSessionHas('info', 'Cập nhật này đã được gửi duyệt.');

        $this->assertDatabaseCount('content_updates', 1);
        $this->assertDatabaseCount('course_reviews', 1);
        $update = ContentUpdate::query()->sole();
        $this->assertSame(ContentUpdate::STATUS_PENDING, $update->status);
        $this->assertSame(1, CourseVersion::query()->where('content_update_id', $update->id)->count());

        $edit = $this->edit($instructor, $course->fresh())->assertOk();
        $edit->assertSee('value="Pending New Title"', false)
            ->assertSee('Phiên bản này đang chờ Admin duyệt.')
            ->assertDontSee('Gửi duyệt cập nhật');
        $this->assertStringContainsString('data-read-only="true"', $edit->getContent());
        $this->get(route('courses.show', $course->slug))
            ->assertOk()
            ->assertSee('Published curriculum state')
            ->assertDontSee('Pending New Title');
    }

    public function test_pending_batch_is_frozen_and_approval_leaves_queued_draft_for_next_batch(): void
    {
        [$instructor, $course, $admin] = $this->publishedReadyCourse();
        $lesson = $course->lessons()->firstOrFail();
        $pending = app(ContentUpdateService::class)->recordPendingUpdate(
            ContentUpdate::TYPE_LESSON,
            ContentUpdate::ACTION_UPDATE,
            $course->id,
            $lesson->id,
            ['title' => 'Frozen pending lesson title'],
            $instructor,
        );
        app(ContentVersionService::class)->prepareDraftCandidate($pending, $instructor);
        app(CourseReviewService::class)->submitForReview($course->fresh(), $instructor);

        $this->putCourse($instructor, $course->fresh(), $this->coursePayload($course, ['title' => 'Queued batch two title']))
            ->assertRedirect()
            ->assertSessionHas('success');

        $queued = ContentUpdate::query()->where('type', ContentUpdate::TYPE_COURSE)->sole();
        $this->assertSame(ContentUpdate::STATUS_PENDING, $pending->fresh()->status);
        $this->assertSame(ContentUpdate::STATUS_DRAFT, $queued->status);
        $state = app(ContentUpdateService::class)->instructorReviewState($course->fresh(), $instructor);
        $this->assertSame(1, $state['pendingCount']);
        $this->assertSame(1, $state['draftCount']);
        $this->assertFalse($state['canSubmitCourse']);
        $this->assertSame('Chờ Admin xử lý lượt duyệt hiện tại.', $state['submissionBlockedReason']);

        $curriculum = $this->curriculum($instructor, $course->fresh())->assertOk();
        $curriculum->assertSee('1 thay đổi đang chờ Admin duyệt')
            ->assertSee('1 thay đổi mới đang lưu nháp cho lượt tiếp theo')
            ->assertSee('Chờ lượt duyệt hiện tại');

        $adminReview = $this->actingAs($admin)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->get(route('admin.courses.review', $course->fresh()))
            ->assertOk()
            ->assertSee('Frozen pending lesson title')
            ->assertDontSee('Queued batch two title');

        app(CourseReviewService::class)->approve($course->fresh(), $admin, $this->completeChecklist());

        $this->assertSame(ContentUpdate::STATUS_APPROVED, $pending->fresh()->status);
        $this->assertSame(ContentUpdate::STATUS_DRAFT, $queued->fresh()->status);
        $this->assertSame('Queued batch two title', CourseVersion::query()->where('content_update_id', $queued->id)->value('title'));
        $this->assertTrue(app(ContentUpdateService::class)->instructorReviewState($course->fresh(), $instructor)['canSubmitCourse']);
    }

    public function test_rejection_keeps_queued_draft_and_revision_preserves_rejected_proposal(): void
    {
        [$instructor, $course, $admin] = $this->publishedReadyCourse();
        $pending = app(ContentUpdateService::class)->saveCourseMetadataDraft(
            $course,
            $this->coursePayload($course, ['title' => 'Rejected proposed title']),
            $instructor,
        )['update'];
        app(CourseReviewService::class)->submitForReview($course->fresh(), $instructor);

        $lesson = $course->lessons()->firstOrFail();
        $queued = app(ContentUpdateService::class)->recordPendingUpdate(
            ContentUpdate::TYPE_LESSON,
            ContentUpdate::ACTION_UPDATE,
            $course->id,
            $lesson->id,
            ['title' => 'Queued lesson after rejection'],
            $instructor,
        );
        app(ContentVersionService::class)->prepareDraftCandidate($queued, $instructor);
        $frozenTitle = CourseVersion::query()->where('content_update_id', $pending->id)->value('title');

        app(CourseReviewService::class)->reject($course->fresh(), $admin, 'Needs correction', $this->completeChecklist());

        $this->assertSame(ContentUpdate::STATUS_REJECTED, $pending->fresh()->status);
        $this->assertSame(ContentUpdate::STATUS_DRAFT, $queued->fresh()->status);
        $this->assertSame($frozenTitle, CourseVersion::query()->where('content_update_id', $pending->id)->value('title'));
        $revision = app(ContentUpdateService::class)->createRevisionFromRejected($pending->fresh(), $instructor);
        $this->assertSame('Rejected proposed title', $revision->payload['title']);
        $this->edit($instructor, $course->fresh())
            ->assertOk()
            ->assertSee('value="Rejected proposed title"', false);
        $this->assertSame('Published curriculum state', $course->fresh()->title);
    }

    public function test_lesson_diff_label_prefers_frozen_candidate_then_payload_then_identity(): void
    {
        [$instructor, $course] = $this->publishedReadyCourse();
        $lesson = $course->lessons()->firstOrFail();
        $update = app(ContentUpdateService::class)->recordPendingUpdate(
            ContentUpdate::TYPE_LESSON,
            ContentUpdate::ACTION_UPDATE,
            $course->id,
            $lesson->id,
            ['title' => 'Frozen candidate lesson'],
            $instructor,
        );
        app(ContentVersionService::class)->prepareDraftCandidate($update, $instructor);
        $update->update(['payload' => []]);
        $lesson->update(['title' => 'Later live identity title']);

        $diff = app(ContentUpdateDiffService::class)->build($update->fresh());
        $this->assertSame('Bài học: Frozen candidate lesson', $diff['label']);

        Lesson::query()->whereKey($lesson->id)->update(['draft_version_id' => null]);
        LessonVersion::query()->where('content_update_id', $update->id)->delete();
        $update->update(['payload' => ['title' => 'Payload lesson title']]);
        $this->assertSame('Bài học: Payload lesson title', app(ContentUpdateDiffService::class)->build($update->fresh())['label']);

        $update->update(['payload' => []]);
        $this->assertSame('Bài học: Later live identity title', app(ContentUpdateDiffService::class)->build($update->fresh())['label']);
    }

    public function test_rejected_lesson_revision_replaces_actionable_card_and_remains_in_history_after_approval(): void
    {
        [$instructor, $course, $admin] = $this->publishedReadyCourse();
        $lesson = $course->lessons()->firstOrFail();
        app(ContentVersionService::class)->createInitialLessonVersion($lesson, $admin);

        foreach (range(2, 4) as $version) {
            $this->approveLessonGeneration($course, $lesson, $instructor, $admin, "Lesson published V{$version}");
        }
        $this->assertSame(4, $lesson->fresh()->publishedVersion->version_number);

        $rejected = app(ContentUpdateService::class)->recordPendingUpdate(
            ContentUpdate::TYPE_LESSON,
            ContentUpdate::ACTION_UPDATE,
            $course->id,
            $lesson->id,
            ['title' => 'Rejected lesson proposal'],
            $instructor,
        );
        $rejectedCandidate = app(ContentVersionService::class)->prepareDraftCandidate($rejected, $instructor);
        $rejected->update(['status' => ContentUpdate::STATUS_PENDING, 'submitted_at' => now()]);
        app(ContentUpdateService::class)->rejectUpdate($rejected->fresh(), $admin, 'Sửa tên bài học đi');

        $this->assertSame(5, $rejectedCandidate?->version_number);
        $rejectedPage = $this->curriculum($instructor, $course->fresh())->assertOk();
        $rejectedPage->assertSee('data-content-update-id="'.$rejected->id.'"', false)
            ->assertSee('Đang xuất bản: V4')
            ->assertSee('Đề xuất V5 — Bị từ chối')
            ->assertSee('Sửa tên bài học đi')
            ->assertSee('Tạo bản chỉnh sửa mới');

        $revision = app(ContentUpdateService::class)->createRevisionFromRejected($rejected->fresh(), $instructor);
        $revisionCandidate = LessonVersion::query()->where('content_update_id', $revision->id)->sole();

        $this->assertSame(ContentUpdate::STATUS_REJECTED, $rejected->fresh()->status);
        $this->assertSame(LessonVersion::STATUS_REJECTED, $rejectedCandidate->fresh()->status);
        $this->assertSame($rejected->id, data_get($revision->metadata, 'revision_of_content_update_id'));
        $this->assertSame(6, $revisionCandidate->version_number);
        $this->assertSame(ContentUpdate::STATUS_DRAFT, $revision->status);

        $draftState = app(ContentUpdateService::class)->instructorReviewState($course->fresh(), $instructor);
        $this->assertSame([$revision->id], $draftState['activeUpdates']->pluck('id')->all());
        $this->assertTrue($draftState['actionableRejectedUpdates']->isEmpty());
        $draftPage = $this->curriculum($instructor, $course->fresh())->assertOk();
        $draftPage->assertDontSee('data-content-update-id="'.$rejected->id.'"', false)
            ->assertSee('data-content-update-id="'.$revision->id.'"', false)
            ->assertSee('Đề xuất V6 — Nháp')
            ->assertDontSee('Tạo bản chỉnh sửa mới');

        app(CourseReviewService::class)->submitForReview($course->fresh(), $instructor);
        $pendingState = app(ContentUpdateService::class)->instructorReviewState($course->fresh(), $instructor);
        $this->assertSame([$revision->id], $pendingState['activeUpdates']->pluck('id')->all());
        $this->assertTrue($pendingState['actionableRejectedUpdates']->isEmpty());
        $pendingPage = $this->curriculum($instructor, $course->fresh())->assertOk();
        $pendingPage->assertDontSee('data-content-update-id="'.$rejected->id.'"', false)
            ->assertSee('data-content-update-id="'.$revision->id.'"', false)
            ->assertSee('Đề xuất V6 — Chờ duyệt');

        app(CourseReviewService::class)->approve($course->fresh(), $admin, $this->completeChecklist());

        $this->assertSame(ContentUpdate::STATUS_REJECTED, $rejected->fresh()->status);
        $this->assertSame(ContentUpdate::STATUS_APPROVED, $revision->fresh()->status);
        $this->assertSame(LessonVersion::STATUS_REJECTED, $rejectedCandidate->fresh()->status);
        $this->assertSame(LessonVersion::STATUS_PUBLISHED, $revisionCandidate->fresh()->status);
        $this->assertSame(6, $lesson->fresh()->publishedVersion->version_number);
        $this->assertNull($lesson->fresh()->draft_version_id);

        $approvedState = app(ContentUpdateService::class)->instructorReviewState($course->fresh(), $instructor);
        $this->assertTrue($approvedState['updates']->isEmpty());
        $approvedPage = $this->curriculum($instructor, $course->fresh())->assertOk();
        $approvedPage->assertDontSee('id="curriculum-active-review-panel"', false)
            ->assertDontSee('id="curriculum-actionable-rejections-panel"', false)
            ->assertDontSee('Tạo bản chỉnh sửa mới');

        $this->actingAs($instructor)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->get(route('instructor.courses.versions.index', $course->fresh()))
            ->assertOk()
            ->assertSee('V5')
            ->assertSee('Bị từ chối');
    }

    public function test_legacy_rejected_update_with_a_later_approved_successor_is_historical_only(): void
    {
        [$instructor, $course] = $this->publishedReadyCourse();
        $lesson = $course->lessons()->firstOrFail();
        $rejected = ContentUpdate::create([
            'type' => ContentUpdate::TYPE_LESSON,
            'action' => ContentUpdate::ACTION_UPDATE,
            'course_id' => $course->id,
            'entity_id' => $lesson->id,
            'payload' => ['title' => 'Legacy rejected title'],
            'status' => ContentUpdate::STATUS_REJECTED,
            'rejection_reason' => 'Legacy rejection',
            'created_by' => $instructor->id,
        ]);
        $successor = ContentUpdate::create([
            'type' => ContentUpdate::TYPE_LESSON,
            'action' => ContentUpdate::ACTION_UPDATE,
            'course_id' => $course->id,
            'entity_id' => $lesson->id,
            'payload' => ['title' => 'Approved successor title'],
            'status' => ContentUpdate::STATUS_APPROVED,
            'created_by' => $instructor->id,
        ]);

        $state = app(ContentUpdateService::class)->instructorReviewState($course->fresh(), $instructor);
        $this->assertTrue($state['updates']->isEmpty());
        $this->assertTrue($state['actionableRejectedUpdates']->isEmpty());
        $this->assertDatabaseHas('content_updates', ['id' => $rejected->id, 'status' => ContentUpdate::STATUS_REJECTED]);
        $this->assertDatabaseHas('content_updates', ['id' => $successor->id, 'status' => ContentUpdate::STATUS_APPROVED]);

        $this->curriculum($instructor, $course->fresh())
            ->assertOk()
            ->assertDontSee('Legacy rejected title')
            ->assertDontSee('Tạo bản chỉnh sửa mới');
    }

    /** @return array{0: User, 1: Course, 2: User} */
    private function publishedReadyCourse(): array
    {
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'approved',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $category = Category::create([
            'name' => 'Curriculum state '.uniqid(),
            'slug' => 'curriculum-state-'.uniqid(),
            'status' => true,
        ]);
        $profile = InstructorProfile::create(['user_id' => $instructor->id]);
        $profile->teachingCategories()->attach($category->id, ['is_primary' => true]);
        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Published curriculum state',
            'slug' => 'published-curriculum-state-'.uniqid(),
            'short_description' => 'Short description',
            'description' => 'Detailed published description',
            'objectives' => 'Learning objective',
            'requirements' => 'None',
            'target_audience' => 'Students',
            'thumbnail' => 'courses/thumbnail.png',
            'price' => 100000,
            'language' => 'vi',
            'level' => 'beginner',
            'status' => Course::STATUS_PUBLISHED,
            'is_published' => true,
            'copyright_agreed' => true,
        ]);
        $v2 = CourseVersion::create([
            'course_id' => $course->id,
            'version_number' => 2,
            'status' => CourseVersion::STATUS_PUBLISHED,
            'title' => $course->title,
            'slug' => $course->slug,
            'short_description' => $course->short_description,
            'description' => $course->description,
            'objectives' => $course->objectives,
            'requirements' => $course->requirements,
            'target_audience' => $course->target_audience,
            'category_id' => $category->id,
            'level' => $course->level,
            'language' => 'vi',
            'price' => 100000,
            'thumbnail' => $course->thumbnail,
            'created_by' => $instructor->id,
            'published_by' => $admin->id,
            'published_at' => now(),
        ]);
        $course->forceFill(['published_version_id' => $v2->id])->save();
        $section = CourseSection::create([
            'course_id' => $course->id,
            'title' => 'Published section',
            'sort_order' => 0,
        ]);

        foreach (range(1, 5) as $position) {
            Lesson::create([
                'course_id' => $course->id,
                'section_id' => $section->id,
                'title' => "Ready video {$position}",
                'type' => Lesson::TYPE_VIDEO,
                'duration' => 360,
                'duration_seconds' => 360,
                'sort_order' => $position,
                'status' => Lesson::STATUS_PUBLISHED,
                'original_video_key' => "originals/ready-{$position}.mp4",
                'hls_manifest_key' => "hls/ready-{$position}/master.m3u8",
                'video_path' => "lesson-hls/ready-{$position}/playlist.m3u8",
                'upload_status' => 'uploaded',
                'processing_status' => 'completed',
            ]);
        }

        return [$instructor, $course->fresh(), $admin];
    }

    private function draftUpdate(Course $course, User $instructor): ContentUpdate
    {
        return app(ContentUpdateService::class)->recordPendingUpdate(
            ContentUpdate::TYPE_COURSE,
            ContentUpdate::ACTION_UPDATE,
            $course->id,
            $course->id,
            ['title' => 'Published course V3 candidate'],
            $instructor,
        );
    }

    private function approveLessonGeneration(Course $course, Lesson $lesson, User $instructor, User $admin, string $title): void
    {
        $update = app(ContentUpdateService::class)->recordPendingUpdate(
            ContentUpdate::TYPE_LESSON,
            ContentUpdate::ACTION_UPDATE,
            $course->id,
            $lesson->id,
            ['title' => $title],
            $instructor,
        );
        app(ContentVersionService::class)->prepareDraftCandidate($update, $instructor);
        $update->update(['status' => ContentUpdate::STATUS_PENDING, 'submitted_at' => now()]);
        app(ContentUpdateService::class)->applyApprovedUpdate($update->fresh(), $admin);
    }

    private function curriculum(User $instructor, Course $course)
    {
        return $this->actingAs($instructor)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->get(route('instructor.courses.curriculum', $course));
    }

    private function edit(User $instructor, Course $course)
    {
        return $this->actingAs($instructor)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->get(route('instructor.courses.edit', $course));
    }

    private function putCourse(User $instructor, Course $course, array $payload)
    {
        return $this->actingAs($instructor)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->put(route('instructor.courses.update', $course), $payload);
    }

    /** @param array<string, mixed> $overrides @return array<string, mixed> */
    private function coursePayload(Course $course, array $overrides = []): array
    {
        return array_merge([
            'title' => $course->title,
            'category_id' => $course->category_id,
            'short_description' => $course->short_description,
            'description' => $course->description,
            'objectives' => $course->objectives,
            'preview_video' => $course->preview_video,
            'price' => (int) $course->price,
            'discount_price' => $course->discount_price,
            'level' => $course->level,
            'language' => $course->language,
        ], $overrides);
    }

    /** @return array<string, bool> */
    private function completeChecklist(): array
    {
        return collect(config('course.admin_review_checklist'))
            ->mapWithKeys(fn ($label, $key) => [$key => true])
            ->all();
    }
}
