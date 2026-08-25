<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\User;
use App\Services\QuizContentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstructorQuizAuthoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_lazily_creates_one_inactive_shell_and_explains_save_requirements(): void
    {
        [$instructor, $course, $lesson] = $this->authoringContext();

        $this->actingAs($instructor)
            ->get(route('instructor.courses.lessons.quiz.show', [$course, $lesson]))
            ->assertOk()
            ->assertSee('Quiz chưa thể lưu/đưa vào sử dụng vì còn thiếu:')
            ->assertSee('chưa đủ 5 câu hỏi')
            ->assertSee('disabled', false);

        $this->actingAs($instructor)
            ->get(route('instructor.courses.lessons.quiz.show', [$course, $lesson]))
            ->assertOk();

        $this->assertSame(1, Quiz::where('lesson_id', $lesson->id)->count());
        $this->assertFalse(Quiz::where('lesson_id', $lesson->id)->firstOrFail()->is_active);
    }

    public function test_manual_question_option_mutations_delegate_to_shared_rules(): void
    {
        [$instructor, $course, $lesson] = $this->authoringContext();
        $quiz = app(QuizContentService::class)->getOrCreateForLesson($lesson);

        $this->actingAs($instructor)
            ->post(route('instructor.quizzes.questions.store', $quiz), [
                'question_text' => 'What is PHP?',
                'question_type' => 'single_choice',
                'score' => 2,
            ])
            ->assertSessionHasNoErrors();

        $single = $quiz->questions()->firstOrFail();
        $this->assertSame(QuizQuestion::TYPE_SINGLE, $single->type);

        foreach ([['PHP', true], ['Laravel', false], ['Symfony', false]] as $index => [$text, $correct]) {
            $this->actingAs($instructor)
                ->post(route('instructor.quiz-questions.answers.store', $single), [
                    'answer_text' => $text,
                    'is_correct' => $correct,
                    'sort_order' => $index,
                    'target_question_id' => $single->id,
                ])
                ->assertSessionHasNoErrors();
        }

        $this->actingAs($instructor)
            ->from(route('instructor.courses.lessons.quiz.show', [$course, $lesson]))
            ->post(route('instructor.quiz-questions.answers.store', $single), [
                'answer_text' => ' php ',
                'target_question_id' => $single->id,
            ])
            ->assertSessionHasErrors('answer_text');

        $this->actingAs($instructor)
            ->post(route('instructor.quizzes.questions.store', $quiz), [
                'question_text' => 'The statement is true?',
                'question_type' => 'true_false',
                'score' => 1,
            ])
            ->assertSessionHasNoErrors();

        $trueFalse = $quiz->questions()->where('type', QuizQuestion::TYPE_TRUE_FALSE)->firstOrFail();
        $this->assertSame(['Đúng', 'Sai'], $trueFalse->options->pluck('option_text')->all());

        $this->actingAs($instructor)
            ->put(route('instructor.quiz-questions.update', $single), [
                'question_text' => $single->question,
                'question_type' => 'true_false',
                'score' => 2,
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame(QuizQuestion::TYPE_TRUE_FALSE, $single->refresh()->type);
        $this->assertCount(2, $single->options);
    }

    public function test_metadata_save_uses_shared_completeness_and_can_activate_only_complete_quiz(): void
    {
        [$instructor, $course, $lesson] = $this->authoringContext();
        $quiz = app(QuizContentService::class)->getOrCreateForLesson($lesson);

        $this->actingAs($instructor)
            ->post(route('instructor.courses.lessons.quiz.store', [$course, $lesson]), [
                'title' => 'Chapter quiz',
                'pass_score' => 70,
                'is_active' => 1,
            ])
            ->assertSessionHasErrors('quiz');

        for ($index = 1; $index <= 5; $index++) {
            $question = QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question' => 'Question '.$index,
                'type' => QuizQuestion::TYPE_SINGLE,
                'points' => 1,
                'sort_order' => $index,
            ]);

            foreach ([['A', true], ['B', false], ['C', false]] as $sortOrder => [$text, $correct]) {
                QuizOption::create([
                    'quiz_question_id' => $question->id,
                    'option_text' => $text.$index,
                    'is_correct' => $correct,
                    'sort_order' => $sortOrder,
                ]);
            }
        }

        $this->actingAs($instructor)
            ->post(route('instructor.courses.lessons.quiz.store', [$course, $lesson]), [
                'title' => ' Chapter quiz ',
                'description' => ' Description ',
                'pass_score' => 70,
                'time_limit_minutes' => 60,
                'max_attempts' => 3,
                'is_active' => 1,
            ])
            ->assertSessionHasNoErrors();

        $quiz->refresh();
        $this->assertSame('Chapter quiz', $quiz->title);
        $this->assertSame('Description', $quiz->description);
        $this->assertTrue($quiz->is_active);
    }

    /**
     * @return array{0: User, 1: Course, 2: Lesson}
     */
    private function authoringContext(): array
    {
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'approved',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $category = Category::create(['name' => 'Quiz '.uniqid(), 'slug' => 'quiz-'.uniqid(), 'status' => true]);
        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Quiz authoring',
            'slug' => 'quiz-authoring-'.uniqid(),
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
}
