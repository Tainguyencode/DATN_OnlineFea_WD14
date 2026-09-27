<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\InstructorApplication;
use App\Models\InstructorProfile;
use App\Models\Role;
use App\Models\User;
use App\Services\RoleSyncService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        app(RoleSyncService::class)->ensurePrimaryRolesExist();
        $this->ensureSuperAdminRoleExists();

        $defaultHashedPassword = Hash::make('password');

        // =========================================================================
        // 1. QUẢN TRỊ VIÊN HỆ THỐNG (ADMINS)
        // =========================================================================
        $admins = [
            [
                'name' => 'Hệ thống Admin',
                'username' => 'admin',
                'email' => 'admin@example.com',
                'role' => 'admin',
                'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200',
                'bio' => 'Quản trị viên tối cao của hệ thống LMS OnlineFEA.',
                'phone' => '0912345678',
            ],
            [
                'name' => 'Trần Bảo An',
                'username' => 'admin_an',
                'email' => 'admin.an@example.com',
                'role' => 'admin',
                'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200',
                'bio' => 'Chuyên viên quản trị nội dung và kiểm duyệt khóa học.',
                'phone' => '0912345679',
            ],
        ];

        foreach ($admins as $adminData) {
            $user = User::where('email', $adminData['email'])
                ->orWhere('username', $adminData['username'])
                ->first() ?? new User();

            $user->fill([
                'name' => $adminData['name'],
                'username' => $adminData['username'],
                'email' => $adminData['email'],
                'role' => 'admin',
                'avatar' => $adminData['avatar'],
                'bio' => $adminData['bio'],
                'phone' => $adminData['phone'],
                'password' => $defaultHashedPassword,
                'is_active' => true,
                'account_status' => 'active',
                'email_verified_at' => now(),
                'two_factor_enabled' => false,
            ]);
            $user->save();
            $this->assignRole($user, 'admin');
        }

        $superAdminId = User::where('role', 'admin')->orderBy('id')->value('id') ?? 1;

        // =========================================================================
        // 2. DANH SÁCH GIẢNG VIÊN CHUYÊN GIA (25+ INSTRUCTORS)
        // =========================================================================
        $instructors = [
            [
                'name' => 'Nguyễn Văn Giảng',
                'username' => 'instructor',
                'email' => 'instructor@example.com',
                'avatar' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=250',
                'bio' => 'Giảng viên cấp cao với hơn 10 năm kinh nghiệm trong ngành lập trình Web Fullstack, Laravel và kiến trúc hệ thống phân tán.',
                'phone' => '0987654321',
                'position' => 'Senior Fullstack Architect',
                'specialty' => 'Laravel Framework, Vue.js, Clean Architecture',
                'organization' => 'Học viện Đào tạo OnlineFEA',
                'experience' => '10 năm kinh nghiệm phát triển phần mềm doanh nghiệp',
                'category_name' => 'Phát triển Web',
                'bank_code' => 'VCB',
                'bank_name' => 'Vietcombank',
                'bank_account_number' => '1012345678',
            ],
            [
                'name' => 'Trần Đức Dũng',
                'username' => 'instructor2',
                'email' => 'instructor2@example.com',
                'avatar' => 'https://images.unsplash.com/photo-1570295999919-56ceb5ecca61?w=250',
                'bio' => 'Chuyên gia thiết kế giao diện UI/UX và thiết kế hệ thống sản phẩm số với Figma, Design System chuẩn chỉnh.',
                'phone' => '0977665544',
                'position' => 'Lead UI/UX Designer',
                'specialty' => 'Figma Prototype, Design System, UX Research',
                'organization' => 'Figma Design Studio Vietnam',
                'experience' => '8 năm thiết kế giao diện ứng dụng web & mobile',
                'category_name' => 'Thiết kế UI/UX',
                'bank_code' => 'TCB',
                'bank_name' => 'Techcombank',
                'bank_account_number' => '1903456789',
            ],
            [
                'name' => 'TS. Hoàng Văn Tiến',
                'username' => 'instructor3',
                'email' => 'tien.datascience@example.com',
                'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=250',
                'bio' => 'Trưởng lab nghiên cứu Trí tuệ nhân tạo và Phân tích dữ liệu lớn với 12 năm kinh nghiệm giảng dạy đại học.',
                'phone' => '0981112233',
                'position' => 'Trưởng phòng Nghiên cứu AI',
                'specialty' => 'Python, Machine Learning, Deep Learning, Big Data',
                'organization' => 'Viện Nghiên cứu Trí tuệ Nhân tạo',
                'experience' => '12 năm nghiên cứu và ứng dụng AI/ML vào tài chính',
                'category_name' => 'Khoa học dữ liệu',
                'bank_code' => 'MB',
                'bank_name' => 'MBBank',
                'bank_account_number' => '0981112233',
            ],
            [
                'name' => 'Trịnh Đình Long',
                'username' => 'instructor4',
                'email' => 'long.marketing@example.com',
                'avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=250',
                'bio' => 'Giám đốc Brand & Performance Marketing tại tập đoàn công nghệ đa quốc gia, chuyên gia tối ưu hóa SEO & Ads.',
                'phone' => '0982223344',
                'position' => 'Head of Digital Marketing',
                'specialty' => 'SEO Onpage/Offpage, Facebook & TikTok Performance Ads',
                'organization' => 'Growth Hackers Media',
                'experience' => '9 năm quản lý ngân sách quảng cáo triệu USD',
                'category_name' => 'Digital Marketing',
                'bank_code' => 'ACB',
                'bank_name' => 'ACB',
                'bank_account_number' => '2233445566',
            ],
            [
                'name' => 'Thầy Tú Phạm',
                'username' => 'instructor5',
                'email' => 'tu.ielts@example.com',
                'avatar' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=250',
                'bio' => 'Chuyên gia luyện thi IELTS 8.5, cựu sinh viên Đại học Oxford với 8 năm giảng dạy tiếng Anh học thuật.',
                'phone' => '0983334455',
                'position' => 'Senior IELTS Master Trainer',
                'specialty' => 'IELTS Academic Writing, Speaking, Business Communication',
                'organization' => 'Oxford English Academy',
                'experience' => '8 năm đào tạo hơn 3,000 học viên đạt 7.0+ IELTS',
                'category_name' => 'Ngoại ngữ',
                'bank_code' => 'VCB',
                'bank_name' => 'Vietcombank',
                'bank_account_number' => '1023456789',
            ],
            [
                'name' => 'Phạm Nhật Minh',
                'username' => 'instructor6',
                'email' => 'minh.reactnative@example.com',
                'avatar' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=250',
                'bio' => 'Senior Mobile Developer chuyên trách React Native & Flutter cho các dự án FinTech hàng đầu.',
                'phone' => '0984445566',
                'position' => 'Senior Mobile Architect',
                'specialty' => 'Flutter, Dart, React Native, iOS & Android',
                'organization' => 'FinTech Innovations Lab',
                'experience' => '7 năm xây dựng ứng dụng di động hàng triệu lượt tải',
                'category_name' => 'Phát triển ứng dụng Mobile',
                'bank_code' => 'BIDV',
                'bank_name' => 'BIDV',
                'bank_account_number' => '3344556677',
            ],
            [
                'name' => 'Nguyễn Thành Nam',
                'username' => 'nam_uiux',
                'email' => 'nam.uiux@example.com',
                'avatar' => 'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?w=250',
                'bio' => 'Giảng viên UI/UX tập trung vào nghiên cứu người dùng, thiết kế tương tác và tối ưu trải nghiệm khách hàng.',
                'phone' => '0984445572',
                'position' => 'Senior Product Designer',
                'specialty' => 'User Journey, Wireframing, Usability Testing, Figma',
                'organization' => 'Creative UX Agency',
                'experience' => '6 năm dẫn dắt các dự án thiết kế ứng dụng thương mại điện tử',
                'category_name' => 'Thiết kế UI/UX',
                'bank_code' => 'TCB',
                'bank_name' => 'Techcombank',
                'bank_account_number' => '1905566778',
            ],
            [
                'name' => 'Đặng Thanh Tùng',
                'username' => 'tung_data',
                'email' => 'tung.data@example.com',
                'avatar' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=250',
                'bio' => 'Giảng viên Data Science chuyên về Python, trực quan hóa dữ liệu và Machine Learning ứng dụng thực tế.',
                'phone' => '0984445573',
                'position' => 'Lead Data Scientist',
                'specialty' => 'Python, Pandas, NumPy, Scikit-Learn, Power BI',
                'organization' => 'Data Analytics Corporation',
                'experience' => '8 năm phân tích dữ liệu thị trường và hành vi tiêu dùng',
                'category_name' => 'Khoa học dữ liệu',
                'bank_code' => 'MB',
                'bank_name' => 'MBBank',
                'bank_account_number' => '0984445573',
            ],
            [
                'name' => 'Hà Tuấn Khang',
                'username' => 'khang_seo',
                'email' => 'khang.seo@example.com',
                'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=250',
                'bio' => 'Giảng viên SEO và Content Marketing, chuyên xây dựng chiến lược tăng trưởng traffic tự nhiên bền vững.',
                'phone' => '0984445574',
                'position' => 'SEO Director',
                'specialty' => 'Technical SEO, Content Strategy, Google Search Console',
                'organization' => 'Top SEO Vietnam',
                'experience' => '7 năm đẩy hàng trăm từ khóa cạnh tranh cao vào Top 1 Google',
                'category_name' => 'Digital Marketing',
                'bank_code' => 'VCB',
                'bank_name' => 'Vietcombank',
                'bank_account_number' => '1034567890',
            ],
            [
                'name' => 'Ms. Lê Mai Hoa',
                'username' => 'hoa_english',
                'email' => 'hoa.english@example.com',
                'avatar' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=250',
                'bio' => 'Giảng viên tiếng Anh chuyên giao tiếp học thuật, tiếng Anh thương mại và phát âm chuẩn quốc tế.',
                'phone' => '0984445575',
                'position' => 'Head of Business English',
                'specialty' => 'Business English, Pronunciation, Workplace Presentation',
                'organization' => 'Global English Academy',
                'experience' => '9 năm đào tạo tiếng Anh doanh nghiệp cho các tập đoàn đa quốc gia',
                'category_name' => 'Ngoại ngữ',
                'bank_code' => 'ACB',
                'bank_name' => 'ACB',
                'bank_account_number' => '2244668800',
            ],
            [
                'name' => 'Vũ Minh Đức',
                'username' => 'duc_cloud',
                'email' => 'duc.cloud@example.com',
                'avatar' => 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=250',
                'bio' => 'Chuyên gia DevOps & SRE Hạ tầng Đám mây AWS, Docker, Kubernetes với kinh nghiệm vận hành hệ thống tải hàng triệu CCU.',
                'phone' => '0984445576',
                'position' => 'Principal DevOps Architect',
                'specialty' => 'Docker, Kubernetes, AWS, CI/CD, Terraform, Grafana',
                'organization' => 'CloudNative Vietnam',
                'experience' => '11 năm thiết kế hạ tầng điện toán đám mây quy mô lớn',
                'category_name' => 'DevOps',
                'bank_code' => 'TCB',
                'bank_name' => 'Techcombank',
                'bank_account_number' => '1908877665',
            ],
            [
                'name' => 'Lê Tuấn Anh',
                'username' => 'anh_security',
                'email' => 'anh.security@example.com',
                'avatar' => 'https://images.unsplash.com/photo-1501196354995-cbb51c65aaea?w=250',
                'bio' => 'Chuyên gia An toàn thông tin & Penetration Testing, nắm giữ các chứng chỉ quốc tế CISSP, CEH, OSCP.',
                'phone' => '0984445577',
                'position' => 'Cyber Security Specialist',
                'specialty' => 'Web Security, OWASP Top 10, Ethical Hacking, Network Security',
                'organization' => 'CyberShield Labs',
                'experience' => '8 năm săn lỗi bảo mật Bug Bounty và cố vấn bảo mật hệ thống ngân hàng',
                'category_name' => 'An ninh mạng',
                'bank_code' => 'MB',
                'bank_name' => 'MBBank',
                'bank_account_number' => '0984445577',
            ],
            [
                'name' => 'Trần Thu Phương',
                'username' => 'phuong_frontend',
                'email' => 'phuong.frontend@example.com',
                'avatar' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=250',
                'bio' => 'Senior Frontend Engineer với đam mê sâu sắc về React 18, Next.js 14, TypeScript và tối ưu hiệu năng Web Vitals.',
                'phone' => '0984445578',
                'position' => 'Frontend Tech Lead',
                'specialty' => 'React.js, Next.js, TypeScript, TailwindCSS, Web Performance',
                'organization' => 'NextGen Software Corp',
                'experience' => '7 năm xây dựng các ứng dụng Single Page App và Server-Side Rendering',
                'category_name' => 'Phát triển Web',
                'bank_code' => 'VCB',
                'bank_name' => 'Vietcombank',
                'bank_account_number' => '1045678901',
            ],
            [
                'name' => 'Đỗ Hồng Sơn',
                'username' => 'son_backend',
                'email' => 'son.backend@example.com',
                'avatar' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=250',
                'bio' => 'Chuyên gia kiến trúc phần mềm Backend Java Spring Boot 3, NestJS, gRPC và kiến trúc Microservices phân tán.',
                'phone' => '0984445579',
                'position' => 'Backend Architect',
                'specialty' => 'Java Spring Boot, Microservices, RabbitMQ, Kafka, gRPC',
                'organization' => 'Enterprise Architecture Group',
                'experience' => '10 năm phụ trách các hệ thống core banking và xử lý giao dịch thời gian thực',
                'category_name' => 'Ngôn ngữ lập trình',
                'bank_code' => 'BIDV',
                'bank_name' => 'BIDV',
                'bank_account_number' => '3355779911',
            ],
            [
                'name' => 'Nguyễn Quang Huy',
                'username' => 'huy_flutter',
                'email' => 'huy.flutter@example.com',
                'avatar' => 'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?w=250',
                'bio' => 'Chuyên gia Lập trình Flutter & BLoC Pattern, Clean Architecture, tác giả nhiều package mã nguồn mở phổ biến.',
                'phone' => '0984445580',
                'position' => 'Senior Flutter Engineer',
                'specialty' => 'Flutter, Dart, Clean Architecture, BLoC, Firebase',
                'organization' => 'Flutter Mobile Labs',
                'experience' => '6 năm phát triển ứng dụng di động cho các startup công nghệ Singapore & Mỹ',
                'category_name' => 'Phát triển ứng dụng Mobile',
                'bank_code' => 'TCB',
                'bank_name' => 'Techcombank',
                'bank_account_number' => '1909988776',
            ],
            [
                'name' => 'Bùi Thành Trung',
                'username' => 'trung_golang',
                'email' => 'trung.golang@example.com',
                'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=250',
                'bio' => 'Senior Backend Developer chuyên sâu ngôn ngữ Go (Golang), xử lý đồng thời Concurrency, tối ưu hóa bộ nhớ và mạng.',
                'phone' => '0984445581',
                'position' => 'Senior Go Engineer',
                'specialty' => 'Golang, High Concurrency, Goroutines, Redis, Gin Gonic',
                'organization' => 'HighLoad Tech Systems',
                'experience' => '8 năm xây dựng dịch vụ thanh toán và gateway tốc độ cao',
                'category_name' => 'Ngôn ngữ lập trình',
                'bank_code' => 'VCB',
                'bank_name' => 'Vietcombank',
                'bank_account_number' => '1056789012',
            ],
            [
                'name' => 'Phan Minh Hiếu',
                'username' => 'hieu_game',
                'email' => 'hieu.game@example.com',
                'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=250',
                'bio' => 'Lead Game Developer với hơn 8 năm làm việc tại các studio game quốc tế, chuyên Unity 3D, C# và đồ họa Shader.',
                'phone' => '0984445582',
                'position' => 'Lead Game Developer',
                'specialty' => 'Unity 3D, C#, Physics Engine, Shader Graph, Mobile Games',
                'organization' => 'Pixel Studio Games',
                'experience' => '8 năm xuất bản hơn 15 tựa game 2D/3D lên App Store & Google Play',
                'category_name' => 'Lập trình Game',
                'bank_code' => 'MB',
                'bank_name' => 'MBBank',
                'bank_account_number' => '0984445582',
            ],
            [
                'name' => 'Nguyễn Phương Thảo',
                'username' => 'thao_qa',
                'email' => 'thao.qa@example.com',
                'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=250',
                'bio' => 'Senior Quality Assurance & Automation Lead với chuyên môn cao về Playwright, Cypress, Selenium và Performance Test.',
                'phone' => '0984445583',
                'position' => 'QA Automation Lead',
                'specialty' => 'Playwright, Cypress, Selenium, JMeter, CI/CD Integration',
                'organization' => 'Software Quality Assurance Vietnam',
                'experience' => '7 năm xây dựng khung kiểm thử tự động toàn diện cho các dự án ERP lớn',
                'category_name' => 'Kiểm thử phần mềm',
                'bank_code' => 'ACB',
                'bank_name' => 'ACB',
                'bank_account_number' => '2255779911',
            ],
            [
                'name' => 'Võ Nhật Quang',
                'username' => 'quang_db',
                'email' => 'quang.db@example.com',
                'avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=250',
                'bio' => 'Database Administrator (DBA) & Data Architect với 10 năm tối ưu hóa MySQL, PostgreSQL và kiến trúc NoSQL.',
                'phone' => '0984445584',
                'position' => 'Principal Database Administrator',
                'specialty' => 'MySQL Tuning, PostgreSQL, Indexing Optimization, Sharding, Redis',
                'organization' => 'Database Performance Lab',
                'experience' => '10 năm quản trị hệ thống CSDL Terabytes trong lĩnh vực thương mại điện tử',
                'category_name' => 'Cơ sở dữ liệu',
                'bank_code' => 'TCB',
                'bank_name' => 'Techcombank',
                'bank_account_number' => '1901122334',
            ],
            [
                'name' => 'Lê Tuyết Mai',
                'username' => 'mai_finance',
                'email' => 'mai.finance@example.com',
                'avatar' => 'https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?w=250',
                'bio' => 'Chuyên gia Phân tích Tài chính Doanh nghiệp (CFA Charterholder), cựu Giám đốc Tài chính công ty chứng khoán.',
                'phone' => '0984445585',
                'position' => 'Senior Financial Advisor',
                'specialty' => 'Phân tích BCTC, Định giá Doanh nghiệp, Đầu tư Chứng khoán',
                'organization' => 'Capital Financial Partners',
                'experience' => '12 năm quản lý danh mục đầu tư và tư vấn tài chính doanh nghiệp',
                'category_name' => 'Tài chính doanh nghiệp',
                'bank_code' => 'VCB',
                'bank_name' => 'Vietcombank',
                'bank_account_number' => '1067890123',
            ],
            [
                'name' => 'Hoàng Quốc Khánh',
                'username' => 'khanh_business',
                'email' => 'khanh.business@example.com',
                'avatar' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=250',
                'bio' => 'Cố vấn Khởi nghiệp đổi mới sáng tạo, chuyên gia Quản trị dự án Agile Scrum (PMP, PMI-ACP Certified).',
                'phone' => '0984445586',
                'position' => 'Agile Coach & Startup Mentor',
                'specialty' => 'Agile Scrum, Lean Startup, Xây dựng MVP, Quản trị Dự án',
                'organization' => 'Vietnam Startup Incubator',
                'experience' => '11 năm ươm tạo và đồng hành cùng hơn 40 dự án startup công nghệ',
                'category_name' => 'Quản trị doanh nghiệp',
                'bank_code' => 'MB',
                'bank_name' => 'MBBank',
                'bank_account_number' => '0984445586',
            ],
            [
                'name' => 'Trương Tuấn Văn',
                'username' => 'van_media',
                'email' => 'van.media@example.com',
                'avatar' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=250',
                'bio' => 'Đạo diễn hình ảnh và chuyên gia dựng phim với Adobe Premiere Pro, After Effects, DaVinci Resolve.',
                'phone' => '0984445587',
                'position' => 'Creative Video Director',
                'specialty' => 'Premiere Pro, After Effects, Motion Graphics, Color Grading',
                'organization' => 'Apex Media Production',
                'experience' => '8 năm sản xuất video quảng cáo TVC và khóa học trực tuyến',
                'category_name' => 'Dựng và chỉnh sửa video',
                'bank_code' => 'BIDV',
                'bank_name' => 'BIDV',
                'bank_account_number' => '3366880022',
            ],
            [
                'name' => 'Phạm Ngọc Lan',
                'username' => 'lan_office',
                'email' => 'lan.office@example.com',
                'avatar' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=250',
                'bio' => 'Chuyên gia Microsoft Office & Power BI Master Trainer, chuyên tự động hóa báo cáo và trực quan hóa Dashboard.',
                'phone' => '0984445588',
                'position' => 'BI & Analytics Specialist',
                'specialty' => 'Power BI, DAX, Advanced Excel, Power Query, VBA Dashboard',
                'organization' => 'Smart Office Solutions',
                'experience' => '9 năm đào tạo và tối ưu hóa quy trình báo cáo dữ liệu cho các tổng công ty',
                'category_name' => 'Power BI',
                'bank_code' => 'ACB',
                'bank_name' => 'ACB',
                'bank_account_number' => '2266880022',
            ],
            [
                'name' => 'Ngô Mạnh Cường',
                'username' => 'cuong_ecommerce',
                'email' => 'cuong.ecommerce@example.com',
                'avatar' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=250',
                'bio' => 'Chuyên gia xây dựng và vận hành chuỗi gian hàng Thương mại điện tử Shopee, TikTok Shop, Lazada.',
                'phone' => '0984445589',
                'position' => 'E-Commerce Growth Director',
                'specialty' => 'Thương mại điện tử, TikTok Shop Livestream, Shopee Mall, Dropshipping',
                'organization' => 'E-Commerce Success Academy',
                'experience' => '7 năm xây dựng các thương hiệu đạt doanh số triệu đơn hàng',
                'category_name' => 'Thương mại điện tử',
                'bank_code' => 'TCB',
                'bank_name' => 'Techcombank',
                'bank_account_number' => '1902233445',
            ],
            [
                'name' => 'Dương Hải Yến',
                'username' => 'yen_hr',
                'email' => 'yen.hr@example.com',
                'avatar' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=250',
                'bio' => 'Giám đốc Nhân sự & Chuyên gia Kỹ năng Lãnh đạo, Đàm phán và Phát triển Đội ngũ hiệu suất cao.',
                'phone' => '0984445590',
                'position' => 'HR Director & Executive Coach',
                'specialty' => 'Kỹ năng lãnh đạo, Quản trị nhân sự, Đàm phán, Kỹ năng giao tiếp',
                'organization' => 'People First Consulting',
                'experience' => '12 năm tư vấn quản trị nhân lực và phát triển văn hóa doanh nghiệp',
                'category_name' => 'Kỹ năng lãnh đạo',
                'bank_code' => 'VCB',
                'bank_name' => 'Vietcombank',
                'bank_account_number' => '1078901234',
            ],
        ];

        foreach ($instructors as $instData) {
            $user = User::where('email', $instData['email'])
                ->orWhere('username', $instData['username'])
                ->first() ?? new User();

            $user->fill([
                'name' => $instData['name'],
                'username' => $instData['username'],
                'email' => $instData['email'],
                'role' => 'instructor',
                'avatar' => $instData['avatar'],
                'bio' => $instData['bio'],
                'phone' => $instData['phone'],
                'password' => $defaultHashedPassword,
                'is_active' => true,
                'account_status' => 'active',
                'instructor_status' => 'approved',
                'commission_rate' => 80.00,
                'bank_code' => $instData['bank_code'],
                'bank_name' => $instData['bank_name'],
                'bank_account_number' => $instData['bank_account_number'],
                'bank_account_name' => mb_strtoupper(Str::ascii($instData['name'])),
                'submitted_for_review_at' => now()->subMonths(6),
                'approved_at' => now()->subMonths(6)->addDays(1),
                'approved_by' => $superAdminId,
                'email_verified_at' => now(),
                'two_factor_enabled' => false,
            ]);
            $user->save();

            $this->assignRole($user, 'instructor');

            // Tìm Category ID tương ứng
            $category = Category::where('name', $instData['category_name'])->first();
            $categoryId = $category?->id ?? 1;

            // Cập nhật InstructorProfile
            $profile = InstructorProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'category_id' => $categoryId,
                    'bio' => $instData['bio'],
                    'experience' => $instData['experience'],
                    'position' => $instData['position'],
                    'specialty' => $instData['specialty'],
                    'organization' => $instData['organization'],
                    'phone' => $instData['phone'],
                    'website_url' => 'https://onlinefea.edu.vn/instructors/' . $instData['username'],
                    'github_url' => 'https://github.com/' . $instData['username'],
                    'linkedin_url' => 'https://linkedin.com/in/' . $instData['username'],
                    'teaching_field' => $instData['position'],
                    'agree_information' => true,
                    'agree_terms' => true,
                ]
            );

            // Liên kết Danh mục giảng dạy chuyên môn (teachingCategories)
            $profile->syncTeachingCategories([$categoryId], $categoryId);

            // Cập nhật Đơn xét duyệt Giảng viên (InstructorApplication)
            InstructorApplication::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'expertise' => $instData['specialty'],
                    'experience' => $instData['experience'],
                    'introduction' => $instData['bio'],
                    'cv_path' => 'demo/cv/' . $instData['username'] . '_cv.pdf',
                    'certificate_path' => 'demo/certificates/' . $instData['username'] . '_cert.pdf',
                    'bank_name' => $instData['bank_name'],
                    'bank_account_number' => $instData['bank_account_number'],
                    'bank_account_name' => mb_strtoupper(Str::ascii($instData['name'])),
                    'status' => 'approved',
                    'admin_notes' => 'Hồ sơ chuyên môn đầy đủ, đạt tiêu chuẩn giảng viên cao cấp của OnlineFEA.',
                    'reviewed_by' => $superAdminId,
                    'reviewed_at' => now()->subMonths(6)->addDays(1),
                ]
            );
        }

        // =========================================================================
        // 3. DANH SÁCH HỌC VIÊN CHUẨN (30+ STUDENTS)
        // =========================================================================
        $students = [
            ['name' => 'Trần Thị Học', 'username' => 'student', 'email' => 'student@example.com', 'bio' => 'Học viên đam mê lập trình web và khoa học dữ liệu.', 'phone' => '0966554433'],
            ['name' => 'Lê Văn Học', 'username' => 'student2', 'email' => 'student2@example.com', 'bio' => 'Học viên đam mê lập trình di động Flutter và Android.', 'phone' => '0966554444'],
            ['name' => 'Phạm Minh Tuấn', 'username' => 'student3', 'email' => 'student3@example.com', 'bio' => 'Sinh viên CNTT năm cuối đang chuẩn bị đồ án tốt nghiệp.', 'phone' => '0966554455'],
            ['name' => 'Nguyễn Thị Mai', 'username' => 'student4', 'email' => 'student4@example.com', 'bio' => 'Học viên muốn chuyển ngành sang lập trình Frontend.', 'phone' => '0966554466'],
            ['name' => 'Hoàng Văn Nam', 'username' => 'student5', 'email' => 'student5@example.com', 'bio' => 'Học viên quan tâm đến bảo mật và an toàn thông tin.', 'phone' => '0966554477'],
            ['name' => 'Vũ Thị Hoa', 'username' => 'student6', 'email' => 'student6@example.com', 'bio' => 'Sinh viên học thiết kế đồ họa muốn tìm hiểu thêm về UI/UX.', 'phone' => '0966554488'],
            ['name' => 'Đỗ Minh Khang', 'username' => 'student7', 'email' => 'student7@example.com', 'bio' => 'Học viên đam mê xây dựng hệ thống phần mềm doanh nghiệp.', 'phone' => '0966554499'],
            ['name' => 'Qtrung', 'username' => 'qtrung', 'email' => 'tungazquoc@gmail.com', 'bio' => 'Học viên QTrung - Trải nghiệm hệ thống OnlineFEA.', 'phone' => '0123456789'],
            ['name' => 'Bùi Đức Anh', 'username' => 'student8', 'email' => 'anh.bui@example.com', 'bio' => 'Học viên theo học lộ trình DevOps và Cloud Computing.', 'phone' => '0966554501'],
            ['name' => 'Phan Thùy Linh', 'username' => 'student9', 'email' => 'linh.phan@example.com', 'bio' => 'Sinh viên ngành Marketing muốn trau dồi kỹ năng Digital Ads.', 'phone' => '0966554502'],
            ['name' => 'Đặng Hải Đăng', 'username' => 'student10', 'email' => 'dang.dang@example.com', 'bio' => 'Kỹ sư tự động hóa chuyển hướng học Data Science.', 'phone' => '0966554503'],
            ['name' => 'Lý Gia Hân', 'username' => 'student11', 'email' => 'han.ly@example.com', 'bio' => 'Học viên luyện thi chứng chỉ IELTS 7.5+.', 'phone' => '0966554504'],
            ['name' => 'Võ Quốc Bảo', 'username' => 'student12', 'email' => 'bao.vo@example.com', 'bio' => 'Lập trình viên muốn nâng cao kỹ năng Microservices và NestJS.', 'phone' => '0966554505'],
            ['name' => 'Ngô Phương Anh', 'username' => 'student13', 'email' => 'phuonganh.ngo@example.com', 'bio' => 'Chuyên viên phân tích tài chính học thêm Power BI.', 'phone' => '0966554506'],
            ['name' => 'Trịnh Gia Huy', 'username' => 'student14', 'email' => 'huy.trinh@example.com', 'bio' => 'Học viên quan tâm đến công nghệ AI, LLM và Prompt Engineering.', 'phone' => '0966554507'],
            ['name' => 'Dương Thúy Kiều', 'username' => 'student15', 'email' => 'kieu.duong@example.com', 'bio' => 'Sinh viên Ngoại ngữ học thêm Thiết kế UI/UX.', 'phone' => '0966554508'],
        ];

        foreach ($students as $sIdx => $stData) {
            $user = User::where('email', $stData['email'])
                ->orWhere('username', $stData['username'])
                ->first() ?? new User();

            $user->fill([
                'name' => $stData['name'],
                'username' => $stData['username'],
                'email' => $stData['email'],
                'role' => 'student',
                'avatar' => 'https://images.unsplash.com/photo-' . (1510000000000 + ($sIdx * 314159) % 50000000) . '?w=150',
                'bio' => $stData['bio'],
                'phone' => $stData['phone'],
                'password' => $defaultHashedPassword,
                'is_active' => true,
                'account_status' => 'active',
                'email_verified_at' => now(),
                'last_learning_at' => now()->subDays(rand(0, 7)),
            ]);
            $user->save();

            $this->assignRole($user, 'student');
        }
    }

    private function ensureSuperAdminRoleExists(): void
    {
        Role::query()->firstOrCreate(
            ['slug' => 'super_admin'],
            [
                'name' => 'Super Admin',
                'description' => 'Toàn quyền tối cao quản trị hệ thống và phân quyền.',
                'is_system' => true,
            ]
        );
    }

    private function assignRole(User $user, string $roleSlug): void
    {
        $role = Role::where('slug', $roleSlug)->first();
        if ($role) {
            $user->roles()->syncWithoutDetaching([$role->id]);
        }
    }
}
