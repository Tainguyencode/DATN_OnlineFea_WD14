<?php

namespace App\Services;

use App\Exceptions\LessonImportException;
use App\Support\FullCourseImportWorkbookSchema;
use App\Support\LessonImportWorkbookSchema;
use DateTimeInterface;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Throwable;
use ZipArchive;

class LessonImportParser
{
    public const MAX_DATA_ROWS = 100;

    /**
     * @return array{
     *     template_version: int,
     *     schema: string,
     *     rows?: array<int, array{row_number: int, values: array<string, mixed>}>,
     *     sheets?: array<string, array<int, array{row_number: int, values: array<string, mixed>}>>
     * }
     */
    public function parse(UploadedFile $file): array
    {
        $path = $file->getRealPath();

        if (! is_string($path) || ! is_readable($path)) {
            throw new LessonImportException('unreadable_file', 'Không thể đọc file Excel đã tải lên.');
        }

        $this->assertRealXlsx($file, $path);

        try {
            if (IOFactory::identify($path) !== 'Xlsx') {
                throw new LessonImportException('invalid_xlsx', 'File tải lên không phải workbook XLSX hợp lệ.');
            }

            $reader = IOFactory::createReader('Xlsx');
            // Formula cells are rejected below. Never ask PhpSpreadsheet to
            // calculate or substitute their cached values.
            $reader->setReadDataOnly(false);
            $spreadsheet = $reader->load($path);
        } catch (LessonImportException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            throw new LessonImportException(
                'invalid_workbook',
                'File Excel bị hỏng hoặc không thể đọc được.',
                $exception,
            );
        }

        try {
            $meta = $spreadsheet->getSheetByName(LessonImportWorkbookSchema::META_SHEET);
            if (! $meta) {
                throw new LessonImportException('missing_meta', 'Template không có sheet _meta bắt buộc.');
            }

            // The dispatch happens before inspecting a data sheet. This prevents
            // a v2 workbook from ever falling through to the v1 parser.
            $metadata = $this->readMetadata($meta);
            $declaredVersion = (string) ($metadata['template_version'] ?? '');

            if ($declaredVersion === (string) LessonImportWorkbookSchema::VERSION_V1) {
                return $this->parseV1($spreadsheet, $metadata);
            }

            if ($declaredVersion === (string) FullCourseImportWorkbookSchema::VERSION
                && ($metadata['schema'] ?? null) === FullCourseImportWorkbookSchema::SCHEMA) {
                return $this->parseV3($spreadsheet, $metadata);
            }

            if (($metadata['schema'] ?? null) !== LessonImportWorkbookSchema::SCHEMA) {
                throw new LessonImportException('wrong_schema', 'Template Excel không đúng schema lesson_import.');
            }

            if ($declaredVersion === (string) LessonImportWorkbookSchema::VERSION_V2) {
                return $this->parseV2($spreadsheet, $metadata);
            }

            throw new LessonImportException('unsupported_version', 'Phiên bản template Excel không được hỗ trợ.');
        } finally {
            $spreadsheet->disconnectWorksheets();
        }
    }

    /**
     * @param  array<string, mixed>  $metadata
     * @return array{template_version: int, schema: string, rows: array<int, array{row_number: int, values: array<string, mixed>}>}
     */
    private function parseV1(Spreadsheet $spreadsheet, array $metadata): array
    {
        $lessons = $spreadsheet->getSheetByName(LessonImportWorkbookSchema::LESSONS_SHEET);
        if (! $lessons) {
            throw new LessonImportException('missing_lessons', 'Template không có sheet Lessons bắt buộc.');
        }

        // This branch intentionally retains the complete v1 contract.
        $this->assertMetadata($metadata);
        $this->assertHeaders($lessons);
        $this->assertNoFormulas($lessons);
        $rows = $this->extractRows($lessons);

        if ($rows === []) {
            throw new LessonImportException('empty_workbook', 'Sheet Lessons chưa có dòng dữ liệu nào để preview.');
        }

        return [
            'template_version' => LessonImportTemplateService::TEMPLATE_VERSION,
            'schema' => LessonImportTemplateService::SCHEMA,
            'rows' => $rows,
        ];
    }

    /**
     * @param  array<string, mixed>  $metadata
     * @return array{template_version: int, schema: string, sheets: array<string, array<int, array{row_number: int, values: array<string, mixed>}>>}
     */
    private function parseV2(Spreadsheet $spreadsheet, array $metadata): array
    {
        $this->assertV2Metadata($metadata);
        $this->assertV2Sheets($spreadsheet);

        $headersBySheet = [
            LessonImportWorkbookSchema::LESSONS_SHEET => LessonImportWorkbookSchema::V2_LESSON_HEADERS,
            LessonImportWorkbookSchema::QUIZZES_SHEET => LessonImportWorkbookSchema::QUIZ_HEADERS,
            LessonImportWorkbookSchema::QUIZ_QUESTIONS_SHEET => LessonImportWorkbookSchema::QUIZ_QUESTION_HEADERS,
            LessonImportWorkbookSchema::QUIZ_OPTIONS_SHEET => LessonImportWorkbookSchema::QUIZ_OPTION_HEADERS,
        ];
        $sheets = [];

        foreach ($headersBySheet as $sheetName => $headers) {
            $sheet = $spreadsheet->getSheetByName($sheetName);
            if (! $sheet) {
                // assertV2Sheets() supplies the normal user-facing rejection.
                // Keep a guard in case the schema is changed in the future.
                throw new LessonImportException('missing_sheet', "Template không có sheet {$sheetName} bắt buộc.");
            }

            $this->assertHeadersForSheet($sheet, $headers, $sheetName);
            $this->assertNoFormulasForSheet($sheet, $headers, $sheetName);
            $sheets[$sheetName] = $this->extractRowsForSheet($sheet, $headers, $sheetName);
        }

        if ($sheets[LessonImportWorkbookSchema::LESSONS_SHEET] === []) {
            throw new LessonImportException('empty_workbook', 'Sheet Lessons chưa có dòng dữ liệu nào để preview.');
        }

        return [
            'template_version' => LessonImportWorkbookSchema::VERSION_V2,
            'schema' => LessonImportWorkbookSchema::SCHEMA,
            'sheets' => $sheets,
        ];
    }

    /**
     * @param  array<string, mixed>  $metadata
     * @return array{template_version: int, schema: string, sheets: array<string, array<int, array{row_number: int, values: array<string, mixed>}>>}
     */
    private function parseV3(Spreadsheet $spreadsheet, array $metadata): array
    {
        if (($metadata['schema'] ?? null) !== FullCourseImportWorkbookSchema::SCHEMA
            || (string) ($metadata['template_version'] ?? '') !== (string) FullCourseImportWorkbookSchema::VERSION) {
            throw new LessonImportException('wrong_schema', 'Template Excel không đúng schema full_course_import.');
        }

        $this->assertV3Sheets($spreadsheet);
        $headersBySheet = [
            FullCourseImportWorkbookSchema::COURSE_SHEET => FullCourseImportWorkbookSchema::COURSE_HEADERS,
            FullCourseImportWorkbookSchema::SECTIONS_SHEET => FullCourseImportWorkbookSchema::SECTION_HEADERS,
            FullCourseImportWorkbookSchema::LESSONS_SHEET => FullCourseImportWorkbookSchema::LESSON_HEADERS,
            FullCourseImportWorkbookSchema::QUIZZES_SHEET => LessonImportWorkbookSchema::QUIZ_HEADERS,
            FullCourseImportWorkbookSchema::QUIZ_QUESTIONS_SHEET => LessonImportWorkbookSchema::QUIZ_QUESTION_HEADERS,
            FullCourseImportWorkbookSchema::QUIZ_OPTIONS_SHEET => LessonImportWorkbookSchema::QUIZ_OPTION_HEADERS,
        ];
        $sheets = [];
        foreach ($headersBySheet as $sheetName => $headers) {
            $sheet = $spreadsheet->getSheetByName($sheetName);
            if (! $sheet) {
                throw new LessonImportException('missing_sheet', "Template không có sheet {$sheetName} bắt buộc.");
            }
            $this->assertHeadersForSheet($sheet, $headers, $sheetName);
            $this->assertNoFormulasForSheet($sheet, $headers, $sheetName);
            $sheets[$sheetName] = $this->extractRowsForSheet($sheet, $headers, $sheetName);
        }

        return [
            'template_version' => FullCourseImportWorkbookSchema::VERSION,
            'schema' => FullCourseImportWorkbookSchema::SCHEMA,
            'sheets' => $sheets,
        ];
    }

    private function assertRealXlsx(UploadedFile $file, string $path): void
    {
        if (strtolower($file->getClientOriginalExtension()) !== 'xlsx') {
            throw new LessonImportException('invalid_extension', 'Chỉ hỗ trợ file có định dạng .xlsx.');
        }

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($path);
        if (! in_array($mime, [
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/zip',
            'application/octet-stream',
        ], true)) {
            throw new LessonImportException('invalid_mime', 'Nội dung file không phải workbook XLSX hợp lệ.');
        }

        $zip = new ZipArchive;
        if ($zip->open($path) !== true) {
            throw new LessonImportException('invalid_zip', 'File XLSX bị hỏng hoặc không đúng định dạng.');
        }

        try {
            foreach (['[Content_Types].xml', '_rels/.rels', 'xl/workbook.xml'] as $requiredEntry) {
                if ($zip->locateName($requiredEntry) === false) {
                    throw new LessonImportException('invalid_xlsx_structure', 'File tải lên không phải workbook XLSX hợp lệ.');
                }
            }

            $contentTypes = (string) $zip->getFromName('[Content_Types].xml');
            if (str_contains($contentTypes, 'macroEnabled')
                || $zip->locateName('xl/vbaProject.bin', ZipArchive::FL_NOCASE) !== false) {
                throw new LessonImportException('macro_workbook', 'Workbook có macro không được hỗ trợ. Chỉ dùng .xlsx không macro.');
            }
        } finally {
            $zip->close();
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function readMetadata(Worksheet $sheet): array
    {
        $metadata = [];
        $metadataRows = [];

        foreach ($sheet->getCellCollection()->getCoordinates() as $coordinate) {
            [$columnLetters, $row] = Coordinate::coordinateFromString($coordinate);

            if ($sheet->getCell($coordinate)->getDataType() === DataType::TYPE_FORMULA) {
                throw new LessonImportException('formula_cell', 'Template chứa công thức Excel không được phép.');
            }

            if (in_array($columnLetters, ['A', 'B'], true)) {
                $metadataRows[(int) $row] = true;
            }
        }

        $metadataRows = array_keys($metadataRows);
        sort($metadataRows, SORT_NUMERIC);

        foreach ($metadataRows as $row) {
            $key = trim((string) $sheet->getCell('A'.$row)->getValue());
            if ($key !== '') {
                $metadata[$key] = $this->primitiveValue($sheet->getCell('B'.$row)->getValue());
            }
        }

        return $metadata;
    }

    /**
     * @param  array<string, mixed>  $metadata
     */
    private function assertMetadata(array $metadata): void
    {
        if (($metadata['schema'] ?? null) !== LessonImportTemplateService::SCHEMA) {
            throw new LessonImportException('wrong_schema', 'Template Excel không đúng schema lesson_import.');
        }

        if ((string) ($metadata['template_version'] ?? '') !== (string) LessonImportTemplateService::TEMPLATE_VERSION) {
            throw new LessonImportException('unsupported_version', 'Phiên bản template Excel không được hỗ trợ.');
        }
    }

    /**
     * @param  array<string, mixed>  $metadata
     */
    private function assertV2Metadata(array $metadata): void
    {
        if (($metadata['schema'] ?? null) !== LessonImportWorkbookSchema::SCHEMA) {
            throw new LessonImportException('wrong_schema', 'Template Excel không đúng schema lesson_import.');
        }

        if ((string) ($metadata['template_version'] ?? '') !== (string) LessonImportWorkbookSchema::VERSION_V2) {
            throw new LessonImportException('unsupported_version', 'Phiên bản template Excel không được hỗ trợ.');
        }
    }

    private function assertV2Sheets(Spreadsheet $spreadsheet): void
    {
        $sheetNames = $spreadsheet->getSheetNames();
        $expected = LessonImportWorkbookSchema::V2_SHEETS;
        $normalizedNames = [];

        foreach ($sheetNames as $sheetName) {
            $normalized = mb_strtolower(trim($sheetName));
            if (isset($normalizedNames[$normalized])) {
                throw new LessonImportException('duplicate_sheet', 'Workbook chứa sheet bị trùng hoặc sai tên.');
            }
            $normalizedNames[$normalized] = $sheetName;
        }

        foreach ($expected as $sheetName) {
            if (! in_array($sheetName, $sheetNames, true)) {
                $code = $sheetName === LessonImportWorkbookSchema::META_SHEET
                    ? 'missing_meta'
                    : ($sheetName === LessonImportWorkbookSchema::LESSONS_SHEET ? 'missing_lessons' : 'missing_sheet');
                throw new LessonImportException($code, "Template không có sheet {$sheetName} bắt buộc.");
            }
        }

        $normalizedExpected = array_map(
            static fn (string $sheetName): string => mb_strtolower($sheetName),
            $expected,
        );

        foreach ($sheetNames as $sheetName) {
            if (in_array($sheetName, $expected, true)) {
                continue;
            }

            // A misspelled/case-shifted required name is rejected even when it
            // happens to be empty; otherwise an empty scratch sheet is harmless.
            if (in_array(mb_strtolower(trim($sheetName)), $normalizedExpected, true)) {
                throw new LessonImportException('invalid_sheet_name', "Sheet {$sheetName} không đúng tên bắt buộc của template v2.");
            }

            $sheet = $spreadsheet->getSheetByName($sheetName);
            if ($sheet && $this->sheetHasData($sheet)) {
                throw new LessonImportException('unknown_sheet', "Workbook chứa sheet {$sheetName} không được hỗ trợ.");
            }
        }
    }

    private function assertV3Sheets(Spreadsheet $spreadsheet): void
    {
        $sheetNames = $spreadsheet->getSheetNames();
        $expected = FullCourseImportWorkbookSchema::SHEETS;
        $normalized = [];
        foreach ($sheetNames as $sheetName) {
            $key = mb_strtolower(trim($sheetName));
            if (isset($normalized[$key])) {
                throw new LessonImportException('duplicate_sheet', 'Workbook chứa sheet bị trùng hoặc sai tên.');
            }
            $normalized[$key] = $sheetName;
        }
        foreach ($expected as $sheetName) {
            if (! in_array($sheetName, $sheetNames, true)) {
                throw new LessonImportException('missing_sheet', "Template không có sheet {$sheetName} bắt buộc.");
            }
        }
        $normalizedExpected = array_map(static fn (string $name): string => mb_strtolower($name), $expected);
        foreach ($sheetNames as $sheetName) {
            if (in_array($sheetName, $expected, true)) {
                continue;
            }
            if (in_array(mb_strtolower(trim($sheetName)), $normalizedExpected, true)) {
                throw new LessonImportException('invalid_sheet_name', "Sheet {$sheetName} không đúng tên bắt buộc của template v3.");
            }
            $sheet = $spreadsheet->getSheetByName($sheetName);
            if ($sheet && $this->sheetHasData($sheet)) {
                throw new LessonImportException('unknown_sheet', "Workbook chứa sheet {$sheetName} không được hỗ trợ.");
            }
        }
    }

    private function sheetHasData(Worksheet $sheet): bool
    {
        foreach ($sheet->getCellCollection()->getCoordinates() as $coordinate) {
            $cell = $sheet->getCell($coordinate);

            if ($cell->getDataType() === DataType::TYPE_FORMULA || ! $this->isBlank($cell->getValue())) {
                return true;
            }
        }

        return false;
    }

    private function assertHeaders(Worksheet $sheet): void
    {
        $headers = [];

        foreach (range(1, count(LessonImportTemplateService::HEADERS)) as $columnIndex) {
            $cell = $sheet->getCell(Coordinate::stringFromColumnIndex($columnIndex).'1');
            if ($cell->getDataType() === DataType::TYPE_FORMULA) {
                throw new LessonImportException('formula_cell', 'Header không được chứa công thức Excel.');
            }
            $headers[] = trim((string) $cell->getValue());
        }

        $highestHeaderColumn = Coordinate::columnIndexFromString($sheet->getHighestDataColumn(1));
        if ($headers !== LessonImportTemplateService::HEADERS
            || $highestHeaderColumn !== count(LessonImportTemplateService::HEADERS)
            || count(array_unique($headers)) !== count($headers)) {
            throw new LessonImportException(
                'invalid_headers',
                'Header sheet Lessons không đúng schema template v1.',
            );
        }
    }

    /**
     * @param  array<int, string>  $expectedHeaders
     */
    private function assertHeadersForSheet(Worksheet $sheet, array $expectedHeaders, string $sheetName): void
    {
        $headers = [];

        foreach (range(1, count($expectedHeaders)) as $columnIndex) {
            $cell = $sheet->getCell(Coordinate::stringFromColumnIndex($columnIndex).'1');
            if ($cell->getDataType() === DataType::TYPE_FORMULA) {
                throw new LessonImportException('formula_cell', "Header sheet {$sheetName} không được chứa công thức Excel.");
            }
            $headers[] = trim((string) $cell->getValue());
        }

        $highestHeaderColumn = Coordinate::columnIndexFromString($sheet->getHighestDataColumn(1));
        if ($headers !== $expectedHeaders
            || $highestHeaderColumn !== count($expectedHeaders)
            || count(array_unique($headers)) !== count($headers)) {
            throw new LessonImportException(
                'invalid_headers',
                "Header sheet {$sheetName} không đúng schema template v2.",
            );
        }
    }

    private function assertNoFormulas(Worksheet $sheet): void
    {
        foreach ($sheet->getCellCollection()->getCoordinates() as $coordinate) {
            [$columnLetters, $row] = Coordinate::coordinateFromString($coordinate);
            $row = (int) $row;

            if ($sheet->getCell($coordinate)->getDataType() === DataType::TYPE_FORMULA) {
                throw new LessonImportException(
                    'formula_cell',
                    "Dòng {$row} chứa công thức Excel không được phép.",
                );
            }

            if ($row >= 2
                && Coordinate::columnIndexFromString($columnLetters) > count(LessonImportTemplateService::HEADERS)
                && ! $this->isBlank($sheet->getCell($coordinate)->getValue())) {
                throw new LessonImportException(
                    'unknown_data_column',
                    'Sheet Lessons chứa dữ liệu ngoài các cột được hỗ trợ bởi template v1.',
                );
            }
        }
    }

    /**
     * @param  array<int, string>  $headers
     */
    private function assertNoFormulasForSheet(Worksheet $sheet, array $headers, string $sheetName): void
    {
        foreach ($sheet->getCellCollection()->getCoordinates() as $coordinate) {
            [$columnLetters, $row] = Coordinate::coordinateFromString($coordinate);
            $row = (int) $row;
            $cell = $sheet->getCell($coordinate);

            if ($cell->getDataType() === DataType::TYPE_FORMULA) {
                throw new LessonImportException(
                    'formula_cell',
                    "Sheet {$sheetName}, dòng {$row} chứa công thức Excel không được phép.",
                );
            }

            if ($row >= 2
                && Coordinate::columnIndexFromString($columnLetters) > count($headers)
                && ! $this->isBlank($cell->getValue())) {
                throw new LessonImportException(
                    'unknown_data_column',
                    "Sheet {$sheetName} chứa dữ liệu ngoài các cột được hỗ trợ bởi template v2.",
                );
            }
        }
    }

    /**
     * @return array<int, array{row_number: int, values: array<string, mixed>}>
     */
    private function extractRows(Worksheet $sheet): array
    {
        $rows = [];
        $dataRowNumbers = [];

        foreach ($sheet->getCellCollection()->getCoordinates() as $coordinate) {
            [$columnLetters, $row] = Coordinate::coordinateFromString($coordinate);
            $row = (int) $row;
            $columnIndex = Coordinate::columnIndexFromString($columnLetters);

            if ($row >= 2
                && $columnIndex <= count(LessonImportTemplateService::HEADERS)
                && ! $this->isBlank($sheet->getCell($coordinate)->getValue())) {
                $dataRowNumbers[$row] = true;
            }
        }

        $dataRowNumbers = array_keys($dataRowNumbers);
        sort($dataRowNumbers, SORT_NUMERIC);

        foreach ($dataRowNumbers as $row) {
            $values = [];

            foreach (LessonImportTemplateService::HEADERS as $index => $header) {
                $coordinate = Coordinate::stringFromColumnIndex($index + 1).$row;
                $values[$header] = $this->primitiveValue($sheet->getCell($coordinate)->getValue());
            }

            $rows[] = [
                'row_number' => $row,
                'values' => $values,
            ];

            if (count($rows) > self::MAX_DATA_ROWS) {
                throw new LessonImportException(
                    'too_many_rows',
                    'Mỗi lần preview chỉ hỗ trợ tối đa 100 dòng dữ liệu.',
                );
            }
        }

        return $rows;
    }

    /**
     * @param  array<int, string>  $headers
     * @return array<int, array{row_number: int, values: array<string, mixed>}>
     */
    private function extractRowsForSheet(Worksheet $sheet, array $headers, string $sheetName): array
    {
        $rows = [];
        $dataRowNumbers = [];

        foreach ($sheet->getCellCollection()->getCoordinates() as $coordinate) {
            [$columnLetters, $row] = Coordinate::coordinateFromString($coordinate);
            $row = (int) $row;
            $columnIndex = Coordinate::columnIndexFromString($columnLetters);

            if ($row >= 2
                && $columnIndex <= count($headers)
                && ! $this->isBlank($sheet->getCell($coordinate)->getValue())) {
                $dataRowNumbers[$row] = true;
            }
        }

        $dataRowNumbers = array_keys($dataRowNumbers);
        sort($dataRowNumbers, SORT_NUMERIC);

        foreach ($dataRowNumbers as $row) {
            $values = [];

            foreach ($headers as $index => $header) {
                $coordinate = Coordinate::stringFromColumnIndex($index + 1).$row;
                $values[$header] = $this->primitiveValue($sheet->getCell($coordinate)->getValue());
            }

            $rows[] = [
                'row_number' => $row,
                'values' => $values,
            ];

            if (count($rows) > self::MAX_DATA_ROWS) {
                throw new LessonImportException(
                    'too_many_rows',
                    "Sheet {$sheetName} chỉ hỗ trợ tối đa 100 dòng dữ liệu mỗi lần preview.",
                );
            }
        }

        return $rows;
    }

    private function primitiveValue(mixed $value): mixed
    {
        if ($value instanceof RichText) {
            return $value->getPlainText();
        }

        if ($value instanceof DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        return is_scalar($value) || $value === null ? $value : (string) $value;
    }

    private function isBlank(mixed $value): bool
    {
        return $value === null || (is_string($value) && trim($value) === '');
    }
}
