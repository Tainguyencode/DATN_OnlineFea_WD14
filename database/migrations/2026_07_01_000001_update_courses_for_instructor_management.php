<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('courses')) {
            return;
        }

        Schema::table('courses', function (Blueprint $table) {
            if (! Schema::hasColumn('courses', 'short_description')) {
                $table->text('short_description')->nullable()->after('slug');
            }

            if (! Schema::hasColumn('courses', 'discount_price')) {
                $table->decimal('discount_price', 12, 2)->nullable()->after('price');
            }

            if (! Schema::hasColumn('courses', 'language')) {
                $table->string('language', 10)->default('vi')->after('level');
            }

            if (! Schema::hasColumn('courses', 'is_published')) {
                $table->boolean('is_published')->default(false)->after('status');
            }
        });

        if (DB::connection()->getDriverName() === 'mysql') {
            $this->dropForeignKeyIfExists('courses', 'courses_category_id_foreign');

            DB::statement('ALTER TABLE courses MODIFY category_id BIGINT UNSIGNED NULL');
            DB::statement('ALTER TABLE courses MODIFY description TEXT NULL');
            DB::statement("ALTER TABLE courses MODIFY level ENUM('beginner','intermediate','advanced') NULL DEFAULT NULL");
            DB::statement("ALTER TABLE courses MODIFY status ENUM('draft','pending','published','rejected','archived') NOT NULL DEFAULT 'draft'");

            $this->addForeignKeyIfMissing();
        }

        DB::statement('UPDATE courses SET discount_price = sale_price WHERE discount_price IS NULL AND sale_price IS NOT NULL');
        DB::statement("UPDATE courses SET language = 'vi' WHERE language IS NULL OR language = ''");
        DB::statement("UPDATE courses SET is_published = CASE WHEN status = 'published' THEN 1 ELSE 0 END");
    }

    public function down(): void
    {
        // Intentionally non-destructive: these columns are now part of the base
        // courses schema, and removing them would discard instructor course data.
    }

    private function dropForeignKeyIfExists(string $table, string $foreignKey): void
    {
        try {
            Schema::table($table, function (Blueprint $table) use ($foreignKey) {
                $table->dropForeign($foreignKey);
            });
        } catch (Throwable) {
            // Some local databases may already have this key removed or renamed.
        }
    }

    private function addForeignKeyIfMissing(): void
    {
        try {
            Schema::table('courses', function (Blueprint $table) {
                $table->foreign('category_id')->references('id')->on('categories')->nullOnDelete();
            });
        } catch (Throwable) {
            // Keep migration idempotent for databases that already recreated the key.
        }
    }
};
