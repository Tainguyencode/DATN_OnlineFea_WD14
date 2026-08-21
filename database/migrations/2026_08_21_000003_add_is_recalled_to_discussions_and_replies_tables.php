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
        if (Schema::hasTable('discussions') && !Schema::hasColumn('discussions', 'is_recalled')) {
            Schema::table('discussions', function (Blueprint $table) {
                $table->boolean('is_recalled')->default(false)->after('is_resolved');
            });
        }

        if (Schema::hasTable('discussion_replies') && !Schema::hasColumn('discussion_replies', 'is_recalled')) {
            Schema::table('discussion_replies', function (Blueprint $table) {
                $table->boolean('is_recalled')->default(false)->after('is_helpful');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('discussions') && Schema::hasColumn('discussions', 'is_recalled')) {
            Schema::table('discussions', function (Blueprint $table) {
                $table->dropColumn('is_recalled');
            });
        }

        if (Schema::hasTable('discussion_replies') && Schema::hasColumn('discussion_replies', 'is_recalled')) {
            Schema::table('discussion_replies', function (Blueprint $table) {
                $table->dropColumn('is_recalled');
            });
        }
    }
};
