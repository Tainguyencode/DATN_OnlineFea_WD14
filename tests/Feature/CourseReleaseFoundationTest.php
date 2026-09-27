<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Category;
use App\Models\ContentUpdate;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\CourseVersion;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Quiz;
use App\Models\Submission;
use App\Models\User;
use App\Services\ContentUpdateService;
use App\Services\ContentVersionService;
use App\Services\EnrollmentReleaseBackfillService;
use App\Services\EnrollmentVersionService;
use App\Services\PaymentGatewayService;
use App\Services\QuizContentService;
use App\Services\QuizVersioningService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CourseReleaseFoundationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Course $course;

    private CourseSection $section;

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(now()->startOfSecond());
        $this->admin = User::factory()->create(['role' => 'admin']);
        $instructor = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'approved']);
        $category = Category::create(['name' => 'Releases', 'slug' => 'releases']);
        $profile = $instructor->instructorProfile()->create([]);
        $profile->teachingFields()->create(['category_id' => $category->id, 'approval_status' => 'approved']);
        $this->course = Course::create(['instructor_id' => $instructor->id, 'category_id' => $category->id,
            'title' => 'Course V1', 'slug' => 'course-releases', 'price' => 0,
            'status' => 'published', 'is_published' => true, 'certificate_enabled' => true]);
        $this->section = CourseSection::create(['course_id' => $this->course->id, 'title' => 'Section', 'sort_order' => 1]);
    }

    private function lesson(string $title = 'L1', int $order = 1, string $type = 'document'): Lesson
    {
        return Lesson::create(['course_id' => $this->course->id, 'section_id' => $this->section->id,
            'title' => $title, 'type' => $type, 'content' => $title, 'sort_order' => $order,
            'is_required' => true, 'status' => 'published']);
    }

    private function initial(): CourseVersion
    {
        app(ContentVersionService::class)->publishInitialCourseTree($this->course, $this->admin);

        return $this->course->fresh()->publishedVersion;
    }

    private function enroll(?User $user = null): Enrollment
    {
        return app(EnrollmentVersionService::class)->firstOrCreate($this->course, ($user ?? User::factory()->create())->id);
    }

    private function update(string $type, int $entity, array $payload, string $action = 'update'): ContentUpdate
    {
        $update = ContentUpdate::create(['course_id' => $this->course->id, 'entity_id' => $entity,
            'type' => $type, 'action' => $action, 'payload' => $payload, 'status' => 'pending', 'created_by' => $this->admin->id]);
        app(ContentVersionService::class)->materializeCandidate($update, $this->admin);

        return $update;
    }

    private function approve(ContentUpdate $update): CourseVersion
    {
        $this->travel(2)->seconds();
        app(ContentUpdateService::class)->applyApprovedUpdate($update, $this->admin);

        return $this->course->fresh()->publishedVersion;
    }

    public function test_v1_v2_draft_v3_and_published_v3_keep_each_enrollment_on_its_release(): void
    {
        $this->lesson();
        $v1 = $this->initial();
        $a = $this->enroll();
        $this->assertSame($v1->id, $a->course_version_id);
        $v2 = $this->approve($this->update('course', $this->course->id, ['title' => 'V2']));
        $this->assertSame($v1->id, $a->fresh()->course_version_id);
        $b = $this->enroll();
        $this->assertSame($v2->id, $b->course_version_id);
        $pending = $this->update('course', $this->course->id, ['title' => 'V3']);
        $c = $this->enroll();
        $this->assertSame($v2->id, $c->course_version_id);
        $v3 = $this->approve($pending);
        $d = $this->enroll();
        $this->assertSame($v3->id, $d->course_version_id);
        $this->assertSame($v1->id, $a->fresh()->course_version_id);
        $this->assertSame($v2->id, $b->fresh()->course_version_id);
        $this->assertSame(1, Course::count());
    }

    public function test_add_lesson_does_not_mutate_old_manifest(): void
    {
        $lesson = $this->lesson();
        $v1 = $this->initial();
        $v2 = $this->approve($this->update('lesson', 0, ['section_id' => $this->section->id, 'title' => 'L2', 'type' => 'document', 'sort_order' => 2, 'status' => 'published'], 'create'));
        $this->assertSame([$lesson->id], $v1->lessonMappings()->pluck('lesson_id')->all());
        $this->assertCount(2, $v2->lessonMappings);
        $this->assertSame($v1->lessonMappings->first()->lesson_version_id, $v2->lessonMappings->first()->lesson_version_id);
    }

    public function test_delete_keeps_old_lesson_identity_and_version_readable(): void
    {
        $lesson = $this->lesson();
        $v1 = $this->initial();
        $a = $this->enroll();
        $v2 = $this->approve($this->update('lesson', $lesson->id, [], 'delete'));
        $this->assertCount(0, $v2->lessonMappings);
        $old = app(EnrollmentVersionService::class)->resolveEnrollmentRelease($a);
        $this->assertSame($v1->id, $old->id);
        $this->assertSame('superseded', $old->status);
        $this->assertSame($lesson->id, $old->lessonMappings->first()->lesson->id);
        $this->assertSame('L1', $old->lessonMappings->first()->lessonVersion->title);
        $this->assertNull(Lesson::find($lesson->id));
    }

    public function test_reorder_uses_frozen_candidates_not_mutated_live_rows(): void
    {
        $l1 = $this->lesson('L1', 1);
        $l2 = $this->lesson('L2', 2);
        $l3 = $this->lesson('L3', 3);
        $v1 = $this->initial();
        $update = $this->update('lesson', $l1->id, ['lesson_orders' => [
            ['id' => $l3->id, 'sort_order' => 1], ['id' => $l1->id, 'sort_order' => 2], ['id' => $l2->id, 'sort_order' => 3],
        ]], 'reorder');
        $l3->update(['sort_order' => 99]);
        $v2 = $this->approve($update);
        $this->assertSame([$l1->id, $l2->id, $l3->id], $v1->lessonMappings()->pluck('lesson_id')->all());
        $this->assertSame([$l3->id, $l1->id, $l2->id], $v2->lessonMappings()->pluck('lesson_id')->all());
    }

    public function test_assignment_mapping_and_completion_rules_are_snapshotted(): void
    {
        $lesson = $this->lesson('Assignment', 1, 'assignment');
        $assignment = Assignment::create(['course_id' => $this->course->id, 'lesson_id' => $lesson->id, 'title' => 'A1', 'description' => 'Instructions', 'passing_score' => 70]);
        $v1 = $this->initial();
        $v2 = $this->approve($this->update('assignment', $assignment->id, ['passing_score' => 90]));
        $this->assertNotSame($v1->lessonMappings->first()->assignment_version_id, $v2->lessonMappings->first()->assignment_version_id);
        $this->assertSame(70, $v1->lessonMappings->first()->assignmentVersion->passing_score);
        $this->assertSame(90, $v2->lessonMappings->first()->assignmentVersion->passing_score);
        $v3 = $this->approve($this->update('course', $this->course->id, ['certificate_enabled' => false, 'required_video_percent' => 95]));
        $this->assertTrue($v1->certificate_enabled);
        $this->assertFalse($v3->certificate_enabled);
        $this->assertSame(95, $v3->required_video_percent);
        $this->assertSame(80, $v1->required_video_percent);
    }

    public function test_quiz_mapping_stays_readable_without_any_previous_attempt(): void
    {
        $lesson = $this->lesson('Quiz', 1, 'quiz');
        $quiz = Quiz::create(['lesson_id' => $lesson->id, 'title' => 'Q1']);
        $q1 = $quiz->versions()->create(['version' => 1, 'status' => 'published', 'title' => 'Q1', 'published_at' => now()]);
        $quiz->update(['current_published_version_id' => $q1->id]);
        $v1 = $this->initial();
        $a = $this->enroll();
        // Use the existing approval path; validation is tested by quiz authoring tests.
        $this->mock(QuizContentService::class)->shouldReceive('validateQuizVersion')->andReturn(['is_complete' => true, 'errors' => []]);
        $q2 = $quiz->versions()->create(['version' => 2, 'status' => 'draft', 'title' => 'Q2']);
        $quiz->update(['current_draft_version_id' => $q2->id]);
        $update = $this->update('quiz', $quiz->id, ['quiz_id' => $quiz->id, 'quiz_version_id' => $q2->id]);
        $v2 = $this->approve($update);
        $this->assertSame($q2->id, $v2->lessonMappings->first()->quiz_version_id);
        $this->assertSame($q1->id, app(EnrollmentVersionService::class)->resolveEnrollmentRelease($a)->lessonMappings->first()->quizVersion->id);
        $this->assertSame($q1->id, $v1->lessonMappings->first()->quiz_version_id);
        $this->assertDatabaseCount('quiz_attempts', 0);
        app(ContentUpdateService::class)->applyApprovedUpdate($update->fresh(), $this->admin);
        $this->assertSame($v2->id, $this->course->fresh()->published_version_id);
    }

    public function test_invalid_foreign_course_pointer_fails_closed(): void
    {
        $v1 = $this->initial();
        $other = $this->course->replicate();
        $other->slug = 'other-course';
        $other->published_version_id = $v1->id;
        $other->save();
        $this->expectException(ValidationException::class);
        app(EnrollmentVersionService::class)->firstOrCreate($other, User::factory()->create()->id);
    }

    public function test_missing_pointer_is_not_repaired_by_latest_version_fallback(): void
    {
        $this->initial();
        $this->course->forceFill(['published_version_id' => null])->save();
        $this->expectException(ValidationException::class);
        $this->enroll();
    }

    public function test_draft_pointer_is_never_enrollable(): void
    {
        $this->initial();
        $draft = app(ContentVersionService::class)->cloneCourseVersion($this->course, $this->admin);
        $this->course->forceFill(['published_version_id' => $draft->id])->save();
        $this->expectException(ValidationException::class);
        $this->enroll();
    }

    public function test_enrollment_pin_cannot_be_reassigned(): void
    {
        $this->initial();
        $a = $this->enroll();
        $v2 = $this->approve($this->update('course', $this->course->id, ['title' => 'V2']));
        $this->expectException(ValidationException::class);
        $a->update(['course_version_id' => $v2->id]);
    }

    public function test_published_manifest_membership_is_immutable(): void
    {
        $this->lesson();
        $v1 = $this->initial();
        $this->expectException(ValidationException::class);
        $v1->lessonMappings->first()->update(['sort_order' => 90]);
    }

    public function test_superseded_metadata_is_immutable(): void
    {
        $v1 = $this->initial();
        $this->approve($this->update('course', $this->course->id, ['title' => 'V2']));
        $this->expectException(ValidationException::class);
        $v1->fresh()->update(['title' => 'Corrupted']);
    }

    private function legacyEnrollment(): Enrollment
    {
        return Enrollment::create(['user_id' => User::factory()->create()->id, 'course_id' => $this->course->id, 'enrolled_at' => now(), 'status' => 'active']);
    }

    public function test_backfill_default_and_explicit_dry_run_do_not_write(): void
    {
        $v1 = $this->initial();
        $a = $this->legacyEnrollment();
        $this->artisan('enrollments:backfill-releases')->expectsOutputToContain('DRY RUN')->assertSuccessful();
        $this->artisan('enrollments:backfill-releases --dry-run')->assertSuccessful();
        $this->assertNull($a->fresh()->course_version_id);
        $this->assertSame($v1->id, app(EnrollmentReleaseBackfillService::class)->inspect($a)['selected_version']);
    }

    public function test_backfill_selects_historical_release_and_is_idempotent(): void
    {
        $v1 = $this->initial();
        $a = $this->legacyEnrollment();
        $this->approve($this->update('course', $this->course->id, ['title' => 'V2']));
        $backfill = app(EnrollmentReleaseBackfillService::class);
        $this->assertSame(1, $backfill->run(true)['pinned']);
        $this->assertSame($v1->id, $a->fresh()->course_version_id);
        $this->assertSame(1, $backfill->run(true)['already_pinned']);
        $this->assertSame($v1->id, $a->fresh()->course_version_id);
    }

    public function test_legacy_enrollment_before_history_and_missing_pointer_stay_unresolved(): void
    {
        $a = $this->legacyEnrollment();
        $backfill = app(EnrollmentReleaseBackfillService::class);
        $this->assertSame('legacy_missing_published_pointer', $backfill->inspect($a)['unresolved_reason']);
        $this->travel(2)->seconds();
        $this->initial();
        $this->assertSame('enrollment_predates_release_history', $backfill->inspect($a)['unresolved_reason']);
        $this->assertSame(1, $backfill->run(true)['unresolved']);
        $this->assertNull($a->fresh()->course_version_id);
    }

    public function test_metadata_only_legacy_snapshot_is_not_a_historical_release(): void
    {
        $version = $this->course->versions()->create(['version_number' => 1, 'status' => 'published', 'title' => 'Legacy', 'published_at' => now()]);
        $this->course->forceFill(['published_version_id' => $version->id])->save();
        $a = $this->legacyEnrollment();
        $this->assertSame('historical_manifest_not_proven', app(EnrollmentReleaseBackfillService::class)->inspect($a)['unresolved_reason']);
        $this->assertNull($a->fresh()->course_version_id);
    }

    private function order(float $amount = 100000): Order
    {
        $order = Order::create(['order_code' => 'REL-'.fake()->uuid(), 'user_id' => User::factory()->create(['role' => 'student'])->id,
            'subtotal' => $amount, 'total_amount' => $amount, 'status' => 'pending', 'payment_method' => 'bank_transfer']);
        $order->items()->create(['course_id' => $this->course->id, 'price' => $amount, 'commission_rate' => 20, 'commission_amount' => $amount * .2, 'instructor_earning' => $amount * .8]);
        Payment::create(['order_id' => $order->id, 'gateway' => 'bank_transfer', 'amount' => $amount, 'status' => 'pending']);

        return $order;
    }

    private function replaceCurrentWithLegacyRelease(): CourseVersion
    {
        $current = $this->course->fresh()->publishedVersion;
        $current->forceFill(['status' => 'superseded', 'superseded_at' => now()])->save();
        $legacy = $this->course->versions()->create([
            ...app(ContentVersionService::class)->courseSnapshot($this->course),
            'version_number' => $current->version_number + 1,
            'status' => 'published',
            'source_version_id' => $current->id,
            'created_by' => $this->admin->id,
            'published_by' => $this->admin->id,
            'published_at' => now(),
        ]);
        $this->course->forceFill(['published_version_id' => $legacy->id])->save();

        return $legacy;
    }

    public function test_payment_link_preflight_creates_forward_only_release_for_legacy_course(): void
    {
        config(['services.payos.mode' => 'live']);
        $this->lesson();
        $this->initial();
        $legacy = $this->replaceCurrentWithLegacyRelease();
        $order = $this->order();
        Http::fake(['api-merchant.payos.vn/*' => Http::response([
            'code' => '00', 'data' => ['checkoutUrl' => 'https://pay.payos.vn/web/release-ready'],
        ])]);

        $url = app(PaymentGatewayService::class)->getPaymentUrl($order);

        $release = $this->course->fresh()->publishedVersion;
        $this->assertSame('https://pay.payos.vn/web/release-ready', $url);
        $this->assertNotSame($legacy->id, $release->id);
        $this->assertSame('superseded', $legacy->fresh()->status);
        $this->assertNotNull($release->manifest_built_at);
        $this->assertSame($legacy->id, $release->source_version_id);
        $this->assertSame(1, $release->lessonMappings()->count());
    }

    public function test_verified_paid_payos_order_recovers_from_local_cancel_and_legacy_release(): void
    {
        $this->lesson();
        $this->initial();
        $legacy = $this->replaceCurrentWithLegacyRelease();
        $order = $this->order(99000);
        $payment = $order->payment;
        $payment->update(['gateway_order_code' => '987654321', 'status' => 'failed']);
        $order->update(['status' => 'cancelled']);
        Http::fake(['api-merchant.payos.vn/*' => Http::response(['code' => '00', 'data' => [
            'orderCode' => 987654321, 'amount' => 99000, 'amountPaid' => 99000, 'status' => 'PAID',
            'transactions' => [['reference' => 'BANK-PAID-987']],
        ]])]);

        $this->actingAs($order->user)
            ->get(route('student.checkout.failed', $order->order_code))
            ->assertRedirect(route('student.checkout.success', $order->order_code));

        $release = $this->course->fresh()->publishedVersion;
        $this->assertSame('paid', $order->fresh()->status);
        $this->assertSame('success', $payment->fresh()->status);
        $this->assertSame('BANK-PAID-987', $order->fresh()->transaction_id);
        $this->assertNotSame($legacy->id, $release->id);
        $this->assertNotNull($release->manifest_built_at);
        $this->assertSame($release->id, Enrollment::where('user_id', $order->user_id)->sole()->course_version_id);
    }

    public function test_repeated_payos_callback_preserves_pin_after_a_new_release(): void
    {
        $v1 = $this->initial();
        $order = $this->order();
        $payments = app(PaymentGatewayService::class);
        $this->assertTrue($payments->completePayOSPayment($order, 'REL-PAYOS'));
        $a = Enrollment::where('user_id', $order->user_id)->sole();
        $this->approve($this->update('course', $this->course->id, ['title' => 'V2']));
        $this->assertTrue($payments->completePayOSPayment($order, 'REL-PAYOS'));
        $this->assertSame($v1->id, $a->fresh()->course_version_id);
        $this->assertSame(1, Enrollment::count());
    }

    public function test_momo_mock_and_free_finalization_each_pin_current_release(): void
    {
        $v1 = $this->initial();
        $payments = app(PaymentGatewayService::class);
        $this->assertTrue($payments->completeMomoPayment($this->order(), 'REL-MOMO'));
        config(['services.payos.mode' => 'mock']);
        $this->assertTrue($payments->processMockPayment($this->order(), 'success', 'REL-MOCK'));
        $this->assertTrue($payments->completeFreeOrder($this->order(0)));
        $this->assertSame([$v1->id, $v1->id, $v1->id], Enrollment::pluck('course_version_id')->all());
    }

    public function test_direct_enroll_route_pins_release(): void
    {
        $this->lesson();
        $v1 = $this->initial();
        $student = User::factory()->create(['role' => 'student', 'email_verified_at' => now()]);
        $this->actingAs($student)->post(route('courses.enroll', $this->course))->assertRedirect();
        $this->assertSame($v1->id, Enrollment::where('user_id', $student->id)->sole()->course_version_id);
    }

    public function test_publication_failure_rolls_back_child_activation_and_pointer(): void
    {
        $lesson = $this->lesson();
        $v1 = $this->initial();
        $update = $this->update('lesson', $lesson->id, ['type' => 'quiz']);
        try {
            $this->approve($update);
            $this->fail('Missing quiz version must reject the entire publication.');
        } catch (ValidationException) {
            $this->assertSame($v1->id, $this->course->fresh()->published_version_id);
            $this->assertSame('document', $lesson->fresh()->type);
            $this->assertSame('pending', $update->fresh()->status);
            $this->assertSame(1, CourseVersion::count());
        }
    }

    public function test_concurrent_enrollment_waits_for_the_mysql_course_publication_lock(): void
    {
        $this->lesson();
        $this->initial();
        $student = User::factory()->create();
        $update = $this->update('course', $this->course->id, ['title' => 'V2']);
        Course::whereKey($this->course->id)->lockForUpdate()->firstOrFail();
        $original = DB::getDefaultConnection();
        config(['database.connections.release_competing' => config('database.connections.'.$original)]);
        try {
            DB::connection('release_competing')->statement('SET SESSION innodb_lock_wait_timeout = 1');
            DB::setDefaultConnection('release_competing');
            try {
                app(EnrollmentVersionService::class)->firstOrCreate($this->course, $student->id);
                $this->fail('A competing enrollment must wait for the course lock.');
            } catch (QueryException $exception) {
                $this->assertSame(1205, $exception->errorInfo[1]);
            }
        } finally {
            DB::setDefaultConnection($original);
            DB::purge('release_competing');
        }
        $this->assertDatabaseCount('enrollments', 0);
        $v2 = $this->approve($update);
        $a = $this->enroll($student);
        $this->assertSame($v2->id, $a->course_version_id);
        $this->assertNotNull($a->courseVersion->manifest_built_at);
        $this->assertSame(1, $a->courseVersion->lessonMappings()->count());
    }

    public function test_cancelled_enrollment_reactivation_keeps_its_original_pin(): void
    {
        $v1 = $this->initial();
        $order = $this->order();
        $a = $this->enroll($order->user);
        $a->update(['status' => 'cancelled']);
        $this->approve($this->update('course', $this->course->id, ['title' => 'V2']));
        $this->assertTrue(app(PaymentGatewayService::class)->completePayOSPayment($order, 'REACTIVATE'));
        $this->assertSame('active', $a->fresh()->status);
        $this->assertSame($v1->id, $a->fresh()->course_version_id);
    }

    public function test_backfill_reports_conflicting_submission_pins_without_rewriting_them(): void
    {
        $lesson = $this->lesson('Assignment', 1, 'assignment');
        $assignment = Assignment::create(['course_id' => $this->course->id, 'lesson_id' => $lesson->id, 'title' => 'A1', 'description' => 'Instructions']);
        $this->initial();
        $a = $this->legacyEnrollment();
        $v2 = $this->approve($this->update('assignment', $assignment->id, ['passing_score' => 90]));
        $pin = $v2->lessonMappings->first()->assignment_version_id;
        $submission = Submission::create(['assignment_id' => $assignment->id, 'assignment_version_id' => $pin,
            'user_id' => $a->user_id, 'content' => 'Historical answer', 'status' => 'submitted', 'submitted_at' => now()]);
        $backfill = app(EnrollmentReleaseBackfillService::class);
        $this->assertSame('assessment_history_conflicts_with_release', $backfill->inspect($a)['unresolved_reason']);
        $this->assertSame(1, $backfill->run(true)['unresolved']);
        $this->assertNull($a->fresh()->course_version_id);
        $this->assertSame($pin, $submission->fresh()->assignment_version_id);
    }

    public function test_section_deletion_preserves_the_old_section_membership(): void
    {
        $lesson = $this->lesson();
        $v1 = $this->initial();
        $v2 = $this->approve($this->update('chapter', $this->section->id, [], 'delete'));
        $this->assertSame(0, $v2->sectionMappings()->count());
        $this->assertSame(0, $v2->lessonMappings()->count());
        $this->assertSame($this->section->id, $v1->sectionMappings->first()->section->id);
        $this->assertSame($lesson->id, $v1->lessonMappings->first()->lesson->id);
    }

    public function test_new_quiz_lesson_publishes_the_frozen_quiz_candidate_into_only_the_new_release(): void
    {
        $v1 = $this->initial();
        $lesson = $this->lesson('New quiz', 1, 'quiz');
        $lesson->update(['status' => 'draft']);
        $quiz = Quiz::create(['lesson_id' => $lesson->id, 'title' => 'New quiz']);
        $draft = $quiz->versions()->create(['version' => 1, 'status' => 'draft', 'title' => 'Frozen quiz']);
        $quiz->update(['current_draft_version_id' => $draft->id]);
        $update = $this->update('lesson', $lesson->id, ['type' => 'quiz', 'title' => 'New quiz', 'section_id' => $this->section->id, 'status' => 'published'], 'create');
        $this->assertSame($draft->id, $update->fresh()->payload['quiz_version_id']);
        try {
            app(QuizVersioningService::class)->assertDraftEditable($quiz->fresh(), $draft);
            $this->fail('The quiz for a pending new lesson must be frozen.');
        } catch (ValidationException) {
            $this->assertSame('draft', $draft->fresh()->status);
        }
        $this->mock(QuizContentService::class)->shouldReceive('validateQuizVersion')->andReturn(['is_complete' => true, 'errors' => []]);
        $v2 = $this->approve($update);
        $this->assertCount(0, $v1->lessonMappings);
        $this->assertSame($draft->id, $v2->lessonMappings->first()->quiz_version_id);
        $this->assertSame('published', $draft->fresh()->status);
    }

    public function test_metadata_arrays_and_child_content_are_immutable_snapshots(): void
    {
        $this->course->update(['tags' => ['php', 'laravel']]);
        $this->lesson();
        $v1 = $this->initial();
        $v2 = $this->approve($this->update('course', $this->course->id, ['title' => 'V2']));
        $this->assertSame(['php', 'laravel'], $v1->tags);
        $this->assertSame($v1->tags, $v2->tags);
        $this->expectException(ValidationException::class);
        $v1->lessonMappings->first()->lessonVersion->update(['content' => 'Overwrite']);
    }
}
