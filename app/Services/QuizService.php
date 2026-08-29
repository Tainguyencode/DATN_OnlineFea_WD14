<?php

namespace App\Services;

use App\Models\QuestionVersion;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\QuizVersion;
use App\Models\QuizVersionQuestionInvalidation;
use Illuminate\Validation\ValidationException;

class QuizService
{
    public function buildAttemptReview(QuizAttempt $attempt): array
    {
        $attempt->loadMissing([
            'quiz.questions.options',
            'quizVersion.questionMappings.question',
            'quizVersion.questionMappings.questionVersion.options',
            'attemptAnswers',
            'user',
            'quiz.lesson.course',
            'quiz.lesson.section.course',
            'quiz.lesson.chapter.course',
        ]);

        $quiz = $attempt->quiz;
        if ($quiz && $attempt->quizVersion && (int) $attempt->quizVersion->quiz_id === (int) $quiz->id) {
            $quiz = app(QuizAttemptService::class)->projectQuiz($attempt);
        }
        $attemptAnswersGrouped = $attempt->attemptAnswers->groupBy('question_id');
        $rawSavedAnswers = is_array($attempt->answers) ? $attempt->answers : [];

        $questionsReview = [];
        $totalQuestions = $quiz ? $quiz->questions->count() : 0;
        $correctQuestionsCount = 0;

        if ($quiz) {
            foreach ($quiz->questions as $index => $question) {
                $selectedIds = [];
                if ($attemptAnswersGrouped->has($question->id)) {
                    $selectedIds = $attemptAnswersGrouped->get($question->id)
                        ->whereNotNull('answer_id')
                        ->pluck('answer_id')
                        ->map(fn ($id) => (int) $id)
                        ->unique()
                        ->values()
                        ->all();
                }

                if (empty($selectedIds) && isset($rawSavedAnswers[$question->id])) {
                    $raw = is_array($rawSavedAnswers[$question->id]) ? $rawSavedAnswers[$question->id] : [$rawSavedAnswers[$question->id]];
                    $selectedIds = collect($raw)
                        ->filter(fn ($id) => $id !== null && $id !== '')
                        ->map(fn ($id) => (int) $id)
                        ->unique()
                        ->values()
                        ->all();
                }

                $correctIds = $question->options
                    ->where('is_correct', true)
                    ->pluck('id')
                    ->map(fn ($id) => (int) $id)
                    ->values()
                    ->all();

                $isCorrect = $this->questionIsCorrect($question->type, $selectedIds, $correctIds);
                if ($isCorrect) {
                    $correctQuestionsCount++;
                }

                $optionsReview = $question->options->map(function ($option) use ($selectedIds, $correctIds) {
                    $optionId = (int) $option->id;
                    $isSelected = in_array($optionId, $selectedIds, true);
                    $isCorrectOption = in_array($optionId, $correctIds, true);

                    return [
                        'id' => $optionId,
                        'option_text' => $option->option_text,
                        'is_selected' => $isSelected,
                        'is_correct' => $isCorrectOption,
                    ];
                })->values()->all();

                $questionsReview[] = [
                    'id' => $question->id,
                    'question_number' => $index + 1,
                    'question' => $question->question,
                    'type' => $question->type,
                    'form_type' => $question->form_type,
                    'points' => (int) $question->points,
                    'explanation' => $question->explanation,
                    'is_correct' => $isCorrect,
                    'selected_ids' => $selectedIds,
                    'correct_ids' => $correctIds,
                    'options' => $optionsReview,
                ];
            }
        }

        $allAttempts = $quiz ? QuizAttempt::where('quiz_id', $quiz->id)
            ->where('user_id', $attempt->user_id)
            ->orderBy('id', 'asc')
            ->get()
            ->values()
            ->map(function ($att, $idx) use ($attempt) {
                return [
                    'id' => $att->id,
                    'attempt_number' => $idx + 1,
                    'score' => $att->score,
                    'total_score' => $att->total_score,
                    'percent' => (float) $att->percent,
                    'passed' => (bool) $att->passed,
                    'completed_at' => $att->completed_at,
                    'is_current' => (int) $att->id === (int) $attempt->id,
                ];
            })
            ->all() : [];

        $currentAttemptIndex = collect($allAttempts)->search(fn ($a) => $a['is_current']);
        $attemptNumber = $currentAttemptIndex !== false ? $currentAttemptIndex + 1 : 1;

        $course = $quiz?->lesson?->course
            ?? $quiz?->lesson?->section?->course
            ?? $quiz?->lesson?->chapter?->course;

        return [
            'attempt' => $attempt,
            'attempt_number' => $attemptNumber,
            'quiz' => $quiz,
            'user' => $attempt->user,
            'course' => $course,
            'lesson' => $quiz?->lesson,
            'questions' => $questionsReview,
            'total_questions' => $totalQuestions,
            'correct_questions_count' => $correctQuestionsCount,
            'all_attempts' => $allAttempts,
        ];
    }

    public function grade(Quiz|QuizAttempt|QuizVersion $subject, array $submittedAnswers, ?QuizVersion $version = null): array
    {
        if ($subject instanceof QuizAttempt) {
            $subject->loadMissing('quizVersion');

            if (! $subject->quizVersion) {
                throw ValidationException::withMessages([
                    'attempt' => 'Quiz attempt does not have a bound quiz version.',
                ]);
            }

            return $this->gradeVersion($subject->quizVersion, $submittedAnswers, $subject->question_ids);
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

    private function gradeVersion(QuizVersion $version, array $submittedAnswers, ?array $allowedQuestionIds = null): array
    {
        $score = 0;
        $totalScore = 0;
        $answers = [];
        $questions = [];

        $version->loadMissing('questionMappings.questionVersion.options', 'questionMappings.invalidations');

        $allowedQuestionIds = collect($allowedQuestionIds ?? [])->map(fn ($id) => (int) $id);
        $mappings = $version->questionMappings
            ->when($allowedQuestionIds->isNotEmpty(), fn ($items) => $items->whereIn('question_id', $allowedQuestionIds));

        foreach ($mappings as $mapping) {
            $questionVersion = $mapping->questionVersion;

            if (! $questionVersion || (int) $questionVersion->question_id !== (int) $mapping->question_id) {
                throw ValidationException::withMessages([
                    'quiz' => 'The bound quiz version has an invalid question composition.',
                ]);
            }

            $questionId = (int) $mapping->question_id;
            $points = (int) $questionVersion->points;
            $isExcluded = $mapping->invalidations
                ->contains('status', QuizVersionQuestionInvalidation::STATUS_ACTIVE);

            if (! $isExcluded) {
                $totalScore += $points;
            }

            $selectedIds = $this->selectedAnswerIds($submittedAnswers[$questionId] ?? [], $questionVersion);
            $correctIds = $questionVersion->options
                ->where('is_correct', true)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->values()
                ->all();

            $questionPassed = $this->questionIsCorrect($questionVersion->type, $selectedIds, $correctIds);

            if ($questionPassed && ! $isExcluded) {
                $score += $points;
            }

            $answers[$questionId] = $selectedIds;
            $questions[$questionId] = [
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
