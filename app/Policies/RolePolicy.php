<?php

namespace App\Policies;

use App\Models\User;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('roles.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('roles.create');
    }

    public function update(User $user): bool
    {
        return $user->hasPermissionTo('roles.update');
    }

    public function delete(User $user): bool
    {
        return $user->hasPermissionTo('roles.delete');
    }
}
