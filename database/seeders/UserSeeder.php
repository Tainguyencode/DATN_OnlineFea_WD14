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
                'username' => 'admin',
                'email' => 'admin@example.com',
                'role' => 'admin',
                'avatar' => 'https://api.dicebear.com/7.x/adventurer/svg?seed=admin',
                'bio' => 'Quản trị viên tối cao của hệ thống LMS.',
                'phone' => '0912345678',
            ],
            [
                'name' => 'Nguyễn Văn Giảng',
                'username' => 'instructor',
                'email' => 'instructor@example.com',
                'role' => 'instructor',
                'avatar' => 'https://api.dicebear.com/7.x/adventurer/sv
                // g?seed=giangvien1',
                'bio' => 'Giảng viên cấp cao với hơn 10 năm kinh nghiệm trong ngành lập trình Web Fullstack và AI.',
                'phone' => '0987654321',
                'password' => 'password123',
            ],
            [
                'name' => 'Trần Đức Dũng',
                'username' => 'instructor2',
                'email' => 'instructor2@example.com',
                'role' => 'instructor',
                'avatar' => 'https://api.dicebear.com/7.x/adventurer/svg?seed=giangvien2',
                'bio' => 'Chuyên gia thiết kế giao diện UI/UX và thiết kế hệ thống sản phẩm số.',
                'phone' => '0977665544',
                'password' => 'password123',
            ],
            [
                'name' => 'TS. Hoàng Văn Tiến',
                'username' => 'instructor3',
                'email' => 'tien.datascience@example.com',
                'role' => 'instructor',
                'avatar' => 'https://api.dicebear.com/7.x/adventurer/svg?seed=giangvien3',
                'bio' => 'Trưởng lab nghiên cứu Trí tuệ nhân tạo và Phân tích dữ liệu lớn với 12 năm kinh nghiệm.',
                'phone' => '0981112233',
                'password' => 'password123',
            ],
            [
                'name' => 'Trịnh Đình Long',
                'username' => 'instructor4',
                'email' => 'long.marketing@example.com',
                'role' => 'instructor',
                'avatar' => 'https://api.dicebear.com/7.x/adventurer/svg?seed=giangvien4',
                'bio' => 'Giám đốc Brand & Performance Marketing tại tập đoàn công nghệ đa quốc gia.',
                'phone' => '0982223344',
                'password' => 'password123',
            ],
            [
                'name' => 'Thầy Tú Phạm',
                'username' => 'instructor5',
                'email' => 'tu.ielts@example.com',
                'role' => 'instructor',
                'avatar' => 'https://api.dicebear.com/7.x/adventurer/svg?seed=giangvien5',
                'bio' => 'Chuyên gia luyện thi IELTS 8.5, cựu sinh viên Đại học Oxford với 8 năm giảng dạy.',
                'phone' => '0983334455',
                'password' => 'password123',
            ],
            [
                'name' => 'Phạm Nhật Minh',
                'username' => 'instructor6',
                'email' => 'minh.mobile@example.com',
                'role' => 'instructor',
                'avatar' => 'https://api.dicebear.com/7.x/adventurer/svg?seed=giangvien6',
                'bio' => 'Senior Mobile Developer chuyên trách React Native & Flutter.',
                'phone' => '0984445566',
            ],
            [
                'name' => 'Phạm Nhật Minh',
                'username' => 'minh_reactnative',
                'email' => 'minh.reactnative@example.com',
                'role' => 'instructor',
                'avatar' => 'https://api.dicebear.com/7.x/adventurer/svg?seed=minh-reactnative',
                'bio' => 'Giảng viên chuyên phát triển ứng dụng đa nền tảng với React Native và hệ sinh thái JavaScript.',
                'phone' => '0984445571',
                'password' => 'password123',
            ],
            [
                'name' => 'Nguyễn Thành Nam',
                'username' => 'nam_uiux',
                'email' => 'nam.uiux@example.com',
                'role' => 'instructor',
                'avatar' => 'https://api.dicebear.com/7.x/adventurer/svg?seed=nam-uiux',
                'bio' => 'Giảng viên UI/UX tập trung vào nghiên cứu người dùng, thiết kế tương tác và hệ thống thiết kế.',
                'phone' => '0984445572',
                'password' => 'password123',
            ],
            [
                'name' => 'Đặng Thanh Tùng',
                'username' => 'tung_data',
                'email' => 'tung.data@example.com',
                'role' => 'instructor',
                'avatar' => 'https://api.dicebear.com/7.x/adventurer/svg?seed=tung-data',
                'bio' => 'Giảng viên Data Science chuyên về Python, trực quan hóa dữ liệu và Machine Learning ứng dụng.',
                'phone' => '0984445573',
                'password' => 'password123',
            ],
            [
                'name' => 'Hà Tuấn Khang',
                'username' => 'khang_seo',
                'email' => 'khang.seo@example.com',
                'role' => 'instructor',
                'avatar' => 'https://api.dicebear.com/7.x/adventurer/svg?seed=khang-seo',
                'bio' => 'Giảng viên SEO và Content Marketing, chuyên xây dựng chiến lược tăng trưởng dựa trên dữ liệu.',
                'phone' => '0984445574',
                'password' => 'password123',
            ],
            [
                'name' => 'Ms. Hoa',
                'username' => 'hoa_english',
                'email' => 'hoa.english@example.com',
                'role' => 'instructor',
                'avatar' => 'https://api.dicebear.com/7.x/adventurer/svg?seed=hoa-english',
                'bio' => 'Giảng viên tiếng Anh chuyên giao tiếp học thuật, IELTS Speaking và Writing.',
                'phone' => '0984445575',
                'password' => 'password123',
            ],
            [
                'name' => 'Trần Thị Học',
                'username' => 'student',
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
                'bio' => 'Học viên đam mê lập trình di động.',
                'phone' => '0966554444',
            ],
            [
                'name' => 'Phạm Minh Tuấn',
                'username' => 'student3',
                'email' => 'student3@example.com',
                'role' => 'student',
                'avatar' => 'https://api.dicebear.com/7.x/adventurer/svg?seed=hocvien3',
                'bio' => 'Sinh viên CNTT.',
                'phone' => '0966554455',
            ],
            [
                'name' => 'Nguyễn Thị Mai',
                'username' => 'student4',
                'email' => 'student4@example.com',
                'role' => 'student',
                'avatar' => 'https://api.dicebear.com/7.x/adventurer/svg?seed=hocvien4',
                'bio' => 'Học viên muốn chuyển ngành sang lập trình Frontend.',
                'phone' => '0966554466',
            ],
            [
                'name' => 'Hoàng Văn Nam',
                'username' => 'student5',
                'email' => 'student5@example.com',
                'role' => 'student',
                'avatar' => 'https://api.dicebear.com/7.x/adventurer/svg?seed=hocvien5',
                'bio' => 'Học viên quan tâm đến bảo mật và an toàn thông tin.',
                'phone' => '0966554477',
            ],
            [
                'name' => 'Vũ Thị Hoa',
                'username' => 'student6',
                'email' => 'student6@example.com',
                'role' => 'student',
                'avatar' => 'https://api.dicebear.com/7.x/adventurer/svg?seed=hocvien6',
                'bio' => 'Sinh viên học thiết kế đồ họa muốn tìm hiểu thêm về UI/UX.',
                'phone' => '0966554488',
            ],
            [
                'name' => 'Đỗ Minh Khang',
                'username' => 'student7',
                'email' => 'student7@example.com',
                'role' => 'student',
                'avatar' => 'https://api.dicebear.com/7.x/adventurer/svg?seed=hocvien7',
                'bio' => 'Học viên đam mê xây dựng hệ thống phần mềm doanh nghiệp.',
                'phone' => '0966554499',
            ],
            [
                'name' => 'Qtrung',
                'username' => 'qtrung',
                'email' => 'tungazquoc@gmail.com',
                'role' => 'student',
                'avatar' => 'https://api.dicebear.com/7.x/adventurer/svg?seed=qtrung',
                'bio' => 'Học viên QTrung.',
                'phone' => '0123456789',
            ],
        ];

        foreach ($users as $data) {
            $user = User::query()->updateOrCreate(
                ['email' => $data['email']],
                [
                    ...$data,
                    'password' => Hash::make($data['password'] ?? 'password'),
                    'two_factor_enabled' => false,
                    'two_factor_secret' => null,
                    'is_active' => true,
                    'instructor_status' => ($data['role'] ?? '') === 'instructor' ? 'approved' : null,
                ]
            );

            $user->forceFill(['email_verified_at' => now()])->save();
            app(RoleSyncService::class)->syncPrimaryRole($user, $data['role'] ?? null);
        }
    }
}
