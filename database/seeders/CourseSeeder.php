<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\QuestionVersion;
use App\Models\Quiz;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\QuizVersion;
use App\Models\QuizVersionQuestion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $coursesData = $this->getCoursesData();

        echo "\n========== BẮT ĐẦU NẠP DỮ LIỆU KHÓA HỌC CHUẨN ONLINEFEA ==========\n";

        foreach ($coursesData as $idx => $cData) {
            // 1. Tìm Instructor ID
            $instructor = User::where('email', $cData['instructor_email'])->first()
                ?? User::where('role', 'instructor')->first()
                ?? User::where('role', 'admin')->first();

            $instructorId = $instructor ? $instructor->id : 1;

            // 2. Tìm Category ID
            $category = Category::where('name', $cData['category_name'])->first()
                ?? Category::where('slug', Str::slug($cData['category_name']))->first()
                ?? Category::first();

            $categoryId = $category ? $category->id : 1;

            // 3. Chuẩn bị dữ liệu khóa học
            $price = $cData['price'] ?? 0;
            $salePrice = $cData['sale_price'] ?? null;
            $status = $cData['status'] ?? 'published';
            $isPublished = ($status === 'published');

            $course = Course::updateOrCreate(
                ['slug' => $cData['slug']],
                [
                    'instructor_id' => $instructorId,
                    'category_id' => $categoryId,
                    'title' => $cData['title'],
                    'short_description' => $cData['short_description'] ?? Str::limit($cData['description'], 150),
                    'description' => $cData['description'],
                    'objectives' => json_encode($cData['objectives']),
                    'requirements' => json_encode($cData['requirements']),
                    'target_audience' => json_encode($cData['target_audience']),
                    'thumbnail' => $cData['thumbnail'],
                    'preview_video' => $cData['preview_video'] ?? 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    'level' => $cData['level'] ?? 'beginner',
                    'price' => $price,
                    'sale_price' => $salePrice,
                    'discount_price' => $salePrice,
                    'status' => $status,
                    'is_published' => $isPublished,
                    'is_featured' => $cData['is_featured'] ?? false,
                    'rating_avg' => $cData['rating_avg'] ?? 4.85,
                    'rating_count' => $cData['rating_count'] ?? rand(25, 120),
                    'enrollment_count' => $cData['enrollment_count'] ?? rand(80, 650),
                    'duration_minutes' => $cData['duration_minutes'] ?? 360,
                    'tags' => json_encode($cData['tags']),
                    'published_at' => $isPublished ? now()->subMonths(2) : null,
                    'submitted_at' => ($status === 'pending_review') ? now()->subDays(2) : null,
                    'submission_count' => ($status === 'pending_review') ? 1 : 0,
                    'created_at' => now()->subMonths(3),
                    'updated_at' => now(),
                ]
            );

            // 4. Tạo Chapters, CourseSections và Lessons
            $this->seedCurriculumForCourse($course, $cData);

            if (($idx + 1) % 5 === 0 || ($idx + 1) === count($coursesData)) {
                echo '✓ Đã hoàn thành nạp ' . ($idx + 1) . '/' . count($coursesData) . " khóa học kèm giáo trình đầy đủ.\n";
            }
        }

        echo "\n✓ HOÀN TẤT NẠP TOÀN BỘ KHÓA HỌC CHUẨN VÀ GIÁO TRÌNH BÀI HỌC!\n\n";
    }

    private function seedCurriculumForCourse(Course $course, array $cData): void
    {
        $chapters = $cData['chapters'] ?? [
            [
                'title' => 'Chương 1: Tổng quan Nền tảng & Cài đặt Môi trường',
                'lessons' => [
                    ['title' => 'Bài 1.1: Giới thiệu khóa học & Lộ trình thực chiến', 'type' => Lesson::TYPE_DOCUMENT, 'is_preview' => true],
                    ['title' => 'Bài 1.2: Cài đặt công cụ và cấu hình môi trường phát triển', 'type' => Lesson::TYPE_VIDEO, 'is_preview' => true],
                    ['title' => 'Bài 1.3: Cấu trúc dự án và các quy ước chuẩn quốc tế', 'type' => Lesson::TYPE_DOCUMENT, 'is_preview' => false],
                ]
            ],
            [
                'title' => 'Chương 2: Kiến trúc Cốt lõi & Kỹ thuật Xử lý Chuyên sâu',
                'lessons' => [
                    ['title' => 'Bài 2.1: Phân tích kiến trúc và nguyên tắc thiết kế', 'type' => Lesson::TYPE_DOCUMENT, 'is_preview' => false],
                    ['title' => 'Bài 2.2: Hướng dẫn kỹ thuật xử lý dữ liệu và luồng nghiệp vụ', 'type' => Lesson::TYPE_VIDEO, 'is_preview' => false],
                    ['title' => 'Bài 2.3: Thực hành tối ưu hóa logic và xử lý ngoại lệ', 'type' => Lesson::TYPE_VIDEO, 'is_preview' => false],
                ]
            ],
            [
                'title' => 'Chương 3: Xây dựng Dự án Thực tế & Tích hợp Hệ thống',
                'lessons' => [
                    ['title' => 'Bài 3.1: Xây dựng các module chức năng chính của dự án', 'type' => Lesson::TYPE_VIDEO, 'is_preview' => false],
                    ['title' => 'Bài 3.2: Tích hợp API, bảo mật và kiểm soát truy cập', 'type' => Lesson::TYPE_VIDEO, 'is_preview' => false],
                    ['title' => 'Bài 3.3: Hướng dẫn Debug và giải quyết các lỗi thường gặp', 'type' => Lesson::TYPE_DOCUMENT, 'is_preview' => false],
                ]
            ],
            [
                'title' => 'Chương 4: Tối ưu Hiệu năng, Triển khai & Đánh giá Năng lực',
                'lessons' => [
                    ['title' => 'Bài 4.1: Tối ưu hóa hiệu năng, bảo mật và lưu trữ Cache', 'type' => Lesson::TYPE_VIDEO, 'is_preview' => false],
                    ['title' => 'Bài 4.2: Quy trình Deploy hệ thống lên môi trường Production', 'type' => Lesson::TYPE_DOCUMENT, 'is_preview' => false],
                    ['title' => 'Bài 4.3: Bài Kiểm Tra Trắc Nghiệm Đánh Giá Năng Lực Tổng Kết', 'type' => Lesson::TYPE_QUIZ, 'is_preview' => false],
                ]
            ],
        ];

        $totalDurationSeconds = 0;

        foreach ($chapters as $cIdx => $chapData) {
            $sortOrder = $cIdx + 1;

            $chapter = Chapter::updateOrCreate(
                ['course_id' => $course->id, 'sort_order' => $sortOrder],
                ['title' => $chapData['title']]
            );

            $section = CourseSection::updateOrCreate(
                ['course_id' => $course->id, 'sort_order' => $sortOrder],
                ['title' => $chapData['title'], 'description' => 'Nội dung chi tiết cho ' . $chapData['title']]
            );

            foreach ($chapData['lessons'] as $lIdx => $lesData) {
                $lessonOrder = $lIdx + 1;
                $type = $lesData['type'] ?? Lesson::TYPE_VIDEO;
                $duration = ($type === Lesson::TYPE_QUIZ) ? 1200 : (($type === Lesson::TYPE_DOCUMENT) ? 600 : 900);
                $totalDurationSeconds += $duration;

                $lesson = Lesson::updateOrCreate(
                    ['chapter_id' => $chapter->id, 'sort_order' => $lessonOrder],
                    [
                        'course_id' => $course->id,
                        'section_id' => $section->id,
                        'title' => $lesData['title'],
                        'type' => $type,
                        'content' => ($type === Lesson::TYPE_DOCUMENT)
                            ? "### Nội dung bài học: {$lesData['title']}\n\nTrong bài học này, bạn sẽ được hướng dẫn chi tiết về lý thuyết nền tảng, sơ đồ kiến trúc và các ví dụ code thực tiễn.\n\n#### Điểm trọng tâm:\n* Hiểu rõ bản chất vấn đề và nguyên lý hoạt động\n* Áp dụng ngay vào dự án thực tế của khóa học\n* Ghi nhớ các best-practices để tránh lỗi tiềm ẩn khi triển khai."
                            : null,
                        'duration' => $duration,
                        'duration_seconds' => $duration,
                        'is_preview' => $lesData['is_preview'] ?? false,
                        'is_required' => true,
                        'status' => 'published',
                        'upload_status' => ($type === Lesson::TYPE_VIDEO) ? 'uploaded' : 'completed',
                        'processing_status' => ($type === Lesson::TYPE_VIDEO) ? 'ready' : 'completed',
                    ]
                );

                if ($type === Lesson::TYPE_QUIZ) {
                    $this->createQuizForLesson($course, $lesson);
                }
            }
        }

        $course->update(['duration_minutes' => (int) round($totalDurationSeconds / 60)]);
    }

    private function createQuizForLesson(Course $course, Lesson $lesson): void
    {
        $quiz = Quiz::updateOrCreate(
            ['lesson_id' => $lesson->id],
            [
                'title' => 'Đánh giá năng lực tổng kết: ' . $course->title,
                'pass_score' => 75,
                'time_limit_minutes' => 20,
            ]
        );

        $quizVersion = QuizVersion::updateOrCreate(
            ['quiz_id' => $quiz->id, 'version' => 1],
            [
                'title' => $quiz->title,
                'description' => 'Bộ câu hỏi trắc nghiệm kiểm tra độ hiểu sâu toàn bộ kiến thức trong khóa học.',
                'pass_score' => 75,
                'time_limit_minutes' => 20,
                'max_attempts' => 5,
                'status' => 'published',
                'published_at' => now(),
            ]
        );

        $quiz->update(['current_published_version_id' => $quizVersion->id]);

        $questions = [
            [
                'q' => "Đâu là ưu điểm cốt lõi của công nghệ được giảng dạy trong khóa học: {$course->title}?",
                'options' => [
                    ['text' => 'Cấu trúc module rõ ràng, khả năng mở rộng cao và hiệu năng tối ưu', 'is_correct' => true],
                    ['text' => 'Không cần kiểm thử mã nguồn trước khi đưa vào vận hành', 'is_correct' => false],
                    ['text' => 'Chỉ có thể chạy cục bộ trên môi trường máy tính cá nhân', 'is_correct' => false],
                    ['text' => 'Không thể kết nối với các cơ sở dữ liệu quan hệ', 'is_correct' => false],
                ],
                'exp' => 'Kiến trúc hiện đại chú trọng tính tái sử dụng, khả năng mở rộng (scalability) và hiệu năng vận hành.'
            ],
            [
                'q' => 'Nguyên tắc thiết kế nào giúp tách biệt mối bận tâm và nâng cao tính dễ bảo trì của ứng dụng?',
                'options' => [
                    ['text' => 'Dependency Injection và phân tầng kiến trúc (Clean Architecture)', 'is_correct' => true],
                    ['text' => 'Viết tất cả logic vào trong một file controller duy nhất', 'is_correct' => false],
                    ['text' => 'Lưu trữ thông tin bí mật trực tiếp vào frontend client', 'is_correct' => false],
                    ['text' => 'Bỏ qua việc validate dữ liệu người dùng nhập vào', 'is_correct' => false],
                ],
                'exp' => 'Clean Architecture và Dependency Injection là chuẩn mực giúp code dễ đọc, dễ test và dễ bảo trì.'
            ],
            [
                'q' => 'Giải pháp bảo mật nào là thiết yếu nhất khi xây dựng ứng dụng web và API hiện đại?',
                'options' => [
                    ['text' => 'Validate dữ liệu chặt chẽ, dùng Prepared Statements, HTTPS và kiểm soát phân quyền RBAC', 'is_correct' => true],
                    ['text' => 'Mở toàn bộ quyền truy cập công khai cho mọi endpoint API', 'is_correct' => false],
                    ['text' => 'Không mã hóa mật khẩu người dùng trong cơ sở dữ liệu', 'is_correct' => false],
                    ['text' => 'Tắt tường lửa WAF để tiết kiệm băng thông mạng', 'is_correct' => false],
                ],
                'exp' => 'Bảo mật đa lớp (Defense in depth) là nguyên tắc bắt buộc đối với mọi hệ thống thông tin.'
            ],
            [
                'q' => 'Lợi ích lớn nhất của việc áp dụng quy trình kiểm thử tự động (Automated Testing) là gì?',
                'options' => [
                    ['text' => 'Phát hiện lỗi hồi quy sớm, tự tin refactor và đẩy nhanh tốc độ release phần mềm', 'is_correct' => true],
                    ['text' => 'Làm cho hệ thống chạy chậm hơn và không đem lại giá trị bảo trì', 'is_correct' => false],
                    ['text' => 'Chỉ có tác dụng với các ứng dụng desktop nhỏ lẻ', 'is_correct' => false],
                    ['text' => 'Thay thế hoàn toàn vai trò của người lập trình viên', 'is_correct' => false],
                ],
                'exp' => 'Automated Testing giúp nâng cao độ tin cậy và sự ổn định của hệ thống trong vòng đời phát triển.'
            ],
        ];

        foreach ($questions as $qIdx => $qItem) {
            $question = QuizQuestion::updateOrCreate(
                ['quiz_id' => $quiz->id, 'sort_order' => $qIdx + 1],
                [
                    'question' => $qItem['q'],
                    'type' => 'single',
                    'points' => 25,
                    'explanation' => $qItem['exp'],
                ]
            );

            $questionVersion = QuestionVersion::updateOrCreate(
                ['question_id' => $question->id, 'version' => 1],
                [
                    'question' => $qItem['q'],
                    'type' => 'single',
                    'points' => 25,
                    'explanation' => $qItem['exp'],
                    'status' => 'published',
                    'published_at' => now(),
                ]
            );

            QuizVersionQuestion::updateOrCreate(
                [
                    'quiz_version_id' => $quizVersion->id,
                    'question_id' => $question->id,
                ],
                [
                    'question_version_id' => $questionVersion->id,
                    'sort_order' => $qIdx + 1,
                ]
            );

            foreach ($qItem['options'] as $oIdx => $opt) {
                QuizOption::updateOrCreate(
                    [
                        'quiz_question_id' => $question->id,
                        'option_text' => $opt['text'],
                    ],
                    [
                        'question_version_id' => $questionVersion->id,
                        'is_correct' => $opt['is_correct'],
                        'sort_order' => $oIdx + 1,
                    ]
                );
            }
        }
    }

    private function getCoursesData(): array
    {
        return [
            // =========================================================================
            // 1. LẬP TRÌNH & PHÁT TRIỂN WEB
            // =========================================================================
            [
                'title' => 'Laravel từ Zero đến Hero',
                'slug' => Str::slug('Laravel từ Zero đến Hero'),
                'instructor_email' => 'instructor@example.com',
                'category_name' => 'Phát triển Web',
                'description' => 'Khóa học toàn diện về framework Laravel từ cơ bản đến nâng cao. Xây dựng các dự án web thực tế chuẩn doanh nghiệp với MVC, Eloquent, Authentication và RESTful API.',
                'objectives' => ['Nắm vững kiến trúc MVC và vòng đời Request trong Laravel', 'Làm chủ Eloquent ORM và tối ưu truy vấn Database', 'Xây dựng hệ thống phân quyền RBAC và RESTful API', 'Triển khai dự án lên Cloud Server Production'],
                'requirements' => ['Có kiến thức cơ bản về lập trình PHP và HTML/CSS', 'Máy tính cá nhân có kết nối Internet'],
                'target_audience' => ['Lập trình viên muốn thành thạo Laravel Framework', 'Sinh viên CNTT chuẩn bị đi làm'],
                'thumbnail' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=700',
                'level' => 'beginner',
                'price' => 790000,
                'sale_price' => 499000,
                'rating_avg' => 4.90,
                'rating_count' => 165,
                'enrollment_count' => 1450,
                'tags' => ['laravel', 'php', 'backend', 'web', 'mvc'],
                'is_featured' => true,
                'status' => 'published',
            ],
            [
                'title' => 'React.js Masterclass',
                'slug' => Str::slug('React.js Masterclass'),
                'instructor_email' => 'phuong.frontend@example.com',
                'category_name' => 'Phát triển Web',
                'description' => 'Làm chủ React.js và các công nghệ hiện đại như Redux Toolkit, React Router v6, Vite, Tailwind CSS. Xây dựng các ứng dụng Single Page Application (SPA) chuyên nghiệp.',
                'objectives' => ['Hiểu sâu Virtual DOM, Component Lifecycle và React Hooks', 'Quản lý Global State với Redux Toolkit và Zustand', 'Xử lý tối ưu Re-rendering với useMemo, useCallback', 'Kết nối Backend API và deploy lên Vercel/Netlify'],
                'requirements' => ['Nắm vững cú pháp JavaScript ES6+ (Async/Await, Destructuring...)'],
                'target_audience' => ['Frontend Developer muốn nâng cao trình độ React.js', 'Người chuyển ngành sang lập trình web'],
                'thumbnail' => 'https://images.unsplash.com/photo-1633356122544-f134324a6cee?w=700',
                'level' => 'intermediate',
                'price' => 890000,
                'sale_price' => 599000,
                'rating_avg' => 4.88,
                'rating_count' => 120,
                'enrollment_count' => 980,
                'tags' => ['react', 'javascript', 'frontend', 'redux', 'spa'],
                'is_featured' => true,
                'status' => 'published',
            ],
            [
                'title' => 'Lập trình Web Frontend Hiện Đại với React 18, Next.js 14 & TypeScript',
                'slug' => 'react-18-nextjs-14-typescript-masterclass',
                'instructor_email' => 'phuong.frontend@example.com',
                'category_name' => 'Phát triển Web',
                'description' => 'Khóa học chuyên sâu Next.js 14 App Router, React Server Components (RSC), Server Actions, tối ưu SEO vượt trội và tích hợp thanh toán trực tuyến PayOS.',
                'objectives' => ['Làm chủ Server Components và App Router trong Next.js 14', 'Áp dụng TypeScript nghiêm ngặt nâng cao chất lượng mã nguồn', 'Tối ưu Core Web Vitals và SEO on-page', 'Triển khai Authentication với NextAuth.js và JWT'],
                'requirements' => ['Đã biết lập trình React.js cơ bản'],
                'target_audience' => ['Frontend Developer muốn trở thành Fullstack Next.js Engineer'],
                'thumbnail' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=700',
                'level' => 'advanced',
                'price' => 1290000,
                'sale_price' => 890000,
                'rating_avg' => 4.95,
                'rating_count' => 95,
                'enrollment_count' => 740,
                'tags' => ['react', 'nextjs', 'typescript', 'frontend', 'seo'],
                'is_featured' => true,
                'status' => 'published',
            ],
            [
                'title' => 'Xây dựng Web Microservices với NestJS, Node.js & RabbitMQ',
                'slug' => 'microservices-nestjs-nodejs-rabbitmq',
                'instructor_email' => 'son.backend@example.com',
                'category_name' => 'Phát triển Web',
                'description' => 'Kiến trúc hệ thống Backend phân tán tải cao với NestJS, gRPC, RabbitMQ Message Queue, Redis In-Memory Cache và Docker Swarm chuẩn Enterprise.',
                'objectives' => ['Thiết kế kiến trúc hướng sự kiện Event-Driven Architecture', 'Giao tiếp liên dịch vụ qua gRPC và RabbitMQ', 'Triển khai Redis Cache và Database Sharding', 'Giám sát dịch vụ với Prometheus và Grafana'],
                'requirements' => ['Nắm vững JavaScript/TypeScript và Node.js cơ bản'],
                'target_audience' => ['Backend Developer muốn lên vị trí Senior/Architect'],
                'thumbnail' => 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=700',
                'level' => 'advanced',
                'price' => 1490000,
                'sale_price' => 990000,
                'rating_avg' => 4.92,
                'rating_count' => 78,
                'enrollment_count' => 520,
                'tags' => ['nestjs', 'nodejs', 'microservices', 'rabbitmq', 'docker'],
                'is_featured' => false,
                'status' => 'published',
            ],
            [
                'title' => 'Java Core Nâng Cao & Lập Trình Spring Boot 3 Chuẩn Doanh Nghiệp',
                'slug' => 'java-spring-boot-3-microservices-enterprise',
                'instructor_email' => 'son.backend@example.com',
                'category_name' => 'Ngôn ngữ lập trình',
                'description' => 'Học Java 21 LTS, Virtual Threads, Spring Boot 3, Spring Data JPA, Spring Security JWT và triển khai dự án ngân hàng mẫu quy mô lớn.',
                'objectives' => ['Nắm vững Virtual Threads và tính năng mới trong Java 21', 'Bảo mật API với Spring Security 6 & OAuth2', 'Thiết kế cơ sở dữ liệu với Spring Data JPA & Hibernate', 'Viết Unit Test và Integration Test với JUnit 5/Mockito'],
                'requirements' => ['Hiểu kiến thức lập trình hướng đối tượng (OOP) cơ bản'],
                'target_audience' => ['Java Developer muốn làm việc tại các tập đoàn tài chính/ngân hàng'],
                'thumbnail' => 'https://images.unsplash.com/photo-1537432376769-00f5c2f4c8d2?w=700',
                'level' => 'intermediate',
                'price' => 1390000,
                'sale_price' => 950000,
                'rating_avg' => 4.86,
                'rating_count' => 84,
                'enrollment_count' => 610,
                'tags' => ['java', 'springboot', 'spring', 'enterprise', 'backend'],
                'is_featured' => true,
                'status' => 'published',
            ],
            [
                'title' => 'Lập Trình Golang Nền Tảng & Xây Dựng High-Performance Microservices',
                'slug' => 'golang-high-performance-microservices',
                'instructor_email' => 'trung.golang@example.com',
                'category_name' => 'Ngôn ngữ lập trình',
                'description' => 'Làm chủ Concurrency với Goroutines & Channels, xây dựng Web Service siêu nhẹ với Gin Gonic, tối ưu bộ nhớ và benchmark xử lý hàng vạn RPS.',
                'objectives' => ['Làm chủ mô hình Concurrency đa luồng trong Golang', 'Viết RESTful API hiệu năng cao với Gin Framework', 'Testing và Profiling tối ưu hóa bộ nhớ RAM/CPU', 'Kết nối PostgreSQL và Redis Pooling chuẩn chỉnh'],
                'requirements' => ['Đã biết một ngôn ngữ lập trình bất kỳ (C++, Java, JS, Python...)'],
                'target_audience' => ['Backend Developer muốn tăng tốc hiệu năng hệ thống'],
                'thumbnail' => 'https://images.unsplash.com/photo-1515879218367-8466d910aaa4?w=700',
                'level' => 'intermediate',
                'price' => 1190000,
                'sale_price' => 790000,
                'rating_avg' => 4.91,
                'rating_count' => 62,
                'enrollment_count' => 430,
                'tags' => ['golang', 'go', 'concurrency', 'microservices', 'gin'],
                'is_featured' => false,
                'status' => 'published',
            ],

            // =========================================================================
            // 2. PHÁT TRIỂN ỨNG DỤNG MOBILE
            // =========================================================================
            [
                'title' => 'Lập trình di động Flutter & Dart từ cơ bản',
                'slug' => Str::slug('Lập trình di động Flutter Dart từ cơ bản'),
                'instructor_email' => 'minh.reactnative@example.com',
                'category_name' => 'Phát triển ứng dụng Mobile',
                'description' => 'Khóa học lập trình ứng dụng di động đa nền tảng iOS & Android với Flutter và ngôn ngữ Dart. Xây dựng giao diện mượt mà và kết nối API thời gian thực.',
                'objectives' => ['Làm chủ cú pháp Dart và hệ thống Widget trong Flutter', 'Quản lý State với Provider và Bloc Pattern', 'Kết nối REST API và lưu trữ Local Storage (Hive/SQLite)', 'Build và xuất file APK/AAB cho Android và IPA cho iOS'],
                'requirements' => ['Tư duy lập trình căn bản'],
                'target_audience' => ['Người muốn bắt đầu làm app mobile cho cả 2 hệ điều hành'],
                'thumbnail' => 'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?w=700',
                'level' => 'beginner',
                'price' => 790000,
                'sale_price' => 499000,
                'rating_avg' => 4.85,
                'rating_count' => 90,
                'enrollment_count' => 780,
                'tags' => ['flutter', 'dart', 'mobile', 'ios', 'android'],
                'is_featured' => true,
                'status' => 'published',
            ],
            [
                'title' => 'Lập trình Mobile Đa Nền Tảng với Flutter 3 & Clean Architecture',
                'slug' => 'flutter-3-clean-architecture-mastery',
                'instructor_email' => 'huy.flutter@example.com',
                'category_name' => 'Phát triển ứng dụng Mobile',
                'description' => 'Khóa học thực chiến xây dựng ứng dụng E-Commerce giao đồ ăn hoàn chỉnh với Flutter 3, BLoC Pattern, Clean Architecture, Firebase Notifications và PayOS.',
                'objectives' => ['Áp dụng Clean Architecture tách lớp Domain, Data, Presentation', 'Quản lý state phức tạp với Flutter BLoC', 'Tích hợp Push Notification và Realtime Database', 'Tối ưu hiệu năng rendering đạt chuẩn 60-120 FPS'],
                'requirements' => ['Đã biết lập trình Flutter cơ bản'],
                'target_audience' => ['Mobile Developer muốn nâng cấp tư duy thiết kế app lớn'],
                'thumbnail' => 'https://images.unsplash.com/photo-1526470608268-f674ce90ebd4?w=700',
                'level' => 'intermediate',
                'price' => 1190000,
                'sale_price' => 790000,
                'rating_avg' => 4.93,
                'rating_count' => 75,
                'enrollment_count' => 540,
                'tags' => ['flutter', 'bloc', 'clean-architecture', 'mobile', 'dart'],
                'is_featured' => true,
                'status' => 'published',
            ],
            [
                'title' => 'Phát triển Ứng dụng Di động với React Native, Expo & Supabase',
                'slug' => 'react-native-expo-supabase-mobile-apps',
                'instructor_email' => 'minh.reactnative@example.com',
                'category_name' => 'Phát triển ứng dụng Mobile',
                'description' => 'Tận dụng kiến thức JavaScript/React để tạo ứng dụng di động native với Expo SDK 50+, Supabase Backend, Tailwind React Native và React Navigation v6.',
                'objectives' => ['Sử dụng Expo Router xây dựng cấu trúc ứng dụng trực quan', 'Tích hợp Supabase làm Backend (Auth, Database, Storage, Realtime)', 'Truy cập Camera, GPS, Push Notifications trên thiết bị', 'Deploy ứng dụng qua Expo EAS Build lên App Store & Play Store'],
                'requirements' => ['Biết lập trình React.js hoặc JavaScript'],
                'target_audience' => ['Web Frontend Developer muốn lấn sân sang làm Mobile App'],
                'thumbnail' => 'https://images.unsplash.com/photo-1510519138197-06b8628cbf4f?w=700',
                'level' => 'intermediate',
                'price' => 990000,
                'sale_price' => 690000,
                'rating_avg' => 4.87,
                'rating_count' => 68,
                'enrollment_count' => 490,
                'tags' => ['reactnative', 'expo', 'supabase', 'javascript', 'mobile'],
                'is_featured' => false,
                'status' => 'published',
            ],

            // =========================================================================
            // 3. LẬP TRÌNH GAME
            // =========================================================================
            [
                'title' => 'Lập trình Game 2D/3D với Unity & C# từ Cơ bản đến Xuất bản Mobile',
                'slug' => 'unity-csharp-game-dev-2d-3d',
                'instructor_email' => 'hieu.game@example.com',
                'category_name' => 'Lập trình Game',
                'description' => 'Học làm game từ số 0 với Unity Engine và ngôn ngữ C#. Tự tay tạo ra 3 dự án game: Flappy Bird 2D, Endless Runner 3D và RPG Action Game.',
                'objectives' => ['Làm chủ Unity Editor, Physics Engine 2D/3D và Rigidbody', 'Viết mã logic gameplay, AI di chuyển và hệ thống điểm số với C#', 'Thiết kế Animation, Particle System và âm thanh sống động', 'Tích hợp quảng cáo AdMob và in-app purchase để kiếm tiền'],
                'requirements' => ['Máy tính cá nhân cấu hình tầm trung (Core i5, 8GB RAM trở lên)'],
                'target_audience' => ['Những bạn trẻ đam mê làm game và muốn trở thành Game Developer'],
                'thumbnail' => 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?w=700',
                'level' => 'beginner',
                'price' => 1190000,
                'sale_price' => 790000,
                'rating_avg' => 4.90,
                'rating_count' => 82,
                'enrollment_count' => 590,
                'tags' => ['unity', 'game', 'csharp', 'gamedev', '3d'],
                'is_featured' => true,
                'status' => 'published',
            ],

            // =========================================================================
            // 4. KHOA HỌC DỮ LIỆU & AI
            // =========================================================================
            [
                'title' => 'Python cho Phân tích dữ liệu & Machine Learning',
                'slug' => Str::slug('Python cho Phân tích dữ liệu Machine Learning'),
                'instructor_email' => 'tien.datascience@example.com',
                'category_name' => 'Khoa học dữ liệu',
                'description' => 'Trở thành Data Analyst chuyên nghiệp với Python, Pandas, NumPy, SQL và thuật toán Machine Learning. Xử lý tập dữ liệu thực tế và tạo báo cáo Insight giá trị.',
                'objectives' => ['Làm sạch và xử lý tập dữ liệu lớn với Pandas/NumPy', 'Trực quan hóa dữ liệu trực quan với Matplotlib và Seaborn', 'Xây dựng mô hình Machine Learning phân loại và dự báo', 'Áp dụng SQL truy vấn dữ liệu từ cơ sở dữ liệu quan hệ'],
                'requirements' => ['Không yêu cầu kinh nghiệm lập trình từ trước'],
                'target_audience' => ['Sinh viên các ngành kỹ thuật, kinh tế, nhân viên văn phòng'],
                'thumbnail' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=700',
                'level' => 'beginner',
                'price' => 890000,
                'sale_price' => 549000,
                'rating_avg' => 4.95,
                'rating_count' => 140,
                'enrollment_count' => 1150,
                'tags' => ['python', 'datascience', 'pandas', 'analytics', 'data'],
                'is_featured' => true,
                'status' => 'published',
            ],
            [
                'title' => 'Machine Learning & Deep Learning Thực Chiến với PyTorch & YOLOv8',
                'slug' => 'machine-learning-pytorch-yolov8-vision',
                'instructor_email' => 'tien.datascience@example.com',
                'category_name' => 'Trí tuệ nhân tạo và Machine Learning',
                'description' => 'Huấn luyện mô hình Thị giác máy tính (Computer Vision), nhận diện vật thể thời gian thực, phân đoạn ảnh và Fine-tuning mô hình AI tiên tiến.',
                'objectives' => ['Hiểu sâu cơ chế Mạng Neural tích chập (CNN)', 'Train mô hình Object Detection với YOLOv8', 'Deploy AI Model lên Web API với FastAPI và Docker', 'Xử lý dữ liệu hình ảnh với OpenCV chuyên sâu'],
                'requirements' => ['Biết ngôn ngữ Python và toán đại số tuyến tính cơ bản'],
                'target_audience' => ['Kỹ sư AI/ML, Nghiên cứu sinh, Lập trình viên muốn làm AI'],
                'thumbnail' => 'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=700',
                'level' => 'intermediate',
                'price' => 1490000,
                'sale_price' => 990000,
                'rating_avg' => 4.94,
                'rating_count' => 96,
                'enrollment_count' => 680,
                'tags' => ['ai', 'machinelearning', 'deeplearning', 'pytorch', 'computervision'],
                'is_featured' => true,
                'status' => 'published',
            ],
            [
                'title' => 'Lập Trình Ứng Dụng GenAI, RAG & LLM với LangChain & OpenAI API',
                'slug' => 'genai-rag-llm-langchain-openai-masterclass',
                'instructor_email' => 'tung.data@example.com',
                'category_name' => 'Trí tuệ nhân tạo và Machine Learning',
                'description' => 'Xây dựng trợ lý ảo thông minh (AI Chatbot), hệ thống hỏi đáp tài liệu doanh nghiệp RAG với Vector Database Milvus/Pinecone và LangChain.',
                'objectives' => ['Xây dựng ứng dụng hỏi đáp văn bản thông minh (RAG Pipeline)', 'Tích hợp Vector Database và Embeddings Model', 'Kỹ thuật Prompt Engineering chuẩn doanh nghiệp', 'Tạo Agent tự hành có khả năng gọi công cụ Tool Calling'],
                'requirements' => ['Có kinh nghiệm lập trình Python hoặc JavaScript'],
                'target_audience' => ['Software Engineer muốn tích hợp AI vào sản phẩm doanh nghiệp'],
                'thumbnail' => 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=700',
                'level' => 'intermediate',
                'price' => 1390000,
                'sale_price' => 920000,
                'rating_avg' => 4.96,
                'rating_count' => 110,
                'enrollment_count' => 840,
                'tags' => ['genai', 'llm', 'rag', 'langchain', 'openai'],
                'is_featured' => true,
                'status' => 'published',
            ],
            [
                'title' => 'Xử lý Dữ liệu Lớn với Apache Spark, PySpark & Kafka Streaming',
                'slug' => 'bigdata-apache-spark-pyspark-kafka',
                'instructor_email' => 'tung.data@example.com',
                'category_name' => 'Khoa học dữ liệu',
                'description' => 'Xây dựng Data Pipeline thời gian thực, xử lý Streaming Data quy mô Terabytes trên kiến trúc Delta Lake và Databricks.',
                'objectives' => ['Thiết kế kiến trúc Data Lakehouse hiện đại', 'Xử lý luồng sự kiện Real-time với Kafka và Spark Streaming', 'Tối ưu hóa Shuffle, Partitioning và Memory Spill trong Spark', 'Xây dựng quy trình ETL tự động hóa'],
                'requirements' => ['Biết lập trình Python và kiến thức cơ bản về SQL'],
                'target_audience' => ['Data Engineer, Backend Engineer muốn chuyển sang Big Data'],
                'thumbnail' => 'https://images.unsplash.com/photo-1504868584819-f8e8b4b6d7e3?w=700',
                'level' => 'advanced',
                'price' => 1690000,
                'sale_price' => 1190000,
                'rating_avg' => 4.88,
                'rating_count' => 54,
                'enrollment_count' => 380,
                'tags' => ['bigdata', 'spark', 'kafka', 'dataengineering', 'pyspark'],
                'is_featured' => false,
                'status' => 'published',
            ],

            // =========================================================================
            // 5. CƠ SỞ DỮ LIỆU & KIỂM THỬ
            // =========================================================================
            [
                'title' => 'Tối Ưu Hóa & Quản Trị CSDL MySQL & PostgreSQL Cho Hệ Thống Lớn',
                'slug' => 'mysql-postgresql-performance-tuning-indexing',
                'instructor_email' => 'quang.db@example.com',
                'category_name' => 'Cơ sở dữ liệu',
                'description' => 'Học kỹ thuật đánh Index (B-Tree, Hash, GIN), phân tích EXPLAIN Query Execution Plan, tối ưu hóa Slow Query, Partitioning và Master-Slave Replication.',
                'objectives' => ['Đọc hiểu và tối ưu Execution Plan trong MySQL & Postgres', 'Thiết kế chiến lược Indexing chính xác giảm tải 90% CPU', 'Thiết lập Replication và Failover tự động', 'Sao lưu dự phòng và khôi phục thảm họa (Disaster Recovery)'],
                'requirements' => ['Biết viết câu lệnh SQL SELECT/INSERT/UPDATE/JOIN cơ bản'],
                'target_audience' => ['Database Administrator (DBA), Backend Developer, System Engineer'],
                'thumbnail' => 'https://images.unsplash.com/photo-1544383835-bda2bc66a55d?w=700',
                'level' => 'advanced',
                'price' => 1290000,
                'sale_price' => 850000,
                'rating_avg' => 4.92,
                'rating_count' => 70,
                'enrollment_count' => 480,
                'tags' => ['database', 'mysql', 'postgresql', 'optimization', 'sql'],
                'is_featured' => true,
                'status' => 'published',
            ],
            [
                'title' => 'Thiết Kế CSDL NoSQL với MongoDB & Kiến Trúc Caching với Redis',
                'slug' => 'nosql-mongodb-redis-caching-architect',
                'instructor_email' => 'quang.db@example.com',
                'category_name' => 'Cơ sở dữ liệu',
                'description' => 'Mô hình hóa dữ liệu Document-based, Sharding, Replica Set với MongoDB và chiến lược Caching (Write-Through, Cache-Aside) với Redis.',
                'objectives' => ['Mô hình hóa dữ liệu linh hoạt trên MongoDB', 'Ứng dụng Redis làm Cache, Message Queue và Pub/Sub', 'Xử lý bài toán Cache Stampede và Cache Avalanche', 'Triển khai cụm Redis Cluster chịu tải cao'],
                'requirements' => ['Có hiểu biết cơ bản về cơ sở dữ liệu quan hệ'],
                'target_audience' => ['Backend Developer, DevOps Engineer'],
                'thumbnail' => 'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=700',
                'level' => 'intermediate',
                'price' => 990000,
                'sale_price' => 690000,
                'rating_avg' => 4.85,
                'rating_count' => 62,
                'enrollment_count' => 410,
                'tags' => ['nosql', 'mongodb', 'redis', 'caching', 'database'],
                'is_featured' => false,
                'status' => 'published',
            ],
            [
                'title' => 'Kiểm Thử Tự Động Toàn Diện với Playwright & TypeScript Thực Chiến',
                'slug' => 'automation-testing-playwright-typescript',
                'instructor_email' => 'thao.qa@example.com',
                'category_name' => 'Kiểm thử phần mềm',
                'description' => 'Học viết End-to-End Test (E2E), API Test, Visual Regression Testing với Playwright, Page Object Model (POM) và tích hợp CI/CD tự động.',
                'objectives' => ['Xây dựng Automation Framework với Playwright & TypeScript', 'Áp dụng Page Object Model (POM) và Data-Driven Testing', 'Chạy kiểm thử đa trình duyệt song song (Chromium, Firefox, WebKit)', 'Tích hợp Test Report vào GitHub Actions và GitLab CI'],
                'requirements' => ['Hiểu biết cơ bản về HTML/CSS và JavaScript/TypeScript'],
                'target_audience' => ['Manual Tester muốn chuyển sang Automation Tester, Software Developer'],
                'thumbnail' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=700',
                'level' => 'intermediate',
                'price' => 1090000,
                'sale_price' => 720000,
                'rating_avg' => 4.89,
                'rating_count' => 58,
                'enrollment_count' => 390,
                'tags' => ['testing', 'playwright', 'typescript', 'qa', 'automation'],
                'is_featured' => true,
                'status' => 'published',
            ],

            // =========================================================================
            // 6. CLOUD & DEVOPS & BẢO MẬT
            // =========================================================================
            [
                'title' => 'Chinh Phục Docker, Kubernetes (K8s) & Triển Khai Microservices Production',
                'slug' => 'docker-kubernetes-k8s-microservices-production',
                'instructor_email' => 'duc.cloud@example.com',
                'category_name' => 'DevOps',
                'description' => 'Làm chủ Containerization với Docker đa tầng (Multi-stage Build), điều phối cụm Kubernetes K8s, Helm Chart, Ingress Controller và Auto-scaling HPA.',
                'objectives' => ['Đóng gói ứng dụng tối ưu kích thước Image với Multi-stage Build', 'Quản lý Pods, Deployments, Services, ConfigMaps, Secrets trong K8s', 'Triển khai CI/CD tự động deploy lên K8s', 'Giám sát và ghi log với Prometheus, Grafana và Loki'],
                'requirements' => ['Biết lệnh Linux cơ bản và kiến thức mạng máy tính'],
                'target_audience' => ['DevOps Engineer, SysAdmin, Backend Developer'],
                'thumbnail' => 'https://images.unsplash.com/photo-1607799279861-4dd421887fb3?w=700',
                'level' => 'advanced',
                'price' => 1490000,
                'sale_price' => 990000,
                'rating_avg' => 4.95,
                'rating_count' => 105,
                'enrollment_count' => 820,
                'tags' => ['docker', 'kubernetes', 'k8s', 'devops', 'cloud'],
                'is_featured' => true,
                'status' => 'published',
            ],
            [
                'title' => 'AWS Certified Solutions Architect Associate (SAA-C03) Thực Chiến',
                'slug' => 'aws-certified-solutions-architect-associate-pro',
                'instructor_email' => 'duc.cloud@example.com',
                'category_name' => 'Điện toán đám mây',
                'description' => 'Luyện thi chứng chỉ quốc tế AWS SAA-C03 và thực hành thiết kế kiến trúc đám mây có độ sẵn sàng cao, chịu lỗi tốt (High Availability & Fault Tolerant).',
                'objectives' => ['Làm chủ các dịch vụ cốt lõi: EC2, VPC, S3, RDS, Lambda, CloudFront', 'Thiết kế hệ thống đa vùng (Multi-AZ) và Auto Scaling', 'Bảo mật hạ tầng đám mây với IAM, KMS, Security Groups', 'Tự tin vượt qua kỳ thi chứng chỉ quốc tế AWS SAA-C03'],
                'requirements' => ['Có hiểu biết cơ bản về hạ tầng CNTT và mạng máy tính'],
                'target_audience' => ['Kỹ sư muốn lấy chứng chỉ AWS và làm việc với Cloud Computing'],
                'thumbnail' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=700',
                'level' => 'intermediate',
                'price' => 1690000,
                'sale_price' => 1190000,
                'rating_avg' => 4.93,
                'rating_count' => 88,
                'enrollment_count' => 690,
                'tags' => ['aws', 'cloud', 'solutionsarchitect', 'devops', 'cert'],
                'is_featured' => true,
                'status' => 'published',
            ],
            [
                'title' => 'An Toàn Thông Tin Web & Phòng Chống Top 10 Lỗ Hổng OWASP',
                'slug' => 'web-security-owasp-top-10-defense',
                'instructor_email' => 'anh.security@example.com',
                'category_name' => 'An ninh mạng',
                'description' => 'Tìm hiểu cơ chế tấn công và phương pháp phòng thủ vững chắc cho 10 lỗ hổng bảo mật phổ biến nhất: SQL Injection, XSS, CSRF, SSRF, Broken Auth, v.v.',
                'objectives' => ['Nhận diện và khai thác thử nghiệm các lỗ hổng OWASP Top 10', 'Viết mã an toàn (Secure Coding) cho Backend và Frontend', 'Cấu hình HTTP Security Headers và WAF bảo vệ máy chủ', 'Kiểm tra rà soát an toàn thông tin (Security Code Review)'],
                'requirements' => ['Hiểu kiến thức phát triển Web (HTTP, Cookie, Session, SQL)'],
                'target_audience' => ['Web Developer, Security Analyst, DevOps Engineer'],
                'thumbnail' => 'https://images.unsplash.com/photo-1563089145-599997674d42?w=700',
                'level' => 'intermediate',
                'price' => 1290000,
                'sale_price' => 890000,
                'rating_avg' => 4.91,
                'rating_count' => 74,
                'enrollment_count' => 510,
                'tags' => ['security', 'owasp', 'cybersecurity', 'hacking', 'websecurity'],
                'is_featured' => false,
                'status' => 'published',
            ],

            // =========================================================================
            // 7. THIẾT KẾ UI/UX & ĐỒ HỌA & VIDEO
            // =========================================================================
            [
                'title' => 'Figma Prototype & Design System',
                'slug' => Str::slug('Figma Prototype Design System'),
                'instructor_email' => 'instructor2@example.com',
                'category_name' => 'Thiết kế UI/UX',
                'description' => 'Khóa học thiết kế giao diện UI/UX chuyên nghiệp với Figma. Xây dựng Design System chuẩn chỉnh, Auto Layout, Interactive Prototype và bàn giao cho Dev.',
                'objectives' => ['Sử dụng thành thạo Figma, Auto Layout, Variants và Variables', 'Thiết kế Wireframe, UI Kit và Design System quy mô lớn', 'Tạo Interactive Prototype sống động mô phỏng trải nghiệm thật', 'Quy trình bàn giao thiết kế (Hand-off) mượt mà cho lập trình viên'],
                'requirements' => ['Không yêu cầu kiến thức đồ họa từ trước'],
                'target_audience' => ['Người mới bắt đầu muốn trở thành UI/UX Designer chuyên nghiệp'],
                'thumbnail' => 'https://images.unsplash.com/photo-1581291518857-4e27b48ff24e?w=700',
                'level' => 'beginner',
                'price' => 690000,
                'sale_price' => 450000,
                'rating_avg' => 4.92,
                'rating_count' => 115,
                'enrollment_count' => 920,
                'tags' => ['figma', 'uiux', 'design', 'prototype', 'designsystem'],
                'is_featured' => true,
                'status' => 'published',
            ],
            [
                'title' => 'Nghiên Cứu Người Dùng (UX Research) & Thiết Kế Trải Nghiệm Khách Hàng',
                'slug' => 'ux-research-user-experience-architecture',
                'instructor_email' => 'nam.uiux@example.com',
                'category_name' => 'Thiết kế UI/UX',
                'description' => 'Học phương pháp phỏng vấn người dùng, xây dựng User Persona, Customer Journey Map, Information Architecture và Usability Testing thực tế.',
                'objectives' => ['Thực hiện nghiên cứu định tính và định lượng người dùng', 'Xây dựng bản đồ hành trình khách hàng (Journey Mapping)', 'Kiểm thử khả năng sử dụng (Usability Testing) và đo lường SUS score', 'Thiết kế luồng trải nghiệm tối ưu tỷ lệ chuyển đổi (CRO)'],
                'requirements' => ['Đam mê thấu hiểu hành vi người dùng'],
                'target_audience' => ['Product Manager, UX Designer, Business Analyst'],
                'thumbnail' => 'https://images.unsplash.com/photo-1586717791821-3f44a563fa4c?w=700',
                'level' => 'beginner',
                'price' => 790000,
                'sale_price' => 490000,
                'rating_avg' => 4.88,
                'rating_count' => 60,
                'enrollment_count' => 420,
                'tags' => ['ux', 'research', 'userexperience', 'product', 'design'],
                'is_featured' => false,
                'status' => 'published',
            ],
            [
                'title' => 'Adobe Photoshop & Illustrator Thực Chiến Cho Người Mới Bắt Đầu',
                'slug' => 'photoshop-illustrator-graphic-design-mastery',
                'instructor_email' => 'instructor2@example.com',
                'category_name' => 'Adobe Photoshop',
                'description' => 'Làm chủ 2 phần mềm đồ họa quyền lực nhất thế giới. Thiết kế Banner quảng cáo, Poster, Logo thương hiệu và ấn phẩm truyền thông chuyên nghiệp.',
                'objectives' => ['Thành thạo công cụ cắt ghép ảnh, chỉnh sửa màu sắc trong Photoshop', 'Vẽ vector, thiết kế Logo và biểu tượng thương hiệu trong Illustrator', 'Nguyên lý bố cục, phối màu và nghệ thuật Typography ấn tượng', 'Xuất file in ấn và digital đúng tiêu chuẩn màu CMYK/RGB'],
                'requirements' => ['Máy tính cá nhân có cài đặt Adobe Photoshop & Illustrator'],
                'target_audience' => ['Sinh viên, Marketer, người làm sáng tạo nội dung'],
                'thumbnail' => 'https://images.unsplash.com/photo-1626785774573-4b799315345d?w=700',
                'level' => 'beginner',
                'price' => 690000,
                'sale_price' => 420000,
                'rating_avg' => 4.86,
                'rating_count' => 92,
                'enrollment_count' => 710,
                'tags' => ['photoshop', 'illustrator', 'graphicdesign', 'adobe', 'art'],
                'is_featured' => true,
                'status' => 'published',
            ],
            [
                'title' => 'Dựng Phim & Kỹ Xảo Video với Adobe Premiere Pro & After Effects',
                'slug' => 'premiere-pro-after-effects-video-editing',
                'instructor_email' => 'van.media@example.com',
                'category_name' => 'Dựng và chỉnh sửa video',
                'description' => 'Khóa học dựng video toàn diện từ cắt ghép cơ bản, chỉnh màu phim (Color Grading), xử lý âm thanh đến tạo hiệu ứng Motion Graphics hút mắt.',
                'objectives' => ['Dựng video chuyên nghiệp với Adobe Premiere Pro', 'Tạo hoạt họa chữ (Typography) và hiệu ứng VFX với After Effects', 'Chỉnh màu chuẩn điện ảnh với Lumetri Color', 'Sản xuất video ngắn TikTok, Reels, YouTube Shorts triệu view'],
                'requirements' => ['Máy tính cá nhân có card đồ họa rời (tối thiểu 4GB VRAM)'],
                'target_audience' => ['Content Creator, Youtuber, Video Editor muốn nâng cao tay nghề'],
                'thumbnail' => 'https://images.unsplash.com/photo-1574717024653-61fd2cf4d44d?w=700',
                'level' => 'intermediate',
                'price' => 990000,
                'sale_price' => 650000,
                'rating_avg' => 4.93,
                'rating_count' => 85,
                'enrollment_count' => 630,
                'tags' => ['premiere', 'aftereffects', 'videoediting', 'motiongraphics', 'vfx'],
                'is_featured' => true,
                'status' => 'published',
            ],

            // =========================================================================
            // 8. MARKETING & KINH DOANH & THƯƠNG MẠI ĐIỆN TỬ
            // =========================================================================
            [
                'title' => 'Digital Marketing & SEO Performance Mastery',
                'slug' => Str::slug('Digital Marketing SEO Performance Mastery'),
                'instructor_email' => 'long.marketing@example.com',
                'category_name' => 'Digital Marketing',
                'description' => 'Xây dựng chiến lược Digital Marketing đa kênh, tối ưu hóa SEO Google và chạy quảng cáo Facebook/TikTok Ads chuyển đổi cao với chi phí tối ưu.',
                'objectives' => ['Nghiên cứu từ khóa và xây dựng cấu trúc website chuẩn SEO', 'Chạy chiến dịch Facebook Ads và TikTok Ads chuyển đổi cao', 'Đo lường hiệu quả chiến dịch với Google Analytics 4 (GA4)', 'Tối ưu hóa phễu bán hàng và chi phí chuyển đổi (CPA)'],
                'requirements' => ['Có đam mê với kinh doanh và marketing online'],
                'target_audience' => ['Chủ doanh nghiệp nhỏ, Marketer, Freelancer'],
                'thumbnail' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=700',
                'level' => 'beginner',
                'price' => 790000,
                'sale_price' => 499000,
                'rating_avg' => 4.88,
                'rating_count' => 110,
                'enrollment_count' => 940,
                'tags' => ['marketing', 'seo', 'facebookads', 'tiktokads', 'digital'],
                'is_featured' => true,
                'status' => 'published',
            ],
            [
                'title' => 'SEO Google Masterclass: Đẩy Top 1 Từ Khóa Bền Vững & Tăng Trưởng Traffic',
                'slug' => 'seo-google-masterclass-top-rankings',
                'instructor_email' => 'khang.seo@example.com',
                'category_name' => 'SEO',
                'description' => 'Bí quyết On-page SEO, Technical SEO, Entity SEO và chiến lược xây dựng Backlink chất lượng giúp website thống trị trang nhất Google bền vững.',
                'objectives' => ['Nghiên cứu từ khóa chiều sâu và phân nhóm Search Intent', 'Tối ưu Technical SEO: tốc độ trang, Schema Markup, Sitemap', 'Xây dựng chiến lược Content Pillar và Topic Cluster', 'Đo lường và khắc phục sự cố rớt hạng thuật toán Google'],
                'requirements' => ['Có website WordPress hoặc blog cá nhân để thực hành'],
                'target_audience' => ['SEO Specialist, Content Creator, Web Master'],
                'thumbnail' => 'https://images.unsplash.com/photo-1571786256017-aee7a0c009b6?w=700',
                'level' => 'intermediate',
                'price' => 890000,
                'sale_price' => 590000,
                'rating_avg' => 4.91,
                'rating_count' => 78,
                'enrollment_count' => 560,
                'tags' => ['seo', 'google', 'content', 'traffic', 'digitalmarketing'],
                'is_featured' => false,
                'status' => 'published',
            ],
            [
                'title' => 'Xây Dựng và Vận Hành Gian Hàng Thương Mại Điện Tử Triệu Đơn Hàng',
                'slug' => 'ecommerce-shopee-tiktok-shop-scale',
                'instructor_email' => 'cuong.ecommerce@example.com',
                'category_name' => 'Thương mại điện tử',
                'description' => 'Chiến lược kinh doanh bùng nổ doanh số trên Shopee và TikTok Shop. Quy trình chọn sản phẩm win, tối ưu SEO gian hàng, livestream và quản lý kho vận.',
                'objectives' => ['Nghiên cứu và lựa chọn sản phẩm ngách có biên lợi nhuận cao', 'Tối ưu hình ảnh, tiêu đề và SEO gian hàng Shopee Mall', 'Chiến thuật Livestream bán hàng và hợp tác với KOC/KOL', 'Quản trị dòng tiền, đóng gói vận hành và chăm sóc khách hàng'],
                'requirements' => ['Mong muốn kinh doanh trực tuyến'],
                'target_audience' => ['Nhà bán hàng Online, Chủ shop thời trang, mỹ phẩm, gia dụng'],
                'thumbnail' => 'https://images.unsplash.com/photo-1556742049-0a67e5572293?w=700',
                'level' => 'beginner',
                'price' => 890000,
                'sale_price' => 590000,
                'rating_avg' => 4.90,
                'rating_count' => 95,
                'enrollment_count' => 830,
                'tags' => ['ecommerce', 'shopee', 'tiktokshop', 'livestream', 'business'],
                'is_featured' => true,
                'status' => 'published',
            ],
            [
                'title' => 'Quản Trị Dự Án Linh Hoạt với Agile Scrum & Công Cụ Jira/Trello',
                'slug' => 'agile-scrum-project-management-jira',
                'instructor_email' => 'khanh.business@example.com',
                'category_name' => 'Quản lý dự án',
                'description' => 'Trang bị tư duy Agile, phương pháp Scrum thực chiến và kỹ năng vận hành công cụ quản lý dự án Jira, Trello nâng cao hiệu suất làm việc đội ngũ.',
                'objectives' => ['Hiểu sâu các nghi thức Scrum: Sprint Planning, Daily, Review, Retro', 'Quản lý Product Backlog và ước lượng Story Points chính xác', 'Vận hành bảng Kanban và Jira Software chuyên nghiệp', 'Giải quyết xung đột và thúc đẩy tinh thần tự chủ trong đội nhóm'],
                'requirements' => ['Không yêu cầu kiến thức chuyên ngành'],
                'target_audience' => ['Project Manager, Scrum Master, Team Leader, Tech Lead'],
                'thumbnail' => 'https://images.unsplash.com/photo-1531403009284-440f080d1e12?w=700',
                'level' => 'beginner',
                'price' => 790000,
                'sale_price' => 490000,
                'rating_avg' => 4.87,
                'rating_count' => 64,
                'enrollment_count' => 450,
                'tags' => ['agile', 'scrum', 'jira', 'projectmanagement', 'leadership'],
                'is_featured' => false,
                'status' => 'published',
            ],

            // =========================================================================
            // 9. TÀI CHÍNH & KẾ TOÁN & TIN HỌC VĂN PHÒNG
            // =========================================================================
            [
                'title' => 'Phân Tích Báo Cáo Tài Chính Doanh Nghiệp & Định Giá Cổ Phiếu',
                'slug' => 'financial-statement-analysis-stock-valuation',
                'instructor_email' => 'mai.finance@example.com',
                'category_name' => 'Phân tích tài chính',
                'description' => 'Đọc vị Bảng cân đối kế toán, Báo cáo kết quả kinh doanh và Lưu chuyển tiền tệ. Phát hiện thủ thuật làm đẹp số liệu và định giá doanh nghiệp chuẩn xác.',
                'objectives' => ['Đọc hiểu và phân tích 3 báo cáo tài chính cốt lõi', 'Tính toán và đánh giá các chỉ số tài chính (ROA, ROE, P/E, P/B, Debt/Equity)', 'Nhận diện rủi ro tài chính và gian lận báo cáo sổ sách', 'Áp dụng các phương pháp định giá DCF, P/E để tìm cổ phiếu định giá rẻ'],
                'requirements' => ['Yêu thích tìm hiểu về tài chính và đầu tư'],
                'target_audience' => ['Nhà đầu tư cá nhân, Chuyên viên phân tích tài chính, Sinh viên'],
                'thumbnail' => 'https://images.unsplash.com/photo-1590283603385-17ffb3a7f29f?w=700',
                'level' => 'intermediate',
                'price' => 1190000,
                'sale_price' => 790000,
                'rating_avg' => 4.94,
                'rating_count' => 88,
                'enrollment_count' => 670,
                'tags' => ['finance', 'stock', 'valuation', 'accounting', 'investing'],
                'is_featured' => true,
                'status' => 'published',
            ],
            [
                'title' => 'Power BI Master: Xây Dựng Báo Cáo & Dashboard Dữ Liệu Tự Động Hóa',
                'slug' => 'power-bi-automated-business-dashboard',
                'instructor_email' => 'lan.office@example.com',
                'category_name' => 'Power BI',
                'description' => 'Biến dữ liệu thô Excel/SQL thành các Dashboard trực quan tương tác sống động với Power BI, ngôn ngữ DAX và Power Query tự động hóa 100%.',
                'objectives' => ['Làm sạch và biến đổi dữ liệu với Power Query Editor', 'Xây dựng Data Model chuẩn Star Schema tối ưu hiệu năng', 'Viết hàm DAX phân tích chuyên sâu (CALCULATE, Time Intelligence)', 'Thiết kế Dashboard báo cáo điều hành chuyên nghiệp cho Ban Giám Đốc'],
                'requirements' => ['Biết sử dụng Microsoft Excel căn bản'],
                'target_audience' => ['Kế toán, Nhân sự, Sales/Marketing, Data Analyst'],
                'thumbnail' => 'https://images.unsplash.com/photo-1551836022-d5d88e9218df?w=700',
                'level' => 'beginner',
                'price' => 890000,
                'sale_price' => 550000,
                'rating_avg' => 4.92,
                'rating_count' => 102,
                'enrollment_count' => 860,
                'tags' => ['powerbi', 'dax', 'dashboard', 'analytics', 'excel'],
                'is_featured' => true,
                'status' => 'published',
            ],
            [
                'title' => 'Microsoft Excel Nâng Cao: Macro, VBA & Tự Động Hóa Xử Lý Bảng Tính',
                'slug' => 'advanced-excel-vba-macros-automation',
                'instructor_email' => 'lan.office@example.com',
                'category_name' => 'Microsoft Excel',
                'description' => 'Học các hàm nâng cao (XLOOKUP, INDEX/MATCH, SUMIFS), Pivot Table động và lập trình VBA Macro để tự động hóa mọi công việc bảng tính lặp đi lặp lại.',
                'objectives' => ['Làm chủ các hàm tính toán phức tạp và tổ chức bảng tính khoa học', 'Phân tích dữ liệu đa chiều với Dynamic Pivot Table & Pivot Chart', 'Lập trình VBA tự động tạo và gửi email báo cáo hàng ngày', 'Tăng tốc 300% hiệu suất làm việc văn phòng'],
                'requirements' => ['Đã biết nhập liệu Excel cơ bản'],
                'target_audience' => ['Nhân viên văn phòng, Kế toán, Chuyên viên phân tích'],
                'thumbnail' => 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?w=700',
                'level' => 'intermediate',
                'price' => 690000,
                'sale_price' => 420000,
                'rating_avg' => 4.89,
                'rating_count' => 76,
                'enrollment_count' => 590,
                'tags' => ['excel', 'vba', 'macros', 'office', 'productivity'],
                'is_featured' => false,
                'status' => 'published',
            ],

            // =========================================================================
            // 10. NGOẠI NGỮ & KỸ NĂNG MỀM
            // =========================================================================
            [
                'title' => 'Luyện thi IELTS 7.0+ 4 Kỹ năng & Speaking Practical',
                'slug' => Str::slug('Luyen thi IELTS 7 0 4 Ky nang Speaking Practical'),
                'instructor_email' => 'tu.ielts@example.com',
                'category_name' => 'Luyện thi',
                'description' => 'Bứt phá band điểm IELTS từ 5.0 lên 7.0+ với chiến thuật làm bài Listening, Reading, Writing Task 1-2 và Speaking phản xạ tự nhiên chuẩn bản xứ.',
                'objectives' => ['Nắm vững tư duy phản biện trong Writing Task 2 đạt 7.0+', 'Luyện phản xạ Speaking tự nhiên với hệ thống từ vựng Band 8.0', 'Kỹ thuật Scanning/Skimming đọc hiểu bài Reading siêu tốc', 'Chiến thuật bắt Keyword trong Listening tránh bẫy đề thi'],
                'requirements' => ['Trình độ tiếng Anh tương đương 4.5 - 5.0 IELTS'],
                'target_audience' => ['Học sinh, sinh viên chuẩn bị du học hoặc xin việc làm quốc tế'],
                'thumbnail' => 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=700',
                'level' => 'intermediate',
                'price' => 1290000,
                'sale_price' => 890000,
                'rating_avg' => 4.96,
                'rating_count' => 155,
                'enrollment_count' => 1320,
                'tags' => ['ielts', 'english', 'speaking', 'writing', 'testprep'],
                'is_featured' => true,
                'status' => 'published',
            ],
            [
                'title' => 'Tiếng Anh Giao Tiếp Công Sở Quốc Tế, Đàm Phán & Thuyết Trình Chuyên Nghiệp',
                'slug' => 'business-english-communication-negotiation',
                'instructor_email' => 'hoa.english@example.com',
                'category_name' => 'Ngoại ngữ',
                'description' => 'Tự tin làm việc trong môi trường đa quốc gia với các mẫu câu giao tiếp công sở chuẩn mực, kỹ năng viết Email chuyên nghiệp và đàm phán hợp đồng.',
                'objectives' => ['Phát âm chuẩn ngữ điệu và giao tiếp lưu loát trong công việc', 'Viết Email kinh doanh súc tích, lịch sự và chuyên nghiệp', 'Kỹ năng thuyết trình dự án bằng tiếng Anh tự tin trước đối tác', 'Xử lý các tình huống đàm phán và giải quyết xung đột bằng tiếng Anh'],
                'requirements' => ['Biết từ vựng tiếng Anh căn bản'],
                'target_audience' => ['Người đi làm muốn thăng tiến tại các tập đoàn nước ngoài'],
                'thumbnail' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=700',
                'level' => 'beginner',
                'price' => 790000,
                'sale_price' => 490000,
                'rating_avg' => 4.90,
                'rating_count' => 86,
                'enrollment_count' => 740,
                'tags' => ['businessenglish', 'english', 'communication', 'career', 'speaking'],
                'is_featured' => true,
                'status' => 'published',
            ],
            [
                'title' => 'Nghệ Thuật Thuyết Trình Đỉnh Cao & Truyền Cảm Hứng Cho Lãnh Đạo',
                'slug' => 'public-speaking-inspirational-leadership',
                'instructor_email' => 'yen.hr@example.com',
                'category_name' => 'Kỹ năng lãnh đạo',
                'description' => 'Vượt qua nỗi sợ đứng trước đám đông, làm chủ ngôn ngữ cơ thể, giọng nói truyền cảm và kỹ thuật Storytelling thuyết phục người nghe hoàn toàn.',
                'objectives' => ['Làm chủ tâm lý và phong thái tự tin khi đứng trước đám đông', 'Áp dụng cấu trúc bài thuyết trình thu hút từ 30 giây đầu tiên', 'Sử dụng ngôn ngữ hình thể, ánh mắt và điều tiết giọng nói có lực', 'Nghệ thuật kể chuyện (Storytelling) chạm tới cảm xúc khán giả'],
                'requirements' => ['Mong muốn hoàn thiện kỹ năng giao tiếp và lãnh đạo'],
                'target_audience' => ['Quản lý, Trưởng phòng, Giảng viên, Sinh viên'],
                'thumbnail' => 'https://images.unsplash.com/photo-1475721027785-f74eccf877e2?w=700',
                'level' => 'beginner',
                'price' => 690000,
                'sale_price' => 450000,
                'rating_avg' => 4.92,
                'rating_count' => 70,
                'enrollment_count' => 530,
                'tags' => ['publicspeaking', 'presentation', 'leadership', 'communication', 'softskills'],
                'is_featured' => false,
                'status' => 'published',
            ],
            [
                'title' => 'Quản Trị Thời Gian & Thiết Lập Hệ Thống Năng Suất Cá Nhân Cá Biệt',
                'slug' => 'time-management-personal-productivity-system',
                'instructor_email' => 'yen.hr@example.com',
                'category_name' => 'Quản lý thời gian',
                'description' => 'Xây dựng hệ thống quản lý công việc theo phương pháp GTD (Getting Things Done), Ma trận Eisenhower, Time Blocking và ứng dụng công cụ Notion thông minh.',
                'objectives' => ['Loại bỏ thói quen trì hoãn và phân tán tư tưởng khi làm việc', 'Thiết lập mục tiêu theo nguyên tắc OKRs và SMART', 'Tổ chức không gian số và quản lý tác vụ với Notion/Todoist', 'Cân bằng giữa công việc, học tập và cuộc sống cá nhân'],
                'requirements' => ['Sẵn sàng thay đổi thói quen sinh hoạt'],
                'target_audience' => ['Tất cả những ai muốn nâng cao hiệu suất và chất lượng cuộc sống'],
                'thumbnail' => 'https://images.unsplash.com/photo-1506784365847-bbad939e9335?w=700',
                'level' => 'beginner',
                'price' => 590000,
                'sale_price' => 390000,
                'rating_avg' => 4.88,
                'rating_count' => 55,
                'enrollment_count' => 410,
                'tags' => ['timemanagement', 'productivity', 'notion', 'gtd', 'focus'],
                'is_featured' => false,
                'status' => 'published',
            ],
        ];
    }
}
