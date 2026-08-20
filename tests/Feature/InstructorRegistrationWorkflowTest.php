<?php

namespace Tests\Feature;

use App\Models\InstructorCertificate;
use App\Models\Role;
use App\Models\User;
use App\Services\CaptchaService;
use App\Services\RoleSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
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
     * CASE 1: Đăng ký Instructor không upload certificate -> đăng ký thành công
     */
    public function test_case_1_register_instructor_without_certificate_succeeds(): void
    {
        $captcha = $this->generateCaptcha('register');

        $response = $this->post(route('register.role', 'instructor'), [
            'name' => 'Giảng viên Không Cert',
            'email' => 'instructor_nocert@example.com',
            'phone' => '0912345671',
            'password' => self::PASSWORD,
            'password_confirmation' => self::PASSWORD,
            'specialty' => 'Lập trình Laravel',
            'experience' => '3 năm kinh nghiệm',
            'bio' => 'Giảng viên nhiệt tình',
            'agree_information' => '1',
            'agree_terms' => '1',
            'captcha_token' => $captcha['token'],
            'captcha_answer' => $captcha['answer'],
        ]);

        $response->assertRedirect(route('verification.notice'));
        $this->assertAuthenticated();

        $user = User::where('email', 'instructor_nocert@example.com')->firstOrFail();
        $this->assertSame('instructor', $user->role);
        $this->assertSame('pending', $user->instructor_status);
        $this->assertNull($user->submitted_for_review_at);
        $this->assertSame(0, $user->instructorCertificates()->count());
    }

    /**
     * CASE 2: Verify email -> đăng nhập được, vào được /instructor/pending, KHÔNG vào được Dashboard
     */
    public function test_case_2_verified_instructor_can_access_pending_but_not_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'pending',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('instructor.pending'))
            ->assertOk()
            ->assertSee('Hoàn thiện hồ sơ Giảng viên');

        $this->actingAs($user)
            ->get(route('instructor.dashboard'))
            ->assertRedirect(route('instructor.pending'));
    }

    /**
     * CASE 3: Chưa có certificate -> cảnh báo deadline 7 ngày trên /instructor/pending
     */
    public function test_case_3_no_certificate_shows_7_day_deadline_warning(): void
    {
        $user = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'pending',
            'email_verified_at' => now()->subDays(2),
        ]);

        $this->actingAs($user)
            ->get(route('instructor.pending'))
            ->assertOk()
            ->assertSee('Hồ sơ chứng chỉ chưa hoàn thiện')
            ->assertSee('còn 5 ngày');
    }

    /**
     * CASE 4 & 5: Upload certificate_1 rồi certificate_2 -> 2 record riêng, không bị ghi đè
     */
    public function test_case_4_and_5_upload_multiple_certificates_sequentially(): void
    {
        $user = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'pending',
            'email_verified_at' => now(),
        ]);

        // Upload certificate 1
        $file1 = UploadedFile::fake()->create('certificate_python.pdf', 500, 'application/pdf');
        $this->actingAs($user)
            ->post(route('instructor.certificates.upload'), [
                'file' => $file1,
                'title' => 'Chứng chỉ Python',
            ])
            ->assertRedirect(route('instructor.pending'));

        $this->assertSame(1, $user->instructorCertificates()->count());
        $cert1 = $user->instructorCertificates()->first();
        $this->assertSame('certificate_python.pdf', $cert1->original_name);

        // Upload certificate 2
        $file2 = UploadedFile::fake()->create('certificate_aws.pdf', 600, 'application/pdf');
        $this->actingAs($user)
            ->post(route('instructor.certificates.upload'), [
                'file' => $file2,
                'title' => 'Chứng chỉ AWS',
            ])
            ->assertRedirect(route('instructor.pending'));

        $this->assertSame(2, $user->instructorCertificates()->count());

        // Check both records exist
        $names = $user->instructorCertificates()->pluck('original_name')->all();
        $this->assertContains('certificate_python.pdf', $names);
        $this->assertContains('certificate_aws.pdf', $names);
    }

    /**
     * CASE 6: Upload 3 file cùng lúc -> 3 record riêng
     */
    public function test_case_6_upload_batch_files_creates_separate_records(): void
    {
        $user = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'pending',
            'email_verified_at' => now(),
        ]);

        $files = [
            UploadedFile::fake()->create('cert_1.pdf', 200, 'application/pdf'),
            UploadedFile::fake()->image('cert_2.jpg'),
            UploadedFile::fake()->image('cert_3.png'),
        ];

        $this->actingAs($user)
            ->post(route('instructor.certificates.upload'), [
                'files' => $files,
            ])
            ->assertRedirect(route('instructor.pending'));

        $this->assertSame(3, $user->instructorCertificates()->count());
    }

    /**
     * CASE 7: Gửi hồ sơ xét duyệt:
     * - Khi chưa có cert -> bị từ chối
     * - Khi có cert -> status = pending, submitted_for_review_at được ghi nhận
     */
    public function test_case_7_submit_application_for_review_requires_at_least_one_certificate(): void
    {
        $user = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'pending',
            'email_verified_at' => now(),
            'submitted_for_review_at' => null,
        ]);

        // Submit without certificate -> fail
        $this->actingAs($user)
            ->post(route('instructor.submit-review'))
            ->assertSessionHas('error');

        $this->assertNull($user->fresh()->submitted_for_review_at);

        // Add 1 certificate
        $file = UploadedFile::fake()->create('degree.pdf', 300, 'application/pdf');
        $this->actingAs($user)
            ->post(route('instructor.certificates.upload'), ['file' => $file]);

        // Submit with certificate -> success
        $this->actingAs($user)
            ->post(route('instructor.submit-review'))
            ->assertRedirect(route('instructor.pending'))
            ->assertSessionHas('success');

        $this->assertNotNull($user->fresh()->submitted_for_review_at);
    }

    /**
     * CASE 8: Admin Review -> thấy toàn bộ certificate và có thể xem
     */
    public function test_case_8_admin_can_view_all_certificates(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $instructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'pending',
            'email_verified_at' => now(),
            'submitted_for_review_at' => now(),
        ]);

        $cert = InstructorCertificate::create([
            'user_id' => $instructor->id,
            'file_path' => "instructor-certificates/{$instructor->id}/sample.pdf",
            'original_name' => 'sample.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'title' => 'Chứng chỉ mẫu',
            'status' => 'pending',
            'uploaded_at' => now(),
        ]);

        Storage::disk('local')->put($cert->file_path, 'sample pdf content');

        $this->actingAs($admin)
            ->get(route('admin.instructors.applications.show', $instructor))
            ->assertOk()
            ->assertSee('sample.pdf')
            ->assertSee('Chứng chỉ mẫu');

        $this->actingAs($admin)
            ->get(route('admin.instructors.applications.certificates.view', $cert))
            ->assertOk();
    }

    /**
     * CASE 9: Admin Reject -> không vào Dashboard, thấy rejection reason, có thể upload thêm và gửi lại
     */
    public function test_case_9_admin_reject_flow(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'pending',
            'email_verified_at' => now(),
            'submitted_for_review_at' => now(),
        ]);

        // Admin rejects
        $this->actingAs($admin)
            ->post(route('admin.instructors.applications.reject', $instructor), [
                'rejected_reason' => 'Chứng chỉ mờ không đọc được thông tin.',
            ])
            ->assertRedirect(route('admin.instructors.applications.index'));

        $this->assertSame('rejected', $instructor->fresh()->instructor_status);

        // Instructor visits pending page
        $this->actingAs($instructor->fresh())
            ->get(route('instructor.pending'))
            ->assertOk()
            ->assertSee('Chứng chỉ mờ không đọc được thông tin.');

        // Instructor cannot access dashboard
        $this->actingAs($instructor->fresh())
            ->get(route('instructor.dashboard'))
            ->assertRedirect(route('instructor.pending'));

        // Instructor resubmits with new certificate
        $newCert = UploadedFile::fake()->create('new_cert.pdf', 300, 'application/pdf');
        $this->actingAs($instructor->fresh())
            ->post(route('instructor.resubmit'), [
                'phone' => '0988776655',
                'specialty' => 'Fullstack Dev',
                'experience' => '4 years',
                'bio' => 'Updated bio',
                'certificate' => $newCert,
                'agree_information' => '1',
                'agree_terms' => '1',
            ])
            ->assertRedirect(route('instructor.pending'));

        $this->assertSame('pending', $instructor->fresh()->instructor_status);
        $this->assertNull($instructor->fresh()->rejected_reason);
    }

    /**
     * CASE 10: Admin Approve -> Instructor vào Dashboard đầy đủ
     */
    public function test_case_10_admin_approve_flow(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'pending',
            'email_verified_at' => now(),
            'submitted_for_review_at' => now(),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.instructors.applications.approve', $instructor))
            ->assertRedirect(route('admin.instructors.applications.index'));

        $this->assertSame('approved', $instructor->fresh()->instructor_status);

        // Instructor can now access Dashboard
        $this->actingAs($instructor->fresh())
            ->get(route('instructor.dashboard'))
            ->assertOk();
    }

    /**
     * CASE 11: Instructor đã approved trước đây không bị ảnh hưởng
     */
    public function test_case_11_already_approved_instructor_unaffected(): void
    {
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'approved',
            'email_verified_at' => now()->subMonths(2),
        ]);

        $this->actingAs($instructor)
            ->get(route('instructor.dashboard'))
            ->assertOk();

        $this->actingAs($instructor)
            ->get(route('instructor.pending'))
            ->assertRedirect(route('instructor.dashboard'));
    }

    /**
     * CASE 12: Quá 7 ngày chưa hoàn thiện -> giáng xuống Học viên, không cho upload nữa
     */
    public function test_case_12_deadline_expired_demotes_to_student(): void
    {
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'pending',
            'email_verified_at' => now()->subDays(8), // > 7 days ago
            'submitted_for_review_at' => null, // not submitted
        ]);

        // When accessing pending page, user is automatically demoted to student
        $this->actingAs($instructor)
            ->get(route('instructor.pending'))
            ->assertRedirect(route('student.dashboard'));

        $freshUser = $instructor->fresh();
        $this->assertSame('student', $freshUser->role);
        $this->assertSame('expired', $freshUser->instructor_status);

        // Cannot upload certificate anymore (blocked by role middleware)
        $file = UploadedFile::fake()->create('late_cert.pdf', 300, 'application/pdf');
        $this->actingAs($freshUser)
            ->post(route('instructor.certificates.upload'), ['file' => $file])
            ->assertRedirect(route('student.dashboard'))
            ->assertSessionHas('error');
    }

    private function generateCaptcha(string $action): array
    {
        $this->startSession();
        $generated = CaptchaService::generate($action);
        $captchas = session('auth_captchas', []);

        return [
            'token' => $generated['token'],
            'answer' => $captchas[$generated['token']]['answer'] ?? '0',
        ];
    }
}
