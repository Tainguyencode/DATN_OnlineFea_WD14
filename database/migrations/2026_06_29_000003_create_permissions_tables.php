<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! DB::table('roles')->where('slug', 'super_admin')->exists()) {
            DB::table('roles')->insert([
                'name' => 'Super Admin',
                'slug' => 'super_admin',
                'description' => 'Quản trị viên cấp cao',
                'is_system' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $permissions = [
            ['name' => 'Xem người dùng', 'slug' => 'users.view', 'group' => 'users'],
            ['name' => 'Quản lý người dùng', 'slug' => 'users.manage', 'group' => 'users'],
            ['name' => 'Xem khóa học', 'slug' => 'courses.view', 'group' => 'courses'],
            ['name' => 'Quản lý khóa học', 'slug' => 'courses.manage', 'group' => 'courses'],
            ['name' => 'Duyệt đơn giảng viên', 'slug' => 'instructor_applications.review', 'group' => 'instructors'],
            ['name' => 'Quản lý vai trò', 'slug' => 'roles.manage', 'group' => 'roles'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['slug' => $permission['slug']],
                array_merge($permission, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        $rolePermissions = [
            'instructor' => ['courses.view'],
            'admin' => ['users.view', 'users.manage', 'courses.view', 'courses.manage', 'instructor_applications.review'],
            'super_admin' => array_column($permissions, 'slug'),
        ];

        foreach ($rolePermissions as $roleSlug => $slugs) {
            $roleId = DB::table('roles')->where('slug', $roleSlug)->value('id');
            if (! $roleId) {
                continue;
            }

            foreach ($slugs as $slug) {
                $permissionId = DB::table('permissions')->where('slug', $slug)->value('id');
                if (! $permissionId) {
                    continue;
                }

                DB::table('permission_role')->updateOrInsert(
                    ['role_id' => $roleId, 'permission_id' => $permissionId],
                    []
                );
            }
        }
    }

    public function down(): void
    {
        DB::table('roles')->where('slug', 'super_admin')->delete();
    }
};
