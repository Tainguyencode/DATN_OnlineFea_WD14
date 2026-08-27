<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const DUPLICATE_LOOKUP_INDEX = 'lesson_import_batches_duplicate_lookup_index';

    private const QUIZ_LESSON_UNIQUE = 'quizzes_lesson_id_unique';

    public function up(): void
    {
        $this->assertQuizLessonsAreUnique();

        Schema::table('quizzes', function (Blueprint $table): void {
            $table->unique('lesson_id', self::QUIZ_LESSON_UNIQUE);
        });

        Schema::table('quiz_questions', function (Blueprint $table): void {
            $table->enum('type', ['single', 'multiple', 'true_false'])
                ->default('single')
                ->change();
        });

        Schema::table('lesson_import_batches', function (Blueprint $table): void {
            $table->json('result_payload')->nullable()->after('validation_report');
            $table->index(
                ['user_id', 'course_id', 'section_id', 'file_sha256', 'status'],
                self::DUPLICATE_LOOKUP_INDEX,
            );
        });
    }

    public function down(): void
    {
        if (DB::table('quiz_questions')->where('type', 'true_false')->exists()) {
            throw new RuntimeException(
                'Cannot roll back Quiz Foundation while true_false questions exist. Convert or remove them explicitly first.',
            );
        }

        Schema::table('lesson_import_batches', function (Blueprint $table): void {
            $table->dropIndex(self::DUPLICATE_LOOKUP_INDEX);
            $table->dropColumn('result_payload');
        });

        Schema::table('quiz_questions', function (Blueprint $table): void {
            $table->enum('type', ['single', 'multiple'])
                ->default('single')
                ->change();
        });

        Schema::table('quizzes', function (Blueprint $table): void {
            $table->dropUnique(self::QUIZ_LESSON_UNIQUE);
        });
    }

    private function assertQuizLessonsAreUnique(): void
    {
        $duplicates = DB::table('quizzes')
            ->select('lesson_id', DB::raw('COUNT(*) AS quiz_count'))
            ->groupBy('lesson_id')
            ->havingRaw('COUNT(*) > 1')
            ->orderBy('lesson_id')
            ->get();

        if ($duplicates->isEmpty()) {
            return;
        }

        $summary = $duplicates
            ->map(fn (object $row): string => sprintf(
                'lesson_id=%d (%d quizzes)',
                $row->lesson_id,
                $row->quiz_count,
            ))
            ->implode(', ');

        throw new RuntimeException(
            'Quiz Foundation migration stopped because duplicate quizzes require manual review: '.$summary,
        );
    }
};
