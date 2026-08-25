<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_conversations', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['course_id']);
            $table->dropForeign(['lesson_id']);
            $table->dropUnique('ai_conversations_user_course_lesson_unique');
        });

        Schema::table('ai_conversations', function (Blueprint $table) {
            $table->unsignedBigInteger('course_id')->nullable()->change();
            $table->unsignedBigInteger('lesson_id')->nullable()->change();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('course_id')->references('id')->on('courses')->cascadeOnDelete();
            $table->foreign('lesson_id')->references('id')->on('lessons')->cascadeOnDelete();

            $table->string('scope', 50)->default('course')->after('user_id');
            $table->string('session_id', 100)->nullable()->after('user_id');
            $table->string('title')->nullable()->after('scope');
            $table->string('current_topic')->nullable()->after('title');
            $table->foreignId('learning_path_id')->nullable()->after('lesson_id')->constrained('learning_paths')->nullOnDelete();
            $table->json('context_data')->nullable()->after('learning_path_id');
            $table->timestamp('last_activity_at')->nullable()->after('context_data');
        });

        Schema::table('ai_chat_messages', function (Blueprint $table) {
            if (!Schema::hasColumn('ai_chat_messages', 'metadata')) {
                $table->json('metadata')->nullable()->after('content');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ai_chat_messages', function (Blueprint $table) {
            if (Schema::hasColumn('ai_chat_messages', 'metadata')) {
                $table->dropColumn('metadata');
            }
        });

        Schema::table('ai_conversations', function (Blueprint $table) {
            $table->dropForeign(['learning_path_id']);
            $table->dropColumn([
                'scope',
                'session_id',
                'title',
                'current_topic',
                'learning_path_id',
                'context_data',
                'last_activity_at',
            ]);
        });
    }
};
