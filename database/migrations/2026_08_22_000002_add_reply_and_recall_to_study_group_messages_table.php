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
        Schema::table('study_group_messages', function (Blueprint $table) {
            if (! Schema::hasColumn('study_group_messages', 'reply_to_message_id')) {
                $table->foreignId('reply_to_message_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('study_group_messages')
                    ->nullOnDelete();
            }
            if (! Schema::hasColumn('study_group_messages', 'is_recalled')) {
                $table->boolean('is_recalled')->default(false)->after('message');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('study_group_messages', function (Blueprint $table) {
            $table->dropForeign(['reply_to_message_id']);
            $table->dropColumn(['reply_to_message_id', 'is_recalled']);
        });
    }
};
