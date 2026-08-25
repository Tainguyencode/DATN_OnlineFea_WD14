<?php

namespace App\Services;

use App\Models\CourseSection;
use App\Models\Lesson;
use Illuminate\Support\Str;

class LessonImportValidator
{
    /**
     * @param  array<int, array{row_number: int, values: array<string, mixed>}>  $rows
     * @return array{
     *     canonical_rows: array<int, array<string, mixed>>,
     *     reports: array<int, array{row_number: int, status: string, errors: array<int, string>, warnings: array<int, string>}>,
     *     valid_count: int,
     *     warning_count: int,
     *     error_count: int
     * }
     */
    public function validate(array $rows, CourseSection $section): array
    {
        $canonicalRows = [];
        $reports = [];
        $codeIndexes = [];
        $titleIndexes = [];
        $existingTitles = $section->lessons()
            ->pluck('title')
            ->map(fn (string $title): string => Str::lower(trim($title)))
            ->all();

        foreach ($rows as $relativeOrder => $row) {
            [$canonical, $errors, $warnings] = $this->validateRow(
                $row['row_number'],
                $relativeOrder,
                $row['values'],
            );

            $canonicalRows[] = $canonical;
            $reports[] = [
                'row_number' => $row['row_number'],
                'status' => 'valid',
                'errors' => $errors,
                'warnings' => $warnings,
            ];

            if ($canonical['lesson_code'] !== '') {
                $codeIndexes[Str::lower($canonical['lesson_code'])][] = $relativeOrder;
            }

            if ($canonical['title'] !== '') {
                $normalizedTitle = Str::lower($canonical['title']);
                $titleIndexes[$normalizedTitle][] = $relativeOrder;

                if (in_array($normalizedTitle, $existingTitles, true)) {
                    $reports[$relativeOrder]['warnings'][] = "Dòng {$row['row_number']} — “title”: tiêu đề đã tồn tại trong chương này.";
                }
            }
        }

        foreach ($codeIndexes as $indexes) {
            if (count($indexes) < 2) {
                continue;
            }

            foreach ($indexes as $index) {
                $rowNumber = $canonicalRows[$index]['row_number'];
                $reports[$index]['errors'][] = "Dòng {$rowNumber} — “lesson_code”: mã bị trùng trong workbook (không phân biệt hoa thường).";
            }
        }

        foreach ($titleIndexes as $indexes) {
            if (count($indexes) < 2) {
                continue;
            }

            foreach ($indexes as $index) {
                $rowNumber = $canonicalRows[$index]['row_number'];
                $reports[$index]['warnings'][] = "Dòng {$rowNumber} — “title”: tiêu đề bị trùng trong workbook.";
            }
        }

        $counts = [
            'valid_count' => 0,
            'warning_count' => 0,
            'error_count' => 0,
        ];

        foreach ($reports as $index => $report) {
            $reports[$index]['errors'] = array_values(array_unique($report['errors']));
            $reports[$index]['warnings'] = array_values(array_unique($report['warnings']));
            $status = $reports[$index]['errors'] !== []
                ? 'error'
                : ($reports[$index]['warnings'] !== [] ? 'warning' : 'valid');
            $reports[$index]['status'] = $status;
            $counts[$status.'_count']++;
        }

        return [
            'canonical_rows' => $canonicalRows,
            'reports' => $reports,
            ...$counts,
        ];
    }

    /**
     * @param  array<string, mixed>  $values
     * @return array{0: array<string, mixed>, 1: array<int, string>, 2: array<int, string>}
     */
    private function validateRow(int $rowNumber, int $relativeOrder, array $values): array
    {
        $errors = [];
        $warnings = [];

        $lessonCode = $this->stringValue($values['lesson_code'] ?? null);
        $title = $this->stringValue($values['title'] ?? null);
        $type = Str::lower($this->stringValue($values['type'] ?? null));
        $content = $this->nullableStringValue($values['content'] ?? null);

        foreach (['lesson_code', 'title', 'type', 'content'] as $field) {
            $rawValue = $values[$field] ?? null;
            if (! $this->isBlank($rawValue) && ! is_string($rawValue)) {
                $errors[] = "Dòng {$rowNumber} — “{$field}”: phải là dữ liệu văn bản.";
            }
        }

        if ($lessonCode === '') {
            $errors[] = "Dòng {$rowNumber} — “lesson_code”: bắt buộc nhập mã bài học.";
        } else {
            $lessonCode = Str::upper($lessonCode);
            if (mb_strlen($lessonCode) > 64) {
                $errors[] = "Dòng {$rowNumber} — “lesson_code”: tối đa 64 ký tự.";
            } elseif (! preg_match('/^[A-Z0-9_-]+$/', $lessonCode)) {
                $errors[] = "Dòng {$rowNumber} — “lesson_code”: chỉ cho phép A-Z, 0-9, dấu gạch dưới và gạch ngang.";
            }
        }

        if ($title === '') {
            $errors[] = "Dòng {$rowNumber} — “title”: bắt buộc nhập tiêu đề bài học.";
        } elseif (mb_strlen($title) > 255) {
            $errors[] = "Dòng {$rowNumber} — “title”: tối đa 255 ký tự.";
        }

        if (! in_array($type, Lesson::TYPES, true)) {
            $errors[] = "Dòng {$rowNumber} — “type”: giá trị “{$type}” không hợp lệ. Cho phép: video, document, quiz, assignment.";
        }

        $duration = $this->integerValue(
            $values['duration_seconds'] ?? null,
            'duration_seconds',
            $rowNumber,
            0,
            0,
            999999,
            $errors,
        );

        $assignmentDueDays = null;
        $assignmentMaxScore = null;
        $assignmentPassingScore = null;

        if ($type === Lesson::TYPE_ASSIGNMENT) {
            $assignmentDueDays = $this->integerValue(
                $values['assignment_due_days'] ?? null,
                'assignment_due_days',
                $rowNumber,
                null,
                1,
                3650,
                $errors,
            );
            $assignmentMaxScore = $this->integerValue(
                $values['assignment_max_score'] ?? null,
                'assignment_max_score',
                $rowNumber,
                100,
                1,
                1000,
                $errors,
            );
            $assignmentPassingScore = $this->integerValue(
                $values['assignment_passing_score'] ?? null,
                'assignment_passing_score',
                $rowNumber,
                70,
                0,
                1000,
                $errors,
            );

            if ($assignmentMaxScore !== null
                && $assignmentPassingScore !== null
                && $assignmentPassingScore > $assignmentMaxScore) {
                $errors[] = "Dòng {$rowNumber} — “assignment_passing_score”: điểm đạt không được lớn hơn điểm tối đa.";
            }

            if ($content === null) {
                $warnings[] = "Dòng {$rowNumber}: cần bổ sung yêu cầu bài tập hoặc tệp đính kèm sau khi import.";
            }
        } else {
            foreach (['assignment_due_days', 'assignment_max_score', 'assignment_passing_score'] as $field) {
                if (! $this->isBlank($values[$field] ?? null)) {
                    $errors[] = "Dòng {$rowNumber} — “{$field}”: chỉ được nhập cho bài học assignment.";
                }
            }
        }

        if ($type === Lesson::TYPE_VIDEO) {
            $warnings[] = "Dòng {$rowNumber}: cần tải video lên sau khi import.";
        } elseif ($type === Lesson::TYPE_DOCUMENT && $content === null) {
            $warnings[] = "Dòng {$rowNumber}: cần bổ sung nội dung hoặc tệp tài liệu sau khi import.";
        } elseif ($type === Lesson::TYPE_QUIZ && $content !== null) {
            $errors[] = "Dòng {$rowNumber} — “content”: quiz shell không được chứa nội dung trong template v1.";
        }

        return [[
            'row_number' => $rowNumber,
            'relative_order' => $relativeOrder,
            'lesson_code' => $lessonCode,
            'title' => $title,
            'type' => $type,
            'duration' => $duration,
            'duration_seconds' => $duration,
            'content' => $content,
            'assignment_due_days' => $assignmentDueDays,
            'assignment_max_score' => $assignmentMaxScore,
            'assignment_passing_score' => $assignmentPassingScore,
            'status' => Lesson::STATUS_DRAFT,
            'is_preview' => false,
        ], $errors, $warnings];
    }

    /**
     * @param  array<int, string>  $errors
     */
    private function integerValue(
        mixed $value,
        string $field,
        int $rowNumber,
        ?int $default,
        int $minimum,
        int $maximum,
        array &$errors,
    ): ?int {
        if ($this->isBlank($value)) {
            return $default;
        }

        $isInteger = is_int($value)
            || (is_float($value) && floor($value) === $value)
            || (is_string($value) && preg_match('/^-?\d+$/', trim($value)) === 1);

        if (! $isInteger) {
            $errors[] = "Dòng {$rowNumber} — “{$field}”: phải là số nguyên.";

            return $default;
        }

        $integer = (int) $value;
        if ($integer < $minimum || $integer > $maximum) {
            $errors[] = "Dòng {$rowNumber} — “{$field}”: phải nằm trong khoảng {$minimum}..{$maximum}.";
        }

        return $integer;
    }

    private function stringValue(mixed $value): string
    {
        return is_scalar($value) ? trim((string) $value) : '';
    }

    private function nullableStringValue(mixed $value): ?string
    {
        $string = $this->stringValue($value);

        return $string === '' ? null : $string;
    }

    private function isBlank(mixed $value): bool
    {
        return $value === null || (is_string($value) && trim($value) === '');
    }
}
