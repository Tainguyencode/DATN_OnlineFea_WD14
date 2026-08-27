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
        Schema::table('user_points', function (Blueprint $table) {
            if (! Schema::hasColumn('user_points', 'course_id')) {
                $table->foreignId('course_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('courses')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('user_points', 'reason')) {
                $table->string('reason')->nullable()->after('source');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_points', function (Blueprint $table) {
            if (Schema::hasColumn('user_points', 'course_id')) {
                $table->dropConstrainedForeignId('course_id');
            }

            if (Schema::hasColumn('user_points', 'reason')) {
                $table->dropColumn('reason');
            }
        });
    }
};
