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
        Schema::table('ai_chat_messages', function (Blueprint $table) {
            if (! Schema::hasColumn('ai_chat_messages', 'learning_path_id')) {
                $table->foreignId('learning_path_id')
                    ->nullable()
                    ->after('lesson_id')
                    ->constrained('learning_paths')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_chat_messages', function (Blueprint $table) {
            if (Schema::hasColumn('ai_chat_messages', 'learning_path_id')) {
                $table->dropForeign(['learning_path_id']);
                $table->dropColumn('learning_path_id');
            }
        });
    }
};
