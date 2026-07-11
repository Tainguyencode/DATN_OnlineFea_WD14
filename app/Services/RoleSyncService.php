<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class RoleSyncService
{
    public const PRIMARY_ROLE_SLUGS = ['student', 'instructor', 'admin'];

    /**
     * Ensure system primary roles exist (idempotent).
     */
    public function ensurePrimaryRolesExist(): void
    {
        $definitions = [
            ['name' => 'Admin', 'slug' => 'admin', 'description' => 'Toàn quyền quản trị hệ thống.', 'is_system' => true],
            ['name' => 'Instructor', 'slug' => 'instructor', 'description' => 'Quản lý khóa học và học viên.', 'is_system' => true],
            ['name' => 'Student', 'slug' => 'student', 'description' => 'Học viên sử dụng nền tảng.', 'is_system' => true],
        ];

        foreach ($definitions as $definition) {
            Role::query()->firstOrCreate(
                ['slug' => $definition['slug']],
                [
                    'name' => $definition['name'],
                    'description' => $definition['description'],
                    'is_system' => $definition['is_system'],
                ]
            );
        }
    }

    /**
     * Sync users.role to role_user pivot for RBAC permissions.
     * Removes previous primary roles from pivot; keeps non-primary roles intact.
     */
    public function syncPrimaryRole(User $user, ?string $roleSlug = null): void
    {
        $roleSlug = $roleSlug ?? $user->role;

        if (! in_array($roleSlug, self::PRIMARY_ROLE_SLUGS, true)) {
            return;
        }

        $role = Role::query()->where('slug', $roleSlug)->first();

        if (! $role) {
            Log::warning('Primary role slug not found in roles table; skipping role_user sync.', [
                'user_id' => $user->id,
                'role_slug' => $roleSlug,
            ]);

            return;
        }

        $primaryRoleIds = Role::query()
            ->whereIn('slug', self::PRIMARY_ROLE_SLUGS)
            ->pluck('id');

        $user->roles()->detach($primaryRoleIds);
        $user->roles()->attach($role->id);
    }
}
