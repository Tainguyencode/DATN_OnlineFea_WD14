<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('lesson_progress')) {
            return;
        }

        Schema::table('lesson_progress', function (Blueprint $table) {
            if (! Schema::hasColumn('lesson_progress', 'last_position_seconds')) {
                $table->unsignedInteger('last_position_seconds')->default(0)->after('duration_seconds');
            }

            if (! Schema::hasColumn('lesson_progress', 'furthest_position_seconds')) {
                $table->unsignedInteger('furthest_position_seconds')->default(0)->after('last_position_seconds');
            }

            if (! Schema::hasColumn('lesson_progress', 'last_client_updated_at')) {
                $table->timestamp('last_client_updated_at')->nullable()->after('last_watched_at');
            }
        });

        if (
            Schema::hasColumn('lesson_progress', 'last_position_seconds')
            && Schema::hasColumn('lesson_progress', 'current_time')
        ) {
            DB::table('lesson_progress')
                ->where('last_position_seconds', 0)
                ->where('current_time', '>', 0)
                ->update(['last_position_seconds' => DB::raw('current_time')]);
        }

        if (Schema::hasColumn('lesson_progress', 'furthest_position_seconds')) {
            DB::table('lesson_progress')
                ->where('furthest_position_seconds', 0)
                ->where('watched_seconds', '>', 0)
                ->update(['furthest_position_seconds' => DB::raw('watched_seconds')]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('lesson_progress')) {
            return;
        }

        Schema::table('lesson_progress', function (Blueprint $table) {
            $columns = [];

            foreach (['last_client_updated_at', 'furthest_position_seconds', 'last_position_seconds'] as $column) {
                if (Schema::hasColumn('lesson_progress', $column)) {
                    $columns[] = $column;
                }
            }

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
