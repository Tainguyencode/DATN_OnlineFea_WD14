<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\InstructorApplication;
use App\Models\InstructorCertificate;
use App\Models\InstructorDocumentRequirement;
use App\Models\InstructorProfile;
use App\Models\User;
use App\Services\InstructorRequirementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class InstructorDocumentRequirementWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private string $localStorageRoot;

    private string $publicStorageRoot;

    protected User $admin;

    protected User $instructor;

    protected Category $categoryWeb;

    protected Category $categoryMarketing;

    protected InstructorDocumentRequirement $reqDegree;

    protected InstructorDocumentRequirement $reqCert;

    protected function setUp(): void
    {
        parent::setUp();
        $this->localStorageRoot = storage_path('framework/testing/instructor-document-requirements/local/'.Str::uuid());
        $this->publicStorageRoot = storage_path('framework/testing/instructor-document-requirements/public/'.Str::uuid());
        config([
            'filesystems.disks.local.root' => $this->localStorageRoot,
            'filesystems.disks.public.root' => $this->publicStorageRoot,
        ]);
        Storage::forgetDisk('local');
        Storage::forgetDisk('public');

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->categoryWeb = Category::create([
            'name' => 'Lập trình & Phát triển Web',
            'slug' => 'lap-trinh-web',
            'status' => true,
        ]);

        $this->categoryMarketing = Category::create([
            'name' => 'Marketing & Truyền thông',
            'slug' => 'marketing-truyen-thong',
            'status' => true,
        ]);

        // Yêu cầu cho ngành Lập trình Web
        $this->reqDegree = InstructorDocumentRequirement::create([
            'category_id' => $this->categoryWeb->id,
            'document_type' => 'degree',
            'document_title' => 'Bằng tốt nghiệp CNTT',
            'description' => 'Bằng cử nhân hoặc kỹ sư CNTT',
            'is_required' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->reqCert = InstructorDocumentRequirement::create([
            'category_id' => $this->categoryWeb->id,
            'document_type' => 'certificate',
            'document_title' => 'Chứng chỉ Lập trình',
            'description' => 'Chứng chỉ AWS, Google, v.v.',
            'is_required' => true,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        // Giảng viên đăng ký ngành Lập trình Web
        $this->instructor = User::factory()->create([
            'username' => 'instructor_test',
            'phone' => '0987654321',
            'role' => 'instructor',
            'instructor_status' => 'pending',
            'email_verified_at' => now(),
        ]);

        InstructorProfile::create([
            'user_id' => $this->instructor->id,
            'category_id' => $this->categoryWeb->id,
            'teaching_field' => $this->categoryWeb->name,
            'specialty' => 'Laravel & Vue.js',
            'experience' => '5 năm kinh nghiệm giảng dạy Web',
            'bio' => 'Senior Web Developer',
            'phone' => '0987654321',
            'agree_information' => true,
            'agree_terms' => true,
        ]);
    }

    protected function tearDown(): void
    {
        Storage::forgetDisk('local');
        Storage::forgetDisk('public');
        File::deleteDirectory(dirname($this->localStorageRoot));
        File::deleteDirectory(dirname($this->publicStorageRoot));

        parent::tearDown();
    }

    /**
     * 1. Admin có thể xem trang cấu hình yêu cầu hồ sơ theo ngành.
     */
    public function test_admin_can_view_document_requirements_configuration_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.instructors.requirements.index', [
            'category_id' => $this->categoryWeb->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Lập trình & Phát triển Web');
        $response->assertSee('Bằng tốt nghiệp CNTT');
        $response->assertSee('Chứng chỉ Lập trình');
    }

    /**
     * 2. Admin có thể tạo yêu cầu hồ sơ mới cho ngành.
     */
    public function test_admin_can_create_document_requirement_for_category(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.instructors.requirements.store'), [
            'category_id' => $this->categoryWeb->id,
            'document_type' => 'employment_confirmation',
            'document_title' => 'Giấy xác nhận công tác tại công ty phần mềm',
            'description' => 'Tối thiểu 2 năm kinh nghiệm',
            'is_required' => 1,
            'sort_order' => 3,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('instructor_document_requirements', [
            'category_id' => $this->categoryWeb->id,
            'document_title' => 'Giấy xác nhận công tác tại công ty phần mềm',
            'is_required' => true,
        ]);
    }

    /**
     * 3. Admin có thể cập nhật và bật/tắt yêu cầu hồ sơ.
     */
    public function test_admin_can_update_and_toggle_status_of_requirement(): void
    {
        // Update
        $response = $this->actingAs($this->admin)->put(route('admin.instructors.requirements.update', $this->reqDegree), [
            'document_type' => 'degree',
            'document_title' => 'Bằng Đại học / Cao đẳng CNTT (Cập nhật)',
            'description' => 'Mô tả mới',
            'is_required' => 1,
            'is_active' => 1,
            'sort_order' => 1,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('instructor_document_requirements', [
            'id' => $this->reqDegree->id,
            'document_title' => 'Bằng Đại học / Cao đẳng CNTT (Cập nhật)',
        ]);

        // Toggle Status
        $toggleResponse = $this->actingAs($this->admin)->post(route('admin.instructors.requirements.toggle-status', $this->reqDegree));
        $toggleResponse->assertRedirect();
        $this->assertDatabaseHas('instructor_document_requirements', [
            'id' => $this->reqDegree->id,
            'is_active' => false,
        ]);
    }

    /**
     * 4. Trang hồ sơ giảng viên hiển thị danh mục yêu cầu hồ sơ đúng theo ngành đã đăng ký.
     */
    public function test_instructor_profile_displays_field_requirements_checklist(): void
    {
        $response = $this->actingAs($this->instructor)->get(route('instructor.profile'));

        $response->assertStatus(200);
        $response->assertSee('Hồ sơ minh chứng theo ngành giảng dạy');
        $response->assertSee('Bằng tốt nghiệp CNTT');
        $response->assertSee('Chứng chỉ Lập trình');
    }

    /**
     * 5. Giảng viên có thể tải lên tài liệu minh chứng cho từng requirement cụ thể.
     */
    public function test_instructor_can_upload_document_for_specific_requirement(): void
    {
        $file = UploadedFile::fake()->create('bang-dai-hoc.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->instructor)->post(route('instructor.profile.documents.upload'), [
            'requirement_id' => $this->reqDegree->id,
            'files' => [$file],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('instructor_certificates', [
            'user_id' => $this->instructor->id,
            'requirement_id' => $this->reqDegree->id,
            'document_type' => 'degree',
            'status' => 'draft',
        ]);
    }

    public function test_instructor_can_upload_images_and_supported_certificate_videos_up_to_50mb(): void
    {
        $files = [
            UploadedFile::fake()->image('certificate.jpg')->size(1024),
            UploadedFile::fake()->create('evidence.mp4', 51200, 'video/mp4'),
            UploadedFile::fake()->create('evidence.mov', 2048, 'video/quicktime'),
            UploadedFile::fake()->create('evidence.webm', 2048, 'video/webm'),
        ];

        $this->actingAs($this->instructor)
            ->post(route('instructor.profile.documents.upload'), [
                'requirement_id' => $this->reqDegree->id,
                'source_type' => 'file',
                'files' => $files,
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        foreach (['certificate.jpg', 'evidence.mp4', 'evidence.mov', 'evidence.webm'] as $originalName) {
            $this->assertDatabaseHas('instructor_certificates', [
                'user_id' => $this->instructor->id,
                'requirement_id' => $this->reqDegree->id,
                'original_name' => $originalName,
                'source_type' => 'file',
                'status' => 'draft',
            ]);
        }

        $video = InstructorCertificate::query()->where('original_name', 'evidence.mp4')->firstOrFail();
        $this->assertTrue($video->isVideo());
        $this->assertSame(51200 * 1024, $video->file_size);
    }

    public function test_certificate_video_larger_than_50mb_is_rejected(): void
    {
        $this->actingAs($this->instructor)
            ->post(route('instructor.profile.documents.upload'), [
                'requirement_id' => $this->reqDegree->id,
                'source_type' => 'file',
                'files' => [UploadedFile::fake()->create('too-large.mp4', 51201, 'video/mp4')],
            ])
            ->assertRedirect()
            ->assertSessionHasErrors(['files.0']);

        $this->assertDatabaseMissing('instructor_certificates', [
            'user_id' => $this->instructor->id,
            'original_name' => 'too-large.mp4',
        ]);
    }

    public function test_unverified_instructor_cannot_upload_documents(): void
    {
        $instructor = $this->createUnverifiedInstructor();

        $this->actingAs($instructor)
            ->post(route('instructor.profile.documents.upload'), [
                'requirement_id' => $this->reqDegree->id,
                'files' => [UploadedFile::fake()->create('degree.pdf', 100, 'application/pdf')],
            ])
            ->assertRedirect(route('verification.notice'));

        $this->assertDatabaseMissing('instructor_certificates', ['user_id' => $instructor->id]);
    }

    public function test_unverified_instructor_cannot_submit_review(): void
    {
        $instructor = $this->createUnverifiedInstructor();
        $this->createDocumentForUser($instructor, $this->reqDegree);
        $this->createDocumentForUser($instructor, $this->reqCert);

        $this->actingAs($instructor)
            ->post(route('instructor.profile.submit-review'))
            ->assertRedirect(route('verification.notice'));

        $instructor->refresh();
        $this->assertNull($instructor->submitted_for_review_at);
    }

    public function test_unverified_rejected_instructor_cannot_resubmit(): void
    {
        $instructor = $this->createUnverifiedInstructor(['instructor_status' => 'rejected']);

        $this->actingAs($instructor)
            ->post(route('instructor.resubmit'), [
                'phone' => $instructor->phone,
                'specialty' => 'Laravel & Vue.js',
                'experience' => '5 năm kinh nghiệm',
                'bio' => 'Senior Web Developer',
                'agree_information' => true,
                'agree_terms' => true,
            ])
            ->assertRedirect(route('verification.notice'));

        $instructor->refresh();
        $this->assertSame('rejected', $instructor->instructor_status);
        $this->assertNull($instructor->submitted_for_review_at);
    }

    public function test_missing_required_documents_disable_submit_and_direct_post_cannot_bypass_guard(): void
    {
        $this->actingAs($this->instructor)
            ->get(route('instructor.profile'))
            ->assertOk()
            ->assertSee('Còn thiếu 2 tài liệu bắt buộc')
            ->assertSee('disabled', false);

        $this->actingAs($this->instructor)
            ->post(route('instructor.profile.submit-review'))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->instructor->refresh();
        $this->assertNull($this->instructor->submitted_for_review_at);
        $this->assertFalse($this->instructor->needs_admin_review);
    }

    public function test_all_required_pending_documents_enable_submit_and_set_review_state(): void
    {
        $this->createDocument($this->reqDegree);
        $this->createDocument($this->reqCert);

        $eligibility = app(InstructorRequirementService::class)->getSubmitEligibility($this->instructor);
        $this->assertTrue($eligibility['can_submit']);
        $this->assertSame(2, $eligibility['required_count']);
        $this->assertSame(2, $eligibility['submitted_count']);
        $this->assertSame(0, $eligibility['missing_count']);

        $this->actingAs($this->instructor)
            ->get(route('instructor.profile'))
            ->assertOk()
            ->assertSee('Đã đủ hồ sơ để gửi xét duyệt')
            ->assertSee('bg-amber-600 text-white hover:bg-amber-700', false)
            ->assertDontSee('cursor-not-allowed bg-slate-300', false);

        $this->actingAs($this->instructor)
            ->post(route('instructor.profile.submit-review'))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->instructor->refresh();
        $this->assertNotNull($this->instructor->submitted_for_review_at);
        $this->assertTrue($this->instructor->needs_admin_review);
    }

    public function test_rejected_document_requires_pending_replacement_and_optional_document_does_not_block(): void
    {
        $this->createDocument($this->reqDegree, 'approved');
        $this->createDocument($this->reqCert, 'rejected');
        InstructorDocumentRequirement::create([
            'category_id' => $this->categoryWeb->id,
            'document_type' => 'portfolio',
            'document_title' => 'Portfolio tùy chọn',
            'is_required' => false,
            'is_active' => true,
        ]);

        $service = app(InstructorRequirementService::class);
        $this->assertFalse($service->getSubmitEligibility($this->instructor)['can_submit']);

        $this->createDocument($this->reqCert, 'pending');

        $eligibility = $service->getSubmitEligibility($this->instructor->fresh());
        $this->assertTrue($eligibility['can_submit']);
    }

    public function test_resubmit_cannot_bypass_requirement_guard(): void
    {
        $this->instructor->update(['instructor_status' => 'rejected']);

        $this->actingAs($this->instructor)
            ->post(route('instructor.resubmit'), [
                'phone' => $this->instructor->phone,
                'specialty' => 'Laravel & Vue.js',
                'experience' => '5 năm kinh nghiệm',
                'bio' => 'Senior Web Developer',
                'agree_information' => true,
                'agree_terms' => true,
            ])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->instructor->refresh();
        $this->assertSame('rejected', $this->instructor->instructor_status);
        $this->assertNull($this->instructor->submitted_for_review_at);
    }

    public function test_instructor_without_teaching_field_cannot_submit(): void
    {
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'pending',
            'email_verified_at' => now(),
        ]);
        InstructorProfile::create(['user_id' => $instructor->id]);

        $eligibility = app(InstructorRequirementService::class)->getSubmitEligibility($instructor);

        $this->assertFalse($eligibility['can_submit']);
        $this->assertSame(['Ngành / Lĩnh vực giảng dạy'], $eligibility['missing_titles']);
    }

    public function test_admin_can_still_view_legacy_certificate_path_when_no_certificate_records_exist(): void
    {
        $legacyPath = 'legacy/instructor-certificate.pdf';
        Storage::disk('local')->put($legacyPath, 'legacy certificate');
        InstructorApplication::create([
            'user_id' => $this->instructor->id,
            'certificate_path' => $legacyPath,
            'status' => 'pending',
            'source_type' => 'file',
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.instructors.applications.certificate', $this->instructor))
            ->assertOk();
    }

    public function test_instructor_can_submit_https_url_for_a_requirement(): void
    {
        $response = $this->actingAs($this->instructor)->post(route('instructor.profile.documents.upload'), [
            'requirement_id' => $this->reqDegree->id,
            'source_type' => 'url',
            'document_url' => 'https://example.com/documents/degree.pdf',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('instructor_certificates', [
            'user_id' => $this->instructor->id,
            'requirement_id' => $this->reqDegree->id,
            'source_type' => 'url',
            'document_url' => 'https://example.com/documents/degree.pdf',
            'status' => 'draft',
        ]);
    }

    public function test_instructor_can_submit_http_url_when_policy_allows_http(): void
    {
        $response = $this->actingAs($this->instructor)->post(route('instructor.profile.documents.upload'), [
            'requirement_id' => $this->reqDegree->id,
            'source_type' => 'url',
            'document_url' => 'http://example.com/degree.pdf',
        ]);

        $response->assertSessionHasNoErrors();
    }

    public function test_instructor_cannot_submit_unsafe_or_malformed_document_url(): void
    {
        foreach (['javascript:alert(1)', 'data:text/html,test', 'file:///etc/passwd', 'not-a-url'] as $url) {
            $this->actingAs($this->instructor)
                ->post(route('instructor.profile.documents.upload'), [
                    'requirement_id' => $this->reqDegree->id,
                    'source_type' => 'url',
                    'document_url' => $url,
                ])
                ->assertSessionHasErrors(['document_url']);
        }

        $this->assertDatabaseMissing('instructor_certificates', ['user_id' => $this->instructor->id, 'source_type' => 'url']);
    }

    public function test_admin_can_review_url_and_an_approved_url_fulfils_its_requirement(): void
    {
        $document = InstructorCertificate::create([
            'user_id' => $this->instructor->id,
            'requirement_id' => $this->reqDegree->id,
            'source_type' => 'url',
            'document_url' => 'https://example.com/degree.pdf',
            'title' => 'Bằng CNTT trực tuyến',
            'document_type' => 'degree',
            'status' => 'pending',
            'uploaded_at' => now(),
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.instructors.applications.documents.review', [$this->instructor, $document]), ['status' => 'approved'])
            ->assertRedirect();

        $this->assertDatabaseHas('instructor_certificates', ['id' => $document->id, 'status' => 'approved']);
        $summary = app(InstructorRequirementService::class)->getRequirementsForInstructor($this->instructor)['summary'];
        $this->assertSame(1, $summary['required_approved_count']);
    }

    public function test_owner_can_delete_url_record_without_touching_file_storage(): void
    {
        $document = InstructorCertificate::create([
            'user_id' => $this->instructor->id,
            'requirement_id' => $this->reqDegree->id,
            'source_type' => 'url',
            'document_url' => 'https://example.com/degree.pdf',
            'title' => 'Bằng CNTT trực tuyến',
            'document_type' => 'degree',
            'status' => 'draft',
            'uploaded_at' => now(),
        ]);

        $this->actingAs($this->instructor)
            ->delete(route('instructor.profile.documents.delete', $document))
            ->assertRedirect();

        $this->assertDatabaseMissing('instructor_certificates', ['id' => $document->id]);
    }

    public function test_url_document_stays_draft_when_edited_and_cannot_use_file_replacement(): void
    {
        $document = InstructorCertificate::create([
            'user_id' => $this->instructor->id,
            'requirement_id' => $this->reqDegree->id,
            'source_type' => 'url',
            'document_url' => 'https://example.com/old.pdf',
            'title' => 'Bằng trực tuyến',
            'document_type' => 'degree',
            'status' => 'draft',
            'uploaded_at' => now(),
        ]);

        $this->actingAs($this->instructor)
            ->put(route('instructor.profile.documents.url.update', $document), [
                'document_url' => 'https://example.com/new.pdf',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('instructor_certificates', [
            'id' => $document->id,
            'document_url' => 'https://example.com/new.pdf',
            'status' => 'draft',
            'file_path' => null,
        ]);

        $this->actingAs($this->instructor)
            ->patch(route('instructor.profile.documents.replace', $document), [
                'file' => UploadedFile::fake()->create('replacement.pdf', 100, 'application/pdf'),
            ])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertNull($document->fresh()->file_path);
    }

    public function test_pending_url_cannot_be_mutated(): void
    {
        $document = InstructorCertificate::create([
            'user_id' => $this->instructor->id,
            'requirement_id' => $this->reqDegree->id,
            'source_type' => 'url',
            'document_url' => 'https://example.com/original.pdf',
            'title' => 'Bằng trực tuyến',
            'document_type' => 'degree',
            'status' => 'pending',
            'uploaded_at' => now(),
        ]);

        $this->actingAs($this->instructor)
            ->put(route('instructor.profile.documents.url.update', $document), [
                'document_url' => 'https://example.com/tampered.pdf',
            ])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertSame('https://example.com/original.pdf', $document->fresh()->document_url);
        $this->assertSame('pending', $document->fresh()->status);
    }

    public function test_another_instructor_cannot_replace_delete_or_edit_document(): void
    {
        $other = $this->createUnverifiedInstructor(['email_verified_at' => now()]);
        $fileDocument = $this->createDocument($this->reqDegree, 'draft');
        $urlDocument = InstructorCertificate::create([
            'user_id' => $this->instructor->id,
            'requirement_id' => $this->reqCert->id,
            'source_type' => 'url',
            'document_url' => 'https://example.com/document.pdf',
            'document_type' => 'certificate',
            'status' => 'draft',
            'uploaded_at' => now(),
        ]);

        $this->actingAs($other)->patch(route('instructor.profile.documents.replace', $fileDocument), ['title' => 'Tampered'])->assertForbidden();
        $this->actingAs($other)->delete(route('instructor.profile.documents.delete', $fileDocument))->assertForbidden();
        $this->actingAs($other)->put(route('instructor.profile.documents.url.update', $urlDocument), [
            'document_url' => 'https://example.com/tampered.pdf',
        ])->assertForbidden();

        $this->assertDatabaseHas('instructor_certificates', ['id' => $fileDocument->id, 'title' => $this->reqDegree->document_title]);
        $this->assertDatabaseHas('instructor_certificates', ['id' => $urlDocument->id, 'document_url' => 'https://example.com/document.pdf']);
    }

    public function test_free_upload_accepts_portfolio_and_employment_contract_types(): void
    {
        foreach (['portfolio', 'employment_contract'] as $documentType) {
            $this->actingAs($this->instructor)
                ->post(route('instructor.profile.documents.upload'), [
                    'source_type' => 'url',
                    'document_url' => "https://example.com/{$documentType}.pdf",
                    'document_type' => $documentType,
                ])
                ->assertRedirect()
                ->assertSessionHasNoErrors();

            $this->assertDatabaseHas('instructor_certificates', [
                'user_id' => $this->instructor->id,
                'document_type' => $documentType,
                'status' => 'draft',
            ]);
        }
    }

    /**
     * 6. Chống gian lận: Giảng viên KHÔNG THỂ tải lên tài liệu cho requirement của ngành khác.
     */
    public function test_instructor_cannot_upload_document_for_a_different_category_requirement(): void
    {
        // Tạo requirement thuộc ngành Marketing
        $reqMarketing = InstructorDocumentRequirement::create([
            'category_id' => $this->categoryMarketing->id,
            'document_type' => 'certificate',
            'document_title' => 'Chứng chỉ Google Ads',
            'is_required' => true,
            'is_active' => true,
        ]);

        $file = UploadedFile::fake()->create('chung-chi-marketing.pdf', 300, 'application/pdf');

        $response = $this->actingAs($this->instructor)->post(route('instructor.profile.documents.upload'), [
            'requirement_id' => $reqMarketing->id,
            'files' => [$file],
        ]);

        $response->assertSessionHasErrors(['requirement_id']);
        $this->assertDatabaseMissing('instructor_certificates', [
            'user_id' => $this->instructor->id,
            'requirement_id' => $reqMarketing->id,
        ]);
    }

    /**
     * 7. Admin KHÔNG THỂ duyệt giảng viên khi còn thiếu tài liệu bắt buộc của ngành.
     */
    public function test_admin_cannot_approve_instructor_when_required_documents_are_missing(): void
    {
        // Giảng viên mới chỉ nộp 1 trong 2 tài liệu bắt buộc (Bằng tốt nghiệp)
        InstructorCertificate::create([
            'user_id' => $this->instructor->id,
            'requirement_id' => $this->reqDegree->id,
            'file_path' => 'test/path.pdf',
            'original_name' => 'bang.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'title' => 'Bằng CNTT',
            'document_type' => 'degree',
            'status' => 'pending',
            'uploaded_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.instructors.applications.approve', $this->instructor));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertStringContainsString('Không thể duyệt hồ sơ. Giảng viên còn thiếu tài liệu bắt buộc của ngành', session('error'));

        $this->instructor->refresh();
        $this->assertEquals('pending', $this->instructor->instructor_status);
    }

    /**
     * 8. Admin KHÔNG THỂ duyệt giảng viên khi một tài liệu bắt buộc bị từ chối.
     */
    public function test_admin_cannot_approve_instructor_when_required_document_is_rejected(): void
    {
        // Nộp bằng (approved)
        InstructorCertificate::create([
            'user_id' => $this->instructor->id,
            'requirement_id' => $this->reqDegree->id,
            'file_path' => 'test/degree.pdf',
            'original_name' => 'degree.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'title' => 'Bằng CNTT',
            'document_type' => 'degree',
            'status' => 'approved',
            'uploaded_at' => now(),
        ]);

        // Nộp chứng chỉ (nhưng bị rejected)
        InstructorCertificate::create([
            'user_id' => $this->instructor->id,
            'requirement_id' => $this->reqCert->id,
            'file_path' => 'test/cert.pdf',
            'original_name' => 'cert.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'title' => 'Chứng chỉ Lập trình',
            'document_type' => 'certificate',
            'status' => 'rejected',
            'rejection_reason' => 'Chứng chỉ hết hạn',
            'uploaded_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.instructors.applications.approve', $this->instructor));

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->instructor->refresh();
        $this->assertEquals('pending', $this->instructor->instructor_status);
    }

    /**
     * 9. Admin CÓ THỂ duyệt giảng viên khi đã nộp đầy đủ toàn bộ tài liệu bắt buộc.
     */
    public function test_admin_can_approve_instructor_when_all_required_documents_are_submitted(): void
    {
        InstructorCertificate::create([
            'user_id' => $this->instructor->id,
            'requirement_id' => $this->reqDegree->id,
            'file_path' => 'test/degree.pdf',
            'original_name' => 'degree.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'title' => 'Bằng CNTT',
            'document_type' => 'degree',
            'status' => 'pending',
            'uploaded_at' => now(),
        ]);

        InstructorCertificate::create([
            'user_id' => $this->instructor->id,
            'requirement_id' => $this->reqCert->id,
            'file_path' => 'test/cert.pdf',
            'original_name' => 'cert.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'title' => 'Chứng chỉ Lập trình',
            'document_type' => 'certificate',
            'status' => 'pending',
            'uploaded_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.instructors.applications.approve', $this->instructor));

        $response->assertRedirect();
        $this->instructor->refresh();
        $this->assertEquals('approved', $this->instructor->instructor_status);
    }

    /**
     * 10. Khi giảng viên thay đổi ngành giảng dạy: Tính toán lại yêu cầu và gỡ requirement_id không tương thích.
     */
    public function test_changing_teaching_field_keeps_submitted_certificate_requirement_history(): void
    {
        // Gán cert cho requirement của ngành Web
        $cert = InstructorCertificate::create([
            'user_id' => $this->instructor->id,
            'requirement_id' => $this->reqDegree->id,
            'file_path' => 'test/degree.pdf',
            'original_name' => 'degree.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'title' => 'Bằng CNTT',
            'document_type' => 'degree',
            'status' => 'pending',
            'uploaded_at' => now(),
        ]);

        // Đổi sang ngành Marketing
        $response = $this->actingAs($this->instructor)->put(route('instructor.profile.update'), [
            'name' => $this->instructor->name,
            'username' => $this->instructor->username,
            'category_id' => $this->categoryMarketing->id,
            'specialty' => 'Digital Marketing',
        ]);

        $response->assertRedirect();

        $cert->refresh();
        // Pending là lịch sử đã gửi, không được mất liên kết requirement cũ.
        $this->assertSame($this->reqDegree->id, $cert->requirement_id);

        $this->instructor->refresh();
        $this->assertEquals($this->categoryMarketing->id, $this->instructor->getTeachingCategoryId());
    }

    /**
     * 11. Tương thích ngược: Giảng viên cũ chưa gắn requirement_id vẫn đối soát theo document_type.
     */
    public function test_backward_compatibility_for_existing_instructors_without_requirement_id(): void
    {
        // Tạo certificate legacy không có requirement_id
        InstructorCertificate::create([
            'user_id' => $this->instructor->id,
            'requirement_id' => null,
            'file_path' => 'test/legacy-degree.pdf',
            'original_name' => 'legacy-degree.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'title' => 'Bằng Đại học',
            'document_type' => 'degree',
            'status' => 'approved',
            'uploaded_at' => now(),
        ]);

        InstructorCertificate::create([
            'user_id' => $this->instructor->id,
            'requirement_id' => null,
            'file_path' => 'test/legacy-cert.pdf',
            'original_name' => 'legacy-cert.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'title' => 'Chứng chỉ nghề',
            'document_type' => 'certificate',
            'status' => 'approved',
            'uploaded_at' => now(),
        ]);

        $service = app(InstructorRequirementService::class);
        $result = $service->getRequirementsForInstructor($this->instructor);

        $this->assertTrue($result['summary']['has_all_required_submitted']);
        $this->assertTrue($result['summary']['can_approve']);
        $this->assertEquals(2, $result['summary']['required_approved_count']);
    }

    public function test_applications_index_shows_progress_for_required_documents_only(): void
    {
        InstructorDocumentRequirement::create([
            'category_id' => $this->categoryWeb->id,
            'document_type' => 'transcript',
            'document_title' => 'Bảng điểm',
            'is_required' => true,
            'is_active' => true,
            'sort_order' => 3,
        ]);

        $optionalRequirement = InstructorDocumentRequirement::create([
            'category_id' => $this->categoryWeb->id,
            'document_type' => 'portfolio',
            'document_title' => 'Portfolio',
            'is_required' => false,
            'is_active' => true,
            'sort_order' => 4,
        ]);

        InstructorCertificate::create([
            'user_id' => $this->instructor->id,
            'requirement_id' => $this->reqDegree->id,
            'file_path' => 'test/degree.pdf',
            'original_name' => 'degree.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'document_type' => 'degree',
            'status' => 'approved',
            'uploaded_at' => now(),
        ]);

        InstructorCertificate::create([
            'user_id' => $this->instructor->id,
            'requirement_id' => $optionalRequirement->id,
            'file_path' => 'test/portfolio.pdf',
            'original_name' => 'portfolio.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'document_type' => 'portfolio',
            'status' => 'approved',
            'uploaded_at' => now(),
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.instructors.applications.index'))
            ->assertOk()
            ->assertSee('33%')
            ->assertSee('1/3');
    }

    public function test_submit_promotes_only_current_requirement_drafts(): void
    {
        $degree = $this->createDocument($this->reqDegree, 'draft');
        $certificate = $this->createDocument($this->reqCert, 'draft');
        $unassigned = InstructorCertificate::create([
            'user_id' => $this->instructor->id,
            'file_path' => 'test/unassigned.pdf', 'original_name' => 'unassigned.pdf', 'document_type' => 'degree',
            'status' => 'draft', 'uploaded_at' => now(),
        ]);
        $outside = InstructorDocumentRequirement::create([
            'category_id' => $this->categoryMarketing->id, 'document_type' => 'degree', 'document_title' => 'Marketing degree',
            'is_required' => true, 'is_active' => true,
        ]);
        $outsideDraft = $this->createDocument($outside, 'draft');

        $this->actingAs($this->instructor)->post(route('instructor.profile.submit-review'))->assertRedirect();

        $this->assertSame('pending', $degree->fresh()->status);
        $this->assertSame('pending', $certificate->fresh()->status);
        $this->assertSame('draft', $unassigned->fresh()->status);
        $this->assertSame('draft', $outsideDraft->fresh()->status);
    }

    public function test_duplicate_submit_does_not_repeat_application_transition(): void
    {
        $this->createDocument($this->reqDegree, 'draft');
        $this->createDocument($this->reqCert, 'draft');

        $this->actingAs($this->instructor)
            ->post(route('instructor.profile.submit-review'))
            ->assertRedirect()
            ->assertSessionHas('success');
        $submittedAt = $this->instructor->fresh()->submitted_for_review_at;

        $this->actingAs($this->instructor)
            ->post(route('instructor.profile.submit-review'))
            ->assertRedirect()
            ->assertSessionHas('error', 'Hồ sơ đã được gửi xét duyệt trước đó.');

        $this->assertTrue($submittedAt->equalTo($this->instructor->fresh()->submitted_for_review_at));
    }

    public function test_draft_document_can_be_replaced_and_deleted_while_pending_cannot(): void
    {
        $draft = $this->createDocument($this->reqDegree, 'draft');
        Storage::disk('local')->put($draft->file_path, 'old');
        $this->actingAs($this->instructor)->patch(route('instructor.profile.documents.replace', $draft), [
            'file' => UploadedFile::fake()->create('replacement.pdf', 100, 'application/pdf'), 'title' => 'Replacement',
        ])->assertRedirect();
        $this->assertSame('Replacement', $draft->fresh()->title);
        $this->actingAs($this->instructor)->delete(route('instructor.profile.documents.delete', $draft))->assertRedirect();
        $this->assertDatabaseMissing('instructor_certificates', ['id' => $draft->id]);

        $pending = $this->createDocument($this->reqCert, 'pending');
        $this->actingAs($this->instructor)->delete(route('instructor.profile.documents.delete', $pending))->assertRedirect();
        $this->assertSame('pending', $pending->fresh()->status);
    }

    public function test_rejected_history_is_kept_and_admin_can_only_review_pending_documents(): void
    {
        $rejected = $this->createDocument($this->reqDegree, 'rejected');
        $this->actingAs($this->instructor)->post(route('instructor.profile.documents.upload'), [
            'requirement_id' => $this->reqDegree->id,
            'file' => UploadedFile::fake()->create('replacement.pdf', 100, 'application/pdf'),
        ])->assertRedirect();
        $this->assertSame('rejected', $rejected->fresh()->status);
        $this->assertSame(1, $this->instructor->instructorCertificates()->where('requirement_id', $this->reqDegree->id)->where('status', 'draft')->count());

        $draft = $this->instructor->instructorCertificates()->where('status', 'draft')->firstOrFail();
        $pending = $this->createDocument($this->reqCert, 'pending');
        $this->actingAs($this->admin)->post(route('admin.instructors.applications.documents.review', [$this->instructor, $draft]), ['status' => 'approved'])->assertStatus(422);
        $this->actingAs($this->admin)->post(route('admin.instructors.applications.documents.review', [$this->instructor, $pending]), ['status' => 'approved'])->assertRedirect();
        $this->assertSame('approved', $pending->fresh()->status);
    }

    private function createDocument(InstructorDocumentRequirement $requirement, string $status = 'pending'): InstructorCertificate
    {
        return $this->createDocumentForUser($this->instructor, $requirement, $status);
    }

    private function createDocumentForUser(User $user, InstructorDocumentRequirement $requirement, string $status = 'pending'): InstructorCertificate
    {
        return InstructorCertificate::create([
            'user_id' => $user->id,
            'requirement_id' => $requirement->id,
            'file_path' => 'test/'.$requirement->id.'-'.$status.'-'.Str::uuid().'.pdf',
            'original_name' => 'document.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'title' => $requirement->document_title,
            'document_type' => $requirement->document_type,
            'status' => $status,
            'uploaded_at' => now(),
        ]);
    }

    /** @param array<string, mixed> $attributes */
    private function createUnverifiedInstructor(array $attributes = []): User
    {
        $instructor = User::factory()->create(array_merge([
            'role' => 'instructor',
            'instructor_status' => 'pending',
            'email_verified_at' => null,
        ], $attributes));
        InstructorProfile::create([
            'user_id' => $instructor->id,
            'category_id' => $this->categoryWeb->id,
            'teaching_field' => $this->categoryWeb->name,
        ]);

        return $instructor;
    }
}
