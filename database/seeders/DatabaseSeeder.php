<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Tắt ràng buộc khóa ngoại để tiến hành dọn dẹp (truncate) cơ sở dữ liệu
        Schema::disableForeignKeyConstraints();

        // Danh sách toàn bộ các bảng trong hệ thống cần dọn dẹp trước khi seed dữ liệu mới
        $tables = [
            'activity_logs',
            'user_badges',
            'user_points',
            'certificates',
            'enrollments',
            'payments',
            'order_items',
            'orders',
            'wishlists',
            'cart_items',
            'carts',
            'discussion_replies',
            'discussions',
            'submissions',
            'assignments',
            'quiz_attempts',
            'quiz_options',
            'quiz_questions',
            'quizzes',
            'ai_summaries',
            'lesson_subtitles',
            'lesson_attachments',
            'lessons',
            'chapters',
            'learning_path_courses',
            'learning_paths',
            'courses',
            'faqs',
            'coupons',
            'badges',
            'categories',
            'users',
            'homepage_settings',
            'push_notifications',
            'recently_viewed_courses',
            'live_sessions',
            'study_groups',
            'video_notes',
            'ai_chat_messages',
            'support_tickets',
            'support_ticket_messages',
        ];

        foreach ($tables as $table) {
            // Kiểm tra bảng tồn tại để tránh phát sinh lỗi nếu chưa chạy đầy đủ migrations
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
            }
        }

        // Kích hoạt lại ràng buộc khóa ngoại sau khi đã dọn dẹp sạch sẽ
        Schema::enableForeignKeyConstraints();

        // 2. Thực thi các Seeder theo trình tự logic khóa ngoại (Dependencies)
        $this->call([
            // Bảng độc lập (Không có khóa ngoại)
            UserSeeder::class,
            CategorySeeder::class,
            BadgeSeeder::class,
            CouponSeeder::class,
            FaqSeeder::class,
            HomepageSettingSeeder::class,

            // Bảng phụ thuộc cấp 1 (Chỉ chứa khóa ngoại liên kết đến các bảng trên)
            CourseSeeder::class, // Phụ thuộc vào users và categories

            // Bảng phụ thuộc cấp 2 (Phụ thuộc vào khóa học và bài học)
            LearningPathSeeder::class, // Phụ thuộc vào courses qua bảng pivot
            InteractionSeeder::class,  // Phụ thuộc vào users, courses, lessons, quizzes, assignments, v.v.

            // Bảng ghi chép lịch sử và hoạt động hệ thống
            SystemSeeder::class, // Phụ thuộc vào users, badges, courses
        ]);
    }
}
