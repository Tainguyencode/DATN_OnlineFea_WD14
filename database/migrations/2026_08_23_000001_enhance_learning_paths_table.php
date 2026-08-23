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
        Schema::table('learning_paths', function (Blueprint $table) {
            if (! Schema::hasColumn('learning_paths', 'target_role')) {
                $table->string('target_role')->nullable()->after('level');
            }
            if (! Schema::hasColumn('learning_paths', 'salary_range')) {
                $table->string('salary_range')->nullable()->after('target_role');
            }
            if (! Schema::hasColumn('learning_paths', 'estimated_duration')) {
                $table->string('estimated_duration')->nullable()->after('salary_range');
            }
            if (! Schema::hasColumn('learning_paths', 'skills')) {
                $table->json('skills')->nullable()->after('estimated_duration');
            }
            if (! Schema::hasColumn('learning_paths', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('skills');
            }
        });

        Schema::table('learning_path_courses', function (Blueprint $table) {
            if (! Schema::hasColumn('learning_path_courses', 'stage_name')) {
                $table->string('stage_name')->nullable()->after('sort_order');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('learning_paths', function (Blueprint $table) {
            $table->dropColumn(['target_role', 'salary_range', 'estimated_duration', 'skills', 'is_featured']);
        });

        Schema::table('learning_path_courses', function (Blueprint $table) {
            $table->dropColumn(['stage_name']);
        });
    }
};
