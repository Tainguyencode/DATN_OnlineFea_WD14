<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LearningPathSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Tạo lộ trình học tập nâng cao (Enhanced Learning Paths)
        $paths = [
            [
                'title' => 'Lộ trình trở thành Web Fullstack Developer',
                'slug' => Str::slug('Lộ trình trở thành Web Fullstack Developer'),
                'description' => 'Chương trình đào tạo toàn diện từ A-Z giúp bạn làm chủ cả Frontend và Backend, xây dựng ứng dụng Web thực tế chuẩn doanh nghiệp và tự tin ứng tuyển vị trí Fullstack Developer.',
                'thumbnail' => null,
                'level' => 'intermediate',
                'target_role' => 'Fullstack Web Developer',
                'salary_range' => '15 - 35 triệu/tháng',
                'estimated_duration' => '6 - 8 tháng (180h học)',
                'skills' => json_encode(['HTML5/CSS3', 'JavaScript ES6+', 'Vue.js/React', 'Laravel Framework', 'RESTful API', 'MySQL Database', 'Docker & CI/CD', 'Git']),
                'is_featured' => true,
            ],
            [
                'title' => 'Lộ trình chuyên sâu UI/UX & Thiết kế sản phẩm',
                'slug' => Str::slug('Lộ trình chuyên sâu UI/UX Thiết kế sản phẩm'),
                'description' => 'Trang bị tư duy thiết kế lấy người dùng làm trung tâm (User-Centered Design), nghiên cứu trải nghiệm người dùng, thiết kế giao diện Figma chuyên nghiệp và xây dựng Design System.',
                'thumbnail' => null,
                'level' => 'beginner',
                'target_role' => 'UI/UX Designer / Product Designer',
                'salary_range' => '12 - 28 triệu/tháng',
                'estimated_duration' => '4 - 5 tháng (120h học)',
                'skills' => json_encode(['Figma Core & Prototyping', 'User Research & Wireframing', 'Design System & Component', 'Usability Testing', 'Design Thinking']),
                'is_featured' => true,
            ],
            [
                'title' => 'Lộ trình Lập trình Chuyên sâu & Kiến trúc Phần mềm',
                'slug' => Str::slug('Lộ trình Lập trình Chuyên sâu Kiến trúc Phần mềm'),
                'description' => 'Dành cho các lập trình viên đã có nền tảng muốn nâng cao trình độ lên Senior / Lead Developer. Làm chủ Kiến trúc Microservices, Clean Architecture, Tối ưu hóa hiệu năng & Bảo mật nâng cao.',
                'thumbnail' => null,
                'level' => 'advanced',
                'target_role' => 'Software Architect / Senior Backend Engineer',
                'salary_range' => '30 - 60 triệu/tháng',
                'estimated_duration' => '8 - 10 tháng (240h học)',
                'skills' => json_encode(['Clean Code & Design Patterns', 'Microservices Architecture', 'High Performance Database', 'Redis Caching', 'System Security & Scalability']),
                'is_featured' => false,
            ],
        ];

        foreach ($paths as $pathData) {
            DB::table('learning_paths')->updateOrInsert(
                ['slug' => $pathData['slug']],
                array_merge($pathData, ['updated_at' => now(), 'created_at' => now()])
            );
        }

        // 2. Liên kết các khóa học với từng giai đoạn (Stages)
        $courseIds = DB::table('courses')->orderBy('id')->pluck('id')->values();
        if ($courseIds->isNotEmpty()) {
            $fullstackPathId = DB::table('learning_paths')->where('slug', $paths[0]['slug'])->value('id');
            $designPathId = DB::table('learning_paths')->where('slug', $paths[1]['slug'])->value('id');
            $advancedPathId = DB::table('learning_paths')->where('slug', $paths[2]['slug'])->value('id');

            // Gán giai đoạn cho Fullstack path
            $fullstackStages = [
                'Giai đoạn 1: Nền tảng & Cấu trúc Web',
                'Giai đoạn 2: Lập trình Chuyên sâu & Backend API',
                'Giai đoạn 3: Đồ án Thực chiến & Triển khai',
            ];
            foreach ($courseIds->take(3) as $sortOrder => $courseId) {
                $stageName = $fullstackStages[$sortOrder] ?? 'Giai đoạn ' . ($sortOrder + 1);
                DB::table('learning_path_courses')->updateOrInsert(
                    ['learning_path_id' => $fullstackPathId, 'course_id' => $courseId],
                    ['sort_order' => $sortOrder + 1, 'stage_name' => $stageName]
                );
            }

            // Gán giai đoạn cho UI/UX path
            $uiuxStages = [
                'Giai đoạn 1: Nền tảng Nghiên cứu & UX Design',
                'Giai đoạn 2: Thiết kế Giao diện UI & Prototype Figma',
            ];
            if ($courseIds->count() >= 2) {
                foreach ($courseIds->slice(1, 2)->values() as $sortOrder => $courseId) {
                    $stageName = $uiuxStages[$sortOrder] ?? 'Giai đoạn ' . ($sortOrder + 1);
                    DB::table('learning_path_courses')->updateOrInsert(
                        ['learning_path_id' => $designPathId, 'course_id' => $courseId],
                        ['sort_order' => $sortOrder + 1, 'stage_name' => $stageName]
                    );
                }
            }

            // Gán giai đoạn cho Advanced path
            foreach ($courseIds as $sortOrder => $courseId) {
                $stageIndex = min((int) floor($sortOrder / 2) + 1, 3);
                $stageName = match ($stageIndex) {
                    1 => 'Giai đoạn 1: Kiến trúc Mã nguồn & Pattern',
                    2 => 'Giai đoạn 2: Hệ thống Microservices & Tối ưu Database',
                    default => 'Giai đoạn 3: Triển khai Quy mô lớn & Bảo mật'
                };
                DB::table('learning_path_courses')->updateOrInsert(
                    ['learning_path_id' => $advancedPathId, 'course_id' => $courseId],
                    ['sort_order' => $sortOrder + 1, 'stage_name' => $stageName]
                );
            }
        }
    }
}
