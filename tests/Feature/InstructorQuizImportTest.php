<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class InstructorQuizImportTest extends TestCase
{
    use RefreshDatabase;

    private function createInstructorQuiz(): array
    {
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'approved',
            'account_status' => 'active',
            'email_verified_at' => now(),
        ]);

        $category = Category::create([
            'name' => 'IT',
            'slug' => 'it-import',
            'status' => true,
        ]);

        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Course For Quiz Import',
            'slug' => 'course-for-quiz-import',
            'status' => 'published',
            'is_published' => true,
        ]);

        $section = CourseSection::create([
            'course_id' => $course->id,
            'title' => 'Section 1',
            'sort_order' => 1,
        ]);

        $lesson = Lesson::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'title' => 'Quiz Lesson',
            'type' => 'quiz',
            'sort_order' => 1,
        ]);

        $quiz = Quiz::create([
            'lesson_id' => $lesson->id,
            'title' => 'Quiz Title',
            'pass_score' => 70,
            'is_active' => true,
        ]);

        return [$instructor, $course, $lesson, $quiz];
    }

    public function test_instructor_can_download_sample_template(): void
    {
        [$instructor, $course, $lesson, $quiz] = $this->createInstructorQuiz();

        $response = $this->actingAs($instructor)
            ->get(route('instructor.quizzes.questions.sample-template'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Nội dung câu hỏi', $response->streamedContent());
    }

    public function test_instructor_can_import_quiz_questions_from_csv(): void
    {
        [$instructor, $course, $lesson, $quiz] = $this->createInstructorQuiz();

        $csvContent = "\xEF\xBB\xBF".
            "Nội dung câu hỏi,Loại câu hỏi,Điểm,Giải thích,Đáp án 1,Đáp án 2,Đáp án 3,Đáp án 4,Đáp án đúng\n".
            "Câu hỏi 1 trắc nghiệm,single_choice,2,Giải thích câu 1,Lựa chọn A,Lựa chọn B,Lựa chọn C,Lựa chọn D,2\n".
            "Câu hỏi 2 nhiều đáp án,multiple_choice,3,Giải thích câu 2,Ý A,Ý B,Ý C,Ý D,\"1,3\"\n".
            "Câu hỏi 3 đúng sai,true_false,1,Giải thích câu 3,Đúng,Sai,,,1\n";

        $file = UploadedFile::fake()->createWithContent('questions.csv', $csvContent);

        $response = $this->actingAs($instructor)
            ->post(route('instructor.quizzes.questions.import', $quiz), [
                'import_file' => $file,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertSame(3, $quiz->questions()->count());

        $q1 = $quiz->questions()->first();
        $this->assertSame('Câu hỏi 1 trắc nghiệm', $q1->question);
        $this->assertSame(2, $q1->points);
        $this->assertSame('single_choice', $q1->form_type);
        $this->assertSame(4, $q1->options()->count());
        $this->assertTrue((bool) $q1->options()->where('option_text', 'Lựa chọn B')->first()->is_correct);

        $q2 = $quiz->questions()->skip(1)->first();
        $this->assertSame('multiple_choice', $q2->form_type);
        $this->assertSame(2, $q2->options()->where('is_correct', true)->count());
    }

    public function test_non_owner_instructor_cannot_import_questions(): void
    {
        [$instructor, $course, $lesson, $quiz] = $this->createInstructorQuiz();

        $otherInstructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'approved',
            'account_status' => 'active',
            'email_verified_at' => now(),
        ]);

        $file = UploadedFile::fake()->createWithContent('questions.csv', 'test,single_choice,1,test,A,B,,1');

        $response = $this->actingAs($otherInstructor)
            ->post(route('instructor.quizzes.questions.import', $quiz), [
                'import_file' => $file,
            ]);

        $response->assertForbidden();
    }
}
