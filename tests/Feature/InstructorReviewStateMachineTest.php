<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\InstructorApplication;
use App\Models\InstructorCertificate;
use App\Models\InstructorDocumentRequirement;
use App\Models\InstructorProfile;
use App\Models\InstructorTeachingField;
use App\Models\PushNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class InstructorReviewStateMachineTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Category $categoryA;

    private Category $categoryB;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Storage::fake('public');
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $this->categoryA = $this->category('State machine A');
        $this->categoryB = $this->category('State machine B');
    }

    public function test_first_global_submit_creates_application_and_notifies_admin_only_once(): void
    {
        [$instructor, , $field] = $this->draftInstructor();

        $this->actingAs($instructor)->post(route('instructor.profile.submit-review'))
            ->assertRedirect()->assertSessionHas('success');

        $this->assertDatabaseHas('instructor_applications', ['user_id' => $instructor->id, 'status' => 'pending']);
        $this->assertSame(InstructorTeachingField::STATUS_DRAFT, $field->fresh()->approval_status);
        $this->assertSame(1, PushNotification::query()
            ->where('user_id', $this->admin->id)
            ->where('type', 'instructor_application_submitted')->count());

        $this->actingAs($instructor)->post(route('instructor.profile.submit-review'))
            ->assertRedirect()->assertSessionHas('error');
        $this->assertSame(1, PushNotification::query()
            ->where('user_id', $this->admin->id)
            ->where('type', 'instructor_application_submitted')->count());
    }

    public function test_global_submit_requires_a_verified_email_and_existing_cv_file(): void
    {
        [$missingCv] = $this->draftInstructor(withCv: false);
        $this->actingAs($missingCv)->post(route('instructor.profile.submit-review'))
            ->assertRedirect()->assertSessionHas('error');
        $this->assertDatabaseMissing('instructor_applications', ['user_id' => $missingCv->id]);

        [$missingFile, $profile] = $this->draftInstructor();
        Storage::disk('public')->delete($profile->cv);
        $this->actingAs($missingFile)->post(route('instructor.profile.submit-review'))
            ->assertRedirect()->assertSessionHas('error');
        $this->assertNull($missingFile->fresh()->submitted_for_review_at);
    }

    public function test_global_pending_locks_profile_cv_and_teaching_fields_on_backend(): void
    {
        [$instructor, , $field] = $this->draftInstructor();
        $this->actingAs($instructor)->post(route('instructor.profile.submit-review'));

        $this->actingAs($instructor)->put(route('instructor.profile.update'), [
            'name' => 'Tên bị sửa trong lúc pending',
            'username' => $instructor->username,
            'category_ids' => [$this->categoryB->id],
            'teaching_fields' => [[
                'teaching_field_id' => $field->id,
                'category_id' => $this->categoryB->id,
            ]],
            'cv' => UploadedFile::fake()->create('new-cv.pdf', 100, 'application/pdf'),
        ])->assertRedirect()->assertSessionHasErrors('profile');

        $this->assertNotSame('Tên bị sửa trong lúc pending', $instructor->fresh()->name);
        $this->assertSame($this->categoryA->id, $field->fresh()->category_id);
    }

    public function test_global_pending_blocks_document_upload_and_draft_deletion(): void
    {
        [$instructor] = $this->draftInstructor();
        $draft = InstructorCertificate::create([
            'user_id' => $instructor->id,
            'document_type' => 'other',
            'title' => 'Unassigned draft',
            'source_type' => 'url',
            'document_url' => 'https://example.com/draft',
            'status' => 'draft',
            'uploaded_at' => now(),
        ]);
        $this->actingAs($instructor)->post(route('instructor.profile.submit-review'));

        $this->actingAs($instructor)->post(route('instructor.profile.documents.upload'), [
            'source_type' => 'url',
            'document_url' => 'https://example.com/new',
            'document_type' => 'other',
        ])->assertSessionHasErrors('documents');
        $this->actingAs($instructor)->delete(route('instructor.profile.documents.delete', $draft))->assertStatus(409);

        $this->assertSame('draft', $draft->fresh()->status);
        $this->assertSame(1, $instructor->instructorCertificates()->count());
    }

    public function test_initial_draft_category_replacement_updates_a_to_b_without_creating_a_plus_b(): void
    {
        [$instructor, , $field] = $this->draftInstructor();

        $this->actingAs($instructor)->put(route('instructor.profile.update'), [
            'name' => $instructor->name,
            'username' => $instructor->username,
            'category_ids' => [$this->categoryB->id],
            'teaching_fields' => [[
                'replace_of_teaching_field_id' => $field->id,
                'category_id' => $this->categoryB->id,
                'specialty' => 'Ngành B',
            ]],
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertSame(1, InstructorTeachingField::query()->where('instructor_profile_id', $field->instructor_profile_id)->count());
        $this->assertDatabaseHas('instructor_profile_teaching_fields', [
            'id' => $field->id,
            'category_id' => $this->categoryB->id,
            'approval_status' => 'draft',
        ]);
    }

    public function test_pending_individual_field_cannot_be_silently_edited_by_profile_save(): void
    {
        [$instructor, $profile, $approvedField] = $this->approvedInstructor();
        $pendingField = InstructorTeachingField::create([
            'instructor_profile_id' => $profile->id,
            'category_id' => $this->categoryB->id,
            'approval_status' => 'pending',
            'organization' => 'Original organization',
            'submitted_at' => now(),
        ]);

        $this->actingAs($instructor)->put(route('instructor.profile.update'), [
            'name' => $instructor->name,
            'username' => $instructor->username,
            'category_ids' => [$this->categoryA->id, $this->categoryB->id],
            'teaching_fields' => [
                ['teaching_field_id' => $approvedField->id, 'category_id' => $this->categoryA->id],
                ['teaching_field_id' => $pendingField->id, 'category_id' => $this->categoryB->id, 'organization' => 'Tampered'],
            ],
        ])->assertSessionHasErrors('teaching_fields');

        $this->assertSame('Original organization', $pendingField->fresh()->organization);
    }

    public function test_unapproved_instructor_cannot_use_individual_field_review_flow(): void
    {
        [$instructor, , $field] = $this->draftInstructor();

        $this->actingAs($instructor)
            ->post(route('instructor.profile.teaching-fields.submit-review', $field))
            ->assertRedirect()->assertSessionHas('error');

        $this->assertSame('draft', $field->fresh()->approval_status);
    }

    public function test_admin_cannot_approve_or_reject_an_unsubmitted_global_draft(): void
    {
        [$instructor] = $this->draftInstructor();

        $this->actingAs($this->admin)->post(route('admin.instructors.applications.approve', $instructor))
            ->assertRedirect()->assertSessionHas('error');
        $this->actingAs($this->admin)->post(route('admin.instructors.applications.reject', $instructor), [
            'rejected_reason' => 'Hồ sơ chưa được giảng viên chính thức gửi xét duyệt.',
        ])->assertRedirect()->assertSessionHas('error');

        $this->assertTrue($instructor->fresh()->canSubmitInitialInstructorReview());
    }

    public function test_global_rejection_rejects_pending_package_documents_and_preserves_rows(): void
    {
        [$instructor, , $field] = $this->draftInstructor();
        $requirement = $this->requirement($this->categoryA);
        $document = $this->document($instructor, $field, $requirement, 'draft');
        $this->actingAs($instructor)->post(route('instructor.profile.submit-review'));

        $this->actingAs($this->admin)->post(route('admin.instructors.applications.reject', $instructor), [
            'rejected_reason' => 'Minh chứng chuyên môn chưa đáp ứng tiêu chuẩn xét duyệt.',
        ])->assertRedirect()->assertSessionHas('success');

        $this->assertSame('rejected', $instructor->fresh()->instructor_status);
        $this->assertSame('rejected', $document->fresh()->status);
        $this->assertSame('rejected', $instructor->instructorApplication->fresh()->status);
    }

    public function test_field_rejection_rejects_all_pending_scoped_documents_only(): void
    {
        [$instructor, $profile, $approvedField] = $this->approvedInstructor();
        $pendingField = InstructorTeachingField::create([
            'instructor_profile_id' => $profile->id,
            'category_id' => $this->categoryB->id,
            'approval_status' => 'pending',
            'submitted_at' => now(),
        ]);
        $requirement = $this->requirement($this->categoryB);
        $pendingDocument = $this->document($instructor, $pendingField, $requirement, 'pending');
        $approvedHistory = $this->document($instructor, $approvedField, $this->requirement($this->categoryA), 'approved');

        $this->actingAs($this->admin)->post(route('admin.instructors.teaching-fields.reject', $pendingField), [
            'rejection_reason' => 'Tài liệu ngành mới chưa chứng minh đủ chuyên môn.',
        ])->assertRedirect();

        $this->assertSame('rejected', $pendingField->fresh()->approval_status);
        $this->assertSame('rejected', $pendingDocument->fresh()->status);
        $this->assertSame('approved', $approvedHistory->fresh()->status);
    }

    public function test_supplement_submit_is_idempotent_and_uses_dedicated_admin_queue(): void
    {
        [$instructor, , $field] = $this->approvedInstructor();
        $requirement = $this->requirement($this->categoryA);
        $document = $this->document($instructor, $field, $requirement, 'draft');

        $this->actingAs($instructor)->post(route('instructor.profile.teaching-fields.submit-supplement', $field))
            ->assertRedirect()->assertSessionHas('success');
        $this->actingAs($instructor)->post(route('instructor.profile.teaching-fields.submit-supplement', $field))
            ->assertRedirect()->assertSessionHas('error');

        $this->assertSame('pending', $document->fresh()->status);
        $this->assertSame(1, PushNotification::query()->where('type', 'instructor_document_supplement_submitted')->count());
        $this->actingAs($this->admin)->get(route('admin.instructors.supplements.index'))
            ->assertOk()->assertSee($document->title);
    }

    public function test_requirement_configuration_is_frozen_while_global_review_is_pending(): void
    {
        [$instructor] = $this->draftInstructor();
        $this->actingAs($instructor)->post(route('instructor.profile.submit-review'));

        $payload = [
            'category_id' => $this->categoryA->id,
            'document_type' => 'degree',
            'document_title' => 'Requirement created during review',
            'is_required' => true,
        ];
        $this->actingAs($this->admin)->post(route('admin.instructors.requirements.store'), $payload)->assertStatus(409);

        $this->actingAs($this->admin)->post(route('admin.instructors.applications.reject', $instructor), [
            'rejected_reason' => 'Mở lại hồ sơ để kiểm tra requirement mới.',
        ]);
        $this->actingAs($this->admin)->post(route('admin.instructors.requirements.store'), $payload)->assertRedirect();
        $this->assertDatabaseHas('instructor_document_requirements', ['document_title' => $payload['document_title']]);
    }

    public function test_global_pending_queue_excludes_registered_but_unsubmitted_instructor(): void
    {
        [$draft] = $this->draftInstructor(['name' => 'Unsubmitted State Machine']);
        [$queued] = $this->draftInstructor(['name' => 'Queued State Machine']);
        $this->actingAs($queued)->post(route('instructor.profile.submit-review'));

        $this->actingAs($this->admin)->get(route('admin.instructors.applications.index', ['status' => 'pending']))
            ->assertOk()
            ->assertSee($queued->name)
            ->assertDontSee($draft->name);
    }

    public function test_legacy_resubmit_route_uses_same_canonical_transition_and_notification_type(): void
    {
        [$instructor, $profile] = $this->draftInstructor(['instructor_status' => 'rejected']);
        InstructorApplication::create([
            'user_id' => $instructor->id,
            'status' => 'rejected',
            'cv_path' => $profile->cv,
        ]);

        $this->actingAs($instructor)->post(route('instructor.resubmit'))
            ->assertRedirect()->assertSessionHas('success');

        $this->assertTrue($instructor->fresh()->isGlobalReviewPending());
        $this->assertSame('pending', $instructor->instructorApplication->fresh()->status);
        $this->assertSame(1, PushNotification::query()->where('type', 'instructor_application_resubmitted')->count());
    }

    public function test_global_approval_promotes_only_initial_drafts_not_an_arbitrary_pending_field(): void
    {
        [$instructor, $profile, $initialField] = $this->draftInstructor();
        $independentPending = InstructorTeachingField::create([
            'instructor_profile_id' => $profile->id,
            'category_id' => $this->categoryB->id,
            'approval_status' => 'pending',
            'submitted_at' => now(),
        ]);
        $this->actingAs($instructor)->post(route('instructor.profile.submit-review'));

        $this->actingAs($this->admin)->post(route('admin.instructors.applications.approve', $instructor))
            ->assertRedirect()->assertSessionHas('success');

        $this->assertSame('approved', $initialField->fresh()->approval_status);
        $this->assertSame('pending', $independentPending->fresh()->approval_status);
    }

    /** @return array{User, InstructorProfile, InstructorTeachingField} */
    private function draftInstructor(array $userAttributes = [], bool $withCv = true): array
    {
        $user = User::factory()->create(array_merge([
            'role' => 'instructor',
            'username' => 'state_user_'.Str::lower(Str::random(10)),
            'instructor_status' => 'pending',
            'email_verified_at' => now(),
            'is_active' => true,
        ], $userAttributes));
        $cvPath = $withCv ? "instructor_cvs/{$user->id}.pdf" : null;
        if ($cvPath) {
            Storage::disk('public')->put($cvPath, 'pdf');
        }
        $profile = InstructorProfile::create([
            'user_id' => $user->id,
            'category_id' => $this->categoryA->id,
            'specialty' => 'Chuyên môn A',
            'experience' => 'Năm năm kinh nghiệm',
            'bio' => 'Giới thiệu giảng viên',
            'cv' => $cvPath,
        ]);
        $field = InstructorTeachingField::create([
            'instructor_profile_id' => $profile->id,
            'category_id' => $this->categoryA->id,
            'approval_status' => 'draft',
            'is_primary' => true,
        ]);

        return [$user, $profile, $field];
    }

    /** @return array{User, InstructorProfile, InstructorTeachingField} */
    private function approvedInstructor(): array
    {
        [$user, $profile, $field] = $this->draftInstructor(['instructor_status' => 'approved']);
        $field->update(['approval_status' => 'approved']);

        return [$user, $profile, $field->fresh()];
    }

    private function category(string $name): Category
    {
        return Category::create([
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::random(6),
            'status' => true,
        ]);
    }

    private function requirement(Category $category): InstructorDocumentRequirement
    {
        return InstructorDocumentRequirement::create([
            'category_id' => $category->id,
            'document_type' => 'degree',
            'document_title' => 'Bằng cấp '.Str::random(6),
            'is_required' => true,
            'is_active' => true,
        ]);
    }

    private function document(User $user, InstructorTeachingField $field, InstructorDocumentRequirement $requirement, string $status): InstructorCertificate
    {
        return InstructorCertificate::create([
            'user_id' => $user->id,
            'instructor_teaching_field_id' => $field->id,
            'requirement_id' => $requirement->id,
            'document_type' => $requirement->document_type,
            'title' => $requirement->document_title,
            'source_type' => 'url',
            'document_url' => 'https://example.com/'.Str::random(8),
            'status' => $status,
            'uploaded_at' => now(),
        ]);
    }
}
