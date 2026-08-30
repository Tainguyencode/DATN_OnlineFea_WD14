<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table): void {
            $table->string('random_seed', 64)->nullable()->after('quiz_version_id');
            $table->unsignedInteger('focus_violation_count')->default(0)->after('random_seed');
            $table->json('question_ids')->nullable()->after('focus_violation_count');
        });

        Schema::table('quizzes', fn (Blueprint $table) => $table->unsignedInteger('question_count')->nullable()->after('max_attempts'));
        Schema::table('quiz_versions', fn (Blueprint $table) => $table->unsignedInteger('question_count')->nullable()->after('max_attempts'));

        Schema::table('submissions', function (Blueprint $table): void {
            $table->string('code_language', 30)->nullable()->after('content');
        });

        Schema::table('quiz_questions', fn (Blueprint $table) => $table->string('image_path')->nullable()->after('question'));
        Schema::table('question_versions', fn (Blueprint $table) => $table->string('image_path')->nullable()->after('question'));
    }

    public function down(): void
    {
        Schema::table('question_versions', fn (Blueprint $table) => $table->dropColumn('image_path'));
        Schema::table('quiz_questions', fn (Blueprint $table) => $table->dropColumn('image_path'));
        Schema::table('quiz_versions', fn (Blueprint $table) => $table->dropColumn('question_count'));
        Schema::table('quizzes', fn (Blueprint $table) => $table->dropColumn('question_count'));
        Schema::table('submissions', fn (Blueprint $table) => $table->dropColumn('code_language'));
        Schema::table('quiz_attempts', fn (Blueprint $table) => $table->dropColumn(['random_seed', 'focus_violation_count', 'question_ids']));
    }
};
