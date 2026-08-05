<?php

namespace App\Jobs;

use App\Models\ContentUpdate;
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

class ConvertContentUpdateVideoToHLS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 3600;

    public function __construct(
        public ContentUpdate $contentUpdate
    ) {}

    public function handle(): void
    {
        $payload = $this->contentUpdate->payload ?? [];
        $videoPath = $payload['video_path'] ?? null;

        if (!$videoPath || !\Illuminate\Support\Str::endsWith($videoPath, '.mp4')) {
            return;
        }

        try {
            $mp4PathLocal = Storage::disk('local')->path($videoPath);
            $mp4PathPublic = Storage::disk('public')->path($videoPath);

            $mp4Path = file_exists($mp4PathLocal) ? $mp4PathLocal : (file_exists($mp4PathPublic) ? $mp4PathPublic : null);

            if (!$mp4Path) {
                throw new \Exception("MP4 file not found at local or public disk for ContentUpdate ID {$this->contentUpdate->id}: {$videoPath}");
            }

            $hlsDir = 'lesson-hls/update_' . $this->contentUpdate->id;
            Storage::disk('local')->makeDirectory($hlsDir);

            $hlsPath = Storage::disk('local')->path($hlsDir . '/playlist.m3u8');

            $ffmpeg = FFMpeg::create([
                'ffmpeg.binaries'  => env('FFMPEG_BINARIES', 'ffmpeg'),
                'ffprobe.binaries' => env('FFPROBE_BINARIES', 'ffprobe'),
                'timeout'          => 3600,
                'ffmpeg.threads'   => 12,
            ]);

            $video = $ffmpeg->open($mp4Path);

            $format = new X264('aac', 'libx264');
            $format->setPasses(1);
            $format->setAdditionalParameters([
                '-hls_time', '10',
                '-hls_list_size', '0',
                '-f', 'hls'
            ]);

            $video->save($format, $hlsPath);

            if (Storage::disk('local')->exists($videoPath)) {
                Storage::disk('local')->delete($videoPath);
            }
            if (Storage::disk('public')->exists($videoPath)) {
                Storage::disk('public')->delete($videoPath);
            }

            $payload['video_path'] = $hlsDir . '/playlist.m3u8';
            $this->contentUpdate->update([
                'payload' => $payload,
            ]);

        } catch (Throwable $e) {
            Log::error("Video conversion failed for ContentUpdate ID {$this->contentUpdate->id}: " . $e->getMessage());
            throw $e;
        } finally {
            \Illuminate\Support\Facades\Cache::forget('video_processing_update_' . $this->contentUpdate->id);
        }
    }
}
