<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Category;
use App\Models\ContentUpdate;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\QuestionVersion;
use App\Models\Quiz;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\QuizVersion;
use App\Models\QuizVersionQuestion;
use App\Models\User;
use App\Services\ContentUpdateDiffService;
use App\Services\ContentUpdateService;
use App\Services\CourseReviewService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentUpdateDiffServiceTest extends TestCase
{
    use RefreshDatabase;

    private function context(): array
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $category = Category::create(['name' => 'Lập trình', 'slug' => 'lap-trinh']);
        $course = Course::create(['instructor_id' => $instructor->id, 'category_id' => $category->id, 'title' => 'PHP cơ bản', 'slug' => 'php-co-ban', 'short_description' => 'Cũ', 'description' => 'Mô tả cũ', 'price' => 100000, 'language' => 'vi', 'level' => 'beginner', 'status' => Course::STATUS_PUBLISHED, 'is_published' => true]);
        $section = CourseSection::create(['course_id' => $course->id, 'title' => 'Chương 1', 'description' => 'Mô tả chương', 'sort_order' => 1]);
        $lesson = Lesson::create(['course_id' => $course->id, 'section_id' => $section->id, 'title' => 'Bài cũ', 'type' => Lesson::TYPE_ASSIGNMENT, 'content' => 'Yêu cầu cũ', 'duration' => 600, 'sort_order' => 1, 'status' => Lesson::STATUS_PUBLISHED]);
        Assignment::create(['course_id' => $course->id, 'lesson_id' => $lesson->id, 'title' => $lesson->title, 'description' => 'Yêu cầu cũ', 'instructions' => 'Yêu cầu cũ', 'due_days' => 7, 'max_score' => 100, 'passing_score' => 70]);

        return [$instructor, $course, $section, $lesson];
    }

    private function update(Course $course, User $instructor, string $type, string $action, ?int $entityId, array $payload): ContentUpdate
    {
        return ContentUpdate::create(compact('type', 'action', 'entityId') + [
            'entity_id' => $entityId,
            'course_id' => $course->id,
            'payload' => $payload,
            'status' => ContentUpdate::STATUS_PENDING,
            'created_by' => $instructor->id,
        ]);
    }

    public function test_course_update_has_human_readable_current_and_proposed_values(): void
    {
        [$instructor, $course] = $this->context();
        $update = $this->update($course, $instructor, ContentUpdate::TYPE_COURSE, ContentUpdate::ACTION_UPDATE, $course->id, ['title' => 'PHP nâng cao', 'price' => 120000]);

        $fields = collect(app(ContentUpdateDiffService::class)->build($update)['fields'])->keyBy('key');

        $this->assertSame('PHP cơ bản', $fields['title']['old']);
        $this->assertSame('PHP nâng cao', $fields['title']['new']);
        $this->assertSame('100.000 đ', $fields['price']['old']);
    }

    public function test_section_create_shows_only_proposed_content(): void
    {
        [$instructor, $course] = $this->context();
        $update = $this->update($course, $instructor, ContentUpdate::TYPE_CHAPTER, ContentUpdate::ACTION_CREATE, null, ['title' => 'Chương mới', 'description' => 'Nội dung mới', 'sort_order' => 2]);

        $fields = collect(app(ContentUpdateDiffService::class)->build($update)['fields'])->keyBy('key');

        $this->assertNull($fields['title']['old']);
        $this->assertSame('Chương mới', $fields['title']['new']);
    }

    public function test_lesson_update_only_marks_proposed_changes(): void
    {
        [$instructor, $course, , $lesson] = $this->context();
        $update = $this->update($course, $instructor, ContentUpdate::TYPE_LESSON, ContentUpdate::ACTION_UPDATE, $lesson->id, ['title' => 'Bài đã sửa']);

        $fields = app(ContentUpdateDiffService::class)->build($update)['fields'];

        $this->assertCount(1, $fields);
        $this->assertSame('title', $fields[0]['key']);
        $this->assertSame('Bài cũ', $fields[0]['old']);
    }

    public function test_lesson_delete_resolves_current_entity_within_its_course(): void
    {
        [$instructor, $course, , $lesson] = $this->context();
        $update = $this->update($course, $instructor, ContentUpdate::TYPE_LESSON, ContentUpdate::ACTION_DELETE, $lesson->id, []);

        $diff = app(ContentUpdateDiffService::class)->build($update);

        $this->assertSame('Bài cũ', collect($diff['fields'])->firstWhere('key', 'title')['old']);
        $this->assertSame([], $diff['warnings']);
    }

    public function test_assignment_payload_has_specific_review_fields(): void
    {
        [$instructor, $course, , $lesson] = $this->context();
        $update = $this->update($course, $instructor, ContentUpdate::TYPE_LESSON, ContentUpdate::ACTION_UPDATE, $lesson->id, ['assignment_due_days' => 14, 'assignment_max_score' => 120, 'assignment_passing_score' => 80]);

        $fields = collect(app(ContentUpdateDiffService::class)->build($update)['fields'])->keyBy('key');

        $this->assertSame(7, $fields['assignment_due_days']['old']);
        $this->assertSame(14, $fields['assignment_due_days']['new']);
        $this->assertSame(80, $fields['assignment_passing_score']['new']);
    }

    public function test_reorder_has_current_and_proposed_labels(): void
    {
        [$instructor, $course, $section, $lesson] = $this->context();
        $second = Lesson::create(['course_id' => $course->id, 'section_id' => $section->id, 'title' => 'Bài hai', 'type' => Lesson::TYPE_DOCUMENT, 'sort_order' => 2, 'status' => Lesson::STATUS_PUBLISHED]);
        $update = $this->update($course, $instructor, ContentUpdate::TYPE_LESSON, ContentUpdate::ACTION_REORDER, null, ['lesson_orders' => [['id' => $second->id, 'sort_order' => 1], ['id' => $lesson->id, 'sort_order' => 2]]]);

        $diff = app(ContentUpdateDiffService::class)->build($update);

        $this->assertSame('Bài cũ', $diff['current'][0]['label']);
        $this->assertSame('Bài hai', $diff['proposed'][0]['label']);
    }

    public function test_missing_entity_is_a_safe_fallback(): void
    {
        [$instructor, $course] = $this->context();
        $update = $this->update($course, $instructor, ContentUpdate::TYPE_LESSON, ContentUpdate::ACTION_UPDATE, 999999, ['title' => 'Không tồn tại']);

        $this->assertNotEmpty(app(ContentUpdateDiffService::class)->build($update)['warnings']);
    }

    public function test_rejected_update_stays_immutable_while_its_revision_follows_the_canonical_workflow(): void
    {
        [$instructor, $course, , $lesson] = $this->context();
        $admin = User::factory()->create(['role' => 'admin']);
        $rejected = $this->update($course, $instructor, ContentUpdate::TYPE_LESSON, ContentUpdate::ACTION_UPDATE, $lesson->id, ['title' => 'Bài đề xuất bị từ chối']);

        app(ContentUpdateService::class)->rejectUpdate($rejected, $admin, 'Cần làm rõ nội dung bài học.');

        $this->assertSame(ContentUpdate::STATUS_REJECTED, $rejected->fresh()->status);
        $this->assertSame('Bài cũ', $lesson->fresh()->title);

        $revision = app(ContentUpdateService::class)->createRevisionFromRejected($rejected, $instructor);
        $this->assertSame(ContentUpdate::STATUS_DRAFT, $revision->status);
        $this->assertSame(ContentUpdate::STATUS_REJECTED, $rejected->fresh()->status);

        $course->update(['copyright_agreed' => true]);
        app(CourseReviewService::class)->submitForReview($course->fresh(), $instructor);
        app(ContentUpdateService::class)->applyApprovedUpdate($revision->fresh(), $admin);

        $this->assertSame(ContentUpdate::STATUS_APPROVED, $revision->fresh()->status);
        $this->assertSame('Bài đề xuất bị từ chối', $lesson->fresh()->title);
    }

    public function test_quiz_diff_uses_versioned_question_identities_for_changed_and_added_questions(): void
    {
        [$instructor, $course, $section] = $this->context();
        $lesson = Lesson::create(['course_id' => $course->id, 'section_id' => $section->id, 'title' => 'Quiz', 'type' => Lesson::TYPE_QUIZ, 'status' => Lesson::STATUS_PUBLISHED]);
        $quiz = Quiz::create(['lesson_id' => $lesson->id, 'title' => 'Quiz V1', 'pass_score' => 70, 'time_limit_minutes' => 10, 'max_attempts' => 2, 'is_active' => true]);
        $v1 = QuizVersion::create(['quiz_id' => $quiz->id, 'version' => 1, 'title' => 'Quiz V1', 'pass_score' => 70, 'time_limit_minutes' => 10, 'max_attempts' => 2, 'status' => QuizVersion::STATUS_PUBLISHED, 'created_by' => $instructor->id]);
        $v2 = QuizVersion::create(['quiz_id' => $quiz->id, 'version' => 2, 'title' => 'Quiz V2', 'pass_score' => 80, 'time_limit_minutes' => 15, 'max_attempts' => 3, 'status' => QuizVersion::STATUS_DRAFT, 'created_by' => $instructor->id]);
        $identity = QuizQuestion::create(['quiz_id' => $quiz->id, 'question' => 'Câu hỏi', 'type' => QuizQuestion::TYPE_SINGLE, 'points' => 1, 'sort_order' => 1]);
        $q1 = QuestionVersion::create(['question_id' => $identity->id, 'version' => 1, 'question' => 'Câu hỏi cũ', 'type' => QuizQuestion::TYPE_SINGLE, 'points' => 1, 'status' => QuestionVersion::STATUS_PUBLISHED]);
        $q2 = QuestionVersion::create(['question_id' => $identity->id, 'version' => 2, 'question' => 'Câu hỏi mới', 'type' => QuizQuestion::TYPE_SINGLE, 'points' => 2, 'status' => QuestionVersion::STATUS_DRAFT]);
        QuizOption::create(['quiz_question_id' => $identity->id, 'question_version_id' => $q1->id, 'option_text' => 'A', 'is_correct' => true, 'sort_order' => 1]);
        QuizOption::create(['quiz_question_id' => $identity->id, 'question_version_id' => $q2->id, 'option_text' => 'B', 'is_correct' => true, 'sort_order' => 1]);
        QuizVersionQuestion::create(['quiz_version_id' => $v1->id, 'question_id' => $identity->id, 'question_version_id' => $q1->id, 'sort_order' => 1]);
        QuizVersionQuestion::create(['quiz_version_id' => $v2->id, 'question_id' => $identity->id, 'question_version_id' => $q2->id, 'sort_order' => 1]);
        $addedIdentity = QuizQuestion::create(['quiz_id' => $quiz->id, 'question' => 'Câu thêm', 'type' => QuizQuestion::TYPE_SINGLE, 'points' => 1, 'sort_order' => 2]);
        $added = QuestionVersion::create(['question_id' => $addedIdentity->id, 'version' => 1, 'question' => 'Câu thêm', 'type' => QuizQuestion::TYPE_SINGLE, 'points' => 1, 'status' => QuestionVersion::STATUS_DRAFT]);
        QuizVersionQuestion::create(['quiz_version_id' => $v2->id, 'question_id' => $addedIdentity->id, 'question_version_id' => $added->id, 'sort_order' => 2]);
        $quiz->update(['current_published_version_id' => $v1->id, 'current_draft_version_id' => $v2->id]);
        $update = $this->update($course, $instructor, ContentUpdate::TYPE_QUIZ, ContentUpdate::ACTION_UPDATE, $quiz->id, ['quiz_id' => $quiz->id, 'quiz_version_id' => $v2->id]);

        $diff = app(ContentUpdateDiffService::class)->build($update);

        $this->assertSame(1, $diff['quiz_questions']['current_count']);
        $this->assertSame(2, $diff['quiz_questions']['proposed_count']);
        $this->assertCount(1, $diff['quiz_questions']['added']);
        $this->assertCount(1, $diff['quiz_questions']['changed']);
        $this->assertSame(80, collect($diff['fields'])->firstWhere('key', 'pass_score')['new']);
    }
}
