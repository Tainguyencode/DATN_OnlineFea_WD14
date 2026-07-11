<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('courses')) {
            return;
        }

        DB::table('courses')->where('status', 'pending')->update(['status' => 'pending_review']);

        if (Schema::hasColumn('courses', 'status')) {
            $driver = DB::connection()->getDriverName();

            if ($driver === 'sqlite') {
                // SQLite stores status as text; values updated above are sufficient.
                return;
            }

            if ($driver === 'mysql') {
                DB::statement("ALTER TABLE courses MODIFY status VARCHAR(32) NOT NULL DEFAULT 'draft'");
            }
        }
    }

    public function down(): void
    {
        DB::table('courses')->where('status', 'pending_review')->update(['status' => 'pending']);
    }
};
