<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('lessons')) {
            return;
        }

        Schema::table('lessons', function (Blueprint $table) {
            if (! Schema::hasColumn('lessons', 'original_video_key')) {
                $table->string('original_video_key')->nullable()->index()->after('video_path');
            }

            if (! Schema::hasColumn('lessons', 'hls_manifest_key')) {
                $table->string('hls_manifest_key')->nullable()->index()->after('original_video_key');
            }

            if (! Schema::hasColumn('lessons', 'upload_status')) {
                $table->string('upload_status', 32)->default('pending')->after('hls_manifest_key');
            }

            if (! Schema::hasColumn('lessons', 'processing_status')) {
                $table->string('processing_status', 32)->default('pending')->after('upload_status');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('lessons')) {
            return;
        }

        $columns = array_filter([
            Schema::hasColumn('lessons', 'original_video_key') ? 'original_video_key' : null,
            Schema::hasColumn('lessons', 'hls_manifest_key') ? 'hls_manifest_key' : null,
            Schema::hasColumn('lessons', 'upload_status') ? 'upload_status' : null,
            Schema::hasColumn('lessons', 'processing_status') ? 'processing_status' : null,
        ]);

        if ($columns !== []) {
            Schema::table('lessons', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
