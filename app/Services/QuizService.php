<?php

namespace App\Services;

use App\Models\QuestionVersion;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\QuizVersion;
use Illuminate\Validation\ValidationException;

class QuizService
{
    public function grade(Quiz|QuizAttempt|QuizVersion $subject, array $submittedAnswers, ?QuizVersion $version = null): array
    {
        if ($subject instanceof QuizAttempt) {
            $subject->loadMissing('quizVersion');

            if (! $subject->quizVersion) {
                throw ValidationException::withMessages([
                    'attempt' => 'Quiz attempt does not have a bound quiz version.',
                ]);
            }

            return $this->gradeVersion($subject->quizVersion, $submittedAnswers);
        }

        if ($subject instanceof QuizVersion) {
            return $this->gradeVersion($subject, $submittedAnswers);
        }

        if ($version) {
            app(QuizVersioningService::class)->assertVersionBelongsToQuiz($subject, $version);

            return $this->gradeVersion($version, $submittedAnswers);
        }

        if ($subject->current_published_version_id) {
            return $this->gradeVersion(
                app(QuizVersioningService::class)->currentPublished($subject),
                $submittedAnswers,
            );
        }

        return $this->gradeLegacyQuiz($subject, $submittedAnswers);
    }

    private function gradeVersion(QuizVersion $version, array $submittedAnswers): array
    {
        $score = 0;
        $totalScore = 0;
        $answers = [];
        $questions = [];

        $version->loadMissing('questionMappings.questionVersion.options');

        foreach ($version->questionMappings as $mapping) {
            $questionVersion = $mapping->questionVersion;

            if (! $questionVersion || (int) $questionVersion->question_id !== (int) $mapping->question_id) {
                throw ValidationException::withMessages([
                    'quiz' => 'The bound quiz version has an invalid question composition.',
                ]);
            }

            $questionId = (int) $mapping->question_id;
            $points = (int) $questionVersion->points;
            $totalScore += $points;

            $selectedIds = $this->selectedAnswerIds($submittedAnswers[$questionId] ?? [], $questionVersion);
            $correctIds = $questionVersion->options
                ->where('is_correct', true)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->values()
                ->all();

            $questionPassed = $this->questionIsCorrect($questionVersion->type, $selectedIds, $correctIds);

            if ($questionPassed) {
                $score += $points;
            }

            $answers[$questionId] = $selectedIds;
            $questions[$questionId] = [
                'question_version_id' => (int) $questionVersion->id,
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
            'passed' => $percent >= (int) $version->pass_score,
            'answers' => $answers,
            'questions' => $questions,
        ];
    }

    private function gradeLegacyQuiz(Quiz $quiz, array $submittedAnswers): array
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
            $questionPassed = $this->questionIsCorrect($question->type, $selectedIds, $correctIds);

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

    private function selectedAnswerIds(mixed $rawAnswers, QuestionVersion|QuizQuestion $question): array
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

    private function questionIsCorrect(string $type, array $selectedIds, array $correctIds): bool
    {
        sort($selectedIds);
        sort($correctIds);

        if ($correctIds === []) {
            return false;
        }

        if ($type === QuizQuestion::TYPE_MULTIPLE) {
            return $selectedIds === $correctIds;
        }

        return count($selectedIds) === 1 && $selectedIds[0] === $correctIds[0];
    }
}
