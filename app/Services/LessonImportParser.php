<?php

namespace App\Services;

use App\Exceptions\LessonImportException;
use DateTimeInterface;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Throwable;
use ZipArchive;

class LessonImportParser
{
    public const MAX_DATA_ROWS = 100;

    /**
     * @return array{template_version: int, schema: string, rows: array<int, array{row_number: int, values: array<string, mixed>}>}
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
            $meta = $spreadsheet->getSheetByName('_meta');
            if (! $meta) {
                throw new LessonImportException('missing_meta', 'Template không có sheet _meta bắt buộc.');
            }

            $lessons = $spreadsheet->getSheetByName('Lessons');
            if (! $lessons) {
                throw new LessonImportException('missing_lessons', 'Template không có sheet Lessons bắt buộc.');
            }

            $metadata = $this->readMetadata($meta);
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
        } finally {
            $spreadsheet->disconnectWorksheets();
        }
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
                $value = $this->primitiveValue($sheet->getCell($coordinate)->getValue());
                $values[$header] = $value;
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
