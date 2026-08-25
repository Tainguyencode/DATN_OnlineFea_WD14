<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\InstructorDocumentRequirement;
use Illuminate\Database\Seeder;

class InstructorDocumentRequirementSeeder extends Seeder
{
    public function run(): void
    {
        $categoryConfigs = [
            // 1. Lập trình & Phát triển (Web, Mobile, Data...)
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

            // 2. Kinh doanh & Quản trị
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
                    'description' => 'Giấy xác nhận công tác hoặc quyết định bổ nhiệm vị trí quản lý / chuyên gia.',
                    'is_required' => false,
                    'sort_order' => 3,
                ],
            ],

            // 3. Marketing & Truyền thông
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
                    'is_required' => false,
                    'sort_order' => 3,
                ],
            ],

            // 4. Tài chính & Kế toán
            'Tài chính & Kế toán' => [
                [
                    'document_type' => 'degree',
                    'document_title' => 'Bằng cấp chuyên ngành Tài chính - Ngân hàng / Kế toán',
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
                    'document_type' => 'transcript',
                    'document_title' => 'Bảng điểm hoặc Xác nhận kinh nghiệm tài chính',
                    'description' => 'Minh chứng quá trình học tập hoặc làm việc tại các tổ chức tài chính, kiểm toán.',
                    'is_required' => false,
                    'sort_order' => 3,
                ],
            ],

            // 5. Thiết kế & Sáng tạo
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

            // 6. Công nghệ thông tin & Phần mềm
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
            ],
        ];

        foreach ($categoryConfigs as $categoryName => $requirements) {
            // Tìm category theo tên hoặc slug
            $category = Category::where('name', 'like', "%{$categoryName}%")->first();
            if (! $category) {
                $category = Category::create([
                    'name' => $categoryName,
                    'slug' => \Illuminate\Support\Str::slug($categoryName),
                    'status' => true,
                    'sort_order' => 1,
                ]);
            }

            foreach ($requirements as $reqData) {
                InstructorDocumentRequirement::firstOrCreate(
                    [
                        'category_id' => $category->id,
                        'document_title' => $reqData['document_title'],
                    ],
                    [
                        'document_type' => $reqData['document_type'],
                        'description' => $reqData['description'],
                        'is_required' => $reqData['is_required'],
                        'is_active' => true,
                        'sort_order' => $reqData['sort_order'],
                    ]
                );
            }
        }
    }
}
