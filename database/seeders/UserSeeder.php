<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\RoleSyncService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        app(RoleSyncService::class)->ensurePrimaryRolesExist();

        $users = [
            [
                'name' => 'Hệ thống Admin',
                'email' => 'admin@example.com',
                'role' => 'admin',
                'avatar' => 'https://api.dicebear.com/7.x/adventurer/svg?seed=admin',
                'bio' => 'Quản trị viên tối cao của hệ thống LMS.',
                'phone' => '0912345678',
            ],
            [
                'name' => 'Nguyễn Văn Giảng',
                'email' => 'instructor@example.com',
                'role' => 'instructor',
                'avatar' => 'https://api.dicebear.com/7.x/adventurer/svg?seed=giangvien1',
                'bio' => 'Giảng viên cấp cao với hơn 10 năm kinh nghiệm trong ngành lập trình Web Fullstack và AI.',
                'phone' => '0987654321',
            ],
            [
                'name' => 'Trần Đức Dũng',
                'username' => 'instructor2',
                'email' => 'instructor2@example.com',
                'role' => 'instructor',
                'avatar' => 'https://api.dicebear.com/7.x/adventurer/svg?seed=giangvien2',
                'bio' => 'Chuyên gia thiết kế giao diện UI/UX và thiết kế hệ thống sản phẩm số.',
                'phone' => '0977665544',
            ],
            [
                'name' => 'Trần Thị Học',
                'email' => 'student@example.com',
                'role' => 'student',
                'avatar' => 'https://api.dicebear.com/7.x/adventurer/svg?seed=hocvien1',
                'bio' => 'Học viên đam mê lập trình web và khoa học dữ liệu.',
                'phone' => '0966554433',
            ],
            [
                'name' => 'Lê Văn Học',
                'username' => 'student2',
                'email' => 'student2@example.com',
                'role' => 'student',
                'avatar' => 'https://api.dicebear.com/7.x/adventurer/svg?seed=hocvien2',
                'bio' => 'Sinh viên công nghệ thông tin muốn nâng cao kỹ năng thiết kế UI/UX.',
                'phone' => '0955443322',
            ],
            [
                'name' => 'Phạm Minh Tuấn',
                'username' => 'student3',
                'email' => 'student3@example.com',
                'role' => 'student',
                'avatar' => 'https://api.dicebear.com/7.x/adventurer/svg?seed=hocvien3',
                'bio' => 'Lập trình viên muốn chuyển hướng sang mảng AI & Big Data.',
                'phone' => '0944332211',
            ],
        ];

        foreach ($users as $data) {
            User::query()->updateOrCreate(
                ['email' => $data['email']],
                [
                    ...$data,
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                    'two_factor_enabled' => false,
                    'two_factor_secret' => null,
                    'is_active' => true,
                ]
            );
        }
    }
}
