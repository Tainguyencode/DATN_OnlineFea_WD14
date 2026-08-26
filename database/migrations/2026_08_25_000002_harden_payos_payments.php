<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('gateway_order_code')->nullable()->after('gateway');
            $table->unique('order_id', 'payments_order_id_unique');
            $table->unique('gateway_order_code', 'payments_gateway_order_code_unique');
            $table->unique('transaction_id', 'payments_transaction_id_unique');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE refunds MODIFY status ENUM('pending', 'processing', 'approved', 'rejected') NOT NULL DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("UPDATE refunds SET status = 'pending' WHERE status = 'processing'");
            DB::statement("ALTER TABLE refunds MODIFY status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending'");
        }

        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique('payments_order_id_unique');
            $table->dropUnique('payments_gateway_order_code_unique');
            $table->dropUnique('payments_transaction_id_unique');
            $table->dropColumn('gateway_order_code');
        });
    }
};
