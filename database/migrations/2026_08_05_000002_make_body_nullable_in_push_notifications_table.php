<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('push_notifications') && Schema::hasColumn('push_notifications', 'body')) {
            Schema::table('push_notifications', function (Blueprint $table) {
                $table->text('body')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('push_notifications') && Schema::hasColumn('push_notifications', 'body')) {
            Schema::table('push_notifications', function (Blueprint $table) {
                $table->text('body')->nullable(false)->change();
            });
        }
    }
};
