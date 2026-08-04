<?php

namespace App\Services;

use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;

class VideoFrameExtractor
{
    private ?string $ffmpegBin;

    private ?string $ffprobeBin;

    public function __construct()
    {
        $this->ffmpegBin = env('FFMPEG_BIN') ?: null;
        $this->ffprobeBin = env('FFPROBE_BIN') ?: null;
    }

    public function extract(string $videoPath, int $intervalSeconds = 30, ?int $lessonId = null): array
    {
        $config = [];
        if ($this->ffmpegBin) {
            $config['ffmpeg.binaries'] = $this->ffmpegBin;
        }
        if ($this->ffprobeBin) {
            $config['ffprobe.binaries'] = $this->ffprobeBin;
        }

        // 1. Lấy duration thực tế của video
        $ffprobe = FFProbe::create($config);

        $duration = (float) $ffprobe
            ->format($videoPath)
            ->get('duration');

        // 2. Mở video
        $ffmpeg = FFMpeg::create($config);

        $video = $ffmpeg->open($videoPath);

        // 3. Tạo thư mục output — dùng DIRECTORY_SEPARATOR cho Windows
        $outputDir = storage_path('app'.DIRECTORY_SEPARATOR.'temp_frames');
        if ($lessonId) {
            $outputDir .= DIRECTORY_SEPARATOR.'lesson_'.$lessonId;
        }

        if (! is_dir($outputDir)) {
            mkdir($outputDir, 0777, true);
        }

        $frames = [];

        // 4. Cắt frame mỗi $intervalSeconds giây, không vượt quá duration
        for ($i = 0; $i < $duration; $i += $intervalSeconds) {
            // Đảm bảo không lấy frame ở đúng cuối (có thể gây lỗi)
            $second = min($i, max(0, $duration - 1));

            // Dùng DIRECTORY_SEPARATOR để tránh mixed slash trên Windows
            $file = $outputDir.DIRECTORY_SEPARATOR."frame_{$i}.jpg";

            try {
                $video
                    ->frame(TimeCode::fromSeconds($second))
                    ->save($file);

                if (file_exists($file) && filesize($file) > 0) {
                    $frames[] = $file;
                }
            } catch (\Throwable $e) {
                // Bỏ qua frame bị lỗi (nếu timestamp đó bị hỏng/lỗi codec) để tiếp tục các frame khác
                continue;
            }
        }

        return $frames;
    }
}
