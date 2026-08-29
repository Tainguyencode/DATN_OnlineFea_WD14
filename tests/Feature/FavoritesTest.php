<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoritesTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;

    protected User $otherStudent;

    protected User $instructor;

    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::factory()->create([
            'role' => 'student',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $this->otherStudent = User::factory()->create([
            'role' => 'student',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $this->instructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'approved',
            'is_active' => true,
            'account_status' => 'active',
        ]);
        $this->category = Category::create([
            'name' => 'Công nghệ thông tin',
            'slug' => 'cong-nghe-thong-tin',
            'status' => true,
        ]);
    }

    public function test_student_can_favorite_a_published_course(): void
    {
        $course = $this->publishedCourse('Laravel cơ bản');

        $response = $this->actingAs($this->student)
            ->post(route('courses.favorite.store', $course));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Đã thêm khóa học vào danh sách yêu thích.');
        $this->assertDatabaseHas('wishlists', [
            'user_id' => $this->student->id,
            'course_id' => $course->id,
        ]);
    }

    public function test_same_course_cannot_be_favorited_twice(): void
    {
        $course = $this->publishedCourse('Laravel nâng cao');

        $this->actingAs($this->student)->post(route('courses.favorite.store', $course));
        $response = $this->actingAs($this->student)->post(route('courses.favorite.store', $course));

        $response->assertRedirect();
        $this->assertSame(1, Wishlist::where('user_id', $this->student->id)->where('course_id', $course->id)->count());
    }

    public function test_student_can_remove_a_favorite(): void
    {
        $course = $this->publishedCourse('PHP thực chiến');
        Wishlist::create(['user_id' => $this->student->id, 'course_id' => $course->id]);

        $response = $this->actingAs($this->student)
            ->delete(route('courses.favorite.destroy', $course));

        $response->assertRedirect();
        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $this->student->id,
            'course_id' => $course->id,
        ]);
    }

    public function test_course_detail_reflects_the_current_favorite_state(): void
    {
        $course = $this->publishedCourse('Khóa học đồng bộ trạng thái');

        $unfavoritedResponse = $this->actingAs($this->student)
            ->get(route('courses.show', $course->slug));
        $unfavoritedResponse->assertSee('Thêm vào yêu thích');

        Wishlist::create(['user_id' => $this->student->id, 'course_id' => $course->id]);

        $favoritedResponse = $this->actingAs($this->student)
            ->get(route('courses.show', $course->slug));
        $favoritedResponse->assertSee('Bỏ khỏi yêu thích');
    }

    public function test_favorites_page_only_lists_the_current_students_courses(): void
    {
        $studentCourse = $this->publishedCourse('Khóa học của học viên A');
        $otherCourse = $this->publishedCourse('Khóa học của học viên B');
        Wishlist::create(['user_id' => $this->student->id, 'course_id' => $studentCourse->id]);
        Wishlist::create(['user_id' => $this->otherStudent->id, 'course_id' => $otherCourse->id]);

        $response = $this->actingAs($this->student)->get(route('favorites.index'));

        $response->assertOk();
        $response->assertSee($studentCourse->title);
        $response->assertDontSee($otherCourse->title);
    }

    public function test_hidden_or_unpublished_courses_are_not_exposed_on_favorites_page(): void
    {
        $publishedCourse = $this->publishedCourse('Khóa học đang hiển thị');
        $hiddenCourse = $this->publishedCourse('Khóa học chưa xuất bản', [
            'status' => Course::STATUS_DRAFT,
            'is_published' => false,
        ]);
        Wishlist::create(['user_id' => $this->student->id, 'course_id' => $publishedCourse->id]);
        Wishlist::create(['user_id' => $this->student->id, 'course_id' => $hiddenCourse->id]);

        $response = $this->actingAs($this->student)->get(route('favorites.index'));

        $response->assertSee($publishedCourse->title);
        $response->assertDontSee($hiddenCourse->title);
    }

    public function test_header_favorite_badge_counts_only_the_current_students_visible_courses(): void
    {
        $firstCourse = $this->publishedCourse('Khóa học yêu thích 1');
        $secondCourse = $this->publishedCourse('Khóa học yêu thích 2');
        $otherCourse = $this->publishedCourse('Yêu thích của học viên khác');
        Wishlist::create(['user_id' => $this->student->id, 'course_id' => $firstCourse->id]);
        Wishlist::create(['user_id' => $this->student->id, 'course_id' => $secondCourse->id]);
        Wishlist::create(['user_id' => $this->otherStudent->id, 'course_id' => $otherCourse->id]);

        $response = $this->actingAs($this->student)->get(route('courses.index'));

        $response->assertOk();
        $response->assertSee('data-favorite-count="2"', false);
        $response->assertSee('data-favorite-badge', false);
    }

    public function test_header_favorite_badge_disappears_after_unfavorite(): void
    {
        $course = $this->publishedCourse('Khóa học cần bỏ yêu thích');
        Wishlist::create(['user_id' => $this->student->id, 'course_id' => $course->id]);

        $this->actingAs($this->student)->delete(route('courses.favorite.destroy', $course));
        $response = $this->actingAs($this->student)->get(route('courses.index'));

        $response->assertSee('data-favorite-count="0"', false);
        $response->assertDontSee('data-favorite-badge', false);
    }

    public function test_owned_course_uses_learning_action_and_course_in_cart_uses_existing_cart_state(): void
    {
        $ownedCourse = $this->publishedCourse('Khóa học đã sở hữu');
        $cartCourse = $this->publishedCourse('Khóa học đã có trong giỏ');
        Wishlist::create(['user_id' => $this->student->id, 'course_id' => $ownedCourse->id]);
        Wishlist::create(['user_id' => $this->student->id, 'course_id' => $cartCourse->id]);
        Enrollment::create([
            'user_id' => $this->student->id,
            'course_id' => $ownedCourse->id,
            'status' => Enrollment::STATUS_ACTIVE,
            'enrolled_at' => now(),
        ]);
        $cart = Cart::create(['user_id' => $this->student->id]);
        $cart->courses()->attach($cartCourse->id);

        $response = $this->actingAs($this->student)->get(route('favorites.index'));

        $response->assertSee('Vào học');
        $response->assertSee('Đã có trong giỏ');
        $response->assertDontSee('Thêm vào giỏ');
    }

    public function test_favorites_page_is_paginated(): void
    {
        $courses = [];
        for ($index = 1; $index <= 10; $index++) {
            $course = $this->publishedCourse("Khóa học trang {$index}: mục");
            $wishlist = Wishlist::create(['user_id' => $this->student->id, 'course_id' => $course->id]);
            $wishlist->update([
                'created_at' => now()->addMinutes($index),
                'updated_at' => now()->addMinutes($index),
            ]);
            $courses[] = $course;
        }

        $response = $this->actingAs($this->student)->get(route('favorites.index', ['page' => 2]));

        $response->assertOk();
        $response->assertSee($courses[0]->title);
        $response->assertDontSee($courses[9]->title);
    }

    public function test_guest_is_redirected_to_login_when_favoriting(): void
    {
        $course = $this->publishedCourse('Khóa học dành cho khách');

        $response = $this->post(route('courses.favorite.store', $course));

        $response->assertRedirect(route('login'));
    }

    private function publishedCourse(string $title, array $overrides = []): Course
    {
        return Course::create(array_merge([
            'instructor_id' => $this->instructor->id,
            'category_id' => $this->category->id,
            'title' => $title,
            'slug' => str($title)->slug(),
            'short_description' => 'Mô tả khóa học',
            'description' => 'Mô tả chi tiết khóa học',
            'price' => 100000,
            'status' => Course::STATUS_PUBLISHED,
            'is_published' => true,
            'published_at' => now(),
        ], $overrides));
    }
}
