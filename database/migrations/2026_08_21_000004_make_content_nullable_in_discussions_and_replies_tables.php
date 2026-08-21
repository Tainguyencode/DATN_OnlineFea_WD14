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
        if (Schema::hasTable('discussions')) {
            Schema::table('discussions', function (Blueprint $table) {
                $table->text('content')->nullable()->change();
            });
        }

        if (Schema::hasTable('discussion_replies')) {
            Schema::table('discussion_replies', function (Blueprint $table) {
                $table->text('content')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('discussions')) {
            Schema::table('discussions', function (Blueprint $table) {
                $table->text('content')->nullable(false)->change();
            });
        }

        if (Schema::hasTable('discussion_replies')) {
            Schema::table('discussion_replies', function (Blueprint $table) {
                $table->text('content')->nullable(false)->change();
            });
        }
    }
};
