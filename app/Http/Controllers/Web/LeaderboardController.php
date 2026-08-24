<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Enrollment;
use App\Services\PointService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class LeaderboardController extends Controller
{
    public function index(Request $request, PointService $pointService)
    {
        // 1. Period filter: default to 'week', option for 'month'
        $period = $request->query('period', 'week');
        if (!in_array($period, ['week', 'month'])) {
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

        // 3. Query student users with points in period
        $leaderboardQuery = User::query()
            ->where('role', 'student')
            ->joinSub($pointsSubquery, 'points_table', 'users.id', '=', 'points_table.user_id')
            ->select('users.*', 'points_table.period_xp')
            ->when($search, fn($q) => $q->where('users.name', 'like', "%{$search}%"))
            ->orderByDesc('points_table.period_xp')
            ->orderBy('users.id');

        $leaderboard = $leaderboardQuery->paginate(20)->withQueryString();

        // Attach rank and extra metrics to page items
        foreach ($leaderboard->items() as $index => $user) {
            $user->period_xp = (int) $user->period_xp;
            $user->total_xp = $pointService->getUserTotalPoints($user->id);
            $user->completed_courses_count = Enrollment::where('user_id', $user->id)
                ->where('status', Enrollment::STATUS_COMPLETED)
                ->count();
            $user->streak_days = $pointService->getUserStreakDays($user->id);
        }

        // Top 1, Top 2, Top 3 for podium display (if on page 1)
        $top3 = [];
        if ($leaderboard->currentPage() === 1 && !$search) {
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

        // Monthly Top 3 Rewards definition
        $monthlyRewards = [
            1 => [
                'rank' => 'TOP 1',
                'title' => 'Quán Quân Tháng',
                'voucher' => 'Voucher Giảm 40%',
                'xp' => '+1.000 XP Thưởng',
                'badge' => 'Huy hiệu Bá Vương Tháng',
            ],
            2 => [
                'rank' => 'TOP 2',
                'title' => 'Á Quân Tháng',
                'voucher' => 'Voucher Giảm 30%',
                'xp' => '+500 XP Thưởng',
                'badge' => 'Huy hiệu Á Quân Tháng',
            ],
            3 => [
                'rank' => 'TOP 3',
                'title' => 'Top 3 Tinh Anh',
                'voucher' => 'Voucher Giảm 20%',
                'xp' => '+300 XP Thưởng',
                'badge' => 'Huy hiệu Top 3 Tháng',
            ],
            '4_9' => [
                'rank' => 'TOP 4 - TOP 9',
                'title' => 'Khuyến Khích',
                'voucher' => 'Voucher Giảm 15%',
                'xp' => '+100 XP Thưởng',
                'badge' => 'Huy hiệu Top 9 Tháng',
            ],
            '10_50' => [
                'rank' => 'TOP 10 - TOP 50',
                'title' => 'Tích Cực',
                'voucher' => 'Voucher Giảm 10%',
                'xp' => '+50 XP Thưởng',
                'badge' => 'Huy hiệu Top 50 Tháng',
            ],
        ];

        return view('leaderboard.index', compact(
            'leaderboard',
            'top3',
            'currentUserData',
            'period',
            'search',
            'countdownTarget',
            'monthlyRewards'
        ));
    }
}
