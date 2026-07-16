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
        // 1. Tạo lộ trình học tập (Learning Paths)
        $paths = [
            [
                'id' => 1,
                'title' => 'Lộ trình trở thành Web Fullstack Developer',
                'slug' => Str::slug('Lộ trình trở thành Web Fullstack Developer'),
                'description' => 'Học tập theo trình tự được thiết kế sẵn để làm chủ cả Frontend và Backend, sẵn sàng ứng tuyển vị trí Fullstack Web Developer.',
                'thumbnail' => 'path_fullstack.png',
                'level' => 'intermediate',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'title' => 'Lộ trình chuyên sâu UI/UX & Thiết kế sản phẩm',
                'slug' => Str::slug('Lộ trình chuyên sâu UI/UX Thiết kế sản phẩm'),
                'description' => 'Trang bị nền tảng mỹ thuật, tư duy thiết kế lấy người dùng làm trung tâm và kỹ năng xây dựng Prototype chuyên nghiệp.',
                'thumbnail' => 'path_uiux.png',
                'level' => 'beginner',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('learning_paths')->insert($paths);

        // 2. Liên kết các khóa học với Lộ trình (Learning Path Courses pivot)
        $pathCourses = [
            // Lộ trình Fullstack (Path 1) gồm Khóa Laravel (ID: 1) và Khóa React (ID: 2)
            [
                'learning_path_id' => 1,
                'course_id' => 1,
                'sort_order' => 1,
            ],
            [
                'learning_path_id' => 1,
                'course_id' => 2,
                'sort_order' => 2,
            ],
            // Lộ trình UI/UX (Path 2) gồm Khóa UI/UX Fundamentals (ID: 3)
            [
                'learning_path_id' => 2,
                'course_id' => 3,
                'sort_order' => 1,
            ],
        ];

        DB::table('learning_path_courses')->insert($pathCourses);
    }
}
