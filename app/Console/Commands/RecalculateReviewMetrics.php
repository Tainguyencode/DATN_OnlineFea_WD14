<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Models\Review;
use App\Services\ReviewService;
use Illuminate\Console\Command;

class RecalculateReviewMetrics extends Command
{
    protected $signature = 'reviews:recalculate {--course= : Chỉ đồng bộ một khóa học}';

    protected $description = 'Đồng bộ điểm đánh giá khóa học và số lượt hữu ích từ dữ liệu nguồn';

    public function handle(ReviewService $reviews): int
    {
        $courseId = $this->option('course');
        $courseQuery = Course::query()->when($courseId, fn ($query) => $query->whereKey($courseId));

        $courseQuery->select('id')->chunkById(100, function ($courses) use ($reviews) {
            foreach ($courses as $course) {
                $reviews->syncCourseRating($course->id);
            }
        });

        Review::query()->select('id')->chunkById(200, function ($reviewRows) use ($reviews) {
            foreach ($reviewRows as $review) {
                $reviews->syncHelpfulCount($review);
            }
        });

        $this->info('Đã đồng bộ chỉ số đánh giá khóa học.');

        return self::SUCCESS;
    }
}
