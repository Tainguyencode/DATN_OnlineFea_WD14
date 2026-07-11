<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run(): void
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
    }
}
