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
use App\Support\LessonImportWorkbookSchema;
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

    public function test_realistic_v3_workbook_with_sixty_questions_and_204_options_previews_successfully(): void
    {
        $this->selectableCategory();
        $user = User::factory()->create(['role' => 'instructor']);

        $result = app(FullCourseImportPreviewService::class)->preview($this->realisticWorkbook(), $user);

        $this->assertSame(0, $result['error_count']);
        $this->assertSame(6, $result['summary']['sections']);
        $this->assertSame(30, $result['summary']['lessons']);
        $this->assertSame(6, $result['summary']['quiz']);
        $this->assertSame(60, $result['summary']['questions']);
        $this->assertSame(204, $result['summary']['options']);
        $this->assertSame(204, count($result['canonical_payload']['options']));
    }

    public function test_v3_parser_uses_the_centralized_per_sheet_boundaries(): void
    {
        $this->assertSame(2000, $this->extractV3Rows(Schema::QUIZ_QUESTIONS_SHEET, 2000));
        $this->assertSame(10000, $this->extractV3Rows(Schema::QUIZ_OPTIONS_SHEET, 10000));

        foreach ([
            [Schema::SECTIONS_SHEET, 51, 50],
            [Schema::LESSONS_SHEET, 501, 500],
            [Schema::QUIZZES_SHEET, 101, 100],
            [Schema::QUIZ_QUESTIONS_SHEET, 2001, 2000],
            [Schema::QUIZ_OPTIONS_SHEET, 10001, 10000],
        ] as [$sheet, $count, $limit]) {
            try {
                $this->extractV3Rows($sheet, $count);
                $this->fail("{$sheet} must reject {$count} rows.");
            } catch (LessonImportException $exception) {
                $this->assertSame('too_many_rows', $exception->issueCode);
                $this->assertStringContainsString((string) $limit, $exception->userMessage);
            }
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

    private function realisticWorkbook(): UploadedFile
    {
        $book = app(FullCourseImportTemplateService::class)->createWorkbook();
        $book->getSheetByName('Course')->fromArray([['Import v3 quy mô thực tế', 'Ngắn', 'Mô tả', 'Mục tiêu', 'javascript', 'beginner', 'vi', 100000, 90000]], null, 'A2');
        $sections = [];
        $lessons = [];
        $quizzes = [];
        $questions = [];
        $options = [];

        for ($quizNumber = 1; $quizNumber <= 6; $quizNumber++) {
            $sectionCode = 'CH'.str_pad((string) $quizNumber, 2, '0', STR_PAD_LEFT);
            $quizLessonCode = 'QUIZ_'.str_pad((string) $quizNumber, 2, '0', STR_PAD_LEFT);
            $sections[] = [$sectionCode, 'Chương '.$quizNumber, 'Nội dung chương '.$quizNumber];
            $lessons[] = [$sectionCode, 'VIDEO_'.$quizNumber, 'Video '.$quizNumber, 'video', 60, null, null, null, null];
            $lessons[] = [$sectionCode, 'DOC_'.$quizNumber, 'Tài liệu '.$quizNumber, 'document', 0, 'Nội dung', null, null, null];
            $lessons[] = [$sectionCode, 'ASM_'.$quizNumber, 'Bài tập '.$quizNumber, 'assignment', 0, 'Yêu cầu', 7, 100, 70];
            $lessons[] = [$sectionCode, $quizLessonCode, 'Quiz '.$quizNumber, 'quiz', 0, null, null, null, null];
            $lessons[] = [$sectionCode, 'DOC_EXTRA_'.$quizNumber, 'Tài liệu thêm '.$quizNumber, 'document', 0, 'Nội dung thêm', null, null, null];
            $quizzes[] = [$quizLessonCode, 'Quiz '.$quizNumber, 'Mô tả quiz', 70, 30, 2, 'TRUE'];

            $nonTrueFalse = 0;
            for ($questionNumber = 1; $questionNumber <= 10; $questionNumber++) {
                $questionCode = 'Q_'.$quizNumber.'_'.$questionNumber;
                $type = in_array($questionNumber, [3, 8], true)
                    ? 'true_false'
                    : ($questionNumber % 2 === 0 ? 'multiple' : 'single');
                $questions[] = [$quizLessonCode, $questionCode, 'Câu hỏi '.$quizNumber.'.'.$questionNumber, $type, 1, null];

                if ($type === 'true_false') {
                    $options[] = [$questionCode, 'TRUE', 'Đúng', 'TRUE'];
                    $options[] = [$questionCode, 'FALSE', 'Sai', 'FALSE'];

                    continue;
                }

                $nonTrueFalse++;
                $optionCount = $nonTrueFalse <= 6 ? 4 : 3;
                for ($optionNumber = 1; $optionNumber <= $optionCount; $optionNumber++) {
                    $optionCode = chr(64 + $optionNumber);
                    $options[] = [$questionCode, $optionCode, 'Đáp án '.$optionCode, $optionNumber === 1 || ($type === 'multiple' && $optionNumber === 2) ? 'TRUE' : 'FALSE'];
                }
            }
        }

        $book->getSheetByName('Sections')->fromArray($sections, null, 'A2');
        $book->getSheetByName('Lessons')->fromArray($lessons, null, 'A2');
        $book->getSheetByName('Quizzes')->fromArray($quizzes, null, 'A2');
        $book->getSheetByName('QuizQuestions')->fromArray($questions, null, 'A2');
        $book->getSheetByName('QuizOptions')->fromArray($options, null, 'A2');

        $base = tempnam(sys_get_temp_dir(), 'full-course-v3-realistic-');
        $path = $base.'.xlsx';
        rename($base, $path);
        $this->files[] = $path;
        (new Xlsx($book))->save($path);
        $book->disconnectWorksheets();

        return new UploadedFile($path, 'full-course-v3-realistic.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
    }

    private function extractV3Rows(string $sheetName, int $count): int
    {
        $headers = match ($sheetName) {
            Schema::SECTIONS_SHEET => Schema::SECTION_HEADERS,
            Schema::LESSONS_SHEET => Schema::LESSON_HEADERS,
            Schema::QUIZZES_SHEET => LessonImportWorkbookSchema::QUIZ_HEADERS,
            Schema::QUIZ_QUESTIONS_SHEET => LessonImportWorkbookSchema::QUIZ_QUESTION_HEADERS,
            Schema::QUIZ_OPTIONS_SHEET => LessonImportWorkbookSchema::QUIZ_OPTION_HEADERS,
        };
        $book = new Spreadsheet;
        $sheet = $book->getActiveSheet();
        $sheet->setTitle($sheetName);
        $sheet->fromArray([$headers], null, 'A1');
        for ($row = 2; $row <= $count + 1; $row++) {
            $sheet->setCellValue('A'.$row, 'row-'.$row);
        }

        try {
            $method = new \ReflectionMethod(LessonImportParser::class, 'extractRowsForSheet');

            return count($method->invoke(app(LessonImportParser::class), $sheet, $headers, $sheetName, Schema::dataRowLimit($sheetName)));
        } finally {
            $book->disconnectWorksheets();
        }
    }
}
