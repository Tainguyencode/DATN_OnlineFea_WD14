<?php

namespace Database\Seeders\Demo;

use App\Models\Certificate;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Review;
use Illuminate\Support\Str;

class DemoInteractionSeeder
{
    private array $vietnameseReviews = [
        'Khóa học cực kỳ chất lượng, giảng viên giải thích chi tiết, bài tập bám sát thực tế doanh nghiệp!',
        'Nội dung rất thực chiến, các bài lab và video quay rõ nét, hỗ trợ giải đáp thắc mắc rất nhanh chóng.',
        'Học xong khóa này mình đã tự tin apply và vượt qua vòng phỏng vấn kỹ thuật. Cảm ơn thầy nhiều!',
        'Kiến thức từ cơ bản đến nâng cao rất logic, đặc biệt phần tối ưu hiệu năng và bảo mật rất hay.',
        'Giáo trình bài bản, dễ hiểu, slide và tài liệu đi kèm rất đầy đủ. Rất đáng đồng tiền bát gạo!',
        'Khóa học tuyệt vời cho những ai muốn nâng cao tay nghề lập trình. Đánh giá 5 sao!',
        'Thầy dạy rất có tâm, chia sẻ nhiều kinh nghiệm thực tế khi triển khai dự án quy mô lớn.',
        'Bài tập cuối khóa rất thử thách nhưng làm xong thì vỡ ra được rất nhiều điều về kiến trúc hệ thống.',
    ];

    public function run(array $students, array $courses, ?callable $output = null): void
    {
        $log = $output ?: fn(string $msg) => null;
        $log('--- Bắt đầu nạp Enrollments, Tiến độ học, Orders, Payments, Quizzes, Reviews & Certificates ---');

        $studentCount = count($students);
        $courseCount = count($courses);

        $enrollmentCount = 0;
        $completedEnrollmentCount = 0;
        $orderCount = 0;
        $reviewCount = 0;
        $certificateCount = 0;

        foreach ($students as $sIdx => $student) {
            // Mỗi sinh viên đăng ký ngẫu nhiên 2 - 4 khóa học
            $numCourses = 2 + ($sIdx % 3);
            $enrolledCourses = [];

            for ($k = 0; $k < $numCourses; $k++) {
                $cIndex = ($sIdx * 3 + $k * 7) % $courseCount;
                $course = $courses[$cIndex];

                if (in_array($course->id, $enrolledCourses, true)) {
                    continue;
                }
                $enrolledCourses[] = $course->id;

                // Tỷ lệ tiến độ: 0%, 25%, 50%, 75%, 100%
                $progressTiers = [0, 25, 50, 75, 100];
                $progressPercent = $progressTiers[($sIdx + $k) % count($progressTiers)];
                $isCompleted = ($progressPercent === 100);

                $lessons = Lesson::where('course_id', $course->id)->orderBy('sort_order')->get();
                $totalLessons = $lessons->count() ?: 12;
                $completedLessonCount = (int) round(($progressPercent / 100) * $totalLessons);

                $enrollment = Enrollment::updateOrCreate(
                    [
                        'user_id' => $student->id,
                        'course_id' => $course->id,
                    ],
                    [
                        'status' => 'active',
                        'progress_percent' => $progressPercent,
                        'completed_lessons' => $completedLessonCount,
                        'total_lessons' => $totalLessons,
                        'enrolled_at' => now()->subDays(30 + ($sIdx % 30)),
                        'completed_at' => $isCompleted ? now()->subDays(5 + ($sIdx % 10)) : null,
                        'created_at' => now()->subDays(30 + ($sIdx % 30)),
                        'updated_at' => now(),
                    ]
                );
                $enrollmentCount++;

                // Tạo Tiến độ từng bài học (Lesson Progress)
                foreach ($lessons as $lIdx => $lesson) {
                    $lessonDone = ($lIdx < $completedLessonCount);
                    LessonProgress::updateOrCreate(
                        [
                            'user_id' => $student->id,
                            'lesson_id' => $lesson->id,
                        ],
                        [
                            'watched_seconds' => $lessonDone ? ($lesson->duration_seconds ?: 600) : ($lessonDone ? 300 : 0),
                            'current_time' => $lessonDone ? ($lesson->duration_seconds ?: 600) : 0,
                            'duration' => $lesson->duration_seconds ?: 600,
                            'progress_percent' => $lessonDone ? 100.00 : 0.00,
                            'last_watched_at' => $lessonDone ? now()->subDays(rand(1, 20)) : null,
                            'is_completed' => $lessonDone,
                            'completed_at' => $lessonDone ? now()->subDays(rand(1, 20)) : null,
                        ]
                    );
                }

                // Nếu hoàn thành 100% -> Cấp Chứng chỉ hợp lệ (Certificate)
                if ($isCompleted) {
                    $certCode = sprintf('CERT-DEMO-%04d-%s', $enrollment->id, strtoupper(Str::random(6)));
                    Certificate::updateOrCreate(
                        [
                            'user_id' => $student->id,
                            'course_id' => $course->id,
                        ],
                        [
                            'certificate_code' => $certCode,
                            'file_path' => 'certificates/' . $certCode . '.pdf',
                            'issued_at' => $enrollment->completed_at ?? now(),
                        ]
                    );
                    $certificateCount++;
                    $completedEnrollmentCount++;

                    // Tạo Quiz Attempt đạt điểm tuyệt đối cho học viên đã tốt nghiệp
                    $quizLessonIds = Lesson::where('course_id', $course->id)->where('type', Lesson::TYPE_QUIZ)->pluck('id');
                    $quiz = Quiz::whereIn('lesson_id', $quizLessonIds)->first();
                    if ($quiz) {
                        QuizAttempt::updateOrCreate(
                            [
                                'user_id' => $student->id,
                                'quiz_id' => $quiz->id,
                            ],
                            [
                                'score' => 100,
                                'passed' => true,
                                'answers' => json_encode(['demo' => 'all_correct']),
                                'started_at' => now()->subDays(7),
                                'completed_at' => now()->subDays(7)->addMinutes(15),
                            ]
                        );
                    }
                }

                // Nếu khóa học có phí -> Tạo Order & Payment Record tương ứng
                if ($course->price > 0 && ($sIdx % 2 === 0)) {
                    $orderCode = sprintf('ORD-DEMO-%05d-%s', $sIdx * 100 + $k, strtoupper(Str::random(4)));
                    $price = $course->sale_price ?: $course->price;
                    $commissionRate = 80.00;
                    $instructorEarning = round($price * 0.80);
                    $commissionAmount = round($price * 0.20);

                    $order = Order::updateOrCreate(
                        ['order_code' => $orderCode],
                        [
                            'user_id' => $student->id,
                            'subtotal' => $price,
                            'discount_amount' => 0.00,
                            'total_amount' => $price,
                            'status' => 'paid',
                            'payment_method' => 'momo',
                            'created_at' => $enrollment->enrolled_at,
                            'updated_at' => $enrollment->enrolled_at,
                        ]
                    );

                    OrderItem::updateOrCreate(
                        ['order_id' => $order->id, 'course_id' => $course->id],
                        [
                            'price' => $price,
                            'commission_rate' => $commissionRate,
                            'commission_amount' => $commissionAmount,
                            'instructor_earning' => $instructorEarning,
                        ]
                    );

                    Payment::updateOrCreate(
                        ['order_id' => $order->id],
                        [
                            'gateway' => 'momo',
                            'gateway_order_code' => 'PAY-' . Str::random(12),
                            'transaction_id' => 'TXN-' . Str::random(16),
                            'amount' => $price,
                            'status' => 'success',
                            'gateway_response' => ['status' => 'PAID', 'code' => '00', 'message' => 'Success'],
                            'paid_at' => $enrollment->enrolled_at,
                        ]
                    );
                    $orderCount++;
                }

                // Tạo Review (Chỉ dành cho học viên đã ghi danh, rating 4 hoặc 5 sao)
                if ($progressPercent >= 50 && ($sIdx % 3 === 0)) {
                    Review::updateOrCreate(
                        [
                            'user_id' => $student->id,
                            'course_id' => $course->id,
                        ],
                        [
                            'rating' => ($sIdx % 5 === 0) ? 4 : 5,
                            'comment' => $this->vietnameseReviews[($sIdx + $k) % count($this->vietnameseReviews)],
                            'created_at' => now()->subDays(rand(1, 15)),
                            'updated_at' => now(),
                        ]
                    );
                    $reviewCount++;
                }
            }
        }

        $log("✓ Đã tạo {$enrollmentCount} Lượt ghi danh (Enrollments) với tiến độ từ 0% đến 100%");
        $log("✓ Đã cấp {$certificateCount} Chứng chỉ tốt nghiệp chuẩn cho các lượt học 100%");
        $log("✓ Đã tạo {$orderCount} Đơn hàng & Giao dịch thanh toán PayOS/SePay hợp lệ");
        $log("✓ Đã tạo {$reviewCount} Đánh giá thực tế từ chính học viên đã tham gia học");
    }
}
