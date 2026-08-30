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
            'quiz_attempt_answers',
            'quiz_attempts',
            'quiz_version_questions',
            'question_versions',
            'quiz_versions',
            'quiz_options',
            'quiz_questions',
            'quizzes',
            'ai_summaries',
            'lesson_subtitles',
            'lesson_attachments',
            'lessons',
            'chapters',
            'course_sections',
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
            'reviews',
            'lesson_progress',
            'course_reviews',
            'quiz_version_question_invalidations',
            'quiz_attempt_regrades',
            'quiz_version_questions',
            'question_versions',
            'quiz_versions',
            'instructor_profiles',
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
            CourseSeeder::class,
            CourseReviewSeeder::class,

            // Bảng phụ thuộc cấp 2 (Phụ thuộc vào khóa học và bài học)
            LearningPathSeeder::class, // Phụ thuộc vào courses qua bảng pivot
            // InteractionSeeder::class,

            // Bảng ghi chép lịch sử và hoạt động hệ thống
            // SystemSeeder::class, // Đã comment vì thiếu mock data users/courses

            // Thêm dữ liệu mẫu bổ sung cho progress tracking
            // ExpandedSampleDataSeeder::class,
        ]);

        // 3. Tự động đồng bộ course_sections và cập nhật lessons (course_id, section_id) từ chapters
        $this->backfillCourseSections();

        // 4. Tạo đăng ký khóa học mẫu cho học sinh
        $this->seedEnrollments();
    }

    /**
     * Tự động đồng bộ hóa dữ liệu từ chapters sang course_sections và cập nhật lessons
     */
    private function backfillCourseSections(): void
    {
        echo "\n========== ĐỒNG BỘ HÓA COURSE SECTIONS ==========\n";

        $chapters = DB::table('chapters')->orderBy('id')->get();

        foreach ($chapters as $chapter) {
            $sectionId = DB::table('course_sections')->insertGetId([
                'course_id' => $chapter->course_id,
                'title' => $chapter->title,
                'description' => null,
                'sort_order' => $chapter->sort_order,
                'created_at' => $chapter->created_at ?? now(),
                'updated_at' => $chapter->updated_at ?? now(),
            ]);

            DB::table('lessons')
                ->where('chapter_id', $chapter->id)
                ->update([
                    'course_id' => $chapter->course_id,
                    'section_id' => $sectionId,
                    'duration' => DB::raw('COALESCE(duration, duration_seconds)'),
                ]);
        }

        echo '✓ Đồng bộ thành công '.count($chapters)." chương học sang course_sections!\n\n";
    }

    /**
     * Tạo đăng ký khóa học mẫu cho học sinh
     */
    private function seedEnrollments(): void
    {
        echo "\n========== TẠO ĐĂNG KÝ KHÓA HỌC MẪU (ENROLLMENTS) ==========\n";

        $student1 = DB::table('users')->where('email', 'student@example.com')->value('id');
        $student2 = DB::table('users')->where('email', 'student2@example.com')->value('id');
        $student3 = DB::table('users')->where('email', 'student3@example.com')->value('id');
        $student4 = DB::table('users')->where('email', 'student4@example.com')->value('id');
        $qtrung = DB::table('users')->where('email', 'tungazquoc@gmail.com')->value('id');

        $enrollments = [];

        if ($student1) {
            $enrollments[] = [
                'user_id' => $student1,
                'course_id' => 1,
                'status' => 'active',
                'progress_percent' => 35.00,
                'enrolled_at' => now()->subDays(10),
                'created_at' => now()->subDays(10),
                'updated_at' => now(),
            ];
            $enrollments[] = [
                'user_id' => $student1,
                'course_id' => 2,
                'status' => 'active',
                'progress_percent' => 10.00,
                'enrolled_at' => now()->subDays(5),
                'created_at' => now()->subDays(5),
                'updated_at' => now(),
            ];
            $enrollments[] = [
                'user_id' => $student1,
                'course_id' => 3,
                'status' => 'active',
                'progress_percent' => 0.00,
                'enrolled_at' => now()->subDays(2),
                'created_at' => now()->subDays(2),
                'updated_at' => now(),
            ];
        }

        if ($student2) {
            $enrollments[] = [
                'user_id' => $student2,
                'course_id' => 1,
                'status' => 'active',
                'progress_percent' => 0.00,
                'enrolled_at' => now()->subDays(3),
                'created_at' => now()->subDays(3),
                'updated_at' => now(),
            ];
            $enrollments[] = [
                'user_id' => $student2,
                'course_id' => 2,
                'status' => 'active',
                'progress_percent' => 45.00,
                'enrolled_at' => now()->subDays(6),
                'created_at' => now()->subDays(6),
                'updated_at' => now(),
            ];
        }

        if ($student3) {
            $enrollments[] = [
                'user_id' => $student3,
                'course_id' => 1,
                'status' => 'active',
                'progress_percent' => 60.00,
                'enrolled_at' => now()->subDays(8),
                'created_at' => now()->subDays(8),
                'updated_at' => now(),
            ];
        }

        if ($student4) {
            $enrollments[] = [
                'user_id' => $student4,
                'course_id' => 1,
                'status' => 'active',
                'progress_percent' => 15.00,
                'enrolled_at' => now()->subDays(4),
                'created_at' => now()->subDays(4),
                'updated_at' => now(),
            ];
        }

        if ($qtrung) {
            $enrollments[] = [
                'user_id' => $qtrung,
                'course_id' => 1,
                'status' => 'active',
                'progress_percent' => 25.00,
                'enrolled_at' => now()->subDays(10),
                'created_at' => now()->subDays(10),
                'updated_at' => now(),
            ];
            $enrollments[] = [
                'user_id' => $qtrung,
                'course_id' => 2,
                'status' => 'active',
                'progress_percent' => 50.00,
                'enrolled_at' => now()->subDays(7),
                'created_at' => now()->subDays(7),
                'updated_at' => now(),
            ];
            $enrollments[] = [
                'user_id' => $qtrung,
                'course_id' => 3,
                'status' => 'active',
                'progress_percent' => 0.00,
                'enrolled_at' => now()->subDays(5),
                'created_at' => now()->subDays(5),
                'updated_at' => now(),
            ];
            $enrollments[] = [
                'user_id' => $qtrung,
                'course_id' => 4,
                'status' => 'active',
                'progress_percent' => 10.00,
                'enrolled_at' => now()->subDays(3),
                'created_at' => now()->subDays(3),
                'updated_at' => now(),
            ];
        }

        $validCourseIds = DB::table('courses')->pluck('id')->all();

        foreach ($enrollments as $enrollment) {
            if (in_array($enrollment['course_id'], $validCourseIds, true)) {
                DB::table('enrollments')->updateOrInsert(
                    ['user_id' => $enrollment['user_id'], 'course_id' => $enrollment['course_id']],
                    $enrollment
                );
            }
        }

        echo "✓ Đã đăng ký thành công các khóa học mẫu cho học sinh!\n\n";
    }
}
