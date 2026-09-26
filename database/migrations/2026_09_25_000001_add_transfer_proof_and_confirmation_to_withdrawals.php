<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('withdrawals', function (Blueprint $table): void {
            $table->string('transfer_proof_path')->nullable()->after('transaction_ref');
            $table->string('receipt_status', 20)->default('pending')->after('transfer_proof_path');
            $table->timestamp('receipt_confirmed_at')->nullable()->after('receipt_status');
        });
    }

    public function down(): void
    {
        Schema::table('withdrawals', function (Blueprint $table): void {
            $table->dropColumn(['transfer_proof_path', 'receipt_status', 'receipt_confirmed_at']);
        });
    }
};
