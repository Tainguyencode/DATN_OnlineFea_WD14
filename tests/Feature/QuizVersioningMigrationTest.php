<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Tests\TestCase;

class QuizVersioningMigrationTest extends TestCase
{
    private string $originalConnection;

    protected function setUp(): void
    {
        parent::setUp();

        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('The pdo_sqlite driver is required for isolated versioning migration tests.');
        }

        $this->originalConnection = DB::getDefaultConnection();
        config()->set('database.connections.quiz_versioning_sqlite', [
            ...config('database.connections.sqlite'),
            'database' => ':memory:',
            'foreign_key_constraints' => true,
        ]);
        DB::setDefaultConnection('quiz_versioning_sqlite');
        $this->createLegacySchema();
    }

    protected function tearDown(): void
    {
        if (isset($this->originalConnection)) {
            DB::setDefaultConnection($this->originalConnection);
            DB::purge('quiz_versioning_sqlite');
            config()->offsetUnset('database.connections.quiz_versioning_sqlite');
        }

        parent::tearDown();
    }

    public function test_empty_legacy_database_receives_the_complete_versioning_schema(): void
    {
        $this->runAllVersioningMigrations();

        $this->assertTrue(Schema::hasTable('quiz_versions'));
        $this->assertTrue(Schema::hasTable('question_versions'));
        $this->assertTrue(Schema::hasTable('quiz_version_questions'));
        $this->assertTrue(Schema::hasColumns('quizzes', [
            'current_published_version_id',
            'current_draft_version_id',
        ]));
        $this->assertTrue(Schema::hasColumns('quiz_options', ['quiz_question_id', 'question_version_id']));
        $this->assertTrue(Schema::hasColumns('quiz_attempts', ['quiz_id', 'quiz_version_id', 'status']));
        $this->assertTrue(Schema::hasColumns('quiz_attempt_answers', [
            'question_id',
            'question_version_id',
            'answer_id',
        ]));

        $this->backfillMigration()->down();
        foreach (array_reverse($this->schemaMigrationPaths()) as $migration) {
            (require database_path('migrations/'.$migration))->down();
        }

        $this->assertFalse(Schema::hasTable('quiz_versions'));
        $this->assertFalse(Schema::hasTable('question_versions'));
        $this->assertFalse(Schema::hasTable('quiz_version_questions'));
        $this->assertFalse(Schema::hasColumn('quizzes', 'current_published_version_id'));
        $this->assertFalse(Schema::hasColumn('quiz_attempts', 'quiz_version_id'));
    }

    public function test_existing_tree_attempts_and_classifications_are_backfilled_without_changing_ids(): void
    {
        $instructorId = DB::table('users')->insertGetId([
            'instructor_status' => 'approved',
            'is_active' => true,
            'account_status' => null,
        ]);
        $attemptQuiz = $this->insertLegacyQuiz(
            $instructorId,
            courseStatus: 'draft',
            coursePublished: false,
            lessonStatus: 'draft',
            quizActive: false,
        );
        $visibleQuiz = $this->insertLegacyQuiz(
            $instructorId,
            courseStatus: 'approved',
            coursePublished: false,
            lessonStatus: 'draft',
            quizActive: false,
        );
        $draftQuiz = $this->insertLegacyQuiz(
            $instructorId,
            courseStatus: 'draft',
            coursePublished: false,
            lessonStatus: 'draft',
            quizActive: false,
        );
        [$questionIds, $optionIds] = $this->insertQuestionTree($attemptQuiz, duplicateSortOrder: true);
        $draftQuestionId = DB::table('quiz_questions')->insertGetId([
            'quiz_id' => $draftQuiz,
            'question' => 'Draft only question',
            'type' => 'single',
            'points' => 1,
            'explanation' => null,
            'sort_order' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $attemptId = DB::table('quiz_attempts')->insertGetId([
            'user_id' => $instructorId,
            'quiz_id' => $attemptQuiz,
            'score' => 2,
            'total_score' => 2,
            'percent' => 100,
            'passed' => true,
            'answers' => json_encode([]),
            'started_at' => now()->subMinute(),
            'completed_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $answerId = DB::table('quiz_attempt_answers')->insertGetId([
            'quiz_attempt_id' => $attemptId,
            'question_id' => $questionIds[0],
            'answer_id' => $optionIds[0],
            'is_correct' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $nullAnswerId = DB::table('quiz_attempt_answers')->insertGetId([
            'quiz_attempt_id' => $attemptId,
            'question_id' => $questionIds[1],
            'answer_id' => null,
            'is_correct' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->runAllVersioningMigrations();

        $attemptVersion = DB::table('quiz_versions')->where('quiz_id', $attemptQuiz)->first();
        $visibleVersion = DB::table('quiz_versions')->where('quiz_id', $visibleQuiz)->first();
        $draftVersion = DB::table('quiz_versions')->where('quiz_id', $draftQuiz)->first();
        $this->assertSame('published', $attemptVersion->status);
        $this->assertSame('published', $visibleVersion->status);
        $this->assertSame('draft', $draftVersion->status);
        $this->assertSame($attemptVersion->id, DB::table('quizzes')
            ->where('id', $attemptQuiz)->value('current_published_version_id'));
        $this->assertSame($draftVersion->id, DB::table('quizzes')
            ->where('id', $draftQuiz)->value('current_draft_version_id'));

        $this->assertSame(2, DB::table('question_versions')
            ->whereIn('question_id', $questionIds)->count());
        $this->assertSame('draft', DB::table('question_versions')
            ->where('question_id', $draftQuestionId)->value('status'));
        $this->assertSame(2, DB::table('quiz_version_questions')
            ->where('quiz_version_id', $attemptVersion->id)->count());
        $this->assertSame([5, 6], DB::table('quiz_version_questions')
            ->where('quiz_version_id', $attemptVersion->id)
            ->orderBy('sort_order')
            ->pluck('sort_order')
            ->map(fn ($value): int => (int) $value)
            ->all());

        $this->assertSame($optionIds, DB::table('quiz_options')
            ->whereIn('id', $optionIds)
            ->orderBy('id')
            ->pluck('id')
            ->map(fn ($value): int => (int) $value)
            ->all());
        $this->assertSame(0, DB::table('quiz_options')->whereNull('question_version_id')->count());

        $attempt = DB::table('quiz_attempts')->where('id', $attemptId)->first();
        $this->assertSame($attemptQuiz, (int) $attempt->quiz_id);
        $this->assertSame((int) $attemptVersion->id, (int) $attempt->quiz_version_id);
        $this->assertSame('completed', $attempt->status);
        $this->assertSame(2, (int) $attempt->score);
        $this->assertSame(100.0, (float) $attempt->percent);

        $answer = DB::table('quiz_attempt_answers')->where('id', $answerId)->first();
        $this->assertSame($questionIds[0], (int) $answer->question_id);
        $this->assertNotNull($answer->question_version_id);
        $this->assertSame($optionIds[0], (int) $answer->answer_id);
        $this->assertTrue((bool) $answer->is_correct);
        $this->assertNull(DB::table('quiz_attempt_answers')->where('id', $nullAnswerId)->value('answer_id'));
        $this->assertNotNull(DB::table('quiz_attempt_answers')
            ->where('id', $nullAnswerId)->value('question_version_id'));

        $this->runBackfillMigration();
        $this->assertSame(3, DB::table('quiz_versions')->count());
        $this->assertSame(3, DB::table('question_versions')->count());
        $this->assertSame(3, DB::table('quiz_version_questions')->count());

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('irreversible');
        $this->backfillMigration()->down();
    }

    public function test_unresolved_attempt_answer_fails_before_any_version_data_is_written(): void
    {
        $instructorId = DB::table('users')->insertGetId([
            'instructor_status' => 'approved',
            'is_active' => true,
            'account_status' => null,
        ]);
        $quizId = $this->insertLegacyQuiz(
            $instructorId,
            courseStatus: 'draft',
            coursePublished: false,
            lessonStatus: 'draft',
            quizActive: false,
        );
        $attemptId = DB::table('quiz_attempts')->insertGetId([
            'user_id' => $instructorId,
            'quiz_id' => $quizId,
            'score' => 0,
            'total_score' => 0,
            'percent' => 0,
            'passed' => false,
            'answers' => null,
            'started_at' => now(),
            'completed_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('quiz_attempt_answers')->insert([
            'quiz_attempt_id' => $attemptId,
            'question_id' => 999999,
            'answer_id' => null,
            'is_correct' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->runSchemaVersioningMigrations();

        try {
            $this->runBackfillMigration();
            $this->fail('Backfill accepted an attempt answer with unrecoverable question history.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('cannot resolve', $exception->getMessage());
            $this->assertSame(0, DB::table('quiz_versions')->count());
            $this->assertSame(0, DB::table('question_versions')->count());
        }
    }

    private function createLegacySchema(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('instructor_status')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('account_status')->nullable();
        });

        Schema::create('courses', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('instructor_id')->nullable();
            $table->string('status')->default('draft');
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
        });

        Schema::create('lessons', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('course_id')->nullable();
            $table->string('status')->default('draft');
        });

        Schema::create('quizzes', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('lesson_id')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('pass_score')->default(70);
            $table->unsignedInteger('time_limit_minutes')->nullable();
            $table->unsignedInteger('max_attempts')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('quiz_questions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('quiz_id');
            $table->text('question');
            $table->enum('type', ['single', 'multiple', 'true_false'])->default('single');
            $table->unsignedInteger('points')->default(1);
            $table->text('explanation')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('quiz_options', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('quiz_question_id');
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('quiz_attempts', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('quiz_id');
            $table->unsignedInteger('score')->default(0);
            $table->unsignedInteger('total_score')->default(0);
            $table->decimal('percent', 5, 2)->default(0);
            $table->boolean('passed')->default(false);
            $table->json('answers')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('quiz_attempt_answers', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('quiz_attempt_id');
            $table->unsignedBigInteger('question_id');
            $table->unsignedBigInteger('answer_id')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
        });
    }

    private function insertLegacyQuiz(
        int $instructorId,
        string $courseStatus,
        bool $coursePublished,
        string $lessonStatus,
        bool $quizActive,
    ): int {
        $courseId = DB::table('courses')->insertGetId([
            'instructor_id' => $instructorId,
            'status' => $courseStatus,
            'is_published' => $coursePublished,
            'published_at' => $coursePublished || $courseStatus === 'approved' ? now() : null,
        ]);
        $lessonId = DB::table('lessons')->insertGetId([
            'course_id' => $courseId,
            'status' => $lessonStatus,
        ]);

        return DB::table('quizzes')->insertGetId([
            'lesson_id' => $lessonId,
            'title' => 'Legacy quiz '.$lessonId,
            'description' => 'Legacy description',
            'pass_score' => 70,
            'time_limit_minutes' => 30,
            'max_attempts' => 3,
            'is_active' => $quizActive,
            'created_at' => now()->subDay(),
            'updated_at' => now(),
        ]);
    }

    /**
     * @return array{0: array<int, int>, 1: array<int, int>}
     */
    private function insertQuestionTree(int $quizId, bool $duplicateSortOrder): array
    {
        $questionIds = [];
        $optionIds = [];

        foreach (['First legacy question', 'Second legacy question'] as $index => $text) {
            $questionId = DB::table('quiz_questions')->insertGetId([
                'quiz_id' => $quizId,
                'question' => $text,
                'type' => $index === 0 ? 'single' : 'multiple',
                'points' => $index + 1,
                'explanation' => 'Explanation '.$index,
                'sort_order' => $duplicateSortOrder ? 5 : $index,
                'created_at' => now()->subDay(),
                'updated_at' => now(),
            ]);
            $questionIds[] = $questionId;

            foreach (range(0, 2) as $sortOrder) {
                $optionIds[] = DB::table('quiz_options')->insertGetId([
                    'quiz_question_id' => $questionId,
                    'option_text' => 'Option '.$index.'-'.$sortOrder,
                    'is_correct' => $sortOrder === 0,
                    'sort_order' => $sortOrder,
                    'created_at' => now()->subDay(),
                    'updated_at' => now(),
                ]);
            }
        }

        return [$questionIds, $optionIds];
    }

    private function runAllVersioningMigrations(): void
    {
        $this->runSchemaVersioningMigrations();
        $this->runBackfillMigration();
    }

    private function runSchemaVersioningMigrations(): void
    {
        foreach ($this->schemaMigrationPaths() as $migration) {
            (require database_path('migrations/'.$migration))->up();
        }
    }

    /**
     * @return array<int, string>
     */
    private function schemaMigrationPaths(): array
    {
        return [
            '2026_08_25_000003_create_quiz_versions_and_version_pointers.php',
            '2026_08_25_000004_create_question_versions_and_quiz_version_questions.php',
            '2026_08_25_000005_add_version_references_to_quiz_history.php',
        ];
    }

    private function runBackfillMigration(): void
    {
        $this->backfillMigration()->up();
    }

    private function backfillMigration(): object
    {
        return require database_path('migrations/2026_08_25_000006_backfill_quiz_versioning_v1.php');
    }
}
