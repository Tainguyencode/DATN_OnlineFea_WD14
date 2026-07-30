<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('reviews')) {
            return;
        }

        DB::table('reviews')
            ->where('is_hidden', true)
            ->update(['status' => 'hidden']);

        DB::table('reviews')
            ->whereIn('status', ['pending', 'approved'])
            ->where('is_hidden', false)
            ->update([
                'status' => 'visible',
                'is_hidden' => false,
            ]);

        DB::table('reviews')
            ->whereIn('status', ['rejected', 'hidden'])
            ->update([
                'status' => 'hidden',
                'is_hidden' => true,
            ]);

        $this->updateReviewStatusDefault('visible');
        $this->removeRetiredReviewPermissions();
        $this->recalculateCourseRatings('visible');
    }

    public function down(): void
    {
        if (! Schema::hasTable('reviews')) {
            return;
        }

        DB::table('reviews')
            ->where('status', 'visible')
            ->update([
                'status' => 'approved',
                'is_hidden' => false,
            ]);

        DB::table('reviews')
            ->where('status', 'hidden')
            ->update(['is_hidden' => true]);

        $this->updateReviewStatusDefault('pending');
        $this->restoreRetiredReviewPermissions();
        $this->recalculateCourseRatings('approved');
    }

    private function updateReviewStatusDefault(string $default): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE reviews MODIFY status VARCHAR(20) NOT NULL DEFAULT '{$default}'");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE reviews ALTER COLUMN status SET DEFAULT '{$default}'");
        }
    }

    private function removeRetiredReviewPermissions(): void
    {
        if (! Schema::hasTable('permissions') || ! Schema::hasTable('permission_role')) {
            return;
        }

        $permissionIds = DB::table('permissions')
            ->whereIn('slug', ['course_reviews.approve', 'course_reviews.reject'])
            ->pluck('id');

        if ($permissionIds->isEmpty()) {
            return;
        }

        DB::table('permission_role')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')->whereIn('id', $permissionIds)->delete();
    }

    private function restoreRetiredReviewPermissions(): void
    {
        if (! Schema::hasTable('permissions')) {
            return;
        }

        $permissions = [
            ['name' => 'Duyệt đánh giá khóa học', 'slug' => 'course_reviews.approve', 'group' => 'course_reviews'],
            ['name' => 'Từ chối đánh giá khóa học', 'slug' => 'course_reviews.reject', 'group' => 'course_reviews'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['slug' => $permission['slug']],
                array_merge($permission, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    private function recalculateCourseRatings(string $visibleStatus): void
    {
        if (! Schema::hasTable('courses')) {
            return;
        }

        DB::table('courses')->update(['rating_avg' => 0, 'rating_count' => 0]);

        DB::table('reviews')
            ->selectRaw('course_id, COUNT(*) as review_count, AVG(rating) as review_avg')
            ->where('status', $visibleStatus)
            ->where('is_hidden', false)
            ->whereNull('deleted_at')
            ->whereNull('parent_id')
            ->whereNotNull('rating')
            ->groupBy('course_id')
            ->orderBy('course_id')
            ->get()
            ->each(fn ($row) => DB::table('courses')->where('id', $row->course_id)->update([
                'rating_avg' => round((float) $row->review_avg, 2),
                'rating_count' => (int) $row->review_count,
            ]));
    }
};
