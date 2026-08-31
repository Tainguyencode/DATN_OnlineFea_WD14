<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\QuestionVersion;
use App\Models\Quiz;
use App\Models\User;
use DomainException;

/**
 * Persists a previously validated Workbook v2 canonical payload.
 *
 * The caller owns the surrounding transaction and has already locked the
 * course, section, user context, and import batch. This service deliberately
 * does not start an outer transaction or mutate batch state.
 */
class LessonImportV2ImportService
{
    public function __construct(
        private readonly CurriculumLessonService $lessonService,
        private readonly QuizVersioningService $quizVersioning,
        private readonly QuizContentService $quizContent,
    ) {}

    /**
     * @param  array{
     *     template_version: int,
     *     schema: string,
     *     lessons: array<int, array<string, mixed>>,
     *     quizzes: array<int, array<string, mixed>>,
     *     questions: array<int, array<string, mixed>>,
     *     options: array<int, array<string, mixed>>
     * }  $canonicalPayload
     * @return array{
     *     schema_version: int,
     *     lessons: array<string, array{lesson_id: int, quiz_id?: int, quiz_version_id?: int}>,
     *     questions: array<string, array{quiz_id: int, question_id: int, question_version_id: int}>,
     *     options: array<string, array{question_version_id: int, option_id: int}>
     * }
     */
    public function import(
        array $canonicalPayload,
        Course $course,
        CourseSection $section,
        User $actor,
    ): array {
        $lessonRows = $this->orderedRows($this->payloadRows($canonicalPayload, 'lessons'), 'relative_order');
        $quizRows = $this->orderedRows($this->payloadRows($canonicalPayload, 'quizzes'), 'row_number');
        $questionRows = $this->payloadRows($canonicalPayload, 'questions');
        $optionRows = $this->payloadRows($canonicalPayload, 'options');
        $questionsByLessonCode = $this->groupRowsBy($questionRows, 'lesson_code');
        $optionsByQuestionCode = $this->groupRowsBy($optionRows, 'question_code');

        $result = [
            'schema_version' => 2,
            'lessons' => [],
            'questions' => [],
            'options' => [],
        ];
        $lessonsByCode = [];
        $lessonRowsByCode = [];
        $currentMaxSortOrder = $section->lessons()->max('sort_order');
        $nextSortOrder = $currentMaxSortOrder === null ? 0 : ((int) $currentMaxSortOrder + 1);

        foreach ($lessonRows as $lessonRow) {
            $lessonCode = $this->requiredCode($lessonRow, 'lesson_code');
            $createData = [
                ...$lessonRow,
                'sort_order' => $nextSortOrder + (int) $lessonRow['relative_order'],
                'status' => Lesson::STATUS_PUBLISHED,
                'is_preview' => false,
            ];
            unset($createData['row_number'], $createData['relative_order'], $createData['lesson_code']);

            $lesson = $this->lessonService->create($course, $section, $createData);
            $lessonsByCode[$lessonCode] = $lesson;
            $lessonRowsByCode[$lessonCode] = $lessonRow;
            $result['lessons'][$lessonCode] = ['lesson_id' => (int) $lesson->id];
        }

        $importedQuizLessonCodes = [];

        foreach ($quizRows as $quizRow) {
            $lessonCode = $this->requiredCode($quizRow, 'lesson_code');
            $lesson = $lessonsByCode[$lessonCode] ?? null;
            $lessonRow = $lessonRowsByCode[$lessonCode] ?? null;

            if (! $lesson || ! $lessonRow || $lesson->type !== Lesson::TYPE_QUIZ) {
                throw new DomainException('The revalidated quiz row does not resolve to a newly created quiz lesson.');
            }

            $tree = $this->importQuizTree(
                $lesson,
                $lessonCode,
                $quizRow,
                $questionsByLessonCode[$lessonCode] ?? [],
                $optionsByQuestionCode,
                $actor,
            );
            $importedQuizLessonCodes[$lessonCode] = true;
            $result['lessons'][$lessonCode] = [
                'lesson_id' => (int) $lesson->id,
                'quiz_id' => $tree['quiz_id'],
                'quiz_version_id' => $tree['quiz_version_id'],
            ];
            $result['questions'] += $tree['questions'];
            $result['options'] += $tree['options'];
        }

        foreach ($lessonRowsByCode as $lessonCode => $lessonRow) {
            if ($lessonRow['type'] === Lesson::TYPE_QUIZ && ! isset($importedQuizLessonCodes[$lessonCode])) {
                throw new DomainException('The revalidated quiz lesson has no matching quiz metadata row.');
            }
        }

        return $result;
    }

    /**
     * @param  array<string, mixed>  $quizRow
     * @param  array<int, array<string, mixed>>  $questionRows
     * @param  array<string, array<int, array<string, mixed>>>  $optionsByQuestionCode
     * @return array{
     *     quiz_id: int,
     *     quiz_version_id: int,
     *     questions: array<string, array{quiz_id: int, question_id: int, question_version_id: int}>,
     *     options: array<string, array{question_version_id: int, option_id: int}>
     * }
     */
    private function importQuizTree(
        Lesson $lesson,
        string $lessonCode,
        array $quizRow,
        array $questionRows,
        array $optionsByQuestionCode,
        User $actor,
    ): array {
        $metadata = $this->quizContent->normalizeMetadata($quizRow);
        $quiz = Quiz::create([
            'lesson_id' => $lesson->id,
            ...$metadata,
            'is_active' => false,
        ]);
        $draft = $this->quizVersioning->ensureInitialDraft($quiz, $actor);
        $questionResults = [];
        $optionResults = [];
        $orderedQuestions = $this->orderedRows($questionRows, 'relative_order');

        foreach ($orderedQuestions as $questionRow) {
            if ($this->requiredCode($questionRow, 'lesson_code') !== $lessonCode) {
                throw new DomainException('The revalidated question does not belong to its importing quiz lesson.');
            }

            $questionCode = $this->requiredCode($questionRow, 'question_code');
            $attributes = [
                'question' => trim((string) $questionRow['question']),
                'type' => $this->quizContent->canonicalType((string) $questionRow['type']),
                'points' => (int) $questionRow['points'],
                'explanation' => $this->nullableText($questionRow['explanation'] ?? null),
            ];
            $question = $quiz->questions()->create([
                ...$attributes,
                'sort_order' => (int) $questionRow['relative_order'],
            ]);
            $questionVersion = $question->versions()->create([
                ...$attributes,
                'version' => 1,
                'status' => QuestionVersion::STATUS_DRAFT,
            ]);

            foreach ($this->orderedRows($optionsByQuestionCode[$questionCode] ?? [], 'relative_order') as $optionRow) {
                $optionCode = $this->requiredCode($optionRow, 'option_code');
                $option = $questionVersion->options()->create([
                    'quiz_question_id' => $question->id,
                    'option_text' => trim((string) $optionRow['option_text']),
                    'is_correct' => (bool) $optionRow['is_correct'],
                    'sort_order' => (int) $optionRow['relative_order'],
                ]);
                $optionResults[$lessonCode.'/'.$questionCode.'/'.$optionCode] = [
                    'question_version_id' => (int) $questionVersion->id,
                    'option_id' => (int) $option->id,
                ];
            }

            $questionValidation = $this->quizContent->validateQuestionVersion($questionVersion->fresh('options'));

            if (! $questionValidation['is_complete']) {
                throw new DomainException('The revalidated quiz question is not complete for version-aware import.');
            }

            $draft->questionMappings()->create([
                'question_id' => $question->id,
                'question_version_id' => $questionVersion->id,
                'sort_order' => (int) $questionRow['relative_order'],
            ]);
            $questionResults[$lessonCode.'/'.$questionCode] = [
                'quiz_id' => (int) $quiz->id,
                'question_id' => (int) $question->id,
                'question_version_id' => (int) $questionVersion->id,
            ];
        }

        $quizValidation = $this->quizContent->validateQuizVersion(
            $draft->fresh('questionMappings.questionVersion.options'),
        );

        if ($orderedQuestions !== [] && ! $quizValidation['is_complete']) {
            throw new DomainException('The revalidated quiz is not complete for version-aware import.');
        }

        $quiz->update([
            'is_active' => $orderedQuestions === [] ? false : (bool) $quizRow['is_active'],
        ]);

        return [
            'quiz_id' => (int) $quiz->id,
            'quiz_version_id' => (int) $draft->id,
            'questions' => $questionResults,
            'options' => $optionResults,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<int, array<string, mixed>>
     */
    private function payloadRows(array $payload, string $key): array
    {
        $rows = $payload[$key] ?? [];

        if (! is_array($rows)) {
            throw new DomainException('The revalidated Workbook v2 payload has an invalid '.$key.' collection.');
        }

        return array_values($rows);
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return array<int, array<string, mixed>>
     */
    private function orderedRows(array $rows, string $sortField): array
    {
        usort($rows, function (array $left, array $right) use ($sortField): int {
            return [(int) $left[$sortField], (int) $left['row_number']]
                <=> [(int) $right[$sortField], (int) $right['row_number']];
        });

        return $rows;
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return array<string, array<int, array<string, mixed>>>
     */
    private function groupRowsBy(array $rows, string $key): array
    {
        $grouped = [];

        foreach ($rows as $row) {
            $grouped[$this->requiredCode($row, $key)][] = $row;
        }

        return $grouped;
    }

    /** @param array<string, mixed> $row */
    private function requiredCode(array $row, string $key): string
    {
        $value = trim((string) ($row[$key] ?? ''));

        if ($value === '') {
            throw new DomainException('The revalidated Workbook v2 payload has an empty '.$key.'.');
        }

        return $value;
    }

    private function nullableText(mixed $value): ?string
    {
        $text = trim((string) $value);

        return $text === '' ? null : $text;
    }
}
