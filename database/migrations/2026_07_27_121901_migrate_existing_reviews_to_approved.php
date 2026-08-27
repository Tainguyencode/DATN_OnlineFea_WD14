<?php

use App\Models\Course;
use App\Services\ReviewService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update all pending or rejected reviews to approved
        DB::table('reviews')
            ->whereIn('status', ['pending', 'rejected'])
            ->update(['status' => 'approved']);

        // Recalculate course ratings using ReviewService
        $reviewsService = app(ReviewService::class);
        Course::query()->select('id')->chunkById(100, function ($courses) use ($reviewsService) {
            foreach ($courses as $course) {
                $reviewsService->syncCourseRating($course->id);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down migration is needed as it's a one-way data migration
    }
};
