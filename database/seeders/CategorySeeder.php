<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            [
                'name' => 'Lập trình & Phát triển',
                'icon' => 'code',
                'children' => [
                    ['name' => 'Phát triển Web', 'aliases' => ['lap-trinh-web']],
                    ['name' => 'Phát triển ứng dụng Mobile', 'aliases' => ['lap-trinh-di-dong']],
                    ['name' => 'Lập trình Game'],
                    ['name' => 'Khoa học dữ liệu', 'aliases' => ['khoa-hoc-du-lieu-ai']],
                    ['name' => 'Trí tuệ nhân tạo và Machine Learning'],
                    ['name' => 'Ngôn ngữ lập trình'],
                    ['name' => 'Cơ sở dữ liệu'],
                    ['name' => 'Kiểm thử phần mềm'],
                    ['name' => 'Công cụ No-code'],
                ],
            ],
            [
                'name' => 'Kinh doanh',
                'icon' => 'briefcase',
                'children' => [
                    ['name' => 'Khởi nghiệp'],
                    ['name' => 'Giao tiếp'],
                    ['name' => 'Quản trị doanh nghiệp'],
                    ['name' => 'Bán hàng'],
                    ['name' => 'Chiến lược kinh doanh'],
                    ['name' => 'Quản lý vận hành'],
                    ['name' => 'Quản lý dự án'],
                    ['name' => 'Thương mại điện tử'],
                    ['name' => 'Quản trị nhân sự'],
                    ['name' => 'Phân tích kinh doanh'],
                ],
            ],
            [
                'name' => 'Tài chính & Kế toán',
                'icon' => 'banknote',
                'children' => [
                    ['name' => 'Kế toán và ghi sổ'],
                    ['name' => 'Tài chính doanh nghiệp'],
                    ['name' => 'Đầu tư và giao dịch'],
                    ['name' => 'Phân tích tài chính'],
                    ['name' => 'Kinh tế học'],
                    ['name' => 'Thuế'],
                    ['name' => 'Tài chính cá nhân'],
                    ['name' => 'Tiền điện tử và Blockchain'],
                ],
            ],
            [
                'name' => 'Công nghệ thông tin & Phần mềm',
                'icon' => 'server',
                'children' => [
                    ['name' => 'Chứng chỉ CNTT'],
                    ['name' => 'An ninh mạng'],
                    ['name' => 'Mạng máy tính'],
                    ['name' => 'Hệ điều hành'],
                    ['name' => 'Điện toán đám mây'],
                    ['name' => 'Phần cứng'],
                    ['name' => 'DevOps'],
                    ['name' => 'Quản trị hệ thống'],
                ],
            ],
            [
                'name' => 'Tin học văn phòng',
                'icon' => 'file-spreadsheet',
                'children' => [
                    ['name' => 'Microsoft Excel'],
                    ['name' => 'Microsoft Word'],
                    ['name' => 'Microsoft PowerPoint'],
                    ['name' => 'Microsoft Office'],
                    ['name' => 'Google Workspace'],
                    ['name' => 'Power BI'],
                    ['name' => 'SAP'],
                ],
            ],
            [
                'name' => 'Phát triển cá nhân',
                'icon' => 'users',
                'children' => [
                    ['name' => 'Kỹ năng lãnh đạo'],
                    ['name' => 'Quản lý thời gian'],
                    ['name' => 'Phát triển sự nghiệp'],
                    ['name' => 'Kỹ năng giao tiếp', 'aliases' => ['ky-nang-mem']],
                    ['name' => 'Tư duy sáng tạo'],
                    ['name' => 'Thương hiệu cá nhân'],
                    ['name' => 'Quản lý căng thẳng'],
                    ['name' => 'Kỹ năng học tập'],
                    ['name' => 'Động lực và sự tự tin'],
                ],
            ],
            [
                'name' => 'Thiết kế',
                'icon' => 'palette',
                'children' => [
                    ['name' => 'Thiết kế đồ họa'],
                    ['name' => 'Thiết kế UI/UX', 'aliases' => ['thiet-ke-ui-ux']],
                    ['name' => 'Thiết kế Web'],
                    ['name' => 'Thiết kế 3D'],
                    ['name' => 'Adobe Photoshop'],
                    ['name' => 'Adobe Illustrator'],
                    ['name' => 'Thiết kế nội thất'],
                    ['name' => 'Kiến trúc'],
                    ['name' => 'Thiết kế thời trang'],
                ],
            ],
            [
                'name' => 'Marketing',
                'icon' => 'megaphone',
                'children' => [
                    ['name' => 'Digital Marketing', 'aliases' => ['digital-marketing']],
                    ['name' => 'SEO'],
                    ['name' => 'Marketing mạng xã hội'],
                    ['name' => 'Xây dựng thương hiệu'],
                    ['name' => 'Content Marketing'],
                    ['name' => 'Quảng cáo trả phí'],
                    ['name' => 'Email Marketing'],
                    ['name' => 'Affiliate Marketing'],
                    ['name' => 'Marketing Analytics'],
                    ['name' => 'Product Marketing'],
                ],
            ],
            [
                'name' => 'Phong cách sống',
                'icon' => 'sparkles',
                'children' => [
                    ['name' => 'Nghệ thuật và thủ công'],
                    ['name' => 'Làm đẹp'],
                    ['name' => 'Nấu ăn'],
                    ['name' => 'Du lịch'],
                    ['name' => 'Chăm sóc thú cưng'],
                    ['name' => 'Trò chơi'],
                    ['name' => 'Trang trí nhà cửa'],
                    ['name' => 'Thời trang'],
                ],
            ],
            [
                'name' => 'Nhiếp ảnh & Video',
                'icon' => 'camera',
                'children' => [
                    ['name' => 'Nhiếp ảnh kỹ thuật số'],
                    ['name' => 'Nhiếp ảnh chân dung'],
                    ['name' => 'Nhiếp ảnh thương mại'],
                    ['name' => 'Quay phim'],
                    ['name' => 'Dựng và chỉnh sửa video'],
                    ['name' => 'Công cụ nhiếp ảnh'],
                    ['name' => 'Sản xuất nội dung video'],
                ],
            ],
            [
                'name' => 'Sức khỏe & Thể chất',
                'icon' => 'heart-pulse',
                'children' => [
                    ['name' => 'Fitness'],
                    ['name' => 'Dinh dưỡng'],
                    ['name' => 'Yoga'],
                    ['name' => 'Thiền'],
                    ['name' => 'Thể thao'],
                    ['name' => 'Khiêu vũ'],
                    ['name' => 'Sức khỏe tổng quát'],
                    ['name' => 'An toàn và sơ cứu'],
                ],
            ],
            [
                'name' => 'Âm nhạc',
                'icon' => 'music',
                'children' => [
                    ['name' => 'Nhạc cụ'],
                    ['name' => 'Thanh nhạc'],
                    ['name' => 'Sản xuất âm nhạc'],
                    ['name' => 'Lý thuyết âm nhạc'],
                    ['name' => 'Kỹ thuật âm nhạc'],
                    ['name' => 'Phần mềm sản xuất âm nhạc'],
                ],
            ],
            [
                'name' => 'Giảng dạy & Học thuật',
                'icon' => 'graduation-cap',
                'children' => [
                    ['name' => 'Toán học'],
                    ['name' => 'Khoa học'],
                    ['name' => 'Kỹ thuật'],
                    ['name' => 'Khoa học xã hội'],
                    ['name' => 'Nhân văn'],
                    ['name' => 'Ngoại ngữ'],
                    ['name' => 'Đào tạo giáo viên'],
                    ['name' => 'Luyện thi'],
                    ['name' => 'Giáo dục trực tuyến'],
                ],
            ],
        ];

        DB::transaction(function () use ($groups) {
            foreach ($groups as $parentIndex => $group) {
                $parent = $this->persistCategory([
                    'parent_id' => null,
                    'name' => $group['name'],
                    'slug' => $this->slug($group['name']),
                    'description' => null,
                    'icon' => $group['icon'],
                    'status' => true,
                    'sort_order' => $parentIndex + 1,
                ]);

                foreach ($group['children'] as $childIndex => $child) {
                    $this->persistCategory([
                        'parent_id' => $parent->id,
                        'name' => $child['name'],
                        'slug' => $this->slug($child['name']),
                        'description' => null,
                        'icon' => $group['icon'],
                        'status' => true,
                        'sort_order' => $childIndex + 1,
                    ], $child['aliases'] ?? []);
                }
            }
        });
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @param  array<int, string>  $aliases
     */
    private function persistCategory(array $attributes, array $aliases = []): Category
    {
        $category = Category::where('slug', $attributes['slug'])->first();

        if (! $category && $aliases !== []) {
            $category = Category::whereIn('slug', $aliases)->first();
        }

        $category ??= new Category;
        $category->fill($attributes);
        $category->save();

        return $category;
    }

    private function slug(string $name): string
    {
        return Str::slug($name) ?: 'category';
    }
}
