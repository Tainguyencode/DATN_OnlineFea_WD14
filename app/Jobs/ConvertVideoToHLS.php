<?php

namespace App\Jobs;

use App\Models\Lesson;
use App\Services\HlsVideoService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class ConvertVideoToHLS implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $timeout = 3600;

    public $uniqueFor = 3600;

    public function __construct(
        public Lesson $lesson
    ) {}

    public function uniqueId(): string
    {
        return 'lesson:'.$this->lesson->getKey();
    }

    public function handle(HlsVideoService $hlsVideo): void
    {
        $startTime = microtime(true);
        $this->lesson = $this->lesson->fresh() ?? $this->lesson;
        $lessonId = (int) $this->lesson->id;
        $hasS3Original = filled($this->lesson->original_video_key);
        $hasLocalPath = filled($this->lesson->video_path);

        Log::info('[ConvertVideoToHLS] Job started.', [
            'lesson_id' => $lessonId,
            'has_s3_original' => $hasS3Original,
            'has_local_path' => $hasLocalPath,
            'queue_attempts' => $this->attempts(),
        ]);

        if (! $hasS3Original && ! $hasLocalPath) {
            $this->markFailed();
            Log::warning('[ConvertVideoToHLS] Lesson has no video source.', ['lesson_id' => $lessonId]);

            return;
        }

        $this->lesson->update(['processing_status' => 'processing']);
        $tmpDir = storage_path('app/tmp_ffmpeg/lesson_'.$lessonId.'_'.Str::random(8));
        File::ensureDirectoryExists($tmpDir);

        try {
            $localInputPath = $this->resolveInputPath($tmpDir);
            $hlsOutputDirectory = $tmpDir.'/hls_out';
            $encodeStartedAt = microtime(true);
            $conversion = $hlsVideo->transcode($localInputPath, $hlsOutputDirectory);

            Log::info('[ConvertVideoToHLS] FFmpeg conversion completed.', [
                'lesson_id' => $lessonId,
                'files_generated' => $conversion['file_count'],
                'segments_generated' => $conversion['segment_count'],
                'duration_seconds' => round(microtime(true) - $encodeStartedAt, 2),
            ]);

            $s3Directory = 'hls/lessons/'.$lessonId;
            $localDirectory = 'lesson-hls/'.$lessonId;
            $publication = $hlsVideo->publish($hlsOutputDirectory, $s3Directory, $localDirectory);

            $updateData = [
                'processing_status' => 'completed',
                'upload_status' => 'uploaded',
                'status' => 'published',
                'hls_manifest_key' => $publication['use_s3'] ? $s3Directory.'/master.m3u8' : null,
                'video_path' => $publication['mirrored_locally'] ? $localDirectory.'/playlist.m3u8' : null,
            ];
            if ($conversion['duration_seconds']) {
                $updateData['duration_seconds'] = $conversion['duration_seconds'];
                $updateData['duration'] = $conversion['duration_seconds'];
            }
            $this->lesson->update($updateData);

            Log::info('[ConvertVideoToHLS] Job completed.', [
                'lesson_id' => $lessonId,
                'obsolete_s3_files_removed' => $publication['obsolete_s3_files_removed'],
                'duration_seconds' => round(microtime(true) - $startTime, 2),
            ]);
        } catch (Throwable $exception) {
            $this->markFailed();
            Log::error('[ConvertVideoToHLS] Conversion failed.', [
                'lesson_id' => $lessonId,
                'message' => $exception->getMessage(),
                'exception' => $exception,
            ]);

            throw $exception;
        } finally {
            if (File::isDirectory($tmpDir)) {
                File::deleteDirectory($tmpDir);
            }
            Cache::forget('video_processing_'.$lessonId);
        }
    }

    public function failed(?Throwable $exception): void
    {
        $this->markFailed();
        Cache::forget('video_processing_'.$this->lesson->getKey());

        Log::error('[ConvertVideoToHLS] Job exhausted all attempts.', [
            'lesson_id' => $this->lesson->getKey(),
            'message' => $exception?->getMessage(),
        ]);
    }

    private function resolveInputPath(string $tmpDir): string
    {
        if (filled($this->lesson->original_video_key)) {
            $extension = pathinfo($this->lesson->original_video_key, PATHINFO_EXTENSION) ?: 'mp4';
            $target = $tmpDir.'/source_video.'.$extension;
            $source = Storage::disk('s3')->readStream($this->lesson->original_video_key);
            if (! is_resource($source)) {
                throw new RuntimeException('Cannot read the lesson source stream from S3.');
            }

            $destination = fopen($target, 'wb');
            if (! is_resource($destination)) {
                fclose($source);
                throw new RuntimeException('Cannot create the local lesson source file.');
            }

            try {
                if (stream_copy_to_stream($source, $destination) === false) {
                    throw new RuntimeException('Cannot copy the lesson source stream from S3.');
                }
            } finally {
                fclose($source);
                fclose($destination);
            }

            return $target;
        }

        if (filled($this->lesson->video_path)) {
            $localPath = Storage::disk('local')->path($this->lesson->video_path);
            $publicPath = Storage::disk('public')->path($this->lesson->video_path);
            if (is_file($localPath)) {
                return $localPath;
            }
            if (is_file($publicPath)) {
                return $publicPath;
            }
        }

        throw new RuntimeException('Source video not found for lesson ID '.$this->lesson->getKey().'.');
    }

    private function markFailed(): void
    {
        Lesson::query()->whereKey($this->lesson->getKey())->update([
            'processing_status' => 'failed',
        ]);
    }
}
