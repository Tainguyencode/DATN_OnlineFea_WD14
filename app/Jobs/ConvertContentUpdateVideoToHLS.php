<?php

namespace App\Jobs;

use App\Models\ContentUpdate;
use App\Services\HlsVideoService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class ConvertContentUpdateVideoToHLS implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $timeout = 3600;

    public $uniqueFor = 3600;

    public function __construct(
        public ContentUpdate $contentUpdate
    ) {}

    public function uniqueId(): string
    {
        return 'content-update:'.$this->contentUpdate->getKey();
    }

    public function handle(HlsVideoService $hlsVideo): void
    {
        $startTime = microtime(true);
        $this->contentUpdate = $this->contentUpdate->fresh() ?? $this->contentUpdate;
        $updateId = (int) $this->contentUpdate->id;
        $payload = $this->contentUpdate->payload ?? [];
        $s3OriginalKey = $payload['original_video_key'] ?? null;
        $videoPath = $payload['video_path'] ?? null;

        Log::info('[ConvertContentUpdateVideoToHLS] Job started.', [
            'update_id' => $updateId,
            'has_s3_original' => filled($s3OriginalKey),
            'has_local_path' => filled($videoPath),
            'queue_attempts' => $this->attempts(),
        ]);

        if (! $s3OriginalKey && ! $videoPath) {
            $this->mergePayload(['processing_status' => 'failed']);
            Log::warning('[ConvertContentUpdateVideoToHLS] Content update has no video source.', [
                'update_id' => $updateId,
            ]);

            return;
        }

        $this->mergePayload(['processing_status' => 'processing']);
        $tmpDir = storage_path('app/tmp_ffmpeg/update_'.$updateId.'_'.Str::random(8));
        File::ensureDirectoryExists($tmpDir);

        try {
            $localInputPath = $this->resolveInputPath($tmpDir, $s3OriginalKey, $videoPath);
            $hlsOutputDirectory = $tmpDir.'/hls_out';
            $encodeStartedAt = microtime(true);
            $conversion = $hlsVideo->transcode($localInputPath, $hlsOutputDirectory);

            Log::info('[ConvertContentUpdateVideoToHLS] FFmpeg conversion completed.', [
                'update_id' => $updateId,
                'files_generated' => $conversion['file_count'],
                'segments_generated' => $conversion['segment_count'],
                'duration_seconds' => round(microtime(true) - $encodeStartedAt, 2),
            ]);

            $s3Directory = 'hls/updates/'.$updateId;
            $localDirectory = 'lesson-hls/update_'.$updateId;
            $publication = $hlsVideo->publish($hlsOutputDirectory, $s3Directory, $localDirectory);
            $changes = [
                'processing_status' => 'completed',
                'upload_status' => 'uploaded',
                'hls_manifest_key' => $publication['use_s3'] ? $s3Directory.'/master.m3u8' : null,
                'video_path' => $publication['mirrored_locally'] ? $localDirectory.'/playlist.m3u8' : null,
            ];
            if ($conversion['duration_seconds']) {
                $changes['duration_seconds'] = $conversion['duration_seconds'];
                $changes['duration'] = $conversion['duration_seconds'];
            }
            $payload = $this->mergePayload($changes);

            Log::info('[ConvertContentUpdateVideoToHLS] Job completed.', [
                'update_id' => $updateId,
                'hls_manifest_key' => $payload['hls_manifest_key'] ?? null,
                'obsolete_s3_files_removed' => $publication['obsolete_s3_files_removed'],
                'duration_seconds' => round(microtime(true) - $startTime, 2),
            ]);
        } catch (Throwable $exception) {
            $this->mergePayload(['processing_status' => 'failed']);
            Log::error('[ConvertContentUpdateVideoToHLS] Conversion failed.', [
                'update_id' => $updateId,
                'message' => $exception->getMessage(),
                'exception' => $exception,
            ]);

            throw $exception;
        } finally {
            if (File::isDirectory($tmpDir)) {
                File::deleteDirectory($tmpDir);
            }
            Cache::forget('video_processing_update_'.$updateId);
        }
    }

    public function failed(?Throwable $exception): void
    {
        $this->mergePayload(['processing_status' => 'failed']);
        Cache::forget('video_processing_update_'.$this->contentUpdate->getKey());

        Log::error('[ConvertContentUpdateVideoToHLS] Job exhausted all attempts.', [
            'update_id' => $this->contentUpdate->getKey(),
            'message' => $exception?->getMessage(),
        ]);
    }

    private function resolveInputPath(string $tmpDir, ?string $s3OriginalKey, ?string $videoPath): string
    {
        if (filled($s3OriginalKey)) {
            $extension = pathinfo($s3OriginalKey, PATHINFO_EXTENSION) ?: 'mp4';
            $target = $tmpDir.'/source_video.'.$extension;
            $source = Storage::disk('s3')->readStream($s3OriginalKey);
            if (! is_resource($source)) {
                throw new RuntimeException('Cannot read the content-update source stream from S3.');
            }

            $destination = fopen($target, 'wb');
            if (! is_resource($destination)) {
                fclose($source);
                throw new RuntimeException('Cannot create the local content-update source file.');
            }

            try {
                if (stream_copy_to_stream($source, $destination) === false) {
                    throw new RuntimeException('Cannot copy the content-update source stream from S3.');
                }
            } finally {
                fclose($source);
                fclose($destination);
            }

            return $target;
        }

        if (filled($videoPath)) {
            $localPath = Storage::disk('local')->path($videoPath);
            $publicPath = Storage::disk('public')->path($videoPath);
            if (is_file($localPath)) {
                return $localPath;
            }
            if (is_file($publicPath)) {
                return $publicPath;
            }
        }

        throw new RuntimeException('Source video not found for content update ID '.$this->contentUpdate->getKey().'.');
    }

    /** @param array<string, mixed> $changes */
    private function mergePayload(array $changes): array
    {
        return DB::transaction(function () use ($changes): array {
            $contentUpdate = ContentUpdate::query()
                ->lockForUpdate()
                ->find($this->contentUpdate->getKey());
            if (! $contentUpdate) {
                return [];
            }

            $payload = array_merge($contentUpdate->payload ?? [], $changes);
            $contentUpdate->update(['payload' => $payload]);
            $this->contentUpdate = $contentUpdate;

            return $payload;
        });
    }
}
