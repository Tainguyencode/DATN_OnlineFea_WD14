<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('courses')) {
            return;
        }

        Schema::table('courses', function (Blueprint $table) {
            if (! Schema::hasColumn('courses', 'target_audience')) {
                $table->text('target_audience')->nullable()->after('objectives');
            }
            if (! Schema::hasColumn('courses', 'requirements')) {
                $table->text('requirements')->nullable()->after('target_audience');
            }
            if (! Schema::hasColumn('courses', 'submission_count')) {
                $table->unsignedInteger('submission_count')->default(0)->after('submitted_at');
            }
            if (! Schema::hasColumn('courses', 'required_video_percent')) {
                $table->unsignedTinyInteger('required_video_percent')->nullable()->after('submission_count');
            }
            if (! Schema::hasColumn('courses', 'required_lesson_percent')) {
                $table->unsignedTinyInteger('required_lesson_percent')->nullable()->after('required_video_percent');
            }
            if (! Schema::hasColumn('courses', 'minimum_quiz_score')) {
                $table->unsignedTinyInteger('minimum_quiz_score')->nullable()->after('required_lesson_percent');
            }
            if (! Schema::hasColumn('courses', 'require_all_quizzes')) {
                $table->boolean('require_all_quizzes')->default(true)->after('minimum_quiz_score');
            }
            if (! Schema::hasColumn('courses', 'require_all_assignments')) {
                $table->boolean('require_all_assignments')->default(true)->after('require_all_quizzes');
            }
            if (! Schema::hasColumn('courses', 'certificate_enabled')) {
                $table->boolean('certificate_enabled')->default(true)->after('require_all_assignments');
            }
            if (! Schema::hasColumn('courses', 'suspended_at')) {
                $table->timestamp('suspended_at')->nullable()->after('published_at');
            }
            if (! Schema::hasColumn('courses', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('submitted_at');
            }
        });
    }

    public function down(): void
    {
        // Non-destructive workflow migration.
    }
};
