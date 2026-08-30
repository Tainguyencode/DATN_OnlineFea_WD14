<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use App\Services\RoleSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InstructorRegistrationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private const PASSWORD = 'Password1!';

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Storage::fake('public');
        app(RoleSyncService::class)->ensurePrimaryRolesExist();
    }

    /**
     * CASE 1: Instructor đăng ký -> verify email -> vào Dashboard được ngay.
     */
    public function test_case_1_verified_instructor_can_access_dashboard_immediately(): void
    {
        $user = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'pending',
            'account_status' => 'active',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('instructor.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Giảng viên');
    }

    /**
     * CASE 2: Instructor chưa approved (pending) -> tạo khóa học được.
     */
    public function test_case_2_pending_instructor_can_create_course(): void
    {
        $user = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'pending',
            'account_status' => 'active',
            'email_verified_at' => now(),
        ]);

        $category = Category::create([
            'name' => 'Lập trình',
            'slug' => 'lap-trinh',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)
            ->post(route('instructor.courses.store'), [
                'title' => 'Khóa học Laravel 12 Pro',
                'category_id' => $category->id,
                'level' => 'beginner',
                'price' => 299000,
                'short_description' => 'Mô tả ngắn khóa học',
                'description' => 'Mô tả chi tiết khóa học',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('courses', [
            'instructor_id' => $user->id,
            'title' => 'Khóa học Laravel 12 Pro',
        ]);
    }

    /**
     * CASE 3: Instructor chưa approved -> truy cập curriculum / quản lý bài học được.
     */
    public function test_case_3_pending_instructor_can_manage_curriculum(): void
    {
        $user = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'pending',
            'account_status' => 'active',
            'email_verified_at' => now(),
        ]);

        $course = Course::create([
            'instructor_id' => $user->id,
            'title' => 'Khóa học Node.js Master',
            'slug' => 'khoa-hoc-nodejs-master',
            'status' => 'draft',
            'price' => 0,
            'level' => 'beginner',
        ]);

        $this->actingAs($user)
            ->get(route('instructor.courses.curriculum', $course))
            ->assertOk()
            ->assertSee('Khóa học Node.js Master');
    }

    /**
     * CASE 4: Instructor vào Hồ sơ & Chứng chỉ -> upload nhiều certificate.
     */
    public function test_case_4_instructor_can_upload_multiple_certificates(): void
    {
        $user = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'pending',
            'account_status' => 'active',
            'email_verified_at' => now(),
        ]);

        $files = [
            UploadedFile::fake()->create('cert_laravel.pdf', 500, 'application/pdf'),
            UploadedFile::fake()->image('cert_aws.jpg'),
        ];

        $this->actingAs($user)
            ->post(route('instructor.profile.documents.upload'), [
                'document_type' => 'certificate',
                'files' => $files,
            ])
            ->assertRedirect();

        $this->assertSame(2, $user->instructorCertificates()->count());
    }

    /**
     * CASE 5: Upload thêm certificate -> file cũ vẫn còn (không ghi đè).
     */
    public function test_case_5_upload_new_certificate_preserves_old_ones(): void
    {
        $user = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'pending',
            'account_status' => 'active',
            'email_verified_at' => now(),
        ]);

        $file1 = UploadedFile::fake()->create('cert_1.pdf', 300, 'application/pdf');
        $this->actingAs($user)->post(route('instructor.profile.documents.upload'), [
            'document_type' => 'certificate',
            'file' => $file1,
            'title' => 'Chứng chỉ 1',
        ]);

        $file2 = UploadedFile::fake()->create('cert_2.pdf', 400, 'application/pdf');
        $this->actingAs($user)->post(route('instructor.profile.documents.upload'), [
            'document_type' => 'certificate',
            'file' => $file2,
            'title' => 'Chứng chỉ 2',
        ]);

        $this->assertSame(2, $user->instructorCertificates()->count());
        $titles = $user->instructorCertificates()->pluck('title')->all();
        $this->assertContains('Chứng chỉ 1', $titles);
        $this->assertContains('Chứng chỉ 2', $titles);
    }

    /**
     * CASE 6: Upload hợp đồng lao động -> document_type = employment_contract.
     */
    public function test_case_6_upload_employment_contract(): void
    {
        $user = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'pending',
            'account_status' => 'active',
            'email_verified_at' => now(),
        ]);

        $file = UploadedFile::fake()->create('contract_fpt.pdf', 400, 'application/pdf');
        $this->actingAs($user)->post(route('instructor.profile.documents.upload'), [
            'document_type' => 'employment_contract',
            'file' => $file,
            'title' => 'Hợp đồng FPT',
        ]);

        $doc = $user->instructorCertificates()->first();
        $this->assertNotNull($doc);
        $this->assertSame('employment_contract', $doc->document_type);
        $this->assertSame('Hợp đồng lao động', $doc->documentTypeLabel());
    }

    /**
     * CASE 7: Upload bảng điểm -> document_type = transcript.
     */
    public function test_case_7_upload_transcript(): void
    {
        $user = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'pending',
            'account_status' => 'active',
            'email_verified_at' => now(),
        ]);

        $file = UploadedFile::fake()->create('transcript_bk.pdf', 400, 'application/pdf');
        $this->actingAs($user)->post(route('instructor.profile.documents.upload'), [
            'document_type' => 'transcript',
            'file' => $file,
            'title' => 'Bảng điểm Đại học',
        ]);

        $doc = $user->instructorCertificates()->first();
        $this->assertNotNull($doc);
        $this->assertSame('transcript', $doc->document_type);
        $this->assertSame('Bảng điểm', $doc->documentTypeLabel());
    }

    /**
     * CASE 8: Admin approve hồ sơ -> Instructor vẫn sử dụng Dashboard bình thường.
     */
    public function test_case_8_admin_approves_instructor_retains_dashboard_access(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'pending',
            'account_status' => 'active',
            'email_verified_at' => now(),
        ]);
        $category = Category::create(['name' => 'Lập trình', 'slug' => 'lap-trinh-c8', 'status' => true]);
        $profile = $instructor->instructorProfile()->create(['category_id' => $category->id]);
        $profile->teachingCategories()->attach($category->id, ['is_primary' => true]);

        $this->actingAs($admin)
            ->post(route('admin.instructors.applications.approve', $instructor))
            ->assertRedirect();

        $this->assertSame('approved', $instructor->fresh()->instructor_status);

        $this->actingAs($instructor->fresh())
            ->get(route('instructor.dashboard'))
            ->assertOk();
    }

    /**
     * CASE 9: Dashboard hiển thị cảnh báo cần cập nhật hồ sơ sau 7 ngày khi pending.
     */
    public function test_case_9_dashboard_shows_pending_warning_banner(): void
    {
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'pending',
            'account_status' => 'active',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($instructor)
            ->get(route('instructor.dashboard'))
            ->assertOk()
            ->assertSee('Hồ sơ giảng viên đang chờ xét duyệt.');
    }

    /**
     * CASE 10: Quá 7 ngày chưa cập nhật -> account bị locked -> không vào Dashboard -> không xóa tài khoản.
     */
    public function test_case_10_overdue_7_days_locks_account_without_deletion(): void
    {
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'pending',
            'account_status' => 'active',
            'email_verified_at' => now()->subDays(8),
            'submitted_for_review_at' => null,
        ]);

        // Run scheduler command
        Artisan::call('instructors:check-profile-deadlines');

        $fresh = $instructor->fresh();
        $this->assertSame('locked', $fresh->account_status);
        $this->assertSame('instructor', $fresh->role);
        $this->assertNotNull($fresh->locked_at);

        // Cannot access dashboard -> redirects to profile
        $this->actingAs($fresh)
            ->get(route('instructor.dashboard'))
            ->assertRedirect(route('instructor.profile'));
    }

    /**
     * CASE 11: Locked user vào Profile -> vẫn được xem và bổ sung hồ sơ.
     */
    public function test_case_11_locked_instructor_can_access_profile_and_add_documents(): void
    {
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'pending',
            'account_status' => 'locked',
            'locked_at' => now()->subDays(2),
            'locked_reason' => 'Chưa hoàn thiện hồ sơ 7 ngày',
            'email_verified_at' => now()->subDays(9),
        ]);

        $this->actingAs($instructor)
            ->get(route('instructor.profile'))
            ->assertOk()
            ->assertSee('Tài khoản giảng viên đang bị tạm khóa');

        $file = UploadedFile::fake()->create('degree_supplement.pdf', 300, 'application/pdf');
        $this->actingAs($instructor)
            ->post(route('instructor.profile.documents.upload'), [
                'document_type' => 'degree',
                'file' => $file,
                'title' => 'Bằng đại học bổ sung',
            ])
            ->assertRedirect();

        $this->assertSame(1, $instructor->instructorCertificates()->count());
    }

    /**
     * CASE 12: Chưa đủ 10-15 ngày (ví dụ 3 ngày) -> không được gửi yêu cầu cấp lại.
     */
    public function test_case_12_reactivation_blocked_before_cooldown(): void
    {
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'pending',
            'account_status' => 'locked',
            'locked_at' => now()->subDays(3),
            'email_verified_at' => now()->subDays(10),
        ]);

        $this->assertFalse($instructor->canRequestReactivation());
        $this->assertSame(11, $instructor->reactivationCooldownDaysRemaining());

        $this->actingAs($instructor)
            ->post(route('instructor.profile.request-reactivation'), [
                'reason' => 'Tôi đã bổ sung đầy đủ tài liệu, xin mở lại quyền.',
            ])
            ->assertSessionHas('error');
    }

    /**
     * CASE 13: Đủ cooldown 14 ngày -> có thể gửi yêu cầu cấp lại quyền.
     */
    public function test_case_13_reactivation_allowed_after_cooldown(): void
    {
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'pending',
            'account_status' => 'locked',
            'locked_at' => now()->subDays(15),
            'email_verified_at' => now()->subDays(22),
        ]);

        $this->assertTrue($instructor->canRequestReactivation());

        $this->actingAs($instructor)
            ->post(route('instructor.profile.request-reactivation'), [
                'reason' => 'Tôi đã bổ sung đầy đủ bằng cấp và hợp đồng lao động mới.',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $fresh = $instructor->fresh();
        $this->assertSame('pending', $fresh->reactivation_status);
        $this->assertNotNull($fresh->reactivation_requested_at);
    }

    /**
     * CASE 14: Admin approve cấp lại -> mở lại Dashboard.
     */
    public function test_case_14_admin_approves_reactivation_unlocks_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'pending',
            'account_status' => 'locked',
            'locked_at' => now()->subDays(15),
            'reactivation_status' => 'pending',
            'reactivation_requested_at' => now(),
            'reactivation_reason' => 'Xin cấp lại quyền',
            'email_verified_at' => now()->subDays(22),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.instructors.applications.reactivation.approve', $instructor))
            ->assertRedirect();

        $fresh = $instructor->fresh();
        $this->assertSame('active', $fresh->account_status);
        $this->assertNull($fresh->locked_at);

        // Can access dashboard again
        $this->actingAs($fresh)
            ->get(route('instructor.dashboard'))
            ->assertOk();
    }

    /**
     * CASE 15: Admin reject cấp lại -> vẫn locked.
     */
    public function test_case_15_admin_rejects_reactivation_remains_locked(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'pending',
            'account_status' => 'locked',
            'locked_at' => now()->subDays(15),
            'reactivation_status' => 'pending',
            'reactivation_requested_at' => now(),
            'email_verified_at' => now()->subDays(22),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.instructors.applications.reactivation.reject', $instructor), [
                'notes' => 'Tài liệu bổ sung vẫn chưa hợp lệ.',
            ])
            ->assertRedirect();

        $fresh = $instructor->fresh();
        $this->assertSame('locked', $fresh->account_status);
        $this->assertSame('rejected', $fresh->reactivation_status);

        // Still cannot access dashboard
        $this->actingAs($fresh)
            ->get(route('instructor.dashboard'))
            ->assertRedirect(route('instructor.profile'));
    }

    /**
     * CASE 16: Instructor đã approved từ trước -> không bị ảnh hưởng.
     */
    public function test_case_16_already_approved_instructor_unaffected(): void
    {
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'approved',
            'account_status' => 'active',
            'email_verified_at' => now()->subMonths(3),
        ]);

        $this->actingAs($instructor)
            ->get(route('instructor.dashboard'))
            ->assertOk();

        $this->actingAs($instructor)
            ->get(route('instructor.profile'))
            ->assertOk();
    }

    /**
     * CASE 17: Admin sees Pending count & New Updates in index
     */
    public function test_case_17_admin_sees_pending_and_new_update_counts(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);

        User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'pending',
            'needs_admin_review' => true,
            'email_verified_at' => now(),
        ]);

        User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'pending',
            'needs_admin_review' => false,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.instructors.applications.index'))
            ->assertOk()
            ->assertSee('Chờ duyệt')
            ->assertSee('Cập nhật mới');
    }

    /**
     * CASE 18: Instructor updates profile / uploads document -> sets needs_admin_review = true, Admin views -> sets to false
     */
    public function test_case_18_instructor_update_marks_needs_admin_review_and_admin_view_clears_it(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'pending',
            'needs_admin_review' => false,
            'email_verified_at' => now(),
        ]);

        // Instructor uploads document
        $file = UploadedFile::fake()->create('degree.pdf', 300, 'application/pdf');
        $this->actingAs($instructor)
            ->post(route('instructor.profile.documents.upload'), [
                'document_type' => 'degree',
                'file' => $file,
            ])
            ->assertRedirect();

        $this->assertTrue($instructor->fresh()->needs_admin_review);

        // Admin views the detail page
        $this->actingAs($admin)
            ->get(route('admin.instructors.applications.show', $instructor))
            ->assertOk()
            ->assertSee('Cập nhật mới');

        $this->assertFalse($instructor->fresh()->needs_admin_review);
        $this->assertNotNull($instructor->fresh()->admin_last_reviewed_at);
    }

    /**
     * CASE 19: Test unified filters: status, date_from, date_to, category_id
     */
    public function test_case_19_unified_instructor_applications_filter(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);

        $catWeb = Category::create(['name' => 'Web Development', 'slug' => 'web-dev', 'status' => true]);
        $catMobile = Category::create(['name' => 'Mobile Development', 'slug' => 'mobile-dev', 'status' => true]);

        $instPending = User::factory()->create([
            'name' => 'Pending Instructor',
            'role' => 'instructor',
            'instructor_status' => 'pending',
            'created_at' => '2026-01-10 10:00:00',
            'email_verified_at' => now(),
        ]);
        $instPending->instructorProfile()->create([
            'category_id' => $catWeb->id,
            'specialty' => 'Laravel Expert',
        ]);

        $instApproved = User::factory()->create([
            'name' => 'Approved Instructor',
            'role' => 'instructor',
            'instructor_status' => 'approved',
            'created_at' => '2026-02-15 10:00:00',
            'email_verified_at' => now(),
        ]);
        $instApproved->instructorProfile()->create([
            'category_id' => $catMobile->id,
            'specialty' => 'Flutter Developer',
        ]);

        // Filter status=pending
        $res = $this->actingAs($admin)->get(route('admin.instructors.applications.index', ['status' => 'pending']));
        $res->assertOk();
        $res->assertSee('Pending Instructor');
        $res->assertDontSee('Approved Instructor');

        // Filter status=approved
        $res = $this->actingAs($admin)->get(route('admin.instructors.applications.index', ['status' => 'approved']));
        $res->assertOk();
        $res->assertSee('Approved Instructor');
        $res->assertDontSee('Pending Instructor');

        // Filter date_from = 2026-02-01
        $res = $this->actingAs($admin)->get(route('admin.instructors.applications.index', ['date_from' => '2026-02-01']));
        $res->assertOk();
        $res->assertSee('Approved Instructor');
        $res->assertDontSee('Pending Instructor');

        // Filter date_to = 2026-01-31
        $res = $this->actingAs($admin)->get(route('admin.instructors.applications.index', ['date_to' => '2026-01-31']));
        $res->assertOk();
        $res->assertSee('Pending Instructor');
        $res->assertDontSee('Approved Instructor');

        // Filter category_id = catWeb
        $res = $this->actingAs($admin)->get(route('admin.instructors.applications.index', ['category_id' => $catWeb->id]));
        $res->assertOk();
        $res->assertSee('Pending Instructor');
        $res->assertDontSee('Approved Instructor');

        // Filter combined
        $res = $this->actingAs($admin)->get(route('admin.instructors.applications.index', [
            'status' => 'pending',
            'date_from' => '2026-01-01',
            'date_to' => '2026-01-31',
            'category_id' => $catWeb->id,
        ]));
        $res->assertOk();
        $res->assertSee('Pending Instructor');
        $res->assertDontSee('Approved Instructor');
    }
}
