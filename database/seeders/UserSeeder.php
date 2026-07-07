<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Danh sách người dùng mẫu chuyên nghiệp cho hệ thống
        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'Hệ thống Admin',
                'username' => 'admin',
                'email' => 'admin@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'admin',
                'avatar' => 'https://api.dicebear.com/7.x/adventurer/svg?seed=admin',
                'bio' => 'Quản trị viên tối cao của hệ thống LMS.',
                'phone' => '0912345678',
                'google_id' => null,
                'facebook_id' => null,
                'two_factor_enabled' => false,
                'two_factor_secret' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Nguyễn Văn Giảng',
                'username' => 'instructor',
                'email' => 'instructor@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'avatar' => 'https://api.dicebear.com/7.x/adventurer/svg?seed=giangvien1',
                'bio' => 'Giảng viên cấp cao với hơn 10 năm kinh nghiệm trong ngành lập trình Web Fullstack và AI.',
                'phone' => '0987654321',
                'google_id' => null,
                'facebook_id' => null,
                'two_factor_enabled' => false,
                'two_factor_secret' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Trần Đức Dũng',
                'email' => 'instructor2@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'avatar' => 'https://api.dicebear.com/7.x/adventurer/svg?seed=giangvien2',
                'bio' => 'Chuyên gia thiết kế giao diện UI/UX và thiết kế hệ thống sản phẩm số.',
                'phone' => '0977665544',
                'google_id' => null,
                'facebook_id' => null,
                'two_factor_enabled' => false,
                'two_factor_secret' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Trần Thị Học',
                'username' => 'student',
                'email' => 'student@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'student',
                'avatar' => 'https://api.dicebear.com/7.x/adventurer/svg?seed=hocvien1',
                'bio' => 'Học viên đam mê lập trình web và khoa học dữ liệu.',
                'phone' => '0966554433',
                'google_id' => null,
                'facebook_id' => null,
                'two_factor_enabled' => false,
                'two_factor_secret' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'name' => 'Lê Văn Học',
                'email' => 'student2@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'student',
                'avatar' => 'https://api.dicebear.com/7.x/adventurer/svg?seed=hocvien2',
                'bio' => 'Sinh viên công nghệ thông tin muốn nâng cao kỹ năng thiết kế UI/UX.',
                'phone' => '0955443322',
                'google_id' => null,
                'facebook_id' => null,
                'two_factor_enabled' => false,
                'two_factor_secret' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'name' => 'Phạm Minh Tuấn',
                'email' => 'student3@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'student',
                'avatar' => 'https://api.dicebear.com/7.x/adventurer/svg?seed=hocvien3',
                'bio' => 'Lập trình viên muốn chuyển hướng sang mảng AI & Big Data.',
                'phone' => '0944332211',
                'google_id' => null,
                'facebook_id' => null,
                'two_factor_enabled' => false,
                'two_factor_secret' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
