<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('assignments')) {
            Schema::table('assignments', function (Blueprint $table) {
                if (! Schema::hasColumn('assignments', 'course_id')) {
                    $table->foreignId('course_id')->nullable()->after('id')->constrained('courses')->cascadeOnDelete();
                }
                if (! Schema::hasColumn('assignments', 'instructions')) {
                    $table->text('instructions')->nullable()->after('description');
                }
                if (! Schema::hasColumn('assignments', 'passing_score')) {
                    $table->unsignedInteger('passing_score')->default(70)->after('max_score');
                }
                if (! Schema::hasColumn('assignments', 'due_days')) {
                    $table->unsignedInteger('due_days')->nullable()->after('passing_score');
                }
                if (! Schema::hasColumn('assignments', 'is_required')) {
                    $table->boolean('is_required')->default(true)->after('due_days');
                }
                if (! Schema::hasColumn('assignments', 'allowed_file_types')) {
                    $table->string('allowed_file_types')->default('pdf,doc,docx,zip')->after('is_required');
                }
                if (! Schema::hasColumn('assignments', 'maximum_file_size')) {
                    $table->unsignedInteger('maximum_file_size')->default(10240)->after('allowed_file_types');
                }
            });
        }

        if (Schema::hasTable('submissions')) {
            Schema::table('submissions', function (Blueprint $table) {
                if (! Schema::hasColumn('submissions', 'graded_by')) {
                    $table->foreignId('graded_by')->nullable()->after('graded_at')->constrained('users')->nullOnDelete();
                }
            });

            DB::table('submissions')->where('status', 'returned')->update(['status' => 'resubmit_required']);
        }
    }

    public function down(): void
    {
        // Non-destructive.
    }
};
