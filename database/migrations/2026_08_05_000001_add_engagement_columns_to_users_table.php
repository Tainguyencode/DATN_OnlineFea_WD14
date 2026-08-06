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
            $table->timestamp('last_learning_at')->nullable()->after('last_login_ip');
            $table->unsignedTinyInteger('engagement_email_stage')->default(0)->after('last_learning_at');
            $table->timestamp('last_engagement_sent_at')->nullable()->after('engagement_email_stage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'last_learning_at',
                'engagement_email_stage',
                'last_engagement_sent_at',
            ]);
        });
    }
};
