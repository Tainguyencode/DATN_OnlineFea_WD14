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
        Schema::create('monthly_reward_logs', function (Blueprint $table) {
            $table->id();
            $table->string('period_key', 7); // e.g. "2026-08"
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('rank'); // 1, 2, 3
            $table->foreignId('coupon_id')->constrained('coupons')->cascadeOnDelete();
            $table->foreignId('user_coupon_id')->constrained('user_coupons')->cascadeOnDelete();
            $table->decimal('discount_value', 12, 2);
            $table->string('discount_type', 20)->default('fixed');
            $table->timestamp('granted_at')->useCurrent();
            $table->timestamps();

            // Unique constraint to guarantee anti-duplication
            $table->unique(['period_key', 'rank'], 'monthly_reward_logs_period_rank_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_reward_logs');
    }
};
