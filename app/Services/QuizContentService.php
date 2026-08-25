<?php

namespace App\Services;

use App\Models\Lesson;
use App\Models\QuestionVersion;
use App\Models\Quiz;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\QuizVersion;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Collection;
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
            $quiz = $lesson->quiz()->firstOrCreate([], [
                'title' => $lesson->title,
                'pass_score' => 70,
                'time_limit_minutes' => null,
                'max_attempts' => null,
                'is_active' => false,
            ]);

            if (! $quiz->current_published_version_id && ! $quiz->current_draft_version_id) {
                app(QuizVersioningService::class)->ensureInitialDraft($quiz, auth()->user());
            }

            return $quiz->refresh();
        } catch (UniqueConstraintViolationException $exception) {
            $quiz = $lesson->quiz()->first();

            if ($quiz) {
                if (! $quiz->current_published_version_id && ! $quiz->current_draft_version_id) {
                    app(QuizVersioningService::class)->ensureInitialDraft($quiz, auth()->user());
                }

                return $quiz->refresh();
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
            $versioning = app(QuizVersioningService::class);
            $draft = $versioning->ensureDraft($quiz, auth()->user());
            $versioning->assertDraftEditable($quiz, $draft);
            $draft->update($normalized);

            if (! $quiz->current_published_version_id) {
                $quiz->update([...$normalized, 'is_active' => $isActive]);
            } else {
                $versioning->recordCandidateUpdate($quiz->fresh(), $draft, $isActive);
            }

            if ($isActive && ! $this->validateQuizVersion($draft->fresh())['is_complete']) {
                throw ValidationException::withMessages([
                    'quiz' => 'Quiz chỉ có thể bật khi bản nháp hiện tại đã hoàn chỉnh.',
                ]);
            }

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
        if ($this->usesVersionedAuthoring($quiz)) {
            return $this->createVersionedQuestion($quiz, $data, $options);
        }

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
        $question->loadMissing('quiz');

        if ($this->usesVersionedAuthoring($question->quiz)) {
            DB::transaction(function () use ($question, $options): void {
                $mutable = app(QuizVersioningService::class)->ensureMutableQuestionVersion($question->quiz, $question);
                $version = $mutable['version'];

                if ($version->type === QuizQuestion::TYPE_TRUE_FALSE) {
                    $byIdentity = collect($options)->keyBy(
                        fn (array $option): string => mb_strtoupper(trim((string) ($option['identity'] ?? ''))),
                    );

                    if ($byIdentity->keys()->sort()->values()->all() !== ['FALSE', 'TRUE']
                        || $byIdentity->filter(fn (array $option): bool => (bool) ($option['is_correct'] ?? false))->count() !== 1) {
                        throw ValidationException::withMessages([
                            'options' => 'Đáp án Đúng/Sai phải có đúng hai identity TRUE/FALSE và đúng một đáp án đúng.',
                        ]);
                    }

                    $correct = mb_strtoupper(trim((string) data_get(
                        $byIdentity->first(fn (array $option): bool => (bool) ($option['is_correct'] ?? false)),
                        'identity',
                    ))) === 'FALSE' ? 1 : 0;
                    $this->normalizeTrueFalseVersionOptions($version, null, $correct);

                    return;
                }

                foreach ($options as $option) {
                    $version->unsetRelation('options');
                    $this->createVersionOption($question, $version, [
                        'answer_text' => $option['option_text'] ?? '',
                        'is_correct' => $option['is_correct'] ?? false,
                        'sort_order' => $option['sort_order'] ?? null,
                    ]);
                }

                app(QuizVersioningService::class)->recordCandidateUpdate(
                    $question->quiz->fresh(),
                    app(QuizVersioningService::class)->currentDraft($question->quiz->fresh()),
                );
            });

            return;
        }

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
        $question->loadMissing('quiz');

        if ($this->usesVersionedAuthoring($question->quiz)) {
            return $this->updateVersionedQuestion($question, $data);
        }

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
        $question->loadMissing('quiz');

        if ($this->usesVersionedAuthoring($question->quiz)) {
            DB::transaction(function () use ($question): void {
                $versioning = app(QuizVersioningService::class);
                $draft = $versioning->ensureDraft($question->quiz, auth()->user());
                $versioning->assertDraftEditable($question->quiz, $draft);
                $mapping = $versioning->mappingForQuestion($draft, $question);
                $mapping->delete();
                $this->normalizeCompositionOrder($draft);
                $versioning->recordCandidateUpdate($question->quiz->fresh(), $draft);
            });

            return;
        }

        DB::transaction(fn () => $question->delete());
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createOption(QuizQuestion $question, array $data): QuizOption
    {
        $question->loadMissing('quiz');

        if ($this->usesVersionedAuthoring($question->quiz)) {
            return DB::transaction(function () use ($question, $data): QuizOption {
                $versioning = app(QuizVersioningService::class);
                $mutable = $versioning->ensureMutableQuestionVersion($question->quiz, $question);
                $option = $this->createVersionOption($question, $mutable['version'], $data);
                $versioning->recordCandidateUpdate(
                    $question->quiz->fresh(),
                    $versioning->currentDraft($question->quiz->fresh()),
                );

                return $option;
            });
        }

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
        $option->loadMissing('question.quiz');

        if ($this->usesVersionedAuthoring($option->question->quiz)) {
            return $this->updateVersionedOption($option, $data);
        }

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
        $question->loadMissing('quiz');

        if ($this->usesVersionedAuthoring($question->quiz)) {
            $this->updateVersionedOptions($question, $answers, $deleteIds, $correctIds);

            return;
        }

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
        $option->loadMissing('question.quiz');

        if ($this->usesVersionedAuthoring($option->question->quiz)) {
            DB::transaction(function () use ($option): void {
                $question = $option->question;
                $versioning = app(QuizVersioningService::class);
                $draft = $versioning->ensureDraft($question->quiz, auth()->user());
                $source = $versioning->mappingForQuestion($draft, $question)->questionVersion()->with('options')->firstOrFail();
                $this->assertOptionBelongsToVersion($option, $source);
                $mutable = $versioning->ensureMutableQuestionVersion($question->quiz, $question);
                $targetId = $mutable['option_map'][$option->id] ?? null;

                if (! $targetId) {
                    throw ValidationException::withMessages(['answer_text' => 'Đáp án không thuộc bản nháp hiện tại.']);
                }

                $mutable['version']->options()->whereKey($targetId)->delete();
                $versioning->recordCandidateUpdate($question->quiz->fresh(), $draft);
            });

            return;
        }

        DB::transaction(fn () => $option->delete());
    }

    /**
     * @return array{is_complete: bool, errors: array<int, string>, warnings: array<int, string>}
     */
    public function validateQuiz(Quiz $quiz): array
    {
        $version = app(QuizVersioningService::class)->candidateVersion($quiz);

        return $version
            ? $this->validateQuizVersion($version)
            : [
                'is_complete' => false,
                'errors' => ["Quiz '{$quiz->title}' chưa có phiên bản nội dung hợp lệ."],
                'warnings' => [],
            ];
    }

    /**
     * @return array{is_complete: bool, errors: array<int, string>, warnings: array<int, string>}
     */
    public function validateQuizVersion(QuizVersion $version): array
    {
        $version->loadMissing('questionMappings.questionVersion.options');
        $errors = [];
        $warnings = [];

        if ($version->questionMappings->count() < self::MIN_QUESTIONS) {
            $errors[] = sprintf(
                "Quiz '%s' chưa đủ %d câu hỏi (hiện có %d).",
                $version->title,
                self::MIN_QUESTIONS,
                $version->questionMappings->count(),
            );
        }

        if ((int) $version->pass_score < 0 || (int) $version->pass_score > 100) {
            $errors[] = "Quiz '{$version->title}' phải có điểm đạt từ 0 đến 100%.";
        }

        if ($version->time_limit_minutes !== null
            && ((int) $version->time_limit_minutes < 1 || (int) $version->time_limit_minutes > 1440)) {
            $errors[] = "Quiz '{$version->title}' có thời gian làm bài không hợp lệ.";
        }

        if ($version->max_attempts !== null
            && ((int) $version->max_attempts < 1 || (int) $version->max_attempts > 100)) {
            $errors[] = "Quiz '{$version->title}' có số lần làm tối đa không hợp lệ.";
        }

        $questionTexts = [];

        foreach ($version->questionMappings as $mapping) {
            $questionVersion = $mapping->questionVersion;
            $result = $this->validateQuestionVersion($questionVersion);
            array_push($errors, ...$result['errors']);
            array_push($warnings, ...$result['warnings']);
            $key = $this->comparisonKey($questionVersion->question);

            if ($key !== '' && isset($questionTexts[$key])) {
                $warnings[] = "Câu hỏi '{$questionVersion->question}' bị trùng nội dung trong cùng quiz.";
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

        return $this->validateQuestionContent(
            $question->question,
            $question->type,
            (int) $question->points,
            $question->options,
        );
    }

    /**
     * @return array{is_complete: bool, errors: array<int, string>, warnings: array<int, string>}
     */
    public function validateQuestionVersion(QuestionVersion $version): array
    {
        $version->loadMissing('options');

        return $this->validateQuestionContent(
            $version->question,
            $version->type,
            (int) $version->points,
            $version->options,
        );
    }

    public function isEffectivelyActive(Quiz $quiz): bool
    {
        if (! $quiz->is_active || ! $quiz->current_published_version_id) {
            return false;
        }

        $published = app(QuizVersioningService::class)->currentPublished($quiz);

        return $published->status === QuizVersion::STATUS_PUBLISHED
            && $this->validateQuizVersion($published)['is_complete'];
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, array<string, mixed>>  $options
     */
    private function createVersionedQuestion(Quiz $quiz, array $data, array $options): QuizQuestion
    {
        return DB::transaction(function () use ($quiz, $data, $options): QuizQuestion {
            $versioning = app(QuizVersioningService::class);
            $quiz = Quiz::query()->lockForUpdate()->findOrFail($quiz->id);
            $draft = $versioning->ensureDraft($quiz, auth()->user());
            $versioning->assertDraftEditable($quiz, $draft);
            $currentMaxOrder = $draft->questionMappings()->max('sort_order');
            $appendOrder = $currentMaxOrder === null ? 0 : ((int) $currentMaxOrder + 1);
            $requestedOrder = array_key_exists('sort_order', $data) && $data['sort_order'] !== null
                ? max(0, (int) $data['sort_order'])
                : $appendOrder;
            $attributes = $this->questionVersionAttributes($data);
            $question = $quiz->questions()->create([
                ...$attributes,
                'sort_order' => $appendOrder,
            ]);
            $questionVersion = $question->versions()->create([
                ...$attributes,
                'version' => 1,
                'status' => QuestionVersion::STATUS_DRAFT,
            ]);
            $mapping = $draft->questionMappings()->create([
                'question_id' => $question->id,
                'question_version_id' => $questionVersion->id,
                'sort_order' => $appendOrder,
            ]);

            if ($questionVersion->type === QuizQuestion::TYPE_TRUE_FALSE) {
                $this->normalizeTrueFalseVersionOptions($questionVersion);

                if ($options !== []) {
                    $byIdentity = collect($options)->keyBy(
                        fn (array $option): string => mb_strtoupper(trim((string) ($option['identity'] ?? ''))),
                    );

                    if ($byIdentity->keys()->sort()->values()->all() !== ['FALSE', 'TRUE']
                        || $byIdentity->filter(fn (array $option): bool => (bool) ($option['is_correct'] ?? false))->count() !== 1) {
                        throw ValidationException::withMessages([
                            'options' => 'Đáp án Đúng/Sai phải có đúng hai identity TRUE/FALSE và đúng một đáp án đúng.',
                        ]);
                    }

                    $correctIndex = mb_strtoupper(trim((string) data_get(
                        $byIdentity->first(fn (array $option): bool => (bool) ($option['is_correct'] ?? false)),
                        'identity',
                    ))) === 'FALSE' ? 1 : 0;
                    $this->normalizeTrueFalseVersionOptions($questionVersion, null, $correctIndex);
                }
            } else {
                foreach ($options as $option) {
                    $this->createVersionOption($question, $questionVersion, [
                        'answer_text' => $option['option_text'] ?? '',
                        'is_correct' => $option['is_correct'] ?? false,
                        'sort_order' => $option['sort_order'] ?? null,
                    ]);
                }
            }

            if ($requestedOrder !== $appendOrder) {
                $versioning->moveQuestion($draft, $question, $requestedOrder);
                $mapping = $mapping->fresh();
            }

            $versioning->recordCandidateUpdate($quiz->fresh(), $draft);

            return $this->projectQuestion($question->fresh(), $questionVersion->fresh('options'), $mapping->sort_order);
        });
    }

    /** @param array<string, mixed> $data */
    private function updateVersionedQuestion(QuizQuestion $question, array $data): QuizQuestion
    {
        return DB::transaction(function () use ($question, $data): QuizQuestion {
            $versioning = app(QuizVersioningService::class);
            $quiz = Quiz::query()->lockForUpdate()->findOrFail($question->quiz_id);
            $draft = $versioning->ensureDraft($quiz, auth()->user());
            $versioning->assertDraftEditable($quiz, $draft);
            $mapping = $versioning->mappingForQuestion($draft, $question);
            $source = $mapping->questionVersion()->with('options')->firstOrFail();
            $attributes = $this->questionVersionAttributes($data);
            $semanticChanged = collect($attributes)->contains(
                fn (mixed $value, string $key): bool => $source->getAttribute($key) !== $value,
            );
            $target = $source;

            if ($semanticChanged) {
                $mutable = $versioning->ensureMutableQuestionVersion($quiz, $question);
                $target = $mutable['version'];
                $target->update($attributes);

                if ($target->type === QuizQuestion::TYPE_TRUE_FALSE) {
                    $this->normalizeTrueFalseVersionOptions($target);
                } elseif ($target->type === QuizQuestion::TYPE_SINGLE) {
                    $this->keepOnlyFirstCorrectVersionOption($target);
                }

                if (! $quiz->current_published_version_id) {
                    $question->update($attributes);
                }
            }

            if (array_key_exists('sort_order', $data) && $data['sort_order'] !== null
                && (int) $data['sort_order'] !== (int) $mapping->sort_order) {
                $versioning->moveQuestion($draft, $question, (int) $data['sort_order']);
                $mapping = $mapping->fresh();

                if (! $quiz->current_published_version_id) {
                    $question->update(['sort_order' => $mapping->sort_order]);
                }
            }

            $versioning->recordCandidateUpdate($quiz->fresh(), $draft);

            return $this->projectQuestion($question->fresh(), $target->fresh('options'), $mapping->sort_order);
        });
    }

    /** @param array<string, mixed> $data */
    private function updateVersionedOption(QuizOption $option, array $data): QuizOption
    {
        return DB::transaction(function () use ($option, $data): QuizOption {
            $question = $option->question;
            $quiz = $question->quiz;
            $versioning = app(QuizVersioningService::class);
            $draft = $versioning->ensureDraft($quiz, auth()->user());
            $source = $versioning->mappingForQuestion($draft, $question)->questionVersion()->with('options')->firstOrFail();
            $this->assertOptionBelongsToVersion($option, $source);
            $mutable = $versioning->ensureMutableQuestionVersion($quiz, $question);
            $targetId = $mutable['option_map'][$option->id] ?? null;

            if (! $targetId) {
                throw ValidationException::withMessages(['answer_text' => 'Đáp án không thuộc bản nháp hiện tại.']);
            }

            $version = $mutable['version']->fresh('options');
            $target = $version->options->firstWhere('id', $targetId);

            if (! $target) {
                throw ValidationException::withMessages(['answer_text' => 'Không tìm thấy bản sao đáp án có thể chỉnh sửa.']);
            }

            if ($version->type === QuizQuestion::TYPE_TRUE_FALSE) {
                $selectedId = (bool) ($data['is_correct'] ?? false)
                    ? $target->id
                    : $version->options->firstWhere('id', '!=', $target->id)?->id;
                $this->normalizeTrueFalseVersionOptions($version, $selectedId);
                $target = $target->fresh();
            } else {
                $text = trim((string) $data['answer_text']);
                $this->assertUniqueVersionOptionText($version, $text, $target->id);
                $isCorrect = (bool) ($data['is_correct'] ?? false);

                if ($isCorrect && $version->type !== QuizQuestion::TYPE_MULTIPLE) {
                    $version->options()->whereKeyNot($target->id)->update(['is_correct' => false]);
                }

                $target->update([
                    'option_text' => $text,
                    'is_correct' => $isCorrect,
                    'sort_order' => $data['sort_order'] ?? $target->sort_order,
                ]);
            }

            $versioning->recordCandidateUpdate($quiz->fresh(), $draft);

            return $target->fresh();
        });
    }

    /**
     * @param  array<int|string, array<string, mixed>>  $answers
     * @param  array<int>  $deleteIds
     * @param  array<int>  $correctIds
     */
    private function updateVersionedOptions(
        QuizQuestion $question,
        array $answers,
        array $deleteIds,
        array $correctIds,
    ): void {
        DB::transaction(function () use ($question, $answers, $deleteIds, $correctIds): void {
            $quiz = $question->quiz;
            $versioning = app(QuizVersioningService::class);
            $draft = $versioning->ensureDraft($quiz, auth()->user());
            $source = $versioning->mappingForQuestion($draft, $question)->questionVersion()->with('options')->firstOrFail();
            $ownedIds = $source->options->pluck('id')->map(fn (mixed $id): int => (int) $id)->all();
            $answerIds = collect(array_keys($answers))->map(fn (mixed $id): int => (int) $id)->all();
            $requestedIds = collect([...$answerIds, ...$deleteIds, ...$correctIds])
                ->map(fn (mixed $id): int => (int) $id)
                ->filter()
                ->unique();

            if ($requestedIds->diff($ownedIds)->isNotEmpty()) {
                throw ValidationException::withMessages(['answers' => 'Có đáp án không thuộc phiên bản câu hỏi hiện tại.']);
            }

            $mutable = $versioning->ensureMutableQuestionVersion($quiz, $question);
            $version = $mutable['version']->fresh('options');
            $map = $mutable['option_map'];
            $translatedAnswers = [];

            foreach ($answers as $sourceId => $answerData) {
                $translatedId = $map[(int) $sourceId] ?? null;

                if ($translatedId) {
                    $translatedAnswers[$translatedId] = $answerData;
                }
            }

            $translatedDeleteIds = collect($deleteIds)->map(fn (mixed $id) => $map[(int) $id] ?? null)->filter()->values()->all();
            $translatedCorrectIds = collect($correctIds)->map(fn (mixed $id) => $map[(int) $id] ?? null)->filter()->values()->all();

            if ($version->type === QuizQuestion::TYPE_TRUE_FALSE) {
                $selectedId = collect($translatedCorrectIds)->intersect($version->options->pluck('id'))->first();
                $this->normalizeTrueFalseVersionOptions($version, $selectedId ? (int) $selectedId : null);
                $versioning->recordCandidateUpdate($quiz->fresh(), $draft);

                return;
            }

            $currentIds = $version->options->pluck('id')->map(fn (mixed $id): int => (int) $id)->all();
            $remainingIds = array_values(array_diff($currentIds, $translatedDeleteIds));
            $translatedCorrectIds = collect($translatedCorrectIds)->intersect($remainingIds)->values()->all();

            if ($version->type === QuizQuestion::TYPE_SINGLE && count($translatedCorrectIds) > 1) {
                throw ValidationException::withMessages(['answers' => 'Câu hỏi một lựa chọn chỉ được có một đáp án đúng.']);
            }

            $texts = [];
            $updates = [];

            foreach ($remainingIds as $answerId) {
                $answerData = $translatedAnswers[$answerId] ?? null;
                $existing = $version->options->firstWhere('id', $answerId);
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
                    'is_correct' => in_array($answerId, $translatedCorrectIds, true),
                ];
            }

            if ($translatedDeleteIds !== []) {
                $version->options()->whereIn('id', $translatedDeleteIds)->delete();
            }

            foreach ($updates as $answerId => $attributes) {
                $version->options()->whereKey($answerId)->update($attributes);
            }

            $versioning->recordCandidateUpdate($quiz->fresh(), $draft);
        });
    }

    /** @param array<string, mixed> $data */
    private function createVersionOption(QuizQuestion $question, QuestionVersion $version, array $data): QuizOption
    {
        $version->loadMissing('options');

        if ($version->type === QuizQuestion::TYPE_TRUE_FALSE) {
            throw ValidationException::withMessages(['answer_text' => 'Câu hỏi Đúng/Sai luôn có đúng hai đáp án cố định.']);
        }

        if ($version->type === QuizQuestion::TYPE_MULTIPLE && $version->options->count() >= self::MAX_MULTIPLE_OPTIONS) {
            throw ValidationException::withMessages(['answer_text' => 'Câu hỏi chọn nhiều chỉ được có tối đa 10 đáp án.']);
        }

        $text = trim((string) $data['answer_text']);
        $this->assertUniqueVersionOptionText($version, $text);
        $isCorrect = (bool) ($data['is_correct'] ?? false);

        if ($isCorrect && $version->type === QuizQuestion::TYPE_SINGLE) {
            $version->options()->update(['is_correct' => false]);
        }

        return $version->options()->create([
            'quiz_question_id' => $question->id,
            'option_text' => $text,
            'is_correct' => $isCorrect,
            'sort_order' => $data['sort_order'] ?? $this->nextVersionOptionSortOrder($version),
        ]);
    }

    private function normalizeTrueFalseVersionOptions(
        QuestionVersion $version,
        ?int $selectedCorrectId = null,
        ?int $forcedCorrectIndex = null,
    ): void {
        $options = $version->options()->orderBy('sort_order')->orderBy('id')->lockForUpdate()->get();
        $correctIndex = $forcedCorrectIndex ?? 0;

        if ($forcedCorrectIndex === null && $selectedCorrectId !== null) {
            $selectedIndex = $options->search(fn (QuizOption $option): bool => $option->id === $selectedCorrectId);
            $correctIndex = in_array($selectedIndex, [0, 1], true) ? $selectedIndex : 0;
        } elseif ($forcedCorrectIndex === null && $options->where('is_correct', true)->count() === 1) {
            $existingCorrectId = $options->firstWhere('is_correct', true)?->id;
            $existingIndex = $options->search(fn (QuizOption $option): bool => $option->id === $existingCorrectId);
            $correctIndex = in_array($existingIndex, [0, 1], true) ? $existingIndex : 0;
        }

        foreach ([['Đúng', 0], ['Sai', 1]] as $index => [$text, $sortOrder]) {
            $option = $options->get($index);
            $values = [
                'quiz_question_id' => $version->question_id,
                'option_text' => $text,
                'sort_order' => $sortOrder,
                'is_correct' => $index === $correctIndex,
            ];

            if ($option) {
                $option->update($values);
            } else {
                $version->options()->create($values);
            }
        }

        $staleIds = $options->slice(2)->pluck('id');

        if ($staleIds->isNotEmpty()) {
            $version->options()->whereIn('id', $staleIds)->delete();
        }
    }

    private function keepOnlyFirstCorrectVersionOption(QuestionVersion $version): void
    {
        $correct = $version->options()->where('is_correct', true)->orderBy('sort_order')->orderBy('id')->first();

        if ($correct) {
            $version->options()->whereKeyNot($correct->id)->update(['is_correct' => false]);
        }
    }

    private function assertUniqueVersionOptionText(QuestionVersion $version, string $text, ?int $ignoreId = null): void
    {
        $key = $this->comparisonKey($text);
        $options = $version->options()->get();
        $duplicate = $options
            ->when($ignoreId, fn ($items) => $items->where('id', '!=', $ignoreId))
            ->contains(fn (QuizOption $option): bool => $this->comparisonKey($option->option_text) === $key);

        if ($key === '' || $duplicate) {
            throw ValidationException::withMessages([
                'answer_text' => $key === ''
                    ? 'Nội dung đáp án không được để trống.'
                    : 'Đáp án này bị trùng nội dung với một đáp án khác trong cùng câu hỏi.',
            ]);
        }
    }

    private function assertOptionBelongsToVersion(QuizOption $option, QuestionVersion $version): void
    {
        if ((int) $option->question_version_id !== (int) $version->id
            || (int) $option->quiz_question_id !== (int) $version->question_id) {
            throw ValidationException::withMessages(['answer_text' => 'Đáp án không thuộc phiên bản câu hỏi hiện tại.']);
        }
    }

    private function normalizeCompositionOrder(QuizVersion $draft): void
    {
        $mappings = $draft->questionMappings()->lockForUpdate()->get();
        $offset = ((int) $mappings->max('sort_order')) + $mappings->count() + 1000;

        foreach ($mappings as $index => $mapping) {
            $mapping->update(['sort_order' => $offset + $index]);
        }

        foreach ($mappings as $index => $mapping) {
            $mapping->update(['sort_order' => $index]);
        }
    }

    private function projectQuestion(QuizQuestion $question, QuestionVersion $version, int $sortOrder): QuizQuestion
    {
        $projected = clone $question;
        $projected->setAttribute('question', $version->question);
        $projected->setAttribute('type', $version->type);
        $projected->setAttribute('points', $version->points);
        $projected->setAttribute('explanation', $version->explanation);
        $projected->setAttribute('sort_order', $sortOrder);
        $projected->setRelation('options', $version->options);
        $projected->setRelation('authoringVersion', $version);

        return $projected;
    }

    /** @param array<string, mixed> $data */
    private function questionVersionAttributes(array $data): array
    {
        return [
            'question' => trim((string) $data['question_text']),
            'type' => $this->canonicalType((string) $data['question_type']),
            'points' => (int) $data['score'],
            'explanation' => $this->nullableTrimmedString($data['explanation'] ?? null),
        ];
    }

    private function usesVersionedAuthoring(Quiz $quiz): bool
    {
        return $quiz->current_draft_version_id !== null || $quiz->current_published_version_id !== null;
    }

    private function nextVersionOptionSortOrder(QuestionVersion $version): int
    {
        $currentMax = $version->options()->max('sort_order');

        return $currentMax === null ? 0 : ((int) $currentMax + 1);
    }

    /**
     * @param  Collection<int, QuizOption>  $options
     * @return array{is_complete: bool, errors: array<int, string>, warnings: array<int, string>}
     */
    private function validateQuestionContent(string $question, string $type, int $points, $options): array
    {
        $errors = [];
        $warnings = [];
        $label = trim($question) !== '' ? $question : 'Câu hỏi chưa có nội dung';
        $optionCount = $options->count();
        $correctCount = $options->where('is_correct', true)->count();

        if ($points < 1 || $points > 1000) {
            $errors[] = "Câu hỏi '{$label}' phải có điểm từ 1 đến 1000.";
        }

        $keys = $options->map(fn (QuizOption $option): string => $this->comparisonKey($option->option_text));

        if ($keys->contains('')) {
            $errors[] = "Câu hỏi '{$label}' có đáp án trống.";
        }

        if ($keys->filter()->duplicates()->isNotEmpty()) {
            $errors[] = "Câu hỏi '{$label}' có đáp án trùng nội dung.";
        }

        if ($type === QuizQuestion::TYPE_SINGLE) {
            if ($optionCount < self::MIN_OPTIONS) {
                $errors[] = "Câu hỏi '{$label}' chưa có đủ 3 đáp án.";
            }

            if ($correctCount !== 1) {
                $errors[] = "Câu hỏi '{$label}' phải có đúng 1 đáp án đúng.";
            }
        } elseif ($type === QuizQuestion::TYPE_MULTIPLE) {
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
        } elseif ($type === QuizQuestion::TYPE_TRUE_FALSE) {
            if ($optionCount !== 2) {
                $errors[] = "Câu hỏi '{$label}' phải có đúng 2 đáp án Đúng/Sai.";
            }

            if ($correctCount !== 1) {
                $errors[] = "Câu hỏi '{$label}' phải có đúng 1 đáp án đúng.";
            }

            $sortOrders = $options->pluck('sort_order')->map(fn (mixed $value): int => (int) $value)->values()->all();

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
