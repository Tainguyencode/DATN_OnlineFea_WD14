<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\User;
use App\Services\CourseSubmissionValidator;
use App\Services\QuizContentService;
use App\Services\QuizVersioningService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizCourseReadinessTest extends TestCase
{
    use RefreshDatabase;

    public function test_quiz_lesson_without_quiz_and_empty_shell_fail_readiness(): void
    {
        [$course, $lesson] = $this->otherwiseReadyCourse();

        $missing = $this->quizItem($course);
        $this->assertFalse($missing['passed']);
        $this->assertStringContainsString("Quiz '{$lesson->title}' chưa có nội dung quiz", $missing['message']);

        app(QuizContentService::class)->getOrCreateForLesson($lesson);
        $empty = $this->quizItem($course->fresh());
        $this->assertFalse($empty['passed']);
        $this->assertStringContainsString('chưa đủ 5 câu hỏi', $empty['message']);
    }

    public function test_four_complete_questions_fail_and_five_pass_even_when_quiz_is_inactive(): void
    {
        [$course, $lesson] = $this->otherwiseReadyCourse();
        $quiz = app(QuizContentService::class)->getOrCreateForLesson($lesson);

        for ($index = 1; $index <= 4; $index++) {
            $this->addCompleteSingleQuestion($quiz, $index);
        }

        $this->assertFalse($this->quizItem($course)['passed']);

        $this->addCompleteSingleQuestion($quiz, 5);

        $item = $this->quizItem($course);
        $this->assertTrue($item['passed']);
        $this->assertFalse($quiz->is_active);
    }

    public function test_malformed_question_fails_with_user_facing_message(): void
    {
        [$course, $lesson] = $this->otherwiseReadyCourse();
        $quiz = app(QuizContentService::class)->getOrCreateForLesson($lesson);

        for ($index = 1; $index <= 5; $index++) {
            $this->addCompleteSingleQuestion($quiz, $index);
        }

        $draft = app(QuizVersioningService::class)->currentDraft($quiz->fresh());
        $draft->questionMappings()->firstOrFail()->questionVersion->options()->where('is_correct', true)->update(['is_correct' => false]);

        $item = $this->quizItem($course);
        $this->assertFalse($item['passed']);
        $this->assertStringContainsString("Câu hỏi 'Question 1' phải có đúng 1 đáp án đúng", $item['message']);
        $this->assertStringNotContainsString('lesson_id', $item['message']);
        $this->assertStringNotContainsString('quiz_id', $item['message']);
    }

    /**
     * @return array{0: Course, 1: Lesson}
     */
    private function otherwiseReadyCourse(): array
    {
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'approved',
            'is_active' => true,
        ]);
        $parent = Category::create([
            'name' => 'Parent '.uniqid(),
            'slug' => 'parent-'.uniqid(),
            'status' => true,
        ]);
        $category = Category::create([
            'parent_id' => $parent->id,
            'name' => 'Child '.uniqid(),
            'slug' => 'child-'.uniqid(),
            'status' => true,
        ]);
        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Ready course',
            'slug' => 'ready-course-'.uniqid(),
            'short_description' => 'Short',
            'description' => 'Detailed description',
            'objectives' => 'Learning objectives',
            'target_audience' => 'Students',
            'requirements' => 'None',
            'thumbnail' => 'thumbnail.jpg',
            'price' => 100000,
            'language' => 'vi',
            'level' => 'beginner',
            'status' => Course::STATUS_DRAFT,
            'is_published' => false,
        ]);
        $section = CourseSection::create(['course_id' => $course->id, 'title' => 'Section', 'sort_order' => 0]);
        Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Ready video',
            'type' => Lesson::TYPE_VIDEO,
            'video_url' => 'https://example.com/video.mp4',
            'duration' => Course::MIN_VIDEO_DURATION_MINUTES * 60,
            'duration_seconds' => Course::MIN_VIDEO_DURATION_MINUTES * 60,
            'sort_order' => 0,
            'status' => Lesson::STATUS_DRAFT,
        ]);

        foreach (range(1, 3) as $index) {
            Lesson::create([
                'course_id' => $course->id,
                'section_id' => $section->id,
                'title' => 'Document '.$index,
                'type' => Lesson::TYPE_DOCUMENT,
                'content' => 'Content',
                'sort_order' => $index,
                'status' => Lesson::STATUS_DRAFT,
            ]);
        }

        $quizLesson = Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Kiểm tra chương 1',
            'type' => Lesson::TYPE_QUIZ,
            'sort_order' => 4,
            'status' => Lesson::STATUS_DRAFT,
        ]);

        return [$course, $quizLesson];
    }

    private function addCompleteSingleQuestion(Quiz $quiz, int $index): void
    {
        app(QuizContentService::class)->createQuestion($quiz->fresh(), [
            'question_text' => 'Question '.$index,
            'question_type' => 'single',
            'score' => 1,
            'sort_order' => $index - 1,
        ], [
            ['option_text' => 'A'.$index, 'is_correct' => true, 'sort_order' => 0],
            ['option_text' => 'B'.$index, 'is_correct' => false, 'sort_order' => 1],
            ['option_text' => 'C'.$index, 'is_correct' => false, 'sort_order' => 2],
        ]);
    }

    /**
     * @return array{key: string, label: string, passed: bool, message: string|null}
     */
    private function quizItem(Course $course): array
    {
        return collect(app(CourseSubmissionValidator::class)->validate($course)->items())
            ->firstWhere('key', CourseSubmissionValidator::KEY_QUIZ_CONTENT);
    }
}
