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
            ['name' => 'Xem đánh giá khóa học', 'slug' => 'course_reviews.view', 'group' => 'course_reviews'],
            ['name' => 'Kiểm duyệt đánh giá khóa học', 'slug' => 'course_reviews.moderate', 'group' => 'course_reviews'],
            ['name' => 'Duyệt đánh giá khóa học', 'slug' => 'course_reviews.approve', 'group' => 'course_reviews'],
            ['name' => 'Từ chối đánh giá khóa học', 'slug' => 'course_reviews.reject', 'group' => 'course_reviews'],
            ['name' => 'Ẩn đánh giá khóa học', 'slug' => 'course_reviews.hide', 'group' => 'course_reviews'],
            ['name' => 'Xóa đánh giá khóa học', 'slug' => 'course_reviews.delete', 'group' => 'course_reviews'],
            ['name' => 'Xuất đánh giá khóa học', 'slug' => 'course_reviews.export', 'group' => 'course_reviews'],
            ['name' => 'Phản hồi đánh giá khóa học', 'slug' => 'course_reviews.reply', 'group' => 'course_reviews'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['slug' => $permission['slug']],
                array_merge($permission, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
