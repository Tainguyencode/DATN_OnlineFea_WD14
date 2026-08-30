<?php

namespace Database\Seeders\Demo;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DemoDataMasterSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            throw new RuntimeException('Tuyệt đối không được nạp dữ liệu demo trên môi trường Production!');
        }

        echo "\n=======================================================\n";
        echo "   BẮT ĐẦU NẠP BỘ DỮ LIỆU DEMO/TEST LỚN ONLINEFEA\n";
        echo "=======================================================\n\n";

        $output = fn(string $msg) => print($msg . "\n");

        DB::beginTransaction();
        try {
            // 1. Nạp Users (300+ Sinh viên, 30+ Giảng viên, 3 Admins, 1 Super Admin)
            $userSeeder = new DemoUserSeeder();
            $users = $userSeeder->run($output);

            // 2. Nạp Courses (65+ Khóa học phủ kín danh mục active)
            $courseSeeder = new DemoCourseSeeder();
            $courses = $courseSeeder->run($users['instructors'], $output);

            // 3. Sinh 65 Video MP4 thật bằng FFmpeg và gắn vào các bài học Video
            $videoGenerator = new DemoVideoGenerator();
            $videos = $videoGenerator->generateDemoVideos(65, $output);

            // Gắn video MP4 nguồn vào các bài học video
            $videoLessons = Lesson::where('type', Lesson::TYPE_VIDEO)->orderBy('id')->get();
            $videoCount = count($videos);
            if ($videoCount > 0) {
                foreach ($videoLessons as $vIdx => $vLesson) {
                    $vData = $videos[$vIdx % $videoCount];
                    $videoGenerator->attachVideoToLesson($vLesson, $vData, 'pending');
                }
                $output("✓ Đã gắn " . count($videoLessons) . " bài học Video với file MP4 nguồn thực tế!");
            }

            // 4. Nạp Learning Paths
            $pathSeeder = new DemoLearningPathSeeder();
            $paths = $pathSeeder->run($users['super_admin'], $output);

            // 5. Nạp Tương tác (Enrollments, Progress, Orders, Payments, Quizzes, Reviews, Certificates)
            $interactionSeeder = new DemoInteractionSeeder();
            $interactionSeeder->run($users['students'], $courses, $output);

            DB::commit();

            echo "\n=======================================================\n";
            echo "   ✓ NẠP THÀNH CÔNG BỘ DỮ LIỆU DEMO TOÀN DIỆN 100%!\n";
            echo "=======================================================\n\n";
        } catch (\Throwable $e) {
            DB::rollBack();
            echo "\n❌ LỖI TRONG QUÁ TRÌNH NẠP DEMO: " . $e->getMessage() . "\n";
            echo $e->getTraceAsString() . "\n";
            throw $e;
        }
    }
}
