<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\LessonImportBatch;
use App\Models\Quiz;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\User;
use App\Services\QuizContentService;
use App\Services\QuizService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class QuizFoundationTest extends TestCase
{
    use RefreshDatabase;

    private QuizContentService $content;

    protected function setUp(): void
    {
        parent::setUp();

        $this->content = app(QuizContentService::class);
    }

    public function test_lazy_quiz_shell_is_inactive_idempotent_and_uses_canonical_aliases(): void
    {
        [, , $lesson] = $this->courseWithQuizLesson();

        $first = $this->content->getOrCreateForLesson($lesson);
        $second = $this->content->getOrCreateForLesson($lesson);

        $this->assertSame($first->id, $second->id);
        $this->assertFalse($first->is_active);
        $this->assertSame(1, Quiz::where('lesson_id', $lesson->id)->count());
        $this->assertSame(QuizQuestion::TYPE_SINGLE, $this->content->canonicalType('single_choice'));
        $this->assertSame(QuizQuestion::TYPE_MULTIPLE, $this->content->canonicalType('multiple_choice'));
        $this->assertSame(QuizQuestion::TYPE_TRUE_FALSE, $this->content->canonicalType('true_false'));
    }

    public function test_database_rejects_two_quizzes_for_the_same_lesson(): void
    {
        [, , $lesson] = $this->courseWithQuizLesson();
        Quiz::create($this->quizAttributes($lesson));

        $this->expectException(QueryException::class);

        Quiz::create($this->quizAttributes($lesson));
    }

    public function test_single_question_rule_matrix_and_duplicate_option_text(): void
    {
        $question = $this->question(QuizQuestion::TYPE_SINGLE);

        $this->replaceOptions($question, [['A', true], ['B', false], ['C', false]]);
        $this->assertTrue($this->content->validateQuestion($question->refresh())['is_complete']);

        $this->replaceOptions($question, [['A', false], ['B', false], ['C', false]]);
        $this->assertFalse($this->content->validateQuestion($question->refresh())['is_complete']);

        $this->replaceOptions($question, [['A', true], ['B', true], ['C', false]]);
        $this->assertFalse($this->content->validateQuestion($question->refresh())['is_complete']);

        $this->replaceOptions($question, [['A', true], ['B', false]]);
        $this->assertFalse($this->content->validateQuestion($question->refresh())['is_complete']);

        $this->replaceOptions($question, [['PHP', true], [' php ', false], ['Laravel', false]]);
        $result = $this->content->validateQuestion($question->refresh());
        $this->assertFalse($result['is_complete']);
        $this->assertStringContainsString('trùng nội dung', implode(' ', $result['errors']));
    }

    public function test_multiple_question_rule_matrix_and_all_correct_warning(): void
    {
        $question = $this->question(QuizQuestion::TYPE_MULTIPLE);

        foreach ([1, 2] as $correctCount) {
            $this->replaceOptions($question, [
                ['A', $correctCount >= 1],
                ['B', $correctCount >= 2],
                ['C', false],
            ]);
            $this->assertTrue($this->content->validateQuestion($question->refresh())['is_complete']);
        }

        $this->replaceOptions($question, [['A', true], ['B', true], ['C', true]]);
        $allCorrect = $this->content->validateQuestion($question->refresh());
        $this->assertTrue($allCorrect['is_complete']);
        $this->assertNotEmpty($allCorrect['warnings']);

        $this->replaceOptions($question, [['A', false], ['B', false], ['C', false]]);
        $this->assertFalse($this->content->validateQuestion($question->refresh())['is_complete']);

        $this->replaceOptions($question, [['A', true], ['B', false]]);
        $this->assertFalse($this->content->validateQuestion($question->refresh())['is_complete']);

        $options = [];
        for ($index = 1; $index <= 11; $index++) {
            $options[] = ['Option '.$index, $index === 1];
        }
        $this->replaceOptions($question, $options);
        $this->assertFalse($this->content->validateQuestion($question->refresh())['is_complete']);

        $this->replaceOptions($question, array_slice($options, 0, 10));
        $this->expectException(ValidationException::class);
        $this->content->createOption($question->refresh(), [
            'answer_text' => 'Option 11',
            'is_correct' => false,
        ]);
    }

    public function test_true_false_is_portable_and_conversion_removes_stale_options(): void
    {
        [, , $lesson] = $this->courseWithQuizLesson();
        $quiz = Quiz::create($this->quizAttributes($lesson));
        $question = $this->content->createQuestion($quiz, [
            'question_text' => 'PHP is a language?',
            'question_type' => 'true_false',
            'score' => 1,
        ]);

        $this->assertSame(QuizQuestion::TYPE_TRUE_FALSE, $question->type);
        $this->assertSame(['Đúng', 'Sai'], $question->options->pluck('option_text')->all());
        $this->assertSame([0, 1], $question->options->pluck('sort_order')->all());
        $this->assertTrue($this->content->validateQuestion($question)['is_complete']);

        $importReady = $this->content->createQuestion($quiz, [
            'question_text' => 'Imported true/false',
            'question_type' => 'true_false',
            'score' => 1,
        ], [
            ['identity' => 'TRUE', 'is_correct' => false],
            ['identity' => 'FALSE', 'is_correct' => true],
        ]);
        $this->assertFalse($importReady->options[0]->is_correct);
        $this->assertTrue($importReady->options[1]->is_correct);

        $this->replaceOptions($question, [['Only', true]]);
        $this->assertFalse($this->content->validateQuestion($question->refresh())['is_complete']);

        $this->replaceOptions($question, [['A', true], ['B', true]]);
        $this->assertFalse($this->content->validateQuestion($question->refresh())['is_complete']);

        $this->replaceOptions($question, [['A', false], ['B', false]]);
        $this->assertFalse($this->content->validateQuestion($question->refresh())['is_complete']);

        $this->replaceOptions($question, [['First', false], ['Second', true], ['Stale', false]]);
        $this->content->updateQuestion($question, [
            'question_text' => $question->question,
            'question_type' => 'true_false',
            'score' => 1,
        ]);

        $question->refresh()->load('options');
        $this->assertCount(2, $question->options);
        $this->assertSame(['Đúng', 'Sai'], $question->options->pluck('option_text')->all());
        $this->assertTrue($question->options[1]->is_correct);

        $this->content->updateQuestion($question, [
            'question_text' => $question->question,
            'question_type' => 'single_choice',
            'score' => 1,
        ]);
        $this->assertCount(2, $question->refresh()->options);
        $this->assertFalse($this->content->validateQuestion($question)['is_complete']);
    }

    public function test_quiz_grading_uses_exact_sets_weights_and_percentage_pass_score(): void
    {
        [, , $lesson] = $this->courseWithQuizLesson();
        $quiz = Quiz::create([...$this->quizAttributes($lesson), 'pass_score' => 70]);
        $single = $this->question(QuizQuestion::TYPE_SINGLE, $quiz, 7, 'Single');
        $singleOptions = $this->replaceOptions($single, [['Right', true], ['Wrong', false], ['Other', false]]);
        $multiple = $this->question(QuizQuestion::TYPE_MULTIPLE, $quiz, 3, 'Multiple');
        $multipleOptions = $this->replaceOptions($multiple, [['A', true], ['B', true], ['C', false]]);

        $grade = app(QuizService::class)->grade($quiz, [
            $single->id => $singleOptions[0]->id,
            $multiple->id => [$multipleOptions[0]->id],
        ]);

        $this->assertSame(7, $grade['score']);
        $this->assertSame(10, $grade['total_score']);
        $this->assertSame(70.0, $grade['percent']);
        $this->assertTrue($grade['passed']);
        $this->assertFalse($grade['questions'][$multiple->id]['is_correct']);

        $exact = app(QuizService::class)->grade($quiz, [
            $single->id => $singleOptions[0]->id,
            $multiple->id => [$multipleOptions[1]->id, $multipleOptions[0]->id],
        ]);
        $this->assertTrue($exact['questions'][$multiple->id]['is_correct']);
    }

    public function test_result_payload_is_nullable_and_cast_to_array(): void
    {
        [$user, $course, $lesson] = $this->courseWithQuizLesson();
        $batch = LessonImportBatch::create([
            'token' => str_repeat('a', 64),
            'user_id' => $user->id,
            'course_id' => $course->id,
            'section_id' => $lesson->section_id,
            'original_filename' => 'lessons.xlsx',
            'file_sha256' => str_repeat('b', 64),
            'template_version' => 1,
            'canonical_payload' => [],
            'validation_report' => [],
            'result_payload' => null,
            'status' => LessonImportBatch::STATUS_PREVIEWED,
            'expires_at' => now()->addHour(),
        ]);

        $this->assertNull($batch->result_payload);

        $batch->update(['result_payload' => ['lesson_code' => ['Q1' => 123]]]);

        $this->assertSame(['lesson_code' => ['Q1' => 123]], $batch->fresh()->result_payload);
    }

    /**
     * @return array{0: User, 1: Course, 2: Lesson}
     */
    private function courseWithQuizLesson(): array
    {
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'approved',
            'is_active' => true,
        ]);
        $category = Category::create(['name' => 'Quiz '.uniqid(), 'slug' => 'quiz-'.uniqid(), 'status' => true]);
        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Quiz course',
            'slug' => 'quiz-course-'.uniqid(),
            'description' => 'Description',
            'price' => 0,
            'language' => 'vi',
            'status' => Course::STATUS_DRAFT,
        ]);
        $section = CourseSection::create(['course_id' => $course->id, 'title' => 'Section', 'sort_order' => 0]);
        $lesson = Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Quiz lesson',
            'type' => Lesson::TYPE_QUIZ,
            'sort_order' => 0,
            'status' => Lesson::STATUS_DRAFT,
        ]);

        return [$instructor, $course, $lesson];
    }

    /**
     * @return array<string, mixed>
     */
    private function quizAttributes(Lesson $lesson): array
    {
        return [
            'lesson_id' => $lesson->id,
            'title' => 'Quiz',
            'pass_score' => 70,
            'is_active' => false,
        ];
    }

    private function question(
        string $type,
        ?Quiz $quiz = null,
        int $points = 1,
        string $text = 'Question',
    ): QuizQuestion {
        if (! $quiz) {
            [, , $lesson] = $this->courseWithQuizLesson();
            $quiz = Quiz::create($this->quizAttributes($lesson));
        }

        return QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question' => $text,
            'type' => $type,
            'points' => $points,
            'sort_order' => $quiz->questions()->count(),
        ]);
    }

    /**
     * @param  array<int, array{0: string, 1: bool}>  $options
     * @return array<int, QuizOption>
     */
    private function replaceOptions(QuizQuestion $question, array $options): array
    {
        $question->options()->delete();

        return collect($options)->map(function (array $option, int $index) use ($question): QuizOption {
            return QuizOption::create([
                'quiz_question_id' => $question->id,
                'option_text' => $option[0],
                'is_correct' => $option[1],
                'sort_order' => $index,
            ]);
        })->all();
    }
}
