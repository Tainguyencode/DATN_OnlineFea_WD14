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

        $rankings = DB::table('user_points')
            ->join('users', 'users.id', '=', 'user_points.user_id')
            ->where('users.role', 'student')
            ->whereBetween('user_points.created_at', [$startOfWeek, $endOfWeek])
            ->select('user_points.user_id', 'users.name', DB::raw('SUM(user_points.points) as total_points'))
            ->groupBy('user_points.user_id', 'users.name')
            ->orderByDesc('total_points')
            ->orderBy('user_points.user_id')
            ->take(10)
            ->get();

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

        $rankings = DB::table('user_points')
            ->join('users', 'users.id', '=', 'user_points.user_id')
            ->where('users.role', 'student')
            ->whereBetween('user_points.created_at', [$startOfMonth, $endOfMonth])
            ->select('user_points.user_id', 'users.name', DB::raw('SUM(user_points.points) as total_points'))
            ->groupBy('user_points.user_id', 'users.name')
            ->orderByDesc('total_points')
            ->orderBy('user_points.user_id')
            ->take(10)
            ->get();

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
}
