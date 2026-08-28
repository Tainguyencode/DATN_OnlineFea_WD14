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
        Schema::table('submissions', function (Blueprint $table) {
            if (! Schema::hasColumn('submissions', 'attempt_number')) {
                $table->unsignedSmallInteger('attempt_number')->default(1)->after('user_id');
            }
            if (! Schema::hasColumn('submissions', 'allowed_attempts')) {
                $table->unsignedSmallInteger('allowed_attempts')->default(2)->after('attempt_number');
            }
            if (! Schema::hasColumn('submissions', 'granted_by')) {
                $table->foreignId('granted_by')->nullable()->after('graded_by')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('submissions', 'granted_at')) {
                $table->timestamp('granted_at')->nullable()->after('granted_by');
            }
            if (! Schema::hasColumn('submissions', 'grant_reason')) {
                $table->string('grant_reason')->nullable()->after('granted_at');
            }

            // Drop foreign key to allow dropping unique index in MySQL
            $table->dropForeign(['assignment_id']);
            $table->dropUnique('submissions_assignment_id_user_id_unique');

            // Add new unique index with attempt_number and restore foreign key
            $table->unique(['assignment_id', 'user_id', 'attempt_number'], 'submissions_assignment_user_attempt_unique');
            $table->foreign('assignment_id')->references('id')->on('assignments')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropForeign(['assignment_id']);
            $table->dropUnique('submissions_assignment_user_attempt_unique');
            $table->unique(['assignment_id', 'user_id'], 'submissions_assignment_id_user_id_unique');
            $table->foreign('assignment_id')->references('id')->on('assignments')->cascadeOnDelete();

            if (Schema::hasColumn('submissions', 'granted_by')) {
                $table->dropForeign(['granted_by']);
            }
            $columnsToDrop = array_filter([
                'attempt_number',
                'allowed_attempts',
                'granted_by',
                'granted_at',
                'grant_reason',
            ], fn($c) => Schema::hasColumn('submissions', $c));

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
