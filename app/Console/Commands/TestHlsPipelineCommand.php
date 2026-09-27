<?php

namespace App\Console\Commands;

use App\Jobs\ConvertVideoToHLS;
use App\Models\Lesson;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class TestHlsPipelineCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:test-hls {--count=3 : Số lượng video cần chuyển đổi thử sang HLS}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Chạy thử quy trình chuyển đổi MP4 sang HLS (playlist.m3u8 + segments .ts) bằng FFmpeg theo chuẩn dự án';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $count = (int) $this->option('count');
        $this->info("=== BẮT ĐẦU CHẠY THỬ HLS PIPELINE CHO {$count} VIDEO BÀI HỌC ===");

        $lessons = Lesson::where('type', Lesson::TYPE_VIDEO)
            ->whereNotNull('video_path')
            ->where('video_path', 'like', 'videos/sources/%')
            ->take($count)
            ->get();

        if ($lessons->isEmpty()) {
            $this->warn('Không tìm thấy video bài học nào có nguồn MP4 hợp lệ để chuyển đổi.');
            return self::FAILURE;
        }

        $successCount = 0;

        foreach ($lessons as $idx => $lesson) {
            $this->line("\n[" . ($idx + 1) . "/{$count}] Xử lý Lesson ID: {$lesson->id} - {$lesson->title}");
            $this->line("   - Source Path: {$lesson->video_path}");

            try {
                // Chạy trực tiếp qua Job ConvertVideoToHLS chuẩn của hệ thống
                $job = new ConvertVideoToHLS($lesson);
                $job->handle();

                $lesson->refresh();

                // Kiểm tra kết quả output
                $hlsLocalDir = Storage::disk('local')->path('lesson-hls/' . $lesson->id);
                $playlistPath = $hlsLocalDir . DIRECTORY_SEPARATOR . 'playlist.m3u8';

                if (file_exists($playlistPath) && filesize($playlistPath) > 0) {
                    $segmentFiles = File::glob($hlsLocalDir . DIRECTORY_SEPARATOR . 'segment_*.ts');
                    $segmentCount = count($segmentFiles);

                    $this->info("   ✓ Chuyển đổi HLS thành công!");
                    $this->info("   - Output Path: {$lesson->video_path}");
                    $this->info("   - Status: {$lesson->processing_status}");
                    $this->info("   - Duration: {$lesson->duration_seconds}s");
                    $this->info("   - Playlist size: " . filesize($playlistPath) . " bytes");
                    $this->info("   - Tổng số segments HLS (.ts): {$segmentCount}");

                    $successCount++;
                } else {
                    $this->error("   ❌ Không tìm thấy playlist.m3u8 sau khi convert!");
                }
            } catch (\Throwable $e) {
                $this->error("   ❌ Lỗi xử lý HLS: " . $e->getMessage());
            }
        }

        $this->info("\n=== KẾT QUẢ: {$successCount}/{$count} VIDEO ĐÃ CHUYỂN ĐỔI HLS THÀNH CÔNG 100% ===");
        return self::SUCCESS;
    }
}
