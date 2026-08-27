<?php

namespace App\Services;

use App\Exceptions\LessonImportException;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Support\LessonImportWorkbookSchema;
use Illuminate\Support\Str;

class LessonImportV2Validator
{
    public function __construct(
        private readonly LessonImportValidator $lessonValidator,
    ) {}

    /**
     * @param  array<string, array<int, array{row_number: int, values: array<string, mixed>}>>  $sheets
     * @return array<string, mixed>
     */
    public function validate(array $sheets, CourseSection $section): array
    {
        $issues = [];
        $addIssue = function (
            string $severity,
            string $code,
            string $sheet,
            int $rowNumber,
            ?string $field,
            string $message,
        ) use (&$issues): void {
            $issues[] = [
                'severity' => $severity,
                'code' => $code,
                'sheet' => $sheet,
                'row_number' => $rowNumber,
                'field' => $field,
                'message' => $message,
            ];
        };

        $lessonRows = $this->sheetRows($sheets, LessonImportWorkbookSchema::LESSONS_SHEET);
        $legacyLessons = $this->lessonValidator->validate($lessonRows, $section);
        $lessons = $legacyLessons['canonical_rows'];

        foreach ($legacyLessons['reports'] as $report) {
            foreach ($report['errors'] as $message) {
                $addIssue(
                    'error',
                    $this->legacyLessonIssueCode($message),
                    LessonImportWorkbookSchema::LESSONS_SHEET,
                    (int) $report['row_number'],
                    $this->fieldFromLegacyMessage($message),
                    $message,
                );
            }

            foreach ($report['warnings'] as $message) {
                $addIssue(
                    'warning',
                    $this->legacyLessonIssueCode($message),
                    LessonImportWorkbookSchema::LESSONS_SHEET,
                    (int) $report['row_number'],
                    $this->fieldFromLegacyMessage($message),
                    $message,
                );
            }
        }

        $lessonsByCode = [];
        foreach ($lessons as $index => $lesson) {
            if (($lesson['lesson_code'] ?? '') !== '') {
                $lessonsByCode[$lesson['lesson_code']][] = $index;
            }
        }

        $quizzes = [];
        foreach ($this->sheetRows($sheets, LessonImportWorkbookSchema::QUIZZES_SHEET) as $rawRow) {
            $rowNumber = (int) $rawRow['row_number'];
            $values = $rawRow['values'];
            $lessonCode = $this->canonicalCode($values['lesson_code'] ?? null);
            $title = $this->stringValue($values['title'] ?? null);
            $description = $this->nullableStringValue($values['description'] ?? null);

            $this->assertTextCell($values['lesson_code'] ?? null, 'lesson_code', LessonImportWorkbookSchema::QUIZZES_SHEET, $rowNumber, $addIssue);
            $this->assertTextCell($values['title'] ?? null, 'title', LessonImportWorkbookSchema::QUIZZES_SHEET, $rowNumber, $addIssue);
            $this->assertTextCell($values['description'] ?? null, 'description', LessonImportWorkbookSchema::QUIZZES_SHEET, $rowNumber, $addIssue);

            if ($lessonCode === '') {
                $addIssue('error', 'required_lesson_code', 'Quizzes', $rowNumber, 'lesson_code', 'lesson_code là bắt buộc.');
            } elseif (mb_strlen($lessonCode) > 64 || preg_match('/^[A-Z0-9_-]+$/', $lessonCode) !== 1) {
                $addIssue('error', 'invalid_lesson_code', 'Quizzes', $rowNumber, 'lesson_code', 'lesson_code chỉ cho phép A-Z, 0-9, dấu gạch dưới và gạch ngang; tối đa 64 ký tự.');
            }

            if ($title === '') {
                $addIssue('error', 'required_quiz_title', 'Quizzes', $rowNumber, 'title', 'Tiêu đề quiz là bắt buộc.');
            } elseif (mb_strlen($title) > 255) {
                $addIssue('error', 'quiz_title_too_long', 'Quizzes', $rowNumber, 'title', 'Tiêu đề quiz tối đa 255 ký tự.');
            }

            if ($description !== null && mb_strlen($description) > 5000) {
                $addIssue('error', 'quiz_description_too_long', 'Quizzes', $rowNumber, 'description', 'Mô tả quiz tối đa 5000 ký tự.');
            }

            $passScore = $this->integerValue(
                $values['pass_score'] ?? null,
                'pass_score',
                'Quizzes',
                $rowNumber,
                0,
                100,
                false,
                $addIssue,
            );
            $timeLimit = $this->integerValue(
                $values['time_limit_minutes'] ?? null,
                'time_limit_minutes',
                'Quizzes',
                $rowNumber,
                1,
                1440,
                true,
                $addIssue,
            );
            $maxAttempts = $this->integerValue(
                $values['max_attempts'] ?? null,
                'max_attempts',
                'Quizzes',
                $rowNumber,
                1,
                100,
                true,
                $addIssue,
            );
            $isActive = $this->booleanValue(
                $values['is_active'] ?? null,
                'is_active',
                'Quizzes',
                $rowNumber,
                $addIssue,
            );

            $quizzes[] = [
                'row_number' => $rowNumber,
                'lesson_code' => $lessonCode,
                'title' => $title,
                'description' => $description,
                'pass_score' => $passScore,
                'time_limit_minutes' => $timeLimit,
                'max_attempts' => $maxAttempts,
                'is_active' => $isActive,
            ];
        }

        $quizzesByLessonCode = [];
        $quizTitles = [];
        foreach ($quizzes as $index => $quiz) {
            if ($quiz['lesson_code'] !== '') {
                $quizzesByLessonCode[$quiz['lesson_code']][] = $index;
            }
            if ($quiz['title'] !== '') {
                $quizTitles[Str::lower($quiz['title'])][] = $index;
            }
        }

        foreach ($quizzesByLessonCode as $lessonCode => $indexes) {
            if (count($indexes) < 2) {
                continue;
            }

            foreach ($indexes as $index) {
                $addIssue('error', 'duplicate_quiz_metadata', 'Quizzes', (int) $quizzes[$index]['row_number'], 'lesson_code', "Quiz metadata cho lesson_code {$lessonCode} bị trùng.");
            }
        }

        foreach ($quizTitles as $indexes) {
            if (count($indexes) < 2) {
                continue;
            }

            foreach ($indexes as $index) {
                $addIssue('warning', 'duplicate_quiz_title', 'Quizzes', (int) $quizzes[$index]['row_number'], 'title', 'Tiêu đề quiz bị trùng trong workbook.');
            }
        }

        foreach ($quizzes as $index => $quiz) {
            $lessonIndexes = $lessonsByCode[$quiz['lesson_code']] ?? [];
            if ($quiz['lesson_code'] === '' || $lessonIndexes === []) {
                $addIssue('error', 'orphan_quiz', 'Quizzes', (int) $quiz['row_number'], 'lesson_code', 'lesson_code phải tham chiếu một Lesson trong workbook.');

                continue;
            }

            $lesson = $lessons[$lessonIndexes[0]];
            if (($lesson['type'] ?? null) !== Lesson::TYPE_QUIZ) {
                $addIssue('error', 'quiz_reference_not_quiz_lesson', 'Quizzes', (int) $quiz['row_number'], 'lesson_code', 'lesson_code phải tham chiếu Lesson có type=quiz.');
            }
        }

        foreach ($lessons as $lesson) {
            if (($lesson['type'] ?? null) !== Lesson::TYPE_QUIZ || ($lesson['lesson_code'] ?? '') === '') {
                continue;
            }

            if (count($quizzesByLessonCode[$lesson['lesson_code']] ?? []) !== 1) {
                $addIssue('error', 'missing_quiz_metadata', 'Lessons', (int) $lesson['row_number'], 'lesson_code', 'Mỗi Lesson type=quiz phải có đúng một dòng Quizzes.');
            }
        }

        $questions = [];
        $questionOrderByLesson = [];
        foreach ($this->sheetRows($sheets, LessonImportWorkbookSchema::QUIZ_QUESTIONS_SHEET) as $rawRow) {
            $rowNumber = (int) $rawRow['row_number'];
            $values = $rawRow['values'];
            $lessonCode = $this->canonicalCode($values['lesson_code'] ?? null);
            $questionCode = $this->canonicalCode($values['question_code'] ?? null);
            $question = $this->stringValue($values['question'] ?? null);
            $type = Str::lower($this->stringValue($values['type'] ?? null));
            $explanation = $this->nullableStringValue($values['explanation'] ?? null);
            $relativeOrder = $questionOrderByLesson[$lessonCode] ?? 0;
            $questionOrderByLesson[$lessonCode] = $relativeOrder + 1;

            foreach (['lesson_code', 'question_code', 'question', 'type', 'explanation'] as $field) {
                $this->assertTextCell($values[$field] ?? null, $field, 'QuizQuestions', $rowNumber, $addIssue);
            }

            $this->validateCode($lessonCode, 'lesson_code', 'QuizQuestions', $rowNumber, $addIssue, true);
            $this->validateCode($questionCode, 'question_code', 'QuizQuestions', $rowNumber, $addIssue, true);

            if ($question === '') {
                $addIssue('error', 'required_question', 'QuizQuestions', $rowNumber, 'question', 'Nội dung câu hỏi là bắt buộc.');
            } elseif (mb_strlen($question) > 10000) {
                $addIssue('error', 'question_too_long', 'QuizQuestions', $rowNumber, 'question', 'Nội dung câu hỏi tối đa 10000 ký tự.');
            }

            if (! in_array($type, LessonImportWorkbookSchema::QUESTION_TYPES, true)) {
                $addIssue('error', 'invalid_question_type', 'QuizQuestions', $rowNumber, 'type', 'type chỉ hỗ trợ single, multiple hoặc true_false.');
            }

            if ($explanation !== null && mb_strlen($explanation) > 10000) {
                $addIssue('error', 'explanation_too_long', 'QuizQuestions', $rowNumber, 'explanation', 'Giải thích tối đa 10000 ký tự.');
            }

            $points = $this->integerValue($values['points'] ?? null, 'points', 'QuizQuestions', $rowNumber, 1, 1000, false, $addIssue);
            $questions[] = [
                'row_number' => $rowNumber,
                'lesson_code' => $lessonCode,
                'question_code' => $questionCode,
                'question' => $question,
                'type' => $type,
                'points' => $points,
                'explanation' => $explanation,
                'relative_order' => $relativeOrder,
            ];
        }

        $questionsByCode = [];
        $questionsByLessonCode = [];
        $questionTexts = [];
        foreach ($questions as $index => $question) {
            if ($question['question_code'] !== '') {
                $questionsByCode[$question['question_code']][] = $index;
            }
            if ($question['lesson_code'] !== '') {
                $questionsByLessonCode[$question['lesson_code']][] = $index;
            }
            if ($question['lesson_code'] !== '' && $question['question'] !== '') {
                $questionTexts[$question['lesson_code']][Str::lower($question['question'])][] = $index;
            }
        }

        foreach ($questionsByCode as $questionCode => $indexes) {
            if (count($indexes) < 2) {
                continue;
            }
            foreach ($indexes as $index) {
                $addIssue('error', 'duplicate_question_code', 'QuizQuestions', (int) $questions[$index]['row_number'], 'question_code', "question_code {$questionCode} bị trùng trong workbook.");
            }
        }

        foreach ($questionTexts as $texts) {
            foreach ($texts as $indexes) {
                if (count($indexes) < 2) {
                    continue;
                }
                foreach ($indexes as $index) {
                    $addIssue('warning', 'duplicate_question_text', 'QuizQuestions', (int) $questions[$index]['row_number'], 'question', 'Nội dung câu hỏi bị trùng trong cùng quiz.');
                }
            }
        }

        foreach ($questions as $question) {
            $lessonIndexes = $lessonsByCode[$question['lesson_code']] ?? [];
            if ($question['lesson_code'] === '' || $lessonIndexes === []) {
                $addIssue('error', 'orphan_question', 'QuizQuestions', (int) $question['row_number'], 'lesson_code', 'Câu hỏi phải tham chiếu Lesson quiz trong workbook.');

                continue;
            }

            $lesson = $lessons[$lessonIndexes[0]];
            if (($lesson['type'] ?? null) !== Lesson::TYPE_QUIZ) {
                $addIssue('error', 'question_reference_not_quiz_lesson', 'QuizQuestions', (int) $question['row_number'], 'lesson_code', 'Câu hỏi không được tham chiếu Lesson video, document hoặc assignment.');
            }

            if (count($quizzesByLessonCode[$question['lesson_code']] ?? []) !== 1) {
                $addIssue('error', 'question_missing_quiz_metadata', 'QuizQuestions', (int) $question['row_number'], 'lesson_code', 'Câu hỏi phải có Quiz metadata tương ứng.');
            }
        }

        $options = [];
        $optionOrderByQuestion = [];
        foreach ($this->sheetRows($sheets, LessonImportWorkbookSchema::QUIZ_OPTIONS_SHEET) as $rawRow) {
            $rowNumber = (int) $rawRow['row_number'];
            $values = $rawRow['values'];
            $questionCode = $this->canonicalCode($values['question_code'] ?? null);
            $optionCode = $this->canonicalCode($values['option_code'] ?? null);
            $optionText = $this->stringValue($values['option_text'] ?? null);
            $relativeOrder = $optionOrderByQuestion[$questionCode] ?? 0;
            $optionOrderByQuestion[$questionCode] = $relativeOrder + 1;

            foreach (['question_code', 'option_code', 'option_text'] as $field) {
                $this->assertTextCell($values[$field] ?? null, $field, 'QuizOptions', $rowNumber, $addIssue);
            }

            $this->validateCode($questionCode, 'question_code', 'QuizOptions', $rowNumber, $addIssue, true);
            $this->validateCode($optionCode, 'option_code', 'QuizOptions', $rowNumber, $addIssue, true);

            if ($optionText === '') {
                $addIssue('error', 'required_option_text', 'QuizOptions', $rowNumber, 'option_text', 'Nội dung đáp án là bắt buộc.');
            } elseif (mb_strlen($optionText) > 5000) {
                $addIssue('error', 'option_text_too_long', 'QuizOptions', $rowNumber, 'option_text', 'Nội dung đáp án tối đa 5000 ký tự.');
            }

            $options[] = [
                'row_number' => $rowNumber,
                'question_code' => $questionCode,
                'option_code' => $optionCode,
                'option_text' => $optionText,
                'is_correct' => $this->booleanValue($values['is_correct'] ?? null, 'is_correct', 'QuizOptions', $rowNumber, $addIssue),
                'relative_order' => $relativeOrder,
            ];
        }

        $optionsByQuestionCode = [];
        $optionCodes = [];
        $optionTexts = [];
        foreach ($options as $index => $option) {
            if ($option['question_code'] !== '') {
                $optionsByQuestionCode[$option['question_code']][] = $index;
                if ($option['option_code'] !== '') {
                    $optionCodes[$option['question_code']][$option['option_code']][] = $index;
                }
                if ($option['option_text'] !== '') {
                    $optionTexts[$option['question_code']][Str::lower($option['option_text'])][] = $index;
                }
            }
        }

        foreach ($optionCodes as $codes) {
            foreach ($codes as $indexes) {
                if (count($indexes) < 2) {
                    continue;
                }
                foreach ($indexes as $index) {
                    $addIssue('error', 'duplicate_option_code', 'QuizOptions', (int) $options[$index]['row_number'], 'option_code', 'option_code bị trùng trong cùng question_code.');
                }
            }
        }

        foreach ($optionTexts as $texts) {
            foreach ($texts as $indexes) {
                if (count($indexes) < 2) {
                    continue;
                }
                foreach ($indexes as $index) {
                    $addIssue('error', 'duplicate_option_text', 'QuizOptions', (int) $options[$index]['row_number'], 'option_text', 'Nội dung đáp án bị trùng trong cùng câu hỏi.');
                }
            }
        }

        foreach ($options as $option) {
            if ($option['question_code'] === '' || ! isset($questionsByCode[$option['question_code']])) {
                $addIssue('error', 'orphan_option', 'QuizOptions', (int) $option['row_number'], 'question_code', 'question_code phải tham chiếu một câu hỏi trong workbook.');
            }
        }

        foreach ($questions as $question) {
            $questionOptions = array_map(
                fn (int $index): array => $options[$index],
                $optionsByQuestionCode[$question['question_code']] ?? [],
            );
            $this->validateQuestionOptions($question, $questionOptions, $addIssue);
        }

        foreach ($quizzes as $index => $quiz) {
            if ($quiz['lesson_code'] === '' || count($quizzesByLessonCode[$quiz['lesson_code']] ?? []) !== 1) {
                continue;
            }

            $questionCount = count($questionsByLessonCode[$quiz['lesson_code']] ?? []);
            if ($questionCount === 0) {
                $quizzes[$index]['is_active'] = false;
                $addIssue('warning', 'quiz_shell', 'Quizzes', (int) $quiz['row_number'], 'is_active', 'Quiz chưa có câu hỏi sẽ được import dưới dạng draft shell và tắt hoạt động.');
            } elseif ($questionCount < QuizContentService::MIN_QUESTIONS) {
                $addIssue('error', 'incomplete_quiz', 'Quizzes', (int) $quiz['row_number'], null, 'Quiz phải có ít nhất 5 câu hỏi hoặc để trống hoàn toàn.');
            }
        }

        $canonicalPayload = [
            'template_version' => LessonImportWorkbookSchema::VERSION_V2,
            'schema' => LessonImportWorkbookSchema::SCHEMA,
            'lessons' => array_values($lessons),
            'quizzes' => array_values($quizzes),
            'questions' => array_values($questions),
            'options' => array_values($options),
        ];
        $issues = $this->uniqueIssues($issues);
        $displaySheets = $this->displaySheets($canonicalPayload, $issues);
        $summary = [
            'lessons' => count($lessons),
            'quizzes' => count($quizzes),
            'questions' => count($questions),
            'options' => count($options),
            'errors' => count(array_filter($issues, fn (array $issue): bool => $issue['severity'] === 'error')),
            'warnings' => count(array_filter($issues, fn (array $issue): bool => $issue['severity'] === 'warning')),
        ];

        return [
            'canonical_payload' => $canonicalPayload,
            'issues' => $issues,
            'summary' => $summary,
            'sheets' => $displaySheets,
            'valid_count' => $this->validEntityCount($displaySheets),
            'warning_count' => $summary['warnings'],
            'error_count' => $summary['errors'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function validateCanonicalPayload(mixed $payload, CourseSection $section): array
    {
        if (! is_array($payload)
            || ($payload['template_version'] ?? null) !== LessonImportWorkbookSchema::VERSION_V2
            || ($payload['schema'] ?? null) !== LessonImportWorkbookSchema::SCHEMA) {
            throw $this->invalidCanonicalPayload();
        }

        $definitions = [
            'lessons' => LessonImportWorkbookSchema::V2_LESSON_HEADERS,
            'quizzes' => LessonImportWorkbookSchema::QUIZ_HEADERS,
            'questions' => LessonImportWorkbookSchema::QUIZ_QUESTION_HEADERS,
            'options' => LessonImportWorkbookSchema::QUIZ_OPTION_HEADERS,
        ];
        $sheetNames = [
            'lessons' => LessonImportWorkbookSchema::LESSONS_SHEET,
            'quizzes' => LessonImportWorkbookSchema::QUIZZES_SHEET,
            'questions' => LessonImportWorkbookSchema::QUIZ_QUESTIONS_SHEET,
            'options' => LessonImportWorkbookSchema::QUIZ_OPTIONS_SHEET,
        ];
        $extraCanonicalKeys = [
            'lessons' => ['duration', 'status', 'is_preview', 'relative_order'],
            'quizzes' => [],
            'questions' => ['relative_order'],
            'options' => ['relative_order'],
        ];
        $sheets = [];

        foreach ($definitions as $payloadKey => $headers) {
            if (! isset($payload[$payloadKey]) || ! is_array($payload[$payloadKey]) || ! array_is_list($payload[$payloadKey])) {
                throw $this->invalidCanonicalPayload();
            }

            $sheets[$sheetNames[$payloadKey]] = [];
            foreach ($payload[$payloadKey] as $row) {
                if (! is_array($row) || ! isset($row['row_number']) || ! is_int($row['row_number']) || $row['row_number'] < 2) {
                    throw $this->invalidCanonicalPayload();
                }

                $allowedKeys = array_merge(['row_number'], $headers, $extraCanonicalKeys[$payloadKey]);
                if (array_diff(array_keys($row), $allowedKeys) !== []) {
                    throw $this->invalidCanonicalPayload();
                }

                $values = [];
                foreach ($headers as $header) {
                    if (! array_key_exists($header, $row)) {
                        throw $this->invalidCanonicalPayload();
                    }
                    $values[$header] = $row[$header];
                }
                $sheets[$sheetNames[$payloadKey]][] = [
                    'row_number' => $row['row_number'],
                    'values' => $values,
                ];
            }
        }

        if ($sheets[LessonImportWorkbookSchema::LESSONS_SHEET] === []) {
            throw $this->invalidCanonicalPayload();
        }

        $validated = $this->validate($sheets, $section);
        if ($validated['error_count'] > 0) {
            throw $this->invalidCanonicalPayload();
        }

        return $validated;
    }

    /**
     * @param  array<string, array<int, array{row_number: int, values: array<string, mixed>}>>  $sheets
     * @return array<int, array{row_number: int, values: array<string, mixed>}>
     */
    private function sheetRows(array $sheets, string $sheetName): array
    {
        return isset($sheets[$sheetName]) && is_array($sheets[$sheetName])
            ? $sheets[$sheetName]
            : [];
    }

    /** @param callable(string, string, string, int, ?string, string): void $addIssue */
    private function assertTextCell(mixed $value, string $field, string $sheet, int $rowNumber, callable $addIssue): void
    {
        if (! $this->isBlank($value) && ! is_string($value)) {
            $addIssue('error', 'invalid_text_value', $sheet, $rowNumber, $field, "{$field} phải là dữ liệu văn bản.");
        }
    }

    /** @param callable(string, string, string, int, ?string, string): void $addIssue */
    private function validateCode(string $value, string $field, string $sheet, int $rowNumber, callable $addIssue, bool $enforcePattern): void
    {
        if ($value === '') {
            $addIssue('error', 'required_'.$field, $sheet, $rowNumber, $field, "{$field} là bắt buộc.");

            return;
        }

        if ($enforcePattern && (mb_strlen($value) > 64 || preg_match('/^[A-Z0-9_-]+$/', $value) !== 1)) {
            $addIssue('error', 'invalid_'.$field, $sheet, $rowNumber, $field, "{$field} chỉ cho phép A-Z, 0-9, dấu gạch dưới và gạch ngang; tối đa 64 ký tự.");
        }
    }

    /** @param callable(string, string, string, int, ?string, string): void $addIssue */
    private function integerValue(mixed $value, string $field, string $sheet, int $rowNumber, int $minimum, int $maximum, bool $nullable, callable $addIssue): ?int
    {
        if ($this->isBlank($value)) {
            if (! $nullable) {
                $addIssue('error', 'required_'.$field, $sheet, $rowNumber, $field, "{$field} là bắt buộc.");
            }

            return null;
        }

        $isInteger = is_int($value)
            || (is_float($value) && floor($value) === $value)
            || (is_string($value) && preg_match('/^-?\d+$/', trim($value)) === 1);
        if (! $isInteger) {
            $addIssue('error', 'invalid_'.$field, $sheet, $rowNumber, $field, "{$field} phải là số nguyên.");

            return null;
        }

        $integer = (int) $value;
        if ($integer < $minimum || $integer > $maximum) {
            $addIssue('error', 'invalid_'.$field, $sheet, $rowNumber, $field, "{$field} phải nằm trong khoảng {$minimum}..{$maximum}.");
        }

        return $integer;
    }

    /** @param callable(string, string, string, int, ?string, string): void $addIssue */
    private function booleanValue(mixed $value, string $field, string $sheet, int $rowNumber, callable $addIssue): ?bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            return match (mb_strtoupper(trim($value))) {
                'TRUE' => true,
                'FALSE' => false,
                default => $this->invalidBoolean($field, $sheet, $rowNumber, $addIssue),
            };
        }

        $addIssue('error', 'invalid_'.$field, $sheet, $rowNumber, $field, "{$field} chỉ chấp nhận TRUE hoặc FALSE.");

        return null;
    }

    /** @param callable(string, string, string, int, ?string, string): void $addIssue */
    private function invalidBoolean(string $field, string $sheet, int $rowNumber, callable $addIssue): ?bool
    {
        $addIssue('error', 'invalid_'.$field, $sheet, $rowNumber, $field, "{$field} chỉ chấp nhận TRUE hoặc FALSE.");

        return null;
    }

    /**
     * @param  array<string, mixed>  $question
     * @param  array<int, array<string, mixed>>  $options
     * @param  callable(string, string, string, int, ?string, string): void  $addIssue
     */
    private function validateQuestionOptions(array $question, array $options, callable $addIssue): void
    {
        $correctCount = count(array_filter($options, fn (array $option): bool => $option['is_correct'] === true));
        $count = count($options);
        $sheet = LessonImportWorkbookSchema::QUIZ_QUESTIONS_SHEET;
        $row = (int) $question['row_number'];

        if ($question['type'] === 'single') {
            if ($count < QuizContentService::MIN_OPTIONS) {
                $addIssue('error', 'single_minimum_options', $sheet, $row, 'type', 'Câu hỏi single phải có ít nhất 3 đáp án.');
            }
            if ($correctCount !== 1) {
                $addIssue('error', 'single_correct_count', $sheet, $row, 'type', 'Câu hỏi single phải có đúng một đáp án đúng.');
            }
        }

        if ($question['type'] === 'multiple') {
            if ($count < QuizContentService::MIN_OPTIONS || $count > QuizContentService::MAX_MULTIPLE_OPTIONS) {
                $addIssue('error', 'multiple_option_count', $sheet, $row, 'type', 'Câu hỏi multiple phải có từ 3 đến 10 đáp án.');
            }
            if ($correctCount < 1) {
                $addIssue('error', 'multiple_correct_count', $sheet, $row, 'type', 'Câu hỏi multiple phải có ít nhất một đáp án đúng.');
            }
            if ($count > 0 && $correctCount === $count) {
                $addIssue('warning', 'multiple_all_correct', $sheet, $row, 'type', 'Tất cả đáp án của câu hỏi multiple đều được đánh dấu đúng.');
            }
        }

        if ($question['type'] === 'true_false') {
            $codes = array_values(array_unique(array_map(fn (array $option): string => $option['option_code'], $options)));
            sort($codes);
            if ($count !== 2 || $codes !== ['FALSE', 'TRUE']) {
                $addIssue('error', 'true_false_options', $sheet, $row, 'type', 'Câu hỏi true_false phải có đúng hai option_code TRUE và FALSE.');
            }
            if ($correctCount !== 1) {
                $addIssue('error', 'true_false_correct_count', $sheet, $row, 'type', 'Câu hỏi true_false phải có đúng một đáp án đúng.');
            }
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<int, array<string, mixed>>  $issues
     * @return array<string, array<int, array<string, mixed>>>
     */
    private function displaySheets(array $payload, array $issues): array
    {
        $keys = [
            'lessons' => LessonImportWorkbookSchema::LESSONS_SHEET,
            'quizzes' => LessonImportWorkbookSchema::QUIZZES_SHEET,
            'questions' => LessonImportWorkbookSchema::QUIZ_QUESTIONS_SHEET,
            'options' => LessonImportWorkbookSchema::QUIZ_OPTIONS_SHEET,
        ];
        $result = [];

        foreach ($keys as $payloadKey => $sheetName) {
            $result[$sheetName] = [];
            foreach ($payload[$payloadKey] as $row) {
                $rowIssues = array_values(array_filter(
                    $issues,
                    fn (array $issue): bool => $issue['sheet'] === $sheetName && (int) $issue['row_number'] === (int) $row['row_number'],
                ));
                $errors = array_values(array_map(
                    fn (array $issue): string => $issue['message'],
                    array_filter($rowIssues, fn (array $issue): bool => $issue['severity'] === 'error'),
                ));
                $warnings = array_values(array_map(
                    fn (array $issue): string => $issue['message'],
                    array_filter($rowIssues, fn (array $issue): bool => $issue['severity'] === 'warning'),
                ));
                $result[$sheetName][] = [
                    'row_number' => $row['row_number'],
                    'data' => $row,
                    'status' => $errors !== [] ? 'error' : ($warnings !== [] ? 'warning' : 'valid'),
                    'errors' => $errors,
                    'warnings' => $warnings,
                ];
            }
        }

        return $result;
    }

    /** @param array<string, array<int, array<string, mixed>>> $sheets */
    private function validEntityCount(array $sheets): int
    {
        $count = 0;
        foreach ($sheets as $rows) {
            foreach ($rows as $row) {
                if ($row['status'] === 'valid') {
                    $count++;
                }
            }
        }

        return $count;
    }

    /** @param array<int, array<string, mixed>> $issues */
    private function uniqueIssues(array $issues): array
    {
        $unique = [];
        foreach ($issues as $issue) {
            $key = implode('|', [
                $issue['severity'],
                $issue['code'],
                $issue['sheet'],
                $issue['row_number'],
                $issue['field'] ?? '',
                $issue['message'],
            ]);
            $unique[$key] = $issue;
        }

        return array_values($unique);
    }

    private function canonicalCode(mixed $value): string
    {
        return Str::upper($this->stringValue($value));
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

    private function fieldFromLegacyMessage(string $message): ?string
    {
        return preg_match('/“([^”]+)”/u', $message, $matches) === 1 ? $matches[1] : null;
    }

    private function legacyLessonIssueCode(string $message): string
    {
        return match (true) {
            str_contains($message, 'mã bị trùng') => 'duplicate_lesson_code',
            str_contains($message, 'tiêu đề bị trùng') => 'duplicate_lesson_title',
            default => 'invalid_lesson',
        };
    }

    private function invalidCanonicalPayload(): LessonImportException
    {
        return new LessonImportException(
            'invalid_canonical_payload',
            'Dữ liệu kiểm tra đã thay đổi hoặc không còn hợp lệ. Vui lòng kiểm tra lại file.',
        );
    }
}
