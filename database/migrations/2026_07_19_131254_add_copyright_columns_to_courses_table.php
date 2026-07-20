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
            if (! Schema::hasColumn('courses', 'copyright_agreed')) {
                $table->boolean('copyright_agreed')->default(false)->after('submitted_at');
            }
            if (! Schema::hasColumn('courses', 'copyright_agreed_at')) {
                $afterColumn = Schema::hasColumn('courses', 'copyright_agreed') ? 'copyright_agreed' : 'submitted_at';
                $table->timestamp('copyright_agreed_at')->nullable()->after($afterColumn);
            }
            if (! Schema::hasColumn('courses', 'copyright_agreed_by')) {
                $afterColumn = Schema::hasColumn('courses', 'copyright_agreed_at') ? 'copyright_agreed_at' : 'submitted_at';
                $table->foreignId('copyright_agreed_by')
                    ->nullable()
                    ->after($afterColumn)
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $columns = array_values(array_filter([
                Schema::hasColumn('courses', 'copyright_agreed_by') ? 'copyright_agreed_by' : null,
                Schema::hasColumn('courses', 'copyright_agreed_at') ? 'copyright_agreed_at' : null,
                Schema::hasColumn('courses', 'copyright_agreed') ? 'copyright_agreed' : null,
            ]));

            if ($columns !== []) {
                if (in_array('copyright_agreed_by', $columns, true)) {
                    $table->dropForeign(['copyright_agreed_by']);
                }
                $table->dropColumn($columns);
            }
        });
    }
};
