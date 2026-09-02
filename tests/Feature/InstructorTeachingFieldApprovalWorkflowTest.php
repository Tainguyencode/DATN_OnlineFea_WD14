<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\InstructorCertificate;
use App\Models\InstructorDocumentRequirement;
use App\Models\InstructorProfile;
use App\Models\InstructorTeachingField;
use App\Models\User;
use App\Services\InstructorCourseCategoryAccess;
use App\Services\InstructorRequirementService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InstructorTeachingFieldApprovalWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    public function test_profile_adds_new_field_as_draft_without_detaching_approved_field(): void
    {
        [$instructor, , $fieldA, $fieldB, $categoryA, $categoryB] = $this->fixture();
        $fieldB->delete();
        $profile = $fieldA->profile;

        $response = $this->actingAs($instructor)->put(route('instructor.profile.update'), [
            'name' => $instructor->name,
            'username' => 'instructor-'.$instructor->id,
            'phone' => $instructor->phone,
            'bio' => $instructor->bio,
            'bank_name' => $instructor->bank_name,
            'bank_account_number' => $instructor->bank_account_number,
            'bank_account_name' => $instructor->bank_account_name,
            'teaching_fields' => [
                ['teaching_field_id' => $fieldA->id, 'category_id' => $categoryA->id, 'organization' => 'A'],
                ['category_id' => $categoryB->id, 'organization' => 'B'],
            ],
        ]);

        $response->assertRedirect()
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success', 'Đã lưu yêu cầu thay đổi ngành giảng dạy. Vui lòng hoàn thiện hồ sơ và gửi Admin xét duyệt.');

        $this->assertDatabaseHas('instructor_profile_teaching_fields', ['id' => $fieldA->id, 'approval_status' => 'approved']);
        $this->assertDatabaseHas('instructor_profile_teaching_fields', ['category_id' => $categoryB->id, 'approval_status' => 'draft']);
        $this->assertSame($categoryA->id, $profile->fresh()->category_id);
        $this->assertSame([$categoryA->id], $profile->approvedTeachingCategories()->pluck('categories.id')->map(fn ($id) => (int) $id)->all());
        $this->assertSame([$categoryA->id], $instructor->fresh()->getTeachingCategories()->pluck('id')->map(fn ($id) => (int) $id)->all());
        $this->assertTrue(app(InstructorCourseCategoryAccess::class)->canTeachCategory($instructor, $categoryA->id));
        $this->assertFalse(app(InstructorCourseCategoryAccess::class)->canTeachCategory($instructor, $categoryB->id));
        $this->assertCourseDenied($instructor, $categoryB);
        $this->assertDatabaseHas('users', ['id' => $instructor->id, 'instructor_status' => 'approved']);
    }

    public function test_legacy_approved_profile_without_a_pivot_still_has_course_access(): void
    {
        $instructor = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'approved', 'email_verified_at' => now()]);
        $category = Category::create(['name' => 'Legacy category', 'slug' => 'legacy-category', 'status' => true]);
        InstructorProfile::create(['user_id' => $instructor->id, 'category_id' => $category->id]);

        $this->assertTrue(app(InstructorCourseCategoryAccess::class)->canTeachCategory($instructor, $category->id));
        $this->assertCourseAllowed($instructor, $category);
    }

    public function test_legacy_bootstrap_sync_preserves_approval_boundary(): void
    {
        $category = Category::create(['name' => 'Bootstrap category', 'slug' => 'bootstrap-category', 'status' => true]);
        $approved = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'approved', 'email_verified_at' => now()]);
        $approvedProfile = InstructorProfile::create(['user_id' => $approved->id]);
        $approvedProfile->syncTeachingCategories([$category->id]);

        $pending = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'pending', 'email_verified_at' => now()]);
        $pendingProfile = InstructorProfile::create(['user_id' => $pending->id]);
        $pendingProfile->syncTeachingCategories([$category->id]);

        $this->assertDatabaseHas('instructor_profile_teaching_fields', [
            'instructor_profile_id' => $approvedProfile->id,
            'category_id' => $category->id,
            'approval_status' => InstructorTeachingField::STATUS_APPROVED,
            'is_primary' => true,
        ]);
        $this->assertDatabaseHas('instructor_profile_teaching_fields', [
            'instructor_profile_id' => $pendingProfile->id,
            'category_id' => $category->id,
            'approval_status' => InstructorTeachingField::STATUS_DRAFT,
            'is_primary' => true,
        ]);
    }

    public function test_new_field_is_draft_scoped_documents_submit_and_admin_approval_grants_course_access(): void
    {
        [$instructor, $admin, $fieldA, $fieldB, $categoryA, $categoryB, $requirementB] = $this->fixture();

        $this->assertSame('approved', $fieldA->approval_status);
        $this->assertSame('draft', $fieldB->approval_status);
        $this->assertFalse(app(InstructorCourseCategoryAccess::class)->canTeachCategory($instructor, $categoryB->id));

        $document = $this->certificate($instructor, $fieldB, $requirementB, 'draft');
        $this->actingAs($instructor)
            ->post(route('instructor.profile.teaching-fields.submit-review', $fieldB))
            ->assertRedirect();

        $this->assertDatabaseHas('instructor_profile_teaching_fields', ['id' => $fieldB->id, 'approval_status' => 'pending']);
        $this->assertDatabaseHas('instructor_certificates', ['id' => $document->id, 'status' => 'pending', 'instructor_teaching_field_id' => $fieldB->id]);
        $this->assertDatabaseHas('users', ['id' => $instructor->id, 'instructor_status' => 'approved']);
        $this->assertCourseDenied($instructor, $categoryB);

        $this->actingAs($admin)->post(route('admin.instructors.teaching-fields.approve', $fieldB))->assertRedirect();
        $this->assertDatabaseHas('instructor_profile_teaching_fields', ['id' => $fieldB->id, 'approval_status' => 'approved']);
        $this->assertDatabaseHas('instructor_certificates', ['id' => $document->id, 'status' => 'approved']);
        $this->assertCourseAllowed($instructor, $categoryB);
        $this->assertCourseAllowed($instructor, $categoryA);
    }

    public function test_url_upload_is_scoped_to_field_and_promoted_only_on_field_submit(): void
    {
        [$instructor, , , $fieldB, , , $requirementB] = $this->fixture();

        $this->actingAs($instructor)
            ->post(route('instructor.profile.documents.upload'), [
                'instructor_teaching_field_id' => $fieldB->id,
                'requirement_id' => $requirementB->id,
                'source_type' => 'url',
                'document_url' => 'https://example.com/field-proof.pdf',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $document = InstructorCertificate::query()
            ->where('instructor_teaching_field_id', $fieldB->id)
            ->where('source_type', 'url')
            ->firstOrFail();
        $this->assertSame('draft', $document->status);

        $this->actingAs($instructor)
            ->post(route('instructor.profile.teaching-fields.submit-review', $fieldB))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame('pending', $document->fresh()->status);
        $this->assertSame(InstructorTeachingField::STATUS_PENDING, $fieldB->fresh()->approval_status);
    }

    public function test_instructor_cannot_upload_to_another_instructors_teaching_field(): void
    {
        [$owner, , , $fieldB, , , $requirementB] = $this->fixture();
        $attacker = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'approved',
            'email_verified_at' => now(),
        ]);
        InstructorProfile::create(['user_id' => $attacker->id]);

        $this->actingAs($attacker)
            ->post(route('instructor.profile.documents.upload'), [
                'instructor_teaching_field_id' => $fieldB->id,
                'requirement_id' => $requirementB->id,
                'source_type' => 'url',
                'document_url' => 'https://example.com/unauthorized.pdf',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('instructor_certificates', [
            'user_id' => $attacker->id,
            'instructor_teaching_field_id' => $fieldB->id,
        ]);
        $this->assertSame($owner->id, $fieldB->profile->user_id);
    }

    public function test_one_scoped_document_cannot_satisfy_two_fields_with_the_same_inherited_requirement(): void
    {
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'pending',
            'email_verified_at' => now(),
        ]);
        $parent = Category::create(['name' => 'Công nghệ', 'slug' => 'cong-nghe', 'status' => true]);
        $categoryA = Category::create(['name' => 'Web', 'slug' => 'web', 'parent_id' => $parent->id, 'status' => true]);
        $categoryB = Category::create(['name' => 'Mobile', 'slug' => 'mobile', 'parent_id' => $parent->id, 'status' => true]);
        Storage::fake('public');
        Storage::disk('public')->put('instructor_cvs/scoped-fields.pdf', 'pdf');
        $profile = InstructorProfile::create(['user_id' => $instructor->id, 'category_id' => $categoryA->id, 'cv' => 'instructor_cvs/scoped-fields.pdf']);
        $fieldA = InstructorTeachingField::create(['instructor_profile_id' => $profile->id, 'category_id' => $categoryA->id, 'is_primary' => true, 'approval_status' => 'draft']);
        $fieldB = InstructorTeachingField::create(['instructor_profile_id' => $profile->id, 'category_id' => $categoryB->id, 'approval_status' => 'draft']);
        $requirement = InstructorDocumentRequirement::create([
            'category_id' => $parent->id,
            'document_type' => 'degree',
            'document_title' => 'Bằng công nghệ',
            'is_required' => true,
            'is_active' => true,
        ]);
        $this->certificate($instructor, $fieldA, $requirement, 'draft');

        $eligibility = app(InstructorRequirementService::class)->getSubmitEligibility($instructor);

        $this->assertFalse($eligibility['can_submit']);
        $this->assertSame(1, $eligibility['submitted_count']);
        $this->assertSame(1, $eligibility['missing_count']);
        $this->assertStringContainsString($categoryB->name, implode(' ', $eligibility['missing_titles']));
        $this->assertSame('draft', $fieldB->approval_status);
    }

    public function test_admin_cannot_approve_field_after_its_required_document_is_rejected(): void
    {
        [$instructor, $admin, , $fieldB, , , $requirementB] = $this->fixture();
        $document = $this->certificate($instructor, $fieldB, $requirementB, 'rejected');
        $fieldB->update(['approval_status' => InstructorTeachingField::STATUS_PENDING, 'submitted_at' => now()]);

        $this->actingAs($admin)
            ->post(route('admin.instructors.teaching-fields.approve', $fieldB))
            ->assertStatus(422);

        $this->assertSame(InstructorTeachingField::STATUS_PENDING, $fieldB->fresh()->approval_status);
        $this->assertSame('rejected', $document->fresh()->status);
    }

    public function test_first_individually_approved_field_becomes_primary(): void
    {
        $instructor = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'approved', 'email_verified_at' => now()]);
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $category = Category::create(['name' => 'Dữ liệu', 'slug' => 'du-lieu', 'status' => true]);
        $profile = InstructorProfile::create(['user_id' => $instructor->id]);
        $field = InstructorTeachingField::create([
            'instructor_profile_id' => $profile->id,
            'category_id' => $category->id,
            'approval_status' => InstructorTeachingField::STATUS_PENDING,
            'submitted_at' => now(),
            'is_primary' => false,
        ]);
        $requirement = InstructorDocumentRequirement::create([
            'category_id' => $category->id,
            'document_type' => 'degree',
            'document_title' => 'Bằng dữ liệu',
            'is_required' => true,
            'is_active' => true,
        ]);
        $this->certificate($instructor, $field, $requirement, 'pending');

        $this->actingAs($admin)
            ->post(route('admin.instructors.teaching-fields.approve', $field))
            ->assertRedirect();

        $this->assertTrue($field->fresh()->is_primary);
        $this->assertSame($category->id, $profile->fresh()->category_id);
    }

    public function test_rejection_does_not_revoke_existing_approved_field_or_course_management(): void
    {
        [$instructor, $admin, $fieldA, $fieldB, $categoryA, $categoryB, $requirementB] = $this->fixture();
        $this->certificate($instructor, $fieldB, $requirementB, 'pending');
        $fieldB->update(['approval_status' => 'pending', 'submitted_at' => now()]);
        $courseA = $this->course($instructor, $categoryA);

        $this->actingAs($admin)->post(route('admin.instructors.teaching-fields.reject', $fieldB), ['rejection_reason' => 'Tài liệu chưa chứng minh đủ chuyên môn.'])->assertRedirect();

        $this->assertDatabaseHas('instructor_profile_teaching_fields', ['id' => $fieldA->id, 'approval_status' => 'approved']);
        $this->assertDatabaseHas('instructor_profile_teaching_fields', ['id' => $fieldB->id, 'approval_status' => 'rejected']);
        $this->assertCourseAllowed($instructor, $categoryA, $courseA);
        $this->assertCourseDenied($instructor, $categoryB);
    }

    public function test_replacement_supersedes_a_only_after_b_is_approved_and_documents_do_not_cross_scope(): void
    {
        [$instructor, $admin, $fieldA, $fieldB, $categoryA, $categoryB, $requirementB] = $this->fixture();
        $categoryC = Category::create(['name' => 'An toàn thông tin', 'slug' => 'an-toan-thong-tin', 'status' => true]);
        InstructorDocumentRequirement::create(['category_id' => $categoryC->id, 'document_type' => 'degree', 'document_title' => 'Bằng C', 'is_required' => true, 'is_active' => true]);
        $fieldB->update(['replace_of_teaching_field_id' => $fieldA->id]);
        $this->certificate($instructor, $fieldB, $requirementB, 'draft');

        $this->assertTrue(app(InstructorRequirementService::class)->getTeachingFieldSubmitEligibility($fieldB)['can_submit']);
        $fieldC = InstructorTeachingField::create(['instructor_profile_id' => $fieldA->instructor_profile_id, 'category_id' => $categoryC->id, 'approval_status' => 'draft']);
        $this->assertFalse(app(InstructorRequirementService::class)->getTeachingFieldSubmitEligibility($fieldC)['can_submit']);
        $this->assertSame($fieldA->id, $fieldB->fresh()->replace_of_teaching_field_id);
        $this->assertTrue(app(InstructorCourseCategoryAccess::class)->canTeachCategory($instructor, $categoryA->id));

        $courseA = $this->course($instructor, $categoryA);
        $this->actingAs($instructor)
            ->post(route('instructor.profile.teaching-fields.submit-review', $fieldB))
            ->assertRedirect();
        $this->assertDatabaseHas('instructor_profile_teaching_fields', ['id' => $fieldA->id, 'approval_status' => 'approved']);
        $this->assertCourseAllowed($instructor, $categoryA);

        $this->actingAs($admin)
            ->post(route('admin.instructors.teaching-fields.approve', $fieldB))
            ->assertRedirect();
        $this->assertDatabaseHas('instructor_profile_teaching_fields', ['id' => $fieldB->id, 'approval_status' => 'approved']);
        $this->assertDatabaseHas('instructor_profile_teaching_fields', ['id' => $fieldA->id, 'approval_status' => 'superseded']);
        $this->assertCourseDenied($instructor, $categoryA);
        $this->assertCourseAllowed($instructor, $categoryB);
        $this->assertCourseAllowed($instructor, $categoryA, $courseA);
    }

    public function test_profile_change_from_approved_a_to_b_keeps_a_effective_until_admin_approves_b(): void
    {
        [$instructor, $admin, $fieldA, $fieldB, $categoryA, $categoryB, $requirementB] = $this->fixture();
        $fieldB->delete();
        $profile = $fieldA->profile;

        $this->actingAs($instructor)->put(route('instructor.profile.update'), [
            'name' => $instructor->name,
            'username' => 'replacement-'.$instructor->id,
            'phone' => $instructor->phone,
            'bio' => $instructor->bio,
            'bank_name' => $instructor->bank_name,
            'bank_account_number' => $instructor->bank_account_number,
            'bank_account_name' => $instructor->bank_account_name,
            'teaching_fields' => [
                ['teaching_field_id' => $fieldA->id, 'category_id' => $categoryB->id, 'organization' => 'B'],
            ],
        ])->assertRedirect()
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success', 'Đã lưu yêu cầu thay đổi ngành giảng dạy. Vui lòng hoàn thiện hồ sơ và gửi Admin xét duyệt.');

        $replacement = InstructorTeachingField::query()
            ->where('instructor_profile_id', $profile->id)
            ->where('category_id', $categoryB->id)
            ->firstOrFail();

        $this->assertNotSame($fieldA->id, $replacement->id);
        $this->assertSame(InstructorTeachingField::STATUS_DRAFT, $replacement->approval_status);
        $this->assertSame($fieldA->id, $replacement->replace_of_teaching_field_id);
        $this->assertSame(InstructorTeachingField::STATUS_APPROVED, $fieldA->fresh()->approval_status);
        $this->assertSame($categoryA->id, $profile->fresh()->category_id);
        $this->assertSame([$categoryA->id], $profile->approvedTeachingCategories()->pluck('categories.id')->map(fn ($id) => (int) $id)->all());
        $this->assertSame([$categoryA->id], $instructor->fresh()->getTeachingCategories()->pluck('id')->map(fn ($id) => (int) $id)->all());
        $this->assertTrue(app(InstructorCourseCategoryAccess::class)->canTeachCategory($instructor, $categoryA->id));
        $this->assertFalse(app(InstructorCourseCategoryAccess::class)->canTeachCategory($instructor, $categoryB->id));

        $this->certificate($instructor, $replacement, $requirementB, 'draft');
        $this->actingAs($instructor)
            ->post(route('instructor.profile.teaching-fields.submit-review', $replacement))
            ->assertRedirect();

        $this->assertSame(InstructorTeachingField::STATUS_APPROVED, $fieldA->fresh()->approval_status);
        $this->assertSame(InstructorTeachingField::STATUS_PENDING, $replacement->fresh()->approval_status);
        $this->assertSame($categoryA->id, $profile->fresh()->category_id);
        $this->assertDatabaseHas('users', ['id' => $instructor->id, 'instructor_status' => 'approved']);

        $this->actingAs($admin)
            ->post(route('admin.instructors.teaching-fields.approve', $replacement))
            ->assertRedirect();

        $this->assertSame(InstructorTeachingField::STATUS_SUPERSEDED, $fieldA->fresh()->approval_status);
        $this->assertSame(InstructorTeachingField::STATUS_APPROVED, $replacement->fresh()->approval_status);
        $this->assertSame($categoryB->id, $profile->fresh()->category_id);
        $this->assertFalse(app(InstructorCourseCategoryAccess::class)->canTeachCategory($instructor, $categoryA->id));
        $this->assertTrue(app(InstructorCourseCategoryAccess::class)->canTeachCategory($instructor, $categoryB->id));
        $this->assertDatabaseHas('users', ['id' => $instructor->id, 'instructor_status' => 'approved']);
    }

    public function test_profile_displays_all_teaching_field_approval_statuses(): void
    {
        [$instructor, , $fieldA, $fieldB] = $this->fixture();
        $profile = $fieldA->profile;

        $fieldB->update(['approval_status' => InstructorTeachingField::STATUS_DRAFT]);
        foreach ([
            InstructorTeachingField::STATUS_PENDING => 'Pending field',
            InstructorTeachingField::STATUS_REJECTED => 'Rejected field',
            InstructorTeachingField::STATUS_SUPERSEDED => 'Superseded field',
        ] as $status => $name) {
            $category = Category::create(['name' => $name, 'slug' => str($name)->slug()->value(), 'status' => true]);
            InstructorTeachingField::create([
                'instructor_profile_id' => $profile->id,
                'category_id' => $category->id,
                'approval_status' => $status,
                'rejection_reason' => $status === InstructorTeachingField::STATUS_REJECTED ? 'Hồ sơ chưa đạt yêu cầu.' : null,
            ]);
        }

        $this->actingAs($instructor)
            ->get(route('instructor.profile'))
            ->assertOk()
            ->assertSee('✅ Đã duyệt')
            ->assertSee('📝 Chưa gửi')
            ->assertSee('⏳ Chờ duyệt')
            ->assertSee('❌ Bị từ chối')
            ->assertSee('⛔ Đã thay thế');
    }

    public function test_approved_field_with_missing_requirement_can_upload_draft_supplement(): void
    {
        Storage::fake('local');
        [$instructor, , $fieldA, , $categoryA] = $this->fixture();
        $requirement = $this->requirement($categoryA, 'Chứng chỉ bổ sung A');

        $this->actingAs($instructor)
            ->get(route('instructor.profile'))
            ->assertOk()
            ->assertSee('Thiếu 1 tài liệu bắt buộc')
            ->assertSee("activeTeachingFieldId = {$fieldA->id}; activeRequirementId = {$requirement->id}", false)
            ->assertSee('<span>Tải lên</span>', false);

        $this->actingAs($instructor)
            ->from(route('instructor.profile'))
            ->post(route('instructor.profile.documents.upload'), [
                'instructor_teaching_field_id' => $fieldA->id,
                'requirement_id' => $requirement->id,
                'file' => UploadedFile::fake()->create('supplement-a.pdf', 100, 'application/pdf'),
            ])
            ->assertRedirect(route('instructor.profile'))
            ->assertSessionHasNoErrors();

        $certificate = InstructorCertificate::query()
            ->where('user_id', $instructor->id)
            ->where('instructor_teaching_field_id', $fieldA->id)
            ->where('requirement_id', $requirement->id)
            ->firstOrFail();

        $this->assertSame('draft', $certificate->status);
        $this->assertSame(InstructorTeachingField::STATUS_APPROVED, $fieldA->fresh()->approval_status);
        $this->assertDatabaseHas('users', ['id' => $instructor->id, 'instructor_status' => 'approved']);
        $this->assertDatabaseCount('push_notifications', 0);
    }

    public function test_approved_field_submits_draft_supplement_without_losing_approval(): void
    {
        [$instructor, $admin, $fieldA, , $categoryA] = $this->fixture();
        $requirement = $this->requirement($categoryA, 'Giấy xác nhận bổ sung A');
        $certificate = $this->certificate($instructor, $fieldA, $requirement, 'draft');

        $this->actingAs($instructor)
            ->get(route('instructor.profile'))
            ->assertOk()
            ->assertSee('Gửi bổ sung hồ sơ ngành này');

        $this->actingAs($instructor)
            ->post(route('instructor.profile.teaching-fields.submit-supplement', $fieldA))
            ->assertRedirect()
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success', 'Đã gửi bổ sung hồ sơ ngành này. Các tài liệu đang chờ Admin duyệt.');

        $this->assertSame('pending', $certificate->fresh()->status);
        $this->assertSame(InstructorTeachingField::STATUS_APPROVED, $fieldA->fresh()->approval_status);
        $this->assertDatabaseHas('users', ['id' => $instructor->id, 'instructor_status' => 'approved']);
        $this->assertDatabaseHas('push_notifications', [
            'user_id' => $admin->id,
            'type' => 'instructor_document_supplement_submitted',
        ]);
    }

    public function test_admin_approve_or_reject_supplement_does_not_change_approved_field(): void
    {
        [$instructor, $admin, $fieldA, , $categoryA] = $this->fixture();
        $approvedRequirement = $this->requirement($categoryA, 'Tài liệu duyệt bổ sung A');
        $rejectedRequirement = $this->requirement($categoryA, 'Tài liệu từ chối bổ sung A');
        $draftRequirement = $this->requirement($categoryA, 'Tài liệu chưa gửi A');
        $approvedDocument = $this->certificate($instructor, $fieldA, $approvedRequirement, 'pending');
        $rejectedDocument = $this->certificate($instructor, $fieldA, $rejectedRequirement, 'pending');
        $draftDocument = $this->certificate($instructor, $fieldA, $draftRequirement, 'draft');

        $this->actingAs($admin)
            ->post(route('admin.instructors.applications.documents.review', [$instructor, $approvedDocument]), [
                'status' => 'approved',
            ])
            ->assertRedirect();
        $this->assertSame('approved', $approvedDocument->fresh()->status);
        $this->assertSame(InstructorTeachingField::STATUS_APPROVED, $fieldA->fresh()->approval_status);

        $this->actingAs($admin)
            ->post(route('admin.instructors.applications.documents.review', [$instructor, $rejectedDocument]), [
                'status' => 'rejected',
                'rejection_reason' => 'Tài liệu bổ sung chưa hợp lệ.',
            ])
            ->assertRedirect();
        $this->assertSame('rejected', $rejectedDocument->fresh()->status);
        $this->assertSame(InstructorTeachingField::STATUS_APPROVED, $fieldA->fresh()->approval_status);

        $this->actingAs($admin)
            ->post(route('admin.instructors.applications.documents.review', [$instructor, $draftDocument]), [
                'status' => 'approved',
            ])
            ->assertStatus(422);
        $this->assertSame('draft', $draftDocument->fresh()->status);
        $this->assertSame(InstructorTeachingField::STATUS_APPROVED, $fieldA->fresh()->approval_status);
        $this->assertDatabaseHas('users', ['id' => $instructor->id, 'instructor_status' => 'approved']);
    }

    public function test_rejected_supplement_history_is_preserved_when_replacement_is_uploaded(): void
    {
        Storage::fake('local');
        [$instructor, , $fieldA, , $categoryA] = $this->fixture();
        $requirement = $this->requirement($categoryA, 'Tài liệu cần thay thế A');
        $rejected = $this->certificate($instructor, $fieldA, $requirement, 'rejected');
        $rejected->update(['rejection_reason' => 'Ảnh tài liệu bị mờ.']);

        $this->actingAs($instructor)
            ->get(route('instructor.profile'))
            ->assertOk()
            ->assertSee('Tải file thay thế');

        $this->actingAs($instructor)
            ->post(route('instructor.profile.documents.upload'), [
                'instructor_teaching_field_id' => $fieldA->id,
                'requirement_id' => $requirement->id,
                'file' => UploadedFile::fake()->create('replacement-a.pdf', 100, 'application/pdf'),
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('instructor_certificates', [
            'id' => $rejected->id,
            'status' => 'rejected',
            'rejection_reason' => 'Ảnh tài liệu bị mờ.',
        ]);
        $this->assertDatabaseHas('instructor_certificates', [
            'user_id' => $instructor->id,
            'instructor_teaching_field_id' => $fieldA->id,
            'requirement_id' => $requirement->id,
            'status' => 'draft',
            'original_name' => 'replacement-a.pdf',
        ]);
        $this->assertSame(2, InstructorCertificate::query()
            ->where('instructor_teaching_field_id', $fieldA->id)
            ->where('requirement_id', $requirement->id)
            ->count());
        $this->assertSame(InstructorTeachingField::STATUS_APPROVED, $fieldA->fresh()->approval_status);
    }

    private function fixture(): array
    {
        $instructor = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'approved', 'email_verified_at' => now()]);
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $categoryA = Category::create(['name' => 'Mạng máy tính', 'slug' => 'mang-may-tinh', 'status' => true]);
        $categoryB = Category::create(['name' => 'Điện toán đám mây', 'slug' => 'dien-toan-dam-may', 'status' => true]);
        $profile = InstructorProfile::create(['user_id' => $instructor->id, 'category_id' => $categoryA->id]);
        $fieldA = InstructorTeachingField::create(['instructor_profile_id' => $profile->id, 'category_id' => $categoryA->id, 'is_primary' => true, 'approval_status' => 'approved']);
        $fieldB = InstructorTeachingField::create(['instructor_profile_id' => $profile->id, 'category_id' => $categoryB->id, 'approval_status' => 'draft']);
        $requirementB = InstructorDocumentRequirement::create(['category_id' => $categoryB->id, 'document_type' => 'degree', 'document_title' => 'Bằng chuyên môn B', 'is_required' => true, 'is_active' => true]);

        return [$instructor, $admin, $fieldA, $fieldB, $categoryA, $categoryB, $requirementB];
    }

    private function certificate(User $user, InstructorTeachingField $field, InstructorDocumentRequirement $requirement, string $status): InstructorCertificate
    {
        return InstructorCertificate::create([
            'user_id' => $user->id, 'instructor_teaching_field_id' => $field->id, 'requirement_id' => $requirement->id,
            'file_path' => 'testing/'.$field->id.'-'.$status.'.pdf', 'original_name' => 'proof.pdf', 'title' => 'Proof',
            'document_type' => 'degree', 'status' => $status, 'uploaded_at' => now(),
        ]);
    }

    private function requirement(Category $category, string $title): InstructorDocumentRequirement
    {
        return InstructorDocumentRequirement::create([
            'category_id' => $category->id,
            'document_type' => 'degree',
            'document_title' => $title,
            'is_required' => true,
            'is_active' => true,
        ]);
    }

    private function assertCourseDenied(User $instructor, Category $category): void
    {
        $this->actingAs($instructor)->post(route('instructor.courses.store'), $this->coursePayload($category))->assertSessionHasErrors('category_id');
    }

    private function assertCourseAllowed(User $instructor, Category $category, ?Course $existing = null): void
    {
        if ($existing) {
            $this->actingAs($instructor)->get(route('instructor.courses.edit', $existing))->assertOk();

            return;
        }
        $this->actingAs($instructor)->post(route('instructor.courses.store'), $this->coursePayload($category, 'Khóa học hợp lệ '.$category->id))->assertRedirect();
    }

    private function coursePayload(Category $category, string $title = 'Khóa học bị chặn'): array
    {
        return ['title' => $title, 'category_id' => $category->id, 'price' => 100000, 'level' => 'beginner', 'language' => 'vi'];
    }

    private function course(User $instructor, Category $category): Course
    {
        return Course::create(['instructor_id' => $instructor->id, 'category_id' => $category->id, 'title' => 'Khóa A', 'slug' => 'khoa-a-'.uniqid(), 'price' => 100000, 'language' => 'vi', 'level' => 'beginner', 'status' => Course::STATUS_DRAFT]);
    }
}
