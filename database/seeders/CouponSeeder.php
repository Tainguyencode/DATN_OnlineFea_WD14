<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coupons = [
            [
                'id' => 1,
                'code' => 'WELCOME20',
                'type' => 'percentage',
                'value' => 20.00,
                'used_count' => 5,
                'starts_at' => now()->subDays(10),
                'expires_at' => now()->addMonths(3),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'code' => 'DATN2026',
                'type' => 'fixed',
                'value' => 100000.00,
                'used_count' => 2,
                'starts_at' => now()->subDays(5),
                'expires_at' => now()->addMonths(2),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'code' => 'PROMO50',
                'type' => 'percentage',
                'value' => 50.00,
                'used_count' => 0,
                'starts_at' => now(),
                'expires_at' => now()->addDays(7),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('coupons')->insert($coupons);
    }
}
