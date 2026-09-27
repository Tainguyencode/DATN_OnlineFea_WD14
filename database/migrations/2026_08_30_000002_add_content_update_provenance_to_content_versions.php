<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['course_versions', 'course_section_versions', 'lesson_versions', 'assignment_versions'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->foreignId('content_update_id')
                    ->nullable()
                    ->after('status')
                    ->constrained('content_updates')
                    ->nullOnDelete();
                $table->timestamp('rejected_at')->nullable()->after('superseded_at');
            });
        }
    }

    public function down(): void
    {
        foreach (['assignment_versions', 'lesson_versions', 'course_section_versions', 'course_versions'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropConstrainedForeignId('content_update_id');
                $table->dropColumn('rejected_at');
            });
        }
    }
};
