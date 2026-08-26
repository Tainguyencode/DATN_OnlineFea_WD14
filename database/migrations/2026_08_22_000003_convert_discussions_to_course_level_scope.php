<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Thêm cột course_id vào bảng discussions nếu chưa có
        if (Schema::hasTable('discussions')) {
            Schema::table('discussions', function (Blueprint $table) {
                if (! Schema::hasColumn('discussions', 'course_id')) {
                    $table->foreignId('course_id')
                        ->nullable()
                        ->after('id')
                        ->constrained('courses')
                        ->cascadeOnDelete();
                }
            });

            // Cho phép lesson_id nullable trên discussions
            Schema::table('discussions', function (Blueprint $table) {
                $table->foreignId('lesson_id')->nullable()->change();
            });
        }

        // 2. Thêm cột lesson_id vào bảng discussion_replies nếu chưa có
        if (Schema::hasTable('discussion_replies')) {
            Schema::table('discussion_replies', function (Blueprint $table) {
                if (! Schema::hasColumn('discussion_replies', 'lesson_id')) {
                    $table->foreignId('lesson_id')
                        ->nullable()
                        ->after('reply_to_message_id')
                        ->constrained('lessons')
                        ->nullOnDelete();
                }
            });
        }

        // 3. Backfill dữ liệu course_id và lesson_id từ dữ liệu cũ
        if (Schema::hasTable('discussions') && Schema::hasTable('lessons')) {
            $discussions = DB::table('discussions')->get();
            foreach ($discussions as $disc) {
                if (! $disc->course_id && $disc->lesson_id) {
                    $courseId = DB::table('lessons')->where('id', $disc->lesson_id)->value('course_id');
                    if ($courseId) {
                        DB::table('discussions')->where('id', $disc->id)->update(['course_id' => $courseId]);
                    }
                }
            }
        }

        if (Schema::hasTable('discussion_replies') && Schema::hasTable('discussions')) {
            $replies = DB::table('discussion_replies')->get();
            foreach ($replies as $reply) {
                if (! $reply->lesson_id && $reply->discussion_id) {
                    $lessonId = DB::table('discussions')->where('id', $reply->discussion_id)->value('lesson_id');
                    if ($lessonId) {
                        DB::table('discussion_replies')->where('id', $reply->id)->update(['lesson_id' => $lessonId]);
                    }
                }
            }
        }

        // 4. Merge các discussions trùng lặp theo (user_id, course_id)
        if (Schema::hasTable('discussions') && Schema::hasTable('discussion_replies')) {
            $groups = DB::table('discussions')
                ->select('user_id', 'course_id', DB::raw('COUNT(*) as total'))
                ->whereNotNull('course_id')
                ->groupBy('user_id', 'course_id')
                ->having('total', '>', 1)
                ->get();

            foreach ($groups as $group) {
                $discs = DB::table('discussions')
                    ->where('user_id', $group->user_id)
                    ->where('course_id', $group->course_id)
                    ->orderBy('created_at', 'asc')
                    ->orderBy('id', 'asc')
                    ->get();

                if ($discs->count() <= 1) {
                    continue;
                }

                $mainDisc = $discs->first();
                $otherDiscs = $discs->slice(1);

                foreach ($otherDiscs as $other) {
                    // Chuyển nội dung câu hỏi phụ thành một reply trong conversation chính
                    $newReplyId = DB::table('discussion_replies')->insertGetId([
                        'discussion_id' => $mainDisc->id,
                        'reply_to_message_id' => null,
                        'lesson_id' => $other->lesson_id,
                        'user_id' => $other->user_id,
                        'content' => $other->content,
                        'attachment_path' => $other->attachment_path,
                        'attachment_name' => $other->attachment_name,
                        'attachment_type' => $other->attachment_type,
                        'is_instructor_answer' => 0,
                        'is_helpful' => 0,
                        'is_recalled' => $other->is_recalled,
                        'created_at' => $other->created_at,
                        'updated_at' => $other->updated_at,
                    ]);

                    // Cập nhật các replies thuộc discussion phụ trỏ sang conversation chính
                    DB::table('discussion_replies')
                        ->where('discussion_id', $other->id)
                        ->where('id', '!=', $newReplyId)
                        ->update(['discussion_id' => $mainDisc->id]);

                    // Xóa bản ghi discussion phụ
                    DB::table('discussions')->where('id', $other->id)->delete();
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('discussion_replies') && Schema::hasColumn('discussion_replies', 'lesson_id')) {
            Schema::table('discussion_replies', function (Blueprint $table) {
                $table->dropForeign(['lesson_id']);
                $table->dropColumn('lesson_id');
            });
        }

        if (Schema::hasTable('discussions')) {
            if (Schema::hasColumn('discussions', 'course_id')) {
                Schema::table('discussions', function (Blueprint $table) {
                    $table->dropForeign(['course_id']);
                    $table->dropColumn('course_id');
                });
            }
        }
    }
};
