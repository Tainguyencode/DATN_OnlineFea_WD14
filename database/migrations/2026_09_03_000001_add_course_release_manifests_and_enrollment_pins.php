<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_versions', function (Blueprint $table) {
            // NULL identifies legacy metadata snapshots, not complete releases.
            $table->timestamp('manifest_built_at')->nullable();
            $table->unsignedTinyInteger('required_video_percent')->nullable();
            $table->unsignedTinyInteger('required_lesson_percent')->nullable();
            $table->unsignedTinyInteger('minimum_quiz_score')->nullable();
            $table->boolean('require_all_quizzes')->nullable();
            $table->boolean('require_all_assignments')->nullable();
            $table->boolean('certificate_enabled')->nullable();
            $table->index(['course_id', 'published_at'], 'course_release_history');
        });
        Schema::create('course_version_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_version_id')->constrained()->restrictOnDelete();
            $table->foreignId('course_section_id')->constrained()->restrictOnDelete();
            $table->foreignId('course_section_version_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('sort_order');
            $table->timestamps();
            $table->unique(['course_version_id', 'course_section_id'], 'release_section_unique');
            $table->index(['course_version_id', 'sort_order'], 'release_section_order');
        });
        Schema::create('course_version_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_version_id')->constrained()->restrictOnDelete();
            $table->foreignId('course_section_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('lesson_id')->constrained()->restrictOnDelete();
            $table->foreignId('lesson_version_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('sort_order');
            $table->boolean('is_required');
            $table->foreignId('quiz_version_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('assignment_version_id')->nullable()->constrained()->restrictOnDelete();
            $table->timestamps();
            $table->unique(['course_version_id', 'lesson_id'], 'release_lesson_unique');
            $table->index(['course_version_id', 'course_section_id', 'sort_order'], 'release_lesson_order');
            $table->foreign(['course_version_id', 'course_section_id'], 'release_lesson_section_fk')
                ->references(['course_version_id', 'course_section_id'])->on('course_version_sections')->restrictOnDelete();
        });
        Schema::table('enrollments', function (Blueprint $table) {
            $table->foreignId('course_version_id')->nullable()->constrained()->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', fn (Blueprint $table) => $table->dropConstrainedForeignId('course_version_id'));
        Schema::dropIfExists('course_version_lessons');
        Schema::dropIfExists('course_version_sections');
        Schema::table('course_versions', function (Blueprint $table) {
            $table->dropIndex('course_release_history');
            $table->dropColumn(['manifest_built_at', 'required_video_percent', 'required_lesson_percent', 'minimum_quiz_score', 'require_all_quizzes', 'require_all_assignments', 'certificate_enabled']);
        });
    }
};
