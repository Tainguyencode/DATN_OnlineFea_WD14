<?php

namespace App\Services;

use App\Support\LessonImportWorkbookSchema;
use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LessonImportTemplateService
{
    public const TEMPLATE_VERSION = LessonImportWorkbookSchema::VERSION_V1;

    public const SCHEMA = LessonImportWorkbookSchema::SCHEMA;

    public const HEADERS = LessonImportWorkbookSchema::V1_LESSON_HEADERS;

    public const FILENAME = 'mau-import-bai-hoc-v1.xlsx';

    public const V2_FILENAME = 'mau-import-bai-hoc-v2.xlsx';

    public function createWorkbook(int $version = self::TEMPLATE_VERSION): Spreadsheet
    {
        if ($version === LessonImportWorkbookSchema::VERSION_V2) {
            return $this->createV2Workbook();
        }

        if ($version !== LessonImportWorkbookSchema::VERSION_V1) {
            throw new InvalidArgumentException('Unsupported lesson import workbook version.');
        }

        $spreadsheet = new Spreadsheet;
        $spreadsheet->getProperties()
            ->setCreator(config('app.name'))
            ->setTitle('Mẫu import bài học')
            ->setDescription('Lesson import schema v1');

        $meta = $spreadsheet->getActiveSheet();
        $meta->setTitle(LessonImportWorkbookSchema::META_SHEET);
        $meta->fromArray([
            ['template_version', self::TEMPLATE_VERSION],
            ['schema', self::SCHEMA],
        ], null, 'A1');
        $meta->setSheetState($meta::SHEETSTATE_HIDDEN);

        $lessons = $spreadsheet->createSheet();
        $lessons->setTitle(LessonImportWorkbookSchema::LESSONS_SHEET);
        $lessons->fromArray(self::HEADERS, null, 'A1');
        $lessons->freezePane('A2');
        $lessons->setAutoFilter('A1:H1');
        $lessons->setShowGridlines(false);
        $lessons->getRowDimension(1)->setRowHeight(26);

        $lessons->getStyle('A1:H1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F4E78'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        foreach ([
            'A' => 18,
            'B' => 38,
            'C' => 16,
            'D' => 20,
            'E' => 55,
            'F' => 24,
            'G' => 26,
            'H' => 30,
        ] as $column => $width) {
            $lessons->getColumnDimension($column)->setWidth($width);
        }

        $lessons->getStyle('A2:H101')->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
        $lessons->getStyle('B2:B101')->getAlignment()->setWrapText(true);
        $lessons->getStyle('E2:E101')->getAlignment()->setWrapText(true);
        $lessons->getStyle('D2:D101')->getNumberFormat()->setFormatCode('0');
        $lessons->getStyle('F2:H101')->getNumberFormat()->setFormatCode('0');

        $validation = new DataValidation;
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_STOP);
        $validation->setAllowBlank(false);
        $validation->setShowDropDown(true);
        $validation->setShowErrorMessage(true);
        $validation->setErrorTitle('Loại bài học không hợp lệ');
        $validation->setError('Chọn video, document, quiz hoặc assignment.');
        $validation->setFormula1('"video,document,quiz,assignment"');

        for ($row = 2; $row <= 101; $row++) {
            $lessons->getCell('C'.$row)->setDataValidation(clone $validation);
        }

        $spreadsheet->setActiveSheetIndexByName('Lessons');

        return $spreadsheet;
    }

    public function stream(int $version = self::TEMPLATE_VERSION): void
    {
        $spreadsheet = $this->createWorkbook($version);

        try {
            (new Xlsx($spreadsheet))->save('php://output');
        } finally {
            $spreadsheet->disconnectWorksheets();
        }
    }

    public function filenameForVersion(int $version = self::TEMPLATE_VERSION): string
    {
        return match ($version) {
            LessonImportWorkbookSchema::VERSION_V1 => self::FILENAME,
            LessonImportWorkbookSchema::VERSION_V2 => self::V2_FILENAME,
            default => throw new InvalidArgumentException('Unsupported lesson import workbook version.'),
        };
    }

    private function createV2Workbook(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet;
        $spreadsheet->getProperties()
            ->setCreator(config('app.name'))
            ->setTitle('Mẫu import bài học và quiz')
            ->setDescription('Lesson import schema v2');

        $meta = $spreadsheet->getActiveSheet();
        $meta->setTitle(LessonImportWorkbookSchema::META_SHEET);
        $meta->fromArray([
            ['template_version', LessonImportWorkbookSchema::VERSION_V2],
            ['schema', LessonImportWorkbookSchema::SCHEMA],
        ], null, 'A1');
        $meta->setSheetState($meta::SHEETSTATE_HIDDEN);

        $lessons = $spreadsheet->createSheet();
        $lessons->setTitle(LessonImportWorkbookSchema::LESSONS_SHEET);
        $this->configureTableSheet(
            $lessons,
            LessonImportWorkbookSchema::V2_LESSON_HEADERS,
            [
                'A' => 18,
                'B' => 38,
                'C' => 16,
                'D' => 20,
                'E' => 55,
                'F' => 24,
                'G' => 26,
                'H' => 30,
            ],
            ['B', 'E'],
            ['D', 'F', 'G', 'H'],
        );
        $this->addListValidation(
            $lessons,
            'C',
            ['video', 'document', 'quiz', 'assignment'],
            'Loại bài học không hợp lệ',
            'Chọn video, document, quiz hoặc assignment.',
        );

        $quizzes = $spreadsheet->createSheet();
        $quizzes->setTitle(LessonImportWorkbookSchema::QUIZZES_SHEET);
        $this->configureTableSheet(
            $quizzes,
            LessonImportWorkbookSchema::QUIZ_HEADERS,
            [
                'A' => 18,
                'B' => 38,
                'C' => 55,
                'D' => 14,
                'E' => 22,
                'F' => 16,
                'G' => 14,
            ],
            ['B', 'C'],
            ['D', 'E', 'F'],
        );
        $this->addListValidation(
            $quizzes,
            'G',
            LessonImportWorkbookSchema::BOOLEAN_TEMPLATE_VALUES,
            'Giá trị is_active không hợp lệ',
            'Chọn TRUE hoặc FALSE.',
        );
        $quizzes->getComment('A1')->getText()->createTextRun(
            'lesson_code phải khớp một dòng Lessons có type=quiz.',
        );

        $questions = $spreadsheet->createSheet();
        $questions->setTitle(LessonImportWorkbookSchema::QUIZ_QUESTIONS_SHEET);
        $this->configureTableSheet(
            $questions,
            LessonImportWorkbookSchema::QUIZ_QUESTION_HEADERS,
            [
                'A' => 18,
                'B' => 22,
                'C' => 60,
                'D' => 18,
                'E' => 12,
                'F' => 60,
            ],
            ['C', 'F'],
            ['E'],
        );
        $this->addListValidation(
            $questions,
            'D',
            LessonImportWorkbookSchema::QUESTION_TYPES,
            'Loại câu hỏi không hợp lệ',
            'Chọn single, multiple hoặc true_false.',
        );
        $questions->getComment('D1')->getText()->createTextRun(
            'Giá trị hợp lệ: single, multiple, true_false.',
        );
        $questions->getComment('B1')->getText()->createTextRun(
            'question_code duy nhất, tối đa 64 ký tự; nên dùng A-Z, 0-9, _ hoặc -.',
        );

        $options = $spreadsheet->createSheet();
        $options->setTitle(LessonImportWorkbookSchema::QUIZ_OPTIONS_SHEET);
        $this->configureTableSheet(
            $options,
            LessonImportWorkbookSchema::QUIZ_OPTION_HEADERS,
            [
                'A' => 22,
                'B' => 22,
                'C' => 55,
                'D' => 14,
            ],
            ['C'],
            [],
        );
        $this->addListValidation(
            $options,
            'D',
            LessonImportWorkbookSchema::BOOLEAN_TEMPLATE_VALUES,
            'Giá trị is_correct không hợp lệ',
            'Chọn TRUE hoặc FALSE.',
        );
        $options->getComment('B1')->getText()->createTextRun(
            'Với true_false, dùng đúng hai option_code: TRUE và FALSE.',
        );

        $spreadsheet->setActiveSheetIndexByName(LessonImportWorkbookSchema::LESSONS_SHEET);

        return $spreadsheet;
    }

    /**
     * @param  array<int, string>  $headers
     * @param  array<string, int>  $widths
     * @param  array<int, string>  $wrapColumns
     * @param  array<int, string>  $numberColumns
     */
    private function configureTableSheet(
        Worksheet $sheet,
        array $headers,
        array $widths,
        array $wrapColumns,
        array $numberColumns,
    ): void {
        $lastColumn = Coordinate::stringFromColumnIndex(count($headers));
        $sheet->fromArray($headers, null, 'A1');
        $sheet->freezePane('A2');
        $sheet->setAutoFilter('A1:'.$lastColumn.'1');
        $sheet->setShowGridlines(false);
        $sheet->getRowDimension(1)->setRowHeight(26);
        $sheet->getStyle('A1:'.$lastColumn.'1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F4E78'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        foreach ($widths as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }

        $sheet->getStyle('A2:'.$lastColumn.'101')->getAlignment()->setVertical(Alignment::VERTICAL_TOP);

        foreach ($wrapColumns as $column) {
            $sheet->getStyle($column.'2:'.$column.'101')->getAlignment()->setWrapText(true);
        }

        foreach ($numberColumns as $column) {
            $sheet->getStyle($column.'2:'.$column.'101')->getNumberFormat()->setFormatCode('0');
        }
    }

    /**
     * @param  array<int, string>  $values
     */
    private function addListValidation(
        Worksheet $sheet,
        string $column,
        array $values,
        string $errorTitle,
        string $error,
    ): void {
        $validation = new DataValidation;
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_STOP);
        $validation->setAllowBlank(false);
        $validation->setShowDropDown(true);
        $validation->setShowErrorMessage(true);
        $validation->setErrorTitle($errorTitle);
        $validation->setError($error);
        $validation->setFormula1('"'.implode(',', $values).'"');

        for ($row = 2; $row <= 101; $row++) {
            $sheet->getCell($column.$row)->setDataValidation(clone $validation);
        }
    }
}
