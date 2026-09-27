<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\InstructorDocumentRequirement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InstructorDocumentRequirementSeeder extends Seeder
{
    public function run(): void
    {
        $categoryConfigs = [
            // =========================================================================
            // 1. NHÓM: LẬP TRÌNH & PHÁT TRIỂN
            // =========================================================================
            // Danh mục cha
            'Lập trình & Phát triển' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp Đại học/Cao đẳng chuyên ngành CNTT',
                    'description' => 'Bằng Cử nhân, Kỹ sư ngành Khoa học máy tính, Kỹ thuật phần mềm, CNTT hoặc tương đương.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ chuyên môn Lập trình / Công nghệ',
                    'description' => 'Chứng chỉ quốc tế hoặc chứng nhận hoàn thành khóa đào tạo chuyên sâu (AWS, GCP, Oracle, PMP, Meta, v.v.).',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận công tác hoặc Hợp đồng lao động',
                    'description' => 'Chứng minh tối thiểu 2 năm kinh nghiệm làm việc hoặc giảng dạy thực tế trong lĩnh vực phần mềm.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            // Danh mục con
            'Phát triển Web' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng Đại học / Cao đẳng ngành CNTT hoặc Phần mềm',
                    'description' => 'Bằng tốt nghiệp chuyên ngành CNTT, Kỹ thuật phần mềm, Hệ thống thông tin.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Lập trình Web Fullstack / Frontend / Backend',
                    'description' => 'Chứng chỉ chuyên môn quốc tế hoặc chứng nhận chuyên sâu (AWS Certified, Meta Frontend/Backend, Node.js, Laravel).',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm phát triển Web tối thiểu 2 năm',
                    'description' => 'Xác nhận công tác hoặc hợp đồng lao động vị trí Web Developer / Fullstack Developer.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Phát triển ứng dụng Mobile' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp chuyên ngành CNTT hoặc Phần mềm',
                    'description' => 'Bằng Cử nhân, Kỹ sư chuyên ngành Khoa học máy tính hoặc Phát triển phần mềm.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Lập trình Mobile (Flutter, React Native, iOS Swift, Android Kotlin)',
                    'description' => 'Chứng chỉ Google Associate Android Developer, Meta React Native, Apple Certified Developer hoặc tương đương.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Hồ sơ ứng dụng di động đã phát hành (App Store / Google Play)',
                    'description' => 'Link kho ứng dụng hoặc tài liệu chứng minh các app di động đã phát hành thành công.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Lập trình Game' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp chuyên ngành CNTT / Kỹ thuật Game',
                    'description' => 'Bằng Đại học/Cao đẳng ngành CNTT, Đồ họa ứng dụng hoặc Lập trình trò chơi điện tử.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Lập trình Game (Unity Certified / Unreal Engine)',
                    'description' => 'Chứng chỉ chuyên môn Unity Developer, Unreal Engine Specialist hoặc C++ Game Development.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Hồ sơ sản phẩm Game / Demo Gameplay',
                    'description' => 'Video gameplay hoặc link bản dựng game thực tế đã xây dựng trên Steam, Itch.io, Mobile Store.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Khoa học dữ liệu' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp Toán Tin / Khoa học Dữ liệu / Thống kê / CNTT',
                    'description' => 'Bằng Cử nhân/Thạc sĩ chuyên ngành Toán - Tin ứng dụng, Data Science, CNTT.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Data Science / Big Data (IBM, Google, Microsoft, Databricks)',
                    'description' => 'Chứng chỉ Google Professional Data Engineer, IBM Data Science Professional, Databricks Certified.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận công tác hoặc Dự án Data Science thực tế',
                    'description' => 'Minh chứng kinh nghiệm làm việc tối thiểu 2 năm ở vị trí Data Scientist / Data Engineer / Data Analyst.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Trí tuệ nhân tạo và Machine Learning' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp chuyên ngành Trí tuệ Nhân tạo / Khoa học Máy tính / Toán Tin',
                    'description' => 'Bằng Cử nhân, Thạc sĩ hoặc Tiến sĩ chuyên ngành AI, Machine Learning, Computer Science.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ AI / Deep Learning / LLM chuyên sâu',
                    'description' => 'Chứng chỉ DeepLearning.AI, TensorFlow Developer, AWS Certified Machine Learning, NVIDIA DLI.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Xác nhận công tác hoặc Công trình nghiên cứu / Dự án AI',
                    'description' => 'Giấy xác nhận kinh nghiệm phát triển mô hình AI/ML hoặc bài báo/công trình nghiên cứu khoa học.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Ngôn ngữ lập trình' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp chuyên ngành CNTT hoặc Phần mềm',
                    'description' => 'Bằng Cử nhân, Kỹ sư ngành CNTT, Kỹ thuật phần mềm.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Chuyên sâu Ngôn ngữ Lập trình (Java, Python, C++, Golang, PHP, C#)',
                    'description' => 'Chứng chỉ lập trình quốc tế (Oracle Java SE, PCAP Python, Microsoft C#, Zend PHP, v.v.).',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm lập trình hoặc giảng dạy',
                    'description' => 'Xác nhận tối thiểu 2 năm kinh nghiệm thực chiến với ngôn ngữ lập trình giảng dạy.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Cơ sở dữ liệu' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp ngành CNTT hoặc Hệ thống thông tin',
                    'description' => 'Bằng Đại học hoặc Cao đẳng ngành CNTT, Khoa học dữ liệu, Hệ thống thông tin.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Quản trị Cơ sở Dữ liệu (Oracle, MySQL, SQL Server, PostgreSQL, MongoDB)',
                    'description' => 'Chứng chỉ Oracle Certified Database Administrator, Microsoft Azure Database, MongoDB Certified DBA.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Xác nhận kinh nghiệm Quản trị / Thiết kế Database',
                    'description' => 'Giấy xác nhận công tác vị trí Database Administrator (DBA), Database Architect hoặc Backend Dev.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Kiểm thử phần mềm' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp chuyên ngành CNTT hoặc liên quan',
                    'description' => 'Bằng tốt nghiệp Đại học/Cao đẳng khối ngành Công nghệ thông tin.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Kiểm thử Phần mềm Quốc tế (ISTQB / Automation Test)',
                    'description' => 'Chứng chỉ ISTQB Foundation Level, ISTQB Agile/Advanced, Selenium WebDriver, Cypress hoặc tương đương.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm QA / QC / Software Tester',
                    'description' => 'Xác nhận tối thiểu 2 năm làm việc thực tế tại các dự án kiểm thử phần mềm.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Công cụ No-code' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp Đại học / Cao đẳng',
                    'description' => 'Bằng tốt nghiệp Đại học hoặc Cao đẳng khối ngành Công nghệ, Kinh tế hoặc liên quan.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Chuyên môn Nền tảng No-code / Low-code (Bubble, Webflow, FlutterFlow, Zapier)',
                    'description' => 'Chứng nhận chuyên gia hoặc hoàn thành khóa đào tạo chuyên sâu về công cụ No-code.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Hồ sơ sản phẩm ứng dụng đã xây dựng bằng No-code',
                    'description' => 'Link các website, ứng dụng hoặc hệ thống tự động hóa đã triển khai thành công.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],

            // =========================================================================
            // 2. NHÓM: KINH DOANH
            // =========================================================================
            // Danh mục cha
            'Kinh doanh' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng cấp chuyên ngành Kinh tế / Quản trị kinh doanh',
                    'description' => 'Bằng tốt nghiệp các ngành Kinh tế, Quản trị, Ngoại thương hoặc tương đương.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ chuyên môn Quản trị / Nghiệp vụ',
                    'description' => 'Chứng chỉ đào tạo chuyên sâu về Quản lý dự án, Agile/Scrum, Bán hàng hoặc Điều hành.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Xác nhận kinh nghiệm quản lý / kinh doanh',
                    'description' => 'Giấy xác nhận công tác hoặc quyết định bổ nhiệm vị trí quản lý / chuyên gia kinh doanh.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            // Danh mục con
            'Khởi nghiệp' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp Đại học ngành Kinh tế, Quản trị hoặc liên quan',
                    'description' => 'Bằng tốt nghiệp Đại học khối ngành Kinh tế, Kinh doanh hoặc Kỹ thuật.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Đào tạo Khởi nghiệp / Đổi mới sáng tạo (Startup & Innovation)',
                    'description' => 'Chứng nhận cố vấn khởi nghiệp, vườn ươm doanh nghiệp hoặc khóa học quản trị đổi mới sáng tạo.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy chứng nhận Đăng ký Doanh nghiệp hoặc Xác nhận Founder / Cố vấn khởi nghiệp',
                    'description' => 'Minh chứng từng sáng lập hoặc điều hành startup, doanh nghiệp khởi nghiệp thành công.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Giao tiếp' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng Đại học ngành Kinh tế, Báo chí, Ngôn ngữ hoặc Xã hội Nhân văn',
                    'description' => 'Bằng tốt nghiệp cử nhân các khối ngành Xã hội, Báo chí truyền thông, Kinh tế.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Đào tạo Kỹ năng Giao tiếp & Đàm phán chuyên nghiệp',
                    'description' => 'Chứng chỉ nghệ thuật giao tiếp, đàm phán thương mại quốc tế hoặc thuyết phục đỉnh cao.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm Trainer / Diễn giả / Giảng dạy kỹ năng',
                    'description' => 'Minh chứng quá trình huấn luyện kỹ năng giao tiếp cho doanh nghiệp hoặc tổ chức.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Quản trị doanh nghiệp' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng Đại học / Thạc sĩ Quản trị kinh doanh (MBA)',
                    'description' => 'Bằng Cử nhân, Thạc sĩ hoặc Tiến sĩ chuyên ngành Quản trị Kinh doanh, Quản trị Chiến lược.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Quản trị Điều hành / Lãnh đạo cấp cao (CEO / Executive)',
                    'description' => 'Chứng chỉ đào tạo Giám đốc điều hành, Mini MBA hoặc Quản trị cấp tiến.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận chức vụ Quản lý / Giám đốc hoặc Hợp đồng lao động',
                    'description' => 'Minh chứng tối thiểu 3 năm kinh nghiệm điều hành hoặc giữ vị trí quản lý doanh nghiệp.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Bán hàng' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp chuyên ngành Kinh tế / Thương mại / Quản trị',
                    'description' => 'Bằng Đại học hoặc Cao đẳng khối ngành Kinh tế, Kinh doanh thương mại.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Đào tạo Kỹ năng Bán hàng chuyên nghiệp (Sales Master / B2B Sales)',
                    'description' => 'Chứng chỉ nghiệp vụ bán hàng, quản lý kênh phân phối hoặc tư vấn giải pháp.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Xác nhận kinh nghiệm Sales Lead / Sales Manager hoặc Thành tích doanh số',
                    'description' => 'Giấy xác nhận công tác hoặc thư khen ngợi thành tích doanh số xuất sắc.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Chiến lược kinh doanh' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp chuyên ngành Kinh tế / Quản trị Chiến lược / Tài chính',
                    'description' => 'Bằng Cử nhân/Thạc sĩ các chuyên ngành Kinh tế học, Quản trị chiến lược, Ngoại thương.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Hoạch định Chiến lược Doanh nghiệp / Cố vấn Quản trị',
                    'description' => 'Chứng chỉ Strategic Management Professional, McKinsey/BCG Frameworks hoặc tương đương.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm tư vấn hoặc điều hành chiến lược kinh doanh',
                    'description' => 'Xác nhận vai trò Cố vấn chiến lược, Trưởng phòng chiến lược hoặc Quản lý cấp cao.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Quản lý vận hành' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng Đại học chuyên ngành Quản trị Chuỗi cung ứng / Quản trị Sản xuất / Kinh tế',
                    'description' => 'Bằng tốt nghiệp ngành Quản lý công nghiệp, Logistics, Quản trị vận hành.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Quản lý Vận hành / Lean Six Sigma / Supply Chain (APICS / CSCP)',
                    'description' => 'Chứng chỉ Lean Six Sigma Green/Black Belt, APICS CSCP, CPIM hoặc tương đương.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Xác nhận kinh nghiệm Quản lý Vận hành / Operations Manager',
                    'description' => 'Giấy xác nhận công tác tối thiểu 2 năm trong bộ phận Vận hành hoặc Chuỗi cung ứng.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Quản lý dự án' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng Đại học chuyên ngành Kinh tế, Kỹ thuật hoặc Quản trị',
                    'description' => 'Bằng tốt nghiệp Đại học khối ngành Quản trị, Kinh tế hoặc Kỹ thuật công nghệ.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Quản lý Dự án Quốc tế (PMP, CAPM, Agile/Scrum Master, Prince2)',
                    'description' => 'Chứng chỉ Project Management Professional (PMI PMP), PSM, PMI-ACP hoặc Prince2 Practitioner.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận tối thiểu 2 năm kinh nghiệm Quản lý Dự án (Project Manager)',
                    'description' => 'Giấy xác nhận công tác hoặc hợp đồng vị trí Project Manager / Scrum Master / Project Lead.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Thương mại điện tử' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp ngành Thương mại điện tử, Marketing hoặc Kinh tế',
                    'description' => 'Bằng Đại học/Cao đẳng ngành E-Commerce, Quản trị Kinh doanh, Digital Marketing.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Vận hành Gian hàng TMĐT (Shopee, TikTok Shop, Lazada, Amazon)',
                    'description' => 'Chứng chỉ Shopee Certified Trainer, TikTok Shop Academy, Amazon Seller University hoặc tương đương.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Minh chứng kết quả kinh doanh / Doanh thu gian hàng TMĐT',
                    'description' => 'Báo cáo doanh thu hoặc chứng nhận Top Seller / Quản lý Shop xuất sắc.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Quản trị nhân sự' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp ngành Quản trị Nhân lực, Luật, Kinh tế hoặc Tâm lý',
                    'description' => 'Bằng Cử nhân ngành Quản trị nhân sự, Luật học, Tâm lý tổ chức hoặc Kinh tế.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Nghề Nhân sự Quốc tế (SHRM-CP/SCP, HRCI, C&B Specialist)',
                    'description' => 'Chứng chỉ SHRM, HRCI, Quản trị tiền lương & phúc lợi (C&B) hoặc Luật lao động chuyên sâu.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm công tác phòng Nhân sự / HR Manager',
                    'description' => 'Xác nhận tối thiểu 2 năm công tác tại các vị trí HR Generalist, HRBP, HR Manager.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Phân tích kinh doanh' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp ngành Hệ thống thông tin, Kinh tế hoặc Phân tích kinh doanh',
                    'description' => 'Bằng Cử nhân ngành MIS, Business Analytics, CNTT hoặc Kinh tế ứng dụng.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Phân tích Nghiệp vụ Quốc tế (IIBA CBAP, CCBA, ECBA, PMI-PBA)',
                    'description' => 'Chứng chỉ Certified Business Analysis Professional (CBAP), ECBA, PMI-PBA hoặc tương đương.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm vị trí Business Analyst (BA)',
                    'description' => 'Xác nhận tối thiểu 2 năm làm việc tại vị trí Business Analyst hoặc Product Owner.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],

            // =========================================================================
            // 3. NHÓM: TÀI CHÍNH & KẾ TOÁN
            // =========================================================================
            // Danh mục cha
            'Tài chính & Kế toán' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng cấp chuyên ngành Tài chính - Ngân hàng / Kế toán / Kiểm toán',
                    'description' => 'Bằng tốt nghiệp chuyên ngành Tài chính, Kế toán, Kiểm toán hoặc tương đương.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ nghề nghiệp Tài chính / Kế toán',
                    'description' => 'Chứng chỉ CPA, ACCA, CFA, CMA hoặc Chứng chỉ Kế toán trưởng / Chứng chỉ hành nghề.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm làm việc trong lĩnh vực Tài chính - Kế toán',
                    'description' => 'Minh chứng quá trình làm việc tại các tổ chức tài chính, kiểm toán hoặc doanh nghiệp.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            // Danh mục con
            'Kế toán và ghi sổ' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp chuyên ngành Kế toán / Kiểm toán',
                    'description' => 'Bằng Đại học hoặc Cao đẳng chuyên ngành Kế toán doanh nghiệp, Kiểm toán.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Kế toán tổng hợp / Kế toán trưởng / Chứng chỉ Hành nghề Kế toán',
                    'description' => 'Chứng chỉ Kế toán trưởng do Bộ Tài chính cấp, CPA Việt Nam, ACCA hoặc tương đương.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm công tác tại phòng Kế toán',
                    'description' => 'Xác nhận tối thiểu 2 năm làm việc thực tế tại vị trí Kế toán viên hoặc Kế toán trưởng.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Tài chính doanh nghiệp' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp chuyên ngành Tài chính Doanh nghiệp / Ngân hàng',
                    'description' => 'Bằng Cử nhân hoặc Thạc sĩ chuyên ngành Tài chính - Ngân hàng, Tài chính doanh nghiệp.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Quản trị Tài chính Doanh nghiệp / CFA / CMA',
                    'description' => 'Chứng chỉ CFA (Chartered Financial Analyst), CMA (Certified Management Accountant) hoặc chứng chỉ quản trị tài chính.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận công tác vị trí Chuyên viên / Quản lý Tài chính',
                    'description' => 'Minh chứng kinh nghiệm phân tích tài chính hoặc quản lý dòng tiền tại doanh nghiệp.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Đầu tư và giao dịch' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp chuyên ngành Tài chính, Chứng khoán hoặc Kinh tế',
                    'description' => 'Bằng Đại học khối ngành Kinh tế, Tài chính ngân hàng, Thị trường chứng khoán.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Hành nghề Chứng khoán / Phân tích Kỹ thuật (CMT, CFA, SSC)',
                    'description' => 'Chứng chỉ hành nghề môi giới/tư vấn chứng khoán do UBCKNN cấp, CMT hoặc CFA.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Minh chứng kinh nghiệm đầu tư / Báo cáo phân tích thị trường',
                    'description' => 'Báo cáo nhận định thị trường, lịch sử giao dịch hoặc danh mục đầu tư thực tế.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Phân tích tài chính' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp ngành Tài chính - Ngân hàng, Kiểm toán hoặc Kinh tế học',
                    'description' => 'Bằng Cử nhân/Thạc sĩ ngành Phân tích tài chính, Tài chính công ty, Kiểm toán.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Chuyên gia Phân tích Tài chính (CFA Level 1+ / FMVA)',
                    'description' => 'Chứng chỉ CFA Charterholder, Financial Modeling & Valuation Analyst (FMVA) hoặc tương đương.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Báo cáo phân tích tài chính / Mô hình định giá doanh nghiệp mẫu',
                    'description' => 'Tài liệu mô hình tài chính (DCF, Multiples) hoặc báo cáo nghiên cứu ngành đã thực hiện.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Kinh tế học' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng Cử nhân / Thạc sĩ chuyên ngành Kinh tế học hoặc Kinh tế đối ngoại',
                    'description' => 'Bằng tốt nghiệp chuyên ngành Kinh tế học, Kinh tế lượng, Kinh tế quốc tế.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng nhận Đào tạo chuyên sâu về Kinh tế vi mô / Vĩ mô / Kinh tế lượng',
                    'description' => 'Chứng chỉ đào tạo nghiên cứu kinh tế lượng (EViews, Stata, R) hoặc chính sách kinh tế.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm giảng dạy hoặc Nghiên cứu kinh tế',
                    'description' => 'Xác nhận công tác tại các viện nghiên cứu, trường đại học hoặc tổ chức tư vấn kinh tế.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Thuế' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp chuyên ngành Thuế, Kế toán, Luật hoặc Tài chính',
                    'description' => 'Bằng Đại học ngành Thuế & Hải quan, Kế toán doanh nghiệp hoặc Luật thương mại.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Hành nghề Dịch vụ Làm thủ tục về Thuế (Đại lý Thuế)',
                    'description' => 'Chứng chỉ hành nghề dịch vụ làm thủ tục về thuế do Tổng cục Thuế cấp.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm tư vấn Thuế hoặc Quyết toán thuế doanh nghiệp',
                    'description' => 'Minh chứng kinh nghiệm thực tế trong kê khai, quyết toán và tư vấn chính sách thuế.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Tài chính cá nhân' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng Đại học ngành Tài chính, Kinh tế hoặc Quản trị kinh doanh',
                    'description' => 'Bằng tốt nghiệp Đại học khối ngành Kinh tế, Tài chính cá nhân, Ngân hàng.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Cố vấn Tài chính Cá nhân (CFP / ChFC / AFA)',
                    'description' => 'Chứng chỉ Certified Financial Planner (CFP), Associate Financial Advisor hoặc tương đương.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm hoạch định tài chính cá nhân',
                    'description' => 'Xác nhận công tác tại các công ty quản lý quỹ, bảo hiểm hoặc dịch vụ tư vấn tài chính.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Tiền điện tử và Blockchain' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp chuyên ngành CNTT, Toán Tin hoặc Fintech',
                    'description' => 'Bằng Cử nhân/Kỹ sư ngành Công nghệ thông tin, Kỹ thuật phần mềm, Tài chính công nghệ.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Chuyên gia Blockchain / Smart Contract / Crypto Analytics',
                    'description' => 'Chứng chỉ Certified Blockchain Developer (CBD), Chainalysis Certified, Moralis Web3 hoặc tương đương.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Minh chứng sản phẩm Blockchain hoặc Báo cáo On-chain Analytics',
                    'description' => 'Link hợp đồng thông minh đã deploy trên mainnet hoặc báo cáo nghiên cứu on-chain.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],

            // =========================================================================
            // 4. NHÓM: CÔNG NGHỆ THÔNG TIN & PHẦN MỀM
            // =========================================================================
            // Danh mục cha
            'Công nghệ thông tin & Phần mềm' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng cấp CNTT / Điện tử viễn thông / An ninh mạng',
                    'description' => 'Bằng Đại học hoặc Cao đẳng ngành CNTT, An toàn thông tin, Hệ thống thông tin.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Mạng / Hệ thống / Bảo mật',
                    'description' => 'Chứng chỉ CCNA, CCNP, CEH, CISSP, CompTIA Security+, LPI hoặc tương đương.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận công tác trong lĩnh vực CNTT',
                    'description' => 'Minh chứng tối thiểu 2 năm kinh nghiệm làm việc thực tế trong ngành CNTT/Hạ tầng.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            // Danh mục con
            'Chứng chỉ CNTT' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp chuyên ngành Công nghệ thông tin',
                    'description' => 'Bằng Cử nhân, Kỹ sư ngành CNTT, Điện tử truyền thông hoặc liên quan.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Bản scan chứng chỉ quốc tế đang sở hữu (AWS, Cisco, Microsoft, Google, CompTIA)',
                    'description' => 'Bản scan chứng chỉ chuyên môn quốc tế còn hạn hiệu lực mà giảng viên sẽ luyện thi/đào tạo.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm giảng dạy hoặc Luyện thi chứng chỉ quốc tế',
                    'description' => 'Minh chứng đã từng giảng dạy hoặc làm việc thực tế liên quan đến chứng chỉ đào tạo.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'An ninh mạng' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp ngành An toàn thông tin, An ninh mạng hoặc CNTT',
                    'description' => 'Bằng Đại học/Cao đẳng chuyên ngành An toàn thông tin, Kỹ thuật mạng, CNTT.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Bảo mật Quốc tế (CEH, CISSP, CompTIA Security+, OSCP, CISM)',
                    'description' => 'Chứng chỉ Certified Ethical Hacker (CEH), OSCP, CompTIA Security+ hoặc tương đương.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm Kỹ sư / Chuyên gia Bảo mật An ninh mạng',
                    'description' => 'Xác nhận tối thiểu 2 năm kinh nghiệm tại các vị trí SOC, Penetration Tester, Security Engineer.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Mạng máy tính' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp chuyên ngành Kỹ thuật Mạng, Viễn thông hoặc CNTT',
                    'description' => 'Bằng Cử nhân/Kỹ sư ngành Kỹ thuật mạng máy tính, Điện tử viễn thông.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Quản trị Mạng Quốc tế (CCNA, CCNP, CCIE, JNCIA)',
                    'description' => 'Chứng chỉ Cisco Certified Network Associate/Professional (CCNA/CCNP) hoặc Juniper JNCIA.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm triển khai & quản trị hạ tầng mạng',
                    'description' => 'Xác nhận công tác tối thiểu 2 năm vị trí Network Engineer, Network Administrator.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Hệ điều hành' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp chuyên ngành CNTT hoặc Phần mềm',
                    'description' => 'Bằng Đại học hoặc Cao đẳng chuyên ngành CNTT, Hệ thống thông tin.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Quản trị Hệ điều hành (RHCSA, RHCE, LPIC, MCSA Windows Server)',
                    'description' => 'Chứng chỉ Red Hat Certified System Administrator (RHCSA), Linux LPIC hoặc Microsoft Certified.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm quản trị hệ điều hành máy chủ',
                    'description' => 'Minh chứng kinh nghiệm vận hành hệ điều hành Linux/Windows Server trong môi trường doanh nghiệp.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Điện toán đám mây' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp chuyên ngành CNTT hoặc Khoa học máy tính',
                    'description' => 'Bằng Đại học khối ngành Kỹ thuật phần mềm, CNTT, Hệ thống mạng.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Cloud Quốc tế (AWS Solutions Architect, Azure Solutions Architect, GCP Professional)',
                    'description' => 'Chứng chỉ AWS Certified Solutions Architect/SysOps, Microsoft Azure Administrator hoặc GCP Cloud Architect.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm thiết kế & vận hành Cloud Infrastructure',
                    'description' => 'Xác nhận tối thiểu 2 năm làm việc với hạ tầng điện toán đám mây (AWS, Azure, GCP).',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Phần cứng' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp ngành Kỹ thuật Máy tính, Điện tử hoặc CNTT',
                    'description' => 'Bằng Đại học/Cao đẳng chuyên ngành Kỹ thuật phần cứng, Điện tử máy tính.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Kỹ thuật Phần cứng Quốc tế (CompTIA A+, CompTIA Network+)',
                    'description' => 'Chứng chỉ CompTIA A+ Core 1 & Core 2 hoặc chứng chỉ sửa chữa phần cứng chuyên nghiệp.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm lắp ráp, sửa chữa và bảo trì phần cứng',
                    'description' => 'Xác nhận công tác tại các trung tâm bảo hành, phòng IT doanh nghiệp hoặc xưởng kỹ thuật.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'DevOps' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp chuyên ngành CNTT hoặc Phần mềm',
                    'description' => 'Bằng Kỹ sư, Cử nhân chuyên ngành CNTT, Kỹ thuật máy tính, Phần mềm.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Quốc tế về DevOps / Kubernetes (CKA, CKAD, Docker, Terraform)',
                    'description' => 'Chứng chỉ Certified Kubernetes Administrator (CKA), Docker Certified Associate, HashiCorp Terraform.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm triển khai CI/CD và tự động hóa hạ tầng',
                    'description' => 'Xác nhận tối thiểu 2 năm làm việc ở vị trí DevOps Engineer hoặc SRE (Site Reliability Engineer).',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Quản trị hệ thống' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp chuyên ngành Hệ thống thông tin hoặc CNTT',
                    'description' => 'Bằng Cử nhân/Kỹ sư ngành Hệ thống thông tin quản lý, Quản trị mạng, CNTT.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Quản trị Hệ thống (ITIL Foundation, VMware VCP, MCSE)',
                    'description' => 'Chứng chỉ ITIL 4 Foundation, VMware Certified Professional (VCP) hoặc Microsoft Certified.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm Quản trị Hệ thống (System Administrator)',
                    'description' => 'Xác nhận tối thiểu 2 năm kinh nghiệm quản trị máy chủ, ảo hóa và dịch vụ hệ thống.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],

            // =========================================================================
            // 5. NHÓM: TIN HỌC VĂN PHÒNG
            // =========================================================================
            // Danh mục cha
            'Tin học văn phòng' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp Đại học / Cao đẳng',
                    'description' => 'Bằng tốt nghiệp Đại học hoặc Cao đẳng các khối ngành.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Tin học Văn phòng Quốc tế (MOS / IC3 / ICDL)',
                    'description' => 'Chứng chỉ Microsoft Office Specialist (MOS), IC3 Digital Literacy hoặc ICDL.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm ứng dụng hoặc giảng dạy tin học văn phòng',
                    'description' => 'Minh chứng kinh nghiệm làm việc hoặc đào tạo tin học văn phòng tại cơ quan, trường học.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            // Danh mục con
            'Microsoft Excel' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp Đại học / Cao đẳng',
                    'description' => 'Bằng tốt nghiệp Đại học hoặc Cao đẳng khối ngành Kinh tế, Kỹ thuật hoặc liên quan.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Microsoft Office Specialist (MOS Excel / MOS Excel Expert)',
                    'description' => 'Chứng chỉ MOS Excel Associate hoặc MOS Excel Expert chính thức do Microsoft / Certiport cấp.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'File sản phẩm mẫu Excel nâng cao / Báo cáo Dashboard tự động hóa',
                    'description' => 'File mẫu ứng dụng VBA Macro, Power Query hoặc Dashboard Excel quản trị điều hành.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Microsoft Word' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp Đại học / Cao đẳng',
                    'description' => 'Bằng tốt nghiệp Đại học hoặc Cao đẳng.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Microsoft Office Specialist (MOS Word / MOS Word Expert)',
                    'description' => 'Chứng chỉ MOS Word hoặc MOS Word Expert do Microsoft / Certiport cấp.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Hồ sơ tài liệu mẫu soạn thảo chuẩn quốc tế / Sách / Quy chuẩn trình bày',
                    'description' => 'File văn bản mẫu hoàn chỉnh chuẩn hành chính, mục lục tự động, biểu mẫu nâng cao.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Microsoft PowerPoint' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp Đại học / Cao đẳng',
                    'description' => 'Bằng tốt nghiệp Đại học hoặc Cao đẳng các khối ngành.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ MOS PowerPoint hoặc Chứng nhận Đào tạo Thiết kế Slide chuyên nghiệp',
                    'description' => 'Chứng chỉ MOS PowerPoint của Microsoft hoặc chứng nhận Master Slide Presentation.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Hồ sơ bài thuyết trình / Slide mẫu tự thiết kế',
                    'description' => 'File thuyết trình PowerPoint (.pptx / PDF) thể hiện phong cách thiết kế và hiệu ứng ấn tượng.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Microsoft Office' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp Đại học / Cao đẳng',
                    'description' => 'Bằng tốt nghiệp Đại học hoặc Cao đẳng.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Tổng hợp Microsoft Office Specialist Master (MOS Master)',
                    'description' => 'Chứng chỉ MOS Master danh giá của Microsoft bao gồm Word, Excel, PowerPoint, Outlook/Access.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm giảng dạy hoặc đào tạo ứng dụng tin học văn phòng',
                    'description' => 'Minh chứng kinh nghiệm đào tạo tin học văn phòng doanh nghiệp hoặc tại trường học.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Google Workspace' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp Đại học / Cao đẳng',
                    'description' => 'Bằng tốt nghiệp Đại học hoặc Cao đẳng.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Google Workspace Certified Administrator / Professional',
                    'description' => 'Chứng chỉ Google Workspace Administrator hoặc chứng nhận đào tạo Google Suite chính thức.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm triển khai / đào tạo Google Workspace',
                    'description' => 'Minh chứng kinh nghiệm vận hành Google Docs, Sheets, Forms, AppSheet cho doanh nghiệp.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Power BI' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp ngành CNTT, Kinh tế, Thống kê hoặc liên quan',
                    'description' => 'Bằng Đại học ngành Phân tích dữ liệu, Hệ thống thông tin, Kinh tế hoặc Toán Tin.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Microsoft Certified: Power BI Data Analyst Associate (PL-300)',
                    'description' => 'Chứng chỉ quốc tế PL-300 Microsoft Power BI Data Analyst Associate chính thức.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'File mẫu Dashboard Power BI trực quan hóa dữ liệu',
                    'description' => 'File .pbix hoặc video demo Dashboard tương tác xử lý dữ liệu phức tạp (DAX, Data Model).',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'SAP' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp chuyên ngành Hệ thống thông tin, Kế toán hoặc Quản trị kinh doanh',
                    'description' => 'Bằng Đại học ngành MIS, Kế toán - Kiểm toán, Quản trị chuỗi cung ứng hoặc CNTT.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Chuyên gia SAP (SAP Certified Application Associate / Consultant)',
                    'description' => 'Chứng chỉ SAP FICO, SAP MM, SAP SD, SAP S/4HANA hoặc tương đương do SAP cấp.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm tư vấn hoặc vận hành hệ thống ERP SAP',
                    'description' => 'Minh chứng tối thiểu 2 năm kinh nghiệm triển khai hoặc key user hệ thống SAP.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],

            // =========================================================================
            // 6. NHÓM: PHÁT TRIỂN CÁ NHÂN
            // =========================================================================
            // Danh mục cha
            'Phát triển cá nhân' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp Đại học / Cao đẳng',
                    'description' => 'Bằng tốt nghiệp Đại học hoặc Cao đẳng các chuyên ngành liên quan.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Đào tạo Kỹ năng Mềm / Huấn luyện viên Kỹ năng (Life Coach / Trainer)',
                    'description' => 'Chứng chỉ Life Coach, NLP, Huấn luyện kỹ năng mềm quốc tế hoặc trong nước.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm đào tạo kỹ năng hoặc Diễn giả',
                    'description' => 'Minh chứng quá trình giảng dạy, tổ chức workshop hoặc đào tạo phát triển bản thân.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            // Danh mục con
            'Kỹ năng lãnh đạo' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp Đại học / Thạc sĩ Quản trị, Xã hội hoặc liên quan',
                    'description' => 'Bằng tốt nghiệp Đại học hoặc Sau đại học các chuyên ngành.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Đào tạo Lãnh đạo / Leadership Coaching Quốc tế (ICF, Maxwell Leadership)',
                    'description' => 'Chứng chỉ John Maxwell Team, ICF Certified Coach hoặc chứng nhận Executive Leadership.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận vị trí Quản lý / Trưởng nhóm hoặc Điều hành tổ chức',
                    'description' => 'Minh chứng kinh nghiệm quản lý đội ngũ tối thiểu 2 năm.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Quản lý thời gian' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp Đại học / Cao đẳng',
                    'description' => 'Bằng tốt nghiệp Đại học hoặc Cao đẳng.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Đào tạo Kỹ năng Quản trị Thời gian & Hiệu suất cá nhân (Productivity & GTD)',
                    'description' => 'Chứng chỉ Getting Things Done (GTD), Quản lý hiệu suất cá nhân hoặc tương đương.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm đào tạo / Diễn giả kỹ năng làm việc hiệu quả',
                    'description' => 'Minh chứng đã từng giảng dạy kỹ năng quản lý thời gian cho học viên hoặc doanh nghiệp.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Phát triển sự nghiệp' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp Đại học chuyên ngành Kinh tế, Tâm lý hoặc Xã hội',
                    'description' => 'Bằng Cử nhân khối ngành Kinh tế, Quản trị nhân lực, Tâm lý học ứng dụng.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Cố vấn Nghề nghiệp Quốc tế (Career Coach / Career Consultant)',
                    'description' => 'Chứng chỉ NCDA Certified Career Counselor, Career Coaching quốc tế hoặc tương đương.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm tư vấn hướng nghiệp hoặc Quản lý nhân sự',
                    'description' => 'Minh chứng tối thiểu 2 năm kinh nghiệm tư vấn nghề nghiệp hoặc tuyển dụng nhân tài.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Kỹ năng giao tiếp' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng Đại học chuyên ngành Báo chí, Ngôn ngữ, Tâm lý hoặc Xã hội',
                    'description' => 'Bằng tốt nghiệp Cử nhân các khối ngành Xã hội Nhân văn, Báo chí, Ngôn ngữ.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Đào tạo Kỹ năng Giao tiếp & Thuyết trình trước đám đông (Public Speaking)',
                    'description' => 'Chứng chỉ Toastmasters International, Public Speaking Master hoặc Trainer chuyên nghiệp.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm đào tạo / MC / Diễn giả chuyên nghiệp',
                    'description' => 'Minh chứng hoạt động dẫn chương trình, diễn giả hoặc đào tạo giao tiếp.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Tư duy sáng tạo' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp Đại học chuyên ngành Nghệ thuật, Thiết kế hoặc Khoa học Xã hội',
                    'description' => 'Bằng tốt nghiệp Đại học các chuyên ngành liên quan.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Phương pháp Tư duy Sáng tạo (Design Thinking, TRIZ, Mindset)',
                    'description' => 'Chứng chỉ Design Thinking (IDEO / Stanford d.school), TRIZ hoặc Creative Problem Solving.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Hồ sơ minh chứng các dự án / Sáng kiến sáng tạo đã ứng dụng thành công',
                    'description' => 'Tài liệu mô tả các giải pháp đổi mới sáng tạo, giải thưởng sáng tạo đã đạt được.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Thương hiệu cá nhân' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp chuyên ngành Marketing, Truyền thông hoặc Báo chí',
                    'description' => 'Bằng Cử nhân ngành Marketing, Truyền thông đa phương tiện, Quan hệ công chúng.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Xây dựng Thương hiệu Cá nhân (Personal Branding Specialist)',
                    'description' => 'Chứng chỉ đào tạo định vị thương hiệu cá nhân, truyền thông xã hội hoặc KOL/KOC Management.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Hồ sơ minh chứng các kênh truyền thông cá nhân có sức ảnh hưởng',
                    'description' => 'Link trang cá nhân, kênh YouTube, TikTok, LinkedIn hoặc bài báo truyền thông chứng minh uy tín.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Quản lý căng thẳng' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp chuyên ngành Tâm lý học, Y tế công cộng hoặc Xã hội',
                    'description' => 'Bằng Đại học ngành Tâm lý học, Y học, Xã hội học hoặc Sư phạm.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Trị liệu Tâm lý / Quản trị Căng thẳng (Stress Management / Mindfulness)',
                    'description' => 'Chứng chỉ Mindfulness-Based Stress Reduction (MBSR), Trị liệu nhận thức hành vi (CBT) hoặc tương đương.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm tư vấn tâm lý hoặc Đào tạo sức khỏe tinh thần',
                    'description' => 'Minh chứng công tác tại các trung tâm tư vấn, trường học hoặc tổ chức chăm sóc tinh thần.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Kỹ năng học tập' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp Đại học Sư phạm hoặc chuyên ngành liên quan',
                    'description' => 'Bằng Cử nhân khối ngành Sư phạm, Giáo dục học hoặc Tâm lý giáo dục.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Phương pháp Học tập Hiệu quả / Siêu trí nhớ / Sơ đồ tư duy (Mindmap)',
                    'description' => 'Chứng chỉ Tony Buzan Mind Map Instructor, Huấn luyện viên Siêu trí nhớ hoặc Phương pháp học siêu tốc.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm giảng dạy hoặc Huấn luyện kỹ năng học tập',
                    'description' => 'Minh chứng đã từng giảng dạy phương pháp học tập cho học sinh, sinh viên.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Động lực và sự tự tin' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp Đại học / Cao đẳng',
                    'description' => 'Bằng tốt nghiệp Đại học hoặc Cao đẳng.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Huấn luyện Tâm lý / NLP Master / Life Coach Certified',
                    'description' => 'Chứng chỉ NLP Practitioner / NLP Master Practitioner (ABNLP / ITANLP) hoặc Life Coach.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm diễn giả / Truyền cảm hứng tạo động lực',
                    'description' => 'Minh chứng tổ chức các buổi hội thảo truyền động lực hoặc huấn luyện khai vấn cá nhân.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],

            // =========================================================================
            // 7. NHÓM: THIẾT KẾ
            // =========================================================================
            // Danh mục cha
            'Thiết kế' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng cấp Mỹ thuật / Thiết kế đồ họa / Đa phương tiện',
                    'description' => 'Bằng tốt nghiệp chuyên ngành Thiết kế đồ họa, Mỹ thuật ứng dụng, UI/UX hoặc Kiến trúc.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ chuyên môn Thiết kế / Công cụ đồ họa',
                    'description' => 'Chứng nhận Adobe Certified Professional, Figma, Blender hoặc các công cụ thiết kế chuyên sâu.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Hồ sơ năng lực (Portfolio thiết kế)',
                    'description' => 'Link Behance/Dribbble hoặc tệp PDF tổng hợp các tác phẩm / sản phẩm thiết kế tiêu biểu.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            // Danh mục con
            'Thiết kế đồ họa' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp chuyên ngành Mỹ thuật Ứng dụng / Thiết kế Đồ họa',
                    'description' => 'Bằng Cử nhân Thiết kế Đồ họa, Mỹ thuật Công nghiệp hoặc Đồ họa Đa phương tiện.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Chuyên môn Thiết kế Đồ họa Quốc tế (Adobe Certified Professional)',
                    'description' => 'Chứng chỉ Adobe Certified Professional in Visual Design (Photoshop, Illustrator, InDesign).',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Hồ sơ năng lực thiết kế đồ họa (Portfolio Behance / Dribbble)',
                    'description' => 'Link portfolio hoặc file PDF tổng hợp các dự án thiết kế thương hiệu, ấn phẩm đồ họa.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Thiết kế UI/UX' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp chuyên ngành Thiết kế, CNTT hoặc Tương tác người máy (HCI)',
                    'description' => 'Bằng Cử nhân Thiết kế Đồ họa, Khoa học Máy tính, Mỹ thuật Đa phương tiện.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Google UX Design Professional hoặc Figma / Nielsen Norman Group UI/UX',
                    'description' => 'Chứng chỉ Google UX Design Professional Certificate, NN/g UX Certified hoặc chứng chỉ Figma Master.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Hồ sơ năng lực Case Study UI/UX (Figma Prototype & UX Research)',
                    'description' => 'Link case study UI/UX chi tiết gồm user research, wireframe và interactive prototype trên Figma.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Thiết kế Web' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp ngành Thiết kế Đồ họa hoặc CNTT',
                    'description' => 'Bằng Đại học hoặc Cao đẳng ngành Web Design, Mỹ thuật Đa phương tiện, CNTT.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Thiết kế Web Chuyên nghiệp (HTML/CSS/JS UI, Responsive Web Design)',
                    'description' => 'Chứng chỉ W3C Web Design, freeCodeCamp Responsive Web Design hoặc chứng nhận Webflow/WordPress Master.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Danh sách website đã thiết kế giao diện hoàn chỉnh thực tế',
                    'description' => 'Link các trang web đã trực tiếp thiết kế UI và triển khai thực tế trên môi trường live.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Thiết kế 3D' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp chuyên ngành Mỹ thuật, Kỹ thuật Đồ họa 3D hoặc Hoạt hình',
                    'description' => 'Bằng Cử nhân Thiết kế 3D, Hoạt hình 3D Animation, Kỹ xảo VFX.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Chuyên môn 3D (Autodesk 3ds Max / Maya / Blender / ZBrush)',
                    'description' => 'Chứng chỉ Autodesk Certified Professional (Maya/3ds Max), Blender Foundation Certified hoặc tương đương.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Showreel video hoặc Portfolio mô hình 3D (ArtStation)',
                    'description' => 'Link ArtStation hoặc video showreel render các mô hình 3D, nhân vật, bối cảnh chất lượng cao.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Adobe Photoshop' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp ngành Mỹ thuật, Đồ họa hoặc Nhiếp ảnh',
                    'description' => 'Bằng Đại học/Cao đẳng khối ngành Nghệ thuật thị giác, Mỹ thuật ứng dụng.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Adobe Certified Professional in Visual Design with Adobe Photoshop',
                    'description' => 'Chứng chỉ quốc tế chính thức của Adobe xác nhận thành thạo Adobe Photoshop.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Bộ sưu tập tác phẩm chỉnh sửa / Xử lý hình ảnh Photoshop (Portfolio)',
                    'description' => 'Tệp PDF hoặc link Behance tập hợp các tác phẩm cắt ghép, retouch, matte painting xuất sắc.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Adobe Illustrator' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp ngành Mỹ thuật Ứng dụng hoặc Đồ họa',
                    'description' => 'Bằng Cử nhân Thiết kế Đồ họa, Mỹ thuật Công nghiệp.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Adobe Certified Professional in Graphic Design with Adobe Illustrator',
                    'description' => 'Chứng chỉ quốc tế chính thức của Adobe xác nhận thành thạo Adobe Illustrator.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Bộ sưu tập thiết kế Vector / Logo / Bộ nhận diện thương hiệu',
                    'description' => 'Portfolio các sản phẩm minh họa vector, typography, logo guidelines đã thiết kế.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Thiết kế nội thất' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp chuyên ngành Thiết kế Nội thất hoặc Kiến trúc',
                    'description' => 'Bằng Cử nhân/Kỹ sư Thiết kế Nội thất, Mỹ thuật Công nghiệp, Kiến trúc.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Phần mềm Thiết kế Nội thất (AutoCAD, SketchUp, 3ds Max, V-Ray, Corona)',
                    'description' => 'Chứng nhận chuyên sâu phần mềm dựng hình và render nội thất chuyên nghiệp.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Hồ sơ các dự án thiết kế và thi công nội thất thực tế (Portfolio)',
                    'description' => 'Bản vẽ kỹ thuật 2D, phối cảnh 3D và hình ảnh chụp công trình nội thất thực tế hoàn thiện.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Kiến trúc' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng Cử nhân / Kiến trúc sư chuyên ngành Kiến trúc hoặc Quy hoạch',
                    'description' => 'Bằng tốt nghiệp Đại học ngành Kiến trúc công trình, Quy hoạch đô thị.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Hành nghề Kiến trúc sư hoặc Chứng chỉ Thiết kế Kiến trúc',
                    'description' => 'Chứng chỉ hành nghề kiến trúc do Sở Xây dựng cấp hoặc chứng chỉ Revit Architecture Quốc tế.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Hồ sơ các công trình kiến trúc đã tham gia thiết kế / Chủ trì thiết kế',
                    'description' => 'Hồ sơ bản vẽ kiến trúc các công trình dân dụng, công nghiệp đã được phê duyệt thi công.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Thiết kế thời trang' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp chuyên ngành Thiết kế Thời trang hoặc Mỹ thuật Công nghiệp',
                    'description' => 'Bằng Cử nhân Thiết kế Thời trang, Công nghệ May & Thời trang.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Kỹ thuật Cắt may / Thiết kế Rập / Thời trang ứng dụng (CLO 3D, Gerber)',
                    'description' => 'Chứng chỉ phần mềm 3D Fashion Design (CLO 3D, Optitex) hoặc chứng chỉ tạo mẫu thời trang cao cấp.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Bộ sưu tập thời trang (Fashion Lookbook / Portfolio BST)',
                    'description' => 'Bộ ảnh lookbook các bộ sưu tập thời trang đã ra mắt hoặc trình diễn trên sàn catwalk.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],

            // =========================================================================
            // 8. NHÓM: MARKETING
            // =========================================================================
            // Danh mục cha
            'Marketing' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng cấp Marketing / Truyền thông / Kinh tế',
                    'description' => 'Bằng tốt nghiệp chuyên ngành Marketing, Truyền thông đa phương tiện hoặc liên quan.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Digital Marketing / Quảng cáo',
                    'description' => 'Chứng chỉ Google Ads, Meta Certified, Hubspot, Google Analytics hoặc chứng chỉ tương đương.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Hồ sơ năng lực (Portfolio chiến dịch Marketing)',
                    'description' => 'Báo cáo hoặc tài liệu minh chứng các chiến dịch / dự án đã thực hiện thành công.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            // Danh mục con
            'Digital Marketing' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp ngành Marketing, Truyền thông hoặc Quản trị kinh doanh',
                    'description' => 'Bằng Cử nhân Marketing, Thương mại điện tử, Quản trị kinh doanh.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Digital Marketing Tổng thể (Google, Meta, HubSpot)',
                    'description' => 'Chứng chỉ Google Digital Marketing & E-commerce, Meta Certified Digital Marketing Associate, HubSpot.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Báo cáo minh chứng kết quả chiến dịch Marketing số thực tế',
                    'description' => 'Báo cáo tổng hợp số liệu hiệu quả (KPI, Traffic, Conversion, ROI) của các dự án thực hiện.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'SEO' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp Đại học / Cao đẳng ngành CNTT, Marketing hoặc Kinh tế',
                    'description' => 'Bằng tốt nghiệp Đại học/Cao đẳng.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Chuyên gia SEO (Google Analytics 4, SEMrush, Ahrefs Academy, Yoast)',
                    'description' => 'Chứng chỉ SEMrush SEO Toolkit, Ahrefs Certified, Google Analytics 4 Certification.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Bằng chứng từ khóa lên Top Google & Case Study tăng trưởng Organic Traffic',
                    'description' => 'Minh chứng hình ảnh Google Search Console, báo cáo tăng trưởng lưu lượng truy cập tự nhiên.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Marketing mạng xã hội' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp ngành Marketing, Truyền thông hoặc Quan hệ công chúng (PR)',
                    'description' => 'Bằng Cử nhân Marketing, Quan hệ công chúng, Truyền thông đại chúng.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Social Media Marketing (Meta Certified Community Manager, TikTok Academy)',
                    'description' => 'Chứng chỉ Meta Certified Social Media Marketing, TikTok Creative Partner hoặc Hootsuite Certified.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Minh chứng tăng trưởng kênh mạng xã hội (Fanpage, TikTok, Group)',
                    'description' => 'Báo cáo chỉ số tăng trưởng tương tác (Engagement), Reach và Follower của các kênh đã quản trị.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Xây dựng thương hiệu' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp ngành Marketing, Thương hiệu hoặc Quan hệ công chúng',
                    'description' => 'Bằng Cử nhân chuyên ngành Quản trị thương hiệu, Marketing chiến lược.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Quản trị Thương hiệu (Brand Management / Brand Strategy)',
                    'description' => 'Chứng chỉ Brand Strategy Masterclass, Kellogg Brand Strategy hoặc tương đương.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Hồ sơ tài liệu chiến lược định vị thương hiệu đã triển khai thành công',
                    'description' => 'Brand Guidelines, tài liệu định vị thương hiệu và kế hoạch tung sản phẩm mới.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Content Marketing' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp ngành Báo chí, Ngôn ngữ học, Marketing hoặc Truyền thông',
                    'description' => 'Bằng Cử nhân Báo chí, Ngôn ngữ Văn học, Marketing, PR.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Content Marketing Quốc tế (HubSpot Content Marketing Certified)',
                    'description' => 'Chứng chỉ HubSpot Content Marketing, Copywriting Masterclass hoặc Storytelling Certified.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Bộ sưu tập các bài viết / Kịch bản / Chiến dịch nội dung tiêu biểu (Content Portfolio)',
                    'description' => 'Tài liệu tổng hợp các bài viết viral, kịch bản video quảng cáo, chuỗi bài PR đã xuất bản.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Quảng cáo trả phí' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp ngành Marketing, Kinh tế hoặc Thống kê',
                    'description' => 'Bằng Đại học hoặc Cao đẳng khối ngành Kinh tế, Marketing, Thống kê ứng dụng.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Google Ads Search/Display/Video hoặc Meta Certified Media Buying Professional',
                    'description' => 'Chứng chỉ Google Ads All Assessments, Meta Media Buying Professional, TikTok Ads Certified.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Báo cáo số liệu hiệu quả chạy Ads (ROAS, CPA, Conversion Tracking)',
                    'description' => 'Ảnh chụp màn hình trình quản lý quảng cáo hoặc báo cáo ngân sách và doanh thu mang lại từ Ads.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Email Marketing' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp ngành Marketing hoặc Truyền thông',
                    'description' => 'Bằng Đại học/Cao đẳng.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Email Marketing (HubSpot Email Marketing, Mailchimp, Klaviyo Academy)',
                    'description' => 'Chứng chỉ HubSpot Email Marketing Certified, Klaviyo Product Certified hoặc Mailchimp Foundations.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Báo cáo tỷ lệ mở (Open Rate), Click Rate và Chuyển đổi chiến dịch Email Automation',
                    'description' => 'Minh chứng thiết lập phễu email marketing tự động hóa và kết quả chuyển đổi thực tế.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Affiliate Marketing' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp Đại học / Cao đẳng',
                    'description' => 'Bằng tốt nghiệp Đại học hoặc Cao đẳng.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Affiliate Marketing / Performance Marketing',
                    'description' => 'Chứng nhận hoàn thành khóa đào tạo tiếp thị liên kết chuyên sâu hoặc Performance Marketing.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Minh chứng doanh thu hoa hồng từ các mạng Affiliate uy tín (AccessTrade, Shopee, TikTok)',
                    'description' => 'Báo cáo thu nhập hoa hồng tiếp thị liên kết thực tế qua các nền tảng Affiliate uy tín.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Marketing Analytics' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp ngành Phân tích Dữ liệu, Marketing hoặc Toán Tin',
                    'description' => 'Bằng Cử nhân ngành Marketing Analytics, Data Analytics, Hệ thống thông tin.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Google Analytics 4 (GA4 Certification) / Looker Studio',
                    'description' => 'Chứng chỉ Google Analytics Individual Qualification (GA4) hoặc Google Data Studio / Looker Studio.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Dashboard báo cáo số liệu chuyển đổi Marketing thực tế',
                    'description' => 'Link hoặc file báo cáo trực quan hóa phễu chuyển đổi và phân bổ nguồn lưu lượng marketing.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Product Marketing' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp ngành Marketing, Quản trị kinh doanh hoặc CNTT',
                    'description' => 'Bằng Cử nhân Marketing, Quản lý sản phẩm, Quản trị kinh doanh.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Product Marketing Quốc tế (Product Marketing Alliance - PMA Certified)',
                    'description' => 'Chứng chỉ Product Marketing Core (PMA), Pragmatic Institute hoặc tương đương.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Kế hoạch ra mắt sản phẩm (Go-To-Market Strategy) thực tế',
                    'description' => 'Tài liệu Go-To-Market Plan, phân tích đối thủ và định vị giá trị sản phẩm đã ra mắt thành công.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],

            // =========================================================================
            // 9. NHÓM: PHONG CÁCH SỐNG
            // =========================================================================
            // Danh mục cha
            'Phong cách sống' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp Đại học / Cao đẳng hoặc Chứng nhận Đào tạo Nghề chuyên nghiệp',
                    'description' => 'Bằng tốt nghiệp hoặc chứng nhận hoàn thành chương trình đào tạo nghề chuyên sâu.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Hành nghề / Chứng nhận Kỹ năng chuyên môn',
                    'description' => 'Chứng chỉ đào tạo nghề, tay nghề hoặc chuyên môn trong lĩnh vực nghệ thuật/phong cách sống.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Hồ sơ hình ảnh / Video minh chứng tay nghề và sản phẩm thực tế',
                    'description' => 'Hình ảnh sản phẩm, bài viết báo chí hoặc video thực hành chứng minh tay nghề xuất sắc.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            // Danh mục con
            'Nghệ thuật và thủ công' => [
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Đào tạo Nghệ thuật Thủ công / Điêu khắc / Gốm / Handmade',
                    'description' => 'Chứng nhận hoàn thành các khóa đào tạo nghề thủ công mỹ nghệ chuyên sâu.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Bộ sưu tập tác phẩm nghệ thuật thủ công đã hoàn thiện (Handmade Portfolio)',
                    'description' => 'Hình ảnh các sản phẩm thủ công tinh xảo tự tay chế tác kèm mô tả kỹ thuật.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm giảng dạy hoặc Tổ chức Workshop thủ công',
                    'description' => 'Minh chứng đã từng tổ chức các lớp workshop hướng dẫn nghệ thuật thủ công.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Làm đẹp' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng Tốt nghiệp Đào tạo Nghề Làm đẹp / Thẩm mỹ Chuyên nghiệp',
                    'description' => 'Bằng tốt nghiệp trường đào tạo nghề Thẩm mỹ, Chăm sóc sắc đẹp hoặc Makeup chuyên nghiệp.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Hành nghề Chăm sóc Sắc đẹp / Makeup / Spa / Skincare Quốc tế',
                    'description' => 'Chứng chỉ CIDESCO, ITEC, Chứng chỉ Hành nghề Thẩm mỹ do cơ quan có thẩm quyền cấp.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Portfolio hình ảnh tác phẩm trang điểm / Dịch vụ làm đẹp đã thực hiện',
                    'description' => 'Hình ảnh chụp trước và sau (Before/After) các tác phẩm makeup, tạo mẫu hoặc chăm sóc da.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Nấu ăn' => [
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Bằng tốt nghiệp / Chứng chỉ Nghề Bếp Chuyên nghiệp (Culinary Arts)',
                    'description' => 'Bằng Bếp trưởng, Chứng chỉ Kỹ thuật chế biến món ăn (Á/Âu/Bánh) chuyên nghiệp.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Giấy chứng nhận Vệ sinh An toàn Thực phẩm',
                    'description' => 'Giấy xác nhận kiến thức và chứng nhận đủ điều kiện an toàn thực phẩm còn hiệu lực.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Hồ sơ thực đơn và hình ảnh các món ăn tự sáng tạo (Culinary Portfolio)',
                    'description' => 'Hình ảnh trình bày món ăn đẹp mắt và công thức món ăn do giảng viên trực tiếp chế biến.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Du lịch' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp ngành Du lịch, Lữ hành, Khách sạn hoặc Hướng dẫn viên',
                    'description' => 'Bằng Cử nhân/Cao đẳng ngành Quản trị Dịch vụ Du lịch & Lữ hành, Hướng dẫn Du lịch.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Thẻ Hướng dẫn viên Du lịch Quốc tế / Nội địa do Tổng cục Du lịch cấp',
                    'description' => 'Thẻ hướng dẫn viên du lịch còn thời hạn sử dụng theo quy định pháp luật.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm điều hành tour hoặc Hướng dẫn thực tế',
                    'description' => 'Xác nhận công tác tại các công ty lữ hành, du lịch tối thiểu 2 năm.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Chăm sóc thú cưng' => [
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Bằng Bác sĩ Thú y hoặc Chứng chỉ Huấn luyện & Spa Thú cưng (Pet Grooming)',
                    'description' => 'Bằng tốt nghiệp Bác sĩ Thú y, Chứng chỉ Cắt tỉa lông thú cưng quốc tế (ISCC / IPG).',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Hành nghề Thú y hoặc Chứng nhận Chăm sóc & Dinh dưỡng Vật nuôi',
                    'description' => 'Chứng chỉ hành nghề khám chữa bệnh động vật hoặc chứng chỉ dinh dưỡng thú cưng.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Hình ảnh / Video minh chứng quá trình chăm sóc, grooming hoặc huấn luyện',
                    'description' => 'Tài liệu minh chứng sản phẩm cắt tỉa tạo hình hoặc kết quả huấn luyện thú cưng.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Trò chơi' => [
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng nhận Đào tạo / Thi đấu Thể thao Điện tử (Esports) hoặc Trò chơi Trí tuệ',
                    'description' => 'Chứng nhận Kiện tướng, Vận động viên Esports chuyên nghiệp hoặc Huấn luyện viên Cờ vua/Cờ tướng.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Giấy xác nhận thành tích thi đấu hoặc Chứng chỉ Huấn luyện viên Game',
                    'description' => 'Minh chứng đạt thứ hạng cao trong các giải đấu chuyên nghiệp hoặc chứng nhận HLV.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Video phân tích chiến thuật hoặc Kênh hướng dẫn chơi game chất lượng',
                    'description' => 'Link kênh video livestream, highlight hoặc giáo án phân tích meta game chuyên sâu.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Trang trí nhà cửa' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp ngành Kiến trúc, Thiết kế Mỹ thuật hoặc Chứng chỉ Home Decor',
                    'description' => 'Bằng tốt nghiệp Đại học/Cao đẳng ngành Kiến trúc, Thiết kế hoặc chứng chỉ Home Staging.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Phong thủy Ứng dụng hoặc Trang trí Không gian sống (Interior Styling)',
                    'description' => 'Chứng chỉ đào tạo phong thủy kiến trúc hoặc nghệ thuật sắp đặt không gian sống.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Hồ sơ hình ảnh trước/sau (Before/After) các công trình trang trí nhà ở',
                    'description' => 'Bộ ảnh chụp các góc không gian nội thất nhà ở, căn hộ thực tế đã hoàn thiện trang trí.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Thời trang' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp ngành Thời trang, Tạo mẫu hoặc Mỹ thuật Ứng dụng',
                    'description' => 'Bằng Cử nhân Thiết kế Thời trang, Mỹ thuật hoặc Nghệ thuật Biểu diễn.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Stylist / Định hình Phong cách Cá nhân (Fashion Stylist Certified)',
                    'description' => 'Chứng chỉ Fashion Styling quốc tế hoặc chứng nhận chuyên gia tư vấn phong cách.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Portfolio các bộ ảnh phối đồ (Lookbook) hoặc Dự án Stylist thực tế',
                    'description' => 'Bộ ảnh tạp chí, lookbook nhãn hàng hoặc hình ảnh tư vấn phong cách cho khách hàng.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],

            // =========================================================================
            // 10. NHÓM: NHIẾP ẢNH & VIDEO
            // =========================================================================
            // Danh mục cha
            'Nhiếp ảnh & Video' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng cấp Nhiếp ảnh / Truyền hình / Điện ảnh hoặc Nghệ thuật Thị giác',
                    'description' => 'Bằng tốt nghiệp Đại học/Cao đẳng ngành Quay phim, Nhiếp ảnh, Đạo diễn, Sân khấu Điện ảnh.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Chuyên môn Nhiếp ảnh / Dựng phim / Xử lý hậu kỳ',
                    'description' => 'Chứng chỉ Adobe Premiere Pro, DaVinci Resolve, Adobe Lightroom hoặc chứng chỉ nhiếp ảnh quốc tế.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Hồ sơ tác phẩm (Showreel Video hoặc Portfolio Ảnh chụp thực tế)',
                    'description' => 'Link video showreel hoặc album ảnh nghệ thuật/thương mại tiêu biểu đã thực hiện.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            // Danh mục con
            'Nhiếp ảnh kỹ thuật số' => [
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Đào tạo Nhiếp ảnh Kỹ thuật số Chuyên nghiệp (Professional Photography)',
                    'description' => 'Chứng chỉ nhiếp ảnh thương mại của Học viện Nhiếp ảnh hoặc các tổ chức quốc tế.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Xử lý Hậu kỳ Hình ảnh (Adobe Lightroom / Photoshop Master)',
                    'description' => 'Chứng chỉ Adobe Certified Professional hoặc chứng nhận Master Color Grading Photo.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Bộ ảnh chụp nghệ thuật theo nhiều thể loại (Photo Portfolio)',
                    'description' => 'Tệp PDF hoặc link Flickr/500px tập hợp các tác phẩm phong cảnh, đời thường, nghệ thuật xuất sắc.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Nhiếp ảnh chân dung' => [
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Đào tạo Nhiếp ảnh Chân dung / Kỹ thuật Ánh sáng Studio Lighting',
                    'description' => 'Chứng nhận chuyên sâu về ánh sáng Studio, Setup đèn chụp chân dung nghệ thuật.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Bộ ảnh Chân dung Nghệ thuật / Doanh nhân / Lookbook đã chụp',
                    'description' => 'Portfolio bộ ảnh chân dung thể hiện xuất sắc kỹ thuật bắt góc mặt và xử lý da, ánh sáng.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm nhiếp ảnh gia hoặc Hợp đồng dịch vụ chụp ảnh',
                    'description' => 'Minh chứng làm việc tại Studio hoặc thợ ảnh chuyên nghiệp tối thiểu 2 năm.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Nhiếp ảnh thương mại' => [
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Nhiếp ảnh Thương mại / Chụp ảnh Sản phẩm & Quảng cáo (Commercial Photo)',
                    'description' => 'Chứng nhận đào tạo chuyên sâu về Food Photography, Product Advertising Photography.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Bộ ảnh Quảng cáo Sản phẩm & Ẩm thực (Commercial Portfolio)',
                    'description' => 'Bộ sưu tập hình ảnh sản phẩm chất lượng cao phục vụ các chiến dịch quảng cáo.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận hợp tác chụp ảnh với các nhãn hàng / Doanh nghiệp',
                    'description' => 'Hợp đồng hoặc thư xác nhận dịch vụ chụp ảnh quảng cáo cho thương hiệu.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Quay phim' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp ngành Đạo diễn Hình ảnh (DOP), Quay phim hoặc Truyền hình',
                    'description' => 'Bằng Cử nhân/Cao đẳng Sân khấu Điện ảnh, Báo chí Truyền hình chuyên ngành Quay phim.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Vận hành Thiết bị Quay phim Chuyên dụng / Gimbal / Ánh sáng trường quay',
                    'description' => 'Chứng nhận kỹ thuật vận hành máy quay Cinema (Sony, RED, ARRI, Blackmagic) hoặc Flycam.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Showreel các thước phim / Video ngắn đã trực tiếp bấm máy',
                    'description' => 'Video showreel tổng hợp những cảnh quay đẹp nhất do chính giảng viên thực hiện.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Dựng và chỉnh sửa video' => [
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Dựng phim Chuyên nghiệp (Adobe Premiere Pro / DaVinci Resolve / Final Cut Pro)',
                    'description' => 'Chứng chỉ quốc tế Certified Professional của Adobe, Blackmagic Design hoặc Apple.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Kỹ xảo Video / Chỉnh màu (Adobe After Effects / DaVinci Color)',
                    'description' => 'Chứng nhận Motion Graphics, VFX hoặc Color Grading chuyên nghiệp.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Showreel các video đã biên tập và hậu kỳ hoàn chỉnh (Showreel Editor)',
                    'description' => 'Link YouTube/Vimeo video showreel thể hiện kỹ thuật cắt ghép nhịp nhàng, âm thanh và chuyển cảnh.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Công cụ nhiếp ảnh' => [
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Đào tạo Kỹ thuật Thiết bị Nhiếp ảnh & Ống kính (Camera Gear & Optics)',
                    'description' => 'Chứng chỉ chuyên sâu về công nghệ máy ảnh số, cảm biến và kỹ thuật quang học.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng nhận Hậu kỳ chuyên sâu (Capture One / Adobe Lightroom Master)',
                    'description' => 'Chứng chỉ Capture One Certified Professional hoặc tương đương.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Bộ ảnh minh chứng kỹ thuật chụp với các loại ống kính và điều kiện ánh sáng',
                    'description' => 'Bộ ảnh thể hiện thành thạo thông số kỹ thuật, khẩu độ, tốc độ, ISO trong các môi trường phức tạp.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Sản xuất nội dung video' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp ngành Truyền thông, Báo chí hoặc Sáng tạo Nội dung',
                    'description' => 'Bằng Cử nhân ngành Truyền thông đại chúng, Báo chí điện tử hoặc Điện ảnh.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Xây kênh Video Ngắn / Sáng tạo Video YouTube, TikTok (Video Creator)',
                    'description' => 'Chứng nhận đào tạo Video Creator, Storytelling for Video hoặc YouTube Certified.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Minh chứng các video triệu view hoặc Kênh video có lượng người xem lớn',
                    'description' => 'Link các video ngắn viral, kênh YouTube/TikTok do chính giảng viên sáng tạo và sản xuất.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],

            // =========================================================================
            // 11. NHÓM: SỨC KHỎE & THỂ CHẤT
            // =========================================================================
            // Danh mục cha
            'Sức khỏe & Thể chất' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng cấp Y khoa / Thể dục Thể thao / Dinh dưỡng hoặc Y học Cổ truyền',
                    'description' => 'Bằng Cử nhân ĐH Sư phạm TDTT, Đại học Y Dược, Y tế Công cộng hoặc tương đương.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Huấn luyện viên Quốc tế hoặc Chứng chỉ Hành nghề Y tế',
                    'description' => 'Chứng chỉ NASM, ACE, Yoga Alliance, Chứng chỉ Hành nghề Bác sĩ/Dược sĩ/Điều dưỡng.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Sơ cấp cứu hồi sức tim phổi (CPR / First Aid Certified)',
                    'description' => 'Chứng chỉ sơ cấp cứu do Hội Chữ thập đỏ hoặc cơ quan y tế có thẩm quyền cấp.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            // Danh mục con
            'Fitness' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp ĐH Thể dục Thể thao hoặc Chứng chỉ Huấn luyện viên Cá nhân Quốc tế (NASM, ACE, ISSA)',
                    'description' => 'Bằng Đại học TDTT hoặc Chứng chỉ Certified Personal Trainer (CPT) từ NASM, ACE, ISSA.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Sơ cấp cứu và Hồi sức Tim phổi (CPR / AED Certified)',
                    'description' => 'Chứng chỉ cấp cứu cơ bản còn thời hạn hiệu lực bắt buộc đối với huấn luyện viên thể hình.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm huấn luyện thể hình tối thiểu 2 năm',
                    'description' => 'Minh chứng công tác tại các trung tâm thể hình (Fitness Center) hoặc câu lạc bộ thể thao.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Dinh dưỡng' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng Bác sĩ / Cử nhân Dinh dưỡng, Y đa khoa hoặc Công nghệ Thực phẩm',
                    'description' => 'Bằng Đại học chuyên ngành Dinh dưỡng học, Y đa khoa, Y học dự phòng.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Tư vấn Dinh dưỡng Lâm sàng / Dinh dưỡng Thể thao Quốc tế (ISSN / Precision Nutrition)',
                    'description' => 'Chứng chỉ Certified Sports Nutritionist (CISSN), Precision Nutrition Level 1 hoặc Viện Dinh Dưỡng Quốc Gia.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm tư vấn dinh dưỡng hoặc Thiết kế thực đơn khoa học',
                    'description' => 'Xác nhận công tác tại phòng khám, bệnh viện hoặc trung tâm tư vấn dinh dưỡng.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Yoga' => [
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Giáo viên Yoga Quốc tế (Yoga Alliance RYT 200 / RYT 500)',
                    'description' => 'Chứng chỉ hoàn thành khóa đào tạo giáo viên Yoga 200 giờ hoặc 500 giờ chuẩn quốc tế.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Sơ cấp cứu ban đầu (CPR / First Aid)',
                    'description' => 'Chứng nhận hoàn thành kỹ năng sơ cấp cứu chấn thương thể thao và hồi sức tim phổi.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm giảng dạy Yoga tại các trung tâm / Phòng tập',
                    'description' => 'Minh chứng tối thiểu 2 năm trực tiếp đứng lớp giảng dạy bộ môn Yoga.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Thiền' => [
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Huấn luyện Thiền định & Chánh niệm (Mindfulness / Meditation Teacher)',
                    'description' => 'Chứng chỉ giáo viên thiền định được công nhận bởi tổ chức quốc tế hoặc trung tâm uy tín.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng nhận hoàn thành các khóa tu tập Thiền chuyên sâu (Vipassana / Zen Retreat)',
                    'description' => 'Chứng chỉ tham gia các khóa tu thiền miên mật nâng cao.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm hướng dẫn các khóa thiền / Trị liệu tinh thần',
                    'description' => 'Minh chứng đã từng dẫn dắt các khóa thiền chánh niệm cho học viên.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Thể thao' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp Đại học Thể dục Thể thao hoặc Bằng Huấn luyện viên Chuyên ngành',
                    'description' => 'Bằng Đại học TDTT chuyên sâu môn thể thao giảng dạy (Bóng đá, Tennis, Bơi lội, Cầu lông, Bóng rổ...).',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng nhận Vận động viên / Trọng tài / HLV cấp quốc gia hoặc quốc tế',
                    'description' => 'Chứng chỉ HLV bằng A/B/C của Liên đoàn thể thao tương ứng.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận công tác huấn luyện tại các câu lạc bộ thể thao',
                    'description' => 'Minh chứng kinh nghiệm huấn luyện viên tại các CLB, trung tâm TDTT.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Khiêu vũ' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp Trường Múa / Nghệ thuật Biểu diễn hoặc Chứng chỉ Vũ công Chuyên nghiệp',
                    'description' => 'Bằng tốt nghiệp Cao đẳng/Đại học Múa hoặc Học viện Nghệ thuật biểu diễn.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Huấn luyện viên Khiêu vũ / Zumba / Dance Fitness Quốc tế',
                    'description' => 'Chứng chỉ Zumba Instructor Network (ZIN), WDSF Dancesport Trainer hoặc tương đương.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Video biểu diễn hoặc Giấy xác nhận giảng dạy bộ môn nhảy/khiêu vũ',
                    'description' => 'Video biểu diễn vũ đạo chuyên nghiệp thể hiện kỹ thuật chuẩn xác và thần thái biểu diễn.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Sức khỏe tổng quát' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp Bác sĩ Y khoa, Cử nhân Y tế Công cộng hoặc Điều dưỡng',
                    'description' => 'Bằng Bác sĩ Đa khoa, Bác sĩ Y học Cổ truyền hoặc Cử nhân Điều dưỡng chính quy.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Hành nghề Khám bệnh, Chữa bệnh do Sở Y tế / Bộ Y tế cấp',
                    'description' => 'Chứng chỉ hành nghề khám chữa bệnh hợp pháp còn thời hạn.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận công tác tại cơ sở y tế / Bệnh viện uy tín',
                    'description' => 'Minh chứng công tác tối thiểu 2 năm tại các cơ sở khám chữa bệnh được cấp phép.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'An toàn và sơ cứu' => [
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Bằng cấp Y tế / Cứu hộ hoặc Chứng chỉ Giảng viên Sơ cấp cứu Quốc tế (Red Cross / AHA BLS)',
                    'description' => 'Chứng chỉ Giảng viên Sơ cấp cứu của Hội Chữ thập đỏ hoặc Hiệp hội Tim mạch Hoa Kỳ (AHA).',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Huấn luyện An toàn Vệ sinh Lao động & Phòng cháy chữa cháy',
                    'description' => 'Chứng chỉ an toàn lao động do Cục An toàn Lao động cấp phép.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm đào tạo kỹ năng sơ cấp cứu thoát hiểm',
                    'description' => 'Minh chứng trực tiếp huấn luyện sơ cấp cứu và cứu hộ cho doanh nghiệp, trường học.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],

            // =========================================================================
            // 12. NHÓM: ÂM NHẠC
            // =========================================================================
            // Danh mục cha
            'Âm nhạc' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng cấp Nhạc viện / Học viện Âm nhạc / Nghệ thuật Biểu diễn',
                    'description' => 'Bằng Cử nhân, Thạc sĩ chuyên ngành Âm nhạc học, Thanh nhạc, Nhạc cụ hoặc Sáng tác.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Trình độ Âm nhạc Quốc tế (ABRSM / Trinity / Rockschool)',
                    'description' => 'Chứng chỉ ABRSM Grade 6+, Trinity College London hoặc Rockschool.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Video biểu diễn âm nhạc hoặc Tác phẩm sản xuất thực tế',
                    'description' => 'Video trình diễn nhạc cụ, giọng hát hoặc bản phối âm nhạc chất lượng cao.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            // Danh mục con
            'Nhạc cụ' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp chuyên ngành Nhạc cụ (Piano, Guitar, Violin, Trống...) tại Nhạc viện',
                    'description' => 'Bằng tốt nghiệp Cử nhân chuyên ngành biểu diễn nhạc cụ tại Nhạc viện hoặc ĐH Văn hóa Nghệ thuật.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Âm nhạc Quốc tế (ABRSM Grade 6+ / Trinity Grade 6+)',
                    'description' => 'Chứng chỉ ABRSM hoặc Trinity College London tương ứng nhạc cụ giảng dạy.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Video trình tấu nhạc cụ hoàn chỉnh (Music Performance Video)',
                    'description' => 'Video quay trực tiếp quá trình diễn tấu tác phẩm cổ điển hoặc hiện đại thể hiện kỹ thuật điêu luyện.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Thanh nhạc' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp chuyên ngành Thanh nhạc tại Học viện Âm nhạc / ĐH Nghệ thuật',
                    'description' => 'Bằng Cử nhân chuyên ngành Thanh nhạc, Biểu diễn âm nhạc.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Sư phạm Âm nhạc hoặc Đào tạo Giọng hát Chuyên nghiệp (Vocal Coach Certified)',
                    'description' => 'Chứng chỉ Vocal Coaching quốc tế (Estill Voice, SLS) hoặc chứng nhận Sư phạm Âm nhạc.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Video biểu diễn ca hát thể hiện kỹ thuật thanh nhạc chuẩn xác',
                    'description' => 'Video thu âm mộc hoặc biểu diễn trực tiếp trên sân khấu thể hiện quãng giọng và kỹ thuật thanh nhạc.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Sản xuất âm nhạc' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng cấp chuyên ngành Âm học, Sản xuất Âm nhạc hoặc Công nghệ Âm thanh',
                    'description' => 'Bằng Cử nhân chuyên ngành Music Production, Sound Engineering tại các trường đào tạo nghệ thuật.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Kỹ sư Âm thanh / Music Producer Quốc tế (Berklee Online, Point Blank, SAE)',
                    'description' => 'Chứng nhận hoàn thành khóa đào tạo sản xuất âm nhạc chuyên nghiệp từ các học viện quốc tế.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Portfolio các bản phối khí / Track nhạc đã phát hành chính thức',
                    'description' => 'Link Spotify, Apple Music, YouTube hoặc SoundCloud các ca khúc/bản nhạc đã trực tiếp hòa âm phối khí.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Lý thuyết âm nhạc' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng Cử nhân Sư phạm Âm nhạc hoặc Lý luận Âm nhạc',
                    'description' => 'Bằng tốt nghiệp chuyên ngành Lý luận âm nhạc, Sáng tác hoặc Sư phạm Âm nhạc.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Lý thuyết Âm nhạc ABRSM (Music Theory Grade 5+)',
                    'description' => 'Chứng chỉ ABRSM Music Theory từ Grade 5 đến Grade 8 chính thức.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm giảng dạy nhạc lý và ký xướng âm',
                    'description' => 'Minh chứng đã từng giảng dạy lý thuyết âm nhạc tại các trường nghệ thuật hoặc trung tâm âm nhạc.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Kỹ thuật âm nhạc' => [
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Kỹ thuật Thu âm, Mixing & Mastering (Audio Engineering Certified)',
                    'description' => 'Chứng nhận chuyên sâu về kỹ thuật cân chỉnh âm thanh, phòng thu và xử lý acoustic.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Phần mềm Âm thanh Chuyên nghiệp (Avid Pro Tools Certified User / Expert)',
                    'description' => 'Chứng chỉ Avid Certified User: Pro Tools hoặc Logic Pro X Certified.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Bản thu âm trước và sau khi xử lý Mastering (A/B Test Audio)',
                    'description' => 'Tệp audio so sánh chất lượng âm thanh gốc và sau khi hoàn thiện Mixing/Mastering.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Phần mềm sản xuất âm nhạc' => [
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Sử dụng Phần mềm Âm nhạc Chuyên sâu (FL Studio, Logic Pro, Ableton Live, Cubase)',
                    'description' => 'Chứng nhận Ableton Certified Trainer, Steinberg Certified Trainer hoặc FL Studio Power User.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'File Project hoàn chỉnh trên phần mềm DAW kèm sản phẩm Audio xuất ra',
                    'description' => 'Ảnh chụp hoặc file project (.flp, .als, .cpr, .logicx) minh chứng khả năng sắp xếp project khoa học.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm hòa âm phối khí trên máy tính',
                    'description' => 'Minh chứng tối thiểu 2 năm kinh nghiệm làm việc với các phần mềm âm nhạc kỹ thuật số.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],

            // =========================================================================
            // 13. NHÓM: GIẢNG DẠY & HỌC THUẬT
            // =========================================================================
            // Danh mục cha
            'Giảng dạy & Học thuật' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng cấp Sư phạm / Thạc sĩ / Tiến sĩ chuyên ngành giảng dạy',
                    'description' => 'Bằng Cử nhân Sư phạm, Thạc sĩ hoặc Tiến sĩ chuyên ngành học thuật tương ứng.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Nghiệp vụ Sư phạm hoặc Chứng chỉ Ngoại ngữ Quốc tế',
                    'description' => 'Chứng chỉ Nghiệp vụ Sư phạm Giảng viên Đại học/Cao đẳng hoặc chứng chỉ chuyên môn.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận công tác giảng dạy tại trường học hoặc tổ chức giáo dục',
                    'description' => 'Minh chứng tối thiểu 2 năm trực tiếp tham gia giảng dạy học thuật.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            // Danh mục con
            'Toán học' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng Cử nhân / Thạc sĩ Sư phạm Toán hoặc Toán - Tin học',
                    'description' => 'Bằng tốt nghiệp Đại học Sư phạm ngành Toán học, Toán ứng dụng hoặc Toán cơ.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Nghiệp vụ Sư phạm hoặc Bồi dưỡng Học sinh Giỏi Toán',
                    'description' => 'Chứng chỉ nghiệp vụ sư phạm hoặc chứng nhận bồi dưỡng chuyên môn toán học nâng cao.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm giảng dạy bộ môn Toán tối thiểu 2 năm',
                    'description' => 'Xác nhận công tác tại các trường THPT, Đại học hoặc trung tâm luyện thi uy tín.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Khoa học' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp Sư phạm Vật lý, Hóa học, Sinh học hoặc Khoa học Tự nhiên',
                    'description' => 'Bằng Cử nhân Sư phạm các môn Khoa học tự nhiên hoặc Kỹ thuật ứng dụng.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Phương pháp Giảng dạy Khoa học Thực nghiệm / Giáo dục STEM',
                    'description' => 'Chứng chỉ đào tạo giáo viên STEM/STEAM quốc tế hoặc phương pháp giảng dạy tích hợp.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận công tác giảng dạy môn Khoa học tại các cơ sở giáo dục',
                    'description' => 'Minh chứng kinh nghiệm giảng dạy hoặc hướng dẫn nghiên cứu khoa học học sinh/sinh viên.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Kỹ thuật' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng Kỹ sư / Thạc sĩ chuyên ngành Kỹ thuật (Điện, Cơ khí, Tự động hóa, Xây dựng)',
                    'description' => 'Bằng tốt nghiệp Đại học Bách Khoa hoặc các trường kỹ thuật hàng đầu.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Hành nghề Kỹ sư hoặc Chứng chỉ Kỹ thuật Chuyên môn (AutoCAD, SolidWorks, PLC)',
                    'description' => 'Chứng chỉ hành nghề thiết kế kỹ thuật hoặc chứng chỉ phần mềm kỹ thuật quốc tế.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm làm việc hoặc giảng dạy kỹ thuật',
                    'description' => 'Minh chứng tối thiểu 2 năm làm việc thực tế tại các công trình hoặc giảng dạy chuyên ngành.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Khoa học xã hội' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp chuyên ngành Lịch sử, Địa lý, Xã hội học hoặc Sư phạm KHXH',
                    'description' => 'Bằng Cử nhân/Thạc sĩ các chuyên ngành Khoa học Xã hội và Nhân văn.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Bồi dưỡng Phương pháp Giảng dạy Khoa học Xã hội',
                    'description' => 'Chứng chỉ nghiệp vụ sư phạm hoặc chứng nhận đào tạo nghiên cứu xã hội học.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm giảng dạy hoặc Công trình nghiên cứu khoa học xã hội',
                    'description' => 'Minh chứng công tác tại các trường học, viện nghiên cứu hoặc bài báo khoa học đã xuất bản.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Nhân văn' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng Cử nhân / Thạc sĩ chuyên ngành Văn học, Triết học, Ngôn ngữ hoặc Văn hóa',
                    'description' => 'Bằng tốt nghiệp Cử nhân hoặc Sau đại học các khối ngành Nhân văn, Ngữ văn.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Nghiệp vụ Sư phạm hoặc Chứng nhận Giảng dạy Nhân văn',
                    'description' => 'Chứng chỉ nghiệp vụ sư phạm bậc Trung học hoặc Đại học.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm giảng dạy các môn Nhân văn',
                    'description' => 'Minh chứng quá trình giảng dạy Văn học, Triết học hoặc Văn hóa tại cơ sở đào tạo.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Ngoại ngữ' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp chuyên ngành Ngôn ngữ (Anh, Trung, Nhật, Hàn, Pháp) hoặc Sư phạm Ngoại ngữ',
                    'description' => 'Bằng Cử nhân Sư phạm Tiếng Anh/Ngoại ngữ hoặc Cử nhân Ngôn ngữ học.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Năng lực Ngoại ngữ Quốc tế (IELTS 7.5+, HSK 5+, JLPT N2+, TOPIK 5+)',
                    'description' => 'Chứng chỉ ngoại ngữ quốc tế đạt chuẩn đầu ra cao cấp (IELTS, TOEFL, HSK, JLPT, TOPIK, DELF/DALF).',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Phương pháp Giảng dạy Quốc tế (TESOL, CELTA, TEFL, TKT)',
                    'description' => 'Chứng chỉ giảng dạy tiếng Anh quốc tế TESOL (tối thiểu 120 giờ), Cambridge CELTA hoặc tương đương.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Đào tạo giáo viên' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng Thạc sĩ / Tiến sĩ chuyên ngành Quản lý Giáo dục, Sư phạm hoặc Đo lường Đánh giá',
                    'description' => 'Bằng Sau đại học chuyên ngành Giáo dục học, Phương pháp giảng dạy, Quản lý giáo dục.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Đào tạo Giảng viên Nòng cốt (Training of Trainers - ToT Certified)',
                    'description' => 'Chứng chỉ ToT Quốc tế, Chứng chỉ Giảng viên Đào tạo Sư phạm nâng cao.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm bồi dưỡng chuyên môn cho giáo viên',
                    'description' => 'Minh chứng đã từng tập huấn, bồi dưỡng phương pháp giảng dạy cho giáo viên tại các trường.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Luyện thi' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp Đại học chuyên ngành tương ứng môn luyện thi',
                    'description' => 'Bằng tốt nghiệp Đại học đúng chuyên ngành môn học phụ trách luyện thi.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'transcript',
                    'document_title' => 'Bảng điểm / Bằng chứng đạt điểm số thủ khoa, giải quốc gia hoặc điểm thi xuất sắc',
                    'description' => 'Minh chứng thành tích thi cử xuất sắc (Giải Quốc gia, Điểm thi ĐH 9+, Chứng chỉ điểm số tối đa).',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'employment_confirmation',
                    'document_title' => 'Giấy xác nhận kinh nghiệm luyện thi hoặc Bảng vàng thành tích học sinh đỗ đạt cao',
                    'description' => 'Minh chứng tối thiểu 2 năm kinh nghiệm luyện thi chuyển cấp, thi THPT Quốc gia hoặc thi chứng chỉ.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
            'Giáo dục trực tuyến' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng tốt nghiệp Đại học / Cao đẳng ngành Sư phạm hoặc Công nghệ Giáo dục (EdTech)',
                    'description' => 'Bằng tốt nghiệp ngành Sư phạm, Công nghệ Giáo dục, Khoa học Máy tính hoặc liên quan.',
                    'is_required' => true,
                    'sort_order' => 1,
                ],
                [
                    'document_type' => 'certificate',
                    'document_title' => 'Chứng chỉ Thiết kế Bài giảng E-Learning / Quản lý Hệ thống LMS (Moodle, Canvas)',
                    'description' => 'Chứng chỉ thiết kế khóa học trực tuyến (Instructional Design), Articulate Storyline hoặc Quản trị LMS.',
                    'is_required' => true,
                    'sort_order' => 2,
                ],
                [
                    'document_type' => 'other',
                    'document_title' => 'Video bài giảng trực tuyến mẫu đạt chuẩn kỹ thuật âm thanh, hình ảnh',
                    'description' => 'Video demo bài giảng trực tuyến thể hiện kỹ năng tương tác camera, trình bày slide và sư phạm số.',
                    'is_required' => true,
                    'sort_order' => 3,
                ],
            ],
        ];

        $totalCategoriesConfigured = count($categoryConfigs);
        $totalRequirementsSeeded = 0;

        foreach ($categoryConfigs as $categoryName => $requirements) {
            // Tìm category chính xác theo tên, hoặc theo slug
            $category = Category::where('name', $categoryName)->first();
            if (! $category) {
                $category = Category::where('slug', Str::slug($categoryName))->first();
            }
            if (! $category) {
                $category = Category::where('name', 'like', "%{$categoryName}%")->first();
            }

            if (! $category) {
                $category = Category::create([
                    'name' => $categoryName,
                    'slug' => Str::slug($categoryName),
                    'status' => true,
                    'sort_order' => 1,
                ]);
            }

            foreach ($requirements as $reqData) {
                InstructorDocumentRequirement::updateOrCreate(
                    [
                        'category_id' => $category->id,
                        'document_title' => $reqData['document_title'],
                    ],
                    [
                        'document_type' => $reqData['document_type'],
                        'description' => $reqData['description'] ?? null,
                        'is_required' => $reqData['is_required'] ?? true,
                        'is_active' => true,
                        'sort_order' => $reqData['sort_order'] ?? 1,
                    ]
                );
                $totalRequirementsSeeded++;
            }
        }

        echo "✓ Đã nạp thành công {$totalRequirementsSeeded} yêu cầu hồ sơ/chứng chỉ bắt buộc cho {$totalCategoriesConfigured} danh mục (gồm toàn bộ danh mục cha và danh mục con)!\n";
    }
}
