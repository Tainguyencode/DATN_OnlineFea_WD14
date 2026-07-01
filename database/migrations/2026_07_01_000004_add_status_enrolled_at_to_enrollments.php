<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('enrollments')) {
            return;
        }

        Schema::table('enrollments', function (Blueprint $table) {
            if (! Schema::hasColumn('enrollments', 'status')) {
                $table->string('status')->default('active')->after('order_id');
            }

            if (! Schema::hasColumn('enrollments', 'enrolled_at')) {
                $table->timestamp('enrolled_at')->nullable()->after('progress_percent');
            }
        });

        if (Schema::hasColumn('enrollments', 'status')) {
            DB::statement("UPDATE enrollments SET status = 'active' WHERE status IS NULL OR status = ''");
        }

        if (Schema::hasColumn('enrollments', 'enrolled_at')) {
            DB::statement('UPDATE enrollments SET enrolled_at = created_at WHERE enrolled_at IS NULL');
        }
    }

    public function down(): void
    {
        // Non-destructive: enrollment status and join time are business history.
    }
};
