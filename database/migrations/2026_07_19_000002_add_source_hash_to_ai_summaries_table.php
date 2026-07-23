<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ai_summaries')) {
            return;
        }

        Schema::table('ai_summaries', function (Blueprint $table) {
            if (! Schema::hasColumn('ai_summaries', 'source_hash')) {
                $table->string('source_hash', 64)->nullable()->after('language');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('ai_summaries') || ! Schema::hasColumn('ai_summaries', 'source_hash')) {
            return;
        }

        Schema::table('ai_summaries', function (Blueprint $table) {
            $table->dropColumn('source_hash');
        });
    }
};
