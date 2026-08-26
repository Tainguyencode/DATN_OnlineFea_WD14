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
            if (! Schema::hasColumn('user_points', 'type')) {
                $table->string('type')->nullable();
            }
            if (! Schema::hasColumn('user_points', 'source')) {
                $table->string('source')->nullable();
            }
            if (! Schema::hasColumn('user_points', 'description')) {
                $table->text('description')->nullable();
            }
            if (! Schema::hasColumn('user_points', 'course_id')) {
                $table->foreignId('course_id')
                    ->nullable()
                    ->constrained('courses')
                    ->nullOnDelete();
            }
        });

        Schema::table('discussion_replies', function (Blueprint $table) {
            if (! Schema::hasColumn('discussion_replies', 'is_helpful')) {
                $table->boolean('is_helpful')
                    ->default(false);
            }
        });

        Schema::table('user_badges', function (Blueprint $table) {
            if (! Schema::hasColumn('user_badges', 'created_at')) {
                $table->timestamps();
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
                $table->dropForeign(['course_id']);
                $table->dropColumn('course_id');
            }
            if (Schema::hasColumn('user_points', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('user_points', 'source')) {
                $table->dropColumn('source');
            }
            if (Schema::hasColumn('user_points', 'type')) {
                $table->dropColumn('type');
            }
        });

        Schema::table('discussion_replies', function (Blueprint $table) {
            if (Schema::hasColumn('discussion_replies', 'is_helpful')) {
                $table->dropColumn('is_helpful');
            }
        });
    }
};
