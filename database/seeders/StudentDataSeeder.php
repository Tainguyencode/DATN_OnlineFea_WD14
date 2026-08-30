<?php

namespace Database\Seeders;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Role;
use App\Models\User;
use App\Services\RoleSyncService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentDataSeeder extends Seeder
{
    private array $vietnameseLastNames = [
        'Nguyễn', 'Trần', 'Lê', 'Phạm', 'Hoàng', 'Huỳnh', 'Phan', 'Vũ', 'Võ', 'Đặng',
        'Bùi', 'Đỗ', 'Hồ', 'Ngô', 'Dương', 'Lý', 'Đinh', 'Đoàn', 'Lâm', 'Trịnh',
        'Mai', 'Đào', 'Cao', 'Hà', 'Lưu', 'Lương', 'Thái', 'Châu', 'Tạ', 'Phùng',
    ];

    private array $vietnameseMiddleNames = [
        'Văn', 'Thị', 'Đức', 'Minh', 'Hải', 'Thanh', 'Quốc', 'Ngọc', 'Thu', 'Bảo',
        'Gia', 'Hồng', 'Tuấn', 'Mỹ', 'Khánh', 'Hoài', 'Xuân', 'Kim', 'Trọng', 'Đình',
        'Hữu', 'Công', 'Phúc', 'Phương', 'Tấn',
    ];

    private array $vietnameseFirstNames = [
        'An', 'Bình', 'Cường', 'Dũng', 'Dương', 'Đạt', 'Giang', 'Hà', 'Hải', 'Hiếu',
        'Hoa', 'Hoàng', 'Hùng', 'Huy', 'Hương', 'Khánh', 'Khoa', 'Kiên', 'Lâm', 'Linh',
        'Long', 'Mai', 'Minh', 'Nam', 'Nga', 'Ngân', 'Nghĩa', 'Ngọc', 'Nhi', 'Phong',
        'Phúc', 'Phương', 'Quang', 'Quân', 'Sơn', 'Tâm', 'Thái', 'Thành', 'Thảo', 'Thắng',
        'Thịnh', 'Thu', 'Thủy', 'Trang', 'Trung', 'Tú', 'Tuấn', 'Tùng', 'Vinh', 'Vũ',
        'Yến', 'Lan', 'Bích', 'Trúc', 'Diệp', 'Vy', 'Tiến', 'Bách', 'Triết', 'Đăng',
    ];

    public function run(): void
    {
        echo "\n=================================================================\n";
        echo "   BẮT ĐẦU NẠP 360+ HỌC VIÊN & TIẾN ĐỘ HỌC TẬP (T1/2025 - T8/2026)\n";
        echo "=================================================================\n\n";

        app(RoleSyncService::class)->ensurePrimaryRolesExist();

        $hashedPassword = Hash::make('password');

        // Timeline 20 tháng: Tháng 01/2025 đến Tháng 08/2026
        $monthTimeline = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthTimeline[] = ['year' => 2025, 'month' => $m];
        }
        for ($m = 1; $m <= 8; $m++) {
            $monthTimeline[] = ['year' => 2026, 'month' => $m];
        }

        $totalMonths = count($monthTimeline); // 20
        $studentsPerMonth = 18;
        $totalStudents = $totalMonths * $studentsPerMonth; // 360

        $courses = Course::where('status', 'published')->pluck('id')->all();
        if (empty($courses)) {
            $courses = Course::pluck('id')->all();
        }
        $totalCourses = count($courses);

        $completedCount = 0;
        $inProgressCount = 0;
        $incompleteCount = 0;
        $newStudentCount = 0;
        $totalCertificatesIssued = 0;
        $totalEnrollments = 0;

        for ($i = 1; $i <= $totalStudents; $i++) {
            // 1. Xác định tháng đăng ký
            $slot = $monthTimeline[($i - 1) % $totalMonths];
            $year = $slot['year'];
            $month = $slot['month'];
            $inMonthIdx = intdiv($i - 1, $totalMonths); // 0 đến 17

            // Phân bổ ngày đăng ký ngẫu nhiên thực tế
            $day = (($inMonthIdx * 3 + ($i % 5)) % 27) + 1; // 1 đến 28
            $hour = 7 + (($i * 5) % 15);                   // 07:00 - 22:00
            $minute = ($i * 17) % 60;

            $createdAt = Carbon::create($year, $month, $day, $hour, $minute, 0);

            // 2. Tạo Họ và tên
            $fullName = $this->generateVietnameseName($i);
            $slugName = Str::slug($fullName, '_');
            $username = sprintf('hv_%s_%03d', $slugName, $i);
            $email = sprintf('student.%03d@onlinefea.edu.vn', $i);
            $phone = sprintf('03%d%07d', 2 + ($i % 8), ($i * 9876543) % 10000000);

            // Lần đăng nhập cuối cùng (last_login_at)
            $isRecent = ($year === 2026 && $month >= 6);
            if ($isRecent) {
                $lastLoginAt = now()->subDays($i % 14)->subHours($i % 24);
            } else {
                $lastLoginAt = $createdAt->copy()->addDays(rand(5, 60))->addHours(rand(1, 12));
                if ($lastLoginAt->isFuture()) {
                    $lastLoginAt = now()->subDays(rand(1, 10));
                }
            }

            $avatarUrl = 'https://images.unsplash.com/photo-' . (1530000000000 + ($i * 819231) % 50000000) . '?w=200&auto=format&fit=crop&q=80';

            // 3. Tạo/Cập nhật User Học viên
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $fullName,
                    'username' => $username,
                    'role' => 'student',
                    'avatar' => $avatarUrl,
                    'bio' => 'Học viên đam mê công nghệ và phát triển bản thân tại OnlineFEA.',
                    'phone' => $phone,
                    'password' => $hashedPassword,
                    'is_active' => true,
                    'account_status' => 'active',
                    'email_verified_at' => $createdAt,
                    'last_login_at' => $lastLoginAt,
                    'created_at' => $createdAt,
                    'updated_at' => $lastLoginAt ?? $createdAt,
                ]
            );

            $this->assignRole($user, 'student');

            // 4. Phân bổ Loại học viên và Trạng thái học tập
            // Nhóm 1: Hoàn thành khóa học (Completed) - ~35%
            // Nhóm 2: Đang học tích cực (In Progress) - ~40%
            // Nhóm 3: Chưa hoàn thành / Dở dang (Incomplete) - ~15%
            // Nhóm 4: Người dùng mới vào học (New) - ~10% (Tập trung T6-T8/2026)

            if ($year === 2025 || ($year === 2026 && $month <= 4)) {
                // Các tháng cũ: Đa số đã Hoàn thành hoặc Dở dang
                if ($inMonthIdx < 9) {
                    $learningType = 'completed';
                } elseif ($inMonthIdx < 15) {
                    $learningType = 'in_progress';
                } else {
                    $learningType = 'incomplete';
                }
            } else {
                // Các tháng gần đây (T5 - T8/2026): Có cả Người dùng mới, Đang học, Hoàn thành
                if ($inMonthIdx < 5) {
                    $learningType = 'completed';
                } elseif ($inMonthIdx < 12) {
                    $learningType = 'in_progress';
                } elseif ($inMonthIdx < 15) {
                    $learningType = 'new';
                } else {
                    $learningType = 'incomplete';
                }
            }

            // Gán khóa học cho học viên
            $enrolledCoursesCount = ($learningType === 'completed') ? rand(2, 3) : rand(1, 2);

            for ($c = 0; $c < $enrolledCoursesCount; $c++) {
                if ($totalCourses === 0) {
                    break;
                }
                $courseId = $courses[($i + $c * 7) % $totalCourses];
                $enrolledAt = $createdAt->copy()->addDays($c * 5 + rand(1, 3))->addHours(rand(1, 8));

                if ($learningType === 'completed' && $c === 0) {
                    // Khóa học đầu tiên hoàn thành 100%
                    $progress = 100.00;
                    $completedLessons = 12;
                    $completedAt = $enrolledAt->copy()->addDays(rand(14, 45))->addHours(rand(1, 6));
                    if ($completedAt->isFuture()) {
                        $completedAt = now()->subDays(rand(1, 5));
                    }
                    $status = Enrollment::STATUS_COMPLETED;
                    $lastAccess = $completedAt;

                    // Cấp Chứng chỉ hoàn thành (Certificate)
                    $certCode = sprintf('FEA-%d-%s', $year, strtoupper(Str::random(6)));
                    Certificate::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'course_id' => $courseId,
                        ],
                        [
                            'certificate_code' => $certCode,
                            'file_path' => 'certificates/cert_' . $user->id . '_' . $courseId . '.pdf',
                            'issued_at' => $completedAt,
                            'created_at' => $completedAt,
                            'updated_at' => $completedAt,
                        ]
                    );
                    $totalCertificatesIssued++;
                    $completedCount++;
                } elseif ($learningType === 'in_progress' || ($learningType === 'completed' && $c > 0)) {
                    // Đang học tích cực: Tiến độ 25% - 85%
                    $progress = (float) rand(25, 85);
                    $completedLessons = (int) round(($progress / 100) * 12);
                    $completedAt = null;
                    $status = Enrollment::STATUS_ACTIVE;
                    $lastAccess = $enrolledAt->copy()->addDays(rand(5, 25));
                    if ($lastAccess->isFuture()) {
                        $lastAccess = now()->subDays(rand(1, 3));
                    }
                    if ($c === 0) {
                        $inProgressCount++;
                    }
                } elseif ($learningType === 'incomplete') {
                    // Chưa hoàn thành / Tạm dừng: Tiến độ 5% - 18%, không học tiếp
                    $progress = (float) rand(5, 18);
                    $completedLessons = 1;
                    $completedAt = null;
                    $status = Enrollment::STATUS_ACTIVE;
                    $lastAccess = $enrolledAt->copy()->addDays(rand(1, 3));
                    if ($c === 0) {
                        $incompleteCount++;
                    }
                } else {
                    // Người dùng mới: Tiến độ 0% hoặc mới bắt đầu bài 1
                    $progress = ($i % 2 === 0) ? 0.00 : 8.33;
                    $completedLessons = ($progress > 0) ? 1 : 0;
                    $completedAt = null;
                    $status = Enrollment::STATUS_ACTIVE;
                    $lastAccess = $enrolledAt->copy()->addHours(rand(1, 4));
                    if ($c === 0) {
                        $newStudentCount++;
                    }
                }

                Enrollment::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'course_id' => $courseId,
                    ],
                    [
                        'status' => $status,
                        'progress_percent' => $progress,
                        'completed_lessons' => $completedLessons,
                        'total_lessons' => 12,
                        'enrolled_at' => $enrolledAt,
                        'completed_at' => $completedAt,
                        'last_accessed_at' => $lastAccess,
                        'created_at' => $enrolledAt,
                        'updated_at' => $lastAccess ?? $enrolledAt,
                    ]
                );

                $totalEnrollments++;
            }

            if ($i % 45 === 0 || $i === $totalStudents) {
                echo "✓ Đã nạp {$i}/{$totalStudents} học viên (Hoàn thành: {$completedCount}, Đang học: {$inProgressCount}, Dở dang: {$incompleteCount}, Mới: {$newStudentCount})\n";
            }
        }

        echo "\n=================================================================\n";
        echo "✓ HOÀN THÀNH NẠP DỮ LIỆU HỌC VIÊN TOÀN DIỆN!\n";
        echo "   • Tổng Học viên: {$totalStudents}\n";
        echo "   • Hoàn thành khóa học (Completed): {$completedCount}\n";
        echo "   • Đang học tích cực (In Progress): {$inProgressCount}\n";
        echo "   • Chưa hoàn thành / Tạm dừng (Incomplete): {$incompleteCount}\n";
        echo "   • Học viên mới đăng ký (New Users): {$newStudentCount}\n";
        echo "   • Tổng lượt đăng ký khóa học (Enrollments): {$totalEnrollments}\n";
        echo "   • Chứng chỉ hoàn thành cấp (Certificates): {$totalCertificatesIssued}\n";
        echo "   • Thời gian phân bổ: Tháng 01/2025 - Tháng 08/2026 (20 tháng)\n";
        echo "=================================================================\n\n";
    }

    private function generateVietnameseName(int $seed): string
    {
        $last = $this->vietnameseLastNames[($seed - 1) % count($this->vietnameseLastNames)];
        $mid = $this->vietnameseMiddleNames[(($seed * 2) + 3) % count($this->vietnameseMiddleNames)];
        $first = $this->vietnameseFirstNames[(($seed * 5) + 1) % count($this->vietnameseFirstNames)];

        return "$last $mid $first";
    }

    private function assignRole(User $user, string $roleSlug): void
    {
        $role = Role::where('slug', $roleSlug)->first();
        if ($role) {
            $user->roles()->syncWithoutDetaching([$role->id]);
        }
    }
}
