<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Lấy ID giảng viên theo email
        $instWeb = User::where('email', 'instructor@example.com')->value('id') ?? 2;
        $instDesign = User::where('email', 'instructor2@example.com')->value('id') ?? 3;
        $instData = User::where('email', 'tien.datascience@example.com')->value('id') ?? 4;
        $instMarketing = User::where('email', 'long.marketing@example.com')->value('id') ?? 5;
        $instIelts = User::where('email', 'tu.ielts@example.com')->value('id') ?? 6;
        $instMobile = User::where('email', 'minh.reactnative@example.com')->value('id')
            ?? User::where('email', 'minh.mobile@example.com')->value('id')
            ?? 7;

        // 2. Lấy ID danh mục
        $catWeb = $this->categoryId('Phát triển Web');
        $catMobile = $this->categoryId('Phát triển ứng dụng Mobile');
        $catDesign = $this->categoryId('Thiết kế UI/UX');
        $catData = $this->categoryId('Khoa học dữ liệu');
        $catMarketing = $this->categoryId('Digital Marketing');
        $catLanguage = $this->categoryId('Kỹ năng giao tiếp');

        // 3. Danh sách khóa học
        $courses = [
            [
                'id' => 1,
                'instructor_id' => $instWeb,
                'category_id' => $catWeb,
                'title' => 'Laravel từ Zero đến Hero',
                'slug' => Str::slug('Laravel từ Zero đến Hero'),
                'description' => 'Khóa học toàn diện về framework Laravel từ cơ bản đến nâng cao. Học qua xây dựng hệ thống thực tế chuẩn doanh nghiệp.',
                'objectives' => 'Nắm vững MVC, Eloquent ORM, Routing, Middleware, Authentication, RESTful API và Deploy hệ thống.',
                'thumbnail' => 'course-thumbnails/laravel_zero_hero.png',
                'preview_video' => 'https://www.youtube.com/watch?v=ImtZ5yENzgE',
                'level' => 'beginner',
                'price' => 499000.00,
                'sale_price' => 299000.00,
                'status' => 'published',
                'is_published' => true,
                'rating_avg' => 4.85,
                'rating_count' => 150,
                'enrollment_count' => 1250,
                'duration_minutes' => 720,
                'tags' => json_encode(['php', 'laravel', 'backend', 'web']),
                'is_featured' => true,
                'published_at' => now()->subMonths(2),
                'created_at' => now()->subMonths(2),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'instructor_id' => $instWeb,
                'category_id' => $catWeb,
                'title' => 'React.js Masterclass',
                'slug' => Str::slug('React.js Masterclass'),
                'description' => 'Làm chủ React.js và các công cụ hiện đại như Redux Toolkit, React Router v6, Vite, TailwindCSS.',
                'objectives' => 'Hiểu sâu Virtual DOM, Hooks (useState, useEffect, useMemo), State Management và Single Page App.',
                'thumbnail' => 'course-thumbnails/react_masterclass.png',
                'preview_video' => 'https://www.youtube.com/watch?v=w7ejDZ8SWv8',
                'level' => 'intermediate',
                'price' => 599000.00,
                'sale_price' => 399000.00,
                'status' => 'published',
                'is_published' => true,
                'rating_avg' => 4.75,
                'rating_count' => 98,
                'enrollment_count' => 840,
                'duration_minutes' => 600,
                'tags' => json_encode(['javascript', 'react', 'frontend', 'spa']),
                'is_featured' => true,
                'published_at' => now()->subMonths(1),
                'created_at' => now()->subMonths(1),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'instructor_id' => $instDesign,
                'category_id' => $catDesign,
                'title' => 'Figma Prototype & Design System',
                'slug' => Str::slug('Figma Prototype Design System'),
                'description' => 'Khóa học thiết kế giao diện UI/UX chuyên nghiệp với Figma. Xây dựng Design System chuẩn chỉnh.',
                'objectives' => 'Thiết kế Wireframe, Interactive Prototype, Auto Layout, Variants và Hand-off cho Lập trình viên.',
                'thumbnail' => 'course-thumbnails/figma_prototype.png',
                'preview_video' => 'https://www.youtube.com/watch?v=jk1T0CeYwV4',
                'level' => 'beginner',
                'price' => 450000.00,
                'sale_price' => 299000.00,
                'status' => 'published',
                'is_published' => true,
                'rating_avg' => 4.90,
                'rating_count' => 76,
                'enrollment_count' => 510,
                'duration_minutes' => 480,
                'tags' => json_encode(['figma', 'uiux', 'design', 'prototype']),
                'is_featured' => true,
                'published_at' => now()->subWeeks(3),
                'created_at' => now()->subWeeks(3),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'instructor_id' => $instMobile,
                'category_id' => $catMobile,
                'title' => 'Lập trình di động Flutter & Dart từ cơ bản',
                'slug' => Str::slug('Lập trình di động Flutter Dart từ cơ bản'),
                'description' => 'Khóa học lập trình ứng dụng di động đa nền tảng iOS & Android với Flutter và ngôn ngữ Dart.',
                'objectives' => 'Nắm vững Dart, Flutter Widgets, State Management (Bloc/Provider) và kết nối REST API.',
                'thumbnail' => 'course-thumbnails/flutter_beginner.png',
                'preview_video' => 'https://www.youtube.com/watch?v=VPvVD8t02U8',
                'level' => 'beginner',
                'price' => 699000.00,
                'sale_price' => 499000.00,
                'status' => 'published',
                'is_published' => true,
                'rating_avg' => 4.88,
                'rating_count' => 64,
                'enrollment_count' => 320,
                'duration_minutes' => 540,
                'tags' => json_encode(['flutter', 'dart', 'mobile', 'ios', 'android']),
                'is_featured' => false,
                'published_at' => now()->subWeeks(2),
                'created_at' => now()->subWeeks(3),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'instructor_id' => $instData,
                'category_id' => $catData,
                'title' => 'Python cho Phân tích dữ liệu & Machine Learning',
                'slug' => Str::slug('Python cho Phân tích dữ liệu Machine Learning'),
                'description' => 'Trở thành Data Analyst chuyên nghiệp với Python, Pandas, NumPy, SQL và thuật toán Machine Learning.',
                'objectives' => 'Xử lý và làm sạch dữ liệu, trực quan hóa dữ liệu với Matplotlib/Seaborn, xây dựng mô hình dự báo.',
                'thumbnail' => 'course-thumbnails/python_datascience.png',
                'preview_video' => 'https://www.youtube.com/watch?v=r-uOLxNrNk8',
                'level' => 'intermediate',
                'price' => 799000.00,
                'sale_price' => 549000.00,
                'status' => 'published',
                'is_published' => true,
                'rating_avg' => 4.95,
                'rating_count' => 112,
                'enrollment_count' => 930,
                'duration_minutes' => 840,
                'tags' => json_encode(['python', 'datascience', 'machine-learning', 'pandas']),
                'is_featured' => true,
                'published_at' => now()->subMonths(3),
                'created_at' => now()->subMonths(3),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'instructor_id' => $instMarketing,
                'category_id' => $catMarketing,
                'title' => 'Digital Marketing & SEO Performance Mastery',
                'slug' => Str::slug('Digital Marketing SEO Performance Mastery'),
                'description' => 'Xây dựng chiến lược Digital Marketing đa kênh, tối ưu hóa SEO Google và chạy quảng cáo Facebook/TikTok Ads.',
                'objectives' => 'Nghiên cứu từ khóa, On-page/Off-page SEO, chạy camp quảng cáo chuyển đổi và đo lường ROI.',
                'thumbnail' => 'course-thumbnails/digital_marketing.png',
                'preview_video' => 'https://www.youtube.com/watch?v=nU-IIXBWlS4',
                'level' => 'beginner',
                'price' => 499000.00,
                'sale_price' => 349000.00,
                'status' => 'published',
                'is_published' => true,
                'rating_avg' => 4.80,
                'rating_count' => 85,
                'enrollment_count' => 670,
                'duration_minutes' => 420,
                'tags' => json_encode(['marketing', 'seo', 'facebook-ads', 'tiktok']),
                'is_featured' => false,
                'published_at' => now()->subWeeks(4),
                'created_at' => now()->subWeeks(4),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'instructor_id' => $instIelts,
                'category_id' => $catLanguage,
                'title' => 'Luyện thi IELTS 7.0+ 4 Kỹ năng & Speaking Practical',
                'slug' => Str::slug('Luyen thi IELTS 7 0 4 Ky nang Speaking Practical'),
                'description' => 'Bứt phá band điểm IELTS từ 5.0 lên 7.0+ với chiến thuật làm bài Listening, Reading, Writing Task 1-2 và Speaking.',
                'objectives' => 'Nắm vững tư duy tư duy phản biện trong Writing Task 2, phản xạ tự nhiên trong Speaking và từ vựng band cao.',
                'thumbnail' => 'course-thumbnails/ielts_mastery.png',
                'preview_video' => 'https://www.youtube.com/watch?v=sQx9Z8Wb1K0',
                'level' => 'intermediate',
                'price' => 899000.00,
                'sale_price' => 599000.00,
                'status' => 'published',
                'is_published' => true,
                'rating_avg' => 4.92,
                'rating_count' => 140,
                'enrollment_count' => 1100,
                'duration_minutes' => 960,
                'tags' => json_encode(['ielts', 'english', 'speaking', 'writing']),
                'is_featured' => true,
                'published_at' => now()->subMonths(2),
                'created_at' => now()->subMonths(2),
                'updated_at' => now(),
            ],
        ];

        $coursesFormatted = array_map(function (array $course) {
            $course['short_description'] = Str::limit($course['description'], 120);
            $course['target_audience'] = 'Sinh viên, lập trình viên và người muốn học nâng cao tay nghề.';
            $course['requirements'] = 'Máy tính cá nhân có kết nối internet.';
            $course['is_published'] = ($course['status'] ?? '') === 'published';
            $course['discount_price'] = $course['sale_price'] ?? null;
            $course['submitted_at'] = null;
            $course['submission_count'] = 0;
            $course['reject_reason'] = null;

            return $course;
        }, $courses);

        DB::table('courses')->insert($coursesFormatted);

        // 4. Tạo Chương học (Chapters)
        $chapters = [
            // Course 1: Laravel
            ['id' => 1, 'course_id' => 1, 'title' => 'Chương 1: Giới thiệu và thiết lập môi trường', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'course_id' => 1, 'title' => 'Chương 2: Cơ sở dữ liệu và Eloquent ORM', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],

            // Course 2: React.js
            ['id' => 3, 'course_id' => 2, 'title' => 'Chương 1: Cú pháp JSX và Components', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'course_id' => 2, 'title' => 'Chương 2: Hooks và State Management', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],

            // Course 3: Figma
            ['id' => 5, 'course_id' => 3, 'title' => 'Chương 1: Tổng quan Figma và Wireframing', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'course_id' => 3, 'title' => 'Chương 2: Design System và Components', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],

            // Course 5: Python Data
            ['id' => 7, 'course_id' => 5, 'title' => 'Chương 1: Lập trình Python & Thư viện Pandas/NumPy', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'course_id' => 5, 'title' => 'Chương 2: Trực quan hóa dữ liệu & Machine Learning', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],

            // Course 6: Digital Marketing
            ['id' => 9, 'course_id' => 6, 'title' => 'Chương 1: Chiến lược SEO & Content Performance', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],

            // Course 7: IELTS
            ['id' => 10, 'course_id' => 7, 'title' => 'Chương 1: IELTS Writing Task 2 Master Strategy', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 11, 'course_id' => 7, 'title' => 'Chương 2: IELTS Speaking Part 1-3 Reflex', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('chapters')->insert($chapters);

        // 5. Tạo Bài học (Lessons)
        $lessons = [
            // Laravel Chapter 1
            [
                'id' => 1,
                'course_id' => 1,
                'chapter_id' => 1,
                'title' => 'Bài 1: Giới thiệu tổng quan về framework Laravel',
                'content' => 'Trong bài học này, chúng ta sẽ tìm hiểu kiến trúc tổng quan của Laravel và lý do vì sao nó là PHP Framework phổ biến nhất.',
                'type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=ImtZ5yENzgE',
                'video_path' => 'lesson-videos/laravel-intro.mp4',
                'duration_seconds' => 900,
                'is_preview' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'course_id' => 1,
                'chapter_id' => 1,
                'title' => 'Bài 2: Thiết lập môi trường Laragon, Composer và cài đặt Laravel',
                'content' => 'Hướng dẫn chi tiết cài đặt môi trường chạy local bằng Laragon, cài đặt Composer và khởi tạo dự án Laravel mới.',
                'type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=ImtZ5yENzgE',
                'video_path' => 'lesson-videos/laravel-intro.mp4',
                'duration_seconds' => 1200,
                'is_preview' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'course_id' => 1,
                'chapter_id' => 1,
                'title' => 'Bài 3: Trắc nghiệm kiểm tra kiến thức Chương 1 Laravel',
                'content' => 'Làm bài kiểm tra ngắn để ôn lại các kiến thức cơ bản về mô hình MVC và các lệnh Artisan vừa học.',
                'type' => 'quiz',
                'video_url' => null,
                'video_path' => null,
                'duration_seconds' => 0,
                'is_preview' => false,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Laravel Chapter 2
            [
                'id' => 4,
                'course_id' => 1,
                'chapter_id' => 2,
                'title' => 'Bài 1: Database Migrations và Seeding trong Laravel',
                'content' => 'Tìm hiểu cách định nghĩa các bảng cơ sở dữ liệu bằng PHP code thông qua Migrations và tạo dữ liệu mẫu với Seeder.',
                'type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=ImtZ5yENzgE',
                'video_path' => 'lesson-videos/laravel-intro.mp4',
                'duration_seconds' => 1800,
                'is_preview' => false,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'course_id' => 1,
                'chapter_id' => 2,
                'title' => 'Bài tập thực hành 1: Thiết kế bảng cơ sở dữ liệu cho Blog cá nhân',
                'content' => 'Viết file migration để tạo cấu trúc bảng cho một hệ thống blog đơn giản gồm bài viết, chuyên mục và nhận xét.',
                'type' => 'assignment',
                'video_url' => null,
                'video_path' => null,
                'duration_seconds' => 0,
                'is_preview' => false,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // React.js Chapter 1 & 2
            [
                'id' => 6,
                'course_id' => 2,
                'chapter_id' => 3,
                'title' => 'Bài 1: JSX là gì? Cách JSX hoạt động dưới nền tảng',
                'content' => 'Hiểu về cú pháp JSX, cách React biên dịch mã JSX sang các hàm JavaScript thuần thông qua Babel.',
                'type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=w7ejDZ8SWv8',
                'video_path' => 'lesson-videos/react-intro.mp4',
                'duration_seconds' => 1500,
                'is_preview' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'course_id' => 2,
                'chapter_id' => 4,
                'title' => 'Bài 2: Trắc nghiệm kiến thức React.js & Hooks',
                'content' => 'Kiểm tra hiểu biết về useState, useEffect, và truyền dữ liệu props giữa các component trong React.',
                'type' => 'quiz',
                'video_url' => null,
                'video_path' => null,
                'duration_seconds' => 0,
                'is_preview' => false,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'course_id' => 2,
                'chapter_id' => 4,
                'title' => 'Bài tập thực hành 2: Xây dựng Todo App với React.js Hooks',
                'content' => 'Tạo ứng dụng danh sách công việc (Todo List) với các tính năng Thêm, Sửa, Xóa và Đánh dấu hoàn thành.',
                'type' => 'assignment',
                'video_url' => null,
                'video_path' => null,
                'duration_seconds' => 0,
                'is_preview' => false,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Figma Chapter 1 & 2
            [
                'id' => 9,
                'course_id' => 3,
                'chapter_id' => 5,
                'title' => 'Bài 1: Tổng quan giao diện Figma & Tạo Wireframe',
                'content' => 'Làm quen với Canvas, Frames, Vector networks và dựng Wireframe cho ứng dụng di động.',
                'type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=jk1T0CeYwV4',
                'video_path' => 'lesson-videos/figma-intro.mp4',
                'duration_seconds' => 1200,
                'is_preview' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 10,
                'course_id' => 3,
                'chapter_id' => 6,
                'title' => 'Bài tập thực hành 3: Thiết kế Mobile App Wireframe bằng Figma',
                'content' => 'Tạo 3 màn hình Wireframe (Home, Detail, Profile) cho một ứng dụng bán hàng trên thiết bị iPhone 15 Pro.',
                'type' => 'assignment',
                'video_url' => null,
                'video_path' => null,
                'duration_seconds' => 0,
                'is_preview' => false,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Python Data Chapter 1 & 2
            [
                'id' => 11,
                'course_id' => 5,
                'chapter_id' => 7,
                'title' => 'Bài 1: Xử lý mảng dữ liệu với NumPy và DataFrame với Pandas',
                'content' => 'Hướng dẫn cách làm sạch dữ liệu, lọc dữ liệu trùng lặp và tính toán chỉ số thống kê cơ bản với Pandas.',
                'type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=r-uOLxNrNk8',
                'video_path' => 'lesson-videos/python-intro.mp4',
                'duration_seconds' => 2100,
                'is_preview' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 12,
                'course_id' => 5,
                'chapter_id' => 8,
                'title' => 'Bài tập thực hành 4: Phân tích bộ dữ liệu Doanh số bằng Pandas',
                'content' => 'Đọc file CSV đính kèm, tính tổng doanh thu theo từng sản phẩm và vẽ biểu đồ doanh số bằng Matplotlib.',
                'type' => 'assignment',
                'video_url' => null,
                'video_path' => null,
                'duration_seconds' => 0,
                'is_preview' => false,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Digital Marketing Chapter 1
            [
                'id' => 13,
                'course_id' => 6,
                'chapter_id' => 9,
                'title' => 'Bài 1: Nghiên cứu Từ khóa & Lập bản đồ Keyword SEO Top 1',
                'content' => 'Phương pháp nghiên cứu từ khóa với Ahrefs/Google Keyword Planner và lập chiến lược từ khóa ngắn/dài.',
                'type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=nU-IIXBWlS4',
                'video_path' => 'lesson-videos/laravel-intro.mp4',
                'duration_seconds' => 1500,
                'is_preview' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 14,
                'course_id' => 6,
                'chapter_id' => 9,
                'title' => 'Bài tập thực hành 5: Lập Kế hoạch Quảng cáo Facebook Ads',
                'content' => 'Xây dựng kế hoạch ngân sách và phân tích đối tượng mục tiêu (Target Audience) cho chiến dịch quảng cáo.',
                'type' => 'assignment',
                'video_url' => null,
                'video_path' => null,
                'duration_seconds' => 0,
                'is_preview' => false,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // IELTS Chapter 1 & 2
            [
                'id' => 15,
                'course_id' => 7,
                'chapter_id' => 10,
                'title' => 'Bài 1: Cấu trúc bài luận Writing Task 2 & Mở bài chuẩn Band 7.0+',
                'content' => 'Phân tích 5 dạng bài IELTS Writing Task 2 và chiến thuật viết Paraphrase + Thesis Statement chuẩn Band 7.0+.',
                'type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=sQx9Z8Wb1K0',
                'video_path' => 'lesson-videos/react-intro.mp4',
                'duration_seconds' => 1800,
                'is_preview' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 16,
                'course_id' => 7,
                'chapter_id' => 10,
                'title' => 'Bài tập thực hành 6: Viết bài luận IELTS Writing Task 2 Topic Education',
                'content' => 'Viết bài luận hoàn chỉnh từ 250-300 từ trả lời câu hỏi: "Should university education be free for everyone?"',
                'type' => 'assignment',
                'video_url' => null,
                'video_path' => null,
                'duration_seconds' => 0,
                'is_preview' => false,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        $lessonsFormatted = array_map(function (array $lesson) {
            $lesson['status'] = 'published';
            $lesson['is_required'] = true;

            return $lesson;
        }, $lessons);

        DB::table('lessons')->insert($lessonsFormatted);

        // 6. Tài liệu đính kèm bài học (Attachments)
        $attachments = [
            [
                'lesson_id' => 1,
                'title' => 'Laravel Cheatsheet cho lập trình viên',
                'file_path' => 'attachments/laravel-cheatsheet.pdf',
                'file_type' => 'pdf',
                'file_size' => 1024560,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'lesson_id' => 9,
                'title' => 'UI UX Wireframe Kit cho Figma',
                'file_path' => 'attachments/figma-wireframe-kit.fig',
                'file_type' => 'fig',
                'file_size' => 5242880,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'lesson_id' => 11,
                'title' => 'Bộ dữ liệu thực hành Pandas (CSV)',
                'file_path' => 'attachments/sales-data-sample.csv',
                'file_type' => 'csv',
                'file_size' => 450200,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'lesson_id' => 15,
                'title' => 'Bộ Từ vựng IELTS Writing Band 8.0 Topic Education',
                'file_path' => 'attachments/ielts-vocab-band8.pdf',
                'file_type' => 'pdf',
                'file_size' => 2048000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('lesson_attachments')->insert($attachments);

        // 7. Bài tập nộp (Assignments) cho từng khóa học
        $assignments = [
            [
                'id' => 1,
                'lesson_id' => 5, // Course 1: Laravel
                'title' => 'Thiết kế Migration cho Blog cá nhân',
                'description' => 'Hãy viết một đoạn mã PHP trong hàm up() của migration để tạo bảng "posts" có các cột: id, title, slug, content, published_at, user_id (khóa ngoại đến bảng users) và timestamps. Nộp mã nguồn file migration.',
                'due_date' => now()->addDays(7),
                'max_score' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'lesson_id' => 8, // Course 2: React
                'title' => 'Xây dựng Todo App với React.js Hooks',
                'description' => 'Tạo ứng dụng Quản lý công việc (Todo App) bằng React.js function component và useState. Yêu cầu tính năng: Thêm công việc mới, Đánh dấu hoàn thành, Xóa công việc khỏi danh sách.',
                'due_date' => now()->addDays(10),
                'max_score' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'lesson_id' => 10, // Course 3: Figma
                'title' => 'Thiết kế Mobile App Wireframe bằng Figma',
                'description' => 'Thiết kế bộ 3 màn hình Wireframe (Trang chủ, Chi tiết sản phẩm, Giỏ hàng) trên Figma với kích thước khung iPhone 15 Pro.',
                'due_date' => now()->addDays(7),
                'max_score' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'lesson_id' => 12, // Course 5: Python Data
                'title' => 'Phân tích bộ dữ liệu Doanh số bằng Pandas',
                'description' => 'Đọc bộ dữ liệu CSV được đính kèm trong bài học. Sử dụng Pandas để lọc ra top 5 sản phẩm bán chạy nhất và tính doanh thu trung bình mỗi tháng.',
                'due_date' => now()->addDays(14),
                'max_score' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'lesson_id' => 14, // Course 6: Digital Marketing
                'title' => 'Lập Kế hoạch Quảng cáo Facebook Ads cho Thương hiệu',
                'description' => 'Lập kế hoạch phân bổ ngân sách 10 triệu VNĐ cho chiến dịch Facebook Ads. Xác định rõ chân dung khách hàng (Demographics, Interests, Behaviors) và viết 2 mẫu Content Ad.',
                'due_date' => now()->addDays(7),
                'max_score' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'lesson_id' => 16, // Course 7: IELTS
                'title' => 'Viết bài luận IELTS Writing Task 2 Topic Education',
                'description' => 'Viết bài luận mẫu từ 250 đến 300 từ thảo luận về đề bài: "Some people believe that university education should be free for all students. To what extent do you agree or disagree?"',
                'due_date' => now()->addDays(5),
                'max_score' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('assignments')->insert($assignments);

        // 8. Trắc nghiệm Quiz cho các khóa học
        $quizzes = [
            [
                'id' => 1,
                'lesson_id' => 3, // Course 1: Laravel
                'title' => 'Kiểm tra trắc nghiệm Chương 1: Tổng quan Laravel',
                'pass_score' => 70,
                'time_limit_minutes' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'lesson_id' => 7, // Course 2: React
                'title' => 'Kiểm tra trắc nghiệm Chương 1: Cấu trúc React & Hooks',
                'pass_score' => 75,
                'time_limit_minutes' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('quizzes')->insert($quizzes);

        // 9. Câu hỏi Quiz (Ít nhất 5 câu cho mỗi Quiz)
        $quizQuestions = [
            // Quiz 1: Laravel (5 câu)
            [
                'id' => 1,
                'quiz_id' => 1,
                'question' => 'Mô hình MVC của Laravel viết tắt của cụm từ nào?',
                'type' => 'single',
                'points' => 5,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'quiz_id' => 1,
                'question' => 'Lệnh Artisan nào được sử dụng để khởi chạy máy chủ phát triển của Laravel?',
                'type' => 'single',
                'points' => 5,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'quiz_id' => 1,
                'question' => 'File cấu hình biến môi trường chính trong dự án Laravel có tên là gì?',
                'type' => 'single',
                'points' => 5,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'quiz_id' => 1,
                'question' => 'ORM mặc định được tích hợp trong Laravel là gì?',
                'type' => 'single',
                'points' => 5,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'quiz_id' => 1,
                'question' => 'Cú pháp nào trong Blade Template giúp escape HTML để phòng chống XSS?',
                'type' => 'single',
                'points' => 5,
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Quiz 2: React (5 câu)
            [
                'id' => 6,
                'quiz_id' => 2,
                'question' => 'Hook nào trong React được dùng để quản lý state trong Function Component?',
                'type' => 'single',
                'points' => 5,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'quiz_id' => 2,
                'question' => 'JSX trong React có nghĩa là gì?',
                'type' => 'single',
                'points' => 5,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'quiz_id' => 2,
                'question' => 'Hook nào dùng để thực thi Side Effects (gọi API, timer, DOM event)?',
                'type' => 'single',
                'points' => 5,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 9,
                'quiz_id' => 2,
                'question' => 'Công cụ Build tool hiện đại nào được khuyên dùng để khởi tạo dự án React siêu nhanh?',
                'type' => 'single',
                'points' => 5,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 10,
                'quiz_id' => 2,
                'question' => 'Khái niệm nào được dùng để truyền dữ liệu từ Component cha xuống Component con trong React?',
                'type' => 'single',
                'points' => 5,
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('quiz_questions')->insert($quizQuestions);

        // 10. Phương án trắc nghiệm (Quiz Options - 3 đáp án cho mỗi câu hỏi)
        $quizOptions = [
            // Question 1 (Laravel)
            ['quiz_question_id' => 1, 'option_text' => 'Model - View - Controller', 'is_correct' => true, 'created_at' => now(), 'updated_at' => now()],
            ['quiz_question_id' => 1, 'option_text' => 'Module - Variable - Class', 'is_correct' => false, 'created_at' => now(), 'updated_at' => now()],
            ['quiz_question_id' => 1, 'option_text' => 'Main - Value - Component', 'is_correct' => false, 'created_at' => now(), 'updated_at' => now()],
            // Question 2
            ['quiz_question_id' => 2, 'option_text' => 'php artisan serve', 'is_correct' => true, 'created_at' => now(), 'updated_at' => now()],
            ['quiz_question_id' => 2, 'option_text' => 'php artisan start', 'is_correct' => false, 'created_at' => now(), 'updated_at' => now()],
            ['quiz_question_id' => 2, 'option_text' => 'php artisan run', 'is_correct' => false, 'created_at' => now(), 'updated_at' => now()],
            // Question 3
            ['quiz_question_id' => 3, 'option_text' => '.env', 'is_correct' => true, 'created_at' => now(), 'updated_at' => now()],
            ['quiz_question_id' => 3, 'option_text' => 'config.json', 'is_correct' => false, 'created_at' => now(), 'updated_at' => now()],
            ['quiz_question_id' => 3, 'option_text' => 'settings.yaml', 'is_correct' => false, 'created_at' => now(), 'updated_at' => now()],
            // Question 4
            ['quiz_question_id' => 4, 'option_text' => 'Eloquent ORM', 'is_correct' => true, 'created_at' => now(), 'updated_at' => now()],
            ['quiz_question_id' => 4, 'option_text' => 'Doctrine ORM', 'is_correct' => false, 'created_at' => now(), 'updated_at' => now()],
            ['quiz_question_id' => 4, 'option_text' => 'Hibernate ORM', 'is_correct' => false, 'created_at' => now(), 'updated_at' => now()],
            // Question 5
            ['quiz_question_id' => 5, 'option_text' => '{{ $data }}', 'is_correct' => true, 'created_at' => now(), 'updated_at' => now()],
            ['quiz_question_id' => 5, 'option_text' => '{!! $data !!}', 'is_correct' => false, 'created_at' => now(), 'updated_at' => now()],
            ['quiz_question_id' => 5, 'option_text' => '<% $data %>', 'is_correct' => false, 'created_at' => now(), 'updated_at' => now()],

            // Question 6 (React)
            ['quiz_question_id' => 6, 'option_text' => 'useState', 'is_correct' => true, 'created_at' => now(), 'updated_at' => now()],
            ['quiz_question_id' => 6, 'option_text' => 'useEffect', 'is_correct' => false, 'created_at' => now(), 'updated_at' => now()],
            ['quiz_question_id' => 6, 'option_text' => 'useContext', 'is_correct' => false, 'created_at' => now(), 'updated_at' => now()],
            // Question 7
            ['quiz_question_id' => 7, 'option_text' => 'JavaScript XML', 'is_correct' => true, 'created_at' => now(), 'updated_at' => now()],
            ['quiz_question_id' => 7, 'option_text' => 'Java Extension Syntax', 'is_correct' => false, 'created_at' => now(), 'updated_at' => now()],
            ['quiz_question_id' => 7, 'option_text' => 'JSON Extended Notation', 'is_correct' => false, 'created_at' => now(), 'updated_at' => now()],
            // Question 8
            ['quiz_question_id' => 8, 'option_text' => 'useEffect', 'is_correct' => true, 'created_at' => now(), 'updated_at' => now()],
            ['quiz_question_id' => 8, 'option_text' => 'useMemo', 'is_correct' => false, 'created_at' => now(), 'updated_at' => now()],
            ['quiz_question_id' => 8, 'option_text' => 'useCallback', 'is_correct' => false, 'created_at' => now(), 'updated_at' => now()],
            // Question 9
            ['quiz_question_id' => 9, 'option_text' => 'Vite', 'is_correct' => true, 'created_at' => now(), 'updated_at' => now()],
            ['quiz_question_id' => 9, 'option_text' => 'Webpack 3', 'is_correct' => false, 'created_at' => now(), 'updated_at' => now()],
            ['quiz_question_id' => 9, 'option_text' => 'Gulp', 'is_correct' => false, 'created_at' => now(), 'updated_at' => now()],
            // Question 10
            ['quiz_question_id' => 10, 'option_text' => 'Props', 'is_correct' => true, 'created_at' => now(), 'updated_at' => now()],
            ['quiz_question_id' => 10, 'option_text' => 'State', 'is_correct' => false, 'created_at' => now(), 'updated_at' => now()],
            ['quiz_question_id' => 10, 'option_text' => 'Redux', 'is_correct' => false, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('quiz_options')->insert($quizOptions);

        // Kích hoạt và xuất bản các phiên bản Quiz V1
        $quizzes = Quiz::all();
        foreach ($quizzes as $quiz) {
            $quiz->update(['is_active' => true]);

            $versionId = DB::table('quiz_versions')->insertGetId([
                'quiz_id' => $quiz->id,
                'version' => 1,
                'title' => $quiz->title,
                'description' => null,
                'pass_score' => $quiz->pass_score ?? 70,
                'time_limit_minutes' => $quiz->time_limit_minutes ?? 15,
                'max_attempts' => null,
                'status' => 'published',
                'created_by' => 2,
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('quizzes')->where('id', $quiz->id)->update([
                'current_published_version_id' => $versionId,
                'current_draft_version_id' => null,
            ]);

            $questions = DB::table('quiz_questions')->where('quiz_id', $quiz->id)->orderBy('sort_order')->get();
            foreach ($questions as $q) {
                $qVersionId = DB::table('question_versions')->insertGetId([
                    'question_id' => $q->id,
                    'version' => 1,
                    'question' => $q->question,
                    'type' => $q->type,
                    'points' => $q->points,
                    'explanation' => null,
                    'status' => 'published',
                    'published_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('quiz_version_questions')->insert([
                    'quiz_version_id' => $versionId,
                    'question_id' => $q->id,
                    'question_version_id' => $qVersionId,
                    'sort_order' => $q->sort_order,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('quiz_options')->where('quiz_question_id', $q->id)->update([
                    'question_version_id' => $qVersionId,
                ]);
            }
        }
    }

    private function categoryId(string $name): int
    {
        return (int) (
            Category::where('slug', Str::slug($name))->value('id')
            ?: Category::where('name', 'LIKE', "%{$name}%")->value('id')
            ?: 1
        );
    }
}
