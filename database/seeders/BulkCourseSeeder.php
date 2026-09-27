<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BulkCourseSeeder extends Seeder
{
    /**
     * Danh sách video MP4 thật (Direct CDN links, tương thích HTML5 video player và trình duyệt)
     */
    private array $realMp4Videos = [
        'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4',
        'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4',
        'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4',
        'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4',
        'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerFun.mp4',
        'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerJoyBlazes.mp4',
        'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerMeltdowns.mp4',
        'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/Sintel.mp4',
        'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/SubaruOutbackOnStreetAndDirt.mp4',
        'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/TearsOfSteel.mp4',
        'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/WeAreGoingOnBullrun.mp4',
        'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/WhatCarCanYouGetForAGrand.mp4',
        'https://www.w3schools.com/html/mov_bbb.mp4',
    ];

    /**
     * Thư viện ảnh Unsplash phân loại chi tiết theo từng lĩnh vực cụ thể
     */
    private array $domainImages = [
        'web_dev' => [
            'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1633356122544-f134324a6cee?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1537432376769-00f5c2f4c8d2?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1587620962725-abab7fe55159?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1504639725590-34d0984388bd?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1522252234503-e356532cafd5?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1515879218367-8466d910aaa4?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1542838132-92c53300491e?w=800&auto=format&fit=crop&q=80',
        ],
        'mobile_dev' => [
            'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1526470608268-f674ce90ebd4?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1551650975-87deedd944c3?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1555774698-0b77e0d5fac6?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1607252650355-f7fd0460ccdb?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1534972195531-a756b1126f24?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1510519138197-06b8628cbf4f?w=800&auto=format&fit=crop&q=80',
        ],
        'game_dev' => [
            'https://images.unsplash.com/photo-1550745165-9bc0b252726f?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1511512578047-dfb367046420?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1542751371-adc38448a05e?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1538481199705-c710c4e965fc?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1552824722-ddab1374e622?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1579373903781-fd5c0c30c4cd?w=800&auto=format&fit=crop&q=80',
        ],
        'ai_data' => [
            'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1504868584819-f8e8b4b6d7e3?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1518186285589-2f7649de83e0?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1509228468518-180dd4864904?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1535378620166-273708d44e4c?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1551836022-deb4988cc6c0?w=800&auto=format&fit=crop&q=80',
        ],
        'cloud_security' => [
            'https://images.unsplash.com/photo-1544383835-bda2bc66a55d?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1563089145-599997674d42?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1607799279861-4dd421887fb3?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1542744094-3a31f272c490?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?w=800&auto=format&fit=crop&q=80',
        ],
        'business_management' => [
            'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1556742049-0a67e5572293?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1531403009284-440f080d1e12?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1552664730-d307ca884978?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1507679799987-c73779587ccf?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=800&auto=format&fit=crop&q=80',
        ],
        'finance_accounting' => [
            'https://images.unsplash.com/photo-1590283603385-17ffb3a7f29f?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1551836022-d5d88e9218df?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1621416894569-0f39ed31d247?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1526304640581-d334cdbbf45e?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1559526324-4b87b5e36e44?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1450133064473-71024230f91b?w=800&auto=format&fit=crop&q=80',
        ],
        'design_creative' => [
            'https://images.unsplash.com/photo-1581291518857-4e27b48ff24e?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1586717791821-3f44a563fa4c?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1626785774573-4b799315345d?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1574717024653-61fd2cf4d44d?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1513542789411-b6a5d4f31634?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1542744095-fcf48d80b0fd?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=800&auto=format&fit=crop&q=80',
        ],
        'marketing_sales' => [
            'https://images.unsplash.com/photo-1571786256017-aee7a0c009b6?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1533750516457-a7f992034fec?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1557838923-2985c318be48?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1432888498266-38ffec3eaf0a?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1611162617474-5b21e879e113?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1552664730-d307ca884978?w=800&auto=format&fit=crop&q=80',
        ],
        'office_productivity' => [
            'https://images.unsplash.com/photo-1506784365847-bbad939e9335?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1517048676732-d65bc937f952?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1513128034602-7814ccaddd4e?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=800&auto=format&fit=crop&q=80',
        ],
        'personal_dev' => [
            'https://images.unsplash.com/photo-1450133064473-71024230f91b?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1521737711867-e3b97375f902?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1531482615713-2afd69097998?w=800&auto=format&fit=crop&q=80',
        ],
        'languages_academics' => [
            'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1475721027785-f74eccf877e2?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1516979187457-637abb4f9353?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?w=800&auto=format&fit=crop&q=80',
        ],
        'photo_video' => [
            'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1492691527719-9d1e07e534b4?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1574717024653-61fd2cf4d44d?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1533750516457-a7f992034fec?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1471341971476-ae15ff5dd4ea?w=800&auto=format&fit=crop&q=80',
        ],
        'health_fitness' => [
            'https://images.unsplash.com/photo-1517838277536-f5f99be501cd?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1506126613408-eca07ce68773?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1498837167922-ddd27525d352?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1518611012118-696072aa579a?w=800&auto=format&fit=crop&q=80',
        ],
        'music' => [
            'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1507838153414-b4b713384a76?w=800&auto=format&fit=crop&q=80',
        ],
        'lifestyle' => [
            'https://images.unsplash.com/photo-1513542789411-b6a5d4f31634?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1556911220-e15b29be8c8f?w=800&auto=format&fit=crop&q=80',
            'https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=800&auto=format&fit=crop&q=80',
        ],
    ];

    /**
     * Map danh mục với chủ đề môn học
     */
    private function getCategoryTaxonomyData(string $categoryName): array
    {
        return match ($categoryName) {
            'Phát triển Web' => [
                'domain' => 'web_dev',
                'subjects' => ['Laravel 12 & Vue 3', 'React 19 & Next.js 15', 'Fullstack NestJS & TypeScript', 'Django 5 Web API', 'ASP.NET Core 9'],
                'tags' => ['Web', 'Frontend', 'Backend', 'Fullstack', 'JavaScript', 'PHP'],
            ],
            'Phát triển ứng dụng Mobile' => [
                'domain' => 'mobile_dev',
                'subjects' => ['Flutter & Dart Toàn Diện', 'React Native Thực Chiến', 'iOS Swift 6 & SwiftUI', 'Android Kotlin Jetpack Compose'],
                'tags' => ['Mobile', 'Flutter', 'React Native', 'iOS', 'Android'],
            ],
            'Lập trình Game' => [
                'domain' => 'game_dev',
                'subjects' => ['Unity 3D C# Game Engine', 'Unreal Engine 5 C++', 'Thiết Kế Game 2D Pixel Art', 'Lập Trình Game Mobile'],
                'tags' => ['Game Dev', 'Unity', 'Unreal Engine', 'C#', '3D Game'],
            ],
            'Khoa học dữ liệu' => [
                'domain' => 'ai_data',
                'subjects' => ['Python Khoa Học Dữ Liệu', 'Trực Quan Hóa Dữ Liệu với Matplotlib', 'Big Data Analytics với Apache Spark', 'Phân Tích Dữ Liệu với Pandas'],
                'tags' => ['Data Science', 'Python', 'Pandas', 'Big Data', 'Analytics'],
            ],
            'Trí tuệ nhân tạo và Machine Learning' => [
                'domain' => 'ai_data',
                'subjects' => ['Machine Learning Thực Chiến', 'Deep Learning & PyTorch', 'Generative AI & Prompting', 'Thị Giác Máy Tính OpenCV'],
                'tags' => ['AI', 'Machine Learning', 'Deep Learning', 'PyTorch', 'Computer Vision'],
            ],
            'Ngôn ngữ lập trình' => [
                'domain' => 'web_dev',
                'subjects' => ['Lập Trình Golang Concurrency', 'Lập Trình C++ Nâng Cao', 'Java Spring Boot 3 Core', 'Rust Cho Hệ Thống An Toàn', 'Python Chuyên Sâu'],
                'tags' => ['Programming', 'Golang', 'Java', 'Python', 'C++', 'Rust'],
            ],
            'Cơ sở dữ liệu' => [
                'domain' => 'web_dev',
                'subjects' => ['Thiết Kế Cơ Sở Dữ Liệu MySQL', 'Tối Ưu Hóa Index & Query Performance', 'NoSQL MongoDB & Redis Caching', 'PostgreSQL Nâng Cao'],
                'tags' => ['Database', 'SQL', 'MySQL', 'PostgreSQL', 'MongoDB', 'Redis'],
            ],
            'Kiểm thử phần mềm' => [
                'domain' => 'web_dev',
                'subjects' => ['Kiểm Thử Tự Động với Selenium & Cypress', 'Manual Testing & Viết Test Case', 'Kiểm Thử API với Postman', 'QA Lead & Quản Lý Chất Lượng'],
                'tags' => ['QA', 'Software Testing', 'Automation Test', 'Selenium', 'Postman'],
            ],
            'Công cụ No-code' => [
                'domain' => 'web_dev',
                'subjects' => ['Xây Dựng Web App với Bubble.io', 'Tự Động Hóa Quy Trình với Zapier', 'Xây Dựng Website với Webflow', 'AppSheet No-code Apps'],
                'tags' => ['No-Code', 'Bubble', 'Webflow', 'Zapier', 'Automation'],
            ],
            'Khởi nghiệp' => [
                'domain' => 'business_management',
                'subjects' => ['Khởi Nghiệp Tinh Gọn Lean Startup', 'Thẩm Định Ý Tưởng & Mô Hình Kinh Doanh', 'Gọi Vốn Đầu Tư & Pitching', 'Xây Dựng MVP Cho Startup'],
                'tags' => ['Startup', 'Lean Startup', 'Business Model', 'Fundraising', 'MVP'],
            ],
            'Giao tiếp' => [
                'domain' => 'personal_dev',
                'subjects' => ['Kỹ Năng Giao Tiếp Kinh Doanh Hiệu Quả', 'Thuyết Phục Đối Tác Trong Đàm Phán', 'Nghệ Thuật Kể Chuyện Thương Mại', 'Xử Lý Khủng Hoảng Giao Tiếp'],
                'tags' => ['Communication', 'Negotiation', 'Business Skills', 'Storytelling'],
            ],
            'Quản trị doanh nghiệp' => [
                'domain' => 'business_management',
                'subjects' => ['Quản Trị Doanh Nghiệp Thời Đại Số', 'Xây Dựng Hệ Thống Dashboard KPI', 'Quản Trị Chiến Lược & Tái Cấu Trúc', 'Văn Hóa Doanh Nghiệp'],
                'tags' => ['Management', 'Enterprise', 'Strategy', 'KPI', 'Governance'],
            ],
            'Bán hàng' => [
                'domain' => 'marketing_sales',
                'subjects' => ['Bí Quyết Telesales & Chốt Đơn Đỉnh Cao', 'Nghệ Thuật Bán Hàng B2B Doanh Nghiệp', 'Xây Dựng Phễu Bán Hàng Tự Động', 'Tâm Lý Học Khách Hàng'],
                'tags' => ['Sales', 'B2B Sales', 'Closing Deals', 'Sales Funnel', 'Customer Psychology'],
            ],
            'Quản trị nhân sự' => [
                'domain' => 'business_management',
                'subjects' => ['Tuyển Dụng & Săn Đầu Người Headhunting', 'Xây Dựng Hệ Thống Lương Thưởng C&B', 'Đánh Giá Hiệu Suất Theo OKRs & KPIs', 'Đào Tạo Nhân Tài L&D'],
                'tags' => ['HR', 'Human Resources', 'Recruitment', 'C&B', 'OKRs', 'KPIs'],
            ],
            'Phân tích kinh doanh' => [
                'domain' => 'ai_data',
                'subjects' => ['Business Analysis (BA) Thực Chiến', 'Khảo Sát & Viết Tài Liệu Yêu Cầu BRD/SRS', 'Vẽ Sơ Đồ Quy Trình Nghiệp Vụ BPMN', 'SQL Cho Chuyên Viên Phân Tích BA'],
                'tags' => ['Business Analysis', 'BA', 'BRD', 'SRS', 'BPMN', 'Requirements'],
            ],
            'Kế toán và ghi sổ' => [
                'domain' => 'finance_accounting',
                'subjects' => ['Kế Toán Doanh Nghiệp Từ A Đến Z', 'Lập Báo Cáo Tài Chính & Quyết Toán Thuế', 'Thực Hành Phần Mềm Kế Toán MISA', 'Xử Lý Hóa Đơn Chứng Từ'],
                'tags' => ['Accounting', 'Bookkeeping', 'MISA', 'Tax Accounting', 'Financial Statements'],
            ],
            'Tài chính doanh nghiệp' => [
                'domain' => 'finance_accounting',
                'subjects' => ['Quản Trị Tài Chính Doanh Nghiệp Nâng Cao', 'Hoạch Định Ngân Sách & Quản Trị Dòng Tiền', 'Thẩm Định Dự Án Đầu Tư Doanh Nghiệp', 'Cấu Trúc Vốn & Tối Ưu Chi Phí WACC'],
                'tags' => ['Corporate Finance', 'Cash Flow', 'Budgeting', 'WACC', 'Financial Management'],
            ],
            'Đầu tư và giao dịch' => [
                'domain' => 'finance_accounting',
                'subjects' => ['Đầu Tư Chứng Khoán Cơ Bản Đến Nâng Cao', 'Phân Tích Kỹ Thuật Biểu Đồ Nến & Price Action', 'Chiến Lược Giao Dịch Phái Sinh', 'Quản Trị Rủi Ro & Tâm Lý Giao Dịch'],
                'tags' => ['Stock Trading', 'Technical Analysis', 'Price Action', 'Swing Trading', 'Risk Management'],
            ],
            'Phân tích tài chính' => [
                'domain' => 'finance_accounting',
                'subjects' => ['Đọc Hiểu & Phân Tích Báo Cáo Tài Chính', 'Mô Hình Định Giá Cổ Phiếu DCF & P/E', 'Phân Tích Chỉ Số Tài Chính Dupont', 'Financial Modeling trên Excel'],
                'tags' => ['Financial Analysis', 'Valuation', 'DCF', 'Financial Modeling', 'Financial Ratios'],
            ],
            'An ninh mạng' => [
                'domain' => 'cloud_security',
                'subjects' => ['An Toàn Thông Tin & OWASP Top 10', 'Ethical Hacking & Penetration Testing Kali Linux', 'Bảo Mật Ứng Dụng Web & API Security', 'Phòng Chống Tấn Công Mạng'],
                'tags' => ['Cyber Security', 'Ethical Hacking', 'Penetration Testing', 'Kali Linux', 'Web Security'],
            ],
            'Mạng máy tính' => [
                'domain' => 'cloud_security',
                'subjects' => ['Mạng Máy Tính Doanh Nghiệp Toàn Diện', 'Cấu Hình Router & Switch Cisco', 'Giao Thức TCP/IP & Định Tuyến Routing', 'Thiết Kế Hạ Tầng Mạng An Toàn'],
                'tags' => ['Networking', 'Cisco', 'TCP/IP', 'Routing', 'Switching', 'VLAN'],
            ],
            'Hệ điều hành' => [
                'domain' => 'cloud_security',
                'subjects' => ['Quản Trị Linux Ubuntu & RedHat Enterprise', 'Lập Trình Shell Scripting & Bash Automation', 'Quản Trị Windows Server & Active Directory', 'Kiến Trúc Nhân Hệ Điều Hành OS'],
                'tags' => ['Operating Systems', 'Linux', 'Ubuntu', 'Windows Server', 'Bash', 'Shell Script'],
            ],
            'Điện toán đám mây' => [
                'domain' => 'cloud_security',
                'subjects' => ['Kiến Trúc Đám Mây AWS Solutions Architect', 'Xây Dựng Hệ Thống Serverless với AWS Lambda', 'Google Cloud Platform (GCP) Fundamentals', 'Tối Ưu Hóa Chi Phí Đám Mây Cloud FinOps'],
                'tags' => ['Cloud Computing', 'AWS', 'GCP', 'Azure', 'Serverless', 'Cloud Architect'],
            ],
            'DevOps' => [
                'domain' => 'cloud_security',
                'subjects' => ['Docker & Kubernetes Cho Doanh Nghiệp', 'CI/CD Pipeline với GitHub Actions', 'Terraform & Infrastructure as Code (IaC)', 'Giám Sát Hệ Thống với Prometheus & Grafana'],
                'tags' => ['DevOps', 'Docker', 'Kubernetes', 'CI/CD', 'Terraform', 'Prometheus'],
            ],
            'Microsoft Excel' => [
                'domain' => 'office_productivity',
                'subjects' => ['Thành Thạo Microsoft Excel Từ A Đến Z', 'Tự Động Hóa Xử Lý Báo Cáo với VBA & Macro', 'Hàm Nâng Cao VLOOKUP, XLOOKUP, INDEX MATCH', 'Phân Tích Dữ Liệu với Pivot Table & Power Query'],
                'tags' => ['Excel', 'VBA', 'Macro', 'PivotTable', 'Power Query', 'Data Analysis'],
            ],
            'Power BI' => [
                'domain' => 'ai_data',
                'subjects' => ['Dashboard Trực Quan Hóa Dữ Liệu Power BI', 'Viết Hàm DAX Chuyên Sâu Trong Power BI', 'Xử Lý Dữ Liệu với Power Query', 'Power BI Cho Phân Tích Tài Chính & Kinh Doanh'],
                'tags' => ['Power BI', 'DAX', 'Dashboard', 'Data Visualization', 'Business Intelligence'],
            ],
            'Thiết kế UI/UX' => [
                'domain' => 'design_creative',
                'subjects' => ['Figma UI/UX Masterclass & Design System', 'Nghiên Cứu Người Dùng UX Research', 'Thiết Kế Micro-interactions & Prototype Động', 'Thiết Kế Giao Diện Mobile App Chuẩn'],
                'tags' => ['UI/UX', 'Figma', 'UX Research', 'Design System', 'Prototype'],
            ],
            'Digital Marketing' => [
                'domain' => 'marketing_sales',
                'subjects' => ['Digital Marketing Thực Chiến Tổng Lực', 'Lập Kế Hoạch Marketing Đa Kênh Omnichannel', 'Tối Ưu Phễu Chuyển Đổi Conversion Rate (CRO)', 'Phân Tích Dữ Liệu với Google Analytics 4'],
                'tags' => ['Digital Marketing', 'GA4', 'Omnichannel', 'CRO', 'Online Marketing'],
            ],
            'Ngoại ngữ' => [
                'domain' => 'languages_academics',
                'subjects' => ['Tiếng Anh Giao Tiếp Thực Tế Cho Người Đi Làm', 'Tiếng Nhật Cấp Tốc N5 Đến N2', 'Tiếng Hàn Giao Tiếp Hàng Ngày', 'Tiếng Trung Giao Tiếp Thương Mại'],
                'tags' => ['Languages', 'English', 'Japanese', 'Korean', 'Chinese'],
            ],
            default => [
                'domain' => 'business_management',
                'subjects' => ["Khóa Học $categoryName Thực Chiến", "Làm Chủ Chuyên Môn $categoryName", "Kỹ Năng Ứng Dụng $categoryName"],
                'tags' => [$categoryName, 'OnlineFEA', 'Thực Chiến', 'Chuyên Sâu'],
            ]
        };
    }

    private array $prefixes = [
        'Khóa Học Toàn Diện',
        'Làm Chủ',
        'Thực Chiến',
        'Bí Quyết Thành Thạo',
        'Cẩm Nang Chuyên Sâu',
        'Khóa Học Thực Hành',
        'Tối Ưu Hóa',
        'Lộ Trình Tự Học',
        'Nâng Cao Năng Lực',
        'Đột Phá Kỹ Năng',
        'Chinh Phục',
        'Từ Cơ Bản Đến Nâng Cao',
        'Xây Dựng Dự Án Thực Tế',
        'Chuyên Gia',
    ];

    private array $suffixes = [
        'Từ Con Số 0',
        'Chuẩn Quốc Tế',
        'Cho Người Mới Bắt Đầu',
        'Ứng Dụng Doanh Nghiệp',
        'Dành Cho Kỹ Sư Chuyên Nghiệp',
        'Nâng Cao Hiệu Quả Công Việc',
        'Tăng Tốc Sự Nghiệp',
        'Theo Tiêu Chuẩn Thực Tế 2026',
        'Cấp Tốc Trong 30 Ngày',
        'Kèm Dự Án Thực Chiến',
    ];

    /**
     * Thực thi nạp dữ liệu khóa học quy mô lớn kèm giáo trình, bài học video MP4 thật, tài liệu và quiz
     */
    public function run(int $targetTotal = 20000, ?callable $logger = null, bool $cleanBefore = false): void
    {
        $log = $logger ?: function (string $msg) {
            echo $msg . "\n";
        };

        $log("\n=========================================================================");
        $log("   BẮT ĐẦU NẠP $targetTotal KHÓA HỌC KÈM GIÁO TRÌNH, MP4 THẬT, TÀI LIỆU & QUIZZES");
        $log("=========================================================================\n");

        $startTime = microtime(true);

        // 1. Dọn dẹp nếu có yêu cầu
        if ($cleanBefore) {
            $log("▶ Đang dọn dẹp các khóa học nạp bulk trước đó...");
            $bulkCourseIds = DB::table('courses')->where('slug', 'like', 'bulk-course-%')->pluck('id')->all();
            if (!empty($bulkCourseIds)) {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                $bulkLessonIds = DB::table('lessons')->whereIn('course_id', $bulkCourseIds)->pluck('id')->all();
                if (!empty($bulkLessonIds)) {
                    $bulkQuizIds = DB::table('quizzes')->whereIn('lesson_id', $bulkLessonIds)->pluck('id')->all();
                    if (!empty($bulkQuizIds)) {
                        $bulkQuestionIds = DB::table('quiz_questions')->whereIn('quiz_id', $bulkQuizIds)->pluck('id')->all();
                        if (!empty($bulkQuestionIds)) {
                            DB::table('quiz_options')->whereIn('quiz_question_id', $bulkQuestionIds)->delete();
                            DB::table('quiz_version_questions')->whereIn('question_id', $bulkQuestionIds)->delete();
                            DB::table('question_versions')->whereIn('question_id', $bulkQuestionIds)->delete();
                            DB::table('quiz_questions')->whereIn('id', $bulkQuestionIds)->delete();
                        }
                        DB::table('quiz_versions')->whereIn('quiz_id', $bulkQuizIds)->delete();
                        DB::table('quizzes')->whereIn('id', $bulkQuizIds)->delete();
                    }
                    DB::table('lesson_attachments')->whereIn('lesson_id', $bulkLessonIds)->delete();
                    DB::table('lessons')->whereIn('id', $bulkLessonIds)->delete();
                }
                DB::table('chapters')->whereIn('course_id', $bulkCourseIds)->delete();
                DB::table('course_sections')->whereIn('course_id', $bulkCourseIds)->delete();
                DB::table('courses')->whereIn('id', $bulkCourseIds)->delete();
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            }
            $log("✓ Đã dọn dẹp xong.\n");
        }

        // 2. Lấy danh sách Giảng viên đã phê duyệt
        $instructorIds = User::where('role', 'instructor')
            ->orWhere('instructor_status', 'approved')
            ->pluck('id')
            ->all();

        if (empty($instructorIds)) {
            $instructorIds = User::where('role', 'admin')->pluck('id')->all();
        }
        if (empty($instructorIds)) {
            $instructorIds = [1];
        }

        // 3. Lấy toàn bộ danh mục con (leaf categories)
        $childCategories = Category::whereNotNull('parent_id')
            ->with('parent')
            ->get();

        if ($childCategories->isEmpty()) {
            $childCategories = Category::all();
        }

        $totalCategories = $childCategories->count();
        $log("✓ Tìm thấy $totalCategories danh mục con cần phân bổ khóa học.");

        // 4. Lấy ID xuất phát cho các bảng
        $curCourseId = (int)(DB::table('courses')->max('id') ?? 0) + 1;
        $curChapterId = (int)(DB::table('chapters')->max('id') ?? 0) + 1;
        $curSectionId = (int)(DB::table('course_sections')->max('id') ?? 0) + 1;
        $curLessonId = (int)(DB::table('lessons')->max('id') ?? 0) + 1;
        $curQuizId = (int)(DB::table('quizzes')->max('id') ?? 0) + 1;
        $curQuizVersionId = (int)(DB::table('quiz_versions')->max('id') ?? 0) + 1;
        $curQuestionId = (int)(DB::table('quiz_questions')->max('id') ?? 0) + 1;
        $curQuestionVersionId = (int)(DB::table('question_versions')->max('id') ?? 0) + 1;
        $curQuizVersionQuestionId = (int)(DB::table('quiz_version_questions')->max('id') ?? 0) + 1;
        $curOptionId = (int)(DB::table('quiz_options')->max('id') ?? 0) + 1;
        $curAttachmentId = (int)(DB::table('lesson_attachments')->max('id') ?? 0) + 1;

        $chunkSize = 250;
        $totalInserted = 0;
        $now = Carbon::now();

        $levels = ['beginner', 'intermediate', 'advanced'];
        $prices = [
            0, 0,
            199000, 299000, 399000, 499000, 599000,
            699000, 799000, 899000, 999000, 1299000,
            1499000, 1799000, 1999000, 2499000
        ];

        $prefixCount = count($this->prefixes);
        $suffixCount = count($this->suffixes);
        $instructorCount = count($instructorIds);
        $mp4Count = count($this->realMp4Videos);

        $courseBatch = [];
        $chapterBatch = [];
        $sectionBatch = [];
        $lessonBatch = [];
        $attachmentBatch = [];
        $quizBatch = [];
        $quizVersionBatch = [];
        $questionBatch = [];
        $questionVersionBatch = [];
        $quizVersionQuestionBatch = [];
        $optionBatch = [];

        for ($i = 1; $i <= $targetTotal; $i++) {
            $courseId = $curCourseId++;
            $category = $childCategories[($i - 1) % $totalCategories];
            $catTaxonomy = $this->getCategoryTaxonomyData($category->name);

            $subjectList = $catTaxonomy['subjects'];
            $subjectIndex = (int)(($i - 1) / $totalCategories) % count($subjectList);
            $subject = $subjectList[$subjectIndex];

            $prefix = $this->prefixes[($i * 7 + (int)($i / $totalCategories)) % $prefixCount];
            $suffix = $this->suffixes[($i * 11 + (int)($i / $totalCategories)) % $suffixCount];

            $title = "$prefix $subject - $suffix";
            $slug = 'bulk-course-' . Str::slug($category->name) . '-' . Str::slug($subject) . '-' . $courseId;

            // Ảnh Unsplash xoay vòng đa dạng
            $domainKey = $catTaxonomy['domain'];
            $imageList = $this->domainImages[$domainKey] ?? $this->domainImages['web_dev'];
            $imageIndex = ($i + (int)($i / $totalCategories) * 3) % count($imageList);
            $thumbnail = $imageList[$imageIndex];

            $instructorId = $instructorIds[($i - 1) % $instructorCount];
            $price = $prices[$i % count($prices)];
            $salePrice = ($price > 0 && ($i % 3 !== 0)) ? (float)round($price * (rand(60, 85) / 100), -4) : null;
            $level = $levels[$i % count($levels)];
            $status = ($i % 30 === 0) ? 'pending_review' : (($i % 45 === 0) ? 'draft' : 'published');
            $isPublished = ($status === 'published');
            $ratingAvg = ($status === 'published') ? round(4.0 + (($i * 17 % 100) / 100), 2) : 0.00;
            if ($ratingAvg > 5.0) {
                $ratingAvg = 5.00;
            }
            $ratingCount = ($status === 'published') ? (($i * 23) % 450) + 12 : 0;
            $enrollmentCount = ($status === 'published') ? (($i * 37) % 3200) + 45 : 0;

            $objectives = [
                "Nắm vững toàn bộ kiến thức cốt lõi và tư duy thực chiến về $subject.",
                "Tự tay xây dựng và triển khai các dự án thực tế đạt tiêu chuẩn chuyên nghiệp.",
                "Tối ưu hóa quy trình làm việc, nâng cao hiệu suất trong lĩnh vực {$category->name}.",
                "Sở hữu các kỹ năng chuyên sâu và kiến thức cập nhật nhất năm 2026."
            ];

            $requirements = [
                "Máy tính hoặc thiết bị cá nhân có kết nối Internet ổn định.",
                "Tư duy logic tốt và tinh thần kiên trì học tập, thực hành theo từng bài giảng.",
                "Cài đặt các công cụ và phần mềm cần thiết được hướng dẫn trong khóa học."
            ];

            $targetAudience = [
                "Sinh viên và người đi làm muốn nâng cao chuyên môn trong lĩnh vực {$category->name}.",
                "Các chuyên viên mong muốn chuyển đổi nghề nghiệp hoặc bổ sung kỹ năng mới về $subject.",
                "Những người tự học cần một lộ trình bài bản, thực tế và dễ tiếp thu."
            ];

            $courseTags = array_unique(array_merge(
                $catTaxonomy['tags'],
                [$category->name, 'OnlineFEA', 'Thực Chiến']
            ));

            $createdAt = $now->copy()->subDays(($i % 365) + 10)->subHours($i % 24);
            $publishedAt = $isPublished ? $createdAt->copy()->addDays(2) : null;
            $submittedAt = ($status === 'pending_review') ? $now->copy()->subDays(2) : null;
            $approvedAt = $isPublished ? $publishedAt : null;

            // 1. Thêm Course
            $courseBatch[] = [
                'id' => $courseId,
                'instructor_id' => $instructorId,
                'category_id' => $category->id,
                'title' => $title,
                'slug' => $slug,
                'short_description' => "Khóa học chuyên sâu $title thuộc chuyên ngành {$category->name} cung cấp đầy đủ kiến thức thực chiến và kỹ năng ứng dụng thực tế.",
                'description' => "<p>Khóa học <strong>$title</strong> được thiết kế khoa học, kết hợp giữa lý thuyết nền tảng và bài tập thực hành theo dự án thực tế trong lĩnh vực <strong>{$category->name}</strong>.</p><p>Học viên sẽ được hướng dẫn chi tiết từng bước từ cơ bản đến nâng cao về $subject, giải quyết các tình huống thường gặp trong môi trường doanh nghiệp thực tế.</p>",
                'objectives' => json_encode($objectives, JSON_UNESCAPED_UNICODE),
                'requirements' => json_encode($requirements, JSON_UNESCAPED_UNICODE),
                'target_audience' => json_encode($targetAudience, JSON_UNESCAPED_UNICODE),
                'thumbnail' => $thumbnail,
                'preview_video' => $this->realMp4Videos[$i % $mp4Count],
                'level' => $level,
                'language' => ($i % 15 === 0) ? 'en' : 'vi',
                'price' => $price,
                'discount_price' => $salePrice,
                'sale_price' => $salePrice,
                'status' => $status,
                'is_published' => $isPublished,
                'rating_avg' => $ratingAvg,
                'rating_count' => $ratingCount,
                'enrollment_count' => $enrollmentCount,
                'duration_minutes' => 95,
                'tags' => json_encode($courseTags, JSON_UNESCAPED_UNICODE),
                'is_featured' => ($i % 20 === 0),
                'submission_count' => ($status === 'pending_review' ? 1 : 0),
                'required_video_percent' => 80,
                'required_lesson_percent' => 80,
                'minimum_quiz_score' => 80,
                'require_all_quizzes' => true,
                'require_all_assignments' => true,
                'certificate_enabled' => true,
                'copyright_agreed' => true,
                'copyright_agreed_at' => $createdAt,
                'copyright_agreed_by' => $instructorId,
                'published_at' => $publishedAt,
                'submitted_at' => $submittedAt,
                'approved_at' => $approvedAt,
                'created_at' => $createdAt,
                'updated_at' => $now,
            ];

            // 2. Thêm Chương 1 & Section 1
            $chap1Id = $curChapterId++;
            $sec1Id = $curSectionId++;
            $chap1Title = "Chương 1: Tổng quan Nền tảng & Cài đặt Môi trường $subject";

            $chapterBatch[] = [
                'id' => $chap1Id,
                'course_id' => $courseId,
                'title' => $chap1Title,
                'sort_order' => 1,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];

            $sectionBatch[] = [
                'id' => $sec1Id,
                'course_id' => $courseId,
                'title' => $chap1Title,
                'description' => "Giới thiệu tổng quan và lộ trình bài bản về $subject",
                'sort_order' => 1,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];

            // Lesson 1.1: Video Clip MP4 Thật
            $les1_1Id = $curLessonId++;
            $mp4Url1 = $this->realMp4Videos[($i * 3) % $mp4Count];
            $lessonBatch[] = [
                'id' => $les1_1Id,
                'course_id' => $courseId,
                'section_id' => $sec1Id,
                'chapter_id' => $chap1Id,
                'title' => "Bài 1.1: Giới thiệu tổng quan & Lộ trình thực chiến $subject",
                'type' => 'video',
                'video_url' => $mp4Url1,
                'content' => "<p>Trong video bài học này, giảng viên sẽ giới thiệu chi tiết lộ trình học tập thực chiến về <strong>$subject</strong>, mục tiêu đầu ra và các dự án thực tế bạn sẽ tự tay xây dựng.</p>",
                'document_file' => null,
                'duration' => 720,
                'duration_seconds' => 720,
                'is_preview' => true,
                'is_required' => true,
                'sort_order' => 1,
                'status' => 'published',
                'upload_status' => 'uploaded',
                'processing_status' => 'ready',
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];

            // Lesson 1.2: Tài liệu Giáo trình + Attachment File
            $les1_2Id = $curLessonId++;
            $docPath1 = "demo/documents/" . Str::slug($subject) . "-giao-trinh.pdf";
            $docTitle1 = "Giao-trinh-ly-thuyet-" . Str::slug($subject) . ".pdf";
            $lessonBatch[] = [
                'id' => $les1_2Id,
                'course_id' => $courseId,
                'section_id' => $sec1Id,
                'chapter_id' => $chap1Id,
                'title' => "Bài 1.2: Giáo trình lý thuyết nền tảng & Hướng dẫn cài đặt $subject",
                'type' => 'document',
                'video_url' => null,
                'content' => "### Giáo trình Bài học: Tổng quan $subject\n\n#### 1. Nguyên lý hoạt động\nKhóa học giúp bạn nắm vững bản chất hoạt động của **$subject**, từ cấu trúc kiến trúc đến các quy chuẩn doanh nghiệp.\n\n#### 2. Các bước thực hiện\n* Bước 1: Chuẩn bị môi trường và cài đặt các công cụ phụ trợ cần thiết.\n* Bước 2: Cấu hình tham số môi trường và kiểm tra kết nối.\n* Bước 3: Thực thi các bài tập mẫu và kiểm tra kết quả đầu ra.\n\n> **Lưu ý:** Bạn có thể tải tài liệu PDF đính kèm bên dưới để xem chi tiết sơ đồ kiến trúc.",
                'document_file' => $docPath1,
                'duration' => 600,
                'duration_seconds' => 600,
                'is_preview' => false,
                'is_required' => true,
                'sort_order' => 2,
                'status' => 'published',
                'upload_status' => 'completed',
                'processing_status' => 'completed',
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];

            // Lesson Attachment cho Bài 1.2
            $attachmentBatch[] = [
                'id' => $curAttachmentId++,
                'lesson_id' => $les1_2Id,
                'title' => $docTitle1,
                'file_path' => $docPath1,
                'file_type' => "application/pdf",
                'file_size' => 2458120,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];

            // Lesson 1.3: Quizz trắc nghiệm
            $les1_3Id = $curLessonId++;
            $lessonBatch[] = [
                'id' => $les1_3Id,
                'course_id' => $courseId,
                'section_id' => $sec1Id,
                'chapter_id' => $chap1Id,
                'title' => "Bài 1.3: Trắc nghiệm kiểm tra kiến thức nền tảng $subject",
                'type' => 'quiz',
                'video_url' => null,
                'content' => "Bài kiểm tra trắc nghiệm 2 câu hỏi đánh giá mức độ hiểu sâu lý thuyết cơ bản của bạn.",
                'document_file' => null,
                'duration' => 900,
                'duration_seconds' => 900,
                'is_preview' => false,
                'is_required' => true,
                'sort_order' => 3,
                'status' => 'published',
                'upload_status' => 'completed',
                'processing_status' => 'completed',
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];

            // Tạo Quiz & QuizVersion cho Bài 1.3
            $quiz1Id = $curQuizId++;
            $quiz1VerId = $curQuizVersionId++;
            $quizBatch[] = [
                'id' => $quiz1Id,
                'lesson_id' => $les1_3Id,
                'title' => "Trắc nghiệm nền tảng: $subject",
                'description' => "Đánh giá mức độ tiếp thu kiến thức cơ bản về $subject",
                'pass_score' => 80,
                'time_limit_minutes' => 15,
                'max_attempts' => 5,
                'question_count' => 2,
                'is_active' => true,
                'current_published_version_id' => $quiz1VerId,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];

            $quizVersionBatch[] = [
                'id' => $quiz1VerId,
                'quiz_id' => $quiz1Id,
                'version' => 1,
                'title' => "Trắc nghiệm nền tảng: $subject",
                'description' => "Đánh giá mức độ tiếp thu kiến thức cơ bản về $subject",
                'pass_score' => 80,
                'time_limit_minutes' => 15,
                'max_attempts' => 5,
                'status' => 'published',
                'created_by' => $instructorId,
                'published_at' => $createdAt,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];

            // 2 Câu hỏi Quiz 1
            $q1Id = $curQuestionId++;
            $q1VerId = $curQuestionVersionId++;
            $questionBatch[] = [
                'id' => $q1Id,
                'quiz_id' => $quiz1Id,
                'question' => "Ưu điểm nổi bật nhất của $subject trong môi trường doanh nghiệp là gì?",
                'type' => 'single',
                'points' => 50,
                'explanation' => "Kiến trúc và giải pháp của $subject giúp tối ưu hóa hiệu năng, giảm thiểu lỗi và nâng cao khả năng mở rộng hệ thống.",
                'sort_order' => 1,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
            $questionVersionBatch[] = [
                'id' => $q1VerId,
                'question_id' => $q1Id,
                'version' => 1,
                'question' => "Ưu điểm nổi bật nhất của $subject trong môi trường doanh nghiệp là gì?",
                'type' => 'single',
                'points' => 50,
                'explanation' => "Kiến trúc và giải pháp của $subject giúp tối ưu hóa hiệu năng, giảm thiểu lỗi và nâng cao khả năng mở rộng hệ thống.",
                'status' => 'published',
                'published_at' => $createdAt,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
            $quizVersionQuestionBatch[] = [
                'id' => $curQuizVersionQuestionId++,
                'quiz_version_id' => $quiz1VerId,
                'question_id' => $q1Id,
                'question_version_id' => $q1VerId,
                'sort_order' => 1,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
            $optionBatch[] = ['id' => $curOptionId++, 'quiz_question_id' => $q1Id, 'question_version_id' => $q1VerId, 'option_text' => "Tối ưu hóa hiệu năng, khả năng mở rộng cao và giảm thời gian triển khai", 'is_correct' => true, 'sort_order' => 1, 'created_at' => $createdAt, 'updated_at' => $createdAt];
            $optionBatch[] = ['id' => $curOptionId++, 'quiz_question_id' => $q1Id, 'question_version_id' => $q1VerId, 'option_text' => "Không cần bảo trì hay kiểm thử trước khi bàn giao", 'is_correct' => false, 'sort_order' => 2, 'created_at' => $createdAt, 'updated_at' => $createdAt];
            $optionBatch[] = ['id' => $curOptionId++, 'quiz_question_id' => $q1Id, 'question_version_id' => $q1VerId, 'option_text' => "Chỉ áp dụng được cho các bài toán thử nghiệm quy mô nhỏ", 'is_correct' => false, 'sort_order' => 3, 'created_at' => $createdAt, 'updated_at' => $createdAt];
            $optionBatch[] = ['id' => $curOptionId++, 'quiz_question_id' => $q1Id, 'question_version_id' => $q1VerId, 'option_text' => "Hoàn toàn không tương thích với các tiêu chuẩn quốc tế", 'is_correct' => false, 'sort_order' => 4, 'created_at' => $createdAt, 'updated_at' => $createdAt];

            $q2Id = $curQuestionId++;
            $q2VerId = $curQuestionVersionId++;
            $questionBatch[] = [
                'id' => $q2Id,
                'quiz_id' => $quiz1Id,
                'question' => "Yếu tố quan trọng nhất khi bắt đầu triển khai dự án về $subject là gì?",
                'type' => 'single',
                'points' => 50,
                'explanation' => "Việc thiết lập môi trường chuẩn chỉnh và hiểu rõ tài liệu kỹ thuật là bước đầu tiên để tránh lỗi tiềm ẩn.",
                'sort_order' => 2,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
            $questionVersionBatch[] = [
                'id' => $q2VerId,
                'question_id' => $q2Id,
                'version' => 1,
                'question' => "Yếu tố quan trọng nhất khi bắt đầu triển khai dự án về $subject là gì?",
                'type' => 'single',
                'points' => 50,
                'explanation' => "Việc thiết lập môi trường chuẩn chỉnh và hiểu rõ tài liệu kỹ thuật là bước đầu tiên để tránh lỗi tiềm ẩn.",
                'status' => 'published',
                'published_at' => $createdAt,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
            $quizVersionQuestionBatch[] = [
                'id' => $curQuizVersionQuestionId++,
                'quiz_version_id' => $quiz1VerId,
                'question_id' => $q2Id,
                'question_version_id' => $q2VerId,
                'sort_order' => 2,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
            $optionBatch[] = ['id' => $curOptionId++, 'quiz_question_id' => $q2Id, 'question_version_id' => $q2VerId, 'option_text' => "Thiết lập môi trường chuẩn xác và tuân thủ các quy tắc thiết kế (Best Practices)", 'is_correct' => true, 'sort_order' => 1, 'created_at' => $createdAt, 'updated_at' => $createdAt];
            $optionBatch[] = ['id' => $curOptionId++, 'quiz_question_id' => $q2Id, 'question_version_id' => $q2VerId, 'option_text' => "Bỏ qua việc đọc tài liệu hướng dẫn", 'is_correct' => false, 'sort_order' => 2, 'created_at' => $createdAt, 'updated_at' => $createdAt];
            $optionBatch[] = ['id' => $curOptionId++, 'quiz_question_id' => $q2Id, 'question_version_id' => $q2VerId, 'option_text' => "Không sao lưu mã nguồn và dữ liệu định kỳ", 'is_correct' => false, 'sort_order' => 3, 'created_at' => $createdAt, 'updated_at' => $createdAt];
            $optionBatch[] = ['id' => $curOptionId++, 'quiz_question_id' => $q2Id, 'question_version_id' => $q2VerId, 'option_text' => "Chỉ sử dụng mã nguồn không có nguồn gốc rõ ràng", 'is_correct' => false, 'sort_order' => 4, 'created_at' => $createdAt, 'updated_at' => $createdAt];

            // 3. Thêm Chương 2 & Section 2
            $chap2Id = $curChapterId++;
            $sec2Id = $curSectionId++;
            $chap2Title = "Chương 2: Thực Hành Dự Án Chuyên Sâu & Đánh Giá Tốt Nghiệp $subject";

            $chapterBatch[] = [
                'id' => $chap2Id,
                'course_id' => $courseId,
                'title' => $chap2Title,
                'sort_order' => 2,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];

            $sectionBatch[] = [
                'id' => $sec2Id,
                'course_id' => $courseId,
                'title' => $chap2Title,
                'description' => "Xây dựng các tình huống thực tế và dự án chuyên sâu về $subject",
                'sort_order' => 2,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];

            // Lesson 2.1: Video Clip MP4 Thật
            $les2_1Id = $curLessonId++;
            $mp4Url2 = $this->realMp4Videos[($i * 5 + 1) % $mp4Count];
            $lessonBatch[] = [
                'id' => $les2_1Id,
                'course_id' => $courseId,
                'section_id' => $sec2Id,
                'chapter_id' => $chap2Id,
                'title' => "Bài 2.1: Kỹ thuật xử lý chuyên sâu & Triển khai thực tế $subject",
                'type' => 'video',
                'video_url' => $mp4Url2,
                'content' => "<p>Video hướng dẫn từng bước kỹ thuật xử lý các tình huống phức tạp trong dự án thực tế về <strong>$subject</strong>.</p>",
                'document_file' => null,
                'duration' => 1080,
                'duration_seconds' => 1080,
                'is_preview' => false,
                'is_required' => true,
                'sort_order' => 1,
                'status' => 'published',
                'upload_status' => 'uploaded',
                'processing_status' => 'ready',
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];

            // Lesson 2.2: Tài liệu Source Code + Attachment File ZIP
            $les2_2Id = $curLessonId++;
            $zipPath2 = "demo/source/" . Str::slug($subject) . "-project-v1.zip";
            $zipTitle2 = "Source-Code-Du-An-" . Str::slug($subject) . ".zip";
            $lessonBatch[] = [
                'id' => $les2_2Id,
                'course_id' => $courseId,
                'section_id' => $sec2Id,
                'chapter_id' => $chap2Id,
                'title' => "Bài 2.2: Phân tích tình huống Case Study & Source Code thực hành",
                'type' => 'document',
                'video_url' => null,
                'content' => "### Phân tích Case Study Thực Tế\n\nTrong bài học này, bạn sẽ nhận được bộ **Source Code & Tài liệu mẫu** hoàn chỉnh của dự án.\n\n#### Hướng dẫn giải nén & chạy thử nghiệm:\n1. Tải file ZIP đính kèm phía dưới bài học.\n2. Giải nén vào thư mục làm việc của bạn.\n3. Làm theo file `README.md` đi kèm để chạy dự án và đối chiếu kết quả.",
                'document_file' => $zipPath2,
                'duration' => 600,
                'duration_seconds' => 600,
                'is_preview' => false,
                'is_required' => true,
                'sort_order' => 2,
                'status' => 'published',
                'upload_status' => 'completed',
                'processing_status' => 'completed',
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];

            // Lesson Attachment cho Bài 2.2
            $attachmentBatch[] = [
                'id' => $curAttachmentId++,
                'lesson_id' => $les2_2Id,
                'title' => $zipTitle2,
                'file_path' => $zipPath2,
                'file_type' => "application/zip",
                'file_size' => 15728640,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];

            // Lesson 2.3: Quizz đánh giá tốt nghiệp
            $les2_3Id = $curLessonId++;
            $lessonBatch[] = [
                'id' => $les2_3Id,
                'course_id' => $courseId,
                'section_id' => $sec2Id,
                'chapter_id' => $chap2Id,
                'title' => "Bài 2.3: Bài kiểm tra đánh giá năng lực & Tốt nghiệp: $subject",
                'type' => 'quiz',
                'video_url' => null,
                'content' => "Bài thi trắc nghiệm tổng kết hoàn thành khóa học để nhận chứng chỉ OnlineFEA.",
                'document_file' => null,
                'duration' => 1200,
                'duration_seconds' => 1200,
                'is_preview' => false,
                'is_required' => true,
                'sort_order' => 3,
                'status' => 'published',
                'upload_status' => 'completed',
                'processing_status' => 'completed',
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];

            // Tạo Quiz & QuizVersion cho Bài 2.3
            $quiz2Id = $curQuizId++;
            $quiz2VerId = $curQuizVersionId++;
            $quizBatch[] = [
                'id' => $quiz2Id,
                'lesson_id' => $les2_3Id,
                'title' => "Đánh giá năng lực tốt nghiệp: $subject",
                'description' => "Bài kiểm tra năng lực tổng kết khóa học $subject",
                'pass_score' => 80,
                'time_limit_minutes' => 20,
                'max_attempts' => 5,
                'question_count' => 2,
                'is_active' => true,
                'current_published_version_id' => $quiz2VerId,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];

            $quizVersionBatch[] = [
                'id' => $quiz2VerId,
                'quiz_id' => $quiz2Id,
                'version' => 1,
                'title' => "Đánh giá năng lực tốt nghiệp: $subject",
                'description' => "Bài kiểm tra năng lực tổng kết khóa học $subject",
                'pass_score' => 80,
                'time_limit_minutes' => 20,
                'max_attempts' => 5,
                'status' => 'published',
                'created_by' => $instructorId,
                'published_at' => $createdAt,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];

            // 2 Câu hỏi Quiz 2
            $q3Id = $curQuestionId++;
            $q3VerId = $curQuestionVersionId++;
            $questionBatch[] = [
                'id' => $q3Id,
                'quiz_id' => $quiz2Id,
                'question' => "Khi gặp sự cố vận hành trong thực tế với $subject, phương án xử lý tối ưu là gì?",
                'type' => 'single',
                'points' => 50,
                'explanation' => "Kiểm tra log hệ thống, cô lập lỗi và kiểm thử đơn vị trước khi deploy bản vá là quy trình chuẩn.",
                'sort_order' => 1,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
            $questionVersionBatch[] = [
                'id' => $q3VerId,
                'question_id' => $q3Id,
                'version' => 1,
                'question' => "Khi gặp sự cố vận hành trong thực tế với $subject, phương án xử lý tối ưu là gì?",
                'type' => 'single',
                'points' => 50,
                'explanation' => "Kiểm tra log hệ thống, cô lập lỗi và kiểm thử đơn vị trước khi deploy bản vá là quy trình chuẩn.",
                'status' => 'published',
                'published_at' => $createdAt,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
            $quizVersionQuestionBatch[] = [
                'id' => $curQuizVersionQuestionId++,
                'quiz_version_id' => $quiz2VerId,
                'question_id' => $q3Id,
                'question_version_id' => $q3VerId,
                'sort_order' => 1,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
            $optionBatch[] = ['id' => $curOptionId++, 'quiz_question_id' => $q3Id, 'question_version_id' => $q3VerId, 'option_text' => "Kiểm tra log chi tiết, cô lập phạm vi lỗi và áp dụng bản vá đã qua kiểm thử", 'is_correct' => true, 'sort_order' => 1, 'created_at' => $createdAt, 'updated_at' => $createdAt];
            $optionBatch[] = ['id' => $curOptionId++, 'quiz_question_id' => $q3Id, 'question_version_id' => $q3VerId, 'option_text' => "Xóa bỏ toàn bộ dữ liệu người dùng", 'is_correct' => false, 'sort_order' => 2, 'created_at' => $createdAt, 'updated_at' => $createdAt];
            $optionBatch[] = ['id' => $curOptionId++, 'quiz_question_id' => $q3Id, 'question_version_id' => $q3VerId, 'option_text' => "Tắt hệ thống cảnh báo để không thấy thông báo lỗi", 'is_correct' => false, 'sort_order' => 3, 'created_at' => $createdAt, 'updated_at' => $createdAt];
            $optionBatch[] = ['id' => $curOptionId++, 'quiz_question_id' => $q3Id, 'question_version_id' => $q3VerId, 'option_text' => "Thay đổi cấu hình production mà không kiểm tra môi trường staging", 'is_correct' => false, 'sort_order' => 4, 'created_at' => $createdAt, 'updated_at' => $createdAt];

            $q4Id = $curQuestionId++;
            $q4VerId = $curQuestionVersionId++;
            $questionBatch[] = [
                'id' => $q4Id,
                'quiz_id' => $quiz2Id,
                'question' => "Tiêu chí nào thể hiện một dự án $subject đã sẵn sàng đưa lên môi trường Production?",
                'type' => 'single',
                'points' => 50,
                'explanation' => "Hệ thống cần vượt qua toàn bộ test case, được cấu hình bảo mật, logging và tối ưu hóa hiệu năng.",
                'sort_order' => 2,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
            $questionVersionBatch[] = [
                'id' => $q4VerId,
                'question_id' => $q4Id,
                'version' => 1,
                'question' => "Tiêu chí nào thể hiện một dự án $subject đã sẵn sàng đưa lên môi trường Production?",
                'type' => 'single',
                'points' => 50,
                'explanation' => "Hệ thống cần vượt qua toàn bộ test case, được cấu hình bảo mật, logging và tối ưu hóa hiệu năng.",
                'status' => 'published',
                'published_at' => $createdAt,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
            $quizVersionQuestionBatch[] = [
                'id' => $curQuizVersionQuestionId++,
                'quiz_version_id' => $quiz2VerId,
                'question_id' => $q4Id,
                'question_version_id' => $q4VerId,
                'sort_order' => 2,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
            $optionBatch[] = ['id' => $curOptionId++, 'quiz_question_id' => $q4Id, 'question_version_id' => $q4VerId, 'option_text' => "Đạt đầy đủ tiêu chuẩn bảo mật, kiểm thử tự động đạt chuẩn và có kế hoạch rollback", 'is_correct' => true, 'sort_order' => 1, 'created_at' => $createdAt, 'updated_at' => $createdAt];
            $optionBatch[] = ['id' => $curOptionId++, 'quiz_question_id' => $q4Id, 'question_version_id' => $q4VerId, 'option_text' => "Chỉ cần viết xong code không cần kiểm tra bảo mật", 'is_correct' => false, 'sort_order' => 2, 'created_at' => $createdAt, 'updated_at' => $createdAt];
            $optionBatch[] = ['id' => $curOptionId++, 'quiz_question_id' => $q4Id, 'question_version_id' => $q4VerId, 'option_text' => "Không cần thiết lập cơ chế giám sát hay logging", 'is_correct' => false, 'sort_order' => 3, 'created_at' => $createdAt, 'updated_at' => $createdAt];
            $optionBatch[] = ['id' => $curOptionId++, 'quiz_question_id' => $q4Id, 'question_version_id' => $q4VerId, 'option_text' => "Chạy trên máy local của developer là đủ", 'is_correct' => false, 'sort_order' => 4, 'created_at' => $createdAt, 'updated_at' => $createdAt];

            // Chèn theo Chunk vào Cơ sở dữ liệu
            if (count($courseBatch) >= $chunkSize) {
                $this->flushBatches(
                    $courseBatch, $chapterBatch, $sectionBatch, $lessonBatch,
                    $attachmentBatch, $quizBatch, $quizVersionBatch,
                    $questionBatch, $questionVersionBatch, $quizVersionQuestionBatch, $optionBatch
                );

                $totalInserted += count($courseBatch);
                $courseBatch = $chapterBatch = $sectionBatch = $lessonBatch = [];
                $attachmentBatch = $quizBatch = $quizVersionBatch = [];
                $questionBatch = $questionVersionBatch = $quizVersionQuestionBatch = $optionBatch = [];

                if ($totalInserted % 2000 === 0 || $totalInserted === $targetTotal) {
                    $elapsed = round(microtime(true) - $startTime, 2);
                    $percent = round(($totalInserted / $targetTotal) * 100, 1);
                    $log("▶ Đã nạp thành công $totalInserted / $targetTotal khóa học kèm giáo trình ($percent%) - Thời gian: {$elapsed}s");
                }
            }
        }

        if (!empty($courseBatch)) {
            $this->flushBatches(
                $courseBatch, $chapterBatch, $sectionBatch, $lessonBatch,
                $attachmentBatch, $quizBatch, $quizVersionBatch,
                $questionBatch, $questionVersionBatch, $quizVersionQuestionBatch, $optionBatch
            );
            $totalInserted += count($courseBatch);
        }

        $totalTime = round(microtime(true) - $startTime, 2);
        $log("\n=========================================================================");
        $log("✓ HOÀN THÀNH NẠP THÀNH CÔNG $totalInserted KHÓA HỌC KÈM ĐẦY ĐỦ BÀI HỌC, VIDEO MP4, TÀI LIỆU & QUIZZES TRONG {$totalTime}s!");
        $log("=========================================================================\n");
    }

    private function flushBatches(
        array &$courseBatch, array &$chapterBatch, array &$sectionBatch, array &$lessonBatch,
        array &$attachmentBatch, array &$quizBatch, array &$quizVersionBatch,
        array &$questionBatch, array &$questionVersionBatch, array &$quizVersionQuestionBatch, array &$optionBatch
    ): void {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        if (!empty($courseBatch)) {
            DB::table('courses')->insert($courseBatch);
        }
        if (!empty($chapterBatch)) {
            DB::table('chapters')->insert($chapterBatch);
        }
        if (!empty($sectionBatch)) {
            DB::table('course_sections')->insert($sectionBatch);
        }
        if (!empty($lessonBatch)) {
            DB::table('lessons')->insert($lessonBatch);
        }
        if (!empty($attachmentBatch)) {
            DB::table('lesson_attachments')->insert($attachmentBatch);
        }
        if (!empty($quizBatch)) {
            DB::table('quizzes')->insert($quizBatch);
        }
        if (!empty($quizVersionBatch)) {
            DB::table('quiz_versions')->insert($quizVersionBatch);
        }
        if (!empty($questionBatch)) {
            DB::table('quiz_questions')->insert($questionBatch);
        }
        if (!empty($questionVersionBatch)) {
            DB::table('question_versions')->insert($questionVersionBatch);
        }
        if (!empty($quizVersionQuestionBatch)) {
            DB::table('quiz_version_questions')->insert($quizVersionQuestionBatch);
        }
        if (!empty($optionBatch)) {
            DB::table('quiz_options')->insert($optionBatch);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
