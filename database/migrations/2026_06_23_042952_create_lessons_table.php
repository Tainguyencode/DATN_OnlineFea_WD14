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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
             $table->foreignId('chapter_id')
                  ->constrained('chapters')
                  ->cascadeOnDelete();

            $table->string('title');

            $table->text('content')
                  ->nullable();

            $table->enum('type', [
                'video',
                'document',
                'quiz',
                'assignment'
            ])->default('video');

            $table->string('video_url')
                  ->nullable();

            $table->unsignedInteger('duration_seconds')
                  ->default(0);

            $table->boolean('is_preview')
                  ->default(false);

            $table->unsignedInteger('sort_order')
                  ->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
