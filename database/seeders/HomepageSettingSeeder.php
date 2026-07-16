<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HomepageSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'id' => 1,
                'key' => 'hero_section',
                'value' => json_encode([
                    'title' => 'Khám Phá Tri Thức - Nâng Tầm Sự Nghiệp',
                    'subtitle' => 'Hệ thống học lập trình trực tuyến hiện đại với các lộ trình chuyên nghiệp và bài tập thực hành phong phú.',
                    'banner_url' => 'https://example.com/images/hero-banner.jpg',
                    'cta_text' => 'Bắt đầu học ngay',
                    'cta_url' => '/courses',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'key' => 'featured_courses',
                'value' => json_encode([
                    'course_ids' => [1, 2, 3],
                    'title' => 'Khóa Học Nổi Bật',
                    'subtitle' => 'Những khóa học được đánh giá cao và thu hút nhiều học viên tham gia nhất.',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'key' => 'statistics',
                'value' => json_encode([
                    'students_count' => '15,000+',
                    'courses_count' => '120+',
                    'instructors_count' => '30+',
                    'rating_avg' => '4.8/5',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('homepage_settings')->insert($settings);
    }
}
