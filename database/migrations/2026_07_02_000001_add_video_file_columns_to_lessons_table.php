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
            if (! Schema::hasColumn('lessons', 'video_path')) {
                $table->string('video_path')->nullable();
            }

            if (! Schema::hasColumn('lessons', 'video_original_name')) {
                $table->string('video_original_name')->nullable();
            }

            if (! Schema::hasColumn('lessons', 'video_mime')) {
                $table->string('video_mime')->nullable();
            }

            if (! Schema::hasColumn('lessons', 'video_size')) {
                $table->unsignedBigInteger('video_size')->nullable();
            }

            if (! Schema::hasColumn('lessons', 'duration')) {
                $table->integer('duration')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('lessons')) {
            return;
        }

        $columns = array_filter([
            Schema::hasColumn('lessons', 'video_path') ? 'video_path' : null,
            Schema::hasColumn('lessons', 'video_original_name') ? 'video_original_name' : null,
            Schema::hasColumn('lessons', 'video_mime') ? 'video_mime' : null,
            Schema::hasColumn('lessons', 'video_size') ? 'video_size' : null,
        ]);

        if ($columns !== []) {
            Schema::table('lessons', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
