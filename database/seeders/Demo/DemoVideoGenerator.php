<?php

namespace Database\Seeders\Demo;

use App\Models\Lesson;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class DemoVideoGenerator
{
    public const RELATIVE_SOURCE_DIR = 'videos/sources';

    public function generateDemoVideos(int $count = 65, ?callable $output = null): array
    {
        $log = $output ?: fn(string $msg) => null;
        $log("--- Bắt đầu tạo {$count} video MP4 nguồn chuẩn H.264/AAC qua FFmpeg ---");

        $publicDir = Storage::disk('public')->path(self::RELATIVE_SOURCE_DIR);
        $privateDir = Storage::disk('local')->path(self::RELATIVE_SOURCE_DIR);

        File::makeDirectory($publicDir, 0755, true, true);
        File::makeDirectory($privateDir, 0755, true, true);

        $generatedFiles = [];
        $ffmpegBin = $this->resolveFfmpegBinary();

        if (! $ffmpegBin) {
            $log('⚠️ Cảnh báo: Không tìm thấy FFmpeg binary. Vui lòng cài đặt FFmpeg.');
            return [];
        }

        for ($i = 1; $i <= $count; $i++) {
            $filename = sprintf('demo_source_video_%03d.mp4', $i);
            $publicFilePath = $publicDir . DIRECTORY_SEPARATOR . $filename;
            $privateFilePath = $privateDir . DIRECTORY_SEPARATOR . $filename;
            $relativeStoragePath = self::RELATIVE_SOURCE_DIR . '/' . $filename;

            $duration = 12 + ($i % 13); // 12s -> 24s
            $freq = 440 + (($i * 50) % 800); // 440Hz -> 1200Hz

            // Nếu file đã tồn tại và có dung lượng hợp lệ thì bỏ qua không tạo lại (idempotent)
            if (file_exists($publicFilePath) && filesize($publicFilePath) > 10000) {
                if (! file_exists($privateFilePath)) {
                    copy($publicFilePath, $privateFilePath);
                }

                $generatedFiles[] = [
                    'relative_path' => $relativeStoragePath,
                    'public_path' => $publicFilePath,
                    'private_path' => $privateFilePath,
                    'filename' => $filename,
                    'duration' => $duration,
                    'size' => filesize($publicFilePath),
                    'mime' => 'video/mp4',
                ];
                continue;
            }

            // Tạo video bằng FFmpeg lavfi testsrc2 + sine
            $process = new Process([
                $ffmpegBin,
                '-hide_banner',
                '-nostdin',
                '-f', 'lavfi',
                '-i', "testsrc2=size=1280x720:rate=25",
                '-f', 'lavfi',
                '-i', "sine=frequency={$freq}:sample_rate=44100",
                '-t', (string)$duration,
                '-c:v', 'libx264',
                '-preset', 'ultrafast',
                '-pix_fmt', 'yuv420p',
                '-c:a', 'aac',
                '-b:a', '128k',
                '-movflags', '+faststart',
                '-y',
                $publicFilePath,
            ]);

            $process->setTimeout(60);
            $process->run();

            if ($process->isSuccessful() && file_exists($publicFilePath)) {
                // Mirror to local/private disk as well
                copy($publicFilePath, $privateFilePath);

                $fileSize = filesize($publicFilePath);
                $generatedFiles[] = [
                    'relative_path' => $relativeStoragePath,
                    'public_path' => $publicFilePath,
                    'private_path' => $privateFilePath,
                    'filename' => $filename,
                    'duration' => $duration,
                    'size' => $fileSize,
                    'mime' => 'video/mp4',
                ];

                if ($i % 15 === 0 || $i === $count) {
                    $log("✓ Đã sinh {$i}/{$count} video MP4 nguồn thành công");
                }
            } else {
                $log("❌ Lỗi sinh video {$filename}: " . $process->getErrorOutput());
            }
        }

        $log("✓ Hoàn thành sinh tổng cộng " . count($generatedFiles) . " file MP4 thật vào storage");
        return $generatedFiles;
    }

    public function attachVideoToLesson(Lesson $lesson, array $videoInfo, string $processingStatus = 'pending'): void
    {
        $lesson->update([
            'type' => Lesson::TYPE_VIDEO,
            'video_path' => $videoInfo['relative_path'],
            'video_original_name' => $videoInfo['filename'],
            'video_mime' => 'video/mp4',
            'video_size' => $videoInfo['size'],
            'duration' => $videoInfo['duration'],
            'duration_seconds' => $videoInfo['duration'],
            'upload_status' => 'uploaded',
            'processing_status' => $processingStatus,
            'status' => 'published',
        ]);
    }

    private function resolveFfmpegBinary(): ?string
    {
        $bin = env('FFMPEG_BINARIES') ?: env('FFMPEG_BIN');
        if ($bin && file_exists($bin)) {
            return $bin;
        }

        $paths = [
            'C:/laragon/bin/ffmpeg/bin/ffmpeg.exe',
            'C:/ffmpeg/bin/ffmpeg.exe',
            'ffmpeg',
        ];

        foreach ($paths as $path) {
            $p = new Process([$path, '-version']);
            $p->run();
            if ($p->isSuccessful()) {
                return $path;
            }
        }

        return null;
    }
}
