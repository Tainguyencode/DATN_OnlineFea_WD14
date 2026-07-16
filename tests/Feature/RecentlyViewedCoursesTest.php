<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\RecentlyViewedCourse;
use App\Models\User;
use App\Services\RecentlyViewedCourseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

class RecentlyViewedCoursesTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_guest_viewing_course_does_not_create_history(): void
    {
        $course = $this->publishedCourse();

        $this->get(route('courses.show', $course->slug))
            ->assertOk();

        $this->assertDatabaseCount('recently_viewed_courses', 0);
    }

    public function test_student_viewing_course_creates_history(): void
    {
        $student = $this->student();
        $course = $this->publishedCourse();

        $this->actingAs($student)
            ->get(route('courses.show', $course->slug))
            ->assertOk();

        $this->assertDatabaseHas('recently_viewed_courses', [
            'user_id' => $student->id,
            'course_id' => $course->id,
        ]);

        $this->assertNotNull(RecentlyViewedCourse::first()?->last_viewed_at);
    }

    public function test_viewing_same_course_updates_last_viewed_at_without_duplicate(): void
    {
        $student = $this->student();
        $course = $this->publishedCourse();

        Carbon::setTestNow(Carbon::parse('2026-07-17 08:00:00'));
        $this->actingAs($student)->get(route('courses.show', $course->slug))->assertOk();

        Carbon::setTestNow(Carbon::parse('2026-07-17 09:30:00'));
        $this->actingAs($student)->get(route('courses.show', $course->slug))->assertOk();

        $this->assertSame(1, RecentlyViewedCourse::where('user_id', $student->id)->where('course_id', $course->id)->count());
        $this->assertTrue(
            RecentlyViewedCourse::firstOrFail()->last_viewed_at->equalTo(Carbon::parse('2026-07-17 09:30:00'))
        );
    }

    public function test_two_students_have_separate_recent_histories(): void
    {
        $firstStudent = $this->student(['email' => 'first@example.com']);
        $secondStudent = $this->student(['email' => 'second@example.com']);
        $course = $this->publishedCourse();

        $this->actingAs($firstStudent)->get(route('courses.show', $course->slug))->assertOk();
        $this->actingAs($secondStudent)->get(route('courses.show', $course->slug))->assertOk();

        $this->assertSame(2, RecentlyViewedCourse::where('course_id', $course->id)->count());
        $this->assertDatabaseHas('recently_viewed_courses', ['user_id' => $firstStudent->id, 'course_id' => $course->id]);
        $this->assertDatabaseHas('recently_viewed_courses', ['user_id' => $secondStudent->id, 'course_id' => $course->id]);
    }

    public function test_recently_viewed_index_is_sorted_newest_first_and_only_shows_current_user(): void
    {
        $student = $this->student();
        $otherStudent = $this->student(['email' => 'other@example.com']);
        $oldCourse = $this->publishedCourse('Old Laravel');
        $newCourse = $this->publishedCourse('New Laravel');
        $otherCourse = $this->publishedCourse('Other Student Course');

        RecentlyViewedCourse::create([
            'user_id' => $student->id,
            'course_id' => $oldCourse->id,
            'last_viewed_at' => now()->subDays(2),
        ]);
        RecentlyViewedCourse::create([
            'user_id' => $student->id,
            'course_id' => $newCourse->id,
            'last_viewed_at' => now()->subMinutes(5),
        ]);
        RecentlyViewedCourse::create([
            'user_id' => $otherStudent->id,
            'course_id' => $otherCourse->id,
            'last_viewed_at' => now(),
        ]);

        $this->actingAs($student)
            ->get(route('student.recently-viewed.index'))
            ->assertOk()
            ->assertSeeInOrder([$newCourse->title, $oldCourse->title])
            ->assertDontSee($otherCourse->title);
    }

    public function test_student_can_delete_only_their_own_history_item(): void
    {
        $student = $this->student();
        $otherStudent = $this->student(['email' => 'other-delete@example.com']);
        $ownHistory = RecentlyViewedCourse::create([
            'user_id' => $student->id,
            'course_id' => $this->publishedCourse('Own Course')->id,
            'last_viewed_at' => now(),
        ]);
        $otherHistory = RecentlyViewedCourse::create([
            'user_id' => $otherStudent->id,
            'course_id' => $this->publishedCourse('Protected Course')->id,
            'last_viewed_at' => now(),
        ]);

        $this->actingAs($student)
            ->delete(route('student.recently-viewed.destroy', $otherHistory->id))
            ->assertRedirect();

        $this->assertDatabaseHas('recently_viewed_courses', ['id' => $otherHistory->id]);

        $this->actingAs($student)
            ->delete(route('student.recently-viewed.destroy', $ownHistory->id))
            ->assertRedirect();

        $this->assertDatabaseMissing('recently_viewed_courses', ['id' => $ownHistory->id]);
        $this->assertDatabaseHas('recently_viewed_courses', ['id' => $otherHistory->id]);
    }

    public function test_clear_history_only_deletes_current_users_records(): void
    {
        $student = $this->student();
        $otherStudent = $this->student(['email' => 'other-clear@example.com']);
        $ownCourse = $this->publishedCourse('Own Clear Course');
        $otherCourse = $this->publishedCourse('Other Clear Course');

        RecentlyViewedCourse::create([
            'user_id' => $student->id,
            'course_id' => $ownCourse->id,
            'last_viewed_at' => now(),
        ]);
        RecentlyViewedCourse::create([
            'user_id' => $otherStudent->id,
            'course_id' => $otherCourse->id,
            'last_viewed_at' => now(),
        ]);

        $this->actingAs($student)
            ->delete(route('student.recently-viewed.clear'))
            ->assertRedirect();

        $this->assertDatabaseMissing('recently_viewed_courses', ['user_id' => $student->id]);
        $this->assertDatabaseHas('recently_viewed_courses', ['user_id' => $otherStudent->id]);
    }

    public function test_opening_lesson_updates_recently_viewed_course(): void
    {
        $student = $this->student();
        $course = $this->publishedCourse();
        $lesson = $course->lessons->firstOrFail();

        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => Enrollment::STATUS_ACTIVE,
            'progress_percent' => 25,
            'enrolled_at' => now(),
        ]);

        $this->actingAs($student)
            ->get(route('courses.lessons.show', [$course, $lesson]))
            ->assertOk();

        $this->assertDatabaseHas('recently_viewed_courses', [
            'user_id' => $student->id,
            'course_id' => $course->id,
        ]);
        $this->assertDatabaseHas('enrollments', [
            'user_id' => $student->id,
            'course_id' => $course->id,
            'progress_percent' => 25,
        ]);
    }

    public function test_hidden_courses_are_not_recorded_or_shown_in_recent_history(): void
    {
        $student = $this->student();
        $hiddenCourse = $this->publishedCourse('Hidden Course', [
            'status' => Course::STATUS_DRAFT,
            'is_published' => false,
        ]);
        $visibleCourse = $this->publishedCourse('Visible Course');

        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $hiddenCourse->id,
            'status' => Enrollment::STATUS_ACTIVE,
            'enrolled_at' => now(),
        ]);

        $this->actingAs($student)
            ->get(route('courses.show', $hiddenCourse->slug))
            ->assertOk();

        $this->assertDatabaseMissing('recently_viewed_courses', [
            'user_id' => $student->id,
            'course_id' => $hiddenCourse->id,
        ]);

        RecentlyViewedCourse::create([
            'user_id' => $student->id,
            'course_id' => $hiddenCourse->id,
            'last_viewed_at' => now(),
        ]);
        RecentlyViewedCourse::create([
            'user_id' => $student->id,
            'course_id' => $visibleCourse->id,
            'last_viewed_at' => now()->subMinute(),
        ]);

        $this->actingAs($student)
            ->get(route('student.recently-viewed.index'))
            ->assertOk()
            ->assertSee($visibleCourse->title)
            ->assertDontSee($hiddenCourse->title);
    }

    public function test_recently_viewed_history_is_limited_to_fifty_courses_per_user(): void
    {
        $student = $this->student();
        $service = app(RecentlyViewedCourseService::class);
        $oldestCourse = null;

        for ($i = 1; $i <= 55; $i++) {
            Carbon::setTestNow(Carbon::parse('2026-07-17 08:00:00')->addMinutes($i));
            $course = $this->publishedCourse('Course '.$i);
            $oldestCourse ??= $course;

            $service->record($student, $course);
        }

        $this->assertSame(50, RecentlyViewedCourse::where('user_id', $student->id)->count());
        $this->assertDatabaseMissing('recently_viewed_courses', [
            'user_id' => $student->id,
            'course_id' => $oldestCourse->id,
        ]);
    }

    private function student(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'student',
            'email_verified_at' => now(),
            'is_active' => true,
            'two_factor_enabled' => false,
        ], $overrides));
    }

    private function publishedCourse(string $title = 'Published Course', array $overrides = []): Course
    {
        $instructor = User::factory()->create(['role' => 'instructor', 'email_verified_at' => now()]);
        $category = Category::create([
            'name' => 'Development '.Str::random(6),
            'slug' => 'development-'.Str::random(8),
            'status' => true,
        ]);

        $course = Course::create(array_merge([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::random(8),
            'short_description' => 'Short description',
            'description' => 'Course description',
            'thumbnail' => null,
            'price' => 199000,
            'language' => 'vi',
            'status' => Course::STATUS_PUBLISHED,
            'is_published' => true,
            'published_at' => now(),
        ], $overrides));

        $section = CourseSection::create([
            'course_id' => $course->id,
            'title' => 'Section 1',
            'sort_order' => 1,
        ]);

        Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Lesson 1',
            'type' => 'video',
            'video_url' => 'https://example.com/video.mp4',
            'duration_seconds' => 300,
            'content' => 'Lesson content',
            'sort_order' => 1,
            'is_preview' => true,
            'is_required' => true,
            'status' => 'published',
        ]);

        return $course->fresh(['lessons', 'courseSections.lessons', 'chapters.lessons']);
    }
}
