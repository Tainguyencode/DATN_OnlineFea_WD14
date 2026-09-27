<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\FullCourseImportBatch;
use App\Models\Lesson;
use App\Models\QuestionVersion;
use App\Models\Quiz;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\QuizVersion;
use App\Models\QuizVersionQuestion;
use App\Models\User;
use App\Services\FullCourseImportPreviewService;
use App\Services\FullCourseImportTemplateService;
use App\Services\QuizContentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Mockery;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use RuntimeException;
use Tests\TestCase;

class FullCourseImportConfirmTest extends TestCase
{
    use RefreshDatabase;

    /** @var array<int, string> */
    private array $files = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
    }

    protected function tearDown(): void
    {
        foreach ($this->files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        parent::tearDown();
    }

    public function test_confirm_creates_a_draft_course_full_curriculum_and_versioned_quiz_graph(): void
    {
        $category = $this->selectableCategory();
        $instructor = $this->instructor($category);
        $batch = $this->preview($instructor);

        $response = $this->actingAs($instructor)->postJson(route('instructor.courses.full-import.confirm'), [
            'batch_token' => $batch->token,
            // Confirm intentionally ignores client-controlled content fields.
            'title' => 'Tampered browser title',
            'category_slug' => 'tampered-category',
            'price' => 1,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('idempotent', false)
            ->assertJsonPath('batch.status', FullCourseImportBatch::STATUS_COMPLETED);

        $courseId = $response->json('batch.result.course_id');
        $this->assertIsInt($courseId);
        $response->assertJsonPath('redirect_url', route('instructor.courses.curriculum', $courseId));

        $course = Course::query()->whereKey($courseId)->firstOrFail();
        $this->assertSame(1, Course::query()->whereKey($courseId)->count());
        $this->assertSame('Khóa học xác nhận v3', $course->title);
        $this->assertSame($category->id, $course->category_id);
        $this->assertSame($instructor->id, $course->instructor_id);
        $this->assertSame(Course::STATUS_DRAFT, $course->status);
        $this->assertFalse($course->is_published);
        $this->assertNull($course->published_at);
        $this->assertSame('90000.00', $course->sale_price);
        $this->assertSame('90000.00', $course->discount_price);

        $sections = CourseSection::query()->where('course_id', $course->id)->orderBy('sort_order')->get();
        $this->assertSame(['Chương video', 'Chương tài liệu', 'Chương kiểm tra'], $sections->pluck('title')->all());
        $this->assertSame([0, 1, 2], $sections->pluck('sort_order')->all());
        $this->assertSame(4, Lesson::where('course_id', $course->id)->count());
        $this->assertSame(4, Lesson::where('course_id', $course->id)->where('status', Lesson::STATUS_PUBLISHED)->count());

        $video = Lesson::where('course_id', $course->id)->where('type', Lesson::TYPE_VIDEO)->firstOrFail();
        $this->assertSame(120, $video->duration_seconds);
        $this->assertFalse($video->hasVideoSource());
        $this->assertNull($video->video_path);
        $this->assertNull($video->video_url);

        $assignment = Lesson::where('course_id', $course->id)->where('type', Lesson::TYPE_ASSIGNMENT)->firstOrFail()->assignment;
        $this->assertNotNull($assignment);
        $this->assertSame(7, $assignment->due_days);
        $this->assertSame(120, $assignment->max_score);
        $this->assertSame(80, $assignment->passing_score);

        $quiz = Quiz::firstOrFail();
        $this->assertSame('Quiz xác nhận', $quiz->title);
        $this->assertTrue($quiz->is_active);
        $this->assertNull($quiz->current_published_version_id);
        $draft = $quiz->currentDraftVersion()->firstOrFail();
        $this->assertSame(1, $draft->version);
        $this->assertSame(QuizVersion::STATUS_DRAFT, $draft->status);
        $this->assertSame(5, QuizQuestion::where('quiz_id', $quiz->id)->count());
        $this->assertSame(5, QuestionVersion::whereIn('question_id', QuizQuestion::where('quiz_id', $quiz->id)->pluck('id'))->where('version', 1)->count());
        $this->assertSame(5, QuizVersionQuestion::where('quiz_version_id', $draft->id)->count());
        $this->assertSame(['single', 'multiple', 'true_false', 'single', 'multiple'], $draft->questionVersions()->pluck('type')->all());
        $this->assertSame('Giải \(x^2 = 4\)', $draft->questionVersions()->where('question', 'like', 'Giải%')->value('question'));
        $trueFalseVersion = $draft->questionVersions()->where('question_versions.type', 'true_false')->firstOrFail();
        $this->assertSame(2, QuizOption::where('question_version_id', $trueFalseVersion->id)->count());

        $batch->refresh();
        $this->assertSame(FullCourseImportBatch::STATUS_COMPLETED, $batch->status);
        $this->assertNotNull($batch->completed_at);
        $this->assertSame($course->id, $batch->result_payload['course_id']);
        $this->assertSame($course->slug, $batch->result_payload['course_slug']);
        $this->assertSame($sections->first()->id, $batch->result_payload['sections']['CH01']);
        $this->assertSame($quiz->id, $batch->result_payload['quizzes']['QUIZ_01']['quiz_id']);
        $this->assertSame($draft->id, $batch->result_payload['quizzes']['QUIZ_01']['quiz_version_id']);
        $this->assertCount(5, $batch->result_payload['questions']);

        $secondResponse = $this->actingAs($instructor)->postJson(route('instructor.courses.full-import.confirm'), ['batch_token' => $batch->token]);
        $secondResponse->assertOk()->assertJsonPath('idempotent', true);
        $this->assertSame($courseId, $secondResponse->json('batch.result.course_id'));
        $this->assertSame(1, Course::count());
        $this->assertSame(1, Quiz::count());
    }

    public function test_confirm_rejects_a_batch_owned_by_another_instructor_without_writes(): void
    {
        $category = $this->selectableCategory();
        $owner = $this->instructor($category);
        $other = $this->instructor($category);
        $batch = $this->preview($owner);

        $this->actingAs($other)->postJson(route('instructor.courses.full-import.confirm'), ['batch_token' => $batch->token])
            ->assertForbidden()->assertJsonPath('error_code', 'batch_forbidden');

        $this->assertSame(0, Course::count());
        $this->assertSame(FullCourseImportBatch::STATUS_PREVIEWED, $batch->fresh()->status);
    }

    public function test_full_course_import_uses_the_standard_submission_requirements(): void
    {
        $category = $this->selectableCategory();
        $instructor = $this->instructor($category);
        $batch = $this->preview($instructor);

        $this->actingAs($instructor)->postJson(route('instructor.courses.full-import.confirm'), [
            'batch_token' => $batch->token,
        ])->assertOk();

        $course = Course::firstOrFail();
        $errors = $course->submissionCheck()->errorMessages();
        $keys = collect($course->submissionCheck()->items())->pluck('key')->all();

        $this->assertContains('Thiếu thumbnail', $errors);
        $this->assertNotContains('preview_lesson', $keys);
    }

    public function test_confirm_revalidates_a_category_that_was_removed_after_preview(): void
    {
        $category = $this->selectableCategory();
        $instructor = $this->instructor($category);
        $batch = $this->preview($instructor);
        $category->update(['status' => false]);

        $this->actingAs($instructor)->postJson(route('instructor.courses.full-import.confirm'), ['batch_token' => $batch->token])
            ->assertUnprocessable()->assertJsonPath('error_code', 'invalid_canonical_payload');

        $this->assertSame(0, Course::count());
        $this->assertSame(FullCourseImportBatch::STATUS_PREVIEWED, $batch->fresh()->status);
    }

    public function test_a_late_quiz_failure_rolls_back_all_writes_and_leaves_batch_retryable(): void
    {
        $category = $this->selectableCategory();
        $instructor = $this->instructor($category);
        $batch = $this->preview($instructor);
        $mock = Mockery::mock(QuizContentService::class);
        $mock->shouldReceive('saveMetadata')->andThrow(new RuntimeException('late quiz failure'));
        $this->app->instance(QuizContentService::class, $mock);

        $this->actingAs($instructor)->postJson(route('instructor.courses.full-import.confirm'), ['batch_token' => $batch->token])
            ->assertStatus(500);

        $this->assertSame(0, Course::count());
        $this->assertSame(0, CourseSection::count());
        $this->assertSame(0, Lesson::count());
        $this->assertSame(FullCourseImportBatch::STATUS_PREVIEWED, $batch->fresh()->status);

        $this->app->forgetInstance(QuizContentService::class);
        $this->actingAs($instructor)->postJson(route('instructor.courses.full-import.confirm'), ['batch_token' => $batch->token])
            ->assertOk()->assertJsonPath('idempotent', false);
        $this->assertSame(1, Course::count());
    }

    private function instructor(?Category $category = null): User
    {
        $user = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'approved',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        if ($category) {
            $user->instructorProfile()->create([
                'category_id' => $category->id,
            ]);
        }

        return $user;
    }

    private function selectableCategory(): Category
    {
        return Category::create(['name' => 'JavaScript', 'slug' => 'javascript', 'status' => true]);
    }

    private function preview(User $user): FullCourseImportBatch
    {
        $batch = app(FullCourseImportPreviewService::class)->preview($this->workbook(), $user)['batch'];
        $this->assertSame(0, $batch->error_count, json_encode($batch->validation_report));

        return $batch;
    }

    private function workbook(): UploadedFile
    {
        $book = app(FullCourseImportTemplateService::class)->createWorkbook();
        $book->getSheetByName('Course')->fromArray([['Khóa học xác nhận v3', 'Ngắn', 'Mô tả', 'Mục tiêu', 'javascript', 'beginner', 'vi', 100000, 90000]], null, 'A2');
        $book->getSheetByName('Sections')->fromArray([
            ['CH01', 'Chương video', ''], ['CH02', 'Chương tài liệu', ''], ['CH03', 'Chương kiểm tra', ''],
        ], null, 'A2');
        $book->getSheetByName('Lessons')->fromArray([
            ['CH01', 'VIDEO_01', 'Video shell', 'video', 120, null, null, null, null],
            ['CH02', 'DOC_01', 'Tài liệu', 'document', 0, 'Nội dung tài liệu', null, null, null],
            ['CH02', 'ASM_01', 'Bài tập', 'assignment', 0, 'Yêu cầu bài tập', 7, 120, 80],
            ['CH03', 'QUIZ_01', 'Quiz shell', 'quiz', 0, null, null, null, null],
        ], null, 'A2');
        $book->getSheetByName('Quizzes')->fromArray([['QUIZ_01', 'Quiz xác nhận', 'Mô tả quiz', 70, 30, 2, 'TRUE']], null, 'A2');
        $book->getSheetByName('QuizQuestions')->fromArray([
            ['QUIZ_01', 'Q_01', 'Câu single', 'single', 1, null],
            ['QUIZ_01', 'Q_02', 'Câu multiple', 'multiple', 2, null],
            ['QUIZ_01', 'Q_03', 'Câu đúng sai', 'true_false', 1, null],
            ['QUIZ_01', 'Q_04', 'Giải \\(x^2 = 4\\)', 'single', 1, 'LaTex giữ nguyên'],
            ['QUIZ_01', 'Q_05', 'Câu multiple 2', 'multiple', 2, null],
        ], null, 'A2');
        $book->getSheetByName('QuizOptions')->fromArray([
            ['Q_01', 'A', 'A', 'TRUE'], ['Q_01', 'B', 'B', 'FALSE'], ['Q_01', 'C', 'C', 'FALSE'],
            ['Q_02', 'A', 'A', 'TRUE'], ['Q_02', 'B', 'B', 'TRUE'], ['Q_02', 'C', 'C', 'FALSE'],
            ['Q_03', 'TRUE', 'Đúng', 'TRUE'], ['Q_03', 'FALSE', 'Sai', 'FALSE'],
            ['Q_04', 'A', '2 và -2', 'TRUE'], ['Q_04', 'B', 'Bốn', 'FALSE'], ['Q_04', 'C', 'Khác', 'FALSE'],
            ['Q_05', 'A', 'A', 'TRUE'], ['Q_05', 'B', 'B', 'FALSE'], ['Q_05', 'C', 'C', 'TRUE'],
        ], null, 'A2');
        $base = tempnam(sys_get_temp_dir(), 'full-course-confirm-');
        $path = $base.'.xlsx';
        rename($base, $path);
        $this->files[] = $path;
        (new Xlsx($book))->save($path);
        $book->disconnectWorksheets();

        return new UploadedFile($path, 'full-course-confirm.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
    }
}
