<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RevenueOrderSeeder extends Seeder
{
    public function run(): void
    {
        $targetTotalRevenue = 3582962000.00; // CHÍNH XÁC 3.582.962.000 VNĐ

        echo "\n=========================================================================\n";
        echo "   BẮT ĐẦU NẠP DOANH THU BIẾN ĐỘNG (T1/2025 - T8/2026)\n";
        echo "   Mục tiêu Tổng doanh thu: " . number_format($targetTotalRevenue) . " VNĐ\n";
        echo "=========================================================================\n\n";

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('payments')->truncate();
        DB::table('order_items')->truncate();
        DB::table('orders')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $courses = Course::where('status', 'published')->get(['id', 'price']);
        if ($courses->isEmpty()) {
            $courses = Course::all(['id', 'price']);
        }
        $courseList = $courses->all();
        $totalCourses = count($courseList);

        $studentIds = User::where('role', 'student')->limit(3000)->pluck('id')->all();
        if (empty($studentIds)) {
            $studentIds = User::limit(500)->pluck('id')->all();
        }
        $totalStudents = count($studentIds);

        // 20 tháng từ Tháng 01/2025 đến Tháng 08/2026
        $monthlyTargetRevenue = [
            // 2025 (12 tháng: từ 110tr đến 220tr)
            ['year' => 2025, 'month' => 1,  'target' => 110000000],
            ['year' => 2025, 'month' => 2,  'target' => 118000000],
            ['year' => 2025, 'month' => 3,  'target' => 135000000],
            ['year' => 2025, 'month' => 4,  'target' => 128000000],
            ['year' => 2025, 'month' => 5,  'target' => 145000000],
            ['year' => 2025, 'month' => 6,  'target' => 155000000],
            ['year' => 2025, 'month' => 7,  'target' => 170000000],
            ['year' => 2025, 'month' => 8,  'target' => 162000000],
            ['year' => 2025, 'month' => 9,  'target' => 180000000],
            ['year' => 2025, 'month' => 10, 'target' => 195000000],
            ['year' => 2025, 'month' => 11, 'target' => 205000000],
            ['year' => 2025, 'month' => 12, 'target' => 220000000],
            // 2026 (8 tháng: từ 175tr đến 262tr)
            ['year' => 2026, 'month' => 1,  'target' => 175000000],
            ['year' => 2026, 'month' => 2,  'target' => 185000000],
            ['year' => 2026, 'month' => 3,  'target' => 210000000],
            ['year' => 2026, 'month' => 4,  'target' => 200000000],
            ['year' => 2026, 'month' => 5,  'target' => 230000000],
            ['year' => 2026, 'month' => 6,  'target' => 245000000],
            ['year' => 2026, 'month' => 7,  'target' => 255000000],
            ['year' => 2026, 'month' => 8,  'target' => 261962000],
        ];

        // Tính mục tiêu tháng 8/2026
        $sumPrevious19Months = 0;
        for ($i = 0; $i < 19; $i++) {
            $sumPrevious19Months += $monthlyTargetRevenue[$i]['target'];
        }
        $monthlyTargetRevenue[19]['target'] = $targetTotalRevenue - $sumPrevious19Months;

        $currentTotalRevenue = 0.0;
        $orderCounter = 1;
        $gateways = ['vnpay', 'momo', 'bank_transfer'];
        $standardPrices = [399000, 499000, 599000, 699000, 799000, 890000, 990000, 1190000, 1490000, 1990000];

        $ordersBatch = [];
        $itemsBatch = [];
        $paymentsBatch = [];

        foreach ($monthlyTargetRevenue as $mIdx => $slot) {
            $year = $slot['year'];
            $month = $slot['month'];
            $monthTarget = $slot['target'];
            $isLastMonth = ($mIdx === 19);

            $monthGeneratedRevenue = 0.0;
            $monthOrderCount = rand(130, 200);
            $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;

            for ($o = 1; $o <= $monthOrderCount; $o++) {
                $isLastOrderOfMonth = ($o === $monthOrderCount);
                $isAbsoluteLastOrder = ($isLastMonth && $isLastOrderOfMonth);

                if ($isAbsoluteLastOrder) {
                    $itemPrice = $targetTotalRevenue - $currentTotalRevenue;
                } elseif ($isLastOrderOfMonth) {
                    $itemPrice = $monthTarget - $monthGeneratedRevenue;
                } else {
                    $itemPrice = $standardPrices[rand(0, count($standardPrices) - 1)];
                    if ($monthGeneratedRevenue + $itemPrice > $monthTarget - 300000) {
                        $itemPrice = max(199000, (int) round(($monthTarget - $monthGeneratedRevenue) / ($monthOrderCount - $o + 1)));
                    }
                }

                $itemPrice = (float) $itemPrice;
                $currentTotalRevenue += $itemPrice;
                $monthGeneratedRevenue += $itemPrice;

                $day = rand(1, min(28, $daysInMonth));
                $hour = rand(8, 22);
                $minute = rand(0, 59);
                $second = rand(0, 59);

                $createdAt = sprintf('%04d-%02d-%02d %02d:%02d:%02d', $year, $month, $day, $hour, $minute, $second);
                $orderCode = sprintf('ORD-%d%02d-%06d', $year, $month, $orderCounter);
                $userId = $studentIds[($orderCounter - 1) % $totalStudents];
                $course = $courseList[($orderCounter * 3) % $totalCourses];
                $gateway = $gateways[$orderCounter % count($gateways)];
                $orderId = $orderCounter;

                $ordersBatch[] = [
                    'id' => $orderId,
                    'order_code' => $orderCode,
                    'user_id' => $userId,
                    'coupon_id' => null,
                    'subtotal' => $itemPrice,
                    'discount_amount' => 0.00,
                    'total_amount' => $itemPrice,
                    'status' => 'paid',
                    'payment_method' => $gateway,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];

                $itemsBatch[] = [
                    'order_id' => $orderId,
                    'course_id' => $course->id,
                    'price' => $itemPrice,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];

                $paymentsBatch[] = [
                    'order_id' => $orderId,
                    'gateway' => $gateway,
                    'transaction_id' => 'TXN' . strtoupper(Str::random(10)),
                    'amount' => $itemPrice,
                    'status' => 'success',
                    'gateway_response' => json_encode(['response_code' => '00', 'message' => 'Giao dịch thành công']),
                    'paid_at' => $createdAt,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];

                $orderCounter++;

                if ($isAbsoluteLastOrder) {
                    break;
                }
            }

            echo sprintf("   • Tháng %02d/%d: Doanh thu = %15s VNĐ (%3d đơn hàng)\n",
                $month, $year, number_format($monthGeneratedRevenue), $monthOrderCount);
        }

        echo "\n--> Đang lưu " . count($ordersBatch) . " đơn hàng vào cơ sở dữ liệu...\n";
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        foreach (array_chunk($ordersBatch, 1000) as $chunk) {
            DB::table('orders')->insert($chunk);
        }
        foreach (array_chunk($itemsBatch, 1000) as $chunk) {
            DB::table('order_items')->insert($chunk);
        }
        foreach (array_chunk($paymentsBatch, 1000) as $chunk) {
            DB::table('payments')->insert($chunk);
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $finalRevenue = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'paid')
            ->sum('order_items.price');

        echo "\n=========================================================================\n";
        echo "✓ HOÀN TẤT NẠP DOANH THU THÀNH CÔNG!\n";
        echo "   • Tổng số đơn hàng: " . number_format($orderCounter - 1) . " đơn hàng\n";
        echo "   • TỔNG DOANH THU ĐẠT ĐƯỢC: " . number_format($finalRevenue) . " VNĐ\n";
        echo "   • Mục tiêu yêu cầu:       " . number_format($targetTotalRevenue) . " VNĐ\n";
        echo "   • Sai lệch:               " . ($finalRevenue - $targetTotalRevenue) . " VNĐ (Chuẩn 100%)\n";
        echo "=========================================================================\n\n";
    }
}
