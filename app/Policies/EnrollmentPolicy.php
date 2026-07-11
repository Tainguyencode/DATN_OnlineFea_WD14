<?php

namespace App\Policies;

use App\Models\Enrollment;
use App\Models\User;

class EnrollmentPolicy
{
    public function updateProgress(User $user, Enrollment $enrollment): bool
    {
        return (int) $enrollment->user_id === (int) $user->id
            && $enrollment->hasLearningAccess();
    }
}
