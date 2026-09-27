<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('version_number');
            $table->string('status', 24)->index();
            $table->string('title');
            $table->string('slug')->nullable();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->text('objectives')->nullable();
            $table->text('requirements')->nullable();
            $table->text('target_audience')->nullable();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('level', 32)->nullable();
            $table->string('language', 16)->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('discount_price', 12, 2)->nullable();
            $table->decimal('sale_price', 12, 2)->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('preview_video')->nullable();
            $table->json('tags')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamp('superseded_at')->nullable();
            $table->timestamps();
            $table->unique(['course_id', 'version_number']);
        });

        Schema::create('course_section_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_section_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('version_number');
            $table->string('status', 24)->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamp('superseded_at')->nullable();
            $table->timestamps();
            $table->unique(['course_section_id', 'version_number']);
        });

        Schema::create('lesson_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('version_number');
            $table->string('status', 24)->index();
            $table->foreignId('section_id')->nullable()->constrained('course_sections')->nullOnDelete();
            $table->unsignedBigInteger('legacy_chapter_id')->nullable();
            $table->string('title');
            $table->string('type', 32);
            $table->longText('content')->nullable();
            $table->string('document_file')->nullable();
            $table->string('video_url')->nullable();
            $table->string('video_path')->nullable();
            $table->string('original_video_key')->nullable();
            $table->string('hls_manifest_key')->nullable();
            $table->string('hls_playlist')->nullable();
            $table->string('hls_path')->nullable();
            $table->string('video_original_name')->nullable();
            $table->string('video_mime')->nullable();
            $table->unsignedBigInteger('video_size')->default(0);
            $table->unsignedInteger('duration_seconds')->default(0);
            $table->boolean('is_preview')->default(false);
            $table->boolean('is_required')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->json('attachments')->nullable();
            $table->json('subtitles')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamp('superseded_at')->nullable();
            $table->timestamps();
            $table->unique(['lesson_id', 'version_number']);
        });

        Schema::create('assignment_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('version_number');
            $table->string('status', 24)->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->unsignedInteger('due_days')->nullable();
            $table->unsignedInteger('max_score')->default(100);
            $table->unsignedInteger('passing_score')->default(70);
            $table->boolean('is_required')->default(true);
            $table->string('allowed_file_types')->nullable();
            $table->unsignedInteger('maximum_file_size')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamp('superseded_at')->nullable();
            $table->timestamps();
            $table->unique(['assignment_id', 'version_number']);
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->foreignId('published_version_id')->nullable()->constrained('course_versions')->restrictOnDelete();
            $table->foreignId('draft_version_id')->nullable()->constrained('course_versions')->restrictOnDelete();
        });
        Schema::table('course_sections', function (Blueprint $table) {
            $table->foreignId('published_version_id')->nullable()->constrained('course_section_versions')->restrictOnDelete();
            $table->foreignId('draft_version_id')->nullable()->constrained('course_section_versions')->restrictOnDelete();
        });
        Schema::table('lessons', function (Blueprint $table) {
            $table->foreignId('published_version_id')->nullable()->constrained('lesson_versions')->restrictOnDelete();
            $table->foreignId('draft_version_id')->nullable()->constrained('lesson_versions')->restrictOnDelete();
        });
        Schema::table('assignments', function (Blueprint $table) {
            $table->foreignId('published_version_id')->nullable()->constrained('assignment_versions')->restrictOnDelete();
            $table->foreignId('draft_version_id')->nullable()->constrained('assignment_versions')->restrictOnDelete();
        });
        Schema::table('submissions', function (Blueprint $table) {
            $table->foreignId('assignment_version_id')->nullable()->constrained('assignment_versions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('submissions', fn (Blueprint $table) => $table->dropConstrainedForeignId('assignment_version_id'));
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('published_version_id');
            $table->dropConstrainedForeignId('draft_version_id');
        });
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropConstrainedForeignId('published_version_id');
            $table->dropConstrainedForeignId('draft_version_id');
        });
        Schema::table('course_sections', function (Blueprint $table) {
            $table->dropConstrainedForeignId('published_version_id');
            $table->dropConstrainedForeignId('draft_version_id');
        });
        Schema::table('courses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('published_version_id');
            $table->dropConstrainedForeignId('draft_version_id');
        });
        Schema::dropIfExists('assignment_versions');
        Schema::dropIfExists('lesson_versions');
        Schema::dropIfExists('course_section_versions');
        Schema::dropIfExists('course_versions');
    }
};
