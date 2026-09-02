<?php

namespace Tests\Feature;

use App\Enums\CourseStatus;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\InstructorApplication;
use App\Models\InstructorProfile;
use App\Models\Lesson;
use App\Models\User;
use App\Services\CourseReviewService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CourseApprovalInstructorApprovalWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    private function createInstructor(array $attributes = []): User
    {
        $category = Category::firstOrCreate(
            ['slug' => 'lap-trinh-web'],
            ['name' => 'Lập trình Web', 'status' => true]
        );

        $user = User::factory()->create(array_merge([
            'role' => 'instructor',
            'instructor_status' => 'pending',
            'is_active' => true,
            'email_verified_at' => now(),
        ], $attributes));

        Storage::disk('public')->put("instructor_cvs/{$user->id}.pdf", 'pdf');
        $profile = InstructorProfile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'category_id' => $category->id,
                'teaching_field' => $category->name,
                'phone' => '0987654321',
                'specialty' => 'Lập trình',
                'experience' => '3 năm kinh nghiệm',
                'bio' => 'Giảng viên',
                'agree_information' => true,
                'agree_terms' => true,
                'cv' => "instructor_cvs/{$user->id}.pdf",
            ]
        );

        if ($user->instructor_status === 'pending') {
            $user->update(['submitted_for_review_at' => now(), 'needs_admin_review' => true]);
            InstructorApplication::query()->updateOrCreate(
                ['user_id' => $user->id],
                ['status' => 'pending', 'cv_path' => $profile->cv],
            );
        }

        return $user;
    }

    private function createAdmin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }

    private function createStudent(): User
    {
        return User::factory()->create([
            'role' => 'student',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }

    private function createCourse(User $instructor, array $attributes = []): Course
    {
        $category = Category::firstOrCreate(
            ['slug' => 'lap-trinh-web'],
            ['name' => 'Lập trình Web', 'status' => true]
        );

        $course = Course::create(array_merge([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Khóa học Test '.uniqid(),
            'slug' => 'khoa-hoc-test-'.uniqid(),
            'short_description' => 'Mô tả ngắn khóa học',
            'description' => 'Mô tả chi tiết khóa học đầy đủ',
            'objectives' => 'Mục tiêu học tập',
            'target_audience' => 'Học viên',
            'requirements' => 'Kiến thức cơ bản',
            'thumbnail' => 'thumbnails/test.jpg',
            'price' => 200000,
            'level' => 'beginner',
            'language' => 'vi',
            'status' => CourseStatus::Draft->value,
            'is_published' => false,
            'copyright_agreed' => true,
            'copyright_agreed_at' => now(),
            'copyright_agreed_by' => $instructor->id,
        ], $attributes));

        $section = CourseSection::create([
            'course_id' => $course->id,
            'title' => 'Chương 1',
            'sort_order' => 1,
        ]);

        foreach (range(1, 5) as $i) {
            Lesson::create([
                'course_id' => $course->id,
                'section_id' => $section->id,
                'title' => "Bài học {$i}",
                'type' => 'video',
                'video_url' => 'https://example.com/video.mp4',
                'duration_seconds' => 360,
                'content' => 'Nội dung bài học',
                'sort_order' => $i,
                'is_required' => true,
                'status' => 'draft',
            ]);
        }

        return $course->fresh(['courseSections.lessons', 'instructor']);
    }

    /**
     * CASE 1: Instructor chưa approved + Course chưa approved -> Student không thấy.
     */
    public function test_case_1_unapproved_instructor_and_unapproved_course_not_visible_to_student(): void
    {
        $instructor = $this->createInstructor(['instructor_status' => 'pending']);
        $course = $this->createCourse($instructor, ['status' => CourseStatus::Draft->value, 'is_published' => false]);

        $this->assertFalse($course->isPublished());
        $this->assertEmpty(Course::published()->where('id', $course->id)->get());

        // Student accessing catalog
        $response = $this->get(route('courses.index'));
        $response->assertDontSee($course->title);

        // Student accessing direct URL -> 404
        $this->get(route('courses.show', $course->slug))->assertNotFound();
    }

    /**
     * CASE 2: Instructor approved + Course chưa approved -> Student không thấy.
     */
    public function test_case_2_approved_instructor_and_unapproved_course_not_visible_to_student(): void
    {
        $instructor = $this->createInstructor(['instructor_status' => 'approved']);
        $course = $this->createCourse($instructor, ['status' => CourseStatus::PendingReview->value, 'is_published' => false]);

        $this->assertFalse($course->isPublished());
        $this->assertEmpty(Course::published()->where('id', $course->id)->get());

        $response = $this->get(route('courses.index'));
        $response->assertDontSee($course->title);

        $this->get(route('courses.show', $course->slug))->assertNotFound();
    }

    /**
     * CASE 3: Instructor chưa approved + Admin approve Course -> Course approved nội bộ -> Student KHÔNG thấy.
     */
    public function test_case_3_unapproved_instructor_and_approved_course_not_visible_to_student(): void
    {
        $instructor = $this->createInstructor(['instructor_status' => 'pending']);
        $course = $this->createCourse($instructor, [
            'status' => CourseStatus::Approved->value,
            'is_published' => false,
            'approved_at' => now(),
        ]);

        $course->refresh();
        // Course content is approved internally, but not published because instructor is pending
        $this->assertEquals(CourseStatus::Approved->value, $course->status);
        $this->assertFalse($course->is_published);
        $this->assertTrue($course->isContentApproved());
        $this->assertFalse($course->isPublished());

        // Student cannot see in catalog/homepage
        $this->assertEmpty(Course::published()->where('id', $course->id)->get());
        $this->get(route('courses.index'))->assertDontSee($course->title);
        $this->get(route('home'))->assertDontSee($course->title);
    }

    /**
     * CASE 4: Sau đó Admin approve Instructor -> Course TỰ ĐỘNG xuất hiện cho Student, không cần approve Course lần nữa.
     */
    public function test_case_4_approving_instructor_automatically_publishes_previously_approved_courses(): void
    {
        $admin = $this->createAdmin();
        $instructor = $this->createInstructor(['instructor_status' => 'pending']);
        $course = $this->createCourse($instructor, [
            'status' => CourseStatus::Approved->value,
            'is_published' => false,
            'approved_at' => now(),
        ]);

        $this->assertEquals(CourseStatus::Approved->value, $course->fresh()->status);
        $this->assertFalse($course->fresh()->isPublished());

        // Step 2: Admin approves instructor application
        $this->actingAs($admin)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->post(route('admin.instructors.applications.approve', $instructor))
            ->assertRedirect(route('admin.instructors.applications.index'))
            ->assertSessionHas('success');

        $instructor->refresh();
        $course->refresh();

        // Verification: Instructor is approved, Course is AUTOMATICALLY published!
        $this->assertEquals('approved', $instructor->instructor_status);
        $this->assertEquals(CourseStatus::Published->value, $course->status);
        $this->assertTrue($course->is_published);
        $this->assertTrue($course->isPublished());

        // Verification: Student can now see the course in catalog and homepage
        $this->assertNotEmpty(Course::published()->where('id', $course->id)->get());
        $this->get(route('courses.index'))->assertSee($course->title);
        $this->get(route('courses.show', $course->slug))->assertOk();
    }

    /**
     * CASE 5: Instructor đã approved từ trước + Admin approve Course -> Course xuất hiện cho Student ngay.
     */
    public function test_case_5_approved_instructor_and_admin_approves_course_publishes_immediately(): void
    {
        $admin = $this->createAdmin();
        $instructor = $this->createInstructor(['instructor_status' => 'approved']);
        $course = $this->createCourse($instructor);

        app(CourseReviewService::class)->submitForReview($course, $instructor);
        $checklist = collect(config('course.admin_review_checklist'))->mapWithKeys(fn ($l, $k) => [$k => true])->all();
        app(CourseReviewService::class)->approve($course->fresh(), $admin, $checklist, true);

        $course->refresh();
        $this->assertEquals(CourseStatus::Published->value, $course->status);
        $this->assertTrue($course->is_published);
        $this->assertTrue($course->isPublished());

        $this->get(route('courses.index'))->assertSee($course->title);
        $this->get(route('courses.show', $course->slug))->assertOk();
    }

    /**
     * CASE 6: Instructor bị rejected + Course đã approved -> Student không thấy.
     */
    public function test_case_6_rejected_instructor_and_approved_course_not_visible_to_student(): void
    {
        $instructor = $this->createInstructor(['instructor_status' => 'rejected']);
        $course = $this->createCourse($instructor, [
            'status' => CourseStatus::Approved->value,
            'is_published' => false,
            'approved_at' => now(),
        ]);

        $this->assertFalse($course->isPublished());
        $this->assertEmpty(Course::published()->where('id', $course->id)->get());
        $this->get(route('courses.index'))->assertDontSee($course->title);
    }

    /**
     * CASE 7: Instructor approved + Course rejected -> Student không thấy.
     */
    public function test_case_7_approved_instructor_and_rejected_course_not_visible_to_student(): void
    {
        $instructor = $this->createInstructor(['instructor_status' => 'approved']);
        $course = $this->createCourse($instructor, [
            'status' => CourseStatus::Rejected->value,
            'is_published' => false,
        ]);

        $this->assertFalse($course->isPublished());
        $this->assertEmpty(Course::published()->where('id', $course->id)->get());
        $this->get(route('courses.index'))->assertDontSee($course->title);
    }

    /**
     * CASE 8: Instructor approved + Course approved -> Student thấy và truy cập bình thường.
     */
    public function test_case_8_approved_instructor_and_approved_course_fully_accessible(): void
    {
        $instructor = $this->createInstructor(['instructor_status' => 'approved']);
        $course = $this->createCourse($instructor, [
            'status' => CourseStatus::Published->value,
            'is_published' => true,
            'published_at' => now(),
            'approved_at' => now(),
        ]);

        $this->assertTrue($course->isPublished());
        $this->assertNotEmpty(Course::published()->where('id', $course->id)->get());

        $this->get(route('courses.index'))->assertSee($course->title);
        $this->get(route('courses.show', $course->slug))->assertOk();
    }

    /**
     * CASE 9: Instructor có 5 Course (2 approved, 1 pending, 1 rejected, 1 draft).
     * Khi Instructor được approve -> Chỉ 2 Course approved được public, pending/rejected/draft giữ nguyên.
     */
    public function test_case_9_multiple_courses_selective_activation_on_instructor_approval(): void
    {
        $admin = $this->createAdmin();
        $instructor = $this->createInstructor(['instructor_status' => 'pending']);

        $c1Approved = $this->createCourse($instructor, ['title' => 'C1 Approved', 'status' => CourseStatus::Approved->value, 'is_published' => false, 'approved_at' => now()]);
        $c2Approved = $this->createCourse($instructor, ['title' => 'C2 Approved', 'status' => CourseStatus::Approved->value, 'is_published' => false, 'approved_at' => now()]);
        $c3Pending = $this->createCourse($instructor, ['title' => 'C3 Pending', 'status' => CourseStatus::PendingReview->value, 'is_published' => false]);
        $c4Rejected = $this->createCourse($instructor, ['title' => 'C4 Rejected', 'status' => CourseStatus::Rejected->value, 'is_published' => false]);
        $c5Draft = $this->createCourse($instructor, ['title' => 'C5 Draft', 'status' => CourseStatus::Draft->value, 'is_published' => false]);

        // Admin approves instructor
        $this->actingAs($admin)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->post(route('admin.instructors.applications.approve', $instructor));

        $c1Approved->refresh();
        $c2Approved->refresh();
        $c3Pending->refresh();
        $c4Rejected->refresh();
        $c5Draft->refresh();

        // 2 approved courses become published
        $this->assertEquals(CourseStatus::Published->value, $c1Approved->status);
        $this->assertTrue($c1Approved->is_published);
        $this->assertTrue($c1Approved->isPublished());

        $this->assertEquals(CourseStatus::Published->value, $c2Approved->status);
        $this->assertTrue($c2Approved->is_published);
        $this->assertTrue($c2Approved->isPublished());

        // Pending stays pending
        $this->assertEquals(CourseStatus::PendingReview->value, $c3Pending->status);
        $this->assertFalse($c3Pending->is_published);
        $this->assertFalse($c3Pending->isPublished());

        // Rejected stays rejected
        $this->assertEquals(CourseStatus::Rejected->value, $c4Rejected->status);
        $this->assertFalse($c4Rejected->is_published);
        $this->assertFalse($c4Rejected->isPublished());

        // Draft stays draft
        $this->assertEquals(CourseStatus::Draft->value, $c5Draft->status);
        $this->assertFalse($c5Draft->is_published);
        $this->assertFalse($c5Draft->isPublished());

        // Public catalog check
        $publishedCourseIds = Course::published()->pluck('id')->all();
        $this->assertContains($c1Approved->id, $publishedCourseIds);
        $this->assertContains($c2Approved->id, $publishedCourseIds);
        $this->assertNotContains($c3Pending->id, $publishedCourseIds);
        $this->assertNotContains($c4Rejected->id, $publishedCourseIds);
        $this->assertNotContains($c5Draft->id, $publishedCourseIds);
    }

    /**
     * CASE 10: Course đã public + Instructor approved -> Không bị ảnh hưởng.
     */
    public function test_case_10_already_published_course_remains_active_and_unchanged(): void
    {
        $instructor = $this->createInstructor(['instructor_status' => 'approved']);
        $course = $this->createCourse($instructor, [
            'status' => CourseStatus::Published->value,
            'is_published' => true,
            'published_at' => now()->subDays(5),
            'approved_at' => now()->subDays(5),
        ]);

        $publishedAtBefore = $course->published_at;

        // Re-sync should not break anything
        $count = app(CourseReviewService::class)->syncInstructorApprovedCourses($instructor);
        $this->assertEquals(0, $count);

        $course->refresh();
        $this->assertEquals(CourseStatus::Published->value, $course->status);
        $this->assertTrue($course->is_published);
        $this->assertEquals($publishedAtBefore->toDateTimeString(), $course->published_at->toDateTimeString());
    }

    /**
     * CASE 11: Student truy cập trực tiếp URL Course chưa đủ điều kiện public (đã duyệt nội dung nhưng GV chưa duyệt) -> Chặn và hiện thông báo rõ ràng.
     */
    public function test_case_11_direct_access_to_content_approved_course_with_unapproved_instructor_shows_pending_notice(): void
    {
        $instructor = $this->createInstructor(['instructor_status' => 'pending']);
        $course = $this->createCourse($instructor, [
            'status' => CourseStatus::Approved->value,
            'is_published' => false,
            'approved_at' => now(),
        ]);

        $response = $this->get(route('courses.show', $course->slug));
        $response->assertOk();
        $response->assertSee('Khóa học đang chờ hoàn tất xét duyệt hồ sơ giảng viên.');
        $response->assertSee('Khóa học chưa sẵn sàng');
    }

    /**
     * CASE 12: Admin và Giảng viên sở hữu vẫn mở được Course chưa public để duyệt/xem nội dung.
     */
    public function test_case_12_admin_and_owner_can_view_and_review_unapproved_course(): void
    {
        $admin = $this->createAdmin();
        $instructor = $this->createInstructor(['instructor_status' => 'pending']);
        $course = $this->createCourse($instructor, ['status' => CourseStatus::PendingReview->value, 'is_published' => false]);

        // Admin can review course
        $this->actingAs($admin)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->get(route('admin.courses.review', $course))
            ->assertOk()
            ->assertSee($course->title)
            ->assertSee('Chưa duyệt hồ sơ');

        // Instructor owner can view show page
        $this->actingAs($instructor)
            ->withSession(['two_factor_passed_at' => now()->timestamp])
            ->get(route('courses.show', $course->slug))
            ->assertOk()
            ->assertSee($course->title);
    }

    /**
     * CASE 13: Không bắt Instructor gửi lại Course sau khi Instructor được approve.
     */
    public function test_case_13_no_resubmission_required_after_instructor_approval(): void
    {
        $admin = $this->createAdmin();
        $instructor = $this->createInstructor(['instructor_status' => 'pending']);
        $course = $this->createCourse($instructor, [
            'status' => CourseStatus::Approved->value,
            'is_published' => false,
            'approved_at' => now(),
            'submission_count' => 1,
        ]);

        $this->assertEquals(CourseStatus::Approved->value, $course->fresh()->status);
        $this->assertEquals(1, $course->fresh()->submission_count);

        // Step 2: Admin approves instructor
        $instructor->update(['instructor_status' => 'approved']);
        app(CourseReviewService::class)->syncInstructorApprovedCourses($instructor->fresh());

        $course->refresh();
        // Course is now published without needing any resubmission
        $this->assertEquals(CourseStatus::Published->value, $course->status);
        $this->assertTrue($course->is_published);
        $this->assertEquals(1, $course->submission_count);
    }

    /**
     * CASE 14: Instructor bị khóa tài khoản -> Course tự động ẩn khỏi catalog và hiện lại khi mở khóa.
     */
    public function test_case_14_locked_instructor_courses_hidden_and_restored_on_unlock(): void
    {
        $instructor = $this->createInstructor([
            'instructor_status' => 'approved',
            'account_status' => 'active',
        ]);
        $course = $this->createCourse($instructor, [
            'status' => CourseStatus::Published->value,
            'is_published' => true,
            'approved_at' => now(),
            'published_at' => now(),
        ]);

        $this->assertTrue($course->isPublished());
        $this->assertNotEmpty(Course::published()->where('id', $course->id)->get());

        // Lock instructor account
        $instructor->update([
            'account_status' => 'locked',
            'locked_at' => now(),
            'locked_reason' => 'Tạm khóa để kiểm tra',
        ]);

        $course->refresh();
        $instructor->refresh();
        $this->assertFalse($course->isPublished());
        $this->assertEmpty(Course::published()->where('id', $course->id)->get());

        // Unlock instructor account
        $instructor->unlockAccount('active', 'approved');

        $course->refresh();
        $this->assertTrue($course->isPublished());
        $this->assertNotEmpty(Course::published()->where('id', $course->id)->get());
    }
}
