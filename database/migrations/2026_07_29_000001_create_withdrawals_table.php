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
        if (! Schema::hasColumn('users', 'bank_code')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('bank_code', 50)->nullable()->after('commission_rate');
            });
        }

        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->string('bank_code', 50)->nullable();
            $table->string('bank_name', 100);
            $table->string('bank_account_number', 50);
            $table->string('bank_account_name', 100);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('transaction_ref', 100)->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawals');

        if (Schema::hasColumn('users', 'bank_code')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('bank_code');
            });
        }
    }
};
