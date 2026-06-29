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
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
           
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('quiz_id');

            $table->unsignedInteger('score')->default(0);
            $table->boolean('passed')->default(false);

            $table->json('answers')->nullable();

            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            // indexes
            $table->index('user_id', 'quiz_attempts_user_id_foreign');
            $table->index('quiz_id', 'quiz_attempts_quiz_id_foreign');

            // foreign keys
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('quiz_id')
                ->references('id')->on('quizzes')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};
