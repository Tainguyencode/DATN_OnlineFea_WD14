<?php

namespace Tests\Feature;

use App\Exceptions\LessonImportException;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\FullCourseImportBatch;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\User;
use App\Services\FullCourseImportPreviewService;
use App\Services\FullCourseImportTemplateService;
use App\Services\FullCourseImportValidator;
use App\Services\LessonImportParser;
use App\Support\FullCourseImportWorkbookSchema as Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class FullCourseImportV3Test extends TestCase
{
    use RefreshDatabase;

    /** @var array<int, string> */
    private array $files = [];

    protected function tearDown(): void
    {
        foreach ($this->files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        parent::tearDown();
    }

    public function test_v3_template_has_the_exact_contract_and_no_ids(): void
    {
        $book = app(FullCourseImportTemplateService::class)->createWorkbook();
        $this->assertSame(Schema::SHEETS, $book->getSheetNames());
        $this->assertSame(['template_version', (string) Schema::VERSION], $book->getSheetByName('_meta')->rangeToArray('A1:B1')[0]);
        $this->assertSame(Schema::COURSE_HEADERS, $book->getSheetByName('Course')->rangeToArray('A1:I1')[0]);
        $this->assertSame(Schema::LESSON_HEADERS, $book->getSheetByName('Lessons')->rangeToArray('A1:I1')[0]);
        $this->assertNotEmpty($book->getSheetByName('Lessons')->getCell('D2')->getDataValidation()->getFormula1());
        $this->assertStringNotContainsString('id', implode('|', Schema::COURSE_HEADERS));
        $book->disconnectWorksheets();
    }

    public function test_v3_parser_and_validator_preserve_latex_and_build_full_graph(): void
    {
        $this->selectableCategory();
        $upload = $this->workbook(function (Spreadsheet $book): void {
            $book->getSheetByName('QuizQuestions')->setCellValue('C2', 'Cho \\(x_1^2 + x_2^2\\)');
        });
        $parsed = app(LessonImportParser::class)->parse($upload);
        $validated = app(FullCourseImportValidator::class)->validate($parsed['sheets']);
        $this->assertSame(3, $parsed['template_version']);
        $this->assertSame(Schema::SCHEMA, $parsed['schema']);
        $this->assertSame(0, $validated['error_count']);
        $this->assertSame('CH01', $validated['canonical_payload']['lessons'][0]['section_code']);
        $this->assertSame('Cho \\(x_1^2 + x_2^2\\)', $validated['canonical_payload']['questions'][0]['question']);
        $this->assertSame(1, $validated['summary']['video']);
    }

    public function test_preview_persists_only_the_v3_batch_and_no_course_content(): void
    {
        $this->selectableCategory();
        $user = User::factory()->create(['role' => 'instructor']);
        $result = app(FullCourseImportPreviewService::class)->preview($this->workbook(), $user);
        $this->assertDatabaseCount('full_course_import_batches', 1);
        $this->assertSame(0, Course::count());
        $this->assertSame(0, CourseSection::count());
        $this->assertSame(0, Lesson::count());
        $this->assertSame(0, Quiz::count());
        $this->assertSame(0, QuizQuestion::count());
        $this->assertSame(0, QuizOption::count());
        $this->assertSame(FullCourseImportBatch::STATUS_PREVIEWED, $result['batch']->status);
        $this->assertSame(Schema::SCHEMA, $result['batch']->canonical_payload['schema']);
    }

    public function test_v3_parser_rejects_formulas_and_missing_sheets(): void
    {
        try {
            app(LessonImportParser::class)->parse($this->workbook(fn (Spreadsheet $book) => $book->getSheetByName('Lessons')->setCellValue('C2', '=1+1')));
            $this->fail('Formula must be rejected.');
        } catch (LessonImportException $exception) {
            $this->assertSame('formula_cell', $exception->issueCode);
        }
        try {
            app(LessonImportParser::class)->parse($this->workbook(fn (Spreadsheet $book) => $book->removeSheetByIndex($book->getIndex($book->getSheetByName('Course')))));
            $this->fail('Course sheet must be required.');
        } catch (LessonImportException $exception) {
            $this->assertSame('missing_sheet', $exception->issueCode);
        }
    }

    private function selectableCategory(): Category
    {
        return Category::create(['name' => 'JavaScript', 'slug' => 'javascript', 'status' => true]);
    }

    private function workbook(?callable $mutate = null): UploadedFile
    {
        $book = app(FullCourseImportTemplateService::class)->createWorkbook();
        $book->getSheetByName('Course')->fromArray([['JavaScript v3', 'Ngắn', 'Mô tả', 'Mục tiêu', 'javascript', 'beginner', 'vi', 100000, 90000]], null, 'A2');
        $book->getSheetByName('Sections')->fromArray([['CH01', 'Chương 1', 'Mở đầu']], null, 'A2');
        $book->getSheetByName('Lessons')->fromArray([
            ['CH01', 'VIDEO_01', 'Video shell', 'video', 60, null, null, null, null],
            ['CH01', 'QUIZ_01', 'Quiz', 'quiz', 0, null, null, null, null],
        ], null, 'A2');
        $book->getSheetByName('Quizzes')->fromArray([['QUIZ_01', 'Quiz', 'Mô tả', 70, 30, 2, 'TRUE']], null, 'A2');
        $questions = [];
        $options = [];
        for ($number = 1; $number <= 5; $number++) {
            $code = 'Q_'.$number;
            $questions[] = ['QUIZ_01', $code, 'Câu hỏi '.$number, 'single', 1, null];
            $options[] = [$code, 'A', 'Đáp án A', 'TRUE'];
            $options[] = [$code, 'B', 'Đáp án B', 'FALSE'];
            $options[] = [$code, 'C', 'Đáp án C', 'FALSE'];
        }
        $book->getSheetByName('QuizQuestions')->fromArray($questions, null, 'A2');
        $book->getSheetByName('QuizOptions')->fromArray($options, null, 'A2');
        if ($mutate) {
            $mutate($book);
        }
        $base = tempnam(sys_get_temp_dir(), 'full-course-v3-');
        $path = $base.'.xlsx';
        rename($base, $path);
        $this->files[] = $path;
        (new Xlsx($book))->save($path);
        $book->disconnectWorksheets();

        return new UploadedFile($path, 'full-course-v3.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
    }
}
