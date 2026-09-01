<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('content_updates', function (Blueprint $table) {
            $table->json('metadata')->nullable()->after('payload');
        });

        foreach (['course_versions', 'course_section_versions', 'lesson_versions', 'assignment_versions'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $table->foreignId('source_version_id')
                    ->nullable()
                    ->after('content_update_id')
                    ->constrained($tableName)
                    ->restrictOnDelete();
            });
        }
    }

    public function down(): void
    {
        foreach (['assignment_versions', 'lesson_versions', 'course_section_versions', 'course_versions'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropConstrainedForeignId('source_version_id');
            });
        }

        Schema::table('content_updates', function (Blueprint $table) {
            $table->dropColumn('metadata');
        });
    }
};
