<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'id' => 1,
                'name' => 'Lập trình Web',
                'slug' => Str::slug('Lập trình Web'),
                'description' => 'Các khóa học về lập trình Frontend, Backend, Fullstack (PHP, JS, Python, Go...).',
                'icon' => 'code',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Lập trình Di động',
                'slug' => Str::slug('Lập trình Di động'),
                'description' => 'Phát triển ứng dụng di động cho iOS và Android (React Native, Flutter, Swift, Kotlin...).',
                'icon' => 'smartphone',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Khoa học dữ liệu & AI',
                'slug' => Str::slug('Khoa học dữ liệu AI'),
                'description' => 'Khoa học dữ liệu, học máy (Machine Learning), AI, và xử lý dữ liệu lớn (Big Data).',
                'icon' => 'trending-up',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Thiết kế UI/UX',
                'slug' => Str::slug('Thiết kế UI UX'),
                'description' => 'Nắm vững tư duy thiết kế trải nghiệm người dùng (UX) và thiết kế giao diện (UI) bằng Figma.',
                'icon' => 'feather',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'name' => 'Digital Marketing',
                'slug' => Str::slug('Digital Marketing'),
                'description' => 'SEO, Google Ads, Facebook Ads, content marketing và chiến lược quảng bá thương hiệu.',
                'icon' => 'globe',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'name' => 'Kỹ năng mềm',
                'slug' => Str::slug('Kỹ năng mềm'),
                'description' => 'Kỹ năng thuyết trình, quản lý thời gian, làm việc nhóm và giao tiếp chuyên nghiệp.',
                'icon' => 'users',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('categories')->insert($categories);
    }
}
