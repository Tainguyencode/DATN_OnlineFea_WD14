<?php

namespace App\Console\Commands;

use App\Jobs\ConvertVideoToHLS;
use App\Models\Lesson;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RecoverPendingHlsVideosCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'video:recover-pending-hls';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tự động quét và kích hoạt chuyển đổi HLS cho các bài học video có file sẵn trên S3 hoặc Local nhưng bị sót Job';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Đang quét các bài học cần đồng bộ HLS...');

        // 1. Cập nhật bài học không phải dạng video sang completed nếu đang bị kẹt pending
        $nonVideoUpdated = Lesson::where('type', '!=', Lesson::TYPE_VIDEO)
            ->where('processing_status', 'pending')
            ->update([
                'processing_status' => 'completed',
                'upload_status' => 'uploaded',
            ]);

        if ($nonVideoUpdated > 0) {
            $this->info("Đã cập nhật {$nonVideoUpdated} bài học không phải video (quiz/assignment) sang completed.");
        }

        // 2. Tìm các bài học video chưa completed
        $pendingLessons = Lesson::where('type', Lesson::TYPE_VIDEO)
            ->where('processing_status', '!=', 'completed')
            ->get();

        $dispatchedCount = 0;

        foreach ($pendingLessons as $lesson) {
            $hasS3 = filled($lesson->original_video_key) && Storage::disk('s3')->exists($lesson->original_video_key);
            $hasLocal = filled($lesson->video_path) && (
                Storage::disk('local')->exists($lesson->video_path) ||
                Storage::disk('public')->exists($lesson->video_path)
            );

            if ($hasS3 || $hasLocal) {
                // Kiểm tra xem bài học đã có file HLS master/playlist chưa
                $hasHlsLocal = Storage::disk('local')->exists('lesson-hls/' . $lesson->id . '/playlist.m3u8');
                $hasHlsS3 = filled($lesson->hls_manifest_key) && Storage::disk('s3')->exists($lesson->hls_manifest_key);

                if ($hasHlsLocal || $hasHlsS3) {
                    $lesson->update([
                        'processing_status' => 'completed',
                        'upload_status' => 'uploaded',
                        'status' => 'published',
                    ]);
                    $this->line("Bài học #{$lesson->id} ({$lesson->title}) đã có HLS sẵn -> Đánh dấu completed.");
                    continue;
                }

                // Dispatch Job chuyển đổi HLS
                ConvertVideoToHLS::dispatch($lesson);
                $dispatchedCount++;

                $this->info("-> Đã đưa Bài học #{$lesson->id} ('{$lesson->title}') vào hàng đợi HLS.");
                Log::info("[RecoverPendingHlsVideos] Dispatched ConvertVideoToHLS for Lesson #{$lesson->id}");
            }
        }

        $this->info("Hoàn tất! Đã kích hoạt {$dispatchedCount} job chuyển đổi HLS.");

        return self::SUCCESS;
    }
}
