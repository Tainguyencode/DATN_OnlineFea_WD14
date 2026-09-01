<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\InstructorCertificate;
use App\Models\InstructorDocumentRequirement;
use App\Models\InstructorProfile;
use App\Models\InstructorTeachingField;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
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
        $this->assertTrue(app(\App\Services\InstructorCourseCategoryAccess::class)->canTeachCategory($instructor, $categoryA->id));
        $this->assertFalse(app(\App\Services\InstructorCourseCategoryAccess::class)->canTeachCategory($instructor, $categoryB->id));
        $this->assertCourseDenied($instructor, $categoryB);
        $this->assertDatabaseHas('users', ['id' => $instructor->id, 'instructor_status' => 'approved']);
    }

    public function test_legacy_approved_profile_without_a_pivot_still_has_course_access(): void
    {
        $instructor = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'approved', 'email_verified_at' => now()]);
        $category = Category::create(['name' => 'Legacy category', 'slug' => 'legacy-category', 'status' => true]);
        InstructorProfile::create(['user_id' => $instructor->id, 'category_id' => $category->id]);

        $this->assertTrue(app(\App\Services\InstructorCourseCategoryAccess::class)->canTeachCategory($instructor, $category->id));
        $this->assertCourseAllowed($instructor, $category);
    }

    public function test_new_field_is_draft_scoped_documents_submit_and_admin_approval_grants_course_access(): void
    {
        [$instructor, $admin, $fieldA, $fieldB, $categoryA, $categoryB, $requirementB] = $this->fixture();

        $this->assertSame('approved', $fieldA->approval_status);
        $this->assertSame('draft', $fieldB->approval_status);
        $this->assertFalse(app(\App\Services\InstructorCourseCategoryAccess::class)->canTeachCategory($instructor, $categoryB->id));

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

        $this->assertTrue(app(\App\Services\InstructorRequirementService::class)->getTeachingFieldSubmitEligibility($fieldB)['can_submit']);
        $fieldC = InstructorTeachingField::create(['instructor_profile_id' => $fieldA->instructor_profile_id, 'category_id' => $categoryC->id, 'approval_status' => 'draft']);
        $this->assertFalse(app(\App\Services\InstructorRequirementService::class)->getTeachingFieldSubmitEligibility($fieldC)['can_submit']);
        $this->assertSame($fieldA->id, $fieldB->fresh()->replace_of_teaching_field_id);
        $this->assertTrue(app(\App\Services\InstructorCourseCategoryAccess::class)->canTeachCategory($instructor, $categoryA->id));

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
        $this->assertTrue(app(\App\Services\InstructorCourseCategoryAccess::class)->canTeachCategory($instructor, $categoryA->id));
        $this->assertFalse(app(\App\Services\InstructorCourseCategoryAccess::class)->canTeachCategory($instructor, $categoryB->id));

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
        $this->assertFalse(app(\App\Services\InstructorCourseCategoryAccess::class)->canTeachCategory($instructor, $categoryA->id));
        $this->assertTrue(app(\App\Services\InstructorCourseCategoryAccess::class)->canTeachCategory($instructor, $categoryB->id));
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
