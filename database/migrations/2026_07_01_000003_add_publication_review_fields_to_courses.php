<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('courses')) {
            return;
        }

        Schema::table('courses', function (Blueprint $table) {
            if (! Schema::hasColumn('courses', 'reject_reason')) {
                $table->text('reject_reason')->nullable()->after('is_published');
            }

            if (! Schema::hasColumn('courses', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable()->after('published_at');
            }
        });

        if (Schema::hasColumn('courses', 'rejection_reason') && Schema::hasColumn('courses', 'reject_reason')) {
            DB::statement('UPDATE courses SET reject_reason = rejection_reason WHERE reject_reason IS NULL AND rejection_reason IS NOT NULL');
        }
    }

    public function down(): void
    {
        // Non-destructive: review history belongs to the LMS publishing workflow.
    }
};
