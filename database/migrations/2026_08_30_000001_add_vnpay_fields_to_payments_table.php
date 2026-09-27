<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('bank_code', 20)->nullable()->after('status');
            $table->string('transaction_no', 100)->nullable()->after('bank_code');
            $table->string('response_code', 10)->nullable()->after('transaction_no');
            $table->timestamp('transaction_date')->nullable()->after('response_code');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['bank_code', 'transaction_no', 'response_code', 'transaction_date']);
        });
    }
};
