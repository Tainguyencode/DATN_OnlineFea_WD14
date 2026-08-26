<?php

namespace App\Listeners;

use App\Services\PointService;
use Illuminate\Auth\Events\Login;

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
