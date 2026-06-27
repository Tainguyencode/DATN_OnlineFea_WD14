<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $badges = [
            [
                'id' => 1,
                'name' => 'Học viên mới',
                'slug' => Str::slug('Học viên mới'),
                'description' => 'Tham gia khóa học đầu tiên trên hệ thống.',
                'icon' => 'award_new.png',
                'points_required' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Học viên chăm chỉ',
                'slug' => Str::slug('Học viên chăm chỉ'),
                'description' => 'Tích lũy được 50 điểm học tập qua các bài học.',
                'icon' => 'award_diligent.png',
                'points_required' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Bậc thầy tri thức',
                'slug' => Str::slug('Bậc thầy tri thức'),
                'description' => 'Đạt được 150 điểm học tập và hoàn thành xuất sắc các bài tập.',
                'icon' => 'award_master.png',
                'points_required' => 150,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Chiến thần học tập',
                'slug' => Str::slug('Chiến thần học tập'),
                'description' => 'Đạt được 300 điểm học tập, trở thành tấm gương học tập xuất sắc.',
                'icon' => 'award_warrior.png',
                'points_required' => 300,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('badges')->insert($badges);
    }
}
