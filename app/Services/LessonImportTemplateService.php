<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LessonImportTemplateService
{
    public const TEMPLATE_VERSION = 1;

    public const SCHEMA = 'lesson_import';

    public const HEADERS = [
        'lesson_code',
        'title',
        'type',
        'duration_seconds',
        'content',
        'assignment_due_days',
        'assignment_max_score',
        'assignment_passing_score',
    ];

    public const FILENAME = 'mau-import-bai-hoc-v1.xlsx';

    public function createWorkbook(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet;
        $spreadsheet->getProperties()
            ->setCreator(config('app.name'))
            ->setTitle('Mẫu import bài học')
            ->setDescription('Lesson import schema v1');

        $meta = $spreadsheet->getActiveSheet();
        $meta->setTitle('_meta');
        $meta->fromArray([
            ['template_version', self::TEMPLATE_VERSION],
            ['schema', self::SCHEMA],
        ], null, 'A1');
        $meta->setSheetState($meta::SHEETSTATE_HIDDEN);

        $lessons = $spreadsheet->createSheet();
        $lessons->setTitle('Lessons');
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

    public function stream(): void
    {
        $spreadsheet = $this->createWorkbook();

        try {
            (new Xlsx($spreadsheet))->save('php://output');
        } finally {
            $spreadsheet->disconnectWorksheets();
        }
    }
}
