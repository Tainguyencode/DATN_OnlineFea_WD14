<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const ATTEMPT_QUIZ_FOREIGN = 'quiz_attempts_quiz_id_foreign';

    private const ATTEMPT_ANSWER_QUESTION_FOREIGN = 'quiz_attempt_answers_question_id_foreign';

    private const ATTEMPT_ANSWER_OPTION_FOREIGN = 'quiz_attempt_answers_answer_id_foreign';

    public function up(): void
    {
        $this->assertNoOrphanedHistoryReferences();
        $driver = DB::connection()->getDriverName();

        Schema::table('quiz_attempts', function (Blueprint $table) use ($driver): void {
            $table->dropForeign($driver === 'sqlite' ? ['quiz_id'] : self::ATTEMPT_QUIZ_FOREIGN);
            $table->foreign('quiz_id', self::ATTEMPT_QUIZ_FOREIGN)->references('id')->on('quizzes')->restrictOnDelete();
        });

        Schema::table('quiz_attempt_answers', function (Blueprint $table) use ($driver): void {
            $table->dropForeign($driver === 'sqlite' ? ['question_id'] : self::ATTEMPT_ANSWER_QUESTION_FOREIGN);
            $table->dropForeign($driver === 'sqlite' ? ['answer_id'] : self::ATTEMPT_ANSWER_OPTION_FOREIGN);
            $table->foreign('question_id', self::ATTEMPT_ANSWER_QUESTION_FOREIGN)->references('id')->on('quiz_questions')->restrictOnDelete();
            // Nullable legacy answer_id values remain valid; referenced options become immutable.
            $table->foreign('answer_id', self::ATTEMPT_ANSWER_OPTION_FOREIGN)->references('id')->on('quiz_options')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        if (DB::table('quiz_attempts')->exists() || DB::table('quiz_attempt_answers')->exists()) {
            throw new RuntimeException('Quiz historical FK hardening cannot be rolled back while quiz history exists.');
        }

        $driver = DB::connection()->getDriverName();
        Schema::table('quiz_attempts', function (Blueprint $table) use ($driver): void {
            $table->dropForeign($driver === 'sqlite' ? ['quiz_id'] : self::ATTEMPT_QUIZ_FOREIGN);
            $table->foreign('quiz_id', self::ATTEMPT_QUIZ_FOREIGN)->references('id')->on('quizzes')->cascadeOnDelete();
        });
        Schema::table('quiz_attempt_answers', function (Blueprint $table) use ($driver): void {
            $table->dropForeign($driver === 'sqlite' ? ['question_id'] : self::ATTEMPT_ANSWER_QUESTION_FOREIGN);
            $table->dropForeign($driver === 'sqlite' ? ['answer_id'] : self::ATTEMPT_ANSWER_OPTION_FOREIGN);
            $table->foreign('question_id', self::ATTEMPT_ANSWER_QUESTION_FOREIGN)->references('id')->on('quiz_questions')->cascadeOnDelete();
            $table->foreign('answer_id', self::ATTEMPT_ANSWER_OPTION_FOREIGN)->references('id')->on('quiz_options')->nullOnDelete();
        });
    }

    private function assertNoOrphanedHistoryReferences(): void
    {
        $checks = [
            ['quiz_attempts', 'quiz_id', 'quizzes', 'Quiz attempt'],
            ['quiz_attempt_answers', 'question_id', 'quiz_questions', 'Quiz attempt answer question'],
            ['quiz_attempt_answers', 'answer_id', 'quiz_options', 'Quiz attempt answer option'],
        ];

        foreach ($checks as [$table, $column, $relatedTable, $label]) {
            $orphan = DB::table($table.' as history')->leftJoin($relatedTable.' as related', 'related.id', '=', 'history.'.$column)->whereNotNull('history.'.$column)->whereNull('related.id')->value('history.id');
            if ($orphan !== null) {
                throw new RuntimeException(sprintf('%s FK hardening stopped: history row %d references a missing %s row.', $label, $orphan, $relatedTable));
            }
        }
    }
};
