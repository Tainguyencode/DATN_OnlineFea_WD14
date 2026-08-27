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
            if (! Schema::hasColumn('user_points', 'reference_id')) {
                $table->unsignedBigInteger('reference_id')
                    ->nullable()
                    ->after('course_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_points', function (Blueprint $table) {
            if (Schema::hasColumn('user_points', 'reference_id')) {
                $table->dropColumn('reference_id');
            }
        });
    }
};
