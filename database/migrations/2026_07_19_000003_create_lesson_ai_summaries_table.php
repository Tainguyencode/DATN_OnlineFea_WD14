<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('lesson_ai_summaries')) {
            return;
        }

        Schema::create('lesson_ai_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')
                ->constrained('lessons')
                ->cascadeOnDelete()
                ->unique();
            $table->longText('summary');
            $table->json('key_points')->nullable();
            $table->string('source_hash', 64);
            $table->string('model')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_ai_summaries');
    }
};
