<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::table('payments')->where('gateway', 'vnpay')->update(['gateway' => 'bank_transfer']);
            DB::statement("ALTER TABLE payments MODIFY gateway ENUM('momo','bank_transfer','sepay') NOT NULL DEFAULT 'bank_transfer'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::table('payments')->where('gateway', 'sepay')->update(['gateway' => 'bank_transfer']);
            DB::statement("ALTER TABLE payments MODIFY gateway ENUM('momo','vnpay','bank_transfer') NOT NULL DEFAULT 'bank_transfer'");
        }
    }
};
