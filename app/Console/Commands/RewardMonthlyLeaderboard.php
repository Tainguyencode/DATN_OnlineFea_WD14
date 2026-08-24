<?php

namespace App\Console\Commands;

use App\Models\Coupon;
use App\Models\MonthlyRewardLog;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\UserCoupon;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RewardMonthlyLeaderboard extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaderboard:reward-monthly {--period= : Period key YYYY-MM (e.g. 2026-08)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically reward Top 3 monthly leaderboard students with private vouchers and notifications';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService): int
    {
        $periodOption = $this->option('period');

        if ($periodOption) {
            try {
                $targetDate = Carbon::createFromFormat('Y-m', $periodOption)->startOfMonth();
            } catch (\Throwable $e) {
                $this->error("Invalid period format: {$periodOption}. Expected format: YYYY-MM (e.g. 2026-08).");
                return 1;
            }
        } else {
            // Default to previous month
            $targetDate = now()->subMonth()->startOfMonth();
        }

        $periodKey = $targetDate->format('Y-m'); // e.g. 2026-08
        $periodFormat = $targetDate->format('m/Y'); // e.g. 08/2026
        $cleanPeriod = $targetDate->format('Ym'); // e.g. 202608

        $this->info("Processing monthly leaderboard rewards for period {$periodKey} ({$periodFormat})...");

        // 1. Check duplicate execution (Anti-duplication requirement)
        $alreadyRewarded = MonthlyRewardLog::where('period_key', $periodKey)->exists();
        if ($alreadyRewarded) {
            $this->warn("[SKIP] Monthly rewards for period {$periodKey} have already been granted previously.");
            return 0;
        }

        // 2. Determine date range for month
        $startOfMonth = $targetDate->copy()->startOfMonth();
        $endOfMonth = $targetDate->copy()->endOfMonth();

        // 3. Query Top 3 students for period
        $pointsSubquery = DB::table('user_points')
            ->select('user_id', DB::raw('SUM(points) as period_xp'))
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->groupBy('user_id');

        $topStudents = User::query()
            ->where('role', 'student')
            ->joinSub($pointsSubquery, 'points_table', 'users.id', '=', 'points_table.user_id')
            ->select('users.*', 'points_table.period_xp')
            ->orderByDesc('points_table.period_xp')
            ->orderBy('users.id')
            ->take(3)
            ->get();

        if ($topStudents->isEmpty()) {
            $this->warn("No students with points found for period {$periodKey}. Skipped reward generation.");
            return 0;
        }

        // 4. Load reward configurations from SystemSetting or use defaults
        $configs = [
            1 => [
                'type' => SystemSetting::get('leaderboard_reward_top1_type', 'fixed'),
                'value' => (float) SystemSetting::get('leaderboard_reward_top1_value', 200000),
                'expiry_days' => (int) SystemSetting::get('leaderboard_reward_top1_expiry_days', 30),
            ],
            2 => [
                'type' => SystemSetting::get('leaderboard_reward_top2_type', 'fixed'),
                'value' => (float) SystemSetting::get('leaderboard_reward_top2_value', 150000),
                'expiry_days' => (int) SystemSetting::get('leaderboard_reward_top2_expiry_days', 30),
            ],
            3 => [
                'type' => SystemSetting::get('leaderboard_reward_top3_type', 'fixed'),
                'value' => (float) SystemSetting::get('leaderboard_reward_top3_value', 50000),
                'expiry_days' => (int) SystemSetting::get('leaderboard_reward_top3_expiry_days', 30),
            ],
        ];

        // 5. Grant rewards
        $grantedCount = 0;
        foreach ($topStudents as $index => $student) {
            $rank = $index + 1;
            $cfg = $configs[$rank] ?? $configs[3];

            DB::transaction(function () use (
                $periodKey,
                $periodFormat,
                $cleanPeriod,
                $student,
                $rank,
                $cfg,
                $notificationService,
                &$grantedCount
            ) {
                // Secondary check for concurrency safety
                if (MonthlyRewardLog::where('period_key', $periodKey)->where('rank', $rank)->exists()) {
                    return;
                }

                // Generate unique voucher code
                // Example: TOP1-202608-NGUYENDOAN
                $cleanUserName = Str::upper(Str::ascii(Str::slug($student->name, '')));
                $cleanUserName = substr($cleanUserName, 0, 10);
                if (empty($cleanUserName)) {
                    $cleanUserName = 'USER' . $student->id;
                }

                $baseCode = "TOP{$rank}-{$cleanPeriod}-{$cleanUserName}";
                $code = $baseCode;
                $counter = 1;
                while (Coupon::where('code', $code)->exists()) {
                    $code = "TOP{$rank}-{$cleanPeriod}-{$cleanUserName}" . strtoupper(Str::random(3));
                    $counter++;
                    if ($counter > 10) break;
                }

                $expiryDays = max(1, $cfg['expiry_days']);
                $expiresAt = now()->addDays($expiryDays)->endOfDay();

                // Create private Coupon (creator_type = 'admin')
                $coupon = Coupon::create([
                    'code' => $code,
                    'type' => $cfg['type'],
                    'value' => $cfg['value'],
                    'min_order_amount' => 0,
                    'max_uses' => 1,
                    'max_uses_per_user' => 1,
                    'expires_at' => $expiresAt,
                    'is_active' => true,
                    'is_private' => true,
                    'creator_type' => 'admin',
                ]);

                $reason = "Phần thưởng TOP {$rank} bảng xếp hạng tháng {$periodFormat}";

                // Create UserCoupon
                $userCoupon = UserCoupon::create([
                    'user_id' => $student->id,
                    'coupon_id' => $coupon->id,
                    'source' => 'leaderboard',
                    'reason' => $reason,
                    'granted_by' => null,
                    'granted_at' => now(),
                    'saved_at' => now(),
                ]);

                // Create MonthlyRewardLog
                MonthlyRewardLog::create([
                    'period_key' => $periodKey,
                    'user_id' => $student->id,
                    'rank' => $rank,
                    'coupon_id' => $coupon->id,
                    'user_coupon_id' => $userCoupon->id,
                    'discount_value' => $cfg['value'],
                    'discount_type' => $cfg['type'],
                    'granted_at' => now(),
                ]);

                // Formatted text for notification
                $discountText = $cfg['type'] === 'percent'
                    ? ((int) $cfg['value'] . '%')
                    : (number_format($cfg['value'], 0, ',', '.') . 'đ');

                $notificationService->send(
                    $student,
                    '🏆 Chúc mừng bạn!',
                    "Bạn đạt TOP {$rank} bảng xếp hạng tháng {$periodFormat}. 🎁 Bạn nhận được voucher giảm {$discountText}. Voucher đã được thêm vào Kho Voucher.",
                    'leaderboard_reward',
                    route('student.vouchers.index')
                );

                $grantedCount++;
                $this->info("Granted TOP {$rank} reward to {$student->name} (User ID: {$student->id}) - Code: {$code} ({$discountText})");
            });
        }

        $this->info("Successfully processed monthly leaderboard rewards for {$periodKey}. Total granted: {$grantedCount}.");
        return 0;
    }
}
