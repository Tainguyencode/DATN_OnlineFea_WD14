<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_versions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('question_id')
                ->constrained('quiz_questions')
                ->restrictOnDelete();
            $table->unsignedInteger('version');
            $table->text('question');
            $table->enum('type', ['single', 'multiple', 'true_false']);
            $table->unsignedInteger('points');
            $table->text('explanation')->nullable();
            $table->enum('status', ['draft', 'published']);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->unique(['question_id', 'version']);
            $table->index(['question_id', 'status']);
            $table->unique(['id', 'question_id'], 'question_versions_id_question_unique');
        });

        Schema::create('quiz_version_questions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('quiz_version_id')
                ->constrained('quiz_versions')
                ->restrictOnDelete();
            $table->unsignedBigInteger('question_id');
            $table->unsignedBigInteger('question_version_id');
            $table->unsignedInteger('sort_order');
            $table->timestamps();

            $table->foreign('question_id')
                ->references('id')
                ->on('quiz_questions')
                ->restrictOnDelete();
            $table->foreign(
                ['question_version_id', 'question_id'],
                'quiz_version_questions_version_identity_foreign',
            )
                ->references(['id', 'question_id'])
                ->on('question_versions')
                ->restrictOnDelete();

            $table->unique(['quiz_version_id', 'question_id']);
            $table->unique(['quiz_version_id', 'sort_order']);
            $table->index('question_version_id');
        });
    }

    public function down(): void
    {
        if (DB::table('quiz_version_questions')->exists() || DB::table('question_versions')->exists()) {
            throw new RuntimeException(
                'Question versioning rollback stopped because versioned question data exists.',
            );
        }

        Schema::dropIfExists('quiz_version_questions');
        Schema::dropIfExists('question_versions');
    }
};
