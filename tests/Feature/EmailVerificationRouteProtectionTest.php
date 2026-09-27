<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailVerificationRouteProtectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_unverified_student_cannot_access_dashboard_or_direct_account_mutations(): void
    {
        $student = $this->unverifiedUser('student');

        $this->actingAsWithTwoFactor($student)
            ->get(route('student.dashboard'))
            ->assertRedirect(route('verification.notice'));

        $this->actingAsWithTwoFactor($student)
            ->post(route('student.cart.checkout'))
            ->assertRedirect(route('verification.notice'));

        $this->actingAsWithTwoFactor($student)
            ->put(route('student.profile.update'), ['name' => 'Bypass attempt'])
            ->assertRedirect(route('verification.notice'));

        $this->assertNotSame('Bypass attempt', $student->fresh()->name);
    }

    public function test_unverified_student_cannot_post_favorite_directly(): void
    {
        $student = $this->unverifiedUser('student');
        [$course] = $this->publishedCourseAndLesson();

        $this->actingAsWithTwoFactor($student)
            ->post(route('courses.favorite.store', $course))
            ->assertRedirect(route('verification.notice'));

        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $student->id,
            'course_id' => $course->id,
        ]);
    }

    public function test_unverified_student_cannot_use_learning_quiz_or_assignment_routes(): void
    {
        $student = $this->unverifiedUser('student');
        [$course, $lesson] = $this->publishedCourseAndLesson();

        foreach ([
            ['post', route('courses.lessons.progress', [$course, $lesson])],
            ['post', route('courses.lessons.quiz.start', [$course, $lesson])],
            ['get', route('courses.lessons.assignment.download', [$course, $lesson])],
            ['post', route('courses.lessons.assignment.submit', [$course, $lesson])],
        ] as [$method, $url]) {
            $this->actingAsWithTwoFactor($student)
                ->{$method}($url)
                ->assertRedirect(route('verification.notice'));
        }
    }

    public function test_unverified_student_cannot_use_progress_video_or_study_group_apis(): void
    {
        $student = $this->unverifiedUser('student');
        [, $lesson] = $this->publishedCourseAndLesson();

        foreach ([
            ['postJson', route('lesson.complete', $lesson->id)],
            ['postJson', route('video.progress', $lesson)],
            ['getJson', route('user.enrollments')],
            ['postJson', route('api.study-groups.store')],
        ] as [$method, $url]) {
            $this->actingAs($student)
                ->{$method}($url)
                ->assertForbidden();
        }
    }

    public function test_verified_student_can_use_protected_dashboard(): void
    {
        $student = $this->verifiedUser('student');

        $this->actingAsWithTwoFactor($student)
            ->get(route('student.dashboard'))
            ->assertOk();
    }

    public function test_unverified_instructor_cannot_access_any_instructor_area(): void
    {
        $instructor = $this->unverifiedUser('instructor', ['instructor_status' => 'approved']);

        foreach ([
            route('instructor.dashboard'),
            route('instructor.courses.index'),
            route('instructor.wallet.index'),
            route('instructor.profile'),
            route('instructor.pending'),
        ] as $url) {
            $this->actingAsWithTwoFactor($instructor)
                ->get($url)
                ->assertRedirect(route('verification.notice'));
        }
    }

    public function test_unverified_instructor_cannot_bypass_with_direct_posts(): void
    {
        $instructor = $this->unverifiedUser('instructor', ['instructor_status' => 'approved']);

        $this->actingAsWithTwoFactor($instructor)
            ->post(route('instructor.courses.store'))
            ->assertRedirect(route('verification.notice'));

        $this->actingAsWithTwoFactor($instructor)
            ->post(route('instructor.profile.request-reactivation'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_verified_instructor_can_access_dashboard_and_profile(): void
    {
        $instructor = $this->verifiedUser('instructor', ['instructor_status' => 'approved']);

        $this->actingAsWithTwoFactor($instructor)
            ->get(route('instructor.dashboard'))
            ->assertOk();

        $this->actingAsWithTwoFactor($instructor)
            ->get(route('instructor.profile'))
            ->assertOk();
    }

    public function test_unverified_admin_is_blocked_but_verified_admin_still_works(): void
    {
        $unverifiedAdmin = $this->unverifiedUser('admin');

        $this->actingAsWithTwoFactor($unverifiedAdmin)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('verification.notice'));

        $verifiedAdmin = $this->verifiedUser('admin');

        $this->actingAsWithTwoFactor($verifiedAdmin)
            ->get(route('admin.dashboard'))
            ->assertOk();
    }

    private function unverifiedUser(string $role, array $attributes = []): User
    {
        return User::factory()->unverified()->create(array_merge([
            'role' => $role,
            'is_active' => true,
        ], $attributes));
    }

    private function verifiedUser(string $role, array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => $role,
            'is_active' => true,
            'email_verified_at' => now(),
        ], $attributes));
    }

    private function actingAsWithTwoFactor(User $user): static
    {
        return $this->actingAs($user)->withSession([
            'two_factor_passed_at' => now()->timestamp,
        ]);
    }

    /** @return array{0: Course, 1: Lesson} */
    private function publishedCourseAndLesson(): array
    {
        $instructor = $this->verifiedUser('instructor', ['instructor_status' => 'approved']);
        $category = Category::create([
            'name' => 'Email verification test',
            'slug' => 'email-verification-test-'.uniqid(),
            'status' => true,
        ]);
        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Protected course',
            'slug' => 'protected-course-'.uniqid(),
            'short_description' => 'Short description',
            'description' => 'Description',
            'thumbnail' => 'test.png',
            'price' => 0,
            'language' => 'vi',
            'status' => Course::STATUS_PUBLISHED,
            'is_published' => true,
            'published_at' => now(),
        ]);
        $section = CourseSection::create([
            'course_id' => $course->id,
            'title' => 'Section',
            'sort_order' => 1,
        ]);
        $lesson = Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Protected lesson',
            'type' => Lesson::TYPE_VIDEO,
            'video_url' => 'https://example.com/video.mp4',
            'duration_seconds' => 300,
            'content' => 'Content',
            'sort_order' => 1,
            'is_required' => true,
            'status' => Lesson::STATUS_PUBLISHED,
        ]);

        return [$course, $lesson];
    }
}
