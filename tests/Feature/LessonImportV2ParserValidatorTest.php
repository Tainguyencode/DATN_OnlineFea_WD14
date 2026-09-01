<?php

namespace Tests\Feature;

use App\Exceptions\LessonImportException;
use App\Models\Assignment;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\InstructorProfile;
use App\Models\LessonImportBatch;
use App\Models\QuestionVersion;
use App\Models\Quiz;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\QuizVersion;
use App\Models\QuizVersionQuestion;
use App\Models\User;
use App\Services\LessonImportParser;
use App\Services\LessonImportTemplateService;
use App\Services\LessonImportV2Validator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use RuntimeException;
use Tests\TestCase;

class LessonImportV2ParserValidatorTest extends TestCase
{
    use RefreshDatabase;

    /** @var array<int, string> */
    private array $temporaryFiles = [];

    protected function tearDown(): void
    {
        foreach ($this->temporaryFiles as $path) {
            if (is_file($path)) {
                unlink($path);
            }
        }

        parent::tearDown();
    }

    public function test_parser_and_cross_sheet_validator_accept_a_complete_v2_quiz_tree(): void
    {
        $parsed = app(LessonImportParser::class)->parse($this->workbookUpload());
        $validated = app(LessonImportV2Validator::class)->validate($parsed['sheets'], $this->section());

        $this->assertSame(2, $parsed['template_version']);
        $this->assertSame(['Lessons', 'Quizzes', 'QuizQuestions', 'QuizOptions'], array_keys($parsed['sheets']));
        $this->assertSame(0, $validated['error_count']);
        $this->assertSame(4, $validated['summary']['lessons']);
        $this->assertSame(1, $validated['summary']['quizzes']);
        $this->assertSame(5, $validated['summary']['questions']);
        $this->assertSame(14, $validated['summary']['options']);
        $this->assertSame('QZ_01', $validated['canonical_payload']['quizzes'][0]['lesson_code']);
        $this->assertSame(0, $validated['canonical_payload']['questions'][0]['relative_order']);
        $this->assertSame(0, $validated['canonical_payload']['options'][0]['relative_order']);
    }

    public function test_parser_preserves_latex_source_strings_in_quiz_fields(): void
    {
        $parsed = app(LessonImportParser::class)->parse($this->workbookUpload(function (Spreadsheet $workbook): void {
            $workbook->getSheetByName('QuizQuestions')->setCellValue('C2', 'Question \\(x_1^2 + x_2^2\\)');
            $workbook->getSheetByName('QuizQuestions')->setCellValue('F2', 'Explain with \\[a^2 + b^2\\]');
            $workbook->getSheetByName('QuizOptions')->setCellValue('C2', 'Option \\(x = 2\\)');
        }));

        $question = $parsed['sheets']['QuizQuestions'][0]['values'];
        $option = $parsed['sheets']['QuizOptions'][0]['values'];

        $this->assertSame('Question \\(x_1^2 + x_2^2\\)', $question['question']);
        $this->assertSame('Explain with \\[a^2 + b^2\\]', $question['explanation']);
        $this->assertSame('Option \\(x = 2\\)', $option['option_text']);
    }

    public function test_parser_rejects_formula_in_every_v2_data_sheet(): void
    {
        $upload = $this->workbookUpload(function (Spreadsheet $workbook): void {
            $workbook->getSheetByName('QuizOptions')->setCellValue('C2', '=1+1');
        });

        try {
            app(LessonImportParser::class)->parse($upload);
            $this->fail('V2 formulas must be rejected without evaluation.');
        } catch (LessonImportException $exception) {
            $this->assertSame('formula_cell', $exception->issueCode);
        }
    }

    public function test_parser_rejects_missing_required_sheets_and_wrong_headers(): void
    {
        $missingSheet = $this->workbookUpload(function (Spreadsheet $workbook): void {
            $workbook->removeSheetByIndex($workbook->getIndex($workbook->getSheetByName('Quizzes')));
        });

        try {
            app(LessonImportParser::class)->parse($missingSheet);
            $this->fail('A required V2 sheet cannot be omitted.');
        } catch (LessonImportException $exception) {
            $this->assertSame('missing_sheet', $exception->issueCode);
        }

        $wrongHeader = $this->workbookUpload(function (Spreadsheet $workbook): void {
            $workbook->getSheetByName('QuizQuestions')->setCellValue('F1', 'unexpected');
        });

        try {
            app(LessonImportParser::class)->parse($wrongHeader);
            $this->fail('V2 headers must be exact.');
        } catch (LessonImportException $exception) {
            $this->assertSame('invalid_headers', $exception->issueCode);
        }
    }

    public function test_cross_sheet_validator_reports_orphans_and_strict_boolean_values(): void
    {
        $parsed = app(LessonImportParser::class)->parse($this->workbookUpload(function (Spreadsheet $workbook): void {
            $workbook->getSheetByName('QuizQuestions')->setCellValue('A2', 'VIDEO_01');
            $workbook->getSheetByName('QuizOptions')->setCellValue('D2', 'yes');
        }));
        $validated = app(LessonImportV2Validator::class)->validate($parsed['sheets'], $this->section());
        $codes = array_column($validated['issues'], 'code');

        $this->assertGreaterThan(0, $validated['error_count']);
        $this->assertContains('question_reference_not_quiz_lesson', $codes);
        $this->assertContains('invalid_is_correct', $codes);
    }

    public function test_preview_returns_the_v2_contract_without_creating_course_content(): void
    {
        [$instructor, $course, $section] = $this->workflowContext();
        $this->actingAs($instructor)->withSession(['two_factor_passed_at' => now()->timestamp]);

        $this->postJson(
            route('instructor.courses.lessons.import.preview', [$course, $section]),
            ['file' => $this->workbookUpload()],
        )
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('batch.template_version', 2)
            ->assertJsonPath('summary.lessons', 4)
            ->assertJsonPath('summary.quizzes', 1)
            ->assertJsonPath('summary.questions', 5)
            ->assertJsonPath('summary.options', 14)
            ->assertJsonPath('summary.errors', 0)
            ->assertJsonPath('sheets.QuizQuestions.0.data.question_code', 'Q1')
            ->assertJsonPath('sheets.QuizOptions.6.data.option_code', 'TRUE')
            ->assertJsonMissingPath('rows');

        $this->assertDatabaseCount('lessons', 0);
        $this->assertDatabaseCount('quizzes', 0);
        $batch = LessonImportBatch::firstOrFail();
        $this->assertSame(2, $batch->template_version);
        $this->assertSame(4, $batch->row_count);
        $this->assertSame(0, $batch->error_count);
    }

    public function test_confirm_creates_a_version_aware_quiz_tree_and_is_idempotent(): void
    {
        [$instructor, $course, $section] = $this->workflowContext();
        $this->actingAs($instructor)->withSession(['two_factor_passed_at' => now()->timestamp]);
        $preview = $this->postJson(
            route('instructor.courses.lessons.import.preview', [$course, $section]),
            ['file' => $this->workbookUpload()],
        )->assertOk();
        $token = (string) $preview->json('batch.token');

        $this->postJson(
            route('instructor.courses.lessons.import.confirm', [$course, $section]),
            ['batch_token' => $token],
        )
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('batch.status', 'completed')
            ->assertJsonPath('batch.imported_count', 4);

        $this->assertDatabaseCount('lessons', 4);
        $this->assertDatabaseCount('assignments', 1);
        $this->assertDatabaseCount('quizzes', 1);
        $this->assertDatabaseCount('quiz_versions', 1);
        $this->assertDatabaseCount('quiz_questions', 5);
        $this->assertDatabaseCount('question_versions', 5);
        $this->assertDatabaseCount('quiz_options', 14);
        $this->assertDatabaseCount('quiz_version_questions', 5);

        $quiz = Quiz::firstOrFail();
        $draft = QuizVersion::firstOrFail();
        $this->assertTrue((bool) $quiz->is_active);
        $this->assertNull($quiz->current_published_version_id);
        $this->assertSame($draft->id, $quiz->current_draft_version_id);
        $this->assertSame(1, $draft->version);
        $this->assertSame(QuizVersion::STATUS_DRAFT, $draft->status);
        $this->assertSame([0, 1, 2, 3, 4], QuizVersionQuestion::query()->orderBy('sort_order')->pluck('sort_order')->all());
        $this->assertSame('Đúng', QuizOption::query()
            ->whereHas('questionVersion', fn ($query) => $query->where('type', 'true_false'))
            ->orderBy('sort_order')
            ->value('option_text'));
        $this->assertSame(5, QuizQuestion::count());
        $this->assertSame(5, QuestionVersion::count());
        $this->assertSame(1, Assignment::count());

        $batch = LessonImportBatch::where('token', $token)->firstOrFail();
        $this->assertSame(2, $batch->result_payload['schema_version']);
        $this->assertArrayHasKey('QZ_01/Q1', $batch->result_payload['questions']);
        $this->assertArrayHasKey('QZ_01/Q3/TRUE', $batch->result_payload['options']);

        $this->postJson(
            route('instructor.courses.lessons.import.confirm', [$course, $section]),
            ['batch_token' => $token],
        )
            ->assertOk()
            ->assertJsonPath('batch.status', 'completed')
            ->assertJsonPath('batch.imported_count', 4);

        $this->assertDatabaseCount('lessons', 4);
        $this->assertDatabaseCount('quizzes', 1);
        $this->assertDatabaseCount('quiz_questions', 5);
    }

    public function test_confirm_allows_an_inactive_draft_quiz_shell(): void
    {
        [$instructor, $course, $section] = $this->workflowContext();
        $this->actingAs($instructor)->withSession(['two_factor_passed_at' => now()->timestamp]);
        $preview = $this->postJson(
            route('instructor.courses.lessons.import.preview', [$course, $section]),
            ['file' => $this->workbookUpload(function (Spreadsheet $workbook): void {
                $workbook->getSheetByName('QuizQuestions')->removeRow(2, 5);
                $workbook->getSheetByName('QuizOptions')->removeRow(2, 14);
            })],
        )
            ->assertOk()
            ->assertJsonPath('summary.errors', 0)
            ->assertJsonPath('summary.questions', 0)
            ->assertJsonPath('summary.options', 0);

        $this->postJson(
            route('instructor.courses.lessons.import.confirm', [$course, $section]),
            ['batch_token' => $preview->json('batch.token')],
        )
            ->assertOk()
            ->assertJsonPath('batch.status', 'completed');

        $quiz = Quiz::firstOrFail();
        $this->assertFalse((bool) $quiz->is_active);
        $this->assertNull($quiz->current_published_version_id);
        $this->assertNotNull($quiz->current_draft_version_id);
        $this->assertDatabaseCount('quiz_questions', 0);
        $this->assertDatabaseCount('quiz_version_questions', 0);
    }

    public function test_confirm_rolls_back_the_entire_v2_tree_when_option_creation_fails(): void
    {
        [$instructor, $course, $section] = $this->workflowContext();
        $this->actingAs($instructor)->withSession(['two_factor_passed_at' => now()->timestamp]);
        $preview = $this->postJson(
            route('instructor.courses.lessons.import.preview', [$course, $section]),
            ['file' => $this->workbookUpload()],
        )->assertOk();
        $token = (string) $preview->json('batch.token');

        $originalDispatcher = QuizOption::getEventDispatcher();
        QuizOption::setEventDispatcher(clone $originalDispatcher);
        QuizOption::creating(function (): void {
            throw new RuntimeException('Simulated option write failure.');
        });

        try {
            $this->postJson(
                route('instructor.courses.lessons.import.confirm', [$course, $section]),
                ['batch_token' => $token],
            )
                ->assertStatus(500)
                ->assertJsonPath('success', false);
        } finally {
            QuizOption::setEventDispatcher($originalDispatcher);
        }

        $this->assertDatabaseCount('lessons', 0);
        $this->assertDatabaseCount('assignments', 0);
        $this->assertDatabaseCount('quizzes', 0);
        $this->assertDatabaseCount('quiz_versions', 0);
        $this->assertDatabaseCount('quiz_questions', 0);
        $this->assertDatabaseCount('question_versions', 0);
        $this->assertDatabaseCount('quiz_options', 0);
        $this->assertDatabaseCount('quiz_version_questions', 0);
        $this->assertSame(LessonImportBatch::STATUS_FAILED, LessonImportBatch::where('token', $token)->value('status'));
    }

    /** @param null|callable(Spreadsheet): void $mutate */
    private function workbookUpload(?callable $mutate = null): UploadedFile
    {
        $workbook = (new LessonImportTemplateService)->createWorkbook(2);
        $workbook->getSheetByName('Lessons')->fromArray([
            ['VIDEO_01', 'Video', 'video', 120, null, null, null, null],
            ['DOC_01', 'Document', 'document', 0, 'Nội dung', null, null, null],
            ['ASSIGN_01', 'Assignment', 'assignment', 0, 'Yêu cầu', 7, 100, 70],
            ['QZ_01', 'Quiz lesson', 'quiz', 0, null, null, null, null],
        ], null, 'A2');
        $workbook->getSheetByName('Quizzes')->fromArray([
            ['QZ_01', 'Quiz title', 'Mô tả', 70, 30, 2, 'TRUE'],
        ], null, 'A2');
        $workbook->getSheetByName('QuizQuestions')->fromArray([
            ['QZ_01', 'Q1', 'Single?', 'single', 10, null],
            ['QZ_01', 'Q2', 'Multiple?', 'multiple', 10, null],
            ['QZ_01', 'Q3', 'True false?', 'true_false', 10, null],
            ['QZ_01', 'Q4', 'Another single?', 'single', 10, null],
            ['QZ_01', 'Q5', 'Another multiple?', 'multiple', 10, null],
        ], null, 'A2');
        $workbook->getSheetByName('QuizOptions')->fromArray([
            ['Q1', 'A', 'A', 'TRUE'], ['Q1', 'B', 'B', 'FALSE'], ['Q1', 'C', 'C', 'FALSE'],
            ['Q2', 'A', 'A', 'TRUE'], ['Q2', 'B', 'B', 'TRUE'], ['Q2', 'C', 'C', 'FALSE'],
            ['Q3', 'TRUE', 'Đúng', 'TRUE'], ['Q3', 'FALSE', 'Sai', 'FALSE'],
            ['Q4', 'A', 'A', 'TRUE'], ['Q4', 'B', 'B', 'FALSE'], ['Q4', 'C', 'C', 'FALSE'],
            ['Q5', 'A', 'A', 'TRUE'], ['Q5', 'B', 'B', 'FALSE'], ['Q5', 'C', 'C', 'FALSE'],
        ], null, 'A2');
        if ($mutate !== null) {
            $mutate($workbook);
        }

        $base = tempnam(sys_get_temp_dir(), 'lesson-import-v2-');
        $path = $base.'.xlsx';
        rename($base, $path);
        $this->temporaryFiles[] = $path;
        (new Xlsx($workbook))->save($path);
        $workbook->disconnectWorksheets();

        return new UploadedFile(
            $path,
            'lesson-import-v2.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true,
        );
    }

    private function section(): CourseSection
    {
        [, , $section] = $this->workflowContext();

        return $section;
    }

    /** @return array{0: User, 1: Course, 2: CourseSection} */
    private function workflowContext(): array
    {
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'instructor_status' => 'approved',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $category = Category::create([
            'name' => 'Import category '.uniqid(),
            'slug' => 'import-category-'.uniqid(),
            'status' => true,
        ]);
        $profile = InstructorProfile::create(['user_id' => $instructor->id]);
        $profile->teachingCategories()->attach($category->id, ['is_primary' => true]);
        $course = Course::create([
            'instructor_id' => $instructor->id,
            'category_id' => $category->id,
            'title' => 'Import course '.uniqid(),
            'slug' => 'import-course-'.uniqid(),
            'short_description' => 'Short description',
            'description' => 'Detailed description',
            'objectives' => 'Learning objectives',
            'target_audience' => 'Students',
            'requirements' => 'None',
            'price' => 100000,
            'language' => 'vi',
            'level' => 'beginner',
            'status' => Course::STATUS_DRAFT,
            'is_published' => false,
        ]);

        $section = CourseSection::create([
            'course_id' => $course->id,
            'title' => 'Section 1',
            'sort_order' => 0,
        ]);

        return [$instructor, $course, $section];
    }
}
