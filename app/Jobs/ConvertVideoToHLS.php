<?php

namespace App\Jobs;

use App\Models\Lesson;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ConvertVideoToHLS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 3600; // 1 hour max

    public function __construct(
        public Lesson $lesson
    ) {}

    public function handle(): void
    {
        if (!$this->lesson->video_path) {
            return;
        }

        try {
            $mp4PathLocal = Storage::disk('local')->path($this->lesson->video_path);
            $mp4PathPublic = Storage::disk('public')->path($this->lesson->video_path);
            
            $mp4Path = file_exists($mp4PathLocal) ? $mp4PathLocal : (file_exists($mp4PathPublic) ? $mp4PathPublic : null);
            
            // Nếu MP4 không tồn tại thì abort
            if (!$mp4Path) {
                throw new \Exception("MP4 file not found at local or public disk for: " . $this->lesson->video_path);
            }

            // Tạo thư mục HLS output
            $hlsDir = 'lesson-hls/' . $this->lesson->id;
            Storage::disk('local')->makeDirectory($hlsDir);
            
            $hlsPath = Storage::disk('local')->path($hlsDir . '/playlist.m3u8');

            // Cấu hình FFMpeg (đảm bảo ffmpeg đã được cài đặt trên hệ thống)
            $ffmpeg = FFMpeg::create([
                'ffmpeg.binaries'  => env('FFMPEG_BINARIES', 'ffmpeg'),
                'ffprobe.binaries' => env('FFPROBE_BINARIES', 'ffprobe'),
                'timeout'          => 3600, 
                'ffmpeg.threads'   => 12,   
            ]);

            $video = $ffmpeg->open($mp4Path);
            
            $format = new X264('libmp3lame', 'libx264');
            $format->setAdditionalParameters([
                '-hls_time', '10',
                '-hls_list_size', '0',
                '-f', 'hls'
            ]);

            $video->save($format, $hlsPath);

            // Xóa file MP4 gốc để tiết kiệm dung lượng
            if (Storage::disk('local')->exists($this->lesson->video_path)) {
                Storage::disk('local')->delete($this->lesson->video_path);
            }
            if (Storage::disk('public')->exists($this->lesson->video_path)) {
                Storage::disk('public')->delete($this->lesson->video_path);
            }

            // Cập nhật Database: lưu đường dẫn HLS
            $this->lesson->update([
                'video_path' => $hlsDir . '/playlist.m3u8',
                'status' => 'published' // Enum cho phép 'published'
            ]);

        } catch (Throwable $e) {
            Log::error('Video conversion failed for Lesson ID ' . $this->lesson->id . ': ' . $e->getMessage());
            
            throw $e;
        } finally {
            \Illuminate\Support\Facades\Cache::forget('video_processing_' . $this->lesson->id);
        }
    }
}
