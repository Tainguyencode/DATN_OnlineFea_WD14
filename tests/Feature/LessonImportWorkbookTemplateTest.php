<?php

namespace Tests\Feature;

use App\Services\LessonImportTemplateService;
use App\Support\LessonImportWorkbookSchema;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Tests\TestCase;

class LessonImportWorkbookTemplateTest extends TestCase
{
    public function test_v1_template_contract_remains_unchanged(): void
    {
        $workbook = (new LessonImportTemplateService)->createWorkbook();

        $this->assertSame(LessonImportWorkbookSchema::V1_SHEETS, $workbook->getSheetNames());
        $this->assertSame('hidden', $workbook->getSheetByName('_meta')->getSheetState());
        $this->assertSame(1, $workbook->getSheetByName('_meta')->getCell('B1')->getValue());
        $this->assertSame(
            LessonImportWorkbookSchema::V1_LESSON_HEADERS,
            $workbook->getSheetByName('Lessons')->rangeToArray('A1:H1')[0],
        );
        $this->assertSame('A2', $workbook->getSheetByName('Lessons')->getFreezePane());
        $this->assertListValidation(
            $workbook->getSheetByName('Lessons')->getCell('C2')->getDataValidation(),
            '"video,document,quiz,assignment"',
        );

        $workbook->disconnectWorksheets();
    }

    public function test_v2_template_has_exact_schema_sheets_headers_and_validations(): void
    {
        $workbook = (new LessonImportTemplateService)->createWorkbook(LessonImportWorkbookSchema::VERSION_V2);

        $this->assertSame(LessonImportWorkbookSchema::V2_SHEETS, $workbook->getSheetNames());
        $this->assertSame('hidden', $workbook->getSheetByName('_meta')->getSheetState());
        $this->assertSame('template_version', $workbook->getSheetByName('_meta')->getCell('A1')->getValue());
        $this->assertSame(2, $workbook->getSheetByName('_meta')->getCell('B1')->getValue());
        $this->assertSame('schema', $workbook->getSheetByName('_meta')->getCell('A2')->getValue());
        $this->assertSame('lesson_import', $workbook->getSheetByName('_meta')->getCell('B2')->getValue());

        $this->assertSame(
            LessonImportWorkbookSchema::V2_LESSON_HEADERS,
            $workbook->getSheetByName('Lessons')->rangeToArray('A1:H1')[0],
        );
        $this->assertSame(
            LessonImportWorkbookSchema::QUIZ_HEADERS,
            $workbook->getSheetByName('Quizzes')->rangeToArray('A1:G1')[0],
        );
        $this->assertSame(
            LessonImportWorkbookSchema::QUIZ_QUESTION_HEADERS,
            $workbook->getSheetByName('QuizQuestions')->rangeToArray('A1:F1')[0],
        );
        $this->assertSame(
            LessonImportWorkbookSchema::QUIZ_OPTION_HEADERS,
            $workbook->getSheetByName('QuizOptions')->rangeToArray('A1:D1')[0],
        );

        $this->assertListValidation(
            $workbook->getSheetByName('Lessons')->getCell('C2')->getDataValidation(),
            '"video,document,quiz,assignment"',
        );
        $this->assertListValidation(
            $workbook->getSheetByName('QuizQuestions')->getCell('D2')->getDataValidation(),
            '"single,multiple,true_false"',
        );
        $this->assertListValidation(
            $workbook->getSheetByName('Quizzes')->getCell('G2')->getDataValidation(),
            '"TRUE,FALSE"',
        );
        $this->assertListValidation(
            $workbook->getSheetByName('QuizOptions')->getCell('D2')->getDataValidation(),
            '"TRUE,FALSE"',
        );

        $this->assertNoDatabaseOrOrderingColumns($workbook);
        $this->assertNoFormulas($workbook);
        $this->assertNull($workbook->getSheetByName('QuizOptions')->getCell('A2')->getValue());

        $workbook->disconnectWorksheets();
    }

    private function assertListValidation(?DataValidation $validation, string $formula): void
    {
        $this->assertInstanceOf(DataValidation::class, $validation);
        $this->assertSame(DataValidation::TYPE_LIST, $validation->getType());
        $this->assertSame($formula, $validation->getFormula1());
    }

    private function assertNoDatabaseOrOrderingColumns(Spreadsheet $workbook): void
    {
        $forbiddenHeaders = [
            'lesson_id',
            'quiz_id',
            'quiz_version_id',
            'question_id',
            'question_version_id',
            'option_id',
            'sort_order',
        ];

        foreach (['Lessons', 'Quizzes', 'QuizQuestions', 'QuizOptions'] as $sheetName) {
            foreach ($workbook->getSheetByName($sheetName)->rangeToArray('A1:Z1')[0] as $header) {
                $this->assertNotContains($header, $forbiddenHeaders);
            }
        }
    }

    private function assertNoFormulas(Spreadsheet $workbook): void
    {
        foreach ($workbook->getAllSheets() as $sheet) {
            foreach ($sheet->getCoordinates() as $coordinate) {
                $this->assertNotSame(DataType::TYPE_FORMULA, $sheet->getCell($coordinate)->getDataType());
            }
        }
    }
}
