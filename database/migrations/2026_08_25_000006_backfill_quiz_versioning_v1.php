<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const QUIZ_STATUS_DRAFT = 'draft';

    private const QUIZ_STATUS_PUBLISHED = 'published';

    public function up(): void
    {
        $this->assertRecoverableLegacyHistory();
        $this->backfillQuizzesAndQuestions();
        $this->backfillOptions();
        $this->backfillAttempts();
        $this->backfillAttemptAnswers();
        $this->assertBackfillComplete();
    }

    public function down(): void
    {
        if (DB::table('quiz_versions')->exists() || DB::table('question_versions')->exists()) {
            throw new RuntimeException(
                'Quiz V1 backfill is irreversible because rollback would destroy historical snapshots.',
            );
        }
    }

    private function assertRecoverableLegacyHistory(): void
    {
        $orphanQuestion = DB::table('quiz_questions as questions')
            ->leftJoin('quizzes', 'quizzes.id', '=', 'questions.quiz_id')
            ->whereNull('quizzes.id')
            ->select('questions.id', 'questions.quiz_id')
            ->first();

        if ($orphanQuestion) {
            throw new RuntimeException(sprintf(
                'Quiz V1 backfill stopped: question %d references missing quiz %d.',
                $orphanQuestion->id,
                $orphanQuestion->quiz_id,
            ));
        }

        $unsupportedQuestion = DB::table('quiz_questions')
            ->whereNotIn('type', ['single', 'multiple', 'true_false', 'single_choice', 'multiple_choice'])
            ->select('id', 'type')
            ->first();

        if ($unsupportedQuestion) {
            throw new RuntimeException(sprintf(
                'Quiz V1 backfill stopped: question %d has unsupported type "%s".',
                $unsupportedQuestion->id,
                $unsupportedQuestion->type,
            ));
        }

        $orphanOption = DB::table('quiz_options as options')
            ->leftJoin('quiz_questions as questions', 'questions.id', '=', 'options.quiz_question_id')
            ->whereNull('questions.id')
            ->select('options.id', 'options.quiz_question_id')
            ->first();

        if ($orphanOption) {
            throw new RuntimeException(sprintf(
                'Quiz V1 backfill stopped: option %d references missing question %d.',
                $orphanOption->id,
                $orphanOption->quiz_question_id,
            ));
        }

        $orphanAttempt = DB::table('quiz_attempts as attempts')
            ->leftJoin('quizzes', 'quizzes.id', '=', 'attempts.quiz_id')
            ->whereNull('quizzes.id')
            ->select('attempts.id', 'attempts.quiz_id')
            ->first();

        if ($orphanAttempt) {
            throw new RuntimeException(sprintf(
                'Quiz V1 backfill stopped: attempt %d references missing quiz %d.',
                $orphanAttempt->id,
                $orphanAttempt->quiz_id,
            ));
        }

        $orphanAttemptAnswer = DB::table('quiz_attempt_answers as attempt_answers')
            ->leftJoin('quiz_attempts as attempts', 'attempts.id', '=', 'attempt_answers.quiz_attempt_id')
            ->leftJoin('quiz_questions as questions', 'questions.id', '=', 'attempt_answers.question_id')
            ->where(function ($query): void {
                $query->whereNull('attempts.id')->orWhereNull('questions.id');
            })
            ->select(
                'attempt_answers.id',
                'attempt_answers.quiz_attempt_id',
                'attempt_answers.question_id',
            )
            ->first();

        if ($orphanAttemptAnswer) {
            throw new RuntimeException(sprintf(
                'Quiz V1 backfill stopped: attempt answer %d cannot resolve attempt %d and question %d.',
                $orphanAttemptAnswer->id,
                $orphanAttemptAnswer->quiz_attempt_id,
                $orphanAttemptAnswer->question_id,
            ));
        }

        $crossQuizAnswer = DB::table('quiz_attempt_answers as attempt_answers')
            ->join('quiz_attempts as attempts', 'attempts.id', '=', 'attempt_answers.quiz_attempt_id')
            ->join('quiz_questions as questions', 'questions.id', '=', 'attempt_answers.question_id')
            ->whereColumn('attempts.quiz_id', '!=', 'questions.quiz_id')
            ->select('attempt_answers.id')
            ->first();

        if ($crossQuizAnswer) {
            throw new RuntimeException(sprintf(
                'Quiz V1 backfill stopped: attempt answer %d belongs to a question outside its quiz.',
                $crossQuizAnswer->id,
            ));
        }

        $missingSelectedOption = DB::table('quiz_attempt_answers as attempt_answers')
            ->leftJoin('quiz_options as options', 'options.id', '=', 'attempt_answers.answer_id')
            ->whereNotNull('attempt_answers.answer_id')
            ->whereNull('options.id')
            ->select('attempt_answers.id', 'attempt_answers.answer_id')
            ->first();

        if ($missingSelectedOption) {
            throw new RuntimeException(sprintf(
                'Quiz V1 backfill stopped: attempt answer %d references missing option %d.',
                $missingSelectedOption->id,
                $missingSelectedOption->answer_id,
            ));
        }

        $crossQuestionOption = DB::table('quiz_attempt_answers as attempt_answers')
            ->join('quiz_options as options', 'options.id', '=', 'attempt_answers.answer_id')
            ->whereColumn('options.quiz_question_id', '!=', 'attempt_answers.question_id')
            ->select('attempt_answers.id')
            ->first();

        if ($crossQuestionOption) {
            throw new RuntimeException(sprintf(
                'Quiz V1 backfill stopped: attempt answer %d selects an option from another question.',
                $crossQuestionOption->id,
            ));
        }
    }

    private function backfillQuizzesAndQuestions(): void
    {
        DB::table('quizzes')
            ->orderBy('id')
            ->chunkById(100, function ($quizzes): void {
                foreach ($quizzes as $quiz) {
                    $context = $this->publicationContext((int) $quiz->id);
                    $status = $this->classifyQuiz($quiz, $context);
                    $publishedAt = $status === self::QUIZ_STATUS_PUBLISHED
                        ? ($context->course_published_at ?? $quiz->updated_at ?? now())
                        : null;

                    DB::table('quiz_versions')->updateOrInsert(
                        ['quiz_id' => $quiz->id, 'version' => 1],
                        [
                            'title' => $quiz->title,
                            'description' => $quiz->description,
                            'pass_score' => $quiz->pass_score,
                            'time_limit_minutes' => $quiz->time_limit_minutes,
                            'max_attempts' => $quiz->max_attempts,
                            'status' => $status,
                            'created_by' => $context->creator_id,
                            'published_at' => $publishedAt,
                            'created_at' => $quiz->created_at ?? now(),
                            'updated_at' => $quiz->updated_at ?? now(),
                        ],
                    );

                    $quizVersion = DB::table('quiz_versions')
                        ->where('quiz_id', $quiz->id)
                        ->where('version', 1)
                        ->first();

                    $this->updateQuizPointer($quiz, (int) $quizVersion->id, $status);
                    $this->backfillQuestionsForQuiz($quiz, $quizVersion);
                }
            });
    }

    private function publicationContext(int $quizId): object
    {
        return DB::table('quizzes')
            ->join('lessons', 'lessons.id', '=', 'quizzes.lesson_id')
            ->leftJoin('courses', 'courses.id', '=', 'lessons.course_id')
            ->leftJoin('users', 'users.id', '=', 'courses.instructor_id')
            ->where('quizzes.id', $quizId)
            ->select([
                'lessons.status as lesson_status',
                'courses.status as course_status',
                'courses.is_published as course_is_published',
                'courses.published_at as course_published_at',
                'users.id as creator_id',
                'users.instructor_status as instructor_status',
                'users.is_active as instructor_is_active',
                'users.account_status as instructor_account_status',
            ])
            ->firstOrFail();
    }

    private function classifyQuiz(object $quiz, object $context): string
    {
        if (DB::table('quiz_attempts')->where('quiz_id', $quiz->id)->exists()) {
            return self::QUIZ_STATUS_PUBLISHED;
        }

        $contentVisible = (bool) $context->course_is_published
            || in_array($context->course_status, [
                'approved',
                'published',
                'pending_update',
                'rejected_update',
            ], true);
        $visibilityBlocked = ! (bool) $context->course_is_published
            && in_array($context->course_status, [
                'draft',
                'pending',
                'pending_review',
                'submitted',
                'rejected',
                'suspended',
                'archived',
            ], true);
        $instructorVisible = $context->creator_id !== null
            && $context->instructor_status === 'approved'
            && (bool) $context->instructor_is_active
            && ! in_array($context->instructor_account_status, ['locked', 'suspended'], true);

        if ($contentVisible && ! $visibilityBlocked && $instructorVisible) {
            return self::QUIZ_STATUS_PUBLISHED;
        }

        if ((bool) $quiz->is_active && $context->lesson_status === 'published') {
            return self::QUIZ_STATUS_PUBLISHED;
        }

        return self::QUIZ_STATUS_DRAFT;
    }

    private function updateQuizPointer(object $quiz, int $quizVersionId, string $status): void
    {
        if ($status === self::QUIZ_STATUS_PUBLISHED) {
            DB::table('quizzes')->where('id', $quiz->id)->update([
                'current_published_version_id' => $quizVersionId,
                'current_draft_version_id' => (int) $quiz->current_draft_version_id === $quizVersionId
                    ? null
                    : $quiz->current_draft_version_id,
            ]);

            return;
        }

        DB::table('quizzes')->where('id', $quiz->id)->update([
            'current_draft_version_id' => $quizVersionId,
            'current_published_version_id' => (int) $quiz->current_published_version_id === $quizVersionId
                ? null
                : $quiz->current_published_version_id,
        ]);
    }

    private function backfillQuestionsForQuiz(object $quiz, object $quizVersion): void
    {
        $usedSortOrders = [];

        DB::table('quiz_questions')
            ->where('quiz_id', $quiz->id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->lazy(250)
            ->each(function (object $question) use ($quizVersion, &$usedSortOrders): void {
                $status = $quizVersion->status === self::QUIZ_STATUS_DRAFT
                    ? self::QUIZ_STATUS_DRAFT
                    : self::QUIZ_STATUS_PUBLISHED;
                $publishedAt = $status === self::QUIZ_STATUS_PUBLISHED
                    ? ($quizVersion->published_at ?? $question->updated_at ?? now())
                    : null;

                DB::table('question_versions')->updateOrInsert(
                    ['question_id' => $question->id, 'version' => 1],
                    [
                        'question' => $question->question,
                        'type' => $this->canonicalQuestionType($question->type),
                        'points' => $question->points,
                        'explanation' => $question->explanation,
                        'status' => $status,
                        'published_at' => $publishedAt,
                        'created_at' => $question->created_at ?? now(),
                        'updated_at' => $question->updated_at ?? now(),
                    ],
                );

                $questionVersionId = DB::table('question_versions')
                    ->where('question_id', $question->id)
                    ->where('version', 1)
                    ->value('id');
                $sortOrder = max(0, (int) $question->sort_order);

                while (isset($usedSortOrders[$sortOrder])) {
                    $sortOrder++;
                }

                $usedSortOrders[$sortOrder] = true;

                DB::table('quiz_version_questions')->updateOrInsert(
                    [
                        'quiz_version_id' => $quizVersion->id,
                        'question_id' => $question->id,
                    ],
                    [
                        'question_version_id' => $questionVersionId,
                        'sort_order' => $sortOrder,
                        'created_at' => $question->created_at ?? now(),
                        'updated_at' => $question->updated_at ?? now(),
                    ],
                );
            });
    }

    private function canonicalQuestionType(string $type): string
    {
        return match ($type) {
            'single', 'single_choice' => 'single',
            'multiple', 'multiple_choice' => 'multiple',
            'true_false' => 'true_false',
            default => throw new RuntimeException('Unsupported quiz question type: '.$type),
        };
    }

    private function backfillOptions(): void
    {
        DB::table('quiz_options')
            ->orderBy('id')
            ->chunkById(500, function ($options): void {
                foreach ($options as $option) {
                    $questionVersionId = DB::table('question_versions')
                        ->where('question_id', $option->quiz_question_id)
                        ->where('version', 1)
                        ->value('id');

                    DB::table('quiz_options')->where('id', $option->id)->update([
                        'question_version_id' => $questionVersionId,
                    ]);
                }
            });
    }

    private function backfillAttempts(): void
    {
        DB::table('quiz_attempts')
            ->orderBy('id')
            ->chunkById(500, function ($attempts): void {
                foreach ($attempts as $attempt) {
                    $quizVersion = DB::table('quiz_versions')
                        ->where('quiz_id', $attempt->quiz_id)
                        ->where('version', 1)
                        ->first();

                    if ($quizVersion->status === self::QUIZ_STATUS_DRAFT) {
                        $this->publishVersionForHistoricalAttempt($quizVersion);
                    }

                    DB::table('quiz_attempts')->where('id', $attempt->id)->update([
                        'quiz_version_id' => $quizVersion->id,
                        'status' => 'completed',
                    ]);
                }
            });
    }

    private function publishVersionForHistoricalAttempt(object $quizVersion): void
    {
        $publishedAt = $quizVersion->published_at ?? $quizVersion->updated_at ?? now();

        DB::table('quiz_versions')->where('id', $quizVersion->id)->update([
            'status' => self::QUIZ_STATUS_PUBLISHED,
            'published_at' => $publishedAt,
            'updated_at' => now(),
        ]);
        DB::table('question_versions')
            ->whereIn('id', DB::table('quiz_version_questions')
                ->where('quiz_version_id', $quizVersion->id)
                ->select('question_version_id'))
            ->update([
                'status' => self::QUIZ_STATUS_PUBLISHED,
                'published_at' => $publishedAt,
                'updated_at' => now(),
            ]);
        DB::table('quizzes')->where('id', $quizVersion->quiz_id)->update([
            'current_published_version_id' => $quizVersion->id,
            'current_draft_version_id' => null,
        ]);
    }

    private function backfillAttemptAnswers(): void
    {
        DB::table('quiz_attempt_answers')
            ->orderBy('id')
            ->chunkById(500, function ($attemptAnswers): void {
                foreach ($attemptAnswers as $attemptAnswer) {
                    $questionVersionId = DB::table('question_versions')
                        ->where('question_id', $attemptAnswer->question_id)
                        ->where('version', 1)
                        ->value('id');

                    DB::table('quiz_attempt_answers')->where('id', $attemptAnswer->id)->update([
                        'question_version_id' => $questionVersionId,
                    ]);
                }
            });
    }

    private function assertBackfillComplete(): void
    {
        $incomplete = [
            'options' => DB::table('quiz_options')->whereNull('question_version_id')->count(),
            'attempts' => DB::table('quiz_attempts')->whereNull('quiz_version_id')->count(),
            'attempt answers' => DB::table('quiz_attempt_answers')->whereNull('question_version_id')->count(),
        ];

        $missing = collect($incomplete)
            ->filter(fn (int $count): bool => $count > 0)
            ->map(fn (int $count, string $type): string => $type.'='.$count)
            ->implode(', ');

        if ($missing !== '') {
            throw new RuntimeException(
                'Quiz V1 backfill stopped because unresolved rows remain: '.$missing.'.',
            );
        }
    }
};
