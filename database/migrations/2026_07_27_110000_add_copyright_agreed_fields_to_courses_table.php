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
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'copyright_agreed')) {
                $table->boolean('copyright_agreed')->default(false);
            }
            if (!Schema::hasColumn('courses', 'copyright_agreed_at')) {
                $table->timestamp('copyright_agreed_at')->nullable();
            }
            if (!Schema::hasColumn('courses', 'copyright_agreed_by')) {
                $table->foreignId('copyright_agreed_by')->nullable()->constrained('users')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'copyright_agreed_by')) {
                $table->dropForeign(['copyright_agreed_by']);
                $table->dropColumn('copyright_agreed_by');
            }
            if (Schema::hasColumn('courses', 'copyright_agreed_at')) {
                $table->dropColumn('copyright_agreed_at');
            }
            if (Schema::hasColumn('courses', 'copyright_agreed')) {
                $table->dropColumn('copyright_agreed');
            }
        });
    }
};
