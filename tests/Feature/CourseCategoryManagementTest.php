<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CourseCategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        if (! in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('The pdo_sqlite driver is required for the in-memory database used by this test.');
        }

        parent::setUp();
    }

    public function test_admin_can_create_parent_category(): void
    {
        $admin = $this->user('admin');

        $response = $this->actingAs($admin)->post(route('admin.categories.store'), [
            'name' => 'Programming',
            'slug' => '',
            'parent_id' => '',
            'status' => '1',
            'sort_order' => 1,
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', [
            'name' => 'Programming',
            'parent_id' => null,
            'status' => true,
        ]);
    }

    public function test_admin_can_create_child_category(): void
    {
        $admin = $this->user('admin');
        $parent = $this->category('Programming');

        $response = $this->actingAs($admin)->post(route('admin.categories.store'), [
            'name' => 'Web Development',
            'parent_id' => $parent->id,
            'status' => '1',
            'sort_order' => 1,
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', [
            'name' => 'Web Development',
            'parent_id' => $parent->id,
            'status' => true,
        ]);
    }

    public function test_admin_cannot_create_third_level_category(): void
    {
        $admin = $this->user('admin');
        $parent = $this->category('Programming');
        $child = $this->category('Web Development', $parent);

        $response = $this->actingAs($admin)
            ->from(route('admin.categories.create'))
            ->post(route('admin.categories.store'), [
                'name' => 'Laravel',
                'parent_id' => $child->id,
                'status' => '1',
                'sort_order' => 1,
            ]);

        $response->assertRedirect(route('admin.categories.create'));
        $response->assertSessionHasErrors('parent_id');
    }

    public function test_admin_cannot_create_looping_category_relationship(): void
    {
        $admin = $this->user('admin');
        $parent = $this->category('Programming');
        $child = $this->category('Web Development', $parent);

        $response = $this->actingAs($admin)
            ->from(route('admin.categories.edit', $parent))
            ->put(route('admin.categories.update', $parent), [
                'name' => 'Programming',
                'parent_id' => $child->id,
                'status' => '1',
                'sort_order' => 1,
            ]);

        $response->assertRedirect(route('admin.categories.edit', $parent));
        $response->assertSessionHasErrors('parent_id');
    }

    public function test_instructor_sees_categories_in_course_create_form(): void
    {
        $instructor = $this->user('instructor');
        $parent = $this->category('Programming');
        $this->category('Web Development', $parent);

        $response = $this->actingAs($instructor)->get(route('instructor.courses.create'));

        $response->assertOk();
        $response->assertSee('Programming');
        $response->assertSee('Web Development');
    }

    public function test_instructor_can_store_valid_child_category_id(): void
    {
        $instructor = $this->user('instructor');
        $parent = $this->category('Programming');
        $child = $this->category('Web Development', $parent);

        $response = $this->actingAs($instructor)->post(route('instructor.courses.store'), [
            'title' => 'Laravel Basics',
            'category_id' => $child->id,
            'short_description' => 'Learn Laravel from the ground up.',
            'description' => 'A practical Laravel course.',
            'objectives' => 'Build a small Laravel app.',
            'price' => 0,
            'discount_price' => null,
            'level' => 'beginner',
            'language' => 'vi',
        ]);

        $course = Course::where('title', 'Laravel Basics')->firstOrFail();

        $response->assertRedirect(route('instructor.courses.edit', $course));
        $this->assertSame($child->id, $course->category_id);
    }

    public function test_instructor_cannot_store_parent_category_id(): void
    {
        $instructor = $this->user('instructor');
        $parent = $this->category('Programming');

        $response = $this->actingAs($instructor)
            ->from(route('instructor.courses.create'))
            ->post(route('instructor.courses.store'), $this->coursePayload($parent->id));

        $response->assertRedirect(route('instructor.courses.create'));
        $response->assertSessionHasErrors('category_id');
    }

    public function test_instructor_cannot_store_inactive_category_id(): void
    {
        $instructor = $this->user('instructor');
        $parent = $this->category('Programming');
        $child = $this->category('Web Development', $parent, false);

        $response = $this->actingAs($instructor)
            ->from(route('instructor.courses.create'))
            ->post(route('instructor.courses.store'), $this->coursePayload($child->id));

        $response->assertRedirect(route('instructor.courses.create'));
        $response->assertSessionHasErrors('category_id');
    }

    public function test_course_edit_selects_current_category(): void
    {
        $instructor = $this->user('instructor');
        $parent = $this->category('Programming');
        $child = $this->category('Web Development', $parent);
        $course = $this->course($instructor, $child);

        $response = $this->actingAs($instructor)->get(route('instructor.courses.edit', $course));

        $response->assertOk();
        $response->assertSee('value="'.$child->id.'" selected', false);
    }

    public function test_admin_cannot_delete_category_that_has_courses(): void
    {
        $admin = $this->user('admin');
        $instructor = $this->user('instructor');
        $parent = $this->category('Programming');
        $child = $this->category('Web Development', $parent);
        $this->course($instructor, $child);

        $response = $this->actingAs($admin)
            ->from(route('admin.categories.index'))
            ->delete(route('admin.categories.destroy', $child));

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHasErrors('category');
        $this->assertDatabaseHas('categories', ['id' => $child->id]);
    }

    public function test_instructor_cannot_access_admin_category_management(): void
    {
        $instructor = $this->user('instructor');

        $response = $this->actingAs($instructor)->get(route('admin.categories.index'));

        $response->assertRedirect(route('instructor.dashboard'));
    }

    private function user(string $role): User
    {
        return User::factory()->create([
            'role' => $role,
            'is_active' => true,
            'two_factor_enabled' => false,
        ]);
    }

    private function category(string $name, ?Category $parent = null, bool $status = true): Category
    {
        return Category::create([
            'parent_id' => $parent?->id,
            'name' => $name,
            'slug' => Str::slug($name),
            'status' => $status,
            'sort_order' => 1,
        ]);
    }

    private function course(User $instructor, Category $category): Course
    {
        return Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Existing Laravel Course',
            'slug' => 'existing-laravel-course-'.Str::random(6),
            'description' => 'Course description',
            'objectives' => 'Course objectives',
            'price' => 0,
            'language' => 'vi',
            'status' => Course::STATUS_DRAFT,
            'is_published' => false,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function coursePayload(int $categoryId): array
    {
        return [
            'title' => 'Laravel Basics',
            'category_id' => $categoryId,
            'short_description' => 'Learn Laravel from the ground up.',
            'description' => 'A practical Laravel course.',
            'objectives' => 'Build a small Laravel app.',
            'price' => 0,
            'discount_price' => null,
            'level' => 'beginner',
            'language' => 'vi',
        ];
    }
}
