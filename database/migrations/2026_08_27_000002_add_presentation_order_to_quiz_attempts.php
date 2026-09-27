<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table): void {
            if (! Schema::hasColumn('quiz_attempts', 'presentation_order')) {
                $table->json('presentation_order')->nullable()->after('answers');
            }
        });
    }

    public function down(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table): void {
            if (Schema::hasColumn('quiz_attempts', 'presentation_order')) {
                $table->dropColumn('presentation_order');
            }
        });
    }
};
