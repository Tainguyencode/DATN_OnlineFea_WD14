<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('courses') || ! Schema::hasColumn('courses', 'status')) {
            return;
        }

        DB::table('courses')->where('status', 'pending')->update(['status' => 'submitted']);

        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE courses MODIFY status ENUM(
                'draft',
                'submitted',
                'need_revision',
                'approved',
                'published',
                'rejected',
                'archived'
            ) NOT NULL DEFAULT 'draft'");
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('courses') || ! Schema::hasColumn('courses', 'status')) {
            return;
        }

        DB::table('courses')->whereIn('status', ['submitted', 'need_revision', 'approved'])->update(['status' => 'pending']);

        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE courses MODIFY status ENUM(
                'draft',
                'pending',
                'published',
                'rejected',
                'archived'
            ) NOT NULL DEFAULT 'draft'");
        }
    }
};
