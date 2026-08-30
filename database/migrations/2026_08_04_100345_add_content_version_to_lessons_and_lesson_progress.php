<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('lessons') && !Schema::hasColumn('lessons', 'content_version')) {
            Schema::table('lessons', function (Blueprint $table) {
                $table->unsignedInteger('content_version')->default(1)->after('status');
            });
        }

        if (Schema::hasTable('lesson_progress') && !Schema::hasColumn('lesson_progress', 'last_viewed_content_version')) {
            Schema::table('lesson_progress', function (Blueprint $table) {
                $table->unsignedInteger('last_viewed_content_version')->default(1)->after('completed_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('lessons') && Schema::hasColumn('lessons', 'content_version')) {
            Schema::table('lessons', function (Blueprint $table) {
                $table->dropColumn('content_version');
            });
        }

        if (Schema::hasTable('lesson_progress') && Schema::hasColumn('lesson_progress', 'last_viewed_content_version')) {
            Schema::table('lesson_progress', function (Blueprint $table) {
                $table->dropColumn('last_viewed_content_version');
            });
        }
    }
};
