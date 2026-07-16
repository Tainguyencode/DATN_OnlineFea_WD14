<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InteractionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Tạo Giỏ hàng (Carts) - Mỗi học viên có 1 giỏ hàng trống hoặc có đồ
        $carts = [
            [
                'id' => 1,
                'user_id' => 4, // Trần Thị Học
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'user_id' => 5, // Lê Văn Học
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'user_id' => 6, // Phạm Minh Tuấn
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('carts')->insert($carts);

        // 2. Tạo Chi tiết giỏ hàng (Cart Items)
        $cartItems = [
            [
                'cart_id' => 1,
                'course_id' => 1, // Thêm khóa Laravel vào giỏ hàng của Trần Thị Học
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'cart_id' => 2,
                'course_id' => 2, // Thêm khóa React vào giỏ hàng của Lê Văn Học
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('cart_items')->insert($cartItems);

        // 3. Tạo Danh sách yêu thích (Wishlists)
        $wishlists = [
            [
                'user_id' => 4,
                'course_id' => 1, // Trần Thị Học thích khóa Laravel
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 5,
                'course_id' => 2, // Lê Văn Học thích khóa React
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('wishlists')->insert($wishlists);

        // 4. Tạo Đơn hàng (Orders)
        $orders = [
            [
                'id' => 1,
                'order_code' => 'ORD-2026-0001',
                'user_id' => 4, // Trần Thị Học
                'coupon_id' => 1, // WELCOME20 (giảm 20%)
                'subtotal' => 1098000.00, // Laravel 499k + React 599k
                'discount_amount' => 219600.00, // 20% của 1098k
                'total_amount' => 878400.00,
                'status' => 'paid',
                'payment_method' => 'vnpay',
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'id' => 2,
                'order_code' => 'ORD-2026-0002',
                'user_id' => 5, // Lê Văn Học
                'coupon_id' => null,
                'subtotal' => 499000.00, // Mua khóa Laravel
                'discount_amount' => 0.00,
                'total_amount' => 499000.00,
                'status' => 'paid',
                'payment_method' => 'momo',
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ],
            [
                'id' => 3,
                'order_code' => 'ORD-2026-0003',
                'user_id' => 6, // Phạm Minh Tuấn
                'coupon_id' => null,
                'subtotal' => 399000.00, // Đang mua khóa UI/UX
                'discount_amount' => 0.00,
                'total_amount' => 399000.00,
                'status' => 'pending',
                'payment_method' => 'bank_transfer',
                'created_at' => now()->subHours(5),
                'updated_at' => now()->subHours(5),
            ]
        ];

        DB::table('orders')->insert($orders);

        // 5. Tạo Chi tiết đơn hàng (Order Items)
        $orderItems = [
            // Đơn 1
            [
                'order_id' => 1,
                'course_id' => 1,
                'price' => 499000.00,
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'order_id' => 1,
                'course_id' => 2,
                'price' => 599000.00,
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            // Đơn 2
            [
                'order_id' => 2,
                'course_id' => 1,
                'price' => 499000.00,
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ],
            // Đơn 3 (Chờ thanh toán)
            [
                'order_id' => 3,
                'course_id' => 3,
                'price' => 399000.00,
                'created_at' => now()->subHours(5),
                'updated_at' => now()->subHours(5),
            ]
        ];

        DB::table('order_items')->insert($orderItems);

        // 6. Tạo Giao dịch thanh toán (Payments)
        $payments = [
            [
                'order_id' => 1,
                'gateway' => 'vnpay',
                'transaction_id' => 'VNP2026998877',
                'amount' => 878400.00,
                'status' => 'success',
                'gateway_response' => json_encode(['code' => '00', 'message' => 'Success', 'txn_ref' => 'ORD-2026-0001']),
                'paid_at' => now()->subDays(5),
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'order_id' => 2,
                'gateway' => 'momo',
                'transaction_id' => 'MOMO2026112233',
                'amount' => 499000.00,
                'status' => 'success',
                'gateway_response' => json_encode(['resultCode' => 0, 'message' => 'Thành công']),
                'paid_at' => now()->subDays(3),
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ]
        ];

        DB::table('payments')->insert($payments);

        // 7. Đăng ký khóa học (Enrollments)
        $enrollments = [
            [
                'user_id' => 4, // Trần Thị Học
                'course_id' => 1, // Khóa Laravel
                'order_id' => 1,
                'progress_percent' => 60.00, // Hoàn thành 3/5 bài học (Bài 1, 2, 3)
                'completed_at' => null,
                'created_at' => now()->subDays(5),
                'updated_at' => now(),
            ],
            [
                'user_id' => 4, // Trần Thị Học
                'course_id' => 2, // Khóa React
                'order_id' => 1,
                'progress_percent' => 0.00,
                'completed_at' => null,
                'created_at' => now()->subDays(5),
                'updated_at' => now(),
            ],
            [
                'user_id' => 5, // Lê Văn Học
                'course_id' => 1, // Khóa Laravel
                'order_id' => 2,
                'progress_percent' => 100.00, // Hoàn thành 5/5 bài học
                'completed_at' => now()->subDay(),
                'created_at' => now()->subDays(3),
                'updated_at' => now(),
            ],
            [
                'user_id' => 8, // Nguyễn Thị Mai
                'course_id' => 2, // Khóa React
                'order_id' => null,
                'progress_percent' => 25.00, // Bắt đầu học
                'completed_at' => null,
                'created_at' => now()->subDays(2),
                'updated_at' => now(),
            ],
            [
                'user_id' => 9, // Đặng Công Hùng
                'course_id' => 5, // Khóa Vue.js
                'order_id' => null,
                'progress_percent' => 35.00,
                'completed_at' => null,
                'created_at' => now()->subDays(1),
                'updated_at' => now(),
            ],
            [
                'user_id' => 10, // Hoàng Lan Anh
                'course_id' => 1, // Khóa Laravel
                'order_id' => null,
                'progress_percent' => 15.00,
                'completed_at' => null,
                'created_at' => now()->subHours(12),
                'updated_at' => now(),
            ],
            [
                'user_id' => 11, // Tạ Minh Khôi
                'course_id' => 6, // Khóa Node.js
                'order_id' => null,
                'progress_percent' => 50.00,
                'completed_at' => null,
                'created_at' => now()->subDays(4),
                'updated_at' => now(),
            ],
            [
                'user_id' => 12, // Bùi Quỳnh Linh
                'course_id' => 3, // Khóa UI/UX
                'order_id' => null,
                'progress_percent' => 20.00,
                'completed_at' => null,
                'created_at' => now()->subDays(3),
                'updated_at' => now(),
            ]
        ];

        DB::table('enrollments')->insert($enrollments);

        // 8. Tiến độ bài học (Lesson Progress)
        $progress = [
            // Trần Thị Học (User ID: 4) học khóa Laravel (ID: 1)
            [
                'user_id' => 4,
                'lesson_id' => 1, // Bài 1
                'watched_seconds' => 900,
                'is_completed' => true,
                'completed_at' => now()->subDays(4),
                'created_at' => now()->subDays(5),
                'updated_at' => now(),
            ],
            [
                'user_id' => 4,
                'lesson_id' => 2, // Bài 2
                'watched_seconds' => 1200,
                'is_completed' => true,
                'completed_at' => now()->subDays(3),
                'created_at' => now()->subDays(4),
                'updated_at' => now(),
            ],
            [
                'user_id' => 4,
                'lesson_id' => 3, // Bài 3 (Quiz)
                'watched_seconds' => 0,
                'is_completed' => true,
                'completed_at' => now()->subDays(2),
                'created_at' => now()->subDays(3),
                'updated_at' => now(),
            ],
            [
                'user_id' => 4,
                'lesson_id' => 4, // Bài 4
                'watched_seconds' => 450, // Học dở dang
                'is_completed' => false,
                'completed_at' => null,
                'created_at' => now()->subDay(),
                'updated_at' => now(),
            ],

            // Lê Văn Học (User ID: 5) hoàn thành toàn bộ khóa Laravel (ID: 1)
            [
                'user_id' => 5,
                'lesson_id' => 1,
                'watched_seconds' => 900,
                'is_completed' => true,
                'completed_at' => now()->subDays(3),
                'created_at' => now()->subDays(3),
                'updated_at' => now(),
            ],
            [
                'user_id' => 5,
                'lesson_id' => 2,
                'watched_seconds' => 1200,
                'is_completed' => true,
                'completed_at' => now()->subDays(2),
                'created_at' => now()->subDays(2),
                'updated_at' => now(),
            ],
            [
                'user_id' => 5,
                'lesson_id' => 3, // Quiz
                'watched_seconds' => 0,
                'is_completed' => true,
                'completed_at' => now()->subDays(2),
                'created_at' => now()->subDays(2),
                'updated_at' => now(),
            ],
            [
                'user_id' => 5,
                'lesson_id' => 4,
                'watched_seconds' => 1800,
                'is_completed' => true,
                'completed_at' => now()->subDay(),
                'created_at' => now()->subDay(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 5,
                'lesson_id' => 5, // Assignment
                'watched_seconds' => 0,
                'is_completed' => true,
                'completed_at' => now()->subDay(),
                'created_at' => now()->subDay(),
                'updated_at' => now(),
            ],
            // Nguyễn Thị Mai (User ID: 8) bắt đầu học React
            [
                'user_id' => 8,
                'lesson_id' => 6,
                'watched_seconds' => 800,
                'is_completed' => false,
                'completed_at' => null,
                'created_at' => now()->subDays(2),
                'updated_at' => now(),
            ],
            // Đặng Công Hùng (User ID: 9) học Vue.js
            [
                'user_id' => 9,
                'lesson_id' => 10,
                'watched_seconds' => 1200,
                'is_completed' => true,
                'completed_at' => now()->subDays(1),
                'created_at' => now()->subDays(1),
                'updated_at' => now(),
            ],
            [
                'user_id' => 9,
                'lesson_id' => 11,
                'watched_seconds' => 600,
                'is_completed' => false,
                'completed_at' => null,
                'created_at' => now()->subHours(12),
                'updated_at' => now(),
            ],
            // Hoàng Lan Anh (User ID: 10) vừa bắt đầu Laravel
            [
                'user_id' => 10,
                'lesson_id' => 1,
                'watched_seconds' => 300,
                'is_completed' => false,
                'completed_at' => null,
                'created_at' => now()->subHours(12),
                'updated_at' => now(),
            ],
            // Tạ Minh Khôi (User ID: 11) học Node.js
            [
                'user_id' => 11,
                'lesson_id' => 12,
                'watched_seconds' => 1200,
                'is_completed' => true,
                'completed_at' => now()->subDays(2),
                'created_at' => now()->subDays(4),
                'updated_at' => now(),
            ],
            [
                'user_id' => 11,
                'lesson_id' => 13,
                'watched_seconds' => 900,
                'is_completed' => false,
                'completed_at' => null,
                'created_at' => now()->subDays(1),
                'updated_at' => now(),
            ],
            // Bùi Quỳnh Linh (User ID: 12) học UI/UX
            [
                'user_id' => 12,
                'lesson_id' => 7,
                'watched_seconds' => 450,
                'is_completed' => false,
                'completed_at' => null,
                'created_at' => now()->subDays(3),
                'updated_at' => now(),
            ]
        ];

        DB::table('lesson_progress')->insert($progress);

        // 9. Lượt làm quiz kiểm tra (Quiz Attempts)
        $attempts = [
            [
                'user_id' => 4, // Trần Thị Học
                'quiz_id' => 1,
                'score' => 80, // Đạt 80% (đạt)
                'passed' => true,
                'answers' => json_encode([
                    '1' => [1],
                    '2' => [2],
                    '3' => [1, 2] // Sai 1 ý
                ]),
                'started_at' => now()->subDays(2)->subMinutes(12),
                'completed_at' => now()->subDays(2),
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'user_id' => 5, // Lê Văn Học
                'quiz_id' => 1,
                'score' => 100, // Đạt 100%
                'passed' => true,
                'answers' => json_encode([
                    '1' => [1],
                    '2' => [2],
                    '3' => [1, 2, 4] // Đúng hết
                ]),
                'started_at' => now()->subDays(2)->subMinutes(8),
                'completed_at' => now()->subDays(2),
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ]
        ];

        DB::table('quiz_attempts')->insert($attempts);

        // 10. Bài nộp tự luận (Submissions)
        $submissions = [
            [
                'assignment_id' => 1,
                'user_id' => 4, // Trần Thị Học đã nộp
                'file_path' => 'submissions/tran-thi-hoc-blog-migrations.zip',
                'content' => 'Chào thầy, em gửi bài làm thiết kế schema và file migrations cho hệ thống blog cá nhân ạ.',
                'score' => null, // Chưa chấm
                'feedback' => null,
                'status' => 'submitted',
                'submitted_at' => now()->subDay(),
                'graded_at' => null,
                'created_at' => now()->subDay(),
                'updated_at' => now(),
            ],
            [
                'assignment_id' => 1,
                'user_id' => 5, // Lê Văn Học đã được chấm
                'file_path' => 'submissions/le-van-hoc-blog-migrations.zip',
                'content' => 'Gửi thầy bài làm của em. Em đã tối ưu hóa khóa ngoại và các trường unique slug.',
                'score' => 95,
                'feedback' => 'Bài làm rất tốt, viết migration sạch sẽ, đầy đủ các khóa ngoại cascade. Phát huy tiếp nhé!',
                'status' => 'graded',
                'submitted_at' => now()->subDays(2),
                'graded_at' => now()->subDay(),
                'created_at' => now()->subDays(2),
                'updated_at' => now(),
            ]
        ];

        DB::table('submissions')->insert($submissions);

        // 11. Thảo luận hỏi đáp bài học (Discussions)
        $discussions = [
            [
                'id' => 1,
                'lesson_id' => 2, // Bài 2 khóa Laravel
                'user_id' => 4, // Trần Thị Học
                'title' => 'Không khởi động được Laragon trên Windows 11',
                'content' => 'Chào thầy và các bạn, em mở Laragon lên và bấm Start All thì bị báo lỗi trùng cổng 80 do máy có cài IIS. Có cách nào đổi cổng Apache của Laragon không ạ?',
                'is_resolved' => true,
                'created_at' => now()->subDays(4),
                'updated_at' => now()->subDays(3),
            ]
        ];

        DB::table('discussions')->insert($discussions);

        // 12. Câu trả lời thảo luận (Discussion Replies)
        $discussionReplies = [
            [
                'discussion_id' => 1,
                'user_id' => 2, // Nguyễn Văn Giảng (Giảng viên)
                'content' => 'Chào em, lỗi này thường do dịch vụ IIS của Windows đang chiếm dụng cổng 80. Em có thể vào Laragon -> Menu -> Apache -> httpd.conf, tìm dòng "Listen 80" và sửa thành "Listen 8080". Sau đó Restart lại Laragon là chạy được ở địa chỉ localhost:8080 nhé!',
                'is_instructor_answer' => true,
                'created_at' => now()->subDays(4)->addHour(),
                'updated_at' => now()->subDays(4)->addHour(),
            ],
            [
                'discussion_id' => 1,
                'user_id' => 4, // Trần Thị Học
                'content' => 'Dạ em đổi cổng thành 8080 theo hướng dẫn của thầy và đã chạy thành công rồi ạ! Em cảm ơn thầy nhiều.',
                'is_instructor_answer' => false,
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ]
        ];

        DB::table('discussion_replies')->insert($discussionReplies);

        // 13. Đánh giá nhận xét khóa học (Reviews)
        $reviews = [
            [
                'user_id' => 4, // Trần Thị Học
                'course_id' => 1, // Khóa Laravel
                'rating' => 5,
                'comment' => 'Khóa học cực kỳ chất lượng! Thầy giải thích cặn kẽ và các ví dụ dự án thực tế giúp ích cho đồ án của em rất nhiều.',
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
            [
                'user_id' => 5, // Lê Văn Học
                'course_id' => 1, // Khóa Laravel
                'rating' => 4,
                'comment' => 'Bài giảng rất hay và thực tế, tuy nhiên phần nâng cao thầy nói hơi nhanh một chút. Tổng quan vẫn vô cùng đáng tiền mua.',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ]
        ];

        DB::table('reviews')->insert($reviews);

        // --- BỔ SUNG CÁC BẢNG TRONG 46 MIGRATION CHƯA ĐƯỢC SEED ---

        // 14. Buổi học trực tiếp (Live Sessions)
        $liveSessions = [
            [
                'course_id' => 1, // Khóa Laravel
                'instructor_id' => 2, // Nguyễn Văn Giảng
                'title' => 'Q&A và Giải đáp Đồ án tốt nghiệp phần Backend với Laravel',
                'description' => 'Giải đáp thắc mắc về thiết kế CSDL, bảo mật API JWT/Sanctum và tích hợp cổng thanh toán.',
                'stream_url' => 'https://zoom.us/j/9988776655',
                'scheduled_at' => now()->addDays(5),
                'started_at' => null,
                'ended_at' => null,
                'status' => 'scheduled',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'course_id' => 2, // Khóa React
                'instructor_id' => 2, // Nguyễn Văn Giảng
                'title' => 'Live Coding: Triển khai Global State Management với Redux Toolkit',
                'description' => 'Xây dựng hoàn chỉnh luồng xử lý giỏ hàng trong ứng dụng thương mại điện tử bằng React Redux.',
                'stream_url' => 'https://zoom.us/j/8877665544',
                'scheduled_at' => now()->subDays(2),
                'started_at' => now()->subDays(2)->subHours(2),
                'ended_at' => now()->subDays(2),
                'status' => 'ended',
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(2),
            ]
        ];

        DB::table('live_sessions')->insert($liveSessions);

        // 15. Nhóm học tập (Study Groups)
        $studyGroups = [
            [
                'course_id' => 1, // Khóa Laravel
                'creator_id' => 4, // Trần Thị Học
                'name' => 'Biệt đội chiến binh Laravel - DATN 2026',
                'description' => 'Nhóm cùng học tập, trao đổi tài liệu, hỗ trợ sửa lỗi code và hoàn thiện Đồ án tốt nghiệp bằng Laravel.',
                'max_members' => 30,
                'created_at' => now()->subDays(4),
                'updated_at' => now()->subDays(4),
            ]
        ];

        DB::table('study_groups')->insert($studyGroups);

        // 16. Ghi chú của học viên trong video (Video Notes)
        $videoNotes = [
            [
                'user_id' => 4, // Trần Thị Học
                'lesson_id' => 1, // Bài 1 khóa Laravel
                'timestamp_seconds' => 125,
                'content' => 'Nhớ kỹ: Request Lifecycle bắt đầu từ public/index.php, đi qua HTTP Kernel rồi đến Router.',
                'created_at' => now()->subDays(4),
                'updated_at' => now()->subDays(4),
            ],
            [
                'user_id' => 4, // Trần Thị Học
                'lesson_id' => 2, // Bài 2 khóa Laravel
                'timestamp_seconds' => 450,
                'content' => 'Lệnh tạo controller có sẵn resource: php artisan make:controller Web/HomeController --resource',
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ]
        ];

        DB::table('video_notes')->insert($videoNotes);

        // 17. Tin nhắn trò chuyện với AI Bot (AI Chat Messages)
        $aiChatMessages = [
            [
                'user_id' => 4, // Trần Thị Học
                'lesson_id' => 2, // Bài 2 khóa Laravel
                'role' => 'user',
                'content' => 'Hãy viết giúp tôi lệnh tạo một migration thêm cột `deleted_at` cho bảng `courses`?',
                'created_at' => now()->subHours(2),
                'updated_at' => now()->subHours(2),
            ],
            [
                'user_id' => 4,
                'lesson_id' => 2,
                'role' => 'assistant',
                'content' => 'Bạn có thể sử dụng lệnh Artisan sau trong Terminal:\n`php artisan make:migration add_deleted_at_to_courses_table --table=courses`\n\nSau đó trong file migration mới tạo, ở hàm `up()` viết:\n`$table->softDeletes();` và ở hàm `down()` viết:\n`$table->dropSoftDeletes();` nhé!',
                'created_at' => now()->subHours(2)->addMinute(),
                'updated_at' => now()->subHours(2)->addMinute(),
            ]
        ];

        DB::table('ai_chat_messages')->insert($aiChatMessages);

        // 18. Tin nhắn hỗ trợ kỹ thuật (Support Tickets)
        $supportTickets = [
            [
                'id' => 1,
                'user_id' => 4, // Trần Thị Học
                'subject' => 'Hóa đơn thanh toán khóa học bị lỗi thông tin MST',
                'message' => 'Chào Ban quản trị, em vừa mua khóa học ORD-2026-0001 và muốn yêu cầu xuất hóa đơn đỏ về địa chỉ công ty em, tuy nhiên điền nhầm mã số thuế. Mong admin sửa lại giúp em.',
                'status' => 'in_progress',
                'priority' => 'medium',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDay(),
            ]
        ];

        DB::table('support_tickets')->insert($supportTickets);

        // 19. Chi tiết tin nhắn hỗ trợ kỹ thuật (Support Ticket Messages)
        $supportTicketMessages = [
            [
                'ticket_id' => 1,
                'user_id' => 4, // Trần Thị Học gửi yêu cầu
                'message' => 'Thông tin MST đúng của công ty em là: 0101234567-999.',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'ticket_id' => 1,
                'user_id' => 1, // Admin hỗ trợ phản hồi
                'message' => 'Chào bạn Học, Ban hỗ trợ đã tiếp nhận thông tin. Yêu cầu của bạn đã được chuyển tới bộ phận kế toán để xử lý và sẽ phản hồi hóa đơn qua email trong vòng 24 giờ làm việc nhé!',
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay(),
            ]
        ];

        DB::table('support_ticket_messages')->insert($supportTicketMessages);
    }
}
