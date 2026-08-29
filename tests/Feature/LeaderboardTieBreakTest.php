<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserPoint;
use App\Services\PointService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaderboardTieBreakTest extends TestCase
{
    use RefreshDatabase;

    public function test_equal_period_points_are_broken_by_all_time_xp_consistently(): void
    {
        $first = User::factory()->create(['role' => 'student']);
        $second = User::factory()->create(['role' => 'student']);

        UserPoint::create([
            'user_id' => $first->id,
            'points' => 100,
            'created_at' => now(),
        ]);
        UserPoint::create([
            'user_id' => $second->id,
            'points' => 100,
            'created_at' => now(),
        ]);
        UserPoint::create([
            'user_id' => $second->id,
            'points' => 50,
            'created_at' => now()->subMonths(2),
        ]);

        $service = app(PointService::class);

        $this->assertSame(2, $service->getUserRank($first->id, 'week'));
        $this->assertSame(1, $service->getUserRank($second->id, 'week'));

        $response = $this->get(route('leaderboard', ['period' => 'week']));

        $response->assertOk();
        $rankedUsers = $response->viewData('leaderboard')->getCollection();
        $this->assertSame([$second->id, $first->id], $rankedUsers->pluck('id')->all());
    }

    public function test_student_without_period_points_ranks_after_scored_students(): void
    {
        $scored = User::factory()->create(['role' => 'student']);
        $unscored = User::factory()->create(['role' => 'student']);
        UserPoint::create([
            'user_id' => $scored->id,
            'points' => 10,
            'created_at' => now(),
        ]);

        $this->assertSame(2, app(PointService::class)->getUserRank($unscored->id, 'week'));
    }
}
