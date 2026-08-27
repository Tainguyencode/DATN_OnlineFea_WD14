<?php

namespace App\Jobs;

use App\Models\Lesson;
use Aws\Command;
use Aws\S3\S3Client;
use Aws\S3\Transfer;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
        $startTime = microtime(true);
        $lessonId = $this->lesson->id;
        $hasS3Original = filled($this->lesson->original_video_key);
        $hasLocalPath = filled($this->lesson->video_path);

        // ─── [BƯỚC 1] JOB START ───
        Log::info("[ConvertVideoToHLS] [JOB START] Lesson ID: {$lessonId}", [
            'lesson_id' => $lessonId,
            'original_video_key' => $this->lesson->original_video_key,
            'video_path' => $this->lesson->video_path,
            'has_s3_original' => $hasS3Original,
            'has_local_path' => $hasLocalPath,
            'queue_attempts' => $this->attempts(),
        ]);

        if (! $hasS3Original && ! $hasLocalPath) {
            Log::warning("[ConvertVideoToHLS] Skipped: Lesson {$lessonId} has no video source.");

            return;
        }

        $this->lesson->update(['processing_status' => 'processing']);

        $tmpDir = storage_path('app/tmp_ffmpeg/lesson_'.$lessonId.'_'.Str::random(8));
        File::makeDirectory($tmpDir, 0755, true, true);

        $localInputPath = null;
        $downloadedFromS3 = false;

        try {
            // ─── [BƯỚC 2] DOWNLOAD ORIGINAL ───
            if ($hasS3Original && Storage::disk('s3')->exists($this->lesson->original_video_key)) {
                $ext = pathinfo($this->lesson->original_video_key, PATHINFO_EXTENSION) ?: 'mp4';
                $localInputPath = $tmpDir.'/source_video.'.$ext;

                Log::info("[ConvertVideoToHLS] [DOWNLOAD ORIGINAL] Downloading from S3: {$this->lesson->original_video_key} to {$localInputPath}");

                $s3Stream = Storage::disk('s3')->readStream($this->lesson->original_video_key);
                if (! $s3Stream) {
                    throw new \RuntimeException('Cannot read stream from S3: '.$this->lesson->original_video_key);
                }

                $localFile = fopen($localInputPath, 'wb');
                stream_copy_to_stream($s3Stream, $localFile);
                fclose($localFile);
                if (is_resource($s3Stream)) {
                    fclose($s3Stream);
                }

                $sourceSize = file_exists($localInputPath) ? filesize($localInputPath) : 0;
                Log::info('[ConvertVideoToHLS] [DOWNLOAD ORIGINAL] S3 download completed', [
                    'local_path' => $localInputPath,
                    'file_size_bytes' => $sourceSize,
                ]);

                $downloadedFromS3 = true;
            } elseif ($hasLocalPath) {
                $mp4PathLocal = Storage::disk('local')->path($this->lesson->video_path);
                $mp4PathPublic = Storage::disk('public')->path($this->lesson->video_path);
                $localInputPath = file_exists($mp4PathLocal) ? $mp4PathLocal : (file_exists($mp4PathPublic) ? $mp4PathPublic : null);

                Log::info("[ConvertVideoToHLS] [DOWNLOAD ORIGINAL] Using local source video: {$localInputPath}");
            }

            if (! $localInputPath || ! file_exists($localInputPath)) {
                throw new \RuntimeException("Source video not found for Lesson ID: {$lessonId}");
            }

            // ─── [BƯỚC 3] FFMPEG START ───
            $hlsOutDir = $tmpDir.'/hls_out';
            File::makeDirectory($hlsOutDir, 0755, true, true);
            $playlistPath = $hlsOutDir.'/playlist.m3u8';

            $ffmpegConfig = $this->getFfmpegConfig();
            $ffmpegBin = $ffmpegConfig['ffmpeg.binaries'];
            Log::info('[ConvertVideoToHLS] [FFMPEG START] Starting HLS conversion (Stream Copy mode)', [
                'lesson_id' => $lessonId,
                'input' => $localInputPath,
                'output_playlist' => $playlistPath,
                'ffmpeg_binary' => $ffmpegBin,
                'ffprobe_binary' => $ffmpegConfig['ffprobe.binaries'],
            ]);

            // 1. Thử phân đoạn HLS bằng Stream Copy (-c copy) để tốc độ tối đa (chỉ mất 2-5 giây)
            $command = [
                $ffmpegBin,
                '-y',
                '-i', $localInputPath,
                '-c', 'copy',
                '-hls_time', '10',
                '-hls_list_size', '0',
                '-hls_segment_filename', $hlsOutDir.'/segment_%03d.ts',
                '-f', 'hls',
                $playlistPath,
            ];

            $process = new \Symfony\Component\Process\Process($command);
            $process->setTimeout(3600);
            $process->run();

            // 2. Nếu Stream copy gặp lỗi codec, fallback sang re-encode ultrafast
            if (! $process->isSuccessful() || ! file_exists($playlistPath) || filesize($playlistPath) === 0) {
                Log::warning('[ConvertVideoToHLS] Stream copy fallback to ultrafast re-encode: '.$process->getErrorOutput());

                $fallbackCommand = [
                    $ffmpegBin,
                    '-y',
                    '-i', $localInputPath,
                    '-c:v', 'libx264',
                    '-preset', 'ultrafast',
                    '-c:a', 'aac',
                    '-hls_time', '10',
                    '-hls_list_size', '0',
                    '-hls_segment_filename', $hlsOutDir.'/segment_%03d.ts',
                    '-f', 'hls',
                    $playlistPath,
                ];

                $fallbackProcess = new \Symfony\Component\Process\Process($fallbackCommand);
                $fallbackProcess->setTimeout(3600);
                $fallbackProcess->mustRun();
            }

            // Tạo master.m3u8
            $masterContent = "#EXTM3U\n#EXT-X-VERSION:3\n#EXT-X-STREAM-INF:BANDWIDTH=2500000,RESOLUTION=1280x720\nplaylist.m3u8\n";
            file_put_contents($hlsOutDir.'/master.m3u8', $masterContent);

            $hlsFiles = File::files($hlsOutDir);
            $segmentCount = count($hlsFiles);

            // ─── [BƯỚC 4] FFMPEG SUCCESS ───
            Log::info('[ConvertVideoToHLS] [FFMPEG SUCCESS] Conversion completed successfully', [
                'lesson_id' => $lessonId,
                'total_files_generated' => $segmentCount,
                'playlist_size_bytes' => file_exists($playlistPath) ? filesize($playlistPath) : 0,
            ]);

            // ─── [BƯỚC 5] UPLOAD HLS ───
            $s3HlsDir = 'hls/lessons/'.$lessonId;
            $useS3 = ! empty(config('filesystems.disks.s3.key')) && ! empty(config('filesystems.disks.s3.bucket'));

            Log::info("[ConvertVideoToHLS] [UPLOAD HLS] Uploading {$segmentCount} files to destination", [
                'use_s3' => $useS3,
                's3_target_dir' => $s3HlsDir,
            ]);

            if ($useS3) {
                try {
                    $s3Config = [
                        'version' => 'latest',
                        'region' => config('filesystems.disks.s3.region', 'ap-southeast-1'),
                        'credentials' => [
                            'key' => config('filesystems.disks.s3.key'),
                            'secret' => config('filesystems.disks.s3.secret'),
                        ],
                    ];
                    $s3Client = new S3Client($s3Config);
                    $bucket = config('filesystems.disks.s3.bucket');

                    $manager = new Transfer($s3Client, $hlsOutDir, 's3://'.$bucket.'/'.$s3HlsDir, [
                        'concurrency' => 20,
                        'before' => function (Command $command) {
                            $key = $command['Key'] ?? '';
                            $mimeType = str_ends_with($key, '.m3u8') ? 'application/vnd.apple.mpegurl' : 'video/mp2t';
                            $command['ContentType'] = $mimeType;
                        },
                    ]);
                    $manager->transfer();
                } catch (Throwable $e) {
                    Log::warning('[ConvertVideoToHLS] S3 Transfer pool fallback to sequential: '.$e->getMessage());
                    foreach ($hlsFiles as $file) {
                        $filename = $file->getFilename();
                        $filePath = $file->getRealPath();
                        $fileContent = file_get_contents($filePath);
                        $mimeType = str_ends_with($filename, '.m3u8') ? 'application/vnd.apple.mpegurl' : 'video/mp2t';

                        Storage::disk('s3')->put($s3HlsDir.'/'.$filename, $fileContent, [
                            'ContentType' => $mimeType,
                        ]);
                    }
                }
            }

            // Sync sang local storage mirror
            $localMirrorDir = Storage::disk('local')->path('lesson-hls/'.$lessonId);
            File::makeDirectory($localMirrorDir, 0755, true, true);
            File::copyDirectory($hlsOutDir, $localMirrorDir);

            Log::info('[ConvertVideoToHLS] [UPLOAD HLS] Upload completed', [
                'lesson_id' => $lessonId,
                'files_uploaded' => $segmentCount,
            ]);

            // ─── [BƯỚC 6] SAVE DATABASE ───
            $updateData = [
                'processing_status' => 'completed',
                'upload_status' => 'uploaded',
                'status' => 'published',
            ];

            // Trích xuất chính xác thời lượng video
            try {
                $ffprobe = \FFMpeg\FFProbe::create($ffmpegConfig);
                $extractedDuration = (int) round((float) $ffprobe->format($localInputPath)->get('duration'));
                if ($extractedDuration > 0) {
                    $updateData['duration_seconds'] = $extractedDuration;
                    $updateData['duration'] = $extractedDuration;
                }
            } catch (\Throwable $probeEx) {
                Log::warning("[ConvertVideoToHLS] Could not probe video duration: " . $probeEx->getMessage());
            }

            if ($useS3) {
                $updateData['hls_manifest_key'] = $s3HlsDir.'/master.m3u8';
            }

            $updateData['video_path'] = 'lesson-hls/'.$lessonId.'/playlist.m3u8';

            $this->lesson->update($updateData);

            Log::info('[ConvertVideoToHLS] [SAVE DATABASE] Database updated successfully', [
                'lesson_id' => $lessonId,
                'duration_seconds' => $updateData['duration_seconds'] ?? $this->lesson->duration_seconds,
                'hls_manifest_key' => $updateData['hls_manifest_key'] ?? null,
                'video_path' => $updateData['video_path'],
                'processing_status' => 'completed',
            ]);

            // ─── [BƯỚC 7] JOB COMPLETED ───
            $durationSeconds = round(microtime(true) - $startTime, 2);
            Log::info("[ConvertVideoToHLS] [JOB COMPLETED] Full pipeline finished in {$durationSeconds}s for Lesson ID {$lessonId}");

        } catch (Throwable $e) {
            Log::error("[ConvertVideoToHLS] Conversion failed for Lesson ID {$lessonId}: ".$e->getMessage(), [
                'lesson_id' => $lessonId,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->lesson->update([
                'processing_status' => 'failed',
            ]);

            throw $e;
        } finally {
            if (File::exists($tmpDir)) {
                File::deleteDirectory($tmpDir);
            }

            Cache::forget('video_processing_'.$lessonId);
        }
    }

    /**
     * Tự động phát hiện đường dẫn binary FFmpeg & FFprobe
     */
    private function getFfmpegConfig(): array
    {
        $ffmpegBin = env('FFMPEG_BINARIES') ?: env('FFMPEG_BIN');
        $ffprobeBin = env('FFPROBE_BINARIES') ?: env('FFPROBE_BIN');

        if (! $ffmpegBin) {
            if (file_exists('C:/laragon/bin/ffmpeg/bin/ffmpeg.exe')) {
                $ffmpegBin = 'C:/laragon/bin/ffmpeg/bin/ffmpeg.exe';
            } elseif (file_exists('C:/ffmpeg/bin/ffmpeg.exe')) {
                $ffmpegBin = 'C:/ffmpeg/bin/ffmpeg.exe';
            } else {
                $ffmpegBin = 'ffmpeg';
            }
        }

        if (! $ffprobeBin) {
            if (file_exists('C:/laragon/bin/ffmpeg/bin/ffprobe.exe')) {
                $ffprobeBin = 'C:/laragon/bin/ffmpeg/bin/ffprobe.exe';
            } elseif (file_exists('C:/ffmpeg/bin/ffprobe.exe')) {
                $ffprobeBin = 'C:/ffmpeg/bin/ffprobe.exe';
            } else {
                $ffprobeBin = 'ffprobe';
            }
        }

        return [
            'ffmpeg.binaries' => $ffmpegBin,
            'ffprobe.binaries' => $ffprobeBin,
            'timeout' => 3600,
            'ffmpeg.threads' => (int) env('FFMPEG_THREADS', 12),
        ];
    }
}
