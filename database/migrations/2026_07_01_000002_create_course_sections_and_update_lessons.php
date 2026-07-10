<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('course_sections')) {
            Schema::create('course_sections', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
                $table->string('title');
                $table->text('description')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (Schema::hasTable('lessons')) {
            Schema::table('lessons', function (Blueprint $table) {
                if (! Schema::hasColumn('lessons', 'course_id')) {
                    $table->foreignId('course_id')->nullable()->after('id')->constrained('courses')->cascadeOnDelete();
                }

                if (! Schema::hasColumn('lessons', 'section_id')) {
                    $table->foreignId('section_id')->nullable()->after('course_id')->constrained('course_sections')->cascadeOnDelete();
                }

                if (! Schema::hasColumn('lessons', 'document_file')) {
                    $table->string('document_file')->nullable()->after('content');
                }

                if (! Schema::hasColumn('lessons', 'duration')) {
                    $table->unsignedInteger('duration')->nullable()->after('document_file');
                }

                if (! Schema::hasColumn('lessons', 'status')) {
                    $table->enum('status', ['draft', 'published'])->default('draft')->after('sort_order');
                }
            });

            if (DB::connection()->getDriverName() === 'mysql' && Schema::hasColumn('lessons', 'chapter_id')) {
                $this->dropForeignKeyIfExists('lessons', 'lessons_chapter_id_foreign');
                DB::statement('ALTER TABLE lessons MODIFY chapter_id BIGINT UNSIGNED NULL');
                $this->addLegacyChapterForeignKey();
            }

            $this->backfillSectionsFromChapters();
        }
    }

    public function down(): void
    {
        // Non-destructive by design. Curriculum data can include files, section
        // ordering, and lesson text, so rollback should not discard it.
    }

    private function backfillSectionsFromChapters(): void
    {
        if (! Schema::hasTable('chapters') || ! Schema::hasTable('course_sections')) {
            return;
        }

        DB::table('chapters')
            ->orderBy('id')
            ->get()
            ->each(function ($chapter) {
                $section = DB::table('course_sections')
                    ->where('course_id', $chapter->course_id)
                    ->where('title', $chapter->title)
                    ->where('sort_order', $chapter->sort_order)
                    ->first();

                if (! $section) {
                    $sectionId = DB::table('course_sections')->insertGetId([
                        'course_id' => $chapter->course_id,
                        'title' => $chapter->title,
                        'description' => null,
                        'sort_order' => $chapter->sort_order,
                        'created_at' => $chapter->created_at ?? now(),
                        'updated_at' => $chapter->updated_at ?? now(),
                    ]);
                } else {
                    $sectionId = $section->id;
                }

                DB::table('lessons')
                    ->where('chapter_id', $chapter->id)
                    ->update([
                        'course_id' => $chapter->course_id,
                        'section_id' => $sectionId,
                        'duration' => DB::raw('COALESCE(duration, duration_seconds)'),
                    ]);
            });
    }

    private function dropForeignKeyIfExists(string $table, string $foreignKey): void
    {
        try {
            Schema::table($table, function (Blueprint $table) use ($foreignKey) {
                $table->dropForeign($foreignKey);
            });
        } catch (Throwable) {
            //
        }
    }

    private function addLegacyChapterForeignKey(): void
    {
        try {
            Schema::table('lessons', function (Blueprint $table) {
                $table->foreign('chapter_id')->references('id')->on('chapters')->nullOnDelete();
            });
        } catch (Throwable) {
            //
        }
    }
};
