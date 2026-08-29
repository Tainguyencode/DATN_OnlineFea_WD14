<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptRegrade;
use App\Models\QuizVersion;
use App\Models\QuizVersionQuestionInvalidation;
use App\Models\User;

class QuizAttemptResultService
{
    /** @return array{attempt: QuizAttempt, quiz: Quiz, version: QuizVersion, questions: array<int, array<string, mixed>>, regrade: ?QuizAttemptRegrade} */
    public function forLearner(Course $course, Lesson $lesson, User $user, QuizAttempt $requestedAttempt): array
    {
        app(QuizAttemptService::class)->assertAccess($course, $lesson, $user);

        $attempt = QuizAttempt::query()
            ->with([
                'quiz',
                'quizVersion.questionMappings.questionVersion.options',
                'quizVersion.questionMappings.invalidations',
                'attemptAnswers.answer',
                'regrades',
            ])
            ->findOrFail($requestedAttempt->id);

        abort_unless((int) $attempt->user_id === (int) $user->id, 403);
        abort_unless($attempt->status === 'completed', 404);
        abort_unless($attempt->quiz && (int) $attempt->quiz->lesson_id === (int) $lesson->id, 404);
        abort_unless($attempt->quizVersion && (int) $attempt->quizVersion->quiz_id === (int) $attempt->quiz_id, 404);

        return [
            'attempt' => $attempt,
            'quiz' => $attempt->quiz,
            'version' => $attempt->quizVersion,
            'questions' => $this->questions($attempt, $attempt->quizVersion),
            'regrade' => $attempt->regrades->sortByDesc('id')->first(),
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private function questions(QuizAttempt $attempt, QuizVersion $version): array
    {
        $answersByQuestionVersion = $attempt->attemptAnswers->groupBy('question_version_id');

        return app(QuizAttemptPresentationService::class)->orderedQuestionData($attempt)->map(function (array $data, int $index) use ($answersByQuestionVersion, $attempt): array {
            $mapping = $data['mapping'];
            $questionVersion = $data['questionVersion'];
            $options = $data['options'];
            abort_unless($questionVersion && (int) $questionVersion->question_id === (int) $mapping->question_id, 404);

            $rows = $answersByQuestionVersion->get($questionVersion->id, collect());
            $persistedAnswerIds = $rows
                ->pluck('answer_id')
                ->filter(fn ($answerId) => $answerId !== null)
                ->map(fn ($answerId) => (int) $answerId)
                ->unique()
                ->values()
                ->all();
            $selectedIds = $rows
                ->filter(fn ($answer): bool => $answer->answer
                    && (int) $answer->answer->question_version_id === (int) $questionVersion->id)
                ->pluck('answer_id')
                ->map(fn ($answerId) => (int) $answerId)
                ->unique()
                ->values()
                ->all();
            $storedAnswerIds = $this->storedAnswerIds($attempt, (int) $mapping->question_id);
            $missingSelection = count(array_diff($persistedAnswerIds, $selectedIds)) > 0
                || count(array_diff($storedAnswerIds, $persistedAnswerIds)) > 0;

            return [
                'number' => $index + 1,
                'question_id' => (int) $mapping->question_id,
                'question_version_id' => (int) $questionVersion->id,
                'question' => $questionVersion->question,
                'image_url' => $questionVersion->image_path ? asset('storage/'.$questionVersion->image_path) : null,
                'type' => $questionVersion->type,
                'points' => (int) $questionVersion->points,
                'explanation' => $questionVersion->explanation,
                'is_correct' => $rows->contains(fn ($answer): bool => (bool) $answer->is_correct),
                'is_excluded' => $mapping->invalidations
                    ->contains('status', QuizVersionQuestionInvalidation::STATUS_ACTIVE),
                'is_unanswered' => $selectedIds === [] && ! $missingSelection,
                'has_missing_selection' => $missingSelection,
                'options' => $options->map(fn ($option): array => [
                    'id' => (int) $option->id,
                    'text' => $option->option_text,
                    'is_selected' => in_array((int) $option->id, $selectedIds, true),
                    'is_correct' => (bool) $option->is_correct,
                ])->values()->all(),
            ];
        })->all();
    }

    /** @return array<int, int> */
    private function storedAnswerIds(QuizAttempt $attempt, int $questionId): array
    {
        $storedAnswers = $attempt->answers ?? [];
        $stored = $storedAnswers[$questionId] ?? $storedAnswers[(string) $questionId] ?? [];
        $stored = is_array($stored) ? $stored : [$stored];

        return collect($stored)
            ->filter(fn ($answerId) => $answerId !== null && $answerId !== '')
            ->map(fn ($answerId) => (int) $answerId)
            ->unique()
            ->values()
            ->all();
    }
}
