<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryIds = [
            'web' => $this->categoryId('Phát triển Web'),
            'design' => $this->categoryId('Thiết kế UI/UX'),
            'data' => $this->categoryId('Khoa học dữ liệu'),
        ];

        // 1. Tạo khóa học mẫu (Courses)
        $courses = [
            [
                'id' => 1,
                'instructor_id' => 2, // Nguyễn Văn Giảng
                'category_id' => $categoryIds['web'],
                'title' => 'Laravel từ Zero đến Hero',
                'slug' => Str::slug('Laravel từ Zero đến Hero'),
                'description' => 'Khóa học toàn diện về framework Laravel từ cơ bản đến nâng cao. Học qua dự án thực tế.',
                'objectives' => 'Nắm vững MVC, Eloquent ORM, Routing, Middleware, Xây dựng hoàn chỉnh dự án RESTful API và Web app.',
                'thumbnail' => 'laravel_zero_hero.png',
                'preview_video' => 'https://example.com/videos/laravel-intro.mp4',
                'level' => 'beginner',
                'price' => 499000.00,
                'sale_price' => 299000.00,
                'status' => 'published',
                'rejection_reason' => null,
                'rating_avg' => 4.85,
                'rating_count' => 150,
                'enrollment_count' => 1250,
                'duration_minutes' => 720, // 12 tiếng
                'tags' => json_encode(['php', 'laravel', 'backend', 'web']),
                'is_featured' => true,
                'published_at' => now()->subMonths(2),
                'created_at' => now()->subMonths(2),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'instructor_id' => 2, // Nguyễn Văn Giảng
                'category_id' => $categoryIds['web'],
                'title' => 'React.js Masterclass',
                'slug' => Str::slug('React.js Masterclass'),
                'description' => 'Làm chủ React.js và các công cụ hiện đại như Redux Toolkit, React Router, Vite, TailwindCSS.',
                'objectives' => 'Hiểu sâu về Virtual DOM, Component Lifecycle, Hooks, State Management, và xây dựng ứng dụng Single Page App chuyên nghiệp.',
                'thumbnail' => 'react_masterclass.png',
                'preview_video' => 'https://example.com/videos/react-intro.mp4',
                'level' => 'intermediate',
                'price' => 599000.00,
                'sale_price' => 399000.00,
                'status' => 'published',
                'rejection_reason' => null,
                'rating_avg' => 4.75,
                'rating_count' => 98,
                'enrollment_count' => 840,
                'duration_minutes' => 600, // 10 tiếng
                'tags' => json_encode(['javascript', 'react', 'frontend', 'spa']),
                'is_featured' => true,
                'published_at' => now()->subMonths(1),
                'created_at' => now()->subMonths(1),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'instructor_id' => 3, // Trần Đức Dũng
                'category_id' => $categoryIds['design'],
                'title' => 'UI/UX Design Fundamentals',
                'slug' => Str::slug('UI/UX Design Fundamentals'),
                'description' => 'Học thiết kế trải nghiệm người dùng (UX) và giao diện trực quan (UI) bằng công cụ Figma từ con số 0.',
                'objectives' => 'Thiết lập Wireframe, thiết kế Prototype tương tác, hiểu về Grid System, Typography, Color Palette và quy trình Design Thinking.',
                'thumbnail' => 'uiux_fundamentals.png',
                'preview_video' => null,
                'level' => 'beginner',
                'price' => 399000.00,
                'sale_price' => null,
                'status' => 'published',
                'rejection_reason' => null,
                'rating_avg' => 4.90,
                'rating_count' => 64,
                'enrollment_count' => 320,
                'duration_minutes' => 480, // 8 tiếng
                'tags' => json_encode(['figma', 'design', 'ui', 'ux']),
                'is_featured' => false,
                'published_at' => now()->subWeeks(3),
                'created_at' => now()->subWeeks(3),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'instructor_id' => 2, // Nguyễn Văn Giảng
                'category_id' => $categoryIds['data'],
                'title' => 'Python cho Data Science',
                'slug' => Str::slug('Python cho Data Science'),
                'description' => 'Sử dụng Python để phân tích dữ liệu, trực quan hóa và xây dựng các thuật toán Machine Learning cơ bản.',
                'objectives' => 'Sử dụng thành thạo NumPy, Pandas, Matplotlib, Seaborn và Scikit-Learn để xử lý các tập dữ liệu thực tế.',
                'thumbnail' => 'python_data_science.png',
                'preview_video' => 'https://example.com/videos/python-data.mp4',
                'level' => 'advanced',
                'price' => 699000.00,
                'sale_price' => 499000.00,
                'status' => 'published',
                'rejection_reason' => null,
                'rating_avg' => 4.60,
                'rating_count' => 42,
                'enrollment_count' => 210,
                'duration_minutes' => 900, // 15 tiếng
                'tags' => json_encode(['python', 'data-science', 'machine-learning']),
                'is_featured' => false,
                'published_at' => now()->subWeeks(2),
                'created_at' => now()->subWeeks(2),
                'updated_at' => now(),
            ],
        ];

        DB::table('courses')->insert($courses);

        // 2. Tạo Chương học mẫu (Chapters)
        $chapters = [
            // Laravel từ Zero đến Hero (Course ID: 1)
            [
                'id' => 1,
                'course_id' => 1,
                'title' => 'Chương 1: Giới thiệu và thiết lập môi trường',
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'course_id' => 1,
                'title' => 'Chương 2: Cơ sở dữ liệu và Eloquent ORM',
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // React.js Masterclass (Course ID: 2)
            [
                'id' => 3,
                'course_id' => 2,
                'title' => 'Chương 1: Cú pháp JSX và Components',
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // UI/UX Design Fundamentals (Course ID: 3)
            [
                'id' => 4,
                'course_id' => 3,
                'title' => 'Chương 1: Nguyên lý thiết kế trực quan',
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Python cho Data Science (Course ID: 4)
            [
                'id' => 5,
                'course_id' => 4,
                'title' => 'Chương 1: Lập trình Python cơ bản',
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('chapters')->insert($chapters);

        // 3. Tạo Bài học mẫu (Lessons)
        $lessons = [
            // Chương 1 - Laravel (Chapter ID: 1)
            [
                'id' => 1,
                'chapter_id' => 1,
                'title' => 'Bài 1: Giới thiệu tổng quan về framework Laravel',
                'content' => 'Trong bài học này, chúng ta sẽ tìm hiểu kiến trúc tổng quan của Laravel và lý do vì sao nó là PHP Framework phổ biến nhất hiện nay.',
                'type' => 'video',
                'video_url' => 'https://example.com/videos/lessons/laravel-01.mp4',
                'duration_seconds' => 900, // 15 phút
                'is_preview' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'chapter_id' => 1,
                'title' => 'Bài 2: Thiết lập môi trường Laragon, Composer và cài đặt Laravel',
                'content' => 'Hướng dẫn chi tiết cài đặt môi trường chạy local bằng Laragon, cài đặt Composer và khởi tạo dự án Laravel mới.',
                'type' => 'video',
                'video_url' => 'https://example.com/videos/lessons/laravel-02.mp4',
                'duration_seconds' => 1200, // 20 phút
                'is_preview' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'chapter_id' => 1,
                'title' => 'Bài 3: Trắc nghiệm kiểm tra kiến thức Chương 1',
                'content' => 'Làm bài kiểm tra ngắn để ôn lại các kiến thức cơ bản về mô hình MVC và các lệnh Artisan vừa học.',
                'type' => 'quiz',
                'video_url' => null,
                'duration_seconds' => 0,
                'is_preview' => false,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Chương 2 - Laravel (Chapter ID: 2)
            [
                'id' => 4,
                'chapter_id' => 2,
                'title' => 'Bài 1: Database Migrations và Seeding trong Laravel',
                'content' => 'Tìm hiểu cách định nghĩa các bảng cơ sở dữ liệu bằng PHP code thông qua Migrations và tạo dữ liệu mẫu với Seeder.',
                'type' => 'video',
                'video_url' => 'https://example.com/videos/lessons/laravel-03.mp4',
                'duration_seconds' => 1800, // 30 phút
                'is_preview' => false,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'chapter_id' => 2,
                'title' => 'Bài tập thực hành: Thiết kế bảng cơ sở dữ liệu cho Blog cá nhân',
                'content' => 'Viết file migration để tạo cấu trúc bảng cho một hệ thống blog đơn giản gồm bài viết, chuyên mục và nhận xét.',
                'type' => 'assignment',
                'video_url' => null,
                'duration_seconds' => 0,
                'is_preview' => false,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Chương 1 - React (Chapter ID: 3)
            [
                'id' => 6,
                'chapter_id' => 3,
                'title' => 'Bài 1: JSX là gì? Cách JSX hoạt động dưới nền tảng',
                'content' => 'Hiểu về cú pháp JSX, cách React biên dịch mã JSX sang các hàm JavaScript thuần thông qua Babel.',
                'type' => 'video',
                'video_url' => 'https://example.com/videos/lessons/react-01.mp4',
                'duration_seconds' => 1500, // 25 phút
                'is_preview' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Chương 1 - UI/UX (Chapter ID: 4)
            [
                'id' => 7,
                'chapter_id' => 4,
                'title' => 'Bài 1: Luật phân cấp thị giác (Visual Hierarchy) trong thiết kế Web',
                'content' => 'Cách áp dụng độ tương phản, kích thước, khoảng trắng và vị trí để định hướng hành vi đọc của người dùng trên giao diện.',
                'type' => 'document',
                'video_url' => null,
                'duration_seconds' => 0,
                'is_preview' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Chương 1 - Python (Chapter ID: 5)
            [
                'id' => 8,
                'chapter_id' => 5,
                'title' => 'Bài 1: Kiểu dữ liệu và Cấu trúc điều khiển trong Python',
                'content' => 'Nắm rõ các kiểu dữ liệu cơ bản (List, Tuple, Dictionary) và cấu trúc điều kiện, vòng lặp trong Python.',
                'type' => 'video',
                'video_url' => 'https://example.com/videos/lessons/python-01.mp4',
                'duration_seconds' => 2400, // 40 phút
                'is_preview' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('lessons')->insert($lessons);

        // 4. Tạo Tài liệu đính kèm bài học (Lesson Attachments)
        $attachments = [
            [
                'lesson_id' => 1,
                'title' => 'Laravel Cheatsheet cho lập trình viên',
                'file_path' => 'attachments/laravel-cheatsheet.pdf',
                'file_type' => 'pdf',
                'file_size' => 1024560, // ~1MB
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'lesson_id' => 2,
                'title' => 'Slide bài giảng thiết lập môi trường Laravel',
                'file_path' => 'attachments/slide-lesson-02.pptx',
                'file_type' => 'pptx',
                'file_size' => 2048120, // ~2MB
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('lesson_attachments')->insert($attachments);

        // 5. Tạo Phụ đề bài học (Lesson Subtitles)
        $subtitles = [
            [
                'lesson_id' => 1,
                'language' => 'vi',
                'file_path' => 'subtitles/lesson-1-vi.vtt',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'lesson_id' => 1,
                'language' => 'en',
                'file_path' => 'subtitles/lesson-1-en.vtt',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'lesson_id' => 2,
                'language' => 'vi',
                'file_path' => 'subtitles/lesson-2-vi.vtt',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('lesson_subtitles')->insert($subtitles);

        // 6. Tạo Tóm tắt bài học bằng AI (AI Summaries)
        $aiSummaries = [
            [
                'lesson_id' => 1,
                'summary' => 'Bài học này giới thiệu về Laravel Framework, nhấn mạnh kiến trúc MVC (Model-View-Controller), vòng đời request (Request Lifecycle), và thế mạnh của Laravel trong xử lý Routing, Eloquent ORM và bảo mật tích hợp sẵn giúp nâng cao tốc độ phát triển sản phẩm.',
                'language' => 'vi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'lesson_id' => 2,
                'summary' => 'Hướng dẫn quy trình cài đặt môi trường chạy Laravel trên Windows thông qua phần mềm Laragon. Đồng thời hướng dẫn cài đặt và sử dụng Composer - trình quản lý thư viện của PHP để khởi tạo và chạy dự án Laravel đầu tiên.',
                'language' => 'vi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('ai_summaries')->insert($aiSummaries);

        // 7. Tạo Bài tập nộp (Assignments)
        $assignments = [
            [
                'id' => 1,
                'lesson_id' => 5, // Bài 5 của khóa Laravel (Chapter 2)
                'title' => 'Thiết kế Migration cho Blog cá nhân',
                'description' => 'Hãy viết một đoạn mã PHP trong hàm up() của migration để tạo bảng "posts" có các cột: id, title, slug, content, published_at, user_id (khóa ngoại đến bảng users) và timestamps. Đảm bảo cài đặt các điều kiện ràng buộc khóa ngoại thích hợp.',
                'due_date' => now()->addDays(7),
                'max_score' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('assignments')->insert($assignments);

        // 8. Tạo Bài kiểm tra trắc nghiệm (Quizzes)
        $quizzes = [
            [
                'id' => 1,
                'lesson_id' => 3, // Bài 3 của khóa Laravel (Chapter 1)
                'title' => 'Kiểm tra trắc nghiệm Chương 1: Tổng quan Laravel',
                'pass_score' => 70, // Đạt từ 70% điểm trở lên
                'time_limit_minutes' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('quizzes')->insert($quizzes);

        // 9. Tạo câu hỏi trắc nghiệm (Quiz Questions)
        $quizQuestions = [
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
                'question' => 'Lệnh Artisan nào được sử dụng để khởi chạy máy chủ phát triển (Development Server) của Laravel?',
                'type' => 'single',
                'points' => 5,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'quiz_id' => 1,
                'question' => 'Đâu là các thư mục nằm trong cấu trúc thư mục mặc định của Laravel? (Chọn nhiều đáp án)',
                'type' => 'multiple',
                'points' => 10,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('quiz_questions')->insert($quizQuestions);

        // 10. Tạo các phương án lựa chọn (Quiz Options)
        $quizOptions = [
            // Câu 1
            [
                'quiz_question_id' => 1,
                'option_text' => 'Model - View - Controller',
                'is_correct' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'quiz_question_id' => 1,
                'option_text' => 'Module - Variable - Class',
                'is_correct' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'quiz_question_id' => 1,
                'option_text' => 'Model - View - Component',
                'is_correct' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Câu 2
            [
                'quiz_question_id' => 2,
                'option_text' => 'php artisan start-server',
                'is_correct' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'quiz_question_id' => 2,
                'option_text' => 'php artisan serve',
                'is_correct' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'quiz_question_id' => 2,
                'option_text' => 'php artisan run',
                'is_correct' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Câu 3
            [
                'quiz_question_id' => 3,
                'option_text' => 'app/',
                'is_correct' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'quiz_question_id' => 3,
                'option_text' => 'database/',
                'is_correct' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'quiz_question_id' => 3,
                'option_text' => 'src/',
                'is_correct' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'quiz_question_id' => 3,
                'option_text' => 'routes/',
                'is_correct' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('quiz_options')->insert($quizOptions);
    }

    private function categoryId(string $name): int
    {
        return (int) (
            Category::where('slug', Str::slug($name))->value('id')
            ?: Category::query()->value('id')
            ?: 1
        );
    }
}
