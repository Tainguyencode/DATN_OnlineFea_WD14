<?php

namespace Database\Seeders\Demo;

use App\Models\InstructorApplication;
use App\Models\InstructorProfile;
use App\Models\Role;
use App\Models\User;
use App\Models\Withdrawal;
use App\Services\RoleSyncService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoUserSeeder
{
    public const DEMO_PASSWORD = 'Password@123';
    public const DEMO_DOMAIN = '@onlinefea.test';

    private array $vietnameseLastNames = ['Nguyễn', 'Trần', 'Lê', 'Phạm', 'Hoàng', 'Huỳnh', 'Phan', 'Vũ', 'Võ', 'Đặng', 'Bùi', 'Đỗ', 'Hồ', 'Ngô', 'Dương', 'Lý'];
    private array $vietnameseMiddleNames = ['Văn', 'Thị', 'Đức', 'Minh', 'Hải', 'Thanh', 'Quốc', 'Ngọc', 'Thu', 'Bảo', 'Gia', 'Hồng', 'Tuấn', 'Mỹ', 'Khánh', 'Hoài'];
    private array $vietnameseFirstNames = ['An', 'Bình', 'Cường', 'Dũng', 'Dương', 'Đạt', 'Giang', 'Hà', 'Hải', 'Hiếu', 'Hoa', 'Hoàng', 'Hùng', 'Huy', 'Hương', 'Khánh', 'Khoa', 'Kiên', 'Lâm', 'Linh', 'Long', 'Mai', 'Minh', 'Nam', 'Nga', 'Ngân', 'Nghĩa', 'Ngọc', 'Nhi', 'Phong', 'Phúc', 'Phương', 'Quang', 'Quân', 'Sơn', 'Tâm', 'Thái', 'Thành', 'Thảo', 'Thắng', 'Thịnh', 'Thu', 'Thủy', 'Trang', 'Trung', 'Tú', 'Tuấn', 'Tùng', 'Vinh', 'Vũ', 'Yến'];

    private array $cities = ['Hà Nội', 'TP. Hồ Chí Minh', 'Đà Nẵng', 'Hải Phòng', 'Cần Thơ', 'Bình Dương', 'Đồng Nai', 'Huế', 'Nha Trang', 'Quy Nhơn'];

    public function run(?callable $output = null): array
    {
        $log = $output ?: fn(string $msg) => null;
        $log('--- Bắt đầu nạp User Demo (Sinh viên, Giảng viên, Admin, Super Admin) ---');

        app(RoleSyncService::class)->ensurePrimaryRolesExist();
        $this->ensureSuperAdminRoleExists();

        $hashedPassword = Hash::make(self::DEMO_PASSWORD);

        // 1. Super Admin
        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin' . self::DEMO_DOMAIN],
            [
                'name' => 'Nguyễn Hoàng Minh (Super Admin)',
                'username' => 'superadmin_demo',
                'role' => 'admin',
                'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200',
                'bio' => 'Quản trị viên cấp cao nhất của hệ thống LMS OnlineFEA.',
                'phone' => '0901000001',
                'password' => $hashedPassword,
                'email_verified_at' => now(),
                'is_active' => true,
                'account_status' => 'active',
                'created_at' => now()->subMonths(12),
                'updated_at' => now(),
            ]
        );
        $this->assignRole($superAdmin, 'admin');
        $log('✓ Đã tạo/cập nhật Super Admin: superadmin' . self::DEMO_DOMAIN);

        // 2. Admins (3 tài khoản)
        $adminProfiles = [
            ['name' => 'Trần Bảo An (Admin Vận Hành)', 'username' => 'admin_an', 'email' => 'admin.01' . self::DEMO_DOMAIN, 'phone' => '0901000002'],
            ['name' => 'Lê Quốc Thái (Admin Kiểm Duyệt)', 'username' => 'admin_thai', 'email' => 'admin.02' . self::DEMO_DOMAIN, 'phone' => '0901000003'],
            ['name' => 'Phạm Thu Hương (Admin Tài Chính)', 'username' => 'admin_huong', 'email' => 'admin.03' . self::DEMO_DOMAIN, 'phone' => '0901000004'],
        ];

        $admins = [$superAdmin];
        foreach ($adminProfiles as $aData) {
            $admin = User::updateOrCreate(
                ['email' => $aData['email']],
                [
                    'name' => $aData['name'],
                    'username' => $aData['username'],
                    'role' => 'admin',
                    'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200',
                    'bio' => 'Chuyên viên quản trị nghiệp vụ hệ thống.',
                    'phone' => $aData['phone'],
                    'password' => $hashedPassword,
                    'email_verified_at' => now(),
                    'is_active' => true,
                    'account_status' => 'active',
                    'created_at' => now()->subMonths(10),
                    'updated_at' => now(),
                ]
            );
            $this->assignRole($admin, 'admin');
            $admins[] = $admin;
        }
        $log('✓ Đã tạo/cập nhật 3 Quản trị viên Admin nghiệp vụ');

        // 3. Giảng viên (30 Approved + 3 Edge Cases)
        $instructorTitles = [
            'Chuyên gia Kiến trúc Web Fullstack & Cloud',
            'Senior Frontend & Mobile Architect',
            'Trưởng nhóm Nghiên cứu Trí tuệ nhân tạo (AI)',
            'Chuyên gia DevOps & SRE Hạ tầng Đám mây',
            'Product Design & UI/UX Director',
            'Senior Database Administrator & Data Architect',
            'Chuyên gia An toàn thông tin & Penetration Testing',
            'Senior Quality Assurance & Automation Lead',
            'Chuyên gia Big Data & Data Engineering',
            'Giám đốc Kỹ thuật & Quản lý Dự án Agile',
        ];

        $banks = [
            ['code' => 'VCB', 'name' => 'Vietcombank'],
            ['code' => 'TCB', 'name' => 'Techcombank'],
            ['code' => 'MB', 'name' => 'MBBank'],
            ['code' => 'ACB', 'name' => 'ACB'],
            ['code' => 'BIDV', 'name' => 'BIDV'],
        ];

        $instructors = [];
        for ($i = 1; $i <= 30; $i++) {
            $email = sprintf('instructor.%02d%s', $i, self::DEMO_DOMAIN);
            $fullName = $this->generateVietnameseName($i);
            $username = 'instructor_' . strtolower(Str::slug($fullName, '_')) . '_' . $i;
            $title = $instructorTitles[($i - 1) % count($instructorTitles)];
            $bank = $banks[($i - 1) % count($banks)];
            $bankAccNo = '190' . str_pad((string)($i * 987654 % 1000000000), 10, '0', STR_PAD_LEFT);

            $instructor = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $fullName,
                    'username' => $username,
                    'role' => 'instructor',
                    'avatar' => 'https://images.unsplash.com/photo-' . (1500000000000 + ($i * 714159) % 50000000) . '?w=200',
                    'bio' => "Giảng viên chuyên nghiệp với hơn " . (5 + ($i % 10)) . " năm kinh nghiệm làm việc và đào tạo trong lĩnh vực $title.",
                    'phone' => '098' . str_pad((string)($i * 12345 % 10000000), 7, '0', STR_PAD_LEFT),
                    'password' => $hashedPassword,
                    'email_verified_at' => now()->subMonths(6),
                    'is_active' => true,
                    'account_status' => 'active',
                    'instructor_status' => 'approved',
                    'commission_rate' => 80.00,
                    'bank_code' => $bank['code'],
                    'bank_name' => $bank['name'],
                    'bank_account_number' => $bankAccNo,
                    'bank_account_name' => mb_strtoupper(Str::ascii($fullName)),
                    'submitted_for_review_at' => now()->subMonths(8),
                    'approved_at' => now()->subMonths(8)->addDays(2),
                    'approved_by' => $superAdmin->id,
                    'created_at' => now()->subMonths(8),
                    'updated_at' => now(),
                ]
            );
            $this->assignRole($instructor, 'instructor');

            // Instructor Profile
            InstructorProfile::updateOrCreate(
                ['user_id' => $instructor->id],
                [
                    'bio' => $instructor->bio,
                    'experience' => (5 + ($i % 10)) . ' năm kinh nghiệm thực tế tại các tập đoàn công nghệ',
                    'position' => $title,
                    'specialty' => $title,
                    'organization' => 'Học viện Đào tạo OnlineFEA',
                    'phone' => $instructor->phone,
                    'website_url' => 'https://example.com/instructors/' . $instructor->username,
                    'github_url' => 'https://github.com/demo_instructor_' . $i,
                    'linkedin_url' => 'https://linkedin.com/in/demo-instructor-' . $i,
                    'teaching_field' => $title,
                    'agree_information' => true,
                    'agree_terms' => true,
                ]
            );

            // Instructor Application (Approved)
            InstructorApplication::updateOrCreate(
                ['user_id' => $instructor->id],
                [
                    'expertise' => $title,
                    'experience' => (5 + ($i % 10)) . ' năm kinh nghiệm chuyên môn',
                    'introduction' => 'Đam mê chia sẻ kiến thức thực chiến và xây dựng cộng đồng lập trình vững mạnh.',
                    'cv_path' => 'demo/cv/instructor_cv_' . $i . '.pdf',
                    'certificate_path' => 'demo/certificates/instructor_cert_' . $i . '.pdf',
                    'bank_name' => $bank['name'],
                    'bank_account_number' => $bankAccNo,
                    'bank_account_name' => mb_strtoupper(Str::ascii($fullName)),
                    'status' => 'approved',
                    'admin_notes' => 'Hồ sơ chuyên môn xuất sắc, đã xác thực đầy đủ.',
                    'reviewed_by' => $superAdmin->id,
                    'reviewed_at' => now()->subMonths(8)->addDays(2),
                ]
            );

            // Mock Withdrawal Records (Consistent earnings history)
            Withdrawal::updateOrCreate(
                ['user_id' => $instructor->id, 'transaction_ref' => 'WD-DEMO-' . sprintf('%03d', $i)],
                [
                    'amount' => 5000000 + ($i * 500000),
                    'bank_code' => $bank['code'],
                    'bank_name' => $bank['name'],
                    'bank_account_number' => $bankAccNo,
                    'bank_account_name' => mb_strtoupper(Str::ascii($fullName)),
                    'status' => Withdrawal::STATUS_APPROVED,
                    'admin_note' => 'Quyết toán doanh thu giảng dạy tự động qua Napas247.',
                    'processed_at' => now()->subDays(15 + ($i % 10)),
                    'created_at' => now()->subDays(20 + ($i % 10)),
                    'updated_at' => now()->subDays(15 + ($i % 10)),
                ]
            );

            $instructors[] = $instructor;
        }
        $log('✓ Đã tạo/cập nhật 30 Giảng viên chính thức với đầy đủ Profile, Application và Lịch sử Rút tiền');

        // Edge case instructors
        $this->seedEdgeCaseInstructors($hashedPassword, $superAdmin);
        $log('✓ Đã tạo các tài khoản Giảng viên đặc biệt (Pending, Locked, Incomplete)');

        // 4. Sinh viên (300 tài khoản sinh viên chuẩn + 2 Edge Cases)
        $students = [];
        for ($s = 1; $s <= 300; $s++) {
            $email = sprintf('student.%03d%s', $s, self::DEMO_DOMAIN);
            $fullName = $this->generateVietnameseName($s + 100);
            $username = 'student_' . strtolower(Str::slug($fullName, '_')) . '_' . $s;
            $city = $this->cities[($s - 1) % count($this->cities)];

            $student = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $fullName,
                    'username' => $username,
                    'role' => 'student',
                    'avatar' => 'https://images.unsplash.com/photo-' . (1510000000000 + ($s * 314159) % 50000000) . '?w=150',
                    'bio' => "Học viên đam mê công nghệ tại $city, đang theo học lộ trình Fullstack và AI.",
                    'phone' => '097' . str_pad((string)($s * 23456 % 10000000), 7, '0', STR_PAD_LEFT),
                    'password' => $hashedPassword,
                    'email_verified_at' => now()->subDays(rand(10, 180)),
                    'is_active' => true,
                    'account_status' => 'active',
                    'last_learning_at' => now()->subDays(rand(0, 14)),
                    'created_at' => now()->subDays(rand(30, 200)),
                    'updated_at' => now(),
                ]
            );
            $this->assignRole($student, 'student');
            $students[] = $student;
        }
        $log('✓ Đã tạo/cập nhật 300 Học viên chuẩn chỉ');

        // Edge case students
        $this->seedEdgeCaseStudents($hashedPassword);
        $log('✓ Đã tạo các tài khoản Học viên đặc biệt (Unverified, Blocked)');

        return [
            'super_admin' => $superAdmin,
            'admins' => $admins,
            'instructors' => $instructors,
            'students' => $students,
        ];
    }

    private function seedEdgeCaseInstructors(string $hashedPassword, User $reviewer): void
    {
        // 1. Pending Instructor
        $pending = User::updateOrCreate(
            ['email' => 'pending.instructor' . self::DEMO_DOMAIN],
            [
                'name' => 'Ngô Quốc Khánh (Giảng viên Chờ Duyệt)',
                'username' => 'instructor_pending_demo',
                'role' => 'instructor',
                'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=200',
                'bio' => 'Ứng viên giảng viên vừa nộp hồ sơ xin xét duyệt.',
                'phone' => '0989000001',
                'password' => $hashedPassword,
                'email_verified_at' => now(),
                'is_active' => true,
                'account_status' => 'active',
                'instructor_status' => 'pending',
                'submitted_for_review_at' => now()->subDays(2),
            ]
        );
        $this->assignRole($pending, 'instructor');

        InstructorApplication::updateOrCreate(
            ['user_id' => $pending->id],
            [
                'expertise' => 'Lập trình Game Unity & Unreal Engine',
                'experience' => '4 năm làm Game Developer tại studio quốc tế',
                'introduction' => 'Mong muốn mở khóa học hướng dẫn dựng game 3D từ con số 0.',
                'cv_path' => 'demo/cv/pending_instructor_cv.pdf',
                'status' => 'pending',
            ]
        );

        // 2. Locked Instructor
        $locked = User::updateOrCreate(
            ['email' => 'locked.instructor' . self::DEMO_DOMAIN],
            [
                'name' => 'Võ Đình Tuấn (Giảng viên Bị Khóa)',
                'username' => 'instructor_locked_demo',
                'role' => 'instructor',
                'avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=200',
                'bio' => 'Tài khoản giảng viên tạm khóa để kiểm tra xử lý vi phạm.',
                'phone' => '0989000002',
                'password' => $hashedPassword,
                'email_verified_at' => now(),
                'is_active' => false,
                'account_status' => 'locked',
                'locked_at' => now()->subDays(5),
                'locked_reason' => 'Vi phạm chính sách nội dung bài giảng và bản quyền.',
            ]
        );
        $this->assignRole($locked, 'instructor');

        // 3. Incomplete Profile Instructor
        $incomplete = User::updateOrCreate(
            ['email' => 'incomplete.instructor' . self::DEMO_DOMAIN],
            [
                'name' => 'Đặng Thúy Nga (Hồ sơ Chưa Hoàn Thiện)',
                'username' => 'instructor_incomplete_demo',
                'role' => 'instructor',
                'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=200',
                'bio' => 'Tài khoản chưa cập nhật đầy đủ thông tin ngân hàng và chứng chỉ.',
                'phone' => '0989000003',
                'password' => $hashedPassword,
                'email_verified_at' => now(),
                'is_active' => true,
                'account_status' => 'active',
                'instructor_status' => 'approved',
                'needs_admin_review' => true,
            ]
        );
        $this->assignRole($incomplete, 'instructor');
    }

    private function seedEdgeCaseStudents(string $hashedPassword): void
    {
        // 1. Unverified Student
        $unverified = User::updateOrCreate(
            ['email' => 'unverified.student' . self::DEMO_DOMAIN],
            [
                'name' => 'Hoàng Minh Quân (Chưa Xác Minh Email)',
                'username' => 'student_unverified_demo',
                'role' => 'student',
                'avatar' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=150',
                'bio' => 'Học viên vừa đăng ký tài khoản chưa bấm link xác nhận email.',
                'phone' => '0979000001',
                'password' => $hashedPassword,
                'email_verified_at' => null,
                'is_active' => true,
                'account_status' => 'active',
            ]
        );
        $this->assignRole($unverified, 'student');

        // 2. Blocked Student
        $blocked = User::updateOrCreate(
            ['email' => 'blocked.student' . self::DEMO_DOMAIN],
            [
                'name' => 'Trần Văn Vi Phạm (Học viên Bị Khóa)',
                'username' => 'student_blocked_demo',
                'role' => 'student',
                'avatar' => 'https://images.unsplash.com/photo-1570295999919-56ceb5ecca61?w=150',
                'bio' => 'Tài khoản bị khóa do vi phạm điều khoản bình luận cộng đồng.',
                'phone' => '0979000002',
                'password' => $hashedPassword,
                'email_verified_at' => now(),
                'is_active' => false,
                'account_status' => 'locked',
                'locked_at' => now()->subDays(3),
                'locked_reason' => 'Spam link quảng cáo độc hại trong phần thảo luận bài học.',
            ]
        );
        $this->assignRole($blocked, 'student');
    }

    private function generateVietnameseName(int $seed): string
    {
        $last = $this->vietnameseLastNames[$seed % count($this->vietnameseLastNames)];
        $mid = $this->vietnameseMiddleNames[($seed * 3) % count($this->vietnameseMiddleNames)];
        $first = $this->vietnameseFirstNames[($seed * 7) % count($this->vietnameseFirstNames)];

        return "$last $mid $first";
    }

    private function ensureSuperAdminRoleExists(): void
    {
        Role::query()->firstOrCreate(
            ['slug' => 'super_admin'],
            [
                'name' => 'Super Admin',
                'description' => 'Toàn quyền tối cao quản trị hệ thống và phân quyền.',
                'is_system' => true,
            ]
        );
    }

    private function assignRole(User $user, string $roleSlug): void
    {
        $role = Role::where('slug', $roleSlug)->first();
        if ($role) {
            $user->roles()->syncWithoutDetaching([$role->id]);
        }
    }
}
