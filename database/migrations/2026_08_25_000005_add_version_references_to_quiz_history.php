<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quiz_options', function (Blueprint $table): void {
            $table->foreignId('question_version_id')
                ->nullable()
                ->after('quiz_question_id')
                ->constrained('question_versions')
                ->restrictOnDelete();
        });

        Schema::table('quiz_attempts', function (Blueprint $table): void {
            $table->foreignId('quiz_version_id')
                ->nullable()
                ->after('quiz_id')
                ->constrained('quiz_versions')
                ->restrictOnDelete();
            $table->enum('status', ['in_progress', 'completed', 'expired'])
                ->default('completed')
                ->after('quiz_version_id');
        });

        Schema::table('quiz_attempt_answers', function (Blueprint $table): void {
            $table->foreignId('question_version_id')
                ->nullable()
                ->after('question_id')
                ->constrained('question_versions')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        $hasVersionReferences = DB::table('quiz_options')->whereNotNull('question_version_id')->exists()
            || DB::table('quiz_attempts')->whereNotNull('quiz_version_id')->exists()
            || DB::table('quiz_attempt_answers')->whereNotNull('question_version_id')->exists();

        if ($hasVersionReferences || DB::table('quiz_attempts')->exists()) {
            throw new RuntimeException(
                'Quiz history rollback stopped because attempt state or immutable version bindings exist.',
            );
        }

        Schema::table('quiz_attempt_answers', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('question_version_id');
        });

        Schema::table('quiz_attempts', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('quiz_version_id');
            $table->dropColumn('status');
        });

        Schema::table('quiz_options', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('question_version_id');
        });
    }
};
