<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('courses')) {
            return;
        }

        Schema::table('courses', function (Blueprint $table) {
            if (! Schema::hasColumn('courses', 'copyright_agreed')) {
                $table->boolean('copyright_agreed')->default(false)->after('submitted_at');
            }
        });

        Schema::table('courses', function (Blueprint $table) {
            if (! Schema::hasColumn('courses', 'copyright_agreed_at')) {
                $table->timestamp('copyright_agreed_at')->nullable()->after('copyright_agreed');
            }
        });

        if (! Schema::hasColumn('courses', 'copyright_agreed_by')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->foreignId('copyright_agreed_by')
                    ->nullable()
                    ->after('copyright_agreed_at')
                    ->constrained('users')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('courses')) {
            return;
        }

        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'copyright_agreed_by')) {
                $table->dropConstrainedForeignId('copyright_agreed_by');
            }

            $columns = array_values(array_filter([
                Schema::hasColumn('courses', 'copyright_agreed_at') ? 'copyright_agreed_at' : null,
                Schema::hasColumn('courses', 'copyright_agreed') ? 'copyright_agreed' : null,
            ]));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
