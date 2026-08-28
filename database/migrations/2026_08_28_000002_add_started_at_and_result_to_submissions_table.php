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
        Schema::table('submissions', function (Blueprint $table) {
            if (! Schema::hasColumn('submissions', 'started_at')) {
                $table->timestamp('started_at')->nullable()->after('user_id');
            }
            if (! Schema::hasColumn('submissions', 'result')) {
                $table->string('result', 20)->nullable()->after('score'); // 'pass', 'fail'
            }
            $table->string('status', 30)->default('in_progress')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            if (Schema::hasColumn('submissions', 'started_at')) {
                $table->dropColumn('started_at');
            }
            if (Schema::hasColumn('submissions', 'result')) {
                $table->dropColumn('result');
            }
        });
    }
};
