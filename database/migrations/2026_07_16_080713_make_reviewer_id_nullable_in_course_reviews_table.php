<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('course_reviews') || ! Schema::hasColumn('course_reviews', 'reviewer_id')) {
            return;
        }

        $this->dropReviewerForeignKeys();

        if (! $this->reviewerIdIsNullable()) {
            Schema::table('course_reviews', function (Blueprint $table) {
                $table->unsignedBigInteger('reviewer_id')->nullable()->change();
            });
        }

        $this->addReviewerForeignKey(true);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('course_reviews') || ! Schema::hasColumn('course_reviews', 'reviewer_id')) {
            return;
        }

        if (DB::table('course_reviews')->whereNull('reviewer_id')->exists()) {
            throw new RuntimeException('Cannot make course_reviews.reviewer_id NOT NULL while rows with NULL reviewer_id exist.');
        }

        $this->dropReviewerForeignKeys();

        if ($this->reviewerIdIsNullable()) {
            Schema::table('course_reviews', function (Blueprint $table) {
                $table->unsignedBigInteger('reviewer_id')->nullable(false)->change();
            });
        }

        $this->addReviewerForeignKey(false);
    }

    private function dropReviewerForeignKeys(): void
    {
        if ($this->driverName() !== 'mysql') {
            return;
        }

        foreach ($this->reviewerForeignKeyNames() as $foreignKeyName) {
            Schema::table('course_reviews', function (Blueprint $table) use ($foreignKeyName) {
                $table->dropForeign($foreignKeyName);
            });
        }
    }

    private function addReviewerForeignKey(bool $nullOnDelete): void
    {
        if ($this->driverName() !== 'mysql' || ! Schema::hasTable('users')) {
            return;
        }

        if ($this->reviewerForeignKeyNames() !== []) {
            return;
        }

        Schema::table('course_reviews', function (Blueprint $table) use ($nullOnDelete) {
            $foreign = $table
                ->foreign('reviewer_id', 'course_reviews_reviewer_id_foreign')
                ->references('id')
                ->on('users');

            if ($nullOnDelete) {
                $foreign->nullOnDelete();
            }
        });
    }

    /**
     * @return list<string>
     */
    private function reviewerForeignKeyNames(): array
    {
        if ($this->driverName() !== 'mysql') {
            return [];
        }

        return array_map(
            static fn (object $row): string => (string) $row->CONSTRAINT_NAME,
            DB::select(
                <<<'SQL'
                SELECT CONSTRAINT_NAME
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                    AND TABLE_NAME = ?
                    AND COLUMN_NAME = ?
                    AND REFERENCED_TABLE_NAME IS NOT NULL
                SQL,
                ['course_reviews', 'reviewer_id'],
            ),
        );
    }

    private function reviewerIdIsNullable(): bool
    {
        return match ($this->driverName()) {
            'mysql' => $this->mysqlReviewerIdIsNullable(),
            'sqlite' => $this->sqliteReviewerIdIsNullable(),
            default => false,
        };
    }

    private function mysqlReviewerIdIsNullable(): bool
    {
        $column = DB::selectOne(
            <<<'SQL'
            SELECT IS_NULLABLE
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = ?
                AND COLUMN_NAME = ?
            SQL,
            ['course_reviews', 'reviewer_id'],
        );

        return strtoupper((string) ($column->IS_NULLABLE ?? 'NO')) === 'YES';
    }

    private function sqliteReviewerIdIsNullable(): bool
    {
        foreach (DB::select('PRAGMA table_info(course_reviews)') as $column) {
            if (($column->name ?? null) === 'reviewer_id') {
                return (int) ($column->notnull ?? 1) === 0;
            }
        }

        return false;
    }

    private function driverName(): string
    {
        return DB::connection()->getDriverName();
    }
};
