<?php

namespace App\Services;

use App\Models\QuestionVersion;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\QuizVersion;
use App\Models\QuizVersionQuestion;
use Closure;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class QuizAttemptPresentationService
{
    public const SNAPSHOT_VERSION = 1;

    private Closure $shuffler;

    public function __construct(?Closure $shuffler = null)
    {
        $this->shuffler = $shuffler ?? static function (array $items): array {
            shuffle($items);

            return $items;
        };
    }

    /**
     * @return array{version: int, questions: list<array{question_version_id: int, option_ids: list<int>}>}
     */
    public function createSnapshot(QuizVersion $version): array
    {
        $version->loadMissing('questionMappings.questionVersion.options');

        $questions = $version->questionMappings
            ->map(function (QuizVersionQuestion $mapping): array {
                $questionVersion = $this->validQuestionVersion($mapping);
                $optionIds = $questionVersion->options
                    ->map(fn ($option): int => (int) $option->id)
                    ->values()
                    ->all();

                return [
                    'question_version_id' => (int) $questionVersion->id,
                    'option_ids' => $this->shuffle($optionIds),
                ];
            })
            ->values()
            ->all();

        $snapshot = [
            'version' => self::SNAPSHOT_VERSION,
            'questions' => $this->shuffle($questions),
        ];

        if (! $this->validateSnapshot($snapshot, $version)) {
            throw new \UnexpectedValueException('Quiz attempt presentation snapshot is not a valid permutation.');
        }

        return $snapshot;
    }

    /**
     * Return the persisted snapshot when it is complete and bound to this exact version.
     * Legacy or invalid snapshots use the historical deterministic composition order.
     *
     * @return array{version: int, questions: list<array{question_version_id: int, option_ids: list<int>}>}
     */
    public function presentationOrder(QuizAttempt $attempt): array
    {
        $attempt->loadMissing('quizVersion.questionMappings.questionVersion.options');
        $version = $attempt->quizVersion;

        abort_unless($version, 409, 'Quiz attempt does not have a bound quiz version.');

        if ($this->validateSnapshot($attempt->presentation_order, $version)) {
            return $this->normalizeSnapshot($attempt->presentation_order);
        }

        return $this->deterministicSnapshot($version);
    }

    public function validateSnapshot(?array $snapshot, QuizVersion $version): bool
    {
        if (($snapshot['version'] ?? null) != self::SNAPSHOT_VERSION
            || ! is_array($snapshot['questions'] ?? null)) {
            return false;
        }

        $version->loadMissing('questionMappings.questionVersion.options');
        $mappingsByQuestionVersion = [];

        foreach ($version->questionMappings as $mapping) {
            $questionVersion = $mapping->questionVersion;

            if (! $questionVersion
                || (int) $questionVersion->question_id !== (int) $mapping->question_id
                || isset($mappingsByQuestionVersion[(int) $questionVersion->id])) {
                return false;
            }

            $mappingsByQuestionVersion[(int) $questionVersion->id] = $mapping;
        }

        if (count($snapshot['questions']) !== count($mappingsByQuestionVersion)) {
            return false;
        }

        $seenQuestionVersionIds = [];

        foreach ($snapshot['questions'] as $snapshotQuestion) {
            if (! is_array($snapshotQuestion)) {
                return false;
            }

            $questionVersionId = $this->integerId($snapshotQuestion['question_version_id'] ?? null);
            $optionIds = $snapshotQuestion['option_ids'] ?? null;

            if ($questionVersionId === null
                || isset($seenQuestionVersionIds[$questionVersionId])
                || ! is_array($optionIds)
                || ! isset($mappingsByQuestionVersion[$questionVersionId])) {
                return false;
            }

            $questionVersion = $mappingsByQuestionVersion[$questionVersionId]->questionVersion;
            if ($questionVersion->options->contains(
                fn ($option): bool => (int) $option->question_version_id !== $questionVersionId,
            )) {
                return false;
            }

            $expectedOptionIds = $questionVersion->options
                ->map(fn ($option): int => (int) $option->id)
                ->values()
                ->all();
            $normalizedOptionIds = [];

            foreach ($optionIds as $optionId) {
                $normalizedOptionId = $this->integerId($optionId);

                if ($normalizedOptionId === null || in_array($normalizedOptionId, $normalizedOptionIds, true)) {
                    return false;
                }

                $normalizedOptionIds[] = $normalizedOptionId;
            }

            sort($expectedOptionIds);
            sort($normalizedOptionIds);

            if ($expectedOptionIds !== $normalizedOptionIds) {
                return false;
            }

            $seenQuestionVersionIds[$questionVersionId] = true;
        }

        return count($seenQuestionVersionIds) === count($mappingsByQuestionVersion);
    }

    /**
     * @return Collection<int, array{mapping: QuizVersionQuestion, questionVersion: QuestionVersion, options: Collection<int, mixed> }>
     */
    public function orderedQuestionData(QuizAttempt $attempt): Collection
    {
        $attempt->loadMissing('quizVersion.questionMappings.questionVersion.options');
        $version = $attempt->quizVersion;

        abort_unless($version, 409, 'Quiz attempt does not have a bound quiz version.');

        $mappingsByQuestionVersion = $version->questionMappings->keyBy(
            fn (QuizVersionQuestion $mapping): int => (int) $mapping->question_version_id,
        );

        $allowedQuestionIds = collect($attempt->question_ids ?? [])
            ->map(fn ($questionId): int => (int) $questionId);

        return collect($this->presentationOrder($attempt)['questions'])
            ->map(function (array $snapshotQuestion) use ($mappingsByQuestionVersion): array {
                $mapping = $mappingsByQuestionVersion->get((int) $snapshotQuestion['question_version_id']);
                $questionVersion = $mapping->questionVersion;
                $optionsById = $questionVersion->options->keyBy(fn ($option): int => (int) $option->id);
                $options = collect($snapshotQuestion['option_ids'])
                    ->map(fn (int $optionId) => $optionsById->get($optionId))
                    ->values();

                return [
                    'mapping' => $mapping,
                    'questionVersion' => $questionVersion,
                    'options' => $options,
                ];
            })
            ->when(
                $allowedQuestionIds->isNotEmpty(),
                fn (Collection $questions): Collection => $questions
                    ->filter(fn (array $data): bool => $allowedQuestionIds->contains(
                        (int) $data['mapping']->question_id,
                    )),
            )
            ->values();
    }

    public function projectQuiz(Quiz $projectedQuiz, QuizAttempt $attempt): Quiz
    {
        $questionsByVersionId = $projectedQuiz->questions->keyBy(
            fn (QuizQuestion $question): int => (int) $question->authoringVersion->id,
        );

        $questions = $this->orderedQuestionData($attempt)
            ->map(function (array $data) use ($questionsByVersionId): ?QuizQuestion {
                $questionVersionId = (int) $data['questionVersion']->id;
                $question = $questionsByVersionId->get($questionVersionId);

                if (! $question) {
                    return null;
                }

                $projectedQuestion = clone $question;
                $projectedQuestion->setRelation('options', $data['options']);

                return $projectedQuestion;
            })
            ->filter()
            ->values();

        $projected = clone $projectedQuiz;
        $projected->setRelation('questions', new EloquentCollection($questions->all()));

        return $projected;
    }

    /**
     * @return array{version: int, questions: list<array{question_version_id: int, option_ids: list<int>}>}
     */
    private function deterministicSnapshot(QuizVersion $version): array
    {
        $version->loadMissing('questionMappings.questionVersion.options');

        return [
            'version' => self::SNAPSHOT_VERSION,
            'questions' => $version->questionMappings
                ->map(function (QuizVersionQuestion $mapping): array {
                    $questionVersion = $this->validQuestionVersion($mapping);

                    return [
                        'question_version_id' => (int) $questionVersion->id,
                        'option_ids' => $questionVersion->options
                            ->map(fn ($option): int => (int) $option->id)
                            ->values()
                            ->all(),
                    ];
                })
                ->values()
                ->all(),
        ];
    }

    /**
     * @param  array{version: int, questions: list<array{question_version_id: int, option_ids: list<int>}>}  $snapshot
     * @return array{version: int, questions: list<array{question_version_id: int, option_ids: list<int>}>}
     */
    private function normalizeSnapshot(array $snapshot): array
    {
        return [
            'version' => self::SNAPSHOT_VERSION,
            'questions' => collect($snapshot['questions'])
                ->map(fn (array $question): array => [
                    'question_version_id' => (int) $question['question_version_id'],
                    'option_ids' => collect($question['option_ids'])
                        ->map(fn ($optionId): int => (int) $optionId)
                        ->values()
                        ->all(),
                ])
                ->values()
                ->all(),
        ];
    }

    /** @param array<int, mixed> $items */
    private function shuffle(array $items): array
    {
        $shuffled = ($this->shuffler)($items);

        if (! is_array($shuffled) || count($shuffled) !== count($items)) {
            throw new \UnexpectedValueException('Quiz attempt shuffler must return a permutation.');
        }

        return array_values($shuffled);
    }

    private function validQuestionVersion(QuizVersionQuestion $mapping): QuestionVersion
    {
        $questionVersion = $mapping->questionVersion;

        if (! $questionVersion || (int) $questionVersion->question_id !== (int) $mapping->question_id) {
            throw ValidationException::withMessages([
                'quiz' => 'The bound quiz version has an invalid question composition.',
            ]);
        }

        return $questionVersion;
    }

    private function integerId(mixed $value): ?int
    {
        if (is_int($value)) {
            return $value > 0 ? $value : null;
        }

        if (is_string($value) && ctype_digit($value)) {
            $integer = (int) $value;

            return $integer > 0 ? $integer : null;
        }

        return null;
    }
}
