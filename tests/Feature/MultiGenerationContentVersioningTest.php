<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ContentUpdate;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\CourseVersion;
use App\Models\Lesson;
use App\Models\LessonVersion;
use App\Models\User;
use App\Services\ContentUpdateService;
use App\Services\ContentVersionComparisonService;
use App\Services\ContentVersionHistoryService;
use App\Services\ContentVersionService;
use App\Services\CourseReviewService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiGenerationContentVersioningTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_approved_updates_create_exact_v1_to_v4_sequence(): void
    {
        [$instructor, $admin, $course] = $this->context('course-generations');
        $versions = [$course->publishedVersion];

        foreach ([200000, 300000, 400000] as $price) {
            $previous = $course->fresh()->publishedVersion;
            $update = $this->draftUpdate($course, $instructor, ContentUpdate::TYPE_COURSE, $course->id, ['price' => $price]);
            app(CourseReviewService::class)->submitForReview($course->fresh(), $instructor);
            $candidate = CourseVersion::query()->where('content_update_id', $update->id)->sole();

            $this->assertSame($previous->id, $course->fresh()->published_version_id);
            $this->assertSame((float) $previous->price, (float) $course->fresh()->price);
            $this->assertSame($candidate->id, $course->fresh()->draft_version_id);

            app(CourseReviewService::class)->approve($course->fresh(), $admin, $this->completeChecklist());
            $versions[] = $candidate->fresh();
            $this->assertNull($course->fresh()->draft_version_id);
        }

        $this->assertSame([1, 2, 3, 4], $course->versions()->orderBy('version_number')->pluck('version_number')->all());
        $this->assertSame(['superseded', 'superseded', 'superseded', 'published'], $course->versions()->orderBy('version_number')->pluck('status')->all());
        $this->assertSame([100000.0, 200000.0, 300000.0, 400000.0], $course->versions()->orderBy('version_number')->get()->map(fn (CourseVersion $version): float => (float) $version->price)->all());
        $this->assertSame($versions[3]->id, $course->fresh()->published_version_id);
        $this->assertSame(400000.0, (float) $course->fresh()->price);

        $priceDiff = collect(app(ContentVersionComparisonService::class)->compare(
            $course,
            ContentUpdate::TYPE_COURSE,
            $versions[0],
            $versions[3],
        ))->firstWhere('key', 'price');
        $this->assertSame('100.000 đ', $priceDiff['old']);
        $this->assertSame('400.000 đ', $priceDiff['new']);
    }

    public function test_lesson_approved_updates_create_immutable_v1_to_v4_and_project_live_metadata(): void
    {
        [$instructor, $admin, $course, $lesson] = $this->context('lesson-generations');
        $versions = [$lesson->publishedVersion];
        $titles = ['Bài học B', 'Bài học C', 'Bài học D'];

        foreach ($titles as $index => $title) {
            $payload = ['title' => $title];
            if ($index === 2) {
                $payload += [
                    'document_file' => 'documents/lesson-v4.pdf',
                    'is_required' => false,
                ];
            }
            $update = $this->draftUpdate($course, $instructor, ContentUpdate::TYPE_LESSON, $lesson->id, $payload);
            app(CourseReviewService::class)->submitForReview($course->fresh(), $instructor);
            $candidate = LessonVersion::query()->where('content_update_id', $update->id)->sole();
            $this->assertSame($candidate->id, $lesson->fresh()->draft_version_id);

            app(CourseReviewService::class)->approve($course->fresh(), $admin, $this->completeChecklist());
            $versions[] = $candidate->fresh();
            $this->assertNull($lesson->fresh()->draft_version_id);
        }

        $this->assertSame([1, 2, 3, 4], $lesson->versions()->orderBy('version_number')->pluck('version_number')->all());
        $this->assertSame(['superseded', 'superseded', 'superseded', 'published'], $lesson->versions()->orderBy('version_number')->pluck('status')->all());
        $this->assertSame(['Bài học A', 'Bài học B', 'Bài học C', 'Bài học D'], $lesson->versions()->orderBy('version_number')->pluck('title')->all());
        $this->assertSame($versions[3]->id, $lesson->fresh()->published_version_id);
        $this->assertSame('documents/lesson-v4.pdf', $lesson->fresh()->document_file);
        $this->assertFalse((bool) $lesson->fresh()->is_required);
    }

    public function test_admin_course_review_aggregates_course_price_and_lesson_title_before_atomic_approval(): void
    {
        [$instructor, $admin, $course, $lesson] = $this->context('admin-aggregation');
        $courseUpdate = $this->draftUpdate($course, $instructor, ContentUpdate::TYPE_COURSE, $course->id, ['price' => 399000]);
        $lessonUpdate = $this->draftUpdate($course, $instructor, ContentUpdate::TYPE_LESSON, $lesson->id, ['title' => 'Bài học chờ duyệt']);

        app(CourseReviewService::class)->submitForReview($course->fresh(), $instructor);
        $courseCandidate = CourseVersion::query()->where('content_update_id', $courseUpdate->id)->sole();
        $lessonCandidate = LessonVersion::query()->where('content_update_id', $lessonUpdate->id)->sole();

        $this->assertSame(100000.0, (float) $course->fresh()->price);
        $this->assertSame('Bài học A', $lesson->fresh()->title);

        $this->actingAs($admin)
            ->withSession($this->twoFactor())
            ->get(route('admin.courses.review', $course))
            ->assertOk()
            ->assertSee('2 cập nhật đang chờ duyệt')
            ->assertSee('Giá')
            ->assertSee('100.000 đ')
            ->assertSee('399.000 đ')
            ->assertSee('Tên bài học')
            ->assertSee('Bài học A')
            ->assertSee('Bài học chờ duyệt');

        app(CourseReviewService::class)->approve($course->fresh(), $admin, $this->completeChecklist());

        $this->assertSame($courseCandidate->id, $course->fresh()->published_version_id);
        $this->assertSame($lessonCandidate->id, $lesson->fresh()->published_version_id);
        $this->assertSame(399000.0, (float) $course->fresh()->price);
        $this->assertSame('Bài học chờ duyệt', $lesson->fresh()->title);
        $this->assertSame(ContentUpdate::STATUS_APPROVED, $courseUpdate->fresh()->status);
        $this->assertSame(ContentUpdate::STATUS_APPROVED, $lessonUpdate->fresh()->status);
    }

    public function test_all_lesson_generations_are_available_for_arbitrary_snapshot_comparison_without_self_default(): void
    {
        [$instructor, $admin, $course, $lesson] = $this->context('history-compare');
        $versions = [$lesson->publishedVersion];
        foreach (['Bài học B', 'Bài học C', 'Bài học D'] as $title) {
            $update = $this->draftUpdate($course, $instructor, ContentUpdate::TYPE_LESSON, $lesson->id, ['title' => $title]);
            app(CourseReviewService::class)->submitForReview($course->fresh(), $instructor);
            $candidate = LessonVersion::query()->where('content_update_id', $update->id)->sole();
            app(CourseReviewService::class)->approve($course->fresh(), $admin, $this->completeChecklist());
            $versions[] = $candidate->fresh();
        }

        $history = app(ContentVersionHistoryService::class);
        $this->assertSame([4, 3, 2, 1], $history->timeline($course->fresh(), ContentUpdate::TYPE_LESSON)->getCollection()->pluck('version_number')->all());

        $comparison = app(ContentVersionComparisonService::class);
        foreach ([[0, 1, 'Bài học A', 'Bài học B'], [1, 2, 'Bài học B', 'Bài học C'], [2, 3, 'Bài học C', 'Bài học D'], [0, 3, 'Bài học A', 'Bài học D']] as [$from, $to, $old, $new]) {
            $title = collect($comparison->compare($course, ContentUpdate::TYPE_LESSON, $versions[$from], $versions[$to]))->firstWhere('key', 'title');
            $this->assertSame($old, $title['old']);
            $this->assertSame($new, $title['new']);
        }

        $this->actingAs($instructor)
            ->withSession($this->twoFactor())
            ->get(route('instructor.courses.versions.compare', [$course, ContentUpdate::TYPE_LESSON, $versions[3]->id]))
            ->assertOk()
            ->assertSee('So sánh V4 → V3')
            ->assertDontSee('value="'.$versions[3]->id.'"', false);
    }

    public function test_rejected_v3_is_immutable_and_revision_allocates_v4(): void
    {
        [$instructor, $admin, $course, $lesson] = $this->context('rejected-generation');
        $v2Update = $this->draftUpdate($course, $instructor, ContentUpdate::TYPE_LESSON, $lesson->id, ['title' => 'Bài học B']);
        app(CourseReviewService::class)->submitForReview($course->fresh(), $instructor);
        app(CourseReviewService::class)->approve($course->fresh(), $admin, $this->completeChecklist());
        $v2 = LessonVersion::query()->where('content_update_id', $v2Update->id)->sole();

        $rejected = $this->draftUpdate($course, $instructor, ContentUpdate::TYPE_LESSON, $lesson->id, ['title' => 'Bài học C bị từ chối']);
        app(CourseReviewService::class)->submitForReview($course->fresh(), $instructor);
        $v3 = LessonVersion::query()->where('content_update_id', $rejected->id)->sole();
        app(CourseReviewService::class)->reject($course->fresh(), $admin, 'Nội dung cần được chỉnh sửa trước khi duyệt.');

        $this->assertSame(LessonVersion::STATUS_REJECTED, $v3->fresh()->status);
        $this->assertSame($v2->id, $lesson->fresh()->published_version_id);
        $this->assertNull($lesson->fresh()->draft_version_id);

        $revision = app(ContentUpdateService::class)->createRevisionFromRejected($rejected->fresh(), $instructor);
        app(ContentUpdateService::class)->updateDraft($revision, ['title' => 'Bài học D']);
        app(CourseReviewService::class)->submitForReview($course->fresh(), $instructor);
        $v4 = LessonVersion::query()->where('content_update_id', $revision->id)->sole();
        app(CourseReviewService::class)->approve($course->fresh(), $admin, $this->completeChecklist());

        $this->assertSame(4, $v4->version_number);
        $this->assertSame(LessonVersion::STATUS_REJECTED, $v3->fresh()->status);
        $this->assertSame('Bài học C bị từ chối', $v3->fresh()->title);
        $this->assertSame(LessonVersion::STATUS_PUBLISHED, $v4->fresh()->status);
        $this->assertSame('Bài học D', $lesson->fresh()->title);
    }

    /** @return array{0: User, 1: User, 2: Course, 3: Lesson} */
    private function context(string $suffix): array
    {
        $instructor = User::factory()->create(['role' => 'instructor', 'instructor_status' => 'approved', 'is_active' => true, 'email_verified_at' => now()]);
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true, 'email_verified_at' => now()]);
        $category = Category::query()->create(['name' => 'Version '.$suffix, 'slug' => 'version-'.$suffix]);
        $course = Course::query()->create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Khóa học phiên bản',
            'slug' => 'course-'.$suffix,
            'short_description' => 'Mô tả ngắn',
            'description' => 'Mô tả khóa học',
            'status' => Course::STATUS_PUBLISHED,
            'is_published' => true,
            'published_at' => now(),
            'copyright_agreed' => true,
            'price' => 100000,
            'language' => 'vi',
            'level' => 'beginner',
        ]);
        $section = CourseSection::query()->create(['course_id' => $course->id, 'title' => 'Chương 1', 'sort_order' => 1]);
        $lesson = Lesson::query()->create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Bài học A',
            'type' => Lesson::TYPE_DOCUMENT,
            'content' => 'Nội dung A',
            'document_file' => 'documents/a.pdf',
            'sort_order' => 1,
            'status' => Lesson::STATUS_PUBLISHED,
        ]);
        app(ContentVersionService::class)->publishInitialCourseTree($course, $admin);

        return [$instructor, $admin, $course->fresh(), $lesson->fresh()];
    }

    private function draftUpdate(Course $course, User $instructor, string $type, int $entityId, array $payload): ContentUpdate
    {
        return app(ContentUpdateService::class)->recordPendingUpdate(
            $type,
            ContentUpdate::ACTION_UPDATE,
            $course->id,
            $entityId,
            $payload,
            $instructor,
        );
    }

    /** @return array<string, bool> */
    private function completeChecklist(): array
    {
        return array_fill_keys(array_keys(config('course.admin_review_checklist', [])), true);
    }

    /** @return array<string, int> */
    private function twoFactor(): array
    {
        return ['two_factor_passed_at' => now()->timestamp];
    }
}
