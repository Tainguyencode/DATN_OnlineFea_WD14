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
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('commission_rate', 5, 2)->default(0.00)->after('price');
            $table->decimal('commission_amount', 12, 2)->default(0.00)->after('commission_rate');
            $table->decimal('instructor_earning', 12, 2)->default(0.00)->after('commission_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['commission_rate', 'commission_amount', 'instructor_earning']);
        });
    }
};
