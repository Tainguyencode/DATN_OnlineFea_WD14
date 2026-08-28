<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table): void {
            if (! Schema::hasColumn('quiz_attempts', 'termination_reason')) {
                $table->string('termination_reason', 50)->nullable()->after('status');
            }
            if (! Schema::hasColumn('quiz_attempts', 'remaining_seconds')) {
                $table->unsignedInteger('remaining_seconds')->nullable()->after('termination_reason');
            }
            if (! Schema::hasColumn('quiz_attempts', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('completed_at');
            }
            if (! Schema::hasColumn('quiz_attempts', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('ip_address');
            }
        });

        // Ensure status column in MySQL supports 'terminated'
        $driver = DB::connection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE quiz_attempts MODIFY status ENUM('in_progress', 'completed', 'terminated', 'expired') NOT NULL DEFAULT 'completed'");
        }
    }

    public function down(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table): void {
            if (Schema::hasColumn('quiz_attempts', 'user_agent')) {
                $table->dropColumn('user_agent');
            }
            if (Schema::hasColumn('quiz_attempts', 'ip_address')) {
                $table->dropColumn('ip_address');
            }
            if (Schema::hasColumn('quiz_attempts', 'remaining_seconds')) {
                $table->dropColumn('remaining_seconds');
            }
            if (Schema::hasColumn('quiz_attempts', 'termination_reason')) {
                $table->dropColumn('termination_reason');
            }
        });

        $driver = DB::connection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE quiz_attempts MODIFY status ENUM('in_progress', 'completed', 'expired') NOT NULL DEFAULT 'completed'");
        }
    }
};
