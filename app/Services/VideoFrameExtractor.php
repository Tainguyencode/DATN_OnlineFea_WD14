<?php

namespace App\Services;

use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use FFMpeg\Coordinate\TimeCode;

class VideoFrameExtractor
{
    private string $ffmpegBin  = 'C:\\Users\\Lenovo\\Downloads\\ffmpeg-8.1.2-essentials_build\\ffmpeg-8.1.2-essentials_build\\bin\\ffmpeg.exe';
    private string $ffprobeBin = 'C:\\Users\\Lenovo\\Downloads\\ffmpeg-8.1.2-essentials_build\\ffmpeg-8.1.2-essentials_build\\bin\\ffprobe.exe';

    public function extract(string $videoPath, int $intervalSeconds = 30, ?int $lessonId = null): array
    {
        // 1. Lấy duration thực tế của video
        $ffprobe = FFProbe::create([
            'ffmpeg.binaries'  => $this->ffmpegBin,
            'ffprobe.binaries' => $this->ffprobeBin,
        ]);

        $duration = (float) $ffprobe
            ->format($videoPath)
            ->get('duration');

        // 2. Mở video
        $ffmpeg = FFMpeg::create([
            'ffmpeg.binaries'  => $this->ffmpegBin,
            'ffprobe.binaries' => $this->ffprobeBin,
        ]);

        $video = $ffmpeg->open($videoPath);

        // 3. Tạo thư mục output — dùng DIRECTORY_SEPARATOR cho Windows
        $outputDir = storage_path('app' . DIRECTORY_SEPARATOR . 'temp_frames');
        if ($lessonId) {
            $outputDir .= DIRECTORY_SEPARATOR . 'lesson_' . $lessonId;
        }

        if (! is_dir($outputDir)) {
            mkdir($outputDir, 0777, true);
        }

        $frames = [];

        // 4. Cắt frame mỗi $intervalSeconds giây, không vượt quá duration
        for ($i = 0; $i < $duration; $i += $intervalSeconds) {
            // Đảm bảo không lấy frame ở đúng cuối (có thể gây lỗi)
            $second = min($i, $duration - 1);

            // Dùng DIRECTORY_SEPARATOR để tránh mixed slash trên Windows
            $file = $outputDir . DIRECTORY_SEPARATOR . "frame_{$i}.jpg";

            $video
                ->frame(TimeCode::fromSeconds($second))
                ->save($file);

            $frames[] = $file;
        }

        return $frames;
    }
}