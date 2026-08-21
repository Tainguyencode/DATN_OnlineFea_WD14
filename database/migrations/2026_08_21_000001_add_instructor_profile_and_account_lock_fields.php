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
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'account_status')) {
                $table->enum('account_status', ['active', 'locked', 'suspended'])
                    ->default('active')
                    ->after('role');
            }
            if (! Schema::hasColumn('users', 'locked_at')) {
                $table->timestamp('locked_at')->nullable()->after('account_status');
            }
            if (! Schema::hasColumn('users', 'locked_reason')) {
                $table->text('locked_reason')->nullable()->after('locked_at');
            }
            if (! Schema::hasColumn('users', 'reactivation_requested_at')) {
                $table->timestamp('reactivation_requested_at')->nullable()->after('locked_reason');
            }
            if (! Schema::hasColumn('users', 'reactivation_status')) {
                $table->enum('reactivation_status', ['none', 'pending', 'approved', 'rejected'])
                    ->default('none')
                    ->after('reactivation_requested_at');
            }
            if (! Schema::hasColumn('users', 'reactivation_reason')) {
                $table->text('reactivation_reason')->nullable()->after('reactivation_status');
            }
            if (! Schema::hasColumn('users', 'profile_deadline_at')) {
                $table->timestamp('profile_deadline_at')->nullable()->after('submitted_for_review_at');
            }
        });

        Schema::table('instructor_certificates', function (Blueprint $table) {
            if (! Schema::hasColumn('instructor_certificates', 'document_type')) {
                $table->string('document_type', 50)
                    ->default('certificate')
                    ->after('title');
            }
        });

        Schema::table('instructor_profiles', function (Blueprint $table) {
            if (! Schema::hasColumn('instructor_profiles', 'organization')) {
                $table->string('organization')->nullable()->after('phone');
            }
            if (! Schema::hasColumn('instructor_profiles', 'position')) {
                $table->string('position')->nullable()->after('organization');
            }
            if (! Schema::hasColumn('instructor_profiles', 'teaching_field')) {
                $table->string('teaching_field')->nullable()->after('position');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'account_status',
                'locked_at',
                'locked_reason',
                'reactivation_requested_at',
                'reactivation_status',
                'reactivation_reason',
                'profile_deadline_at',
            ]);
        });

        Schema::table('instructor_certificates', function (Blueprint $table) {
            $table->dropColumn(['document_type']);
        });

        Schema::table('instructor_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'organization',
                'position',
                'teaching_field',
            ]);
        });
    }
};
