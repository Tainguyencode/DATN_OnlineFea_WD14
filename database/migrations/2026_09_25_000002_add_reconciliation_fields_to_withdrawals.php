<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('withdrawals', function (Blueprint $table): void {
            $table->string('reconciliation_status', 30)->nullable()->after('receipt_confirmed_at');
            $table->text('reconciliation_note')->nullable()->after('reconciliation_status');
            $table->string('reconciliation_proof_path')->nullable()->after('reconciliation_note');
            $table->timestamp('reconciliation_updated_at')->nullable()->after('reconciliation_proof_path');
        });
    }

    public function down(): void
    {
        Schema::table('withdrawals', function (Blueprint $table): void {
            $table->dropColumn([
                'reconciliation_status',
                'reconciliation_note',
                'reconciliation_proof_path',
                'reconciliation_updated_at',
            ]);
        });
    }
};
