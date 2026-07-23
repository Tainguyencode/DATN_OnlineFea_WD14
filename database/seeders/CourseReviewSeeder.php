<?php

namespace Database\Seeders;

use App\Enums\CourseReviewStatus;
use App\Enums\CourseStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseReviewSeeder extends Seeder
{
    public function run(): void
    {
        if (! DB::getSchemaBuilder()->hasTable('course_reviews')) {
            return;
        }

        $courseIds = DB::table('courses')->orderBy('id')->pluck('id');
        $reviewerId = DB::table('users')->where('role', 'admin')->value('id');

        if ($courseIds->isEmpty() || ! $reviewerId) {
            return;
        }

        $reviewedCourseId = (int) $courseIds->first();
        $pendingCourseId = (int) ($courseIds->get(1) ?? $reviewedCourseId);
        $pendingSubmittedAt = now()->subDays(2);

        DB::table('courses')->where('id', $pendingCourseId)->update([
            'status' => CourseStatus::PendingReview->value,
            'is_published' => false,
            'submitted_at' => $pendingSubmittedAt,
            'submission_count' => 1,
            'updated_at' => now(),
        ]);

        DB::table('course_reviews')->insert([
            [
                'course_id' => $pendingCourseId,
                'reviewer_id' => null,
                'submission_number' => 1,
                'status' => CourseReviewStatus::Pending->value,
                'comment' => null,
                'checklist_json' => null,
                'submitted_at' => $pendingSubmittedAt,
                'reviewed_at' => null,
                'created_at' => $pendingSubmittedAt,
                'updated_at' => $pendingSubmittedAt,
            ],
            [
                'course_id' => $reviewedCourseId,
                'reviewer_id' => $reviewerId,
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
                'course_id' => $reviewedCourseId,
                'reviewer_id' => $reviewerId,
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
