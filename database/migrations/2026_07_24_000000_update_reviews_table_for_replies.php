<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        // 1. Drop unique constraint and rating check constraint
        Schema::table('reviews', function (Blueprint $table) use ($driver) {
            if ($driver !== 'sqlite') {
                // Drop foreign keys first to allow dropping unique index
                $table->dropForeign('reviews_user_id_foreign');
                $table->dropForeign('reviews_course_id_foreign');
                
                $table->dropUnique('reviews_user_id_course_id_unique');
                
                // Recreate foreign keys
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
                $table->foreign('course_id')->references('id')->on('courses')->cascadeOnDelete();
            } else {
                // SQLite handles unique index drops differently
                try {
                    $table->dropUnique(['user_id', 'course_id']);
                } catch (\Exception $e) {}
            }
        });

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE reviews DROP CHECK reviews_rating_between_1_and_5');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE reviews DROP CONSTRAINT reviews_rating_between_1_and_5');
        }

        // 2. Modify columns (rating nullable, add parent_id and is_hidden)
        Schema::table('reviews', function (Blueprint $table) {
            $table->unsignedTinyInteger('rating')->nullable()->change();
            $table->foreignId('parent_id')->nullable()->constrained('reviews')->cascadeOnDelete();
            $table->boolean('is_hidden')->default(false);
        });

        // 3. Re-create constraint to allow null rating
        if ($driver === 'mysql' || $driver === 'pgsql') {
            DB::statement('ALTER TABLE reviews ADD CONSTRAINT reviews_rating_between_1_and_5 CHECK (rating IS NULL OR (rating BETWEEN 1 AND 5))');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql' || $driver === 'pgsql') {
            try {
                if ($driver === 'mysql') {
                    DB::statement('ALTER TABLE reviews DROP CHECK reviews_rating_between_1_and_5');
                } else {
                    DB::statement('ALTER TABLE reviews DROP CONSTRAINT reviews_rating_between_1_and_5');
                }
            } catch (\Exception $e) {}
        }

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropConstrainedForeignId('parent_id');
            $table->dropColumn('is_hidden');
            $table->unsignedTinyInteger('rating')->nullable(false)->change();
        });

        if ($driver === 'mysql' || $driver === 'pgsql') {
            DB::statement('ALTER TABLE reviews ADD CONSTRAINT reviews_rating_between_1_and_5 CHECK (rating BETWEEN 1 AND 5)');
        }

        Schema::table('reviews', function (Blueprint $table) use ($driver) {
            if ($driver !== 'sqlite') {
                $table->unique(['user_id', 'course_id'], 'reviews_user_id_course_id_unique');
            } else {
                try {
                    $table->unique(['user_id', 'course_id']);
                } catch (\Exception $e) {}
            }
        });
    }
};
