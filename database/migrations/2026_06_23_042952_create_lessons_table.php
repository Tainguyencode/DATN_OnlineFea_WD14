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

            $table->longText('content')->nullable();

            $table->enum('type', [
                'video',
                'document',
                'quiz',
                'assignment',
            ])->default('video');

            /*
            |--------------------------------------------------------------------------
            | Video gốc
            |--------------------------------------------------------------------------
            */

            // File upload ban đầu
            $table->string('video_path')->nullable();

            // URL nếu dùng Cloudflare, S3...
            $table->string('video_url')->nullable();

            // Tên file gốc
            $table->string('video_original_name')->nullable();

            // Dung lượng (byte)
            $table->unsignedBigInteger('video_size')->default(0);

            // Mime type
            $table->string('video_mime')->nullable();

            /*
            |--------------------------------------------------------------------------
            | HLS
            |--------------------------------------------------------------------------
            */

            // playlist.m3u8
            $table->string('hls_playlist')->nullable();

            // thư mục chứa .ts
            $table->string('hls_path')->nullable();

            $table->enum('hls_status', [
                'pending',
                'processing',
                'completed',
                'failed'
            ])->default('pending');

            // Lưu lỗi FFmpeg nếu có
            $table->text('processing_error')->nullable();

            // Thời gian convert xong
            $table->timestamp('processed_at')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Video info
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('duration_seconds')->default(0);

            $table->boolean('is_preview')->default(false);

            $table->unsignedInteger('sort_order')->default(0);

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
