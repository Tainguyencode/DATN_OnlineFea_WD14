<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('course_reviews')) {
            $this->upgradeExistingTable();

            return;
        }

        Schema::create('course_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('submission_number')->default(1);
            $table->string('status', 32)->default('pending');
            $table->text('comment')->nullable();
            $table->json('checklist_json')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['course_id', 'submission_number']);
            $table->index(['status', 'submitted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_reviews');
    }

    private function upgradeExistingTable(): void
    {
        Schema::table('course_reviews', function (Blueprint $table) {
            if (! Schema::hasColumn('course_reviews', 'submission_number')) {
                $table->unsignedInteger('submission_number')->default(1)->after('reviewer_id');
            }

            if (! Schema::hasColumn('course_reviews', 'status')) {
                $table->string('status', 32)->default('pending')->after('submission_number');
            }

            if (! Schema::hasColumn('course_reviews', 'checklist_json')) {
                $table->json('checklist_json')->nullable()->after('comment');
            }

            if (! Schema::hasColumn('course_reviews', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable()->after('checklist_json');
            }
        });

        $columns = ['id', 'course_id'];
        $timestampColumn = Schema::hasColumn('course_reviews', 'created_at') ? 'created_at' : null;

        if (Schema::hasColumn('course_reviews', 'action')) {
            $columns[] = 'action';
        }

        if ($timestampColumn) {
            $columns[] = $timestampColumn;
        }

        if (Schema::hasColumn('course_reviews', 'reviewed_at')) {
            $columns[] = 'reviewed_at';
        }

        $reviewsQuery = DB::table('course_reviews')
            ->orderBy('course_id')
            ->orderBy('id');

        if ($timestampColumn) {
            $reviewsQuery->orderBy($timestampColumn);
        }

        $reviews = $reviewsQuery->get(array_unique($columns));

        $submissionNumbers = [];

        foreach ($reviews as $review) {
            $courseId = (int) $review->course_id;
            $submissionNumbers[$courseId] = ($submissionNumbers[$courseId] ?? 0) + 1;

            DB::table('course_reviews')
                ->where('id', $review->id)
                ->update([
                    'submission_number' => $submissionNumbers[$courseId],
                    'status' => $this->statusFromLegacyAction($review->action ?? null),
                    'submitted_at' => $review->created_at ?? $review->reviewed_at ?? now(),
                ]);
        }
    }

    private function statusFromLegacyAction(?string $action): string
    {
        return match ($action) {
            'approved' => 'approved',
            'rejected', 'need_revision' => 'rejected',
            default => 'pending',
        };
    }
};
