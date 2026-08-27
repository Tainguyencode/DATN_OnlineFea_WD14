<?php

namespace App\Services;

use App\Models\QuizAttempt;
use App\Models\QuizAttemptRegrade;
use App\Models\QuizQuestion;
use App\Models\QuizVersion;
use App\Models\QuizVersionQuestionInvalidation;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class QuizAttemptRegradeService
{
    public const SYNC_ATTEMPT_LIMIT = 1000;

    /**
     * Recalculate a completed attempt from its immutable answer rows.
     *
     * @param  array<int, int>|null  $excludedMappingIds  Null means active invalidations for the bound version.
     * @return array<string, mixed>
     */
    public function calculate(QuizAttempt $attempt, ?array $excludedMappingIds = null): array
    {
        $attempt->loadMissing([
            'quizVersion.questionMappings.questionVersion.options',
            'quizVersion.questionMappings.invalidations',
            'attemptAnswers',
        ]);

        $version = $attempt->quizVersion;

        if (! $version) {
            throw ValidationException::withMessages([
                'attempt' => 'Quiz attempt does not have a bound quiz version.',
            ]);
        }

        $excludedMappingIds ??= $version->questionMappings
            ->filter(fn ($mapping) => $mapping->invalidations
                ->contains('status', QuizVersionQuestionInvalidation::STATUS_ACTIVE))
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        return $this->calculateForVersion($version, $attempt->attemptAnswers, $excludedMappingIds);
    }

    public function regrade(QuizAttempt $attempt, QuizVersionQuestionInvalidation $trigger): QuizAttemptRegrade
    {
        return DB::transaction(function () use ($attempt, $trigger): QuizAttemptRegrade {
            $lockedAttempt = QuizAttempt::query()->lockForUpdate()->findOrFail($attempt->id);
            $lockedTrigger = QuizVersionQuestionInvalidation::query()
                ->with('mapping.quizVersion')
                ->lockForUpdate()
                ->findOrFail($trigger->id);

            if ($lockedAttempt->status !== 'completed') {
                throw ValidationException::withMessages([
                    'attempt' => 'Only completed attempts can be historically regraded.',
                ]);
            }

            if ($lockedTrigger->status !== QuizVersionQuestionInvalidation::STATUS_ACTIVE) {
                throw ValidationException::withMessages([
                    'invalidation' => 'Only an approved invalidation can trigger a regrade.',
                ]);
            }

            $mapping = $lockedTrigger->mapping;
            $version = $lockedAttempt->quizVersion()->with([
                'questionMappings.questionVersion.options',
            ])->firstOrFail();

            if (! $mapping || (int) $mapping->quiz_version_id !== (int) $version->id) {
                throw ValidationException::withMessages([
                    'invalidation' => 'The invalidation does not belong to the attempt version.',
                ]);
            }

            $activeMappingIds = $version->questionMappings
                ->filter(fn ($candidate) => $candidate->invalidations
                    ->contains('status', QuizVersionQuestionInvalidation::STATUS_ACTIVE))
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $lockedAttempt->loadMissing('attemptAnswers');
            $recalculated = $this->calculateForVersion(
                $version,
                $lockedAttempt->attemptAnswers,
                $activeMappingIds,
            );
            $baseline = $this->calculateForVersion($version, $lockedAttempt->attemptAnswers);
            $previous = [
                'score' => (int) $lockedAttempt->score,
                'total_score' => (int) $lockedAttempt->total_score,
                'percent' => (float) $lockedAttempt->percent,
                'passed' => (bool) $lockedAttempt->passed,
            ];

            $firstRegrade = QuizAttemptRegrade::query()
                ->where('quiz_attempt_id', $lockedAttempt->id)
                ->orderBy('id')
                ->first();
            $original = $firstRegrade ? [
                'score' => (int) $firstRegrade->original_score,
                'total_score' => (int) $firstRegrade->original_total_score,
                'percent' => (float) $firstRegrade->original_percent,
                'passed' => (bool) $firstRegrade->original_passed,
            ] : [
                'score' => $baseline['score'],
                'total_score' => $baseline['total_score'],
                'percent' => $baseline['percent'],
                'passed' => $baseline['passed'],
            ];

            $useRecalculated = (float) $recalculated['percent'] > $previous['percent']
                || ((float) $recalculated['percent'] === $previous['percent']
                    && (! $previous['passed'] || $recalculated['passed']));
            $effective = $useRecalculated ? [
                'score' => $recalculated['score'],
                'total_score' => $recalculated['total_score'],
                'percent' => $recalculated['percent'],
                'passed' => $recalculated['passed'],
            ] : $previous;

            // Policy 3 is a hard no-disadvantage guard for instructor invalidations.
            if ($previous['passed'] && ! $effective['passed']) {
                $effective = $previous;
            }

            $lockedAttempt->update([
                'score' => $effective['score'],
                'total_score' => $effective['total_score'],
                'percent' => $effective['percent'],
                'passed' => $effective['passed'],
            ]);

            return QuizAttemptRegrade::updateOrCreate(
                [
                    'quiz_attempt_id' => $lockedAttempt->id,
                    'invalidation_id' => $lockedTrigger->id,
                ],
                [
                    'original_score' => $original['score'],
                    'original_total_score' => $original['total_score'],
                    'original_percent' => $original['percent'],
                    'original_passed' => $original['passed'],
                    'recalculated_score' => $recalculated['score'],
                    'recalculated_total_score' => $recalculated['total_score'],
                    'recalculated_percent' => $recalculated['percent'],
                    'recalculated_passed' => $recalculated['passed'],
                    'effective_score' => $effective['score'],
                    'effective_total_score' => $effective['total_score'],
                    'effective_percent' => $effective['percent'],
                    'effective_passed' => $effective['passed'],
                    'regraded_at' => now(),
                ],
            );
        });
    }

    /** @return array<int, int> */
    public function processInvalidation(QuizVersionQuestionInvalidation $invalidation): array
    {
        $invalidation->loadMissing('mapping.quizVersion');
        abort_unless($invalidation->status === QuizVersionQuestionInvalidation::STATUS_ACTIVE, 409);

        if (! $invalidation->regrade_started_at) {
            $invalidation->update(['regrade_started_at' => now()]);
        }

        $mapping = $invalidation->mapping;
        $userIds = [];

        QuizAttempt::query()
            ->where('quiz_version_id', $mapping->quiz_version_id)
            ->where('status', 'completed')
            ->orderBy('id')
            ->chunkById(100, function ($attempts) use ($invalidation, &$userIds): void {
                foreach ($attempts as $attempt) {
                    $this->regrade($attempt, $invalidation);
                    $userIds[(int) $attempt->user_id] = true;
                }
            });

        $invalidation->update(['regrade_completed_at' => now()]);

        return array_map('intval', array_keys($userIds));
    }

    /** @return array<string, mixed> */
    private function calculateForVersion(QuizVersion $version, $attemptAnswers, array $excludedMappingIds = []): array
    {
        $excludedMappingIds = collect($excludedMappingIds)->map(fn ($id) => (int) $id)->flip();
        $answersByQuestionVersion = $attemptAnswers->groupBy('question_version_id');
        $score = 0;
        $totalScore = 0;
        $answers = [];
        $questions = [];
        $excludedQuestionIds = [];

        foreach ($version->questionMappings as $mapping) {
            $questionVersion = $mapping->questionVersion;
            $questionId = (int) $mapping->question_id;

            if (! $questionVersion || (int) $questionVersion->question_id !== $questionId) {
                throw ValidationException::withMessages([
                    'quiz' => 'The bound quiz version has an invalid question composition.',
                ]);
            }

            $rows = $answersByQuestionVersion->get($questionVersion->id, collect());
            $validOptionIds = $questionVersion->options->pluck('id')->map(fn ($id) => (int) $id)->all();
            $selectedIds = $rows
                ->pluck('answer_id')
                ->filter(fn ($id) => $id !== null && in_array((int) $id, $validOptionIds, true))
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values()
                ->all();
            $correctIds = $questionVersion->options
                ->where('is_correct', true)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->values()
                ->all();
            $questionPassed = $this->questionIsCorrect($questionVersion->type, $selectedIds, $correctIds);
            $isExcluded = $excludedMappingIds->has((int) $mapping->id);

            if ($isExcluded) {
                $excludedQuestionIds[] = $questionId;
            } else {
                $totalScore += (int) $questionVersion->points;
                if ($questionPassed) {
                    $score += (int) $questionVersion->points;
                }
            }

            $answers[$questionId] = $selectedIds;
            $questions[$questionId] = [
                'mapping_id' => (int) $mapping->id,
                'question_version_id' => (int) $questionVersion->id,
                'selected_ids' => $selectedIds,
                'correct_ids' => $correctIds,
                'is_correct' => $questionPassed,
                'is_excluded' => $isExcluded,
            ];
        }

        $percent = $totalScore > 0 ? round(($score / $totalScore) * 100, 2) : 0;

        return [
            'score' => $score,
            'total_score' => $totalScore,
            'percent' => $percent,
            'passed' => $totalScore > 0 && $percent >= (int) $version->pass_score,
            'answers' => $answers,
            'questions' => $questions,
            'excluded_mapping_ids' => $excludedMappingIds->keys()->map(fn ($id) => (int) $id)->values()->all(),
            'excluded_question_ids' => array_values(array_unique($excludedQuestionIds)),
        ];
    }

    private function questionIsCorrect(string $type, array $selectedIds, array $correctIds): bool
    {
        sort($selectedIds);
        sort($correctIds);

        if ($correctIds === []) {
            return false;
        }

        return $type === QuizQuestion::TYPE_MULTIPLE
            ? $selectedIds === $correctIds
            : count($selectedIds) === 1 && $selectedIds[0] === $correctIds[0];
    }
}
