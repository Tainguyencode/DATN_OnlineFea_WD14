<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\InstructorApplication;
use App\Models\InstructorCertificate;
use App\Models\InstructorDocumentRequirement;
use App\Models\InstructorProfile;
use App\Models\Role;
use App\Models\User;
use App\Services\RoleSyncService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class InstructorDataSeeder extends Seeder
{
    private array $vietnameseLastNames = [
        'Nguyễn', 'Trần', 'Lê', 'Phạm', 'Hoàng', 'Huỳnh', 'Phan', 'Vũ', 'Võ', 'Đặng',
        'Bùi', 'Đỗ', 'Hồ', 'Ngô', 'Dương', 'Lý', 'Đinh', 'Đoàn', 'Lâm', 'Trịnh',
        'Mai', 'Đào', 'Cao', 'Hà', 'Lưu', 'Lương', 'Thái', 'Châu', 'Tạ', 'Phùng',
    ];

    private array $vietnameseMiddleNames = [
        'Văn', 'Thị', 'Đức', 'Minh', 'Hải', 'Thanh', 'Quốc', 'Ngọc', 'Thu', 'Bảo',
        'Gia', 'Hồng', 'Tuấn', 'Mỹ', 'Khánh', 'Hoài', 'Xuân', 'Kim', 'Trọng', 'Đình',
        'Hữu', 'Công', 'Phúc', 'Phương', 'Tấn',
    ];

    private array $vietnameseFirstNames = [
        'An', 'Bình', 'Cường', 'Dũng', 'Dương', 'Đạt', 'Giang', 'Hà', 'Hải', 'Hiếu',
        'Hoa', 'Hoàng', 'Hùng', 'Huy', 'Hương', 'Khánh', 'Khoa', 'Kiên', 'Lâm', 'Linh',
        'Long', 'Mai', 'Minh', 'Nam', 'Nga', 'Ngân', 'Nghĩa', 'Ngọc', 'Nhi', 'Phong',
        'Phúc', 'Phương', 'Quang', 'Quân', 'Sơn', 'Tâm', 'Thái', 'Thành', 'Thảo', 'Thắng',
        'Thịnh', 'Thu', 'Thủy', 'Trang', 'Trung', 'Tú', 'Tuấn', 'Tùng', 'Vinh', 'Vũ',
        'Yến', 'Lan', 'Bích', 'Trúc', 'Diệp', 'Vy', 'Tiến', 'Bách', 'Triết', 'Đăng',
    ];

    private array $banks = [
        ['code' => 'VCB', 'name' => 'Vietcombank'],
        ['code' => 'TCB', 'name' => 'Techcombank'],
        ['code' => 'MB', 'name' => 'MBBank'],
        ['code' => 'ACB', 'name' => 'ACB'],
        ['code' => 'BIDV', 'name' => 'BIDV'],
        ['code' => 'VPB', 'name' => 'VPBank'],
        ['code' => 'TPB', 'name' => 'TPBank'],
        ['code' => 'VIB', 'name' => 'VIB'],
    ];

    private array $rejectionReasons = [
        'Hồ sơ thiếu chứng chỉ chuyên môn quốc tế theo yêu cầu của danh mục giảng dạy.',
        'Ảnh scan chứng chỉ và bằng cấp bị mờ, không nhận diện được ngày cấp và con dấu.',
        'Kinh nghiệm công tác thực tế chưa đạt tiêu chuẩn tối thiểu 2 năm trong ngành.',
        'Chất lượng video bài giảng mẫu chưa đạt chuẩn âm thanh, ánh sáng và nội dung.',
        'Thông tin bằng cấp không trùng khớp với thông tin cá nhân và CCCD.',
        'Chứng chỉ chuyên môn đã hết hạn hiệu lực, vui lòng bổ sung chứng chỉ mới nhất.',
        'Hồ sơ năng lực (Portfolio) chưa cung cấp đầy đủ minh chứng các dự án thực tế.',
    ];

    public function run(): void
    {
        echo "\n=================================================================\n";
        echo "   BẮT ĐẦU NẠP 280+ HỒ SƠ GIẢNG VIÊN (T1/2025 - T8/2026)\n";
        echo "=================================================================\n\n";

        app(RoleSyncService::class)->ensurePrimaryRolesExist();

        $superAdmin = User::where('role', 'admin')->orderBy('id')->first();
        $adminId = $superAdmin ? $superAdmin->id : 1;

        $hashedPassword = Hash::make('password');

        // Danh sách các tháng trải dài từ THÁNG 01/2025 ĐẾN THÁNG 08/2026 (20 tháng)
        $monthTimeline = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthTimeline[] = ['year' => 2025, 'month' => $m];
        }
        for ($m = 1; $m <= 8; $m++) {
            $monthTimeline[] = ['year' => 2026, 'month' => $m];
        }

        // 14 giảng viên mỗi tháng * 20 tháng = 280 giảng viên
        $totalMonths = count($monthTimeline); // 20
        $instructorsPerMonth = 14;
        $totalInstructors = $totalMonths * $instructorsPerMonth; // 280

        $domains = $this->getDomainTemplates();
        $categories = Category::all();
        $requirements = InstructorDocumentRequirement::all();

        $approvedCount = 0;
        $pendingCount = 0;
        $rejectedCount = 0;
        $totalCertificates = 0;

        for ($i = 1; $i <= $totalInstructors; $i++) {
            // 1. Xác định tháng và năm tương ứng
            $timeSlot = $monthTimeline[($i - 1) % $totalMonths];
            $year = $timeSlot['year'];
            $month = $timeSlot['month'];
            $inMonthIdx = intdiv($i - 1, $totalMonths); // 0 đến 13

            // Phân bổ ngày và giờ ngẫu nhiên thực tế trong tháng
            $day = (($inMonthIdx * 2 + ($i % 3)) % 27) + 1; // 1 đến 28
            $hour = 8 + (($i * 3) % 10);                   // 08:00 - 17:00
            $minute = ($i * 13) % 60;

            $createdAt = Carbon::create($year, $month, $day, $hour, $minute, 0);
            $submittedAt = $createdAt->copy()->addMinutes(rand(15, 180));

            // 2. Phân bổ chuyên ngành
            $domain = $domains[($i - 1) % count($domains)];

            $category = $categories->firstWhere('name', $domain['category_name'])
                ?? $categories->firstWhere('slug', Str::slug($domain['category_name']))
                ?? $categories->first();
            $categoryId = $category ? $category->id : 1;

            $catRequirements = $requirements->where('category_id', $categoryId);
            if ($catRequirements->isEmpty()) {
                $catRequirements = $requirements;
            }

            // 3. Tạo Họ và tên tiếng Việt chuẩn
            $fullName = $this->generateVietnameseName($i);
            $slugName = Str::slug($fullName, '_');
            $username = sprintf('gv_%s_%03d', $slugName, $i);
            $email = sprintf('instructor.%03d@onlinefea.edu.vn', $i);
            $phone = sprintf('09%d%07d', 1 + ($i % 8), ($i * 1234567) % 10000000);

            // 4. Phân bổ trạng thái đồng đều và tự nhiên cho từng tháng
            $bank = $this->banks[($i - 1) % count($this->banks)];
            $bankAccNo = '190' . str_pad((string)(($i * 9876543) % 1000000000), 9, '0', STR_PAD_LEFT);
            $bankAccName = mb_strtoupper(Str::ascii($fullName));

            if ($year === 2025) {
                // Năm 2025: Toàn bộ hồ sơ trong quá khứ đã được xử lý (12 Approved, 2 Rejected mỗi tháng)
                if ($inMonthIdx >= 12) {
                    $status = 'rejected';
                    $approvedAt = null;
                    $approvedBy = null;
                    $rejectedReason = $this->rejectionReasons[($i - 1) % count($this->rejectionReasons)];
                    $reviewedAt = $submittedAt->copy()->addDays(rand(1, 3))->addHours(rand(1, 5));
                    $needsReview = false;
                    $rejectedCount++;
                } else {
                    $status = 'approved';
                    $approvedAt = $submittedAt->copy()->addDays(rand(1, 4))->addHours(rand(1, 6));
                    $approvedBy = $adminId;
                    $rejectedReason = null;
                    $reviewedAt = $approvedAt;
                    $needsReview = false;
                    $approvedCount++;
                }
            } else {
                // Năm 2026:
                if ($month < 5) {
                    // T1 - T4/2026: Đã duyệt xong (12 Approved, 2 Rejected)
                    if ($inMonthIdx >= 12) {
                        $status = 'rejected';
                        $approvedAt = null;
                        $approvedBy = null;
                        $rejectedReason = $this->rejectionReasons[($i - 1) % count($this->rejectionReasons)];
                        $reviewedAt = $submittedAt->copy()->addDays(rand(1, 3))->addHours(rand(1, 5));
                        $needsReview = false;
                        $rejectedCount++;
                    } else {
                        $status = 'approved';
                        $approvedAt = $submittedAt->copy()->addDays(rand(1, 4))->addHours(rand(1, 6));
                        $approvedBy = $adminId;
                        $rejectedReason = null;
                        $reviewedAt = $approvedAt;
                        $needsReview = ($inMonthIdx === 2); // 1 số giảng viên có cập nhật mới
                        $approvedCount++;
                    }
                } else {
                    // T5 - T8/2026: Có hồ sơ Chờ duyệt (Pending: 4), Đã duyệt (Approved: 8), Bị từ chối (Rejected: 2)
                    if ($inMonthIdx >= 12) {
                        $status = 'rejected';
                        $approvedAt = null;
                        $approvedBy = null;
                        $rejectedReason = $this->rejectionReasons[($i - 1) % count($this->rejectionReasons)];
                        $reviewedAt = $submittedAt->copy()->addDays(rand(1, 3))->addHours(rand(1, 5));
                        $needsReview = false;
                        $rejectedCount++;
                    } elseif ($inMonthIdx >= 8) {
                        $status = 'pending';
                        $approvedAt = null;
                        $approvedBy = null;
                        $rejectedReason = null;
                        $reviewedAt = null;
                        $needsReview = false;
                        $pendingCount++;
                    } else {
                        $status = 'approved';
                        $approvedAt = $submittedAt->copy()->addDays(rand(1, 4))->addHours(rand(1, 6));
                        $approvedBy = $adminId;
                        $rejectedReason = null;
                        $reviewedAt = $approvedAt;
                        $needsReview = ($inMonthIdx === 3); // Giảng viên có cập nhật mới cần duyệt lại
                        $approvedCount++;
                    }
                }
            }

            $posIdx = ($i - 1) % count($domain['positions']);
            $position = $domain['positions'][$posIdx];
            $orgIdx = ($i - 1) % count($domain['organizations']);
            $organization = $domain['organizations'][$orgIdx];
            $specialty = $domain['specialties'][($i - 1) % count($domain['specialties'])];
            $experienceYears = 5 + ($i % 12);
            $experienceText = "{$experienceYears} năm kinh nghiệm chuyên sâu trong lĩnh vực {$domain['category_name']}";
            $bio = "{$position} tại {$organization} với hơn {$experienceYears} năm kinh nghiệm thực chiến. Chuyên môn sâu về {$specialty}. Đam mê chia sẻ kiến thức và định hướng nghề nghiệp cho học viên trên toàn quốc.";

            $avatarUrl = 'https://images.unsplash.com/photo-' . (1500000000000 + ($i * 714159) % 60000000) . '?w=250&auto=format&fit=crop&q=80';

            // 5. Tạo hoặc Cập nhật User Giảng viên
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $fullName,
                    'username' => $username,
                    'role' => 'instructor',
                    'avatar' => $avatarUrl,
                    'bio' => $bio,
                    'phone' => $phone,
                    'password' => $hashedPassword,
                    'is_active' => true,
                    'account_status' => 'active',
                    'instructor_status' => $status,
                    'commission_rate' => 75.00 + ($i % 10),
                    'bank_code' => $bank['code'],
                    'bank_name' => $bank['name'],
                    'bank_account_number' => $bankAccNo,
                    'bank_account_name' => $bankAccName,
                    'submitted_for_review_at' => $submittedAt,
                    'approved_at' => $approvedAt,
                    'approved_by' => $approvedBy,
                    'rejected_reason' => $rejectedReason,
                    'needs_admin_review' => $needsReview,
                    'admin_last_reviewed_at' => $reviewedAt,
                    'email_verified_at' => $createdAt,
                    'two_factor_enabled' => false,
                    'created_at' => $createdAt,
                    'updated_at' => $reviewedAt ?? $submittedAt,
                ]
            );

            // Gán Role
            $this->assignRole($user, 'instructor');

            // 6. Tạo hoặc Cập nhật InstructorProfile
            $profile = InstructorProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'category_id' => $categoryId,
                    'bio' => $bio,
                    'experience' => $experienceText,
                    'position' => $position,
                    'specialty' => $specialty,
                    'organization' => $organization,
                    'phone' => $phone,
                    'website_url' => 'https://onlinefea.edu.vn/instructors/' . $user->username,
                    'github_url' => 'https://github.com/' . $user->username,
                    'linkedin_url' => 'https://linkedin.com/in/' . $user->username,
                    'teaching_field' => $position,
                    'agree_information' => true,
                    'agree_terms' => true,
                    'created_at' => $createdAt,
                    'updated_at' => $reviewedAt ?? $submittedAt,
                ]
            );

            // 7. Đồng bộ Chuyên môn giảng dạy (teachingCategories)
            $profile->syncTeachingCategories([$categoryId], $categoryId);

            // 8. Tạo hoặc Cập nhật InstructorApplication
            InstructorApplication::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'expertise' => $specialty,
                    'experience' => $experienceText,
                    'introduction' => $bio,
                    'cv_path' => 'demo/cv/' . $user->username . '_cv.pdf',
                    'certificate_path' => 'demo/certificates/' . $user->username . '_cert_1.pdf',
                    'bank_name' => $bank['name'],
                    'bank_account_number' => $bankAccNo,
                    'bank_account_name' => $bankAccName,
                    'status' => $status,
                    'admin_notes' => ($status === 'approved')
                        ? 'Hồ sơ chuyên môn xuất sắc, chứng chỉ quốc tế hợp lệ.'
                        : (($status === 'rejected') ? $rejectedReason : 'Đang trong quá trình thẩm định hồ sơ chuyên môn.'),
                    'reviewed_by' => $approvedBy,
                    'reviewed_at' => $reviewedAt,
                    'created_at' => $submittedAt,
                    'updated_at' => $reviewedAt ?? $submittedAt,
                ]
            );

            // 9. BẮT BUỘC: Nạp 1 đến 3 CHỨNG CHỈ (InstructorCertificate) cho giảng viên
            $certTemplates = $domain['certificates'];
            $certCount = rand(1, min(3, count($certTemplates)));

            for ($cIdx = 0; $cIdx < $certCount; $cIdx++) {
                $certData = $certTemplates[$cIdx];
                $certReq = $catRequirements->get($cIdx % max(1, $catRequirements->count()));
                $reqId = $certReq ? $certReq->id : null;

                $certStatus = $status;
                $certRejectReason = ($certStatus === 'rejected') ? 'Bản scan chưa đạt tiêu chuẩn rõ nét của hội đồng kiểm duyệt.' : null;

                InstructorCertificate::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'title' => $certData['title'],
                    ],
                    [
                        'requirement_id' => $reqId,
                        'document_type' => $certData['type'] ?? 'certificate',
                        'file_path' => 'demo/certificates/' . $user->username . '_cert_' . ($cIdx + 1) . '.pdf',
                        'original_name' => 'Chung_chi_' . Str::slug($certData['title'], '_') . '.pdf',
                        'mime_type' => 'application/pdf',
                        'file_size' => rand(850000, 3800000),
                        'status' => $certStatus,
                        'rejection_reason' => $certRejectReason,
                        'uploaded_at' => $submittedAt,
                        'reviewed_at' => $reviewedAt,
                        'reviewed_by' => ($certStatus !== 'pending') ? $adminId : null,
                        'created_at' => $submittedAt,
                        'updated_at' => $reviewedAt ?? $submittedAt,
                    ]
                );

                $totalCertificates++;
            }

            if ($i % 35 === 0 || $i === $totalInstructors) {
                echo "✓ Đã nạp thành công {$i}/{$totalInstructors} hồ sơ giảng viên (Đã duyệt: {$approvedCount}, Chờ duyệt: {$pendingCount}, Bị từ chối: {$rejectedCount})\n";
            }
        }

        echo "\n=================================================================\n";
        echo "✓ HOÀN THÀNH NẠP DỮ LIỆU GIẢNG VIÊN TOÀN DIỆN!\n";
        echo "   • Tổng Giảng viên: {$totalInstructors}\n";
        echo "   • Đã được duyệt (Approved): {$approvedCount}\n";
        echo "   • Đang chờ duyệt (Pending): {$pendingCount}\n";
        echo "   • Bị từ chối (Rejected): {$rejectedCount}\n";
        echo "   • Tổng Chứng chỉ & Bằng cấp (Certificates): {$totalCertificates}\n";
        echo "   • Thời gian phân bổ: Tháng 01/2025 - Tháng 08/2026 (20 tháng)\n";
        echo "=================================================================\n\n";
    }

    private function generateVietnameseName(int $seed): string
    {
        $last = $this->vietnameseLastNames[($seed - 1) % count($this->vietnameseLastNames)];
        $mid = $this->vietnameseMiddleNames[(($seed * 3) + 1) % count($this->vietnameseMiddleNames)];
        $first = $this->vietnameseFirstNames[(($seed * 7) + 2) % count($this->vietnameseFirstNames)];

        return "$last $mid $first";
    }

    private function assignRole(User $user, string $roleSlug): void
    {
        $role = Role::where('slug', $roleSlug)->first();
        if ($role) {
            $user->roles()->syncWithoutDetaching([$role->id]);
        }
    }

    private function getDomainTemplates(): array
    {
        return [
            // 1. Lập trình Web
            [
                'category_name' => 'Phát triển Web',
                'positions' => [
                    'Senior Fullstack Architect',
                    'Lead Backend Engineer (Laravel/Node.js)',
                    'Frontend Technical Lead (React/Next.js)',
                    'Principal Software Engineer',
                ],
                'organizations' => [
                    'FPT Software',
                    'VNG Corporation',
                    'Tiki Tech Hub',
                    'KMS Technology',
                    'Viettel Digital Services',
                ],
                'specialties' => [
                    'Laravel 11, Vue 3, Inertia.js, RESTful API, PostgreSQL',
                    'React 18, Next.js 14, TypeScript, TailwindCSS, GraphQL',
                    'NestJS, Node.js, Microservices, RabbitMQ, Docker',
                    'PHP Core, Symfony, Clean Architecture, Design Patterns',
                ],
                'certificates' => [
                    ['title' => 'Bằng Kỹ sư Kỹ thuật Phần mềm - ĐH Bách Khoa', 'type' => 'degree'],
                    ['title' => 'Chứng chỉ AWS Certified Developer - Associate', 'type' => 'certificate'],
                    ['title' => 'Giấy xác nhận công tác Lead Architect tại FPT Software', 'type' => 'employment_confirmation'],
                ],
            ],

            // 2. Lập trình Mobile
            [
                'category_name' => 'Phát triển ứng dụng Mobile',
                'positions' => [
                    'Senior Mobile Architect',
                    'Lead Flutter & Dart Developer',
                    'iOS Native Specialist (SwiftUI)',
                    'Senior React Native Engineer',
                ],
                'organizations' => [
                    'MoMo FinTech Lab',
                    'Zalo Mobile Team',
                    'Amanotes Game Studio',
                    'VNPT IT Solutions',
                ],
                'specialties' => [
                    'Flutter 3, BLoC Pattern, Clean Architecture, Firebase',
                    'iOS Native, Swift 5, SwiftUI, Combine, CoreData',
                    'React Native, Expo SDK, Supabase, Redux Toolkit',
                    'Android Native, Kotlin, Jetpack Compose, Coroutines',
                ],
                'certificates' => [
                    ['title' => 'Bằng Cử nhân Công nghệ Thông tin - ĐH Khoa học Tự nhiên', 'type' => 'degree'],
                    ['title' => 'Chứng chỉ Meta Certified Mobile Developer', 'type' => 'certificate'],
                    ['title' => 'Hồ sơ năng lực 12 ứng dụng mobile xuất bản App Store', 'type' => 'portfolio'],
                ],
            ],

            // 3. AI & Machine Learning
            [
                'category_name' => 'Trí tuệ nhân tạo và Machine Learning',
                'positions' => [
                    'Lead AI Research Scientist',
                    'Kỹ sư Trưởng Machine Learning & Deep Learning',
                    'Chuyên gia Computer Vision & YOLOv8',
                    'Chuyên gia LLM, GenAI & LangChain',
                ],
                'organizations' => [
                    'VinAI Research',
                    'Viettel AI Lab',
                    'FPT AI Center',
                    'Viện Công nghệ Thông tin Quốc gia',
                ],
                'specialties' => [
                    'PyTorch, TensorFlow, Computer Vision, YOLOv8, OpenCV',
                    'Generative AI, LangChain, RAG Pipeline, Vector Database, OpenAI',
                    'Natural Language Processing (NLP), Transformers, BERT, GPT',
                    'Machine Learning Algorithms, Scikit-Learn, Deep Neural Networks',
                ],
                'certificates' => [
                    ['title' => 'Bằng Thạc sĩ Khoa học Dữ liệu & Trí tuệ Nhân tạo', 'type' => 'degree'],
                    ['title' => 'Chứng chỉ DeepLearning.AI Deep Learning Specialization', 'type' => 'certificate'],
                    ['title' => 'Chứng chỉ NVIDIA Certified Deep Learning Associate', 'type' => 'certificate'],
                ],
            ],

            // 4. Khoa học dữ liệu & Big Data
            [
                'category_name' => 'Khoa học dữ liệu',
                'positions' => [
                    'Principal Data Scientist',
                    'Head of Data Analytics & BI',
                    'Senior Data Engineer (Spark/Kafka)',
                    'Chuyên gia Phân tích Dữ liệu Kinh doanh',
                ],
                'organizations' => [
                    'Shopee Vietnam Tech Hub',
                    'Techcombank Data Division',
                    'VPBank Analytics Center',
                    'Giao Hàng Nhanh (GHN Logistics)',
                ],
                'specialties' => [
                    'Python Pandas, NumPy, Data Cleaning, Matplotlib, Seaborn',
                    'Apache Spark, PySpark, Kafka Streaming, Delta Lakehouse',
                    'Advanced SQL, Data Modeling, Star Schema, Snowflake, BigQuery',
                    'Power BI, Tableau, Business Intelligence, Predictive Analytics',
                ],
                'certificates' => [
                    ['title' => 'Bằng Cử nhân Toán Tin Ứng Dụng - ĐH Bách Khoa', 'type' => 'degree'],
                    ['title' => 'Chứng chỉ Databricks Certified Data Engineer Professional', 'type' => 'certificate'],
                    ['title' => 'Chứng chỉ Google Professional Data Engineer', 'type' => 'certificate'],
                ],
            ],

            // 5. Cloud & DevOps
            [
                'category_name' => 'DevOps',
                'positions' => [
                    'Principal Cloud & DevOps Architect',
                    'Senior Site Reliability Engineer (SRE)',
                    'Chuyên gia Hạ tầng Đám mây AWS & Kubernetes',
                    'Lead Infrastructure Automation Engineer',
                ],
                'organizations' => [
                    'CloudNative Vietnam Labs',
                    'Viettel IDC Enterprise',
                    'CMC Telecom Solutions',
                    'SmartOSC Cloud Engineering',
                ],
                'specialties' => [
                    'Docker, Kubernetes (K8s), Helm, Istio Service Mesh',
                    'AWS Solution Architecture, EC2, VPC, EKS, RDS, S3, IAM',
                    'Terraform, Ansible, Infrastructure as Code (IaC)',
                    'GitLab CI/CD, GitHub Actions, Prometheus, Grafana, Loki',
                ],
                'certificates' => [
                    ['title' => 'Chứng chỉ AWS Certified Solutions Architect - Professional', 'type' => 'certificate'],
                    ['title' => 'Chứng chỉ Certified Kubernetes Administrator (CKA)', 'type' => 'certificate'],
                    ['title' => 'Chứng chỉ HashiCorp Certified: Terraform Associate', 'type' => 'certificate'],
                ],
            ],

            // 6. An ninh mạng & Bảo mật
            [
                'category_name' => 'An ninh mạng',
                'positions' => [
                    'Head of Cyber Security',
                    'Chuyên gia Kiểm thử Xâm nhập (Penetration Tester)',
                    'Senior Security Consultant',
                    'Chuyên gia An toàn Thông tin Web & Ứng dụng',
                ],
                'organizations' => [
                    'CyberShield Security Labs',
                    'Bkav Cyber Security Corporation',
                    'Cục An toàn Thông tin Quốc gia',
                    'VNCS Global Security',
                ],
                'specialties' => [
                    'Web Security, OWASP Top 10, SQL Injection, XSS, CSRF Defense',
                    'Penetration Testing, Kali Linux, Metasploit, Burp Suite Pro',
                    'Network Security, Firewall, VPN, WAF, SIEM Splunk',
                    'Chính sách An toàn Thông tin ISO 27001, PCI-DSS',
                ],
                'certificates' => [
                    ['title' => 'Chứng chỉ Certified Ethical Hacker (CEH v12)', 'type' => 'certificate'],
                    ['title' => 'Chứng chỉ CompTIA Security+ Professional', 'type' => 'certificate'],
                    ['title' => 'Chứng chỉ Offensive Security Certified Professional (OSCP)', 'type' => 'certificate'],
                ],
            ],

            // 7. Thiết kế UI/UX & Sản phẩm
            [
                'category_name' => 'Thiết kế UI/UX',
                'positions' => [
                    'Lead Product Designer',
                    'UI/UX Director',
                    'Senior UX Researcher',
                    'Chuyên gia Design System & Prototyping',
                ],
                'organizations' => [
                    'Figma Vietnam Community',
                    'Zalo Design Hub',
                    'Sendo Product Design Lab',
                    'Garena Vietnam Studio',
                ],
                'specialties' => [
                    'Figma Master, Auto Layout, Component Variants, Variables',
                    'Design System, UI Kit, Design Tokens, Responsive Web UI',
                    'UX Research, User Journey Mapping, Information Architecture',
                    'Interactive Prototyping, Micro-interactions, Usability Testing',
                ],
                'certificates' => [
                    ['title' => 'Bằng Cử nhân Mỹ thuật Đa phương tiện - ĐH Mỹ thuật', 'type' => 'degree'],
                    ['title' => 'Chứng chỉ Google UX Design Professional Certificate', 'type' => 'certificate'],
                    ['title' => 'Hồ sơ năng lực (Portfolio) Behance & Dribbble Top Featured', 'type' => 'portfolio'],
                ],
            ],

            // 8. Digital Marketing & Tăng trưởng
            [
                'category_name' => 'Digital Marketing',
                'positions' => [
                    'Head of Digital Marketing',
                    'SEO Director & Traffic Growth Lead',
                    'Performance Marketing Lead (Facebook & TikTok Ads)',
                    'Chuyên gia Tăng trưởng E-Commerce & Sàn TMĐT',
                ],
                'organizations' => [
                    'Growth Hackers Agency',
                    'Dentsu Creative Vietnam',
                    'Ogilvy Performance Media',
                    'Top SEO Vietnam Academy',
                ],
                'specialties' => [
                    'Technical SEO, On-page/Off-page SEO, Entity SEO, Google Search Console',
                    'Facebook Ads, TikTok Ads, Google Search & Display Ads, CBO Tối ưu',
                    'Google Analytics 4 (GA4), Google Tag Manager, Conversion Tracking',
                    'Content Marketing Strategy, Viral Video Storytelling, Phễu bán hàng',
                ],
                'certificates' => [
                    ['title' => 'Bằng Cử nhân Marketing - ĐH Kinh tế Quốc dân', 'type' => 'degree'],
                    ['title' => 'Chứng chỉ Google Ads Search & Display Certified', 'type' => 'certificate'],
                    ['title' => 'Chứng chỉ Meta Certified Digital Marketing Associate', 'type' => 'certificate'],
                ],
            ],

            // 9. Tài chính & Kế toán Doanh nghiệp
            [
                'category_name' => 'Tài chính doanh nghiệp',
                'positions' => [
                    'Senior Financial Analyst (CFA)',
                    'Giám đốc Phân tích Đầu tư Chứng khoán',
                    'Cố vấn Tài chính & Định giá Doanh nghiệp',
                    'Kế toán trưởng & Chuyên gia Thuế Doanh nghiệp',
                ],
                'organizations' => [
                    'SSI Securities Research Hub',
                    'VNDIRECT Investment Academy',
                    'Dragon Capital Vietnam Partners',
                    'KPMG Financial Advisory Services',
                ],
                'specialties' => [
                    'Phân tích Báo cáo Tài chính (BCTC), Bảng Cân đối, Lưu chuyển tiền tệ',
                    'Định giá Doanh nghiệp, Mô hình DCF, P/E, P/B, Định giá Cổ phiếu',
                    'Quản trị Rủi ro Danh mục Đầu tư, Phân tích Vĩ mô & Ngành',
                    'Kế toán Doanh nghiệp, Kê khai Thuế GTGT/TNDN, Chuẩn mực VAS/IFRS',
                ],
                'certificates' => [
                    ['title' => 'Bằng Thạc sĩ Tài chính - Ngân hàng - ĐH Ngoại thương', 'type' => 'degree'],
                    ['title' => 'Chứng chỉ CFA Charterholder Level 2', 'type' => 'certificate'],
                    ['title' => 'Chứng chỉ Kế toán trưởng Doanh nghiệp - Bộ Tài Chính', 'type' => 'certificate'],
                ],
            ],

            // 10. Ngoại ngữ & Luyện thi IELTS
            [
                'category_name' => 'Ngoại ngữ',
                'positions' => [
                    'Senior IELTS Master Trainer (Band 8.5)',
                    'Head of Academic English & Writing',
                    'Chuyên gia Tiếng Anh Giao tiếp Doanh nghiệp',
                    'Giảng viên Luyện thi Tiếng Anh Học thuật',
                ],
                'organizations' => [
                    'Oxford English Academy Vietnam',
                    'British Council Partner Network',
                    'VUS English Education',
                    'Apollo English International',
                ],
                'specialties' => [
                    'IELTS Academic Writing Task 1 & 2, Critical Thinking, Band 8.0+ Vocabulary',
                    'IELTS Speaking Natural Reflex, Fluency & Pronunciation Master',
                    'Business English Communication, Email Etiquette, Presentation Skills',
                    'Advanced English Grammar, Academic Collocations & Phrasal Verbs',
                ],
                'certificates' => [
                    ['title' => 'Chứng chỉ IELTS Academic 8.5 (Overall) - British Council', 'type' => 'certificate'],
                    ['title' => 'Chứng chỉ Giảng dạy Tiếng Anh Quốc tế CELTA - Cambridge', 'type' => 'degree'],
                    ['title' => 'Chứng chỉ TESOL 120 Hours International Accredited', 'type' => 'certificate'],
                ],
            ],

            // 11. Quản trị Dự án & Lãnh đạo
            [
                'category_name' => 'Quản trị doanh nghiệp',
                'positions' => [
                    'Agile Coach & Project Management Director',
                    'PMP Certified Senior Project Manager',
                    'Cố vấn Khởi nghiệp Đổi mới Sáng tạo (Startup Mentor)',
                    'Lead Scrum Master & OKRs Consultant',
                ],
                'organizations' => [
                    'Vietnam Agile Community Hub',
                    'PMP Vietnam Professional Chapter',
                    'Sun* Technology Business Unit',
                    'VinGroup Enterprise Strategy Office',
                ],
                'specialties' => [
                    'Agile Scrum Framework, Sprint Planning, Retrospective, Jira/Confluence',
                    'PMP Project Management, Quản trị Tiến độ, Chi phí và Rủi ro',
                    'Lean Startup Methodology, Xây dựng MVP, Product-Market Fit',
                    'Quản trị Mục tiêu OKRs, KPI Dashboard, Kỹ năng Lãnh đạo Đội ngũ',
                ],
                'certificates' => [
                    ['title' => 'Chứng chỉ Project Management Professional (PMP) - PMI', 'type' => 'certificate'],
                    ['title' => 'Chứng chỉ Professional Scrum Master (PSM I & II) - Scrum.org', 'type' => 'certificate'],
                    ['title' => 'Bằng Thạc sĩ Quản trị Kinh doanh (MBA)', 'type' => 'degree'],
                ],
            ],

            // 12. Tin học Văn phòng & Power BI
            [
                'category_name' => 'Power BI',
                'positions' => [
                    'BI & Data Analytics Consultant',
                    'Microsoft Office & Excel Master Trainer',
                    'Chuyên gia Tự động hóa Báo cáo Doanh nghiệp',
                ],
                'organizations' => [
                    'Smart Office Solutions Vietnam',
                    'DataInsight Corporate Advisory',
                    'Ernst & Young Business Analytics',
                ],
                'specialties' => [
                    'Power BI Dashboard, Data Modeling Star Schema, DAX Advanced',
                    'Advanced Excel, Power Query, VBA Macro Automation, Pivot Table',
                    'Tự động hóa Quy trình Báo cáo Tài chính & Quản trị Điều hành',
                ],
                'certificates' => [
                    ['title' => 'Chứng chỉ Microsoft Certified: Power BI Data Analyst Associate (PL-300)', 'type' => 'certificate'],
                    ['title' => 'Chứng chỉ Microsoft Office Specialist Master (MOS Master)', 'type' => 'certificate'],
                ],
            ],
        ];
    }
}
