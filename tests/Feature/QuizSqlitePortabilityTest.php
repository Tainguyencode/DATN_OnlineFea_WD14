<?php

namespace Tests\Feature;

use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class QuizSqlitePortabilityTest extends TestCase
{
    public function test_quiz_foundation_migration_supports_true_false_crud_on_sqlite(): void
    {
        $originalConnection = DB::getDefaultConnection();
        config()->set('database.connections.quiz_foundation_sqlite', [
            ...config('database.connections.sqlite'),
            'database' => ':memory:',
            'foreign_key_constraints' => false,
        ]);
        DB::setDefaultConnection('quiz_foundation_sqlite');

        try {
            $this->createPreFoundationSchema();

            $migration = require database_path('migrations/2026_08_25_000002_add_quiz_authoring_foundation.php');
            $migration->up();

            $quizId = DB::table('quizzes')->insertGetId(['lesson_id' => 10]);
            $questionId = DB::table('quiz_questions')->insertGetId([
                'quiz_id' => $quizId,
                'question' => 'PHP is a language?',
                'type' => 'true_false',
            ]);

            $this->assertSame('true_false', DB::table('quiz_questions')->where('id', $questionId)->value('type'));

            DB::table('quiz_questions')->where('id', $questionId)->update(['question' => 'Updated question']);
            $this->assertSame('Updated question', DB::table('quiz_questions')->where('id', $questionId)->value('question'));

            try {
                $migration->down();
                $this->fail('Rollback should stop while true_false data still exists.');
            } catch (\RuntimeException $exception) {
                $this->assertStringContainsString('true_false questions exist', $exception->getMessage());
                $this->assertTrue(Schema::hasColumn('lesson_import_batches', 'result_payload'));
            }

            DB::table('quiz_questions')->where('id', $questionId)->delete();
            $this->assertFalse(DB::table('quiz_questions')->where('id', $questionId)->exists());
            $this->assertTrue(Schema::hasColumn('lesson_import_batches', 'result_payload'));

            try {
                DB::table('quizzes')->insert(['lesson_id' => 10]);
                $this->fail('The unique lesson_id constraint did not reject a duplicate quiz.');
            } catch (QueryException) {
                $this->assertSame(1, DB::table('quizzes')->where('lesson_id', 10)->count());
            }

            $migration->down();
            $this->assertFalse(Schema::hasColumn('lesson_import_batches', 'result_payload'));
            DB::table('quizzes')->insert(['lesson_id' => 10]);
            $this->assertSame(2, DB::table('quizzes')->where('lesson_id', 10)->count());
        } finally {
            DB::setDefaultConnection($originalConnection);
            DB::purge('quiz_foundation_sqlite');
            config()->offsetUnset('database.connections.quiz_foundation_sqlite');
        }
    }

    public function test_quiz_foundation_migration_stops_before_mutation_when_duplicate_quizzes_exist(): void
    {
        $originalConnection = DB::getDefaultConnection();
        config()->set('database.connections.quiz_foundation_duplicates', [
            ...config('database.connections.sqlite'),
            'database' => ':memory:',
            'foreign_key_constraints' => false,
        ]);
        DB::setDefaultConnection('quiz_foundation_duplicates');

        try {
            $this->createPreFoundationSchema();
            DB::table('quizzes')->insert([['lesson_id' => 10], ['lesson_id' => 10]]);
            $migration = require database_path('migrations/2026_08_25_000002_add_quiz_authoring_foundation.php');

            try {
                $migration->up();
                $this->fail('Migration should stop for duplicate quizzes.');
            } catch (\RuntimeException $exception) {
                $this->assertStringContainsString('lesson_id=10 (2 quizzes)', $exception->getMessage());
                $this->assertFalse(Schema::hasColumn('lesson_import_batches', 'result_payload'));
            }
        } finally {
            DB::setDefaultConnection($originalConnection);
            DB::purge('quiz_foundation_duplicates');
            config()->offsetUnset('database.connections.quiz_foundation_duplicates');
        }
    }

    private function createPreFoundationSchema(): void
    {
        Schema::create('quizzes', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('lesson_id');
        });

        Schema::create('quiz_questions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('quiz_id');
            $table->text('question');
            $table->enum('type', ['single', 'multiple'])->default('single');
        });

        Schema::create('lesson_import_batches', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('section_id');
            $table->char('file_sha256', 64);
            $table->json('validation_report');
            $table->string('status', 32);
        });
    }
}
