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
        Schema::table('lessons', function (Blueprint $table) {
            $table->unsignedInteger('content_version')->default(1)->after('status');
        });

        Schema::table('lesson_progress', function (Blueprint $table) {
            $table->unsignedInteger('last_viewed_content_version')->default(1)->after('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn('content_version');
        });

        Schema::table('lesson_progress', function (Blueprint $table) {
            $table->dropColumn('last_viewed_content_version');
        });
    }
};
