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
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
        $startTime = microtime(true);
        $updateId = $this->contentUpdate->id;
        $payload = $this->contentUpdate->payload ?? [];
        $s3OriginalKey = $payload['original_video_key'] ?? null;
        $videoPath = $payload['video_path'] ?? null;

        // ─── [BƯỚC 1] JOB START ───
        Log::info("[ConvertContentUpdateVideoToHLS] [JOB START] ContentUpdate ID: {$updateId}", [
            'update_id' => $updateId,
            'original_video_key' => $s3OriginalKey,
            'video_path' => $videoPath,
            'queue_attempts' => $this->attempts(),
        ]);

        if (!$s3OriginalKey && !$videoPath) {
            Log::warning("[ConvertContentUpdateVideoToHLS] Skipped: ContentUpdate {$updateId} has no video source.");
            return;
        }

        // Đánh dấu payload đang xử lý
        $payload['processing_status'] = 'processing';
        $this->contentUpdate->update(['payload' => $payload]);

        $tmpDir = storage_path('app/tmp_ffmpeg/update_' . $updateId . '_' . Str::random(8));
        File::makeDirectory($tmpDir, 0755, true, true);

        $localInputPath = null;

        try {
            // ─── [BƯỚC 2] DOWNLOAD ORIGINAL ───
            if ($s3OriginalKey && Storage::disk('s3')->exists($s3OriginalKey)) {
                $ext = pathinfo($s3OriginalKey, PATHINFO_EXTENSION) ?: 'mp4';
                $localInputPath = $tmpDir . '/source_video.' . $ext;

                Log::info("[ConvertContentUpdateVideoToHLS] [DOWNLOAD ORIGINAL] Downloading from S3: {$s3OriginalKey} to {$localInputPath}");

                $s3Stream = Storage::disk('s3')->readStream($s3OriginalKey);
                if (!$s3Stream) {
                    throw new \RuntimeException("Cannot read stream from S3: " . $s3OriginalKey);
                }

                $localFile = fopen($localInputPath, 'wb');
                stream_copy_to_stream($s3Stream, $localFile);
                fclose($localFile);
                if (is_resource($s3Stream)) {
                    fclose($s3Stream);
                }

                $sourceSize = file_exists($localInputPath) ? filesize($localInputPath) : 0;
                Log::info("[ConvertContentUpdateVideoToHLS] [DOWNLOAD ORIGINAL] S3 download completed", [
                    'local_path' => $localInputPath,
                    'file_size_bytes' => $sourceSize,
                ]);
            } elseif ($videoPath) {
                $mp4PathLocal = Storage::disk('local')->path($videoPath);
                $mp4PathPublic = Storage::disk('public')->path($videoPath);
                $localInputPath = file_exists($mp4PathLocal) ? $mp4PathLocal : (file_exists($mp4PathPublic) ? $mp4PathPublic : null);

                Log::info("[ConvertContentUpdateVideoToHLS] [DOWNLOAD ORIGINAL] Using local source video: {$localInputPath}");
            }

            if (!$localInputPath || !file_exists($localInputPath)) {
                throw new \RuntimeException("Source video not found for ContentUpdate ID {$updateId}");
            }

            // ─── [BƯỚC 3] FFMPEG START ───
            $hlsOutDir = $tmpDir . '/hls_out';
            File::makeDirectory($hlsOutDir, 0755, true, true);
            $playlistPath = $hlsOutDir . '/playlist.m3u8';

            $ffmpegConfig = $this->getFfmpegConfig();
            Log::info("[ConvertContentUpdateVideoToHLS] [FFMPEG START] Starting HLS conversion", [
                'update_id' => $updateId,
                'input' => $localInputPath,
                'output_playlist' => $playlistPath,
                'ffmpeg_binary' => $ffmpegConfig['ffmpeg.binaries'],
                'ffprobe_binary' => $ffmpegConfig['ffprobe.binaries'],
                'threads' => $ffmpegConfig['ffmpeg.threads'],
            ]);

            $ffmpeg = FFMpeg::create($ffmpegConfig);
            $video = $ffmpeg->open($localInputPath);

            $format = new X264('aac', 'libx264');
            $format->setPasses(1);
            $format->setAdditionalParameters([
                '-hls_time', '10',
                '-hls_list_size', '0',
                '-f', 'hls',
            ]);

            $video->save($format, $playlistPath);

            // Master manifest
            $masterContent = "#EXTM3U\n#EXT-X-VERSION:3\n#EXT-X-STREAM-INF:BANDWIDTH=2500000,RESOLUTION=1280x720\nplaylist.m3u8\n";
            file_put_contents($hlsOutDir . '/master.m3u8', $masterContent);

            $hlsFiles = File::files($hlsOutDir);
            $segmentCount = count($hlsFiles);

            // ─── [BƯỚC 4] FFMPEG SUCCESS ───
            Log::info("[ConvertContentUpdateVideoToHLS] [FFMPEG SUCCESS] Conversion completed successfully", [
                'update_id' => $updateId,
                'total_files_generated' => $segmentCount,
                'playlist_size_bytes' => file_exists($playlistPath) ? filesize($playlistPath) : 0,
            ]);

            // ─── [BƯỚC 5] UPLOAD HLS ───
            $s3HlsDir = 'hls/updates/' . $updateId;
            $useS3 = !empty(config('filesystems.disks.s3.key')) && !empty(config('filesystems.disks.s3.bucket'));

            Log::info("[ConvertContentUpdateVideoToHLS] [UPLOAD HLS] Uploading {$segmentCount} files to destination", [
                'use_s3' => $useS3,
                's3_target_dir' => $s3HlsDir,
            ]);

            if ($useS3) {
                try {
                    $s3Config = [
                        'version' => 'latest',
                        'region'  => config('filesystems.disks.s3.region', 'ap-southeast-1'),
                        'credentials' => [
                            'key'    => config('filesystems.disks.s3.key'),
                            'secret' => config('filesystems.disks.s3.secret'),
                        ],
                    ];
                    $s3Client = new \Aws\S3\S3Client($s3Config);
                    $bucket = config('filesystems.disks.s3.bucket');

                    $manager = new \Aws\S3\Transfer($s3Client, $hlsOutDir, 's3://' . $bucket . '/' . $s3HlsDir, [
                        'concurrency' => 20,
                        'before' => function (\Aws\Command $command) {
                            $key = $command['Key'] ?? '';
                            $mimeType = str_ends_with($key, '.m3u8') ? 'application/vnd.apple.mpegurl' : 'video/mp2t';
                            $command['ContentType'] = $mimeType;
                        },
                    ]);
                    $manager->transfer();
                } catch (\Throwable $e) {
                    Log::warning("[ConvertContentUpdateVideoToHLS] S3 Transfer pool fallback: " . $e->getMessage());
                    foreach ($hlsFiles as $file) {
                        $filename = $file->getFilename();
                        $filePath = $file->getRealPath();
                        $fileContent = file_get_contents($filePath);
                        $mimeType = str_ends_with($filename, '.m3u8') ? 'application/vnd.apple.mpegurl' : 'video/mp2t';

                        Storage::disk('s3')->put($s3HlsDir . '/' . $filename, $fileContent, [
                            'ContentType' => $mimeType,
                        ]);
                    }
                }
            }

            // Sync sang local storage mirror
            $localMirrorDir = Storage::disk('local')->path('lesson-hls/update_' . $updateId);
            File::makeDirectory($localMirrorDir, 0755, true, true);
            File::copyDirectory($hlsOutDir, $localMirrorDir);

            Log::info("[ConvertContentUpdateVideoToHLS] [UPLOAD HLS] Upload completed", [
                'update_id' => $updateId,
                'files_uploaded' => $segmentCount,
            ]);

            // ─── [BƯỚC 6] SAVE DATABASE ───
            $payload['processing_status'] = 'completed';
            $payload['upload_status'] = 'uploaded';
            $payload['video_path'] = 'lesson-hls/update_' . $updateId . '/playlist.m3u8';
            if ($useS3) {
                $payload['hls_manifest_key'] = $s3HlsDir . '/master.m3u8';
            }

            $this->contentUpdate->update([
                'payload' => $payload,
            ]);

            Log::info("[ConvertContentUpdateVideoToHLS] [SAVE DATABASE] Database payload updated successfully", [
                'update_id' => $updateId,
                'hls_manifest_key' => $payload['hls_manifest_key'] ?? null,
                'video_path' => $payload['video_path'],
                'processing_status' => 'completed',
            ]);

            // ─── [BƯỚC 7] JOB COMPLETED ───
            $durationSeconds = round(microtime(true) - $startTime, 2);
            Log::info("[ConvertContentUpdateVideoToHLS] [JOB COMPLETED] Full pipeline finished in {$durationSeconds}s for ContentUpdate ID {$updateId}");

        } catch (Throwable $e) {
            Log::error("[ConvertContentUpdateVideoToHLS] Video conversion failed for ContentUpdate ID {$updateId}: " . $e->getMessage(), [
                'update_id' => $updateId,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            $payload['processing_status'] = 'failed';
            $this->contentUpdate->update(['payload' => $payload]);

            throw $e;
        } finally {
            if (File::exists($tmpDir)) {
                File::deleteDirectory($tmpDir);
            }

            \Illuminate\Support\Facades\Cache::forget('video_processing_update_' . $updateId);
        }
    }

    /**
     * Tự động phát hiện đường dẫn binary FFmpeg & FFprobe
     */
    private function getFfmpegConfig(): array
    {
        $ffmpegBin = env('FFMPEG_BINARIES') ?: env('FFMPEG_BIN');
        $ffprobeBin = env('FFPROBE_BINARIES') ?: env('FFPROBE_BIN');

        if (!$ffmpegBin) {
            if (file_exists('C:/laragon/bin/ffmpeg/bin/ffmpeg.exe')) {
                $ffmpegBin = 'C:/laragon/bin/ffmpeg/bin/ffmpeg.exe';
            } elseif (file_exists('C:/ffmpeg/bin/ffmpeg.exe')) {
                $ffmpegBin = 'C:/ffmpeg/bin/ffmpeg.exe';
            } else {
                $ffmpegBin = 'ffmpeg';
            }
        }

        if (!$ffprobeBin) {
            if (file_exists('C:/laragon/bin/ffmpeg/bin/ffprobe.exe')) {
                $ffprobeBin = 'C:/laragon/bin/ffmpeg/bin/ffprobe.exe';
            } elseif (file_exists('C:/ffmpeg/bin/ffprobe.exe')) {
                $ffprobeBin = 'C:/ffmpeg/bin/ffprobe.exe';
            } else {
                $ffprobeBin = 'ffprobe';
            }
        }

        return [
            'ffmpeg.binaries'  => $ffmpegBin,
            'ffprobe.binaries' => $ffprobeBin,
            'timeout'          => 3600,
            'ffmpeg.threads'   => (int) env('FFMPEG_THREADS', 12),
        ];
    }
}
