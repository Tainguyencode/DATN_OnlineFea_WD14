<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->string('status', 20)->default('pending')->after('comment');
            $table->unsignedInteger('helpful_count')->default(0)->after('status');
            $table->text('instructor_reply')->nullable()->after('helpful_count');
            $table->foreignId('replied_by')->nullable()->after('instructor_reply')->constrained('users')->nullOnDelete();
            $table->timestamp('replied_at')->nullable()->after('replied_by');
            $table->foreignId('moderated_by')->nullable()->after('replied_at')->constrained('users')->nullOnDelete();
            $table->timestamp('moderated_at')->nullable()->after('moderated_by');
            $table->text('moderation_note')->nullable()->after('moderated_at');
            $table->boolean('verified_purchase')->default(false)->after('moderation_note');
            $table->softDeletes();

            $table->index(['course_id', 'status', 'created_at'], 'reviews_course_status_created_index');
            $table->index(['course_id', 'rating', 'status'], 'reviews_course_rating_status_index');
        });

        Schema::create('review_helpful', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained('reviews')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['review_id', 'user_id'], 'review_helpful_review_user_unique');
            $table->index(['user_id', 'created_at'], 'review_helpful_user_created_index');
        });

        // Reviews created before moderation existed were already public, so preserve that visibility.
        DB::table('reviews')->where('status', 'pending')->update(['status' => 'approved']);
        DB::table('courses')->update(['rating_avg' => 0, 'rating_count' => 0]);
        DB::table('reviews')
            ->selectRaw('course_id, COUNT(*) as review_count, AVG(rating) as review_avg')
            ->where('status', 'approved')
            ->whereNull('deleted_at')
            ->groupBy('course_id')
            ->orderBy('course_id')
            ->get()
            ->each(fn ($row) => DB::table('courses')->where('id', $row->course_id)->update([
                'rating_avg' => round((float) $row->review_avg, 2),
                'rating_count' => (int) $row->review_count,
            ]));
    }

    public function down(): void
    {
        Schema::dropIfExists('review_helpful');

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex('reviews_course_status_created_index');
            $table->dropIndex('reviews_course_rating_status_index');
            $table->dropConstrainedForeignId('replied_by');
            $table->dropConstrainedForeignId('moderated_by');
            $table->dropColumn([
                'status',
                'helpful_count',
                'instructor_reply',
                'replied_at',
                'moderated_at',
                'moderation_note',
                'verified_purchase',
                'deleted_at',
            ]);
        });
    }
};
