<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Services\PointService;

class AwardLoginPoints
{
    protected PointService $pointService;

    public function __construct(PointService $pointService)
    {
        $this->pointService = $pointService;
    }

    public function handle(Login $event): void
    {
        $user = $event->user;
        if ($user && $user->role === 'student') {
            $this->pointService->awardDailyLoginPoints($user->id);
        }
    }
}
