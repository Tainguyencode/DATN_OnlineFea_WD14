<?php

namespace Database\Seeders\Demo;

use App\Models\Course;
use App\Models\LearningPath;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DemoLearningPathSeeder
{
    private array $pathsDefinition = [
        [
            'title' => 'Lộ Trình Trở Thành Senior Backend PHP & Laravel Architect',
            'slug' => 'demo-lo-trinh-backend-php-laravel-architect',
            'description' => 'Lộ trình từ cơ bản đến chuyên sâu trang bị kiến thức lập trình Web PHP 8.3, Laravel 11, Microservices, Caching Redis, Tối ưu MySQL và triển khai CI/CD chuẩn Enterprise.',
            'level' => 'intermediate',
            'target_role' => 'Senior PHP / Laravel Backend Engineer',
            'salary_range' => '20.000.000đ - 45.000.000đ/tháng',
            'estimated_duration' => '6 - 9 tháng',
            'thumbnail' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=600',
            'skills' => ['PHP 8.3', 'Laravel 11', 'MySQL Optimization', 'Redis', 'Docker', 'RESTful API', 'Microservices'],
            'is_featured' => true,
            'stages' => [
                'Giai đoạn 1: Nền tảng Web & Lập trình PHP/Laravel' => ['demo-laravel-11-vuejs-fullstack-ecommerce'],
                'Giai đoạn 2: Tối ưu Cơ sở dữ liệu & In-Memory Caching' => ['demo-database-optimization-mysql-postgresql', 'demo-nosql-mongodb-redis-caching'],
                'Giai đoạn 3: Kiến trúc Microservices & Triển khai Production' => ['demo-nestjs-microservices-nodejs-docker', 'demo-docker-kubernetes-production-mastery'],
            ],
        ],
        [
            'title' => 'Lộ Trình Kỹ Sư Frontend Web Chuyên Nghiệp với React, Next.js & TypeScript',
            'slug' => 'demo-lo-trinh-frontend-react-nextjs-typescript',
            'description' => 'Chinh phục thế giới giao diện hiện đại với React 18, Next.js 14 App Router, TypeScript Pro, Tailwind CSS, UI/UX Design System và tối ưu hiệu năng Lighthouse 100.',
            'level' => 'intermediate',
            'target_role' => 'Senior Frontend Developer',
            'salary_range' => '18.000.000đ - 40.000.000đ/tháng',
            'estimated_duration' => '5 - 8 tháng',
            'thumbnail' => 'https://images.unsplash.com/photo-1633356122544-f134324a6cee?w=600',
            'skills' => ['React 18', 'Next.js 14', 'TypeScript', 'Tailwind CSS', 'Figma to Code', 'State Management'],
            'is_featured' => true,
            'stages' => [
                'Giai đoạn 1: Tư duy Thiết kế UI/UX & Component' => ['demo-figma-ui-ux-design-masterclass'],
                'Giai đoạn 2: Lập trình Frontend Hiện Đại' => ['demo-react-18-nextjs-typescript-masterclass'],
                'Giai đoạn 3: Kiểm thử Giao diện & Tự Động Hóa' => ['demo-automation-testing-playwright-typescript'],
            ],
        ],
        [
            'title' => 'Lộ Trình Kỹ Sư Lập Trình Ứng Dụng Di Động Toàn Diện (Flutter & iOS)',
            'slug' => 'demo-lo-trinh-mobile-app-developer-flutter-ios',
            'description' => 'Học phát triển ứng dụng di động đa nền tảng Flutter và Native iOS Swift từ số 0 đến xuất bản lên Google Play và Apple App Store.',
            'level' => 'intermediate',
            'target_role' => 'Mobile App Developer (Flutter / iOS)',
            'salary_range' => '18.000.000đ - 38.000.000đ/tháng',
            'estimated_duration' => '6 - 9 tháng',
            'thumbnail' => 'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?w=600',
            'skills' => ['Flutter 3', 'Dart', 'BLoC Pattern', 'Swift 5', 'SwiftUI', 'App Store Publishing'],
            'is_featured' => true,
            'stages' => [
                'Giai đoạn 1: Lập trình Đa nền tảng Flutter' => ['demo-flutter-3-bloc-pattern-mastery'],
                'Giai đoạn 2: Lập trình Chuyên sâu iOS Native' => ['demo-ios-native-swift-swiftui-pro'],
                'Giai đoạn 3: Tích hợp API & Triển khai Backend Mobile' => ['demo-golang-high-performance-microservices'],
            ],
        ],
        [
            'title' => 'Lộ Trình Kỹ Sư Trí Tuệ Nhân Tạo, GenAI & Khoa Học Dữ Liệu',
            'slug' => 'demo-lo-trinh-ai-data-science-machine-learning',
            'description' => 'Lộ trình toàn diện từ phân tích dữ liệu Python Pandas, xây dựng mô hình Deep Learning PyTorch đến ứng dụng GenAI LLM RAG thực chiến.',
            'level' => 'intermediate',
            'target_role' => 'AI Engineer / Data Scientist',
            'salary_range' => '25.000.000đ - 60.000.000đ/tháng',
            'estimated_duration' => '8 - 12 tháng',
            'thumbnail' => 'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=600',
            'skills' => ['Python', 'Pandas', 'PyTorch', 'Computer Vision', 'LangChain', 'LLM & RAG', 'Apache Spark'],
            'is_featured' => true,
            'stages' => [
                'Giai đoạn 1: Nền tảng Xử lý & Phân tích Dữ liệu' => ['demo-python-data-science-pandas-analytics'],
                'Giai đoạn 2: Machine Learning & Deep Learning' => ['demo-machine-learning-pytorch-yolov8', 'demo-bigdata-apache-spark-pyspark-kafka'],
                'Giai đoạn 3: Generative AI & Ứng dụng LLM Doanh Nghiệp' => ['demo-genai-rag-llm-langchain-openai'],
            ],
        ],
        [
            'title' => 'Lộ Trình Chuyên Gia DevOps & Kỹ Sư Điện Toán Đám Mây AWS',
            'slug' => 'demo-lo-trinh-devops-cloud-engineer-aws',
            'description' => 'Trang bị trọn bộ kỹ năng Containerization, Kubernetes, AWS Cloud Architecture, CI/CD GitHub Actions, Giám sát hệ thống và bảo mật đám mây.',
            'level' => 'intermediate',
            'target_role' => 'DevOps Engineer / Cloud Solutions Architect',
            'salary_range' => '22.000.000đ - 55.000.000đ/tháng',
            'estimated_duration' => '6 - 10 tháng',
            'thumbnail' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=600',
            'skills' => ['AWS Solutions Architect', 'Docker', 'Kubernetes', 'CI/CD', 'Linux', 'Microservices'],
            'is_featured' => true,
            'stages' => [
                'Giai đoạn 1: Điện toán Đám mây AWS Căn bản đến Nâng cao' => ['demo-aws-solutions-architect-associate'],
                'Giai đoạn 2: Container & Điều phối Kubernetes' => ['demo-docker-kubernetes-production-mastery'],
                'Giai đoạn 3: Giám sát An toàn & Vận hành Hạ tầng' => ['demo-soc-blue-team-security-operations'],
            ],
        ],
        [
            'title' => 'Lộ Trình Chuyên Gia An Toàn Thông Tin & Kiểm Thử Bảo Mật (Pentesting)',
            'slug' => 'demo-lo-trinh-cybersecurity-penetration-testing',
            'description' => 'Đào tạo kỹ năng phòng thủ và tấn công thử nghiệm (Red Team / Blue Team), phát hiện lỗ hổng OWASP Top 10, quản trị SIEM và ứng phó sự cố.',
            'level' => 'intermediate',
            'target_role' => 'Cybersecurity Analyst / Penetration Tester',
            'salary_range' => '20.000.000đ - 50.000.000đ/tháng',
            'estimated_duration' => '6 - 9 tháng',
            'thumbnail' => 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?w=600',
            'skills' => ['OWASP Top 10', 'Burp Suite', 'Kali Linux', 'SIEM Wazuh/Splunk', 'Digital Forensics'],
            'is_featured' => false,
            'stages' => [
                'Giai đoạn 1: Đánh giá An ninh Ứng dụng Web' => ['demo-web-security-owasp-top-10-pentesting'],
                'Giai đoạn 2: Vận hành Trung tâm SOC & Blue Team' => ['demo-soc-blue-team-security-operations'],
            ],
        ],
        [
            'title' => 'Lộ Trình Kỹ Sư Đảm Bảo Chất Lượng Phần Mềm (QA/QC Automation)',
            'slug' => 'demo-lo-trinh-software-qa-qc-automation-engineer',
            'description' => 'Từ Manual Testing chuẩn ISTQB đến xây dựng Framework Automation Testing hoàn chỉnh với Playwright, API Testing và CI/CD Pipeline.',
            'level' => 'intermediate',
            'target_role' => 'QA / QC Automation Engineer',
            'salary_range' => '15.000.000đ - 35.000.000đ/tháng',
            'estimated_duration' => '4 - 7 tháng',
            'thumbnail' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?w=600',
            'skills' => ['Manual Testing', 'ISTQB', 'Playwright', 'TypeScript', 'API Testing', 'Jira'],
            'is_featured' => false,
            'stages' => [
                'Giai đoạn 1: Nền tảng Kiểm thử Thủ công & Quy trình' => ['demo-manual-testing-istqb-foundation'],
                'Giai đoạn 2: Tự động hóa Kiểm thử với Playwright' => ['demo-automation-testing-playwright-typescript'],
            ],
        ],
        [
            'title' => 'Lộ Trình Thiết Kế Trải Nghiệm & Giao Diện Sản Phẩm (UI/UX Product Designer)',
            'slug' => 'demo-lo-trinh-ui-ux-product-designer',
            'description' => 'Học tư duy thiết kế lấy người dùng làm trung tâm, nghiên cứu UX Research chuyên nghiệp và xây dựng Design System quy mô lớn trên Figma.',
            'level' => 'intermediate',
            'target_role' => 'UI/UX Designer / Product Designer',
            'salary_range' => '16.000.000đ - 36.000.000đ/tháng',
            'estimated_duration' => '4 - 6 tháng',
            'thumbnail' => 'https://images.unsplash.com/photo-1581291518655-9523c932ded8?w=600',
            'skills' => ['Figma', 'UX Research', 'Design System', 'Usability Testing', 'Wireframing', 'Prototyping'],
            'is_featured' => true,
            'stages' => [
                'Giai đoạn 1: Nghiên cứu Trải nghiệm Người dùng' => ['demo-ux-research-usability-testing'],
                'Giai đoạn 2: Thiết kế Giao diện & Design System trên Figma' => ['demo-figma-ui-ux-design-masterclass'],
            ],
        ],
        [
            'title' => 'Lộ Trình Chuyên Viên Phân Tích Dữ Liệu Kinh Doanh (Business & Data Analyst)',
            'slug' => 'demo-lo-trinh-business-data-analyst-powerbi',
            'description' => 'Trang bị trọn bộ công cụ phân tích dữ liệu doanh nghiệp hàng đầu: Microsoft Excel nâng cao, VBA tự động hóa và Microsoft Power BI Dashboard.',
            'level' => 'intermediate',
            'target_role' => 'Data Analyst / Business Intelligence Specialist',
            'salary_range' => '15.000.000đ - 32.000.000đ/tháng',
            'estimated_duration' => '4 - 6 tháng',
            'thumbnail' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=600',
            'skills' => ['Microsoft Excel', 'VBA Macro', 'Power BI', 'DAX', 'SQL', 'Data Modeling'],
            'is_featured' => true,
            'stages' => [
                'Giai đoạn 1: Xử lý Số liệu & Tự động hóa Excel' => ['demo-microsoft-excel-advanced-vba-macros'],
                'Giai đoạn 2: Xây dựng Dashboard Quản trị với Power BI' => ['demo-microsoft-power-bi-business-dashboard'],
                'Giai đoạn 3: Phân tích Dữ liệu Nâng cao với Python' => ['demo-python-data-science-pandas-analytics'],
            ],
        ],
        [
            'title' => 'Lộ Trình Chuyên Gia Digital Marketing & Tăng Trưởng Doanh Số (Growth Marketer)',
            'slug' => 'demo-lo-trinh-digital-marketing-growth-master',
            'description' => 'Làm chủ phễu Marketing đa kênh, SEO Top Google bền vững, tối ưu hóa quảng cáo Facebook/Google/TikTok Ads và phân tích hành vi khách hàng.',
            'level' => 'intermediate',
            'target_role' => 'Digital Marketing Lead / Growth Marketer',
            'salary_range' => '16.000.000đ - 35.000.000đ/tháng',
            'estimated_duration' => '4 - 7 tháng',
            'thumbnail' => 'https://images.unsplash.com/photo-1533750516457-a7f992034fec?w=600',
            'skills' => ['SEO', 'Google Ads', 'Facebook Ads', 'TikTok Ads', 'Content Marketing', 'Google Analytics 4'],
            'is_featured' => false,
            'stages' => [
                'Giai đoạn 1: Chiến lược Marketing Đa Kênh Toàn Diện' => ['demo-digital-marketing-omnichannel-growth'],
                'Giai đoạn 2: Tối ưu Hóa Tìm Kiếm SEO Web Masterclass' => ['demo-seo-web-masterclass-top-google'],
            ],
        ],
    ];

    public function run(User $creator, ?callable $output = null): array
    {
        $log = $output ?: fn(string $msg) => null;
        $log('--- Bắt đầu nạp 10 Lộ trình học tập (Learning Paths) phân giai đoạn chuẩn ---');

        $createdPaths = [];

        foreach ($this->pathsDefinition as $pData) {
            $learningPath = LearningPath::updateOrCreate(
                ['slug' => $pData['slug']],
                [
                    'created_by' => $creator->id,
                    'title' => $pData['title'],
                    'description' => $pData['description'],
                    'thumbnail' => $pData['thumbnail'],
                    'level' => $pData['level'],
                    'target_role' => $pData['target_role'],
                    'salary_range' => $pData['salary_range'],
                    'estimated_duration' => $pData['estimated_duration'],
                    'skills' => $pData['skills'],
                    'is_featured' => $pData['is_featured'],
                ]
            );

            // Gắn các khóa học theo từng Giai đoạn và thứ tự
            $sortOrder = 1;
            $attachedCourseIds = [];

            foreach ($pData['stages'] as $stageName => $courseSlugs) {
                foreach ($courseSlugs as $slug) {
                    $course = Course::where('slug', $slug)->first();
                    if ($course) {
                        $attachedCourseIds[$course->id] = [
                            'sort_order' => $sortOrder++,
                            'stage_name' => $stageName,
                        ];
                    }
                }
            }

            // Nếu số lượng khóa học gắn vào < 4, tự động bổ sung khóa học liên quan cùng danh mục
            if (count($attachedCourseIds) < 4) {
                $categoryIds = Course::whereIn('id', array_keys($attachedCourseIds))->pluck('category_id')->unique();
                $extraCourses = Course::whereIn('category_id', $categoryIds)
                    ->whereNotIn('id', array_keys($attachedCourseIds))
                    ->take(4 - count($attachedCourseIds))
                    ->get();

                foreach ($extraCourses as $extra) {
                    $attachedCourseIds[$extra->id] = [
                        'sort_order' => $sortOrder++,
                        'stage_name' => 'Giai đoạn Bổ trợ & Nâng cao kỹ năng',
                    ];
                }
            }

            $learningPath->courses()->sync($attachedCourseIds);
            $createdPaths[] = $learningPath;
        }

        $log('✓ Đã nạp thành công ' . count($createdPaths) . ' Lộ trình học tập chuyên môn cao');
        return $createdPaths;
    }
}
