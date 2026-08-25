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
        Schema::table('user_coupons', function (Blueprint $table) {
            $table->string('source', 50)->default('saved')->after('coupon_id');
            $table->string('reason')->nullable()->after('source');
            $table->foreignId('granted_by')->nullable()->after('reason')->constrained('users')->nullOnDelete();
            $table->timestamp('granted_at')->nullable()->after('granted_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_coupons', function (Blueprint $table) {
            $table->dropForeign(['granted_by']);
            $table->dropColumn(['source', 'reason', 'granted_by', 'granted_at']);
        });
    }
};
