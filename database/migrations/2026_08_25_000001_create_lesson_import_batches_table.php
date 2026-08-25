<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_import_batches', function (Blueprint $table): void {
            $table->id();
            $table->string('token', 64)->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('section_id')->constrained('course_sections')->cascadeOnDelete();

            $table->string('original_filename');
            $table->char('file_sha256', 64)->index();
            $table->unsignedSmallInteger('template_version');

            $table->json('canonical_payload');
            $table->json('validation_report');

            $table->unsignedSmallInteger('row_count')->default(0);
            $table->unsignedSmallInteger('valid_count')->default(0);
            $table->unsignedSmallInteger('warning_count')->default(0);
            $table->unsignedSmallInteger('error_count')->default(0);

            $table->string('status', 32)->index();
            $table->unsignedSmallInteger('imported_count')->default(0);
            $table->timestamp('expires_at')->index();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_import_batches');
    }
};
