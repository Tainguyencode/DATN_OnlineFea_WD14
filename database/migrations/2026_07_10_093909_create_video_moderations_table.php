<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations. BẢNG LƯU Ý: DÙNG CHO KIỂM DUYỆT VIDEO, KHÔNG DÙNG CHO KIỂM DUYỆT ẢNH (ẢNH SẼ DÙNG BẢNG IMAGE_MODERATIONS)
     */
    public function up(): void
    {
        Schema::create('video_moderations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();

            $table->boolean('violence')->default(false);
            $table->boolean('adult')->default(false);
            $table->boolean('weapon')->default(false);

            $table->boolean('tiktok_logo')->default(false);
            $table->boolean('youtube_logo')->default(false);
            $table->boolean('watermark')->default(false);

            $table->string('copyright_risk');

            $table->text('summary')->nullable();

            $table->json('details')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_moderations');
    }
};
