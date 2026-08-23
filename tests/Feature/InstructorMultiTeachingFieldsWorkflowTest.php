<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\InstructorCertificate;
use App\Models\InstructorDocumentRequirement;
use App\Models\InstructorProfile;
use App\Models\User;
use App\Services\InstructorRequirementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class InstructorMultiTeachingFieldsWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('private');
        Storage::fake('public');
    }

    private function createCategory(string $name, ?int $parentId = null): Category
    {
        return Category::create([
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(5),
            'parent_id' => $parentId,
            'is_active' => true,
        ]);
    }

    private function createInstructor(array $userAttributes = [], array $profileAttributes = []): array
    {
        $user = User::factory()->create(array_merge([
            'email' => 'instructor_multi@test.com',
            'username' => 'instructor_multi',
            'role' => 'instructor',
            'instructor_status' => 'approved',
            'email_verified_at' => now(),
        ], $userAttributes));

        $profile = InstructorProfile::create(array_merge([
            'user_id' => $user->id,
            'phone' => '0987654321',
            'teaching_field' => 'Công nghệ thông tin',
            'specialty' => 'Lập trình Web',
            'experience' => '5 năm kinh nghiệm',
            'bio' => 'Giảng viên chuyên nghiệp',
        ], $profileAttributes));

        return [$user, $profile];
    }

    public function test_case_1_instructor_can_select_and_save_single_teaching_field()
    {
        [$user, $profile] = $this->createInstructor();
        $cat = $this->createCategory('Lập trình & Phát triển');

        $response = $this->actingAs($user)->put(route('instructor.profile.update'), [
            'name' => 'Giảng Viên A',
            'username' => 'giangvien_a',
            'category_ids' => [$cat->id],
            'specialty' => 'Laravel',
            'experience' => '3 năm',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('instructor_profile_teaching_fields', [
            'instructor_profile_id' => $profile->id,
            'category_id' => $cat->id,
            'is_primary' => true,
        ]);

        $this->assertEquals(1, $user->getTeachingCategories()->count());
        $this->assertEquals($cat->id, $user->getTeachingCategories()->first()->id);
    }

    public function test_case_2_instructor_can_select_and_save_multiple_teaching_fields()
    {
        [$user, $profile] = $this->createInstructor();
        $cat1 = $this->createCategory('Lập trình Web');
        $cat2 = $this->createCategory('An ninh mạng');
        $cat3 = $this->createCategory('Trí tuệ nhân tạo');

        $response = $this->actingAs($user)->put(route('instructor.profile.update'), [
            'name' => 'Giảng Viên B',
            'username' => 'giangvien_b',
            'category_ids' => [$cat1->id, $cat2->id, $cat3->id],
            'specialty' => 'Fullstack & AI',
            'experience' => '5 năm',
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertEquals(3, $user->getTeachingCategories()->count());
        $this->assertTrue($user->getTeachingCategories()->contains('id', $cat1->id));
        $this->assertTrue($user->getTeachingCategories()->contains('id', $cat2->id));
        $this->assertTrue($user->getTeachingCategories()->contains('id', $cat3->id));
    }

    public function test_case_3_adding_new_teaching_field_preserves_old_fields_and_documents()
    {
        [$user, $profile] = $this->createInstructor();
        $cat1 = $this->createCategory('Lập trình Web');
        $cat2 = $this->createCategory('Mobile App');

        // Chọn ngành 1 trước
        $profile->syncTeachingCategories([$cat1->id]);

        // Tạo tài liệu thuộc ngành 1
        $req1 = InstructorDocumentRequirement::create([
            'category_id' => $cat1->id,
            'document_type' => 'certificate',
            'document_title' => 'Chứng chỉ Web Master',
            'is_required' => true,
            'is_active' => true,
        ]);

        $cert = InstructorCertificate::create([
            'user_id' => $user->id,
            'instructor_profile_id' => $profile->id,
            'requirement_id' => $req1->id,
            'document_type' => 'certificate',
            'title' => 'Chứng chỉ Web Master',
            'original_name' => 'test.pdf',
            'file_path' => 'test.pdf',
            'status' => 'approved',
        ]);

        // Thêm ngành 2 vào cùng ngành 1
        $response = $this->actingAs($user)->put(route('instructor.profile.update'), [
            'name' => $user->name,
            'username' => $user->username,
            'category_ids' => [$cat1->id, $cat2->id],
        ]);

        $response->assertSessionHasNoErrors();

        // Kiểm tra cả 2 ngành đều tồn tại
        $this->assertEquals(2, $user->getTeachingCategories()->count());
        // Kiểm tra tài liệu cũ không hề bị xóa hoặc mất
        $this->assertDatabaseHas('instructor_certificates', [
            'id' => $cert->id,
            'requirement_id' => $req1->id,
        ]);
    }

    public function test_case_4_removing_one_teaching_field_only_detaches_pivot_and_does_not_delete_documents()
    {
        [$user, $profile] = $this->createInstructor();
        $cat1 = $this->createCategory('Lập trình Web');
        $cat2 = $this->createCategory('An ninh mạng');

        $profile->syncTeachingCategories([$cat1->id, $cat2->id]);

        $req1 = InstructorDocumentRequirement::create([
            'category_id' => $cat1->id,
            'document_type' => 'certificate',
            'document_title' => 'Chứng chỉ Web',
            'is_required' => true,
            'is_active' => true,
        ]);

        $cert = InstructorCertificate::create([
            'user_id' => $user->id,
            'instructor_profile_id' => $profile->id,
            'requirement_id' => $req1->id,
            'document_type' => 'certificate',
            'title' => 'Chứng chỉ Web',
            'original_name' => 'test.pdf',
            'file_path' => 'test.pdf',
            'status' => 'approved',
        ]);

        // Bỏ ngành 1, chỉ giữ lại ngành 2
        $response = $this->actingAs($user)->put(route('instructor.profile.update'), [
            'name' => $user->name,
            'username' => $user->username,
            'category_ids' => [$cat2->id],
        ]);

        $response->assertSessionHasNoErrors();

        // Ngành 1 bị bỏ khỏi pivot
        $this->assertDatabaseMissing('instructor_profile_teaching_fields', [
            'instructor_profile_id' => $profile->id,
            'category_id' => $cat1->id,
        ]);
        $this->assertDatabaseHas('instructor_profile_teaching_fields', [
            'instructor_profile_id' => $profile->id,
            'category_id' => $cat2->id,
        ]);

        // Tài liệu cũ vẫn còn nguyên trong DB
        $this->assertDatabaseHas('instructor_certificates', [
            'id' => $cert->id,
        ]);
    }

    public function test_case_5_profile_page_loads_and_passes_selected_categories_correctly()
    {
        [$user, $profile] = $this->createInstructor();
        $parent = $this->createCategory('CNTT');
        $child1 = $this->createCategory('Web Dev', $parent->id);
        $child2 = $this->createCategory('Mobile Dev', $parent->id);

        $profile->syncTeachingCategories([$child1->id, $child2->id]);

        $response = $this->actingAs($user)->get(route('instructor.profile'));

        $response->assertOk();
        $response->assertViewHas('selectedCategoryIds', [$child1->id, $child2->id]);
        $response->assertSee('Web Dev');
        $response->assertSee('Mobile Dev');
    }

    public function test_case_6_child_category_saves_exact_child_category_id()
    {
        [$user, $profile] = $this->createInstructor();
        $parent = $this->createCategory('Lập trình & Phát triển');
        $child = $this->createCategory('Phát triển Web', $parent->id);

        $response = $this->actingAs($user)->put(route('instructor.profile.update'), [
            'name' => $user->name,
            'username' => $user->username,
            'category_ids' => [$child->id],
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('instructor_profile_teaching_fields', [
            'instructor_profile_id' => $profile->id,
            'category_id' => $child->id,
        ]);
        $this->assertDatabaseMissing('instructor_profile_teaching_fields', [
            'instructor_profile_id' => $profile->id,
            'category_id' => $parent->id,
        ]);
    }

    public function test_case_7_requirements_are_evaluated_strictly_per_category_isolated()
    {
        [$user, $profile] = $this->createInstructor();
        $catA = $this->createCategory('Ngành A - Lập trình Web');
        $catB = $this->createCategory('Ngành B - An ninh mạng');

        $profile->syncTeachingCategories([$catA->id, $catB->id]);

        $reqA = InstructorDocumentRequirement::create([
            'category_id' => $catA->id,
            'document_type' => 'certificate',
            'document_title' => 'Chứng chỉ Web Developer',
            'is_required' => true,
            'is_active' => true,
        ]);

        $reqB = InstructorDocumentRequirement::create([
            'category_id' => $catB->id,
            'document_type' => 'certificate',
            'document_title' => 'Chứng chỉ CEH An ninh mạng',
            'is_required' => true,
            'is_active' => true,
        ]);

        // Giảng viên chỉ nộp tài liệu cho ngành A và đã được duyệt
        InstructorCertificate::create([
            'user_id' => $user->id,
            'instructor_profile_id' => $profile->id,
            'requirement_id' => $reqA->id,
            'document_type' => 'certificate',
            'title' => 'Chứng chỉ Web Developer',
            'original_name' => 'weba.pdf',
            'file_path' => 'weba.pdf',
            'status' => 'approved',
        ]);

        $service = app(InstructorRequirementService::class);
        $requirementData = $service->getRequirementsForInstructor($user);

        // Ngành A đủ, nhưng ngành B chưa đủ
        $this->assertFalse($requirementData['summary']['can_approve'], 'Không thể duyệt vì ngành B còn thiếu tài liệu bắt buộc.');
        $this->assertEquals(1, $requirementData['summary']['required_missing_count']);

        $catGroups = $requirementData['categories_requirements'];
        $this->assertCount(2, $catGroups);

        $groupA = collect($catGroups)->firstWhere('category.id', $catA->id);
        $groupB = collect($catGroups)->firstWhere('category.id', $catB->id);

        $this->assertTrue($groupA['summary']['has_all_required_submitted']);
        $this->assertFalse($groupB['summary']['has_all_required_submitted']);
    }

    public function test_case_8_legacy_instructor_with_single_category_id_works_seamlessly()
    {
        $cat = $this->createCategory('Data Science');
        [$user, $profile] = $this->createInstructor([], ['category_id' => $cat->id]);

        // Giả lập dữ liệu cũ chưa có trong pivot table
        \Illuminate\Support\Facades\DB::table('instructor_profile_teaching_fields')->where('instructor_profile_id', $profile->id)->delete();

        $service = app(InstructorRequirementService::class);
        $requirementData = $service->getRequirementsForInstructor($user);

        $this->assertNotNull($requirementData['category']);
        $this->assertEquals($cat->id, $requirementData['category']->id);

        // Khi load profile page vẫn trả về danh mục
        $response = $this->actingAs($user)->get(route('instructor.profile'));
        $response->assertOk();
        $response->assertSee('Data Science');
    }

    public function test_case_9_each_teaching_field_stores_independent_professional_details()
    {
        [$user, $profile] = $this->createInstructor();
        $catWeb = $this->createCategory('Phát triển Web');
        $catMarketing = $this->createCategory('Digital Marketing');

        $response = $this->actingAs($user)->put(route('instructor.profile.update'), [
            'name' => 'Nguyễn Anh Tài',
            'username' => 'nguyen_anh_tai',
            'teaching_fields' => [
                [
                    'category_id' => $catWeb->id,
                    'organization' => 'FPT Software',
                    'position' => 'Senior Frontend Engineer',
                    'specialty' => 'React, Vue, Laravel',
                    'experience' => '5 năm phát triển web',
                ],
                [
                    'category_id' => $catMarketing->id,
                    'organization' => 'Marketing Agency ABC',
                    'position' => 'Digital Marketing Lead',
                    'specialty' => 'SEO, Google Ads, Meta Ads',
                    'experience' => '3 năm chạy chiến dịch quảng cáo',
                ],
            ],
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        // Kiểm tra pivot table lưu đúng thông tin độc lập của từng ngành
        $this->assertDatabaseHas('instructor_profile_teaching_fields', [
            'instructor_profile_id' => $profile->id,
            'category_id' => $catWeb->id,
            'organization' => 'FPT Software',
            'position' => 'Senior Frontend Engineer',
            'specialty' => 'React, Vue, Laravel',
            'experience' => '5 năm phát triển web',
            'is_primary' => true,
        ]);

        $this->assertDatabaseHas('instructor_profile_teaching_fields', [
            'instructor_profile_id' => $profile->id,
            'category_id' => $catMarketing->id,
            'organization' => 'Marketing Agency ABC',
            'position' => 'Digital Marketing Lead',
            'specialty' => 'SEO, Google Ads, Meta Ads',
            'experience' => '3 năm chạy chiến dịch quảng cáo',
            'is_primary' => false,
        ]);
    }

    public function test_case_10_profile_page_returns_teaching_fields_with_pivot_details()
    {
        [$user, $profile] = $this->createInstructor();
        $cat = $this->createCategory('Thiết kế đồ họa');

        $profile->syncTeachingFields([
            [
                'category_id' => $cat->id,
                'organization' => 'Design Studio XYZ',
                'position' => 'UI/UX Designer',
                'specialty' => 'Figma, Illustrator',
                'experience' => '4 năm thiết kế UI/UX',
            ],
        ]);

        $response = $this->actingAs($user)->get(route('instructor.profile'));
        $response->assertOk();
        $response->assertViewHas('teachingFields');

        $fields = $response->viewData('teachingFields');
        $this->assertCount(1, $fields);
        $this->assertEquals($cat->id, $fields[0]['category_id']);
        $this->assertEquals('Design Studio XYZ', $fields[0]['organization']);
        $this->assertEquals('UI/UX Designer', $fields[0]['position']);
        $this->assertEquals('Figma, Illustrator', $fields[0]['specialty']);
    }
}
