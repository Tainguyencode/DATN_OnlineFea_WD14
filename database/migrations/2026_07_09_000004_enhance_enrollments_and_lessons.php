<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('enrollments')) {
            Schema::table('enrollments', function (Blueprint $table) {
                if (! Schema::hasColumn('enrollments', 'completed_lessons')) {
                    $table->unsignedInteger('completed_lessons')->default(0)->after('progress_percent');
                }
                if (! Schema::hasColumn('enrollments', 'total_lessons')) {
                    $table->unsignedInteger('total_lessons')->default(0)->after('completed_lessons');
                }
                if (! Schema::hasColumn('enrollments', 'last_accessed_at')) {
                    $table->timestamp('last_accessed_at')->nullable()->after('completed_at');
                }
            });
        }

        if (Schema::hasTable('lessons') && ! Schema::hasColumn('lessons', 'is_required')) {
            Schema::table('lessons', function (Blueprint $table) {
                $table->boolean('is_required')->default(true)->after('is_preview');
            });
        }
    }

    public function down(): void
    {
        // Non-destructive.
    }
};
