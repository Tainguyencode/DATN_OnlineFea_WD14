<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SnapshotLeaderboard extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaderboard:snapshot {--type=all : The period to snapshot (week, month, all)} {--date= : Custom target date (YYYY-MM-DD)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Summary and log top leaderboard rankings for week/month from user_points table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = strtolower($this->option('type') ?? 'all');
        $targetDateStr = $this->option('date');
        $targetDate = $targetDateStr ? Carbon::parse($targetDateStr) : now();

        $this->info("Starting leaderboard snapshot summary (Type: {$type}, Date: {$targetDate->toDateString()})...");

        if (in_array($type, ['week', 'all'])) {
            $this->snapshotWeek($targetDate);
        }

        if (in_array($type, ['month', 'all'])) {
            $this->snapshotMonth($targetDate);
        }

        $this->info('Leaderboard snapshot summary completed successfully!');
    }

    protected function snapshotWeek(Carbon $targetDate): void
    {
        $startOfWeek = $targetDate->copy()->startOfWeek();
        $endOfWeek = $targetDate->copy()->endOfWeek();
        $periodKey = $startOfWeek->format('o').'-W'.sprintf('%02d', $startOfWeek->weekOfYear);

        $this->info("Calculating Weekly Leaderboard TOP for {$periodKey} ({$startOfWeek->toDateString()} to {$endOfWeek->toDateString()})...");

        $rankings = $this->rankingsForPeriod($startOfWeek, $endOfWeek, 10);

        $rank = 1;
        foreach ($rankings as $row) {
            $this->line("Rank #{$rank}: {$row->name} (User ID: {$row->user_id}) - {$row->total_points} XP");

            // Log to activity_logs if table exists
            try {
                ActivityLog::create([
                    'user_id' => $row->user_id,
                    'action' => 'leaderboard_weekly_top',
                    'description' => "Đạt Top {$rank} Bảng Xếp Hạng Tuần {$periodKey} với {$row->total_points} XP",
                ]);
            } catch (\Throwable $e) {
                // Ignore if activity_logs schema differs
            }

            $rank++;
        }

        $this->info('Weekly snapshot calculation complete. Top count: '.count($rankings));
    }

    protected function snapshotMonth(Carbon $targetDate): void
    {
        $startOfMonth = $targetDate->copy()->startOfMonth();
        $endOfMonth = $targetDate->copy()->endOfMonth();
        $periodKey = $startOfMonth->format('Y-m');

        $this->info("Calculating Monthly Leaderboard TOP for {$periodKey} ({$startOfMonth->toDateString()} to {$endOfMonth->toDateString()})...");

        $rankings = $this->rankingsForPeriod($startOfMonth, $endOfMonth, 10);

        $rank = 1;
        foreach ($rankings as $row) {
            $this->line("Rank #{$rank}: {$row->name} (User ID: {$row->user_id}) - {$row->total_points} XP");

            try {
                ActivityLog::create([
                    'user_id' => $row->user_id,
                    'action' => 'leaderboard_monthly_top',
                    'description' => "Đạt Top {$rank} Bảng Xếp Hạng Tháng {$periodKey} với {$row->total_points} XP",
                ]);
            } catch (\Throwable $e) {
                // Ignore if activity_logs schema differs
            }

            $rank++;
        }

        $this->info('Monthly snapshot calculation complete. Top count: '.count($rankings));
    }

    private function rankingsForPeriod(Carbon $start, Carbon $end, int $limit)
    {
        $periodPoints = DB::table('user_points')
            ->select('user_id', DB::raw('SUM(points) as total_points'))
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('user_id');
        $completedCourses = DB::table('enrollments')
            ->select('user_id', DB::raw('COUNT(*) as completed_courses'))
            ->where('status', 'completed')
            ->groupBy('user_id');
        $allTimePoints = DB::table('user_points')
            ->select('user_id', DB::raw('SUM(points) as all_time_points'))
            ->groupBy('user_id');

        return DB::table('users')
            ->where('users.role', 'student')
            ->joinSub($periodPoints, 'period_scores', 'users.id', '=', 'period_scores.user_id')
            ->leftJoinSub($completedCourses, 'completed_scores', 'users.id', '=', 'completed_scores.user_id')
            ->leftJoinSub($allTimePoints, 'all_time_scores', 'users.id', '=', 'all_time_scores.user_id')
            ->select('users.id as user_id', 'users.name', 'period_scores.total_points')
            ->orderByDesc('period_scores.total_points')
            ->orderByDesc(DB::raw('COALESCE(completed_scores.completed_courses, 0)'))
            ->orderByDesc(DB::raw('COALESCE(all_time_scores.all_time_points, 0)'))
            ->orderBy('users.id')
            ->take($limit)
            ->get();
    }
}
