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
        Schema::create('video_access_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('lesson_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->ipAddress('ip_address')->nullable();

            $table->text('user_agent')->nullable();

            $table->string('browser')->nullable();

            $table->string('platform')->nullable();

            $table->string('device')->nullable();

            // Thời gian bắt đầu xem
            $table->timestamp('watch_started_at')->nullable();

            // Thời gian kết thúc
            $table->timestamp('watch_ended_at')->nullable();

            // Tổng thời lượng xem (giây)
            $table->unsignedInteger('watch_duration')->default(0);

            $table->timestamps();

            $table->index('user_id');
            $table->index('lesson_id');
            $table->index('watch_started_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_access_logs');
    }
};
