<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Nhật ký hoạt động (Activity Logs)
        $activityLogs = [
            [
                'user_id' => 4, // Trần Thị Học
                'action' => 'login',
                'model_type' => 'App\\Models\\User',
                'model_id' => 4,
                'description' => 'Người dùng đăng nhập thành công vào hệ thống.',
                'properties' => json_encode(['browser' => 'Chrome', 'platform' => 'Windows']),
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'user_id' => 4, // Trần Thị Học
                'action' => 'purchase_course',
                'model_type' => 'App\\Models\\Course',
                'model_id' => 1,
                'description' => 'Đã mua khóa học: Laravel từ Zero đến Hero',
                'properties' => json_encode(['price' => 299000.00, 'order_id' => 1]),
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'user_id' => 5, // Lê Văn Học
                'action' => 'login',
                'model_type' => 'App\\Models\\User',
                'model_id' => 5,
                'description' => 'Người dùng đăng nhập hệ thống.',
                'properties' => json_encode(['browser' => 'Safari', 'platform' => 'macOS']),
                'ip_address' => '192.168.1.15',
                'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X)',
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ],
            [
                'user_id' => 5, // Lê Văn Học
                'action' => 'complete_course',
                'model_type' => 'App\\Models\\Course',
                'model_id' => 1,
                'description' => 'Đã hoàn thành khóa học và yêu cầu cấp chứng chỉ: Laravel từ Zero đến Hero',
                'properties' => json_encode(['score_avg' => 95]),
                'ip_address' => '192.168.1.15',
                'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X)',
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay(),
            ],
        ];

        DB::table('activity_logs')->insert($activityLogs);

        // 2. Điểm tích lũy học tập (User Points)
        $userPoints = [
            // Trần Thị Học (User ID: 4) tích lũy 150 điểm
            [
                'user_id' => 4,
                'points' => 10,
                'type' => 'earn',
                'source' => 'lesson_completed',
                'description' => 'Hoàn thành bài giảng 1 khóa Laravel.',
                'created_at' => now()->subDays(4),
                'updated_at' => now()->subDays(4),
            ],
            [
                'user_id' => 4,
                'points' => 10,
                'type' => 'earn',
                'source' => 'lesson_completed',
                'description' => 'Hoàn thành bài giảng 2 khóa Laravel.',
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ],
            [
                'user_id' => 4,
                'points' => 30,
                'type' => 'earn',
                'source' => 'quiz_passed',
                'description' => 'Đạt điểm cao bài kiểm tra trắc nghiệm Chương 1 khóa Laravel.',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'user_id' => 4,
                'points' => 100, // Điểm thưởng khi mua combo khóa học đầu tiên
                'type' => 'earn',
                'source' => 'bonus_first_purchase',
                'description' => 'Thưởng mua combo khóa học lần đầu.',
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],

            // Lê Văn Học (User ID: 5) tích lũy 50 điểm
            [
                'user_id' => 5,
                'points' => 10,
                'type' => 'earn',
                'source' => 'lesson_completed',
                'description' => 'Hoàn thành bài giảng 1 khóa Laravel.',
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ],
            [
                'user_id' => 5,
                'points' => 10,
                'type' => 'earn',
                'source' => 'lesson_completed',
                'description' => 'Hoàn thành bài giảng 2 khóa Laravel.',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'user_id' => 5,
                'points' => 30,
                'type' => 'earn',
                'source' => 'quiz_passed',
                'description' => 'Vượt qua bài trắc nghiệm Chương 1 khóa Laravel.',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
        ];

        DB::table('user_points')->insert($userPoints);

        // 3. Huy hiệu đã nhận (User Badges)
        $userBadges = [
            // Trần Thị Học (User ID: 4) đạt Huy hiệu 1 (Học viên mới) và 2 (Học viên chăm chỉ)
            [
                'user_id' => 4,
                'badge_id' => 1, // Học viên mới
                'earned_at' => now()->subDays(5),
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'user_id' => 4,
                'badge_id' => 2, // Học viên chăm chỉ
                'earned_at' => now()->subDays(2),
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
        ];

        DB::table('user_badges')->insert($userBadges);

        // 4. Chứng chỉ (Certificates)
        $certificates = [
            // Lê Văn Học (User ID: 5) hoàn thành khóa Laravel (ID: 1) được cấp chứng chỉ
            [
                'user_id' => 5,
                'course_id' => 1,
                'certificate_code' => 'CERT-LARAVEL-'.Str::upper(Str::random(8)),
                'file_path' => 'certificates/cert-laravel-user-5.pdf',
                'issued_at' => now()->subDay(),
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay(),
            ],
        ];

        DB::table('certificates')->insert($certificates);

        // --- BỔ SUNG CÁC BẢNG TRONG 46 MIGRATION CHƯA ĐƯỢC SEED ---

        // 5. Thông báo đẩy (Push Notifications)
        $pushNotifications = [
            [
                'user_id' => 4, // Trần Thị Học
                'title' => 'Bạn đã mở khóa huy hiệu mới!',
                'message' => 'Chúc mừng bạn đã đạt huy hiệu "Học viên chăm chỉ" nhờ tích lũy 50 điểm học tập.',
                'type' => 'badge_unlocked',
                'url' => '/profile/achievements',
                'is_read' => true,
                'read_at' => now()->subDays(2),
                'created_at' => now()->subDays(2),
                'updated_at' => now(),
            ],
            [
                'user_id' => 4, // Trần Thị Học
                'title' => 'Có phản hồi mới từ giảng viên',
                'message' => 'Giảng viên Nguyễn Văn Giảng đã trả lời câu hỏi thảo luận của bạn tại Bài 2.',
                'type' => 'discussion_reply',
                'url' => '/courses/1/lessons/2#discussion-1',
                'is_read' => false,
                'read_at' => null,
                'created_at' => now()->subDays(4)->addHours(2),
                'updated_at' => now()->subDays(4)->addHours(2),
            ],
            [
                'user_id' => 2, // Nguyễn Văn Giảng
                'title' => 'Có học viên mới ghi danh',
                'message' => 'Học viên Trần Thị Học vừa ghi danh khóa học Laravel từ Zero đến Hero.',
                'type' => 'new_enrollment',
                'url' => '/instructor/courses',
                'is_read' => false,
                'read_at' => null,
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay(),
            ],
            [
                'user_id' => 2,
                'title' => 'Thông báo từ quản trị viên',
                'message' => 'Hệ thống sẽ bảo trì vào 22:00 tối nay. Vui lòng hoàn tất upload bài giảng trước thời gian này.',
                'type' => 'announcement',
                'url' => null,
                'is_read' => false,
                'read_at' => null,
                'created_at' => now()->subHours(3),
                'updated_at' => now()->subHours(3),
            ],
        ];

        DB::table('push_notifications')->insert($pushNotifications);

        // 6. Khóa học xem gần đây (Recently Viewed Courses)
        $recentlyViewed = [
            [
                'user_id' => 4, // Trần Thị Học
                'course_id' => 1, // Khóa Laravel
                'last_viewed_at' => now()->subHours(1),
                'created_at' => now()->subHours(1),
                'updated_at' => now()->subHours(1),
            ],
            [
                'user_id' => 4, // Trần Thị Học
                'course_id' => 2, // Khóa React
                'last_viewed_at' => now()->subHours(3),
                'created_at' => now()->subHours(3),
                'updated_at' => now()->subHours(3),
            ],
            [
                'user_id' => 5, // Lê Văn Học
                'course_id' => 1, // Khóa Laravel
                'last_viewed_at' => now()->subDays(1),
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
        ];

        DB::table('recently_viewed_courses')->insert($recentlyViewed);
    }
}
