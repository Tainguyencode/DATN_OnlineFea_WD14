<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use App\Services\CourseRecommendationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RelatedCoursesTest extends TestCase
{
    use RefreshDatabase;

    private int $courseSequence = 1;

    public function test_related_courses_are_scored_filtered_and_limited(): void
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $otherInstructor = User::factory()->create(['role' => 'instructor']);
        $category = $this->category('Web Development');
        $otherCategory = $this->category('Data Science');

        $current = $this->course([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'level' => 'beginner',
            'price' => 100000,
            'tags' => ['laravel', 'php'],
        ]);

        $bestMatch = $this->course([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'level' => 'beginner',
            'price' => 105000,
            'tags' => ['laravel', 'php', 'backend'],
            'rating_avg' => 4.8,
            'rating_count' => 25,
            'enrollment_count' => 60,
            'published_at' => now()->subDays(2),
        ]);

        $sameCategory = $this->course([
            'instructor_id' => $otherInstructor->id,
            'category_id' => $category->id,
            'level' => 'advanced',
            'price' => 450000,
            'tags' => ['frontend'],
            'rating_avg' => 5.0,
            'rating_count' => 40,
            'enrollment_count' => 500,
            'published_at' => now()->subDays(8),
        ]);

        $sameInstructorAndLevel = $this->course([
            'instructor_id' => $instructor->id,
            'category_id' => $otherCategory->id,
            'level' => 'beginner',
            'price' => 98000,
            'tags' => ['php'],
            'rating_avg' => 4.2,
            'rating_count' => 12,
            'enrollment_count' => 20,
            'published_at' => now()->subDays(15),
        ]);

        $highRatingOnly = $this->course([
            'instructor_id' => $otherInstructor->id,
            'category_id' => $otherCategory->id,
            'level' => 'advanced',
            'price' => 900000,
            'tags' => ['machine-learning'],
            'rating_avg' => 5.0,
            'rating_count' => 90,
            'enrollment_count' => 1200,
            'published_at' => now()->subDays(1),
        ]);

        $this->course([
            'instructor_id' => $otherInstructor->id,
            'category_id' => $otherCategory->id,
            'level' => 'intermediate',
            'price' => 850000,
            'rating_avg' => 2.5,
            'enrollment_count' => 1,
            'published_at' => now()->subDays(240),
        ]);

        $unpublished = $this->course([
            'category_id' => $category->id,
            'status' => Course::STATUS_PUBLISHED,
            'is_published' => false,
        ]);

        $draft = $this->course([
            'category_id' => $category->id,
            'status' => Course::STATUS_DRAFT,
            'is_published' => true,
        ]);

        $related = app(CourseRecommendationService::class)->getRelatedCourses($current, 4);
        $ids = $related->pluck('id')->all();

        $this->assertCount(4, $ids);
        $this->assertSame($bestMatch->id, $ids[0]);
        $this->assertContains($sameCategory->id, $ids);
        $this->assertContains($sameInstructorAndLevel->id, $ids);
        $this->assertContains($highRatingOnly->id, $ids);
        $this->assertLessThan(
            array_search($highRatingOnly->id, $ids, true),
            array_search($sameCategory->id, $ids, true)
        );
        $this->assertNotContains($current->id, $ids);
        $this->assertNotContains($unpublished->id, $ids);
        $this->assertNotContains($draft->id, $ids);
        $this->assertSame($ids, array_values(array_unique($ids)));
    }

    public function test_related_courses_fallback_when_category_does_not_fill_limit(): void
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $category = $this->category('Sparse Category');
        $otherCategory = $this->category('Fallback Category');

        $current = $this->course([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'level' => 'beginner',
            'price' => 100000,
            'tags' => [],
        ]);

        $sameCategory = $this->course(['category_id' => $category->id, 'price' => 700000]);
        $sameInstructor = $this->course([
            'instructor_id' => $instructor->id,
            'category_id' => $otherCategory->id,
            'level' => 'advanced',
            'price' => 600000,
        ]);
        $sameLevel = $this->course([
            'category_id' => $otherCategory->id,
            'level' => 'beginner',
            'price' => 650000,
        ]);
        $highRatingFallback = $this->course([
            'category_id' => $otherCategory->id,
            'level' => 'advanced',
            'price' => 900000,
            'rating_avg' => 5.0,
            'rating_count' => 30,
            'published_at' => now()->subDays(6),
        ]);

        $related = app(CourseRecommendationService::class)->getRelatedCourses($current, 4);
        $ids = $related->pluck('id')->all();

        $this->assertCount(4, $ids);
        $this->assertContains($sameCategory->id, $ids);
        $this->assertContains($sameInstructor->id, $ids);
        $this->assertContains($sameLevel->id, $ids);
        $this->assertContains($highRatingFallback->id, $ids);
        $this->assertSame($ids, array_values(array_unique($ids)));
    }

    public function test_tag_overlap_can_lift_course_without_same_category(): void
    {
        $category = $this->category('Laravel');
        $otherCategory = $this->category('PHP');

        $current = $this->course([
            'category_id' => $category->id,
            'level' => 'advanced',
            'price' => 200000,
            'tags' => ['api', 'testing'],
        ]);

        $tagMatch = $this->course([
            'category_id' => $otherCategory->id,
            'level' => 'intermediate',
            'price' => 210000,
            'tags' => ['api', 'testing'],
            'rating_avg' => 4.0,
        ]);

        $priceOnly = $this->course([
            'category_id' => $otherCategory->id,
            'level' => 'intermediate',
            'price' => 205000,
            'tags' => ['design'],
            'rating_avg' => 4.5,
        ]);

        $ids = app(CourseRecommendationService::class)
            ->getRelatedCourses($current, 2)
            ->pluck('id')
            ->all();

        $this->assertSame($tagMatch->id, $ids[0]);
        $this->assertContains($priceOnly->id, $ids);
    }

    public function test_related_courses_handle_missing_optional_metadata(): void
    {
        $current = $this->course([
            'category_id' => null,
            'level' => null,
            'price' => 0,
            'tags' => null,
            'rating_avg' => 0,
            'rating_count' => 0,
        ]);

        $first = $this->course([
            'category_id' => null,
            'level' => null,
            'price' => 0,
            'tags' => null,
            'rating_avg' => 0,
            'rating_count' => 0,
        ]);
        $second = $this->course([
            'category_id' => $this->category('No Metadata Fallback')->id,
            'level' => 'advanced',
            'price' => 0,
            'tags' => null,
            'rating_avg' => 0,
            'rating_count' => 0,
        ]);

        $ids = app(CourseRecommendationService::class)
            ->getRelatedCourses($current, 4)
            ->pluck('id')
            ->all();

        $expectedIds = [$first->id, $second->id];
        sort($expectedIds);
        sort($ids);

        $this->assertSame($expectedIds, $ids);
        $this->assertNotContains($current->id, $ids);
    }

    public function test_related_courses_do_not_issue_query_per_candidate(): void
    {
        $category = $this->category('Performance');
        $current = $this->course(['category_id' => $category->id, 'tags' => []]);

        for ($i = 0; $i < 12; $i++) {
            $this->course([
                'category_id' => $category->id,
                'rating_avg' => 4.0 + ($i / 20),
                'enrollment_count' => 10 + $i,
                'published_at' => now()->subDays($i),
                'tags' => [],
            ]);
        }

        DB::flushQueryLog();
        DB::enableQueryLog();

        $related = app(CourseRecommendationService::class)->getRelatedCourses($current, 4);

        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        $this->assertCount(4, $related);
        $this->assertLessThanOrEqual(5, $queryCount);
    }

    private function category(string $name): Category
    {
        return Category::query()->create([
            'name' => $name,
            'slug' => str($name)->slug().'-'.uniqid(),
            'status' => true,
        ]);
    }

    private function course(array $overrides = []): Course
    {
        $categoryId = array_key_exists('category_id', $overrides)
            ? $overrides['category_id']
            : $this->category('Category '.$this->courseSequence)->id;
        $instructorId = array_key_exists('instructor_id', $overrides)
            ? $overrides['instructor_id']
            : User::factory()->create(['role' => 'instructor'])->id;

        $sequence = $this->courseSequence++;

        return Course::query()->create(array_merge([
            'instructor_id' => $instructorId,
            'category_id' => $categoryId,
            'title' => 'Course '.$sequence,
            'slug' => 'course-'.$sequence.'-'.uniqid(),
            'short_description' => 'Short description',
            'description' => 'Long description',
            'objectives' => 'Course objectives',
            'thumbnail' => null,
            'level' => 'beginner',
            'price' => 100000,
            'discount_price' => null,
            'sale_price' => null,
            'status' => Course::STATUS_PUBLISHED,
            'is_published' => true,
            'rating_avg' => 3.5,
            'rating_count' => 5,
            'enrollment_count' => 5,
            'duration_minutes' => 60,
            'tags' => [],
            'published_at' => now()->subDays(30 + $sequence),
        ], $overrides));
    }
}
