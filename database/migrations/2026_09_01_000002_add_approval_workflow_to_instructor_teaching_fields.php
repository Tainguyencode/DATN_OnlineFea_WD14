<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instructor_profile_teaching_fields', function (Blueprint $table) {
            if (! Schema::hasColumn('instructor_profile_teaching_fields', 'approval_status')) {
                // A newly-created field is never effective until a reviewer approves it.
                // The explicit backfill below is intentionally limited to instructors that
                // had already been globally approved before this workflow existed.
                $table->enum('approval_status', ['draft', 'pending', 'approved', 'rejected', 'superseded'])
                    ->default('draft')
                    ->after('is_primary');
            }
            if (! Schema::hasColumn('instructor_profile_teaching_fields', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable()->after('approval_status');
            }
            if (! Schema::hasColumn('instructor_profile_teaching_fields', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('submitted_at');
            }
            if (! Schema::hasColumn('instructor_profile_teaching_fields', 'reviewed_by')) {
                $table->foreignId('reviewed_by')->nullable()->after('reviewed_at')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('instructor_profile_teaching_fields', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('reviewed_by');
            }
            if (! Schema::hasColumn('instructor_profile_teaching_fields', 'replace_of_teaching_field_id')) {
                $table->foreignId('replace_of_teaching_field_id')->nullable()->after('rejection_reason');
                $table->foreign('replace_of_teaching_field_id', 'itf_replace_of_field_fk')
                    ->references('id')
                    ->on('instructor_profile_teaching_fields')
                    ->nullOnDelete();
            }
        });

        // Existing approved instructors retain every existing field as an effective field.
        DB::table('instructor_profile_teaching_fields as field')
            ->join('instructor_profiles as profile', 'profile.id', '=', 'field.instructor_profile_id')
            ->join('users', 'users.id', '=', 'profile.user_id')
            ->where('users.instructor_status', 'approved')
            ->update(['field.approval_status' => 'approved']);

        Schema::table('instructor_certificates', function (Blueprint $table) {
            if (! Schema::hasColumn('instructor_certificates', 'instructor_teaching_field_id')) {
                $table->foreignId('instructor_teaching_field_id')->nullable()->after('requirement_id')
                    ->constrained('instructor_profile_teaching_fields')->nullOnDelete();
                $table->index(['instructor_teaching_field_id', 'status'], 'idx_cert_teaching_field_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('instructor_certificates', function (Blueprint $table) {
            if (Schema::hasColumn('instructor_certificates', 'instructor_teaching_field_id')) {
                $table->dropIndex('idx_cert_teaching_field_status');
                $table->dropConstrainedForeignId('instructor_teaching_field_id');
            }
        });

        Schema::table('instructor_profile_teaching_fields', function (Blueprint $table) {
            if (Schema::hasColumn('instructor_profile_teaching_fields', 'replace_of_teaching_field_id')) {
                $table->dropForeign('itf_replace_of_field_fk');
                $table->dropColumn('replace_of_teaching_field_id');
            }
            if (Schema::hasColumn('instructor_profile_teaching_fields', 'reviewed_by')) {
                $table->dropConstrainedForeignId('reviewed_by');
            }
            $columns = array_filter([
                Schema::hasColumn('instructor_profile_teaching_fields', 'approval_status') ? 'approval_status' : null,
                Schema::hasColumn('instructor_profile_teaching_fields', 'submitted_at') ? 'submitted_at' : null,
                Schema::hasColumn('instructor_profile_teaching_fields', 'reviewed_at') ? 'reviewed_at' : null,
                Schema::hasColumn('instructor_profile_teaching_fields', 'rejection_reason') ? 'rejection_reason' : null,
            ]);
            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
