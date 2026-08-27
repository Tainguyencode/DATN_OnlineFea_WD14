<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\User;
use App\Services\PointService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    public function index(Request $request, PointService $pointService)
    {
        // 1. Period filter: default to 'week', option for 'month'
        $period = $request->query('period', 'week');
        if (! in_array($period, ['week', 'month'])) {
            $period = 'week';
        }

        $search = $request->query('search');

        // Date range constraints
        if ($period === 'month') {
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();
            $countdownTarget = $endDate;
        } else {
            $period = 'week';
            $startDate = now()->startOfWeek();
            $endDate = now()->endOfWeek();
            $countdownTarget = $endDate;
        }

        // 2. Aggregate points subquery for the current period
        $pointsSubquery = DB::table('user_points')
            ->select('user_id', DB::raw('SUM(points) as period_xp'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('user_id');

        // 3. Query top 50 student users with points in period
        $top50Users = User::query()
            ->where('role', 'student')
            ->joinSub($pointsSubquery, 'points_table', 'users.id', '=', 'points_table.user_id')
            ->select('users.*', 'points_table.period_xp')
            ->when($search, fn ($q) => $q->where('users.name', 'like', "%{$search}%"))
            ->orderByDesc('points_table.period_xp')
            ->orderBy('users.id')
            ->take(50)
            ->get();

        $page = (int) $request->query('page', 1);
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $currentPageItems = $top50Users->slice($offset, $perPage)->values();

        // Attach rank and extra metrics to page items
        foreach ($currentPageItems as $user) {
            $user->period_xp = (int) $user->period_xp;
            $user->total_xp = $pointService->getUserTotalPoints($user->id);
            $user->completed_courses_count = Enrollment::where('user_id', $user->id)
                ->where('status', Enrollment::STATUS_COMPLETED)
                ->count();
            $user->streak_days = $pointService->getUserStreakDays($user->id);
        }

        $leaderboard = new LengthAwarePaginator(
            $currentPageItems,
            $top50Users->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        // Top 1, Top 2, Top 3 for podium display (if on page 1)
        $top3 = [];
        if ($leaderboard->currentPage() === 1 && ! $search) {
            $top3 = $leaderboard->items();
            $top3 = array_slice($top3, 0, 3);
        }

        // 4. Current user personal achievement stats
        $currentUserData = null;
        $currentUser = auth()->user();
        if ($currentUser && $currentUser->role === 'student') {
            $weeklyXp = $pointService->getUserWeeklyPoints($currentUser->id);
            $monthlyXp = $pointService->getUserMonthlyPoints($currentUser->id);
            $totalXp = $pointService->getUserTotalPoints($currentUser->id);
            $weeklyRank = $pointService->getUserRank($currentUser->id, 'week');
            $monthlyRank = $pointService->getUserRank($currentUser->id, 'month');
            $streakDays = $pointService->getUserStreakDays($currentUser->id);
            $completedCourses = Enrollment::where('user_id', $currentUser->id)
                ->where('status', Enrollment::STATUS_COMPLETED)
                ->count();

            $currentUserData = [
                'user' => $currentUser,
                'weekly_xp' => $weeklyXp,
                'monthly_xp' => $monthlyXp,
                'total_xp' => $totalXp,
                'weekly_rank' => $weeklyRank,
                'monthly_rank' => $monthlyRank,
                'streak_days' => $streakDays,
                'completed_courses' => $completedCourses,
                'badges' => $currentUser->badges,
            ];
        }

        // Helper to format voucher text from SystemSetting
        $formatVoucher = function (string $typeKey, string $valueKey, string $defaultType, float $defaultValue) {
            $type = SystemSetting::get($typeKey, $defaultType);
            $value = (float) SystemSetting::get($valueKey, $defaultValue);
            if ($type === 'percent') {
                return 'Voucher Giảm ' . (int) $value . '%';
            }
            return 'Voucher Giảm ' . number_format($value, 0, ',', '.') . 'đ';
        };

        // Monthly Rewards definition
        $monthlyRewards = [
            1 => [
                'rank' => 'TOP 1',
                'title' => 'Quán Quân Tháng',
                'voucher' => $formatVoucher('leaderboard_reward_top1_type', 'leaderboard_reward_top1_value', 'percent', 40),
                'xp' => '+1.000 XP Thưởng',
                'badge' => 'Huy hiệu Bá Vương Tháng',
            ],
            2 => [
                'rank' => 'TOP 2',
                'title' => 'Á Quân Tháng',
                'voucher' => $formatVoucher('leaderboard_reward_top2_type', 'leaderboard_reward_top2_value', 'percent', 30),
                'xp' => '+500 XP Thưởng',
                'badge' => 'Huy hiệu Á Quân Tháng',
            ],
            3 => [
                'rank' => 'TOP 3',
                'title' => 'Top 3 Tinh Anh',
                'voucher' => $formatVoucher('leaderboard_reward_top3_type', 'leaderboard_reward_top3_value', 'percent', 20),
                'xp' => '+300 XP Thưởng',
                'badge' => 'Huy hiệu Top 3 Tháng',
            ],
            '4_9' => [
                'rank' => 'TOP 4 - TOP 9',
                'title' => 'Khuyến Khích',
                'voucher' => $formatVoucher('leaderboard_reward_top4_9_type', 'leaderboard_reward_top4_9_value', 'percent', 15),
                'xp' => '+100 XP Thưởng',
                'badge' => 'Huy hiệu Top 9 Tháng',
            ],
            '10_50' => [
                'rank' => 'TOP 10 - TOP 50',
                'title' => 'Tích Cực',
                'voucher' => $formatVoucher('leaderboard_reward_top10_50_type', 'leaderboard_reward_top10_50_value', 'percent', 10),
                'xp' => '+50 XP Thưởng',
                'badge' => 'Huy hiệu Top 50 Tháng',
            ],
        ];

        // Weekly Rewards definition
        $weeklyRewards = [
            1 => [
                'rank' => 'TOP 1',
                'title' => 'Quán Quân Tuần',
                'voucher' => $formatVoucher('leaderboard_weekly_reward_top1_type', 'leaderboard_weekly_reward_top1_value', 'percent', 30),
                'badge' => 'Voucher quà tặng TOP 1 Tuần',
            ],
            2 => [
                'rank' => 'TOP 2',
                'title' => 'Á Quân Tuần',
                'voucher' => $formatVoucher('leaderboard_weekly_reward_top2_type', 'leaderboard_weekly_reward_top2_value', 'percent', 20),
                'badge' => 'Voucher quà tặng TOP 2 Tuần',
            ],
            3 => [
                'rank' => 'TOP 3',
                'title' => 'Top 3 Tuần',
                'voucher' => $formatVoucher('leaderboard_weekly_reward_top3_type', 'leaderboard_weekly_reward_top3_value', 'percent', 15),
                'badge' => 'Voucher quà tặng TOP 3 Tuần',
            ],
            '4_10' => [
                'rank' => 'TOP 4 - TOP 10',
                'title' => 'Khuyến Khích Tuần',
                'voucher' => $formatVoucher('leaderboard_weekly_reward_top4_10_type', 'leaderboard_weekly_reward_top4_10_value', 'percent', 10),
                'badge' => 'Voucher quà tặng TOP 4 - 10 Tuần',
            ],
        ];

        return view('leaderboard.index', compact(
            'leaderboard',
            'top3',
            'currentUserData',
            'period',
            'search',
            'countdownTarget',
            'monthlyRewards',
            'weeklyRewards'
        ));
    }
}
