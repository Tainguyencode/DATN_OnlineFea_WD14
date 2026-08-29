<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->uuid('idempotency_key')->nullable()->after('order_code');
            $table->unique(['user_id', 'idempotency_key'], 'orders_user_idempotency_unique');
        });

        Schema::table('withdrawals', function (Blueprint $table): void {
            $table->uuid('idempotency_key')->nullable()->after('user_id');
            $table->unique(['user_id', 'idempotency_key'], 'withdrawals_user_idempotency_unique');
        });

        Schema::table('refunds', function (Blueprint $table): void {
            $table->unique('order_id', 'refunds_order_id_unique');
        });

        Schema::table('user_points', function (Blueprint $table): void {
            $table->unique(['user_id', 'source', 'reference_id'], 'user_points_source_reference_unique');
        });
    }

    public function down(): void
    {
        Schema::table('user_points', fn (Blueprint $table) => $table->dropUnique('user_points_source_reference_unique'));
        Schema::table('refunds', fn (Blueprint $table) => $table->dropUnique('refunds_order_id_unique'));
        Schema::table('withdrawals', function (Blueprint $table): void {
            $table->dropUnique('withdrawals_user_idempotency_unique');
            $table->dropColumn('idempotency_key');
        });
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropUnique('orders_user_idempotency_unique');
            $table->dropColumn('idempotency_key');
        });
    }
};
