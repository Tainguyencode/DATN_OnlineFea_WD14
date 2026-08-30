<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonComment;
use App\Models\Review;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentReviewSeeder extends Seeder
{
    private array $reviews5Stars = [
        'Khóa học cực kỳ chất lượng, giảng viên giải thích cặn kẽ và đi sâu vào bản chất vấn đề.',
        'Nội dung thực chiến, áp dụng được ngay vào công việc thực tế tại công ty. 10/10!',
        'Rất thích cách truyền đạt của thầy, dễ hiểu, logic và có nhiều ví dụ minh họa trực quan.',
        'Giáo trình bài bản từ cơ bản đến nâng cao. Nhờ khóa này mà mình đã pass phỏng vấn thành công!',
        'Chất lượng video và âm thanh sắc nét, tài liệu đính kèm đầy đủ source code và slide.',
        'Khóa học rất hay! Giảng viên hỗ trợ giải đáp thắc mắc trong phần thảo luận cực nhanh và tận tâm.',
        'Tuyệt vời! Kiến thức cập nhật mới nhất theo phiên bản hiện tại, không bị lỗi thời.',
        'Đáng đồng tiền bát gạo. Khuyên các bạn mới bắt đầu nên học khóa này để có nền tảng vững chắc.',
        'Bài tập thực hành có độ khó tăng dần, giúp rèn luyện tư duy lập trình và giải quyết bài toán thực tế.',
        'Khóa học xuất sắc, mong trung tâm ra thêm nhiều khóa chuyên sâu nâng cao như thế này nữa.',
    ];

    private array $reviews4Stars = [
        'Nội dung rất hay và bổ ích, tuy nhiên một số bài tập cuối chương hơi nâng cao so với người mới bắt đầu.',
        'Khóa học cung cấp nền tảng vững chắc, mong giảng viên bổ sung thêm bài giảng về tối ưu performance chuyên sâu.',
        'Giảng viên dạy nhiệt tình, kiến thức thực tế. Hy vọng có thêm phiên bản cập nhật cho framework mới nhất.',
        'Khóa học tốt, học được nhiều tư duy lập trình mới. Video một số đoạn nói hơi nhanh một chút.',
        'Chất lượng khóa học rất ổn trong tầm giá, nếu có thêm nhiều dự án mẫu phong phú hơn thì tuyệt vời.',
        'Hệ thống bài tập khá hay, giảng viên hỗ trợ nhanh. Cần bổ sung thêm phần tóm tắt lý thuyết sau mỗi chương.',
    ];

    private array $reviews3Stars = [
        'Nội dung ở mức cơ bản, phù hợp cho người mới bắt đầu hơn là người đã có kinh nghiệm đi làm.',
        'Kiến thức lý thuyết ổn nhưng bài tập thực hành hơi ít, cần bổ sung thêm các case study thực tế.',
        'Giảng viên nói hơi nhỏ ở một vài chương đầu, từ chương 3 trở đi thì âm thanh ổn định hơn.',
        'Khóa học tạm ổn, tuy nhiên tài liệu đính kèm cần được cập nhật link tải mới do một số link bị lỗi.',
        'Khóa học hữu ích nhưng tốc độ giảng dạy hơi chậm, mình phải tua x1.5 tốc độ để nghe phù hợp.',
    ];

    private array $reviews2Stars = [
        'Tiến độ bài giảng đi hơi nhanh, phần giải thích thuật toán chưa thực sự chi tiết đối với người chuyển ngành.',
        'Slide bài giảng còn lỗi chính tả, source code mẫu một số chỗ chưa chạy được ngay cần phải sửa lại.',
        'Phần hỏi đáp phản hồi hơi chậm, mong đội ngũ trợ giảng hỗ trợ học viên tích cực và chi tiết hơn.',
        'Nội dung chưa thực sự đào sâu như tiêu đề khóa học giới thiệu, chỉ dừng lại ở mức giới thiệu khái niệm.',
    ];

    private array $reviews1Star = [
        'Nội dung không đúng như kỳ vọng ban đầu, quá nhiều lý thuyết suông và thiếu bài tập dự án thực chiến.',
        'Âm thanh video bài giảng bị rè và lẫn tạp âm nhiều ở một số bài, rất khó tập trung theo dõi bài học.',
        'Kiến thức đã cũ so với phiên bản hiện tại, code mẫu báo lỗi nhiều khi chạy trên môi trường mới.',
    ];

    private array $instructorReplies = [
        'Cảm ơn bạn đã phản hồi chân thành! Giảng viên đã ghi nhận và vừa cập nhật lại slide cũng như source code chuẩn ở phần tài liệu đính kèm nhé.',
        'Chào bạn, cảm ơn đóng góp quý báu. Đội ngũ kỹ thuật đã xử lý và lọc lại âm thanh để các video đạt chất lượng tốt nhất rồi bạn nhé.',
        'Chào bạn, nếu bạn gặp khó khăn ở phần bài tập nào, bạn có thể đặt câu hỏi trong tab Thảo luận để giảng viên và trợ giảng hỗ trợ 1:1 nhé.',
        'Cảm ơn phản hồi của bạn! Trung tâm sẽ bổ sung thêm các bài tập case study nâng cao trong đợt cập nhật giáo trình sắp tới.',
    ];

    private array $lessonComments = [
        'Bài giảng này hay quá thầy ơi, phần giải thích kiến thức rất dễ hiểu!',
        'Đoạn phút 05:30 áp dụng giải thuật này tối ưu thời gian chạy hơn hẳn cách truyền thống.',
        'Mình đã thực hành theo và chạy thành công trên máy, cảm ơn thầy nhiều ạ!',
        'Code mẫu rất sạch và tuân thủ chuẩn clean code, học hỏi được rất nhiều tư duy.',
        'Thầy cho em hỏi thêm về cách xử lý exception trong trường hợp bất đồng bộ này với ạ?',
        'Xem xong bài này mới vỡ lẽ ra nhiều điều trước đây tự học chưa hiểu sâu.',
        'Bài tập cuối bài khá thử thách nhưng làm xong cảm thấy rất tự tin.',
        'Kiến thức bài này áp dụng trực tiếp vào dự án thực tế mình đang làm, cảm ơn thầy!',
    ];

    public function run(): void
    {
        echo "\n=========================================================================\n";
        echo "   BẮT ĐẦU NẠP ĐÁNH GIÁ ĐỦ TỪ 1 ĐẾN 5 SAO & BÌNH LUẬN BÀI HỌC (2025-2026)\n";
        echo "=========================================================================\n\n";

        $courses = Course::where('status', 'published')->get(['id', 'instructor_id', 'title']);
        if ($courses->isEmpty()) {
            $courses = Course::all(['id', 'instructor_id', 'title']);
        }
        $lessons = Lesson::all(['id', 'course_id', 'title']);

        $studentIds = User::where('role', 'student')->limit(3000)->pluck('id')->all();
        if (empty($studentIds)) {
            $studentIds = User::limit(500)->pluck('id')->all();
        }
        $totalStudents = count($studentIds);

        // Timeline 20 tháng
        $monthTimeline = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthTimeline[] = ['year' => 2025, 'month' => $m];
        }
        for ($m = 1; $m <= 8; $m++) {
            $monthTimeline[] = ['year' => 2026, 'month' => $m];
        }
        $totalMonths = count($monthTimeline);

        $reviewsBatch = [];
        $starStats = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        $reviewCounter = 0;

        foreach ($courses as $cIdx => $course) {
            $instructorId = $course->instructor_id ?? 1;
            // Mỗi khóa học có từ 25 đến 40 đánh giá
            $numReviews = rand(25, 40);

            for ($r = 1; $r <= $numReviews; $r++) {
                $reviewCounter++;
                $mSlot = $monthTimeline[($reviewCounter * 3) % $totalMonths];
                $year = $mSlot['year'];
                $month = $mSlot['month'];
                $day = rand(1, 28);
                $hour = rand(8, 22);
                $minute = rand(0, 59);
                $second = rand(0, 59);

                $createdAt = sprintf('%04d-%02d-%02d %02d:%02d:%02d', $year, $month, $day, $hour, $minute, $second);
                $createdCarbon = Carbon::create($year, $month, $day, $hour, $minute, $second);
                $userId = $studentIds[($reviewCounter * 7 + $r) % $totalStudents];

                // Phân bổ tỷ lệ các mức sao:
                // 5 Sao: ~58%
                // 4 Sao: ~24%
                // 3 Sao: ~10%
                // 2 Sao: ~5%
                // 1 Sao: ~3%
                $randPercent = rand(1, 100);
                if ($randPercent <= 58) {
                    $rating = 5;
                    $comment = $this->reviews5Stars[($reviewCounter + $r) % count($this->reviews5Stars)];
                    $instructorReply = null;
                    $repliedAt = null;
                    $repliedBy = null;
                } elseif ($randPercent <= 82) {
                    $rating = 4;
                    $comment = $this->reviews4Stars[($reviewCounter + $r) % count($this->reviews4Stars)];
                    $instructorReply = null;
                    $repliedAt = null;
                    $repliedBy = null;
                } elseif ($randPercent <= 92) {
                    $rating = 3;
                    $comment = $this->reviews3Stars[($reviewCounter + $r) % count($this->reviews3Stars)];
                    // Có phản hồi từ giảng viên cho 50% đánh giá 3 sao
                    if ($r % 2 === 0) {
                        $instructorReply = $this->instructorReplies[($reviewCounter + $r) % count($this->instructorReplies)];
                        $repliedAt = $createdCarbon->copy()->addHours(rand(2, 24))->format('Y-m-d H:i:s');
                        $repliedBy = $instructorId;
                    } else {
                        $instructorReply = null;
                        $repliedAt = null;
                        $repliedBy = null;
                    }
                } elseif ($randPercent <= 97) {
                    $rating = 2;
                    $comment = $this->reviews2Stars[($reviewCounter + $r) % count($this->reviews2Stars)];
                    // Có phản hồi từ giảng viên
                    $instructorReply = $this->instructorReplies[($reviewCounter + $r) % count($this->instructorReplies)];
                    $repliedAt = $createdCarbon->copy()->addHours(rand(1, 12))->format('Y-m-d H:i:s');
                    $repliedBy = $instructorId;
                } else {
                    $rating = 1;
                    $comment = $this->reviews1Star[($reviewCounter + $r) % count($this->reviews1Star)];
                    // Có phản hồi từ giảng viên
                    $instructorReply = $this->instructorReplies[($reviewCounter + $r) % count($this->instructorReplies)];
                    $repliedAt = $createdCarbon->copy()->addHours(rand(1, 8))->format('Y-m-d H:i:s');
                    $repliedBy = $instructorId;
                }

                $starStats[$rating]++;
                $helpfulCount = ($rating >= 4) ? rand(3, 45) : rand(0, 8);

                $reviewsBatch[] = [
                    'user_id' => $userId,
                    'course_id' => $course->id,
                    'rating' => $rating,
                    'comment' => $comment,
                    'status' => 'visible',
                    'helpful_count' => $helpfulCount,
                    'instructor_reply' => $instructorReply,
                    'replied_by' => $repliedBy,
                    'replied_at' => $repliedAt,
                    'verified_purchase' => true,
                    'is_hidden' => false,
                    'created_at' => $createdAt,
                    'updated_at' => $repliedAt ?? $createdAt,
                ];
            }
        }

        // Chèn reviews vào Database
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('reviews')->truncate();
        foreach (array_chunk($reviewsBatch, 500) as $chunk) {
            DB::table('reviews')->insert($chunk);
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Nạp bình luận bài học (Lesson Comments)
        $commentsBatch = [];
        $commentCount = 0;
        foreach ($lessons->take(200) as $lIdx => $lesson) {
            $numComments = rand(2, 6);
            for ($c = 0; $c < $numComments; $c++) {
                $mSlot = $monthTimeline[($commentCount * 2) % $totalMonths];
                $year = $mSlot['year'];
                $month = $mSlot['month'];
                $day = rand(1, 28);
                $hour = rand(8, 22);
                $minute = rand(0, 59);

                $createdAt = sprintf('%04d-%02d-%02d %02d:%02d:%02d', $year, $month, $day, $hour, $minute, 0);
                $userId = $studentIds[($commentCount * 5 + $c) % $totalStudents];
                $content = $this->lessonComments[($commentCount + $c) % count($this->lessonComments)];

                $commentsBatch[] = [
                    'lesson_id' => $lesson->id,
                    'user_id' => $userId,
                    'parent_id' => null,
                    'content' => $content,
                    'is_hidden' => false,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];

                $commentCount++;
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('lesson_comments')->truncate();
        foreach (array_chunk($commentsBatch, 500) as $chunk) {
            DB::table('lesson_comments')->insert($chunk);
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $totalReviews = count($reviewsBatch);
        $avgScore = round(Review::avg('rating'), 2);

        echo "=========================================================================\n";
        echo "✓ HOÀN TẤT NẠP ĐÁNH GIÁ KHÓA HỌC VÀ BÌNH LUẬN BÀI HỌC!\n";
        echo "   • Tổng số Đánh giá (Reviews): " . number_format($totalReviews) . " đánh giá\n";
        echo "   • Điểm đánh giá trung bình:   " . $avgScore . " / 5.0 ⭐\n";
        echo "   • Phân bổ theo mức sao:\n";
        echo "      ★★★★★ 5 Sao: " . number_format($starStats[5]) . " (" . round(($starStats[5]/$totalReviews)*100, 1) . "%)\n";
        echo "      ★★★★☆ 4 Sao: " . number_format($starStats[4]) . " (" . round(($starStats[4]/$totalReviews)*100, 1) . "%)\n";
        echo "      ★★★☆☆ 3 Sao: " . number_format($starStats[3]) . " (" . round(($starStats[3]/$totalReviews)*100, 1) . "%)\n";
        echo "      ★★☆☆☆ 2 Sao: " . number_format($starStats[2]) . " (" . round(($starStats[2]/$totalReviews)*100, 1) . "%)\n";
        echo "      ★☆☆☆☆ 1 Sao: " . number_format($starStats[1]) . " (" . round(($starStats[1]/$totalReviews)*100, 1) . "%)\n";
        echo "   • Tổng số Bình luận bài học:  " . number_format($commentCount) . " bình luận\n";
        echo "=========================================================================\n\n";
    }
}
