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
        Schema::create('active_sessions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Session hiện tại
            $table->string('session_id')->unique();

            // ID thiết bị (frontend sinh UUID)
            $table->string('device_id')->nullable();

            $table->ipAddress('ip_address')->nullable();

            $table->text('user_agent')->nullable();

            $table->string('browser')->nullable();

            $table->string('platform')->nullable();

            $table->string('device_name')->nullable();

            // Phiên còn hoạt động hay không
            $table->boolean('is_active')->default(true);

            // Lần cuối client ping lên server
            $table->timestamp('last_activity')->nullable();

            $table->timestamps();

            $table->index('user_id');
            $table->index('is_active');
            $table->index('device_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('active_sessions');
    }
};
