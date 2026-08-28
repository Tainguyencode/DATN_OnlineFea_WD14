<?php

namespace App\Services;

use App\Support\FullCourseImportWorkbookSchema as Schema;
use App\Support\LessonImportWorkbookSchema;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class FullCourseImportTemplateService
{
    public const FILENAME = 'mau-import-toan-bo-khoa-hoc-v3.xlsx';

    public function createWorkbook(): Spreadsheet
    {
        $book = new Spreadsheet;
        $book->getProperties()->setCreator(config('app.name'))->setTitle('Mẫu import toàn bộ khóa học v3');
        $meta = $book->getActiveSheet();
        $meta->setTitle(Schema::META_SHEET);
        $meta->fromArray([['template_version', Schema::VERSION], ['schema', Schema::SCHEMA]], null, 'A1');
        $meta->setSheetState($meta::SHEETSTATE_HIDDEN);

        $course = $this->sheet($book, Schema::COURSE_SHEET, Schema::COURSE_HEADERS, [
            'Khóa học JavaScript từ Zero đến Thực chiến', 'Khóa học JavaScript nền tảng', 'Mô tả khóa học', 'Nắm vững JavaScript', 'javascript', 'beginner', 'vi', 499000, 399000,
        ]);
        $this->wrap($course, ['A', 'B', 'C', 'D']);

        $sections = $this->sheet($book, Schema::SECTIONS_SHEET, Schema::SECTION_HEADERS, ['CH01', 'JavaScript cơ bản', 'Kiến thức nhập môn']);
        $this->wrap($sections, ['B', 'C']);

        $lessons = $this->sheet($book, Schema::LESSONS_SHEET, Schema::LESSON_HEADERS, ['CH01', 'JS_01', 'Biến trong JavaScript', 'document', 600, 'Nội dung tài liệu', null, null, null]);
        $this->list($lessons, 'D', ['video', 'document', 'quiz', 'assignment']);
        $this->wrap($lessons, ['C', 'F']);

        $quizzes = $this->sheet($book, Schema::QUIZZES_SHEET, LessonImportWorkbookSchema::QUIZ_HEADERS, ['QUIZ_01', 'Quiz JavaScript', 'Kiểm tra kiến thức', 70, 30, 3, 'TRUE']);
        $this->list($quizzes, 'G', LessonImportWorkbookSchema::BOOLEAN_TEMPLATE_VALUES);
        $this->wrap($quizzes, ['B', 'C']);

        $questions = $this->sheet($book, Schema::QUIZ_QUESTIONS_SHEET, LessonImportWorkbookSchema::QUIZ_QUESTION_HEADERS, ['QUIZ_01', 'Q_01', 'JavaScript là gì?', 'single', 1, 'Đọc lại bài học nếu cần.']);
        $this->list($questions, 'D', LessonImportWorkbookSchema::QUESTION_TYPES);
        $this->wrap($questions, ['C', 'F']);

        $options = $this->sheet($book, Schema::QUIZ_OPTIONS_SHEET, LessonImportWorkbookSchema::QUIZ_OPTION_HEADERS, ['Q_01', 'A', 'Ngôn ngữ lập trình', 'TRUE']);
        $this->list($options, 'D', LessonImportWorkbookSchema::BOOLEAN_TEMPLATE_VALUES);
        $this->wrap($options, ['C']);
        $book->setActiveSheetIndexByName(Schema::COURSE_SHEET);

        return $book;
    }

    public function stream(): void
    {
        $book = $this->createWorkbook();
        try {
            (new Xlsx($book))->save('php://output');
        } finally {
            $book->disconnectWorksheets();
        }
    }

    /** @param array<int, string> $headers @param array<int, mixed> $sample */
    private function sheet(Spreadsheet $book, string $name, array $headers, array $sample): Worksheet
    {
        $sheet = $book->createSheet();
        $sheet->setTitle($name);
        $last = Coordinate::stringFromColumnIndex(count($headers));
        $sheet->fromArray($headers, null, 'A1');
        $sheet->fromArray($sample, null, 'A2');
        $sheet->freezePane('A2');
        $sheet->setAutoFilter("A1:{$last}1");
        $sheet->setShowGridlines(false);
        $sheet->getStyle("A1:{$last}1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F4E78']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle("A2:{$last}101")->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
        foreach (range(1, count($headers)) as $index) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($index))->setWidth(22);
        }

        return $sheet;
    }

    /** @param array<int, string> $columns */
    private function wrap(Worksheet $sheet, array $columns): void
    {
        foreach ($columns as $column) {
            $sheet->getStyle("{$column}2:{$column}101")->getAlignment()->setWrapText(true);
        }
    }

    /** @param array<int, string> $values */
    private function list(Worksheet $sheet, string $column, array $values): void
    {
        $validation = new DataValidation;
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_STOP);
        $validation->setAllowBlank(false);
        $validation->setShowErrorMessage(true);
        $validation->setFormula1('"'.implode(',', $values).'"');
        for ($row = 2; $row <= 101; $row++) {
            $sheet->getCell($column.$row)->setDataValidation(clone $validation);
        }
    }
}
