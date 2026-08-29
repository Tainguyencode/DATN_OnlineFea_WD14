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

class RewardWeeklyLeaderboard extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaderboard:reward-weekly {--period= : Period key YYYY-Www (e.g. 2026-W34)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically reward Top 10 weekly leaderboard students with private vouchers and notifications';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService): int
    {
        $periodOption = $this->option('period');

        if ($periodOption) {
            try {
                if (preg_match('/^(\d{4})-?W?(\d{1,2})$/i', $periodOption, $matches)) {
                    $year = (int) $matches[1];
                    $week = (int) $matches[2];
                    $targetDate = Carbon::now()->setISODate($year, $week)->startOfWeek();
                } else {
                    $targetDate = Carbon::parse($periodOption)->startOfWeek();
                }
            } catch (\Throwable $e) {
                $this->error("Invalid period format: {$periodOption}. Expected format: YYYY-Www (e.g. 2026-W34).");

                return 1;
            }
        } else {
            // Default to previous week
            $targetDate = now()->subWeek()->startOfWeek();
        }

        $year = $targetDate->isoWeekYear;
        $weekNum = sprintf('%02d', $targetDate->isoWeek);
        $periodKey = "{$year}W{$weekNum}"; // 7 chars: e.g. 2026W34
        $periodFormat = "Tuần {$weekNum}/{$year}";
        $cleanPeriod = "{$year}W{$weekNum}";

        $this->info("Processing weekly leaderboard rewards for period {$periodKey} ({$periodFormat})...");

        // 1. Check duplicate execution
        $alreadyRewarded = MonthlyRewardLog::where('period_key', $periodKey)->exists();
        if ($alreadyRewarded) {
            $this->warn("[SKIP] Weekly rewards for period {$periodKey} have already been granted previously.");

            return 0;
        }

        // 2. Determine date range for week
        $startOfWeek = $targetDate->copy()->startOfWeek();
        $endOfWeek = $targetDate->copy()->endOfWeek();

        // 3. Query Top 10 students for week
        $pointsSubquery = DB::table('user_points')
            ->select('user_id', DB::raw('SUM(points) as period_xp'))
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->groupBy('user_id');
        $completedCourses = DB::table('enrollments')->select('user_id', DB::raw('COUNT(*) as completed_courses'))->where('status', 'completed')->groupBy('user_id');
        $totalPoints = DB::table('user_points')->select('user_id', DB::raw('SUM(points) as total_points'))->groupBy('user_id');

        $topStudents = User::query()
            ->where('role', 'student')
            ->joinSub($pointsSubquery, 'points_table', 'users.id', '=', 'points_table.user_id')
            ->leftJoinSub($completedCourses, 'completed_table', 'users.id', '=', 'completed_table.user_id')
            ->leftJoinSub($totalPoints, 'total_table', 'users.id', '=', 'total_table.user_id')
            ->select('users.*', 'points_table.period_xp')
            ->orderByDesc('points_table.period_xp')
            ->orderByDesc(DB::raw('COALESCE(completed_table.completed_courses, 0)'))
            ->orderByDesc(DB::raw('COALESCE(total_table.total_points, 0)'))
            ->orderBy('users.id')
            ->take(10)
            ->get();

        if ($topStudents->isEmpty()) {
            $this->warn("No students with points found for period {$periodKey}. Skipped reward generation.");

            return 0;
        }

        // 4. Load reward configurations for weekly leaderboard
        $configs = [
            1 => [
                'type' => SystemSetting::get('leaderboard_weekly_reward_top1_type', 'percent'),
                'value' => (float) SystemSetting::get('leaderboard_weekly_reward_top1_value', 30),
                'expiry_days' => (int) SystemSetting::get('leaderboard_weekly_reward_top1_expiry_days', 7),
            ],
            2 => [
                'type' => SystemSetting::get('leaderboard_weekly_reward_top2_type', 'percent'),
                'value' => (float) SystemSetting::get('leaderboard_weekly_reward_top2_value', 20),
                'expiry_days' => (int) SystemSetting::get('leaderboard_weekly_reward_top2_expiry_days', 7),
            ],
            3 => [
                'type' => SystemSetting::get('leaderboard_weekly_reward_top3_type', 'percent'),
                'value' => (float) SystemSetting::get('leaderboard_weekly_reward_top3_value', 15),
                'expiry_days' => (int) SystemSetting::get('leaderboard_weekly_reward_top3_expiry_days', 7),
            ],
            '4_10' => [
                'type' => SystemSetting::get('leaderboard_weekly_reward_top4_10_type', 'percent'),
                'value' => (float) SystemSetting::get('leaderboard_weekly_reward_top4_10_value', 10),
                'expiry_days' => (int) SystemSetting::get('leaderboard_weekly_reward_top4_10_expiry_days', 7),
            ],
        ];

        // 5. Grant rewards to Top 10
        $grantedCount = 0;
        foreach ($topStudents as $index => $student) {
            $rank = $index + 1;
            if ($rank === 1) {
                $cfg = $configs[1];
            } elseif ($rank === 2) {
                $cfg = $configs[2];
            } elseif ($rank === 3) {
                $cfg = $configs[3];
            } else {
                $cfg = $configs['4_10'];
            }

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
                if (MonthlyRewardLog::where('period_key', $periodKey)->where('rank', $rank)->exists()) {
                    return;
                }

                $cleanUserName = Str::upper(Str::ascii(Str::slug($student->name, '')));
                $cleanUserName = substr($cleanUserName, 0, 10);
                if (empty($cleanUserName)) {
                    $cleanUserName = 'USER'.$student->id;
                }

                $baseCode = "TOP{$rank}-{$cleanPeriod}-{$cleanUserName}";
                $code = $baseCode;
                $counter = 1;
                while (Coupon::where('code', $code)->exists()) {
                    $code = "TOP{$rank}-{$cleanPeriod}-{$cleanUserName}".strtoupper(Str::random(3));
                    $counter++;
                    if ($counter > 10) {
                        break;
                    }
                }

                $expiryDays = max(1, $cfg['expiry_days']);
                $expiresAt = now()->addDays($expiryDays)->endOfDay();

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

                $reason = "Phần thưởng TOP {$rank} bảng xếp hạng {$periodFormat}";

                $userCoupon = UserCoupon::create([
                    'user_id' => $student->id,
                    'coupon_id' => $coupon->id,
                    'source' => 'leaderboard',
                    'reason' => $reason,
                    'granted_by' => null,
                    'granted_at' => now(),
                    'saved_at' => now(),
                ]);

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

                $discountText = $cfg['type'] === 'percent'
                    ? ((int) $cfg['value'].'%')
                    : (number_format($cfg['value'], 0, ',', '.').'đ');

                $notificationService->send(
                    $student,
                    '🏆 Chúc mừng bạn!',
                    "Bạn đạt TOP {$rank} bảng xếp hạng {$periodFormat}. 🎁 Bạn nhận được voucher giảm {$discountText}. Voucher đã được thêm vào Kho Voucher.",
                    'leaderboard_reward',
                    route('student.vouchers.index')
                );

                $grantedCount++;
                $this->info("Granted TOP {$rank} weekly reward to {$student->name} (User ID: {$student->id}) - Code: {$code} ({$discountText})");
            });
        }

        $this->info("Successfully processed weekly leaderboard rewards for {$periodKey}. Total granted: {$grantedCount}.");

        return 0;
    }
}
