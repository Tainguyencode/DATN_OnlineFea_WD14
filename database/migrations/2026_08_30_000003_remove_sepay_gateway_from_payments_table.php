<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('orders')->where('payment_method', 'sepay')->update(['payment_method' => 'momo']);
        DB::table('payments')->where('gateway', 'sepay')->update(['gateway' => 'momo']);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE payments MODIFY gateway ENUM('momo','bank_transfer') NOT NULL DEFAULT 'bank_transfer'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE payments MODIFY gateway ENUM('momo','bank_transfer','sepay') NOT NULL DEFAULT 'bank_transfer'");
        }
    }
};
