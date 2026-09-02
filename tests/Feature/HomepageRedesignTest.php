<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Review;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class HomepageRedesignTest extends TestCase
{
    use RefreshDatabase;

    private User $instructor;

    private Category $parentCategory;

    private Category $childCategory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->instructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'approved',
            'is_active' => true,
            'account_status' => 'active',
        ]);
        $this->parentCategory = Category::create([
            'name' => 'Công nghệ',
            'slug' => 'cong-nghe',
            'status' => true,
            'sort_order' => 1,
        ]);
        $this->childCategory = Category::create([
            'parent_id' => $this->parentCategory->id,
            'name' => 'Lập trình Web',
            'slug' => 'lap-trinh-web',
            'status' => true,
            'sort_order' => 1,
        ]);
    }

    public function test_homepage_renders_all_categories_for_the_inline_toggle_and_limits_course_sections(): void
    {
        foreach (range(2, 6) as $position) {
            Category::create([
                'name' => "Danh mục {$position}",
                'slug' => "danh-muc-{$position}",
                'status' => true,
                'sort_order' => $position,
            ]);
        }

        foreach (range(1, 6) as $index) {
            $this->createPublishedCourse("Miễn phí {$index}", 0, false, 20 - $index);
            $this->createPublishedCourse("Nổi bật {$index}", 100000, true, 40 - $index);
        }

        foreach (range(1, 5) as $index) {
            $student = User::factory()->create(['role' => 'student']);
            Review::create([
                'user_id' => $student->id,
                'course_id' => Course::query()->where('title', "Nổi bật {$index}")->value('id'),
                'rating' => 5,
                'comment' => "Chia sẻ thực tế số {$index}",
                'status' => 'visible',
                'is_hidden' => false,
            ]);
        }

        Model::preventLazyLoading();
        try {
            $response = $this->get(route('home'));
        } finally {
            Model::preventLazyLoading(false);
        }

        $response->assertOk();
        $html = $response->getContent();

        $this->assertSame(6, substr_count($html, 'data-home-category-card'));
        $this->assertSame(2, substr_count($html, 'data-home-category-extra'));
        $this->assertSame(4, substr_count($html, 'data-home-stat-icon='));
        $this->assertStringContainsString('data-home-category-toggle', $html);
        $this->assertStringContainsString('aria-controls="home-category-grid"', $html);
        $this->assertSame(6, substr_count($html, 'data-home-course-variant="free"'));
        $this->assertSame(6, substr_count($html, 'data-home-course-variant="featured"'));
        $this->assertSame(3, substr_count($html, 'data-home-testimonial'));
        $this->assertStringContainsString('loading="lazy"', $html);
        $this->assertStringContainsString('fetchpriority="high"', $html);

        $sectionPositions = array_map(fn (string $needle) => strpos($html, $needle), [
            'id="categories"',
            'id="free-courses"',
            'id="courses"',
            'id="student-reviews"',
            'id="business"',
        ]);
        $this->assertSame($sectionPositions, collect($sectionPositions)->sort()->values()->all());
    }

    public function test_student_wishlist_and_enrollment_states_are_batched_for_homepage_cards(): void
    {
        $freeCourse = $this->createPublishedCourse('Khóa yêu thích', 0, false, 10);
        $featuredCourse = $this->createPublishedCourse('Khóa đã sở hữu', 250000, true, 20);
        $student = User::factory()->create(['role' => 'student']);

        Wishlist::create(['user_id' => $student->id, 'course_id' => $freeCourse->id]);
        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $featuredCourse->id,
            'status' => Enrollment::STATUS_ACTIVE,
            'enrolled_at' => now(),
        ]);

        $response = $this->actingAs($student)->get(route('home'));

        $response->assertOk();
        $response->assertSee('favorited: true', false);
        $response->assertSee('Đã sở hữu');
    }

    public function test_homepage_query_count_does_not_scale_with_the_number_of_cards(): void
    {
        $this->createPublishedCourse('Miễn phí đầu tiên', 0, false, 10);
        $this->createPublishedCourse('Nổi bật đầu tiên', 100000, true, 20);
        $firstCount = $this->homepageQueryCount();

        foreach (range(2, 8) as $index) {
            $this->createPublishedCourse("Miễn phí thêm {$index}", 0, false, 10 + $index);
            $this->createPublishedCourse("Nổi bật thêm {$index}", 100000, true, 20 + $index);
        }
        $expandedCount = $this->homepageQueryCount();

        $this->assertSame($firstCount, $expandedCount);
        $this->assertLessThanOrEqual(20, $expandedCount);
    }

    private function createPublishedCourse(string $title, int $price, bool $featured, int $ratingSeed): Course
    {
        return Course::create([
            'instructor_id' => $this->instructor->id,
            'category_id' => $this->childCategory->id,
            'title' => $title,
            'slug' => str($title)->slug().'-'.uniqid(),
            'thumbnail' => null,
            'price' => $price,
            'level' => 'beginner',
            'language' => 'vi',
            'status' => Course::STATUS_PUBLISHED,
            'is_published' => true,
            'is_featured' => $featured,
            'rating_avg' => min(5, $ratingSeed / 10),
            'rating_count' => $ratingSeed,
            'enrollment_count' => $ratingSeed,
            'published_at' => now(),
        ]);
    }

    private function homepageQueryCount(): int
    {
        DB::flushQueryLog();
        DB::enableQueryLog();
        $this->get(route('home'))->assertOk();
        $count = count(DB::getQueryLog());
        DB::disableQueryLog();

        return $count;
    }
}
