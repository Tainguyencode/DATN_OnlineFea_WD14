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
        Schema::create('live_sessions', function (Blueprint $table) {
            $table->id();
             $table->foreignId('course_id')
                ->constrained('courses')
                ->cascadeOnDelete();

            $table->foreignId('instructor_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('title');

            $table->text('description')->nullable();

            $table->string('stream_url')->nullable();

            $table->timestamp('scheduled_at');

            $table->timestamp('started_at')->nullable();

            $table->timestamp('ended_at')->nullable();

            $table->enum('status', [
                'scheduled',
                'live',
                'ended',
                'cancelled'
            ])->default('scheduled');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_sessions');
    }
};
