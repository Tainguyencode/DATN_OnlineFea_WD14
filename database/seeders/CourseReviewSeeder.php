<?php

namespace Database\Seeders;

use App\Enums\CourseReviewStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseReviewSeeder extends Seeder
{
    public function run(): void
    {
        if (! DB::getSchemaBuilder()->hasTable('course_reviews')) {
            return;
        }

        DB::table('course_reviews')->insert([
            [
                'course_id' => 6,
                'reviewer_id' => null,
                'submission_number' => 1,
                'status' => CourseReviewStatus::Pending->value,
                'comment' => null,
                'checklist_json' => null,
                'submitted_at' => now()->subDays(2),
                'reviewed_at' => null,
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'course_id' => 1,
                'reviewer_id' => 1,
                'submission_number' => 1,
                'status' => CourseReviewStatus::Rejected->value,
                'comment' => 'Video bài 2 không có âm thanh, vui lòng cập nhật.',
                'checklist_json' => json_encode(['video_quality_ok' => false]),
                'submitted_at' => now()->subMonths(3),
                'reviewed_at' => now()->subMonths(3)->addDay(),
                'created_at' => now()->subMonths(3),
                'updated_at' => now()->subMonths(3)->addDay(),
            ],
            [
                'course_id' => 1,
                'reviewer_id' => 1,
                'submission_number' => 2,
                'status' => CourseReviewStatus::Approved->value,
                'comment' => null,
                'checklist_json' => json_encode(['course_info_complete' => true, 'no_empty_lessons' => true]),
                'submitted_at' => now()->subMonths(2)->subDays(3),
                'reviewed_at' => now()->subMonths(2),
                'created_at' => now()->subMonths(2)->subDays(3),
                'updated_at' => now()->subMonths(2),
            ],
        ]);
    }
}
