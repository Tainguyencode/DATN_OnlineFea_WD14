<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\InstructorCertificate;
use App\Models\InstructorDocumentRequirement;
use App\Models\InstructorProfile;
use App\Models\User;
use App\Services\InstructorRequirementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InstructorDocumentRequirementWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $instructor;

    protected Category $categoryWeb;

    protected Category $categoryMarketing;

    protected InstructorDocumentRequirement $reqDegree;

    protected InstructorDocumentRequirement $reqCert;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        Storage::fake('public');

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
            'status' => 'pending',
        ]);
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
    public function test_changing_teaching_field_recalculates_requirements_and_unassigns_incompatible_certificates(): void
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
        // Requirement cũ của ngành Web không thuộc ngành Marketing nên requirement_id bị đặt về null
        $this->assertNull($cert->requirement_id);

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
}
