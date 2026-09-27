<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('discussion_replies', function (Blueprint $table) {
            if (! Schema::hasColumn('discussion_replies', 'reply_to_discussion_id')) {
                $table->foreignId('reply_to_discussion_id')
                    ->nullable()
                    ->after('reply_to_message_id')
                    ->constrained('discussions')
                    ->nullOnDelete();
            }
        });

        Schema::table('discussions', function (Blueprint $table) {
            if (! Schema::hasColumn('discussions', 'last_message_at')) {
                $table->timestamp('last_message_at')->nullable()->after('is_recalled')->index();
            }
            if (! Schema::hasColumn('discussions', 'last_message_user_id')) {
                $table->foreignId('last_message_user_id')
                    ->nullable()
                    ->after('last_message_at')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });

        $this->mergeDuplicateConversations();
        $this->backfillLastMessageMetadata();

        Schema::table('discussions', function (Blueprint $table) {
            $table->unique(['course_id', 'user_id'], 'discussions_course_student_unique');
        });

        Schema::create('discussion_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discussion_id')->constrained('discussions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('role', 32);
            $table->timestamp('last_read_at')->nullable()->index();
            $table->unsignedInteger('unread_count')->default(0);
            $table->timestamps();
            $table->unique(['discussion_id', 'user_id']);
            $table->index(['user_id', 'last_read_at']);
        });

        $this->backfillParticipants();
    }

    public function down(): void
    {
        Schema::dropIfExists('discussion_participants');

        Schema::table('discussions', function (Blueprint $table) {
            $table->dropUnique('discussions_course_student_unique');
            if (Schema::hasColumn('discussions', 'last_message_user_id')) {
                $table->dropConstrainedForeignId('last_message_user_id');
            }
            if (Schema::hasColumn('discussions', 'last_message_at')) {
                $table->dropIndex(['last_message_at']);
                $table->dropColumn('last_message_at');
            }
        });

        Schema::table('discussion_replies', function (Blueprint $table) {
            if (Schema::hasColumn('discussion_replies', 'reply_to_discussion_id')) {
                $table->dropConstrainedForeignId('reply_to_discussion_id');
            }
        });
    }

    private function mergeDuplicateConversations(): void
    {
        $groups = DB::table('discussions')
            ->select('course_id', 'user_id', DB::raw('COUNT(*) as aggregate'))
            ->whereNotNull('course_id')
            ->groupBy('course_id', 'user_id')
            ->having('aggregate', '>', 1)
            ->get();

        foreach ($groups as $group) {
            $conversations = DB::table('discussions')
                ->where('course_id', $group->course_id)
                ->where('user_id', $group->user_id)
                ->orderBy('created_at')
                ->orderBy('id')
                ->get();

            $primary = $conversations->first();
            foreach ($conversations->slice(1) as $duplicate) {
                $convertedId = DB::table('discussion_replies')->insertGetId([
                    'discussion_id' => $primary->id,
                    'reply_to_message_id' => null,
                    'reply_to_discussion_id' => null,
                    'lesson_id' => $duplicate->lesson_id,
                    'user_id' => $duplicate->user_id,
                    'content' => $duplicate->content,
                    'attachment_path' => $duplicate->attachment_path,
                    'attachment_name' => $duplicate->attachment_name,
                    'attachment_type' => $duplicate->attachment_type,
                    'is_instructor_answer' => false,
                    'is_helpful' => false,
                    'is_recalled' => $duplicate->is_recalled,
                    'created_at' => $duplicate->created_at,
                    'updated_at' => $duplicate->updated_at,
                ]);

                DB::table('discussion_replies')
                    ->where('discussion_id', $duplicate->id)
                    ->where('id', '!=', $convertedId)
                    ->update(['discussion_id' => $primary->id]);

                DB::table('discussions')->where('id', $duplicate->id)->delete();
            }
        }
    }

    private function backfillLastMessageMetadata(): void
    {
        DB::table('discussions')->orderBy('id')->eachById(function ($discussion): void {
            $reply = DB::table('discussion_replies')
                ->where('discussion_id', $discussion->id)
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->first(['user_id', 'created_at']);

            DB::table('discussions')->where('id', $discussion->id)->update([
                'last_message_at' => $reply?->created_at ?? $discussion->created_at,
                'last_message_user_id' => $reply?->user_id ?? $discussion->user_id,
            ]);
        });
    }

    private function backfillParticipants(): void
    {
        $now = now();
        DB::table('discussions')->orderBy('id')->eachById(function ($discussion) use ($now): void {
            DB::table('discussion_participants')->updateOrInsert(
                ['discussion_id' => $discussion->id, 'user_id' => $discussion->user_id],
                ['role' => 'student', 'last_read_at' => $discussion->last_message_at ?? $discussion->created_at, 'unread_count' => 0, 'created_at' => $now, 'updated_at' => $now]
            );

            $instructorId = $discussion->course_id
                ? DB::table('courses')->where('id', $discussion->course_id)->value('instructor_id')
                : DB::table('lessons')
                    ->join('courses', 'courses.id', '=', 'lessons.course_id')
                    ->where('lessons.id', $discussion->lesson_id)
                    ->value('courses.instructor_id');

            if ($instructorId) {
                DB::table('discussion_participants')->updateOrInsert(
                    ['discussion_id' => $discussion->id, 'user_id' => $instructorId],
                    ['role' => 'instructor', 'last_read_at' => $discussion->last_message_at ?? $discussion->created_at, 'unread_count' => 0, 'created_at' => $now, 'updated_at' => $now]
                );
            }
        });
    }
};
