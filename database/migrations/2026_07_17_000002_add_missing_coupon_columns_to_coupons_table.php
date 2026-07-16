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
        if (! Schema::hasColumn('coupons', 'min_order_amount')) {
            Schema::table('coupons', function (Blueprint $table) {
                $table->decimal('min_order_amount', 12, 2)->default(0.00)->after('value');
            });
        }

        if (! Schema::hasColumn('coupons', 'max_uses')) {
            Schema::table('coupons', function (Blueprint $table) {
                $afterColumn = Schema::hasColumn('coupons', 'min_order_amount')
                    ? 'min_order_amount'
                    : 'value';

                $table->unsignedInteger('max_uses')->nullable()->after($afterColumn);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $columns = array_values(array_filter([
            Schema::hasColumn('coupons', 'max_uses') ? 'max_uses' : null,
            Schema::hasColumn('coupons', 'min_order_amount') ? 'min_order_amount' : null,
        ]));

        if ($columns !== []) {
            Schema::table('coupons', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
