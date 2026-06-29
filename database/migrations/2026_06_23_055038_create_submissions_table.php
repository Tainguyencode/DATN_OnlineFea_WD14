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
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')
                ->constrained('assignments')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('file_path')
                ->nullable();

            $table->text('content')
                ->nullable();

            $table->unsignedInteger('score')
                ->nullable();

            $table->text('feedback')
                ->nullable();

            $table->enum('status', [
                'submitted',
                'graded',
                'returned'
            ])->default('submitted');

            $table->timestamp('submitted_at')
                ->nullable();

            $table->timestamp('graded_at')
                ->nullable();

            $table->timestamps();

            $table->unique(['assignment_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
