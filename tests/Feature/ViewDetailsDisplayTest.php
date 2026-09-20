<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Chapter;
use App\Models\Coupon;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\InstructorProfile;
use App\Models\Lesson;
use App\Models\User;
use App\Models\UserCoupon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewDetailsDisplayTest extends TestCase
{
    use RefreshDatabase;

    private function createInstructor(array $attributes = [], ?Category $category = null): User
    {
        $instructor = User::factory()->create(array_merge([
            'role' => 'instructor',
            'instructor_status' => 'approved',
            'is_active' => true,
            'email_verified_at' => now(),
        ], $attributes));

        $profile = InstructorProfile::create([
            'user_id' => $instructor->id,
            'position' => 'Chuyên gia Kỹ thuật Phần mềm',
            'specialty' => 'Laravel & Vue.js',
            'experience' => '10 năm kinh nghiệm phát triển hệ thống lớn',
            'bio' => 'Giới thiệu chi tiết về giảng viên thử nghiệm.',
            'organization' => 'Công ty Công nghệ OnlineFEA',
        ]);

        if ($category) {
            $profile->teachingCategories()->attach($category->id, [
                'is_primary' => true,
                'approval_status' => 'approved',
            ]);
        }

        return $instructor;
    }

    public function test_student_can_view_published_course_detail_with_instructor_profile(): void
    {
        $category = Category::create(['name' => 'Lập trình', 'slug' => 'lap-trinh', 'status' => true]);
        $instructor = $this->createInstructor(['name' => 'Nguyễn Văn Giảng Viên'], $category);

        $course = Course::create([
            'title' => 'Khóa học Lập trình Web Hiện Đại',
            'slug' => 'khoa-hoc-lap-trinh-web-hien-dai',
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'short_description' => 'Mô tả ngắn khóa học',
            'description' => 'Mô tả chi tiết nội dung khóa học',
            'price' => 500000,
            'discount_price' => 350000,
            'status' => Course::STATUS_PUBLISHED,
            'is_published' => true,
            'published_at' => now(),
            'level' => 'intermediate',
            'language' => 'vi',
        ]);

        $section = CourseSection::create([
            'course_id' => $course->id,
            'title' => 'Chương 1: Giới thiệu',
            'sort_order' => 1,
        ]);

        Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Bài 1: Khởi động dự án',
            'type' => 'video',
            'duration_seconds' => 300,
            'is_preview' => true,
            'sort_order' => 1,
        ]);

        $response = $this->get(route('courses.show', $course->slug));

        $response->assertOk();
        $response->assertSee('Khóa học Lập trình Web Hiện Đại');
        $response->assertSee('Nguyễn Văn Giảng Viên');
        $response->assertSee('Chuyên gia Kỹ thuật Phần mềm');
        $response->assertSee('Laravel & Vue.js');
        $response->assertSee('10 năm kinh nghiệm phát triển hệ thống lớn');
        $response->assertSee('Xem chi tiết giảng viên');
        $response->assertSee(route('instructors.show', $instructor));
    }

    public function test_instructor_public_profile_shows_full_details_and_courses(): void
    {
        $category = Category::create(['name' => 'Cơ khí', 'slug' => 'co-khi', 'status' => true]);
        $instructor = $this->createInstructor(['name' => 'Trần Văn Giảng Viên'], $category);

        $course = Course::create([
            'title' => 'Khóa học Mô phỏng ANSYS FEA',
            'slug' => 'khoa-hoc-mo-phong-ansys-fea',
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'short_description' => 'Mô tả FEA',
            'description' => 'Mô tả chi tiết FEA',
            'price' => 800000,
            'status' => Course::STATUS_PUBLISHED,
            'is_published' => true,
            'published_at' => now(),
            'level' => 'advanced',
            'language' => 'vi',
        ]);

        $response = $this->get(route('instructors.show', $instructor));

        $response->assertOk();
        $response->assertSee('Trần Văn Giảng Viên');
        $response->assertSee('Chuyên gia Kỹ thuật Phần mềm');
        $response->assertSee('Laravel & Vue.js');
        $response->assertSee('10 năm kinh nghiệm phát triển hệ thống lớn');
        $response->assertSee('Khóa học Mô phỏng ANSYS FEA');
        $response->assertSee(route('courses.show', $course->slug));
    }

    public function test_instructor_can_view_own_course_detail_but_forbidden_for_other_instructor(): void
    {
        $category = Category::create(['name' => 'Điện tử', 'slug' => 'dien-tu', 'status' => true]);
        $instructor1 = $this->createInstructor(['name' => 'Giảng viên 1'], $category);
        $instructor2 = $this->createInstructor(['name' => 'Giảng viên 2'], $category);

        $course = Course::create([
            'title' => 'Khóa học Thiết kế Mạch Điện',
            'slug' => 'khoa-hoc-thiet-ke-mach-dien',
            'instructor_id' => $instructor1->id,
            'category_id' => $category->id,
            'description' => 'Chi tiết khóa mạch điện',
            'price' => 300000,
            'status' => Course::STATUS_DRAFT,
            'is_published' => false,
            'level' => 'beginner',
            'language' => 'vi',
        ]);

        $chapter = Chapter::create([
            'course_id' => $course->id,
            'title' => 'Chương 1: Khái niệm cơ bản',
            'sort_order' => 1,
        ]);

        Lesson::create([
            'course_id' => $course->id,
            'chapter_id' => $chapter->id,
            'title' => 'Bài 1: Định luật Ohm',
            'type' => 'video',
            'duration_seconds' => 600,
            'sort_order' => 1,
        ]);

        // Giảng viên sở hữu xem được chi tiết
        $response1 = $this->actingAs($instructor1)->get(route('instructor.courses.show', $course));
        $response1->assertOk();
        $response1->assertSee('Khóa học Thiết kế Mạch Điện');
        $response1->assertSee('Chương 1: Khái niệm cơ bản');
        $response1->assertSee('Bài 1: Định luật Ohm');
        $response1->assertSee('Bản nháp');

        // Giảng viên khác bị 403 Forbidden
        $response2 = $this->actingAs($instructor2)->get(route('instructor.courses.show', $course));
        $response2->assertStatus(403);
    }

    public function test_admin_can_view_any_course_detail(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $category = Category::create(['name' => 'Kinh doanh', 'slug' => 'kinh-doanh', 'status' => true]);
        $instructor = $this->createInstructor(['name' => 'Giảng viên A'], $category);

        $course = Course::create([
            'title' => 'Khóa học Quản trị Kinh doanh',
            'slug' => 'khoa-hoc-quan-tri-kinh-doanh',
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'description' => 'Mô tả chi tiết',
            'price' => 1000000,
            'status' => Course::STATUS_PENDING,
            'is_published' => false,
            'level' => 'intermediate',
            'language' => 'vi',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.courses.show', $course));

        $response->assertOk();
        $response->assertSee('Khóa học Quản trị Kinh doanh');
        $response->assertSee('Giảng viên A');
        $response->assertSee('Ngày cập nhật');
    }

    public function test_instructor_can_view_own_coupon_detail_but_forbidden_for_other_instructor(): void
    {
        $instructor1 = $this->createInstructor();
        $instructor2 = $this->createInstructor();

        $coupon = Coupon::create([
            'code' => 'DISCOUNT50',
            'creator_type' => 'instructor',
            'instructor_id' => $instructor1->id,
            'type' => 'percent',
            'value' => 50,
            'min_order_amount' => 100000,
            'max_uses' => 100,
            'used_count' => 5,
            'is_active' => true,
            'is_private' => false,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addDays(30),
        ]);

        // Giảng viên tạo mã xem được
        $response1 = $this->actingAs($instructor1)->get(route('instructor.coupons.show', $coupon));
        $response1->assertOk();
        $response1->assertSee('DISCOUNT50');
        $response1->assertSee('50%');
        $response1->assertSee('Đang hoạt động');

        // Giảng viên khác bị 403
        $response2 = $this->actingAs($instructor2)->get(route('instructor.coupons.show', $coupon));
        $response2->assertStatus(403);
    }

    public function test_admin_can_view_any_coupon_detail(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $instructor = $this->createInstructor();

        $coupon = Coupon::create([
            'code' => 'ADMINPROMO',
            'creator_type' => 'admin',
            'type' => 'fixed',
            'value' => 50000,
            'min_order_amount' => 200000,
            'max_uses' => 50,
            'used_count' => 10,
            'is_active' => true,
            'is_private' => false,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addDays(15),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.coupons.show', $coupon));

        $response->assertOk();
        $response->assertSee('ADMINPROMO');
        $response->assertSee('50.000đ');
        $response->assertSee('Mã của Hệ thống / Admin');
    }

    public function test_student_can_view_accessible_voucher_detail(): void
    {
        $student = User::factory()->create(['role' => 'student', 'email_verified_at' => now()]);
        $otherStudent = User::factory()->create(['role' => 'student', 'email_verified_at' => now()]);

        // Public coupon
        $publicCoupon = Coupon::create([
            'code' => 'PUBLIC10',
            'creator_type' => 'admin',
            'type' => 'percent',
            'value' => 10,
            'min_order_amount' => 0,
            'is_active' => true,
            'is_private' => false,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addDays(10),
        ]);

        $responsePublic = $this->actingAs($student)->get(route('student.vouchers.show', $publicCoupon));
        $responsePublic->assertOk();
        $responsePublic->assertSee('PUBLIC10');
        $responsePublic->assertSee('10%');

        // Private coupon assigned to student
        $privateCoupon = Coupon::create([
            'code' => 'PRIVATE20',
            'creator_type' => 'admin',
            'type' => 'percent',
            'value' => 20,
            'min_order_amount' => 50000,
            'is_active' => true,
            'is_private' => true,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addDays(10),
        ]);

        UserCoupon::create([
            'user_id' => $student->id,
            'coupon_id' => $privateCoupon->id,
            'saved_at' => now(),
        ]);

        // Học viên được cấp xem được
        $responsePrivate1 = $this->actingAs($student)->get(route('student.vouchers.show', $privateCoupon));
        $responsePrivate1->assertOk();
        $responsePrivate1->assertSee('PRIVATE20');

        // Học viên khác không được cấp xem mã riêng tư -> 403
        $responsePrivate2 = $this->actingAs($otherStudent)->get(route('student.vouchers.show', $privateCoupon));
        $responsePrivate2->assertStatus(403);
    }
}
