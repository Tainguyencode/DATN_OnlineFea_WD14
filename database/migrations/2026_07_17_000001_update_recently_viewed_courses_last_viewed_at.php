<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('recently_viewed_courses')) {
            return;
        }

        if (Schema::hasColumn('recently_viewed_courses', 'viewed_at')
            && ! Schema::hasColumn('recently_viewed_courses', 'last_viewed_at')) {
            Schema::table('recently_viewed_courses', function (Blueprint $table) {
                $table->renameColumn('viewed_at', 'last_viewed_at');
            });
        }

        if (! Schema::hasColumn('recently_viewed_courses', 'last_viewed_at')) {
            Schema::table('recently_viewed_courses', function (Blueprint $table) {
                $table->timestamp('last_viewed_at')->useCurrent()->after('course_id');
            });
        }

        Schema::table('recently_viewed_courses', function (Blueprint $table) {
            $table->index(['user_id', 'last_viewed_at'], 'recently_viewed_courses_user_last_viewed_at_index');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('recently_viewed_courses')) {
            return;
        }

        if (Schema::hasColumn('recently_viewed_courses', 'last_viewed_at')) {
            Schema::table('recently_viewed_courses', function (Blueprint $table) {
                $table->dropIndex('recently_viewed_courses_user_last_viewed_at_index');
            });
        }

        if (Schema::hasColumn('recently_viewed_courses', 'last_viewed_at')
            && ! Schema::hasColumn('recently_viewed_courses', 'viewed_at')) {
            Schema::table('recently_viewed_courses', function (Blueprint $table) {
                $table->renameColumn('last_viewed_at', 'viewed_at');
            });
        }
    }
};
