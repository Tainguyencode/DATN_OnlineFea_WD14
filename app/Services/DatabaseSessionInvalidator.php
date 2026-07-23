<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class DatabaseSessionInvalidator
{
    /**
     * Remove persisted auth sessions for a user when the session driver is database.
     * Guest password reset cannot call Auth::logoutOtherDevices().
     */
    public function invalidateForUser(int $userId): int
    {
        if (config('session.driver') !== 'database') {
            return 0;
        }

        return DB::table(config('session.table', 'sessions'))
            ->where('user_id', $userId)
            ->delete();
    }
}
