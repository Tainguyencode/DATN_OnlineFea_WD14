<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('push_notifications')) {
            Schema::table('push_notifications', function (Blueprint $table) {
                if (! Schema::hasColumn('push_notifications', 'created_by')) {
                    $table->foreignId('created_by')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
                } else {
                    $table->unsignedBigInteger('created_by')->nullable()->change();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('push_notifications') && Schema::hasColumn('push_notifications', 'created_by')) {
            Schema::table('push_notifications', function (Blueprint $table) {
                $table->dropColumn('created_by');
            });
        }
    }
};
