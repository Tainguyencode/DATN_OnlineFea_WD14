<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Models\LearningPath;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class CleanDemoDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:clean {--force : Bỏ qua bước xác nhận}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dọn dẹp an toàn TOÀN BỘ dữ liệu demo (@onlinefea.test và slug demo-*) mà không ảnh hưởng dữ liệu gốc';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! $this->option('force') && ! $this->confirm('Bạn có chắc chắn muốn dọn sạch tất cả dữ liệu Demo (@onlinefea.test, demo-* courses/paths/videos)?')) {
            $this->info('Đã hủy thao tác.');
            return self::SUCCESS;
        }

        $this->info('=== BẮT ĐẦU DỌN DẸP DỮ LIỆU DEMO AN TOÀN ===');

        Schema::disableForeignKeyConstraints();

        // 1. Xóa Demo Learning Paths
        $demoPaths = LearningPath::where('slug', 'like', 'demo-%')->get();
        foreach ($demoPaths as $dp) {
            $dp->courses()->detach();
            $dp->delete();
        }
        $this->info('✓ Đã xóa ' . $demoPaths->count() . ' Lộ trình học Demo');

        // 2. Xóa Demo Courses (kéo theo Chapters, Lessons, Quizzes nhờ ON DELETE CASCADE hoặc manual)
        $demoCourses = Course::where('slug', 'like', 'demo-%')->get();
        $courseIds = $demoCourses->pluck('id')->all();

        if (! empty($courseIds)) {
            // Dọn quiz related
            $quizIds = DB::table('quizzes')->whereIn('course_id', $courseIds)->pluck('id')->all();
            if (! empty($quizIds)) {
                $versionIds = DB::table('quiz_versions')->whereIn('quiz_id', $quizIds)->pluck('id')->all();
                DB::table('quiz_version_questions')->whereIn('quiz_version_id', $versionIds)->delete();
                DB::table('quiz_versions')->whereIn('id', $versionIds)->delete();
                $questionIds = DB::table('quiz_questions')->whereIn('quiz_id', $quizIds)->pluck('id')->all();
                DB::table('quiz_options')->whereIn('quiz_question_id', $questionIds)->delete();
                DB::table('question_versions')->whereIn('question_id', $questionIds)->delete();
                DB::table('quiz_questions')->whereIn('id', $questionIds)->delete();
                DB::table('quiz_attempts')->whereIn('quiz_id', $quizIds)->delete();
                DB::table('quizzes')->whereIn('id', $quizIds)->delete();
            }

            // Dọn lessons & chapters
            $lessonIds = DB::table('lessons')->whereIn('course_id', $courseIds)->pluck('id')->all();
            DB::table('lesson_progress')->whereIn('lesson_id', $lessonIds)->delete();
            DB::table('lessons')->whereIn('course_id', $courseIds)->delete();
            DB::table('chapters')->whereIn('course_id', $courseIds)->delete();
            DB::table('course_sections')->whereIn('course_id', $courseIds)->delete();
            DB::table('certificates')->whereIn('course_id', $courseIds)->delete();
            DB::table('reviews')->whereIn('course_id', $courseIds)->delete();
            DB::table('enrollments')->whereIn('course_id', $courseIds)->delete();
            DB::table('order_items')->whereIn('course_id', $courseIds)->delete();
            DB::table('courses')->whereIn('id', $courseIds)->delete();
        }
        $this->info('✓ Đã xóa ' . count($courseIds) . ' Khóa học Demo cùng toàn bộ Chương/Bài học/Quizzes liên quan');

        // 3. Xóa Demo Users
        $demoUsers = User::where('email', 'like', '%@onlinefea.test')->get();
        $userIds = $demoUsers->pluck('id')->all();

        if (! empty($userIds)) {
            DB::table('instructor_profiles')->whereIn('user_id', $userIds)->delete();
            DB::table('instructor_applications')->whereIn('user_id', $userIds)->delete();
            DB::table('withdrawals')->whereIn('user_id', $userIds)->delete();
            DB::table('role_user')->whereIn('user_id', $userIds)->delete();
            DB::table('orders')->whereIn('user_id', $userIds)->delete();
            DB::table('reviews')->whereIn('user_id', $userIds)->delete();
            DB::table('enrollments')->whereIn('user_id', $userIds)->delete();
            DB::table('lesson_progress')->whereIn('user_id', $userIds)->delete();
            DB::table('certificates')->whereIn('user_id', $userIds)->delete();
            DB::table('users')->whereIn('id', $userIds)->forceDelete();
        }
        $this->info('✓ Đã xóa ' . count($userIds) . ' Người dùng Demo (@onlinefea.test)');

        Schema::enableForeignKeyConstraints();

        // 4. Dọn dẹp file MP4 demo và HLS folders nếu cần
        $publicSourceDir = Storage::disk('public')->path('videos/sources');
        $privateSourceDir = Storage::disk('local')->path('videos/sources');
        if (File::exists($publicSourceDir)) {
            File::deleteDirectory($publicSourceDir);
        }
        if (File::exists($privateSourceDir)) {
            File::deleteDirectory($privateSourceDir);
        }
        $this->info('✓ Đã dọn dẹp các file MP4 nguồn demo trong storage');

        $this->info('=== HOÀN TẤT DỌN DẸP DỮ LIỆU DEMO AN TOÀN 100% ===');
        return self::SUCCESS;
    }
}
