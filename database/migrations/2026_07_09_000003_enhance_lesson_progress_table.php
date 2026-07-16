<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('lesson_progress')) {
            return;
        }

        Schema::table('lesson_progress', function (Blueprint $table) {
            if (! Schema::hasColumn('lesson_progress', 'course_id')) {
                $table->foreignId('course_id')->nullable()->after('lesson_id')->constrained('courses')->cascadeOnDelete();
            }
            if (! Schema::hasColumn('lesson_progress', 'duration_seconds')) {
                $table->unsignedInteger('duration_seconds')->default(0)->after('watched_seconds');
            }
            if (! Schema::hasColumn('lesson_progress', 'progress_percent')) {
                $table->decimal('progress_percent', 5, 2)->default(0)->after('duration_seconds');
            }
            if (! Schema::hasColumn('lesson_progress', 'last_watched_at')) {
                $table->timestamp('last_watched_at')->nullable()->after('completed_at');
            }
        });

        if (Schema::hasColumn('lesson_progress', 'course_id')) {
            DB::table('lesson_progress')
                ->whereNull('course_id')
                ->orderBy('id')
                ->chunkById(100, function ($rows) {
                    foreach ($rows as $row) {
                        $courseId = DB::table('lessons')
                            ->leftJoin('course_sections', 'course_sections.id', '=', 'lessons.section_id')
                            ->leftJoin('chapters', 'chapters.id', '=', 'lessons.chapter_id')
                            ->where('lessons.id', $row->lesson_id)
                            ->value(DB::raw('COALESCE(lessons.course_id, course_sections.course_id, chapters.course_id)'));

                        if ($courseId) {
                            DB::table('lesson_progress')->where('id', $row->id)->update(['course_id' => $courseId]);
                        }
                    }
                });
        }

        try {
            Schema::table('lesson_progress', function (Blueprint $table) {
                $table->unique(['user_id', 'lesson_id'], 'lesson_progress_user_id_lesson_id_unique');
            });
        } catch (Throwable) {
            // Index may already exist.
        }
    }

    public function down(): void
    {
        // Non-destructive.
    }
};
