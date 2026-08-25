<?php

namespace App\Services;

use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class QuizContentService
{
    public const MIN_QUESTIONS = 5;

    public const MIN_OPTIONS = 3;

    public const MAX_MULTIPLE_OPTIONS = 10;

    /**
     * @return array<string, string>
     */
    public function questionTypes(): array
    {
        return [
            'single_choice' => 'single',
            'multiple_choice' => 'multiple',
            'true_false' => 'true_false',
        ];
    }

    public function canonicalType(string $type): string
    {
        return match ($type) {
            'single_choice', QuizQuestion::TYPE_SINGLE => QuizQuestion::TYPE_SINGLE,
            'multiple_choice', QuizQuestion::TYPE_MULTIPLE => QuizQuestion::TYPE_MULTIPLE,
            'true_false', QuizQuestion::TYPE_TRUE_FALSE => QuizQuestion::TYPE_TRUE_FALSE,
            default => throw new InvalidArgumentException('Unsupported quiz question type: '.$type),
        };
    }

    public function getOrCreateForLesson(Lesson $lesson): Quiz
    {
        try {
            return $lesson->quiz()->firstOrCreate([], [
                'title' => $lesson->title,
                'pass_score' => 70,
                'time_limit_minutes' => null,
                'max_attempts' => null,
                'is_active' => false,
            ]);
        } catch (UniqueConstraintViolationException $exception) {
            $quiz = $lesson->quiz()->first();

            if ($quiz) {
                return $quiz;
            }

            throw $exception;
        }
    }

    /**
     * @param  array<string, mixed>  $metadata
     */
    public function saveMetadata(Lesson $lesson, array $metadata, bool $isActive): Quiz
    {
        $normalized = $this->normalizeMetadata($metadata);

        return DB::transaction(function () use ($lesson, $normalized, $isActive): Quiz {
            $quiz = $this->getOrCreateForLesson($lesson);
            $quiz = Quiz::query()->lockForUpdate()->findOrFail($quiz->id);
            $quiz->update([...$normalized, 'is_active' => $isActive]);

            if ($lesson->type !== Lesson::TYPE_QUIZ) {
                $lesson->update(['type' => Lesson::TYPE_QUIZ]);
            }

            return $quiz->refresh();
        });
    }

    /**
     * @param  array<string, mixed>  $metadata
     * @return array<string, mixed>
     */
    public function normalizeMetadata(array $metadata): array
    {
        return [
            'title' => trim((string) $metadata['title']),
            'description' => $this->nullableTrimmedString($metadata['description'] ?? null),
            'pass_score' => (int) $metadata['pass_score'],
            'time_limit_minutes' => $this->nullableInteger($metadata['time_limit_minutes'] ?? null),
            'max_attempts' => $this->nullableInteger($metadata['max_attempts'] ?? null),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createQuestion(Quiz $quiz, array $data, array $options = []): QuizQuestion
    {
        return DB::transaction(function () use ($quiz, $data, $options): QuizQuestion {
            $question = $quiz->questions()->create($this->questionAttributes($quiz, $data));

            if ($question->type === QuizQuestion::TYPE_TRUE_FALSE) {
                $this->normalizeTrueFalseOptions($question);
            }

            if ($options !== []) {
                $this->createOptions($question, $options);
            }

            return $question->load('options');
        });
    }

    /**
     * @param  array<int, array<string, mixed>>  $options
     */
    public function createOptions(QuizQuestion $question, array $options): void
    {
        DB::transaction(function () use ($question, $options): void {
            if ($question->type === QuizQuestion::TYPE_TRUE_FALSE) {
                $byIdentity = collect($options)->keyBy(
                    fn (array $option): string => mb_strtoupper(trim((string) ($option['identity'] ?? ''))),
                );

                if ($byIdentity->keys()->sort()->values()->all() !== ['FALSE', 'TRUE']
                    || $byIdentity->filter(fn (array $option): bool => (bool) ($option['is_correct'] ?? false))->count() !== 1) {
                    throw ValidationException::withMessages([
                        'options' => 'Đáp án Đúng/Sai phải có đúng hai identity TRUE/FALSE và đúng một đáp án đúng.',
                    ]);
                }

                $question->load('options');
                $correctIdentity = $byIdentity->first(
                    fn (array $option): bool => (bool) ($option['is_correct'] ?? false),
                );
                $correctIndex = mb_strtoupper(trim((string) ($correctIdentity['identity'] ?? ''))) === 'FALSE' ? 1 : 0;
                $this->normalizeTrueFalseOptions($question, $question->options->get($correctIndex)?->id);

                return;
            }

            foreach ($options as $option) {
                $question->unsetRelation('options');
                $this->createOption($question, [
                    'answer_text' => $option['option_text'] ?? '',
                    'is_correct' => $option['is_correct'] ?? false,
                    'sort_order' => $option['sort_order'] ?? null,
                ]);
            }
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateQuestion(QuizQuestion $question, array $data): QuizQuestion
    {
        return DB::transaction(function () use ($question, $data): QuizQuestion {
            $question->update($this->questionAttributes($question->quiz, $data, $question));

            if ($question->type === QuizQuestion::TYPE_TRUE_FALSE) {
                $this->normalizeTrueFalseOptions($question);
            } elseif ($question->type === QuizQuestion::TYPE_SINGLE) {
                $this->keepOnlyFirstCorrectOption($question);
            }

            return $question->refresh()->load('options');
        });
    }

    public function deleteQuestion(QuizQuestion $question): void
    {
        DB::transaction(fn () => $question->delete());
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createOption(QuizQuestion $question, array $data): QuizOption
    {
        $question->loadMissing('options');

        if ($question->type === QuizQuestion::TYPE_TRUE_FALSE) {
            throw ValidationException::withMessages([
                'answer_text' => 'Câu hỏi Đúng/Sai luôn có đúng hai đáp án cố định.',
            ]);
        }

        if ($question->type === QuizQuestion::TYPE_MULTIPLE && $question->options->count() >= self::MAX_MULTIPLE_OPTIONS) {
            throw ValidationException::withMessages([
                'answer_text' => 'Câu hỏi chọn nhiều chỉ được có tối đa 10 đáp án.',
            ]);
        }

        $text = trim((string) $data['answer_text']);
        $this->assertUniqueOptionText($question, $text);

        return DB::transaction(function () use ($question, $data, $text): QuizOption {
            $isCorrect = (bool) ($data['is_correct'] ?? false);

            if ($isCorrect && $question->type === QuizQuestion::TYPE_SINGLE) {
                $question->options()->update(['is_correct' => false]);
            }

            return $question->options()->create([
                'option_text' => $text,
                'is_correct' => $isCorrect,
                'sort_order' => $data['sort_order'] ?? $this->nextOptionSortOrder($question),
            ]);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateOption(QuizOption $option, array $data): QuizOption
    {
        $option->loadMissing('question.options');

        if ($option->question->type === QuizQuestion::TYPE_TRUE_FALSE) {
            $selectedCorrectId = (bool) ($data['is_correct'] ?? false)
                ? $option->id
                : $option->question->options->firstWhere('id', '!=', $option->id)?->id;
            $this->normalizeTrueFalseOptions($option->question, $selectedCorrectId);

            return $option->refresh();
        }

        $text = trim((string) $data['answer_text']);
        $this->assertUniqueOptionText($option->question, $text, $option->id);

        return DB::transaction(function () use ($option, $data, $text): QuizOption {
            $isCorrect = (bool) ($data['is_correct'] ?? false);

            if ($isCorrect && $option->question->type !== QuizQuestion::TYPE_MULTIPLE) {
                $option->question->options()->whereKeyNot($option->id)->update(['is_correct' => false]);
            }

            $option->update([
                'option_text' => $text,
                'is_correct' => $isCorrect,
                'sort_order' => $data['sort_order'] ?? $option->sort_order,
            ]);

            return $option->refresh();
        });
    }

    /**
     * @param  array<int|string, array<string, mixed>>  $answers
     * @param  array<int>  $deleteIds
     * @param  array<int>  $correctIds
     */
    public function updateOptions(
        QuizQuestion $question,
        array $answers,
        array $deleteIds,
        array $correctIds,
    ): void {
        $question->loadMissing('options');

        if ($question->type === QuizQuestion::TYPE_TRUE_FALSE) {
            $selectedCorrectId = collect($correctIds)
                ->map(fn (mixed $id): int => (int) $id)
                ->intersect($question->options->pluck('id')->map(fn (mixed $id): int => (int) $id))
                ->first();

            $this->normalizeTrueFalseOptions($question, $selectedCorrectId ? (int) $selectedCorrectId : null);

            return;
        }

        $ownedIds = $question->options->pluck('id')->map(fn (mixed $id): int => (int) $id)->all();
        $deleteIds = collect($deleteIds)->map(fn (mixed $id): int => (int) $id)->intersect($ownedIds)->values()->all();
        $remainingIds = array_values(array_diff($ownedIds, $deleteIds));
        $correctIds = collect($correctIds)->map(fn (mixed $id): int => (int) $id)->intersect($remainingIds)->values()->all();

        if ($question->type === QuizQuestion::TYPE_SINGLE && count($correctIds) > 1) {
            throw ValidationException::withMessages([
                'answers' => 'Câu hỏi một lựa chọn chỉ được có một đáp án đúng.',
            ]);
        }

        $texts = [];
        $updates = [];

        foreach ($remainingIds as $answerId) {
            $answerData = $answers[$answerId] ?? $answers[(string) $answerId] ?? null;
            $existing = $question->options->firstWhere('id', $answerId);
            $text = trim((string) ($answerData['answer_text'] ?? $existing?->option_text));
            $key = $this->comparisonKey($text);

            if ($key === '' || isset($texts[$key])) {
                throw ValidationException::withMessages([
                    'answers' => $key === ''
                        ? 'Nội dung đáp án không được để trống.'
                        : 'Các đáp án trong cùng câu hỏi không được trùng nội dung.',
                ]);
            }

            $texts[$key] = true;
            $updates[$answerId] = [
                'option_text' => $text,
                'sort_order' => (int) ($answerData['sort_order'] ?? $existing?->sort_order ?? 0),
                'is_correct' => in_array($answerId, $correctIds, true),
            ];
        }

        DB::transaction(function () use ($question, $deleteIds, $updates): void {
            if ($deleteIds !== []) {
                $question->options()->whereIn('id', $deleteIds)->delete();
            }

            foreach ($updates as $answerId => $attributes) {
                $question->options()->whereKey($answerId)->update($attributes);
            }
        });
    }

    public function deleteOption(QuizOption $option): void
    {
        DB::transaction(fn () => $option->delete());
    }

    /**
     * @return array{is_complete: bool, errors: array<int, string>, warnings: array<int, string>}
     */
    public function validateQuiz(Quiz $quiz): array
    {
        $quiz->loadMissing('questions.options');
        $errors = [];
        $warnings = [];

        if ($quiz->questions->count() < self::MIN_QUESTIONS) {
            $errors[] = sprintf(
                "Quiz '%s' chưa đủ %d câu hỏi (hiện có %d).",
                $quiz->title,
                self::MIN_QUESTIONS,
                $quiz->questions->count(),
            );
        }

        if ((int) $quiz->pass_score < 0 || (int) $quiz->pass_score > 100) {
            $errors[] = "Quiz '{$quiz->title}' phải có điểm đạt từ 0 đến 100%.";
        }

        if ($quiz->time_limit_minutes !== null
            && ((int) $quiz->time_limit_minutes < 1 || (int) $quiz->time_limit_minutes > 1440)) {
            $errors[] = "Quiz '{$quiz->title}' có thời gian làm bài không hợp lệ.";
        }

        if ($quiz->max_attempts !== null
            && ((int) $quiz->max_attempts < 1 || (int) $quiz->max_attempts > 100)) {
            $errors[] = "Quiz '{$quiz->title}' có số lần làm tối đa không hợp lệ.";
        }

        $questionTexts = [];

        foreach ($quiz->questions as $question) {
            $result = $this->validateQuestion($question);
            array_push($errors, ...$result['errors']);
            array_push($warnings, ...$result['warnings']);

            $key = $this->comparisonKey($question->question);

            if ($key !== '' && isset($questionTexts[$key])) {
                $warnings[] = "Câu hỏi '{$question->question}' bị trùng nội dung trong cùng quiz.";
            }

            $questionTexts[$key] = true;
        }

        return [
            'is_complete' => $errors === [],
            'errors' => array_values(array_unique($errors)),
            'warnings' => array_values(array_unique($warnings)),
        ];
    }

    /**
     * @return array{is_complete: bool, errors: array<int, string>, warnings: array<int, string>}
     */
    public function validateQuestion(QuizQuestion $question): array
    {
        $question->loadMissing('options');
        $errors = [];
        $warnings = [];
        $label = trim($question->question) !== '' ? $question->question : 'Câu hỏi chưa có nội dung';
        $optionCount = $question->options->count();
        $correctCount = $question->options->where('is_correct', true)->count();

        if ((int) $question->points < 1 || (int) $question->points > 1000) {
            $errors[] = "Câu hỏi '{$label}' phải có điểm từ 1 đến 1000.";
        }

        $keys = $question->options->map(fn (QuizOption $option): string => $this->comparisonKey($option->option_text));

        if ($keys->contains('')) {
            $errors[] = "Câu hỏi '{$label}' có đáp án trống.";
        }

        if ($keys->filter()->duplicates()->isNotEmpty()) {
            $errors[] = "Câu hỏi '{$label}' có đáp án trùng nội dung.";
        }

        if ($question->type === QuizQuestion::TYPE_SINGLE) {
            if ($optionCount < self::MIN_OPTIONS) {
                $errors[] = "Câu hỏi '{$label}' chưa có đủ 3 đáp án.";
            }

            if ($correctCount !== 1) {
                $errors[] = "Câu hỏi '{$label}' phải có đúng 1 đáp án đúng.";
            }
        } elseif ($question->type === QuizQuestion::TYPE_MULTIPLE) {
            if ($optionCount < self::MIN_OPTIONS) {
                $errors[] = "Câu hỏi '{$label}' chưa có đủ 3 đáp án.";
            }

            if ($optionCount > self::MAX_MULTIPLE_OPTIONS) {
                $errors[] = "Câu hỏi '{$label}' vượt quá 10 đáp án.";
            }

            if ($correctCount < 1) {
                $errors[] = "Câu hỏi '{$label}' cần ít nhất 1 đáp án đúng.";
            } elseif ($optionCount > 0 && $correctCount === $optionCount) {
                $warnings[] = "Câu hỏi '{$label}' đang đánh dấu tất cả đáp án là đúng.";
            }
        } elseif ($question->type === QuizQuestion::TYPE_TRUE_FALSE) {
            if ($optionCount !== 2) {
                $errors[] = "Câu hỏi '{$label}' phải có đúng 2 đáp án Đúng/Sai.";
            }

            if ($correctCount !== 1) {
                $errors[] = "Câu hỏi '{$label}' phải có đúng 1 đáp án đúng.";
            }

            $sortOrders = $question->options->pluck('sort_order')->map(fn (mixed $value): int => (int) $value)->values()->all();

            if ($optionCount === 2 && $sortOrders !== [0, 1]) {
                $errors[] = "Câu hỏi '{$label}' có thứ tự đáp án Đúng/Sai không hợp lệ.";
            }
        } else {
            $errors[] = "Câu hỏi '{$label}' có loại không hợp lệ.";
        }

        return [
            'is_complete' => $errors === [],
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }

    public function isEffectivelyActive(Quiz $quiz): bool
    {
        return $quiz->is_active && $this->validateQuiz($quiz)['is_complete'];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function questionAttributes(Quiz $quiz, array $data, ?QuizQuestion $question = null): array
    {
        $sortOrder = $data['sort_order'] ?? null;

        if ($sortOrder === null) {
            $sortOrder = $question?->sort_order;

            if ($sortOrder === null) {
                $currentMax = $quiz->questions()->max('sort_order');
                $sortOrder = $currentMax === null ? 0 : ((int) $currentMax + 1);
            }
        }

        return [
            'question' => trim((string) $data['question_text']),
            'type' => $this->canonicalType((string) $data['question_type']),
            'points' => (int) $data['score'],
            'explanation' => $this->nullableTrimmedString($data['explanation'] ?? null),
            'sort_order' => (int) $sortOrder,
        ];
    }

    private function normalizeTrueFalseOptions(QuizQuestion $question, ?int $selectedCorrectId = null): void
    {
        DB::transaction(function () use ($question, $selectedCorrectId): void {
            $options = $question->options()->orderBy('sort_order')->orderBy('id')->lockForUpdate()->get();
            $correctIndex = 0;

            if ($selectedCorrectId !== null) {
                $selectedIndex = $options->search(fn (QuizOption $option): bool => $option->id === $selectedCorrectId);
                $correctIndex = in_array($selectedIndex, [0, 1], true) ? $selectedIndex : 0;
            } elseif ($options->where('is_correct', true)->count() === 1) {
                $existingCorrectId = $options->firstWhere('is_correct', true)?->id;
                $existingCorrectIndex = $options->search(fn (QuizOption $option): bool => $option->id === $existingCorrectId);
                $correctIndex = in_array($existingCorrectIndex, [0, 1], true) ? $existingCorrectIndex : 0;
            }

            $canonical = [
                ['option_text' => 'Đúng', 'sort_order' => 0],
                ['option_text' => 'Sai', 'sort_order' => 1],
            ];

            foreach ($canonical as $index => $attributes) {
                $option = $options->get($index);
                $values = [...$attributes, 'is_correct' => $index === $correctIndex];

                if ($option) {
                    $option->update($values);
                } else {
                    $question->options()->create($values);
                }
            }

            $staleIds = $options->slice(2)->pluck('id');

            if ($staleIds->isNotEmpty()) {
                $question->options()->whereIn('id', $staleIds)->delete();
            }
        });
    }

    private function keepOnlyFirstCorrectOption(QuizQuestion $question): void
    {
        $correctOption = $question->options()
            ->where('is_correct', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();

        if ($correctOption) {
            $question->options()->whereKeyNot($correctOption->id)->update(['is_correct' => false]);
        }
    }

    private function assertUniqueOptionText(QuizQuestion $question, string $text, ?int $ignoreOptionId = null): void
    {
        $key = $this->comparisonKey($text);
        $duplicate = $question->options
            ->when($ignoreOptionId, fn ($options) => $options->where('id', '!=', $ignoreOptionId))
            ->contains(fn (QuizOption $option): bool => $this->comparisonKey($option->option_text) === $key);

        if ($key === '' || $duplicate) {
            throw ValidationException::withMessages([
                'answer_text' => $key === ''
                    ? 'Nội dung đáp án không được để trống.'
                    : 'Đáp án này bị trùng nội dung với một đáp án khác trong cùng câu hỏi.',
            ]);
        }
    }

    private function nextOptionSortOrder(QuizQuestion $question): int
    {
        return ((int) $question->options()->max('sort_order')) + 1;
    }

    private function comparisonKey(string $value): string
    {
        return mb_strtolower(trim((string) preg_replace('/\s+/u', ' ', $value)));
    }

    private function nullableTrimmedString(mixed $value): ?string
    {
        $value = trim((string) ($value ?? ''));

        return $value === '' ? null : $value;
    }

    private function nullableInteger(mixed $value): ?int
    {
        return $value === null || $value === '' ? null : (int) $value;
    }
}
