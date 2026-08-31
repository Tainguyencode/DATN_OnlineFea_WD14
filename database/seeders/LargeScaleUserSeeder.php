<?php

namespace Database\Seeders;

use App\Models\Course;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LargeScaleUserSeeder extends Seeder
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
        $targetTotal = 100000;

        echo "\n=========================================================================\n";
        echo "   BẮT ĐẦU NẠP 100.000 USERS BIẾN ĐỘNG & TIẾN ĐỘ HỌC TẬP (2025 - 2026)\n";
        echo "=========================================================================\n\n";

        $startTime = microtime(true);
        $hashedPassword = Hash::make('password');

        // Phân bổ biến động tự nhiên cho từng tháng trong 20 tháng (Tổng = 100.000 users)
        $monthlyDistribution = [
            ['year' => 2025, 'month' => 1,  'count' => 2850],
            ['year' => 2025, 'month' => 2,  'count' => 3200],
            ['year' => 2025, 'month' => 3,  'count' => 4100],
            ['year' => 2025, 'month' => 4,  'count' => 3750],
            ['year' => 2025, 'month' => 5,  'count' => 4500],
            ['year' => 2025, 'month' => 6,  'count' => 5100],
            ['year' => 2025, 'month' => 7,  'count' => 5450],
            ['year' => 2025, 'month' => 8,  'count' => 4900],
            ['year' => 2025, 'month' => 9,  'count' => 5600],
            ['year' => 2025, 'month' => 10, 'count' => 5950],
            ['year' => 2025, 'month' => 11, 'count' => 6300],
            ['year' => 2025, 'month' => 12, 'count' => 6750],
            ['year' => 2026, 'month' => 1,  'count' => 4800],
            ['year' => 2026, 'month' => 2,  'count' => 5200],
            ['year' => 2026, 'month' => 3,  'count' => 6100],
            ['year' => 2026, 'month' => 4,  'count' => 5700],
            ['year' => 2026, 'month' => 5,  'count' => 6400],
            ['year' => 2026, 'month' => 6,  'count' => 6800],
            ['year' => 2026, 'month' => 7,  'count' => 7200],
            ['year' => 2026, 'month' => 8,  'count' => 3350],
        ];

        $courses = Course::pluck('id')->all();
        if (empty($courses)) {
            $courses = [1, 2, 3, 4, 5];
        }
        $totalCourses = count($courses);

        $lastNamesCount = count($this->vietnameseLastNames);
        $middleNamesCount = count($this->vietnameseMiddleNames);
        $firstNamesCount = count($this->vietnameseFirstNames);

        $now = now();
        $userCounter = 0;
        $totalCertificates = 0;
        $totalCompleted = 0;
        $totalInProgress = 0;
        $totalIncomplete = 0;
        $totalNew = 0;

        // Dọn dẹp các user thử nghiệm trước đó
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('certificates')->truncate();
        DB::table('enrollments')->truncate();
        DB::table('users')->where('username', 'like', 'user_%')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Lấy ID xuất phát cho user mới để tránh xung đột
        $maxUserId = DB::table('users')->max('id') ?? 0;
        $currentUserId = $maxUserId + 1;

        $userBatch = [];
        $enrollmentBatch = [];
        $certificateBatch = [];

        foreach ($monthlyDistribution as $mIdx => $slot) {
            $year = $slot['year'];
            $month = $slot['month'];
            $monthCount = $slot['count'];
            $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;

            // Tỷ lệ phân bổ trạng thái theo giai đoạn
            // 2025: Đa số đã hoàn thành hoặc đang học
            // 2026 gần đây: Có nhiều người dùng mới và đang học
            if ($year === 2025) {
                $completedRatio = 0.38;   // 38% hoàn thành
                $inProgressRatio = 0.42;  // 42% đang học
                $incompleteRatio = 0.15;  // 15% dở dang
                $newRatio = 0.05;         // 5% mới
            } else {
                $completedRatio = ($month <= 4) ? 0.32 : 0.22;
                $inProgressRatio = 0.45;
                $incompleteRatio = 0.15;
                $newRatio = 1.0 - ($completedRatio + $inProgressRatio + $incompleteRatio);
            }

            $completedThreshold = (int) ($monthCount * $completedRatio);
            $inProgressThreshold = $completedThreshold + (int) ($monthCount * $inProgressRatio);
            $incompleteThreshold = $inProgressThreshold + (int) ($monthCount * $incompleteRatio);

            for ($u = 1; $u <= $monthCount; $u++) {
                $userCounter++;
                $userId = $currentUserId++;

                $day = (($u * 3 + ($userCounter % 7)) % min(28, $daysInMonth)) + 1;
                $hour = 7 + (($u * 3) % 15);
                $minute = ($u * 11) % 60;
                $second = ($u * 17) % 60;

                $createdAt = sprintf('%04d-%02d-%02d %02d:%02d:%02d', $year, $month, $day, $hour, $minute, $second);
                $createdCarbon = Carbon::create($year, $month, $day, $hour, $minute, $second);

                // Lần đăng nhập
                $isRecent = ($year === 2026 && $month >= 6);
                if ($isRecent) {
                    $loginDaysAgo = ($userCounter % 30);
                    $loginHoursAgo = ($userCounter % 24);
                    $lastLoginAt = $now->copy()->subDays($loginDaysAgo)->subHours($loginHoursAgo)->format('Y-m-d H:i:s');
                } else {
                    $lastLoginAt = $createdAt;
                }

                // Họ tên
                $last = $this->vietnameseLastNames[($userCounter - 1) % $lastNamesCount];
                $mid = $this->vietnameseMiddleNames[(($userCounter * 3) + 1) % $middleNamesCount];
                $first = $this->vietnameseFirstNames[(($userCounter * 7) + 2) % $firstNamesCount];
                $name = "{$last} {$mid} {$first}";

                $username = sprintf('user_%06d', $userCounter);
                $email = sprintf('user_%06d@onlinefea.edu.vn', $userCounter);
                $phone = sprintf('089%07d', $userCounter);

                if ($userCounter % 500 === 0) {
                    $role = 'admin';
                } elseif ($userCounter % 35 === 0) {
                    $role = 'instructor';
                } else {
                    $role = 'student';
                }

                $avatar = 'https://images.unsplash.com/photo-' . (1530000000000 + ($userCounter * 914159) % 50000000) . '?w=150&auto=format&fit=crop&q=80';

                $userBatch[] = [
                    'id' => $userId,
                    'name' => $name,
                    'username' => $username,
                    'email' => $email,
                    'phone' => $phone,
                    'role' => $role,
                    'avatar' => $avatar,
                    'bio' => 'Thành viên cộng đồng học tập trực tuyến OnlineFEA.',
                    'password' => $hashedPassword,
                    'is_active' => ($userCounter % 250 !== 0),
                    'account_status' => ($userCounter % 250 !== 0) ? 'active' : 'locked',
                    'email_verified_at' => $createdAt,
                    'last_login_at' => $lastLoginAt,
                    'two_factor_enabled' => false,
                    'created_at' => $createdAt,
                    'updated_at' => $lastLoginAt,
                ];

                // Xác định trạng thái học tập của User
                $courseId = $courses[($userCounter * 3) % $totalCourses];
                $enrolledAt = $createdAt;

                if ($u <= $completedThreshold) {
                    // HOÀN THÀNH (100% + Chứng chỉ)
                    $completedDays = rand(15, 45);
                    $completedAt = $createdCarbon->copy()->addDays($completedDays)->format('Y-m-d H:i:s');
                    $enrollmentBatch[] = [
                        'user_id' => $userId,
                        'course_id' => $courseId,
                        'order_id' => null,
                        'status' => 'completed',
                        'progress_percent' => 100.00,
                        'completed_lessons' => 12,
                        'total_lessons' => 12,
                        'enrolled_at' => $enrolledAt,
                        'completed_at' => $completedAt,
                        'last_accessed_at' => $completedAt,
                        'created_at' => $enrolledAt,
                        'updated_at' => $completedAt,
                    ];

                    $certificateBatch[] = [
                        'user_id' => $userId,
                        'course_id' => $courseId,
                        'certificate_code' => sprintf('FEA-%d-%06d-%s', $year, $userCounter, strtoupper(Str::random(4))),
                        'file_path' => 'certificates/cert_' . $userId . '_' . $courseId . '.pdf',
                        'issued_at' => $completedAt,
                        'created_at' => $completedAt,
                        'updated_at' => $completedAt,
                    ];

                    $totalCompleted++;
                    $totalCertificates++;
                } elseif ($u <= $inProgressThreshold) {
                    // ĐANG HỌC (Tiến độ 25% - 85%)
                    $progress = (float) rand(25, 85);
                    $completedLessons = (int) round(($progress / 100) * 12);
                    $lastAccess = $createdCarbon->copy()->addDays(rand(5, 20))->format('Y-m-d H:i:s');

                    $enrollmentBatch[] = [
                        'user_id' => $userId,
                        'course_id' => $courseId,
                        'order_id' => null,
                        'status' => 'active',
                        'progress_percent' => $progress,
                        'completed_lessons' => $completedLessons,
                        'total_lessons' => 12,
                        'enrolled_at' => $enrolledAt,
                        'completed_at' => null,
                        'last_accessed_at' => $lastAccess,
                        'created_at' => $enrolledAt,
                        'updated_at' => $lastAccess,
                    ];
                    $totalInProgress++;
                } elseif ($u <= $incompleteThreshold) {
                    // DỞ DANG / CHƯA XONG (Tiến độ < 20%)
                    $progress = (float) rand(5, 18);
                    $lastAccess = $createdCarbon->copy()->addDays(rand(1, 3))->format('Y-m-d H:i:s');

                    $enrollmentBatch[] = [
                        'user_id' => $userId,
                        'course_id' => $courseId,
                        'order_id' => null,
                        'status' => 'active',
                        'progress_percent' => $progress,
                        'completed_lessons' => 1,
                        'total_lessons' => 12,
                        'enrolled_at' => $enrolledAt,
                        'completed_at' => null,
                        'last_accessed_at' => $lastAccess,
                        'created_at' => $enrolledAt,
                        'updated_at' => $lastAccess,
                    ];
                    $totalIncomplete++;
                } else {
                    // NGƯỜI DÙNG MỚI (Tiến độ 0% hoặc mới bắt đầu)
                    $progress = ($userCounter % 2 === 0) ? 0.00 : 8.33;
                    $enrollmentBatch[] = [
                        'user_id' => $userId,
                        'course_id' => $courseId,
                        'order_id' => null,
                        'status' => 'active',
                        'progress_percent' => $progress,
                        'completed_lessons' => ($progress > 0) ? 1 : 0,
                        'total_lessons' => 12,
                        'enrolled_at' => $enrolledAt,
                        'completed_at' => null,
                        'last_accessed_at' => $enrolledAt,
                        'created_at' => $enrolledAt,
                        'updated_at' => $enrolledAt,
                    ];
                    $totalNew++;
                }
            }

            echo sprintf("   • Tháng %02d/%d: %5d Users (Hoàn thành: %4d | Đang học: %4d | Dở dang: %4d | Mới: %4d)\n",
                $month, $year, $monthCount,
                $completedThreshold,
                $inProgressThreshold - $completedThreshold,
                $incompleteThreshold - $inProgressThreshold,
                $monthCount - $incompleteThreshold
            );
        }

        // Chèn vào Database theo từng lô
        echo "\n--> Đang chèn " . number_format(count($userBatch)) . " Users vào Database...\n";
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach (array_chunk($userBatch, 2500) as $chunk) {
            DB::table('users')->insert($chunk);
        }

        echo "--> Đang chèn " . number_format(count($enrollmentBatch)) . " Tiến độ học tập (Enrollments)...\n";
        foreach (array_chunk($enrollmentBatch, 2500) as $chunk) {
            DB::table('enrollments')->insert($chunk);
        }

        echo "--> Đang chèn " . number_format(count($certificateBatch)) . " Chứng chỉ hoàn thành...\n";
        foreach (array_chunk($certificateBatch, 2000) as $chunk) {
            DB::table('certificates')->insert($chunk);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $totalElapsed = round(microtime(true) - $startTime, 2);

        echo "\n=========================================================================\n";
        echo "✓ HOÀN TẤT NẠP THÀNH CÔNG 100.000 USERS VÀ TIẾN ĐỘ HỌC TẬP!\n";
        echo "   • Tổng số Users: " . number_format(DB::table('users')->count()) . "\n";
        echo "   • Hoàn thành khóa học (Completed): " . number_format($totalCompleted) . "\n";
        echo "   • Đang học tích cực (In Progress): " . number_format($totalInProgress) . "\n";
        echo "   • Dở dang / Chưa xong (Incomplete): " . number_format($totalIncomplete) . "\n";
        echo "   • Người dùng mới (New Users): " . number_format($totalNew) . "\n";
        echo "   • Chứng chỉ cấp (Certificates): " . number_format($totalCertificates) . "\n";
        echo "   • Thời gian thực thi: {$totalElapsed} giây\n";
        echo "=========================================================================\n\n";
    }
}
