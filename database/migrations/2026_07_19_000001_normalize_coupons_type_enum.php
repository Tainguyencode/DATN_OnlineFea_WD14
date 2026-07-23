<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Align coupons.type with app code: percent|fixed (legacy DB used percentage).
     */
    public function up(): void
    {
        if (! Schema::hasTable('coupons') || ! Schema::hasColumn('coupons', 'type')) {
            return;
        }

        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        // Expand enum first so legacy "percentage" rows can be rewritten.
        DB::statement("ALTER TABLE coupons MODIFY COLUMN type ENUM('percentage', 'percent', 'fixed') NOT NULL DEFAULT 'percent'");

        DB::table('coupons')
            ->where('type', 'percentage')
            ->update(['type' => 'percent']);

        DB::statement("ALTER TABLE coupons MODIFY COLUMN type ENUM('percent', 'fixed') NOT NULL DEFAULT 'percent'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('coupons') || ! Schema::hasColumn('coupons', 'type')) {
            return;
        }

        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE coupons MODIFY COLUMN type ENUM('percentage', 'percent', 'fixed') NOT NULL DEFAULT 'percentage'");

        DB::table('coupons')
            ->where('type', 'percent')
            ->update(['type' => 'percentage']);

        DB::statement("ALTER TABLE coupons MODIFY COLUMN type ENUM('percentage', 'fixed') NOT NULL DEFAULT 'percentage'");
    }
};
