<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('content_updates', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // course, chapter, lesson
            $table->unsignedBigInteger('entity_id')->nullable(); // nullable for create action
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('action'); // create, update, delete, reorder
            $table->json('payload');
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('rejection_reason')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['course_id', 'status']);
            $table->index(['type', 'entity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_updates');
    }
};
