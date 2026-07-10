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
        Schema::create('lesson_progress', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('lesson_id')
                ->constrained('lessons')
                ->cascadeOnDelete();

            $table->unsignedInteger('watched_seconds')
                ->default(0);

            $table->boolean('is_completed')
                ->default(false);

            $table->timestamp('completed_at')
                ->nullable();
            
            $table->timestamps();

            // Unique constraint: Một user chỉ có một progress record cho mỗi lesson
            $table->unique(['user_id', 'lesson_id']);

            // Index cho các query thường dùng
            $table->index('user_id');
            $table->index('lesson_id');
            $table->index('is_completed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_progress');
    }
};
