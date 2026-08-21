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
            if (! Schema::hasColumn('users', 'needs_admin_review')) {
                $table->boolean('needs_admin_review')->default(false)->after('instructor_status')->index();
            }
            if (! Schema::hasColumn('users', 'admin_last_reviewed_at')) {
                $table->timestamp('admin_last_reviewed_at')->nullable()->after('needs_admin_review');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['needs_admin_review', 'admin_last_reviewed_at']);
        });
    }
};
