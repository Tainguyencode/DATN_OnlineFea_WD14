<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('users.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('users.create');
    }

    public function update(User $user): bool
    {
        return $user->hasPermissionTo('users.update');
    }

    public function delete(User $user): bool
    {
        return $user->hasPermissionTo('users.delete');
    }

    public function restore(User $user): bool
    {
        return $user->hasPermissionTo('users.restore');
    }

    public function forceDelete(User $user): bool
    {
        return $user->hasPermissionTo('users.force_delete');
    }
}
