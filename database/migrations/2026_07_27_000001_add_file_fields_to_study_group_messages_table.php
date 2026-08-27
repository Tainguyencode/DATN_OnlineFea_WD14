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
            if (! Schema::hasColumn('study_group_messages', 'message_type')) {
                $table->string('message_type')->default('text')->after('user_id');
            }
            if (! Schema::hasColumn('study_group_messages', 'file_name')) {
                $table->string('file_name')->nullable()->after('image_path');
            }
            if (! Schema::hasColumn('study_group_messages', 'file_path')) {
                $table->string('file_path')->nullable()->after('file_name');
            }
            if (! Schema::hasColumn('study_group_messages', 'mime_type')) {
                $table->string('mime_type')->nullable()->after('file_path');
            }
            if (! Schema::hasColumn('study_group_messages', 'file_size')) {
                $table->unsignedBigInteger('file_size')->nullable()->after('mime_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('study_group_messages', function (Blueprint $table) {
            $table->dropColumn(['message_type', 'file_name', 'file_path', 'mime_type', 'file_size']);
        });
    }
};
