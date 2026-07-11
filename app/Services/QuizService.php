<?php

namespace App\Services;

use App\Models\Quiz;
use App\Models\QuizQuestion;

class QuizService
{
    public function grade(Quiz $quiz, array $submittedAnswers): array
    {
        $score = 0;
        $totalScore = 0;
        $answers = [];
        $questions = [];

        $quiz->loadMissing('questions.options');

        foreach ($quiz->questions as $question) {
            $points = (int) $question->points;
            $totalScore += $points;

            $selectedIds = $this->selectedAnswerIds($submittedAnswers[$question->id] ?? [], $question);
            $correctIds = $question->options
                ->where('is_correct', true)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->values()
                ->all();

            $questionPassed = $this->questionIsCorrect($question, $selectedIds, $correctIds);

            if ($questionPassed) {
                $score += $points;
            }

            $answers[$question->id] = $selectedIds;
            $questions[$question->id] = [
                'selected_ids' => $selectedIds,
                'correct_ids' => $correctIds,
                'is_correct' => $questionPassed,
            ];
        }

        $percent = $totalScore > 0 ? round(($score / $totalScore) * 100, 2) : 0;

        return [
            'score' => $score,
            'total_score' => $totalScore,
            'percent' => $percent,
            'passed' => $percent >= (int) $quiz->pass_score,
            'answers' => $answers,
            'questions' => $questions,
        ];
    }

    private function selectedAnswerIds(mixed $rawAnswers, QuizQuestion $question): array
    {
        $rawAnswers = is_array($rawAnswers) ? $rawAnswers : [$rawAnswers];
        $validIds = $question->options->pluck('id')->map(fn ($id) => (int) $id)->all();

        return collect($rawAnswers)
            ->filter(fn ($id) => $id !== null && $id !== '')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->filter(fn ($id) => in_array($id, $validIds, true))
            ->values()
            ->all();
    }

    private function questionIsCorrect(QuizQuestion $question, array $selectedIds, array $correctIds): bool
    {
        sort($selectedIds);
        sort($correctIds);

        if ($correctIds === []) {
            return false;
        }

        if ($question->type === QuizQuestion::TYPE_MULTIPLE) {
            return $selectedIds === $correctIds;
        }

        return count($selectedIds) === 1 && $selectedIds[0] === $correctIds[0];
    }
}
