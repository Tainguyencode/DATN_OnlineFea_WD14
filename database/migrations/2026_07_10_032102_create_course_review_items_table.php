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
        Schema::create('course_review_items', function (Blueprint $table) {
              $table->id();

            $table->foreignId('course_review_id')
                ->constrained('course_reviews')
                ->cascadeOnDelete();

            $table->enum('item_key', [
                'course_information',
                'thumbnail',
                'description',
                'objectives',
                'category',
                'price',
                'lesson_count',
                'video_duration',
                'video_quality',
                'attachments',
                'copyright'
            ]);

            $table->enum('status', [
                'pass',
                'fail'
            ]);

            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_review_items');
    }
};
