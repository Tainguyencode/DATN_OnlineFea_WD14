<?php

namespace App\Services;

use App\Models\Category;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Support\FullCourseImportWorkbookSchema as Schema;
use App\Support\LessonImportWorkbookSchema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class FullCourseImportValidator
{
    public function __construct(private readonly LessonImportV2Validator $v2Validator) {}

    /** @param array<string, array<int, array{row_number:int, values:array<string,mixed>}>> $sheets */
    public function validate(array $sheets): array
    {
        $issues = [];
        $add = function (string $severity, string $code, string $sheet, int $row, ?string $field, string $message) use (&$issues): void {
            $issues[] = compact('severity', 'code', 'sheet', 'row', 'field', 'message');
            $issues[array_key_last($issues)]['row_number'] = $row;
            unset($issues[array_key_last($issues)]['row']);
        };
        $rows = fn (string $sheet): array => $sheets[$sheet] ?? [];
        $this->assertLimit($rows(Schema::SECTIONS_SHEET), Schema::MAX_SECTIONS, Schema::SECTIONS_SHEET, $add);
        $this->assertLimit($rows(Schema::LESSONS_SHEET), Schema::MAX_LESSONS, Schema::LESSONS_SHEET, $add);
        $this->assertLimit($rows(Schema::QUIZZES_SHEET), Schema::MAX_QUIZZES, Schema::QUIZZES_SHEET, $add);
        $this->assertLimit($rows(Schema::QUIZ_QUESTIONS_SHEET), Schema::MAX_QUESTIONS, Schema::QUIZ_QUESTIONS_SHEET, $add);
        $this->assertLimit($rows(Schema::QUIZ_OPTIONS_SHEET), Schema::MAX_OPTIONS, Schema::QUIZ_OPTIONS_SHEET, $add);

        $course = $this->course($rows(Schema::COURSE_SHEET), $add);
        $sections = $this->sections($rows(Schema::SECTIONS_SHEET), $add);
        $sectionCodes = [];
        foreach ($sections as $index => $section) {
            if ($section['section_code'] !== '') {
                $sectionCodes[$section['section_code']][] = $index;
            }
        }
        foreach ($sectionCodes as $code => $indexes) {
            if (count($indexes) > 1) {
                foreach ($indexes as $index) {
                    $add('error', 'duplicate_section_code', Schema::SECTIONS_SHEET, $sections[$index]['row_number'], 'section_code', "section_code {$code} bị trùng trong workbook.");
                }
            }
        }

        $lessonRows = $rows(Schema::LESSONS_SHEET);
        $v2Sheets = [
            LessonImportWorkbookSchema::LESSONS_SHEET => array_map(fn (array $row): array => [
                'row_number' => $row['row_number'],
                'values' => array_diff_key($row['values'], ['section_code' => true]),
            ], $lessonRows),
            LessonImportWorkbookSchema::QUIZZES_SHEET => $rows(Schema::QUIZZES_SHEET),
            LessonImportWorkbookSchema::QUIZ_QUESTIONS_SHEET => $rows(Schema::QUIZ_QUESTIONS_SHEET),
            LessonImportWorkbookSchema::QUIZ_OPTIONS_SHEET => $rows(Schema::QUIZ_OPTIONS_SHEET),
        ];
        // v2 supplies the canonical lesson and complete quiz graph semantics;
        // this unsaved section deliberately has no existing DB rows to merge.
        $v2 = $this->v2Validator->validate($v2Sheets, new CourseSection);
        foreach ($v2['issues'] as $issue) {
            $add($issue['severity'], $issue['code'], $issue['sheet'], (int) $issue['row_number'], $issue['field'], $issue['message']);
        }

        $lessons = [];
        foreach ($v2['canonical_payload']['lessons'] as $index => $lesson) {
            $sectionCode = $this->code($lessonRows[$index]['values']['section_code'] ?? null);
            $lesson['section_code'] = $sectionCode;
            $lesson['order'] = count(array_filter($lessons, fn (array $item): bool => $item['section_code'] === $sectionCode)) + 1;
            $lessons[] = $lesson;
            if ($sectionCode === '' || ! isset($sectionCodes[$sectionCode])) {
                $add('error', 'orphan_lesson_section', Schema::LESSONS_SHEET, $lesson['row_number'], 'section_code', 'section_code phải tham chiếu một Section trong workbook.');
            }
            if ($lesson['type'] === Lesson::TYPE_VIDEO) {
                $add('warning', 'video_source_missing', Schema::LESSONS_SHEET, $lesson['row_number'], 'type', 'Bài học video chưa có nguồn video. Giảng viên cần tải video lên sau khi tạo khóa học.');
            }
        }

        $payload = [
            'template_version' => Schema::VERSION,
            'schema' => Schema::SCHEMA,
            'course' => $course,
            'sections' => array_map(fn (array $section, int $index): array => [...$section, 'order' => $index + 1], $sections, array_keys($sections)),
            'lessons' => $lessons,
            'quizzes' => $v2['canonical_payload']['quizzes'],
            'questions' => $v2['canonical_payload']['questions'],
            'options' => $v2['canonical_payload']['options'],
        ];
        $issues = $this->unique($issues);
        $summary = [
            'sections' => count($sections),
            'lessons' => count($lessons),
            'video' => count(array_filter($lessons, fn (array $row): bool => $row['type'] === Lesson::TYPE_VIDEO)),
            'document' => count(array_filter($lessons, fn (array $row): bool => $row['type'] === Lesson::TYPE_DOCUMENT)),
            'assignment' => count(array_filter($lessons, fn (array $row): bool => $row['type'] === Lesson::TYPE_ASSIGNMENT)),
            'quiz' => count(array_filter($lessons, fn (array $row): bool => $row['type'] === Lesson::TYPE_QUIZ)),
            'questions' => count($payload['questions']),
            'options' => count($payload['options']),
            'errors' => count(array_filter($issues, fn (array $issue): bool => $issue['severity'] === 'error')),
            'warnings' => count(array_filter($issues, fn (array $issue): bool => $issue['severity'] === 'warning')),
        ];

        return [
            'canonical_payload' => $payload,
            'issues' => $issues,
            'summary' => $summary,
            'sheets' => $this->display($payload, $issues),
            'valid_count' => max(0, 1 + $summary['sections'] + $summary['lessons'] + count($payload['quizzes']) + count($payload['questions']) + count($payload['options']) - $summary['errors']),
            'warning_count' => $summary['warnings'],
            'error_count' => $summary['errors'],
        ];
    }

    /** @param array<int, array{row_number:int, values:array<string,mixed>}> $rows */
    private function course(array $rows, callable $add): array
    {
        if (count($rows) === 0) {
            $add('error', 'missing_course_row', Schema::COURSE_SHEET, 2, null, 'Sheet Course phải có đúng một dòng dữ liệu.');

            return $this->courseData([]);
        }
        if (count($rows) !== 1) {
            foreach ($rows as $row) {
                $add('error', 'multiple_course_rows', Schema::COURSE_SHEET, $row['row_number'], null, 'Sheet Course chỉ được có đúng một dòng dữ liệu.');
            }
        }
        $row = $rows[0];
        $data = $this->courseData($row['values']);
        $validator = Validator::make($data, [
            'title' => ['required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'], 'objectives' => ['nullable', 'string'],
            'category_slug' => ['required', 'string', 'max:255'],
            'level' => ['nullable', 'in:beginner,intermediate,advanced'],
            'language' => ['nullable', 'string', 'max:10'],
            'price' => ['required', 'numeric', 'multiple_of:1000', 'min:0', 'max:100000000'],
            'sale_price' => ['nullable', 'numeric', 'multiple_of:1000', 'min:0', 'max:100000000', 'lte:price'],
        ]);
        foreach ($validator->errors()->toArray() as $field => $messages) {
            foreach ($messages as $message) {
                $add('error', 'invalid_course_'.$field, Schema::COURSE_SHEET, $row['row_number'], $field, $message);
            }
        }
        if ($data['category_slug'] !== '' && ! Category::query()->selectableForCourse()->where('slug', $data['category_slug'])->exists()) {
            $add('error', 'invalid_category_slug', Schema::COURSE_SHEET, $row['row_number'], 'category_slug', "Không tìm thấy danh mục \"{$data['category_slug']}\".");
        }

        return $data + ['row_number' => $row['row_number']];
    }

    /** @param array<string, mixed> $values */
    private function courseData(array $values): array
    {
        return [
            'title' => $this->text($values['title'] ?? null), 'short_description' => $this->nullable($values['short_description'] ?? null),
            'description' => $this->nullable($values['description'] ?? null), 'objectives' => $this->nullable($values['objectives'] ?? null),
            'category_slug' => $this->text($values['category_slug'] ?? null), 'level' => Str::lower($this->text($values['level'] ?? null)),
            'language' => $this->nullable($values['language'] ?? null), 'price' => $values['price'] ?? null, 'sale_price' => $values['sale_price'] ?? null,
        ];
    }

    /** @param array<int, array{row_number:int, values:array<string,mixed>}> $rows */
    private function sections(array $rows, callable $add): array
    {
        $result = [];
        foreach ($rows as $row) {
            $code = $this->code($row['values']['section_code'] ?? null);
            $title = $this->text($row['values']['title'] ?? null);
            if ($code === '' || mb_strlen($code) > 64 || preg_match('/^[A-Z0-9_-]+$/', $code) !== 1) {
                $add('error', 'invalid_section_code', Schema::SECTIONS_SHEET, $row['row_number'], 'section_code', 'section_code là bắt buộc và chỉ cho phép A-Z, 0-9, _ hoặc -.');
            }
            if ($title === '' || mb_strlen($title) > 255) {
                $add('error', 'invalid_section_title', Schema::SECTIONS_SHEET, $row['row_number'], 'title', 'Tiêu đề chương là bắt buộc, tối đa 255 ký tự.');
            }
            $result[] = ['row_number' => $row['row_number'], 'section_code' => $code, 'title' => $title, 'description' => $this->nullable($row['values']['description'] ?? null)];
        }

        return $result;
    }

    /** @param array<int, array{row_number:int, values:array<string,mixed>}> $rows */
    private function assertLimit(array $rows, int $limit, string $sheet, callable $add): void
    {
        if (count($rows) > $limit) {
            $add('error', 'row_limit_exceeded', $sheet, $rows[$limit]['row_number'] ?? 2, null, "Sheet {$sheet} vượt quá giới hạn {$limit} dòng.");
        }
    }

    private function text(mixed $value): string
    {
        return is_scalar($value) ? trim((string) $value) : '';
    }

    private function nullable(mixed $value): ?string
    {
        $value = $this->text($value);

        return $value === '' ? null : $value;
    }

    private function code(mixed $value): string
    {
        return Str::upper($this->text($value));
    }

    /** @param array<string,mixed> $payload @param array<int,array<string,mixed>> $issues */
    private function display(array $payload, array $issues): array
    {
        $mapping = ['Course' => 'course', 'Sections' => 'sections', 'Lessons' => 'lessons', 'Quizzes' => 'quizzes', 'QuizQuestions' => 'questions', 'QuizOptions' => 'options'];
        $display = [];
        foreach ($mapping as $sheet => $key) {
            $entities = $key === 'course' ? [$payload['course']] : $payload[$key];
            $display[$sheet] = array_map(function (array $entity) use ($sheet, $issues): array {
                $entityIssues = array_values(array_filter($issues, fn (array $issue): bool => $issue['sheet'] === $sheet && $issue['row_number'] === ($entity['row_number'] ?? 0)));
                $errors = array_values(array_map(fn (array $issue): string => $issue['message'], array_filter($entityIssues, fn (array $issue): bool => $issue['severity'] === 'error')));
                $warnings = array_values(array_map(fn (array $issue): string => $issue['message'], array_filter($entityIssues, fn (array $issue): bool => $issue['severity'] === 'warning')));

                return ['row_number' => $entity['row_number'] ?? 2, 'data' => $entity, 'status' => $errors ? 'error' : ($warnings ? 'warning' : 'valid'), 'errors' => $errors, 'warnings' => $warnings];
            }, $entities);
        }

        return $display;
    }

    /** @param array<int,array<string,mixed>> $issues */
    private function unique(array $issues): array
    {
        $unique = [];
        foreach ($issues as $issue) {
            $unique[implode('|', [$issue['severity'], $issue['code'], $issue['sheet'], $issue['row_number'], $issue['field'] ?? '', $issue['message']])] = $issue;
        }

        return array_values($unique);
    }
}
