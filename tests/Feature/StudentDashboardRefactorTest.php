<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentDashboardRefactorTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_dashboard_only_exposes_bounded_overview_data(): void
    {
        $student = $this->student();
        $instructor = User::factory()->create(['role' => 'instructor']);
        $category = Category::create(['name' => 'Công nghệ', 'slug' => 'cong-nghe']);
        $courses = collect();

        foreach (range(1, 4) as $index) {
            $course = Course::create([
                'instructor_id' => $instructor->id,
                'category_id' => $category->id,
                'title' => 'Khóa giới hạn '.$index,
                'slug' => 'khoa-gioi-han-'.$index,
                'price' => 100000,
                'status' => Course::STATUS_PUBLISHED,
                'is_published' => true,
            ]);
            $courses->push($course);
            Enrollment::create([
                'user_id' => $student->id,
                'course_id' => $course->id,
                'status' => Enrollment::STATUS_ACTIVE,
                'progress_percent' => 10 * $index,
                'last_accessed_at' => now()->subMinutes($index),
            ]);
        }

        Wishlist::create(['user_id' => $student->id, 'course_id' => $courses->last()->id]);
        Order::create([
            'user_id' => $student->id,
            'order_code' => 'ORDER-MUST-NOT-BE-LOADED',
            'subtotal' => 100000,
            'total_amount' => 100000,
            'status' => 'paid',
        ]);
        Certificate::create([
            'user_id' => $student->id,
            'course_id' => $courses->first()->id,
            'certificate_code' => 'CERT-OVERVIEW',
            'issued_at' => now(),
        ]);
        ActivityLog::create([
            'user_id' => $student->id,
            'action' => 'complete_lesson',
            'description' => 'Hoàn thành bài học kiểm thử',
        ]);

        $response = $this->actingAsStudent($student)->get(route('student.dashboard'));

        $response->assertOk()
            ->assertViewIs('student.dashboard.overview.index')
            ->assertSee('data-public-header', false)
            ->assertSee('data-student-dashboard-header', false)
            ->assertSee('sticky left-0 top-20', false)
            ->assertSee('Mở menu học viên')
            ->assertViewHas('continueLearning', fn ($items) => $items->count() === 3)
            ->assertViewHas('stats', fn ($stats) => $stats === [
                'enrolled' => 4,
                'in_progress' => 4,
                'completed' => 0,
                'certificates' => 1,
            ])
            ->assertSee('Hoàn thành bài học kiểm thử')
            ->assertDontSee('ORDER-MUST-NOT-BE-LOADED')
            ->assertDontSee('Khóa giới hạn 4');
    }

    public function test_all_separated_student_pages_render_their_compact_empty_state(): void
    {
        $student = $this->student();
        $routes = [
            route('student.courses'),
            route('student.recently-viewed'),
            route('student.wishlist'),
            route('student.certificates'),
            route('student.orders'),
            route('student.vouchers.index'),
            route('student.study-groups.index'),
            route('student.profile'),
            route('student.profile.security'),
        ];

        foreach ($routes as $url) {
            $this->actingAsStudent($student)->get($url)->assertOk();
        }
    }

    public function test_guest_and_non_student_cannot_open_student_dashboard(): void
    {
        $this->get(route('student.dashboard'))->assertRedirect(route('login'));

        $instructor = User::factory()->create([
            'role' => 'instructor',
            'email_verified_at' => now(),
        ]);

        $this->actingAsStudent($instructor)
            ->get(route('student.dashboard'))
            ->assertRedirect(route('instructor.dashboard'));
    }

    private function student(): User
    {
        return User::factory()->create([
            'role' => 'student',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }

    private function actingAsStudent(User $user): static
    {
        $this->actingAs($user);
        $this->withSession(['two_factor_passed_at' => now()->timestamp]);

        return $this;
    }
}
