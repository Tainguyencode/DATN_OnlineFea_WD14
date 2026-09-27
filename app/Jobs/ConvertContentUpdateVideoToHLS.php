<?php

namespace App\Jobs;

use App\Models\ContentUpdate;
use App\Models\LessonVersion;
use App\Services\ContentUpdateService;
use App\Services\ContentVersionService;
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

    public ?string $expectedOriginalVideoKey = null;

    public ?string $expectedVideoPath = null;

    public bool $hasCapturedVideoIdentity = false;

    public function __construct(
        public ContentUpdate $contentUpdate,
        ?string $expectedOriginalVideoKey = null,
        ?string $expectedVideoPath = null,
    ) {
        $payload = $contentUpdate->payload ?? [];
        $this->expectedOriginalVideoKey = $this->normalizeSource(
            $expectedOriginalVideoKey ?? ($payload['original_video_key'] ?? null)
        );
        $this->expectedVideoPath = $this->normalizeSource(
            $expectedVideoPath ?? ($payload['video_path'] ?? null)
        );
        $this->hasCapturedVideoIdentity = true;
    }

    public function uniqueId(): string
    {
        return 'content-update:'.$this->contentUpdate->getKey().':video:'.$this->videoRevisionHash();
    }

    public function handle(ContentUpdateService $updates, ContentVersionService $versions): void
    {
        $hlsVideo = app(HlsVideoService::class);
        $startTime = microtime(true);
        $updateId = (int) $this->contentUpdate->getKey();
        if (! $this->hasCapturedVideoIdentity) {
            Log::warning('[ConvertContentUpdateVideoToHLS] Legacy job without captured video identity skipped.', [
                'update_id' => $updateId,
            ]);

            return;
        }
        $current = $this->beginProcessingIfCurrent($updates);
        if (! $current) {
            $this->logStale('before_processing');

            return;
        }
        $this->contentUpdate = $current;

        $candidate = $this->draftCandidate($this->contentUpdate);
        $payload = $this->contentUpdate->payload ?? [];
        $s3OriginalKey = $payload['original_video_key'] ?? null;
        $videoPath = $payload['video_path'] ?? null;

        Log::info('[ConvertContentUpdateVideoToHLS] Job started.', [
            'update_id' => $updateId,
            'lesson_version_id' => $candidate?->id,
            'version_number' => $candidate?->version_number,
            'has_s3_original' => filled($s3OriginalKey),
            'has_local_path' => filled($videoPath),
            'queue_attempts' => $this->attempts(),
        ]);

        if (! $s3OriginalKey && ! $videoPath) {
            if ($this->markFailedIfEditable($updates)) {
                Log::warning('[ConvertContentUpdateVideoToHLS] Content update has no video source.', [
                    'update_id' => $updateId,
                ]);
            } else {
                $this->logStale('before_missing_source_failure');
            }

            return;
        }

        $tmpDir = storage_path('app/tmp_ffmpeg/update_'.$updateId.'_'.Str::random(8));
        File::ensureDirectoryExists($tmpDir);

        try {
            $localInputPath = $this->resolveInputPath($tmpDir, $s3OriginalKey, $videoPath);
            $hlsOutputDirectory = $tmpDir.'/hls_out';
            $encodeStartedAt = microtime(true);
            $conversion = $hlsVideo->transcode($localInputPath, $hlsOutputDirectory);

            Log::info('[ConvertContentUpdateVideoToHLS] FFmpeg conversion completed.', [
                'update_id' => $updateId,
                'lesson_version_id' => $candidate?->id,
                'files_generated' => $conversion['file_count'],
                'segments_generated' => $conversion['segment_count'],
                'duration_seconds' => round(microtime(true) - $encodeStartedAt, 2),
            ]);

            if ($candidate) {
                $s3Directory = sprintf(
                    'hls/courses/%s/lessons/%s/content-updates/%s/versions/v%s/revisions/%s',
                    $this->contentUpdate->course_id,
                    $this->contentUpdate->entity_id,
                    $updateId,
                    $candidate->version_number,
                    $this->videoRevisionHash(),
                );
                $localDirectory = "lesson-hls/content-updates/{$updateId}/lesson-versions/{$candidate->id}/revisions/{$this->videoRevisionHash()}";
            } else {
                // Legacy/new-identity drafts have no immutable lesson identity yet.
                $s3Directory = 'hls/updates/'.$updateId.'/revisions/'.$this->videoRevisionHash();
                $localDirectory = 'lesson-hls/update_'.$updateId.'/revisions/'.$this->videoRevisionHash();
            }

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

            $committed = $this->commitResultIfCurrent($updates, $versions, $candidate, $changes);
            if (! $committed) {
                $this->logStale('before_commit');

                return;
            }
            $this->contentUpdate = $committed;

            Log::info('[ConvertContentUpdateVideoToHLS] Job completed.', [
                'update_id' => $updateId,
                'lesson_version_id' => $candidate?->id,
                'hls_manifest_key' => $this->contentUpdate->payload['hls_manifest_key'] ?? null,
                'obsolete_s3_files_removed' => $publication['obsolete_s3_files_removed'],
                'duration_seconds' => round(microtime(true) - $startTime, 2),
            ]);
        } catch (Throwable $exception) {
            if (! $this->matchesCurrentVideo()) {
                $this->logStale('after_processing_error', ['message' => $exception->getMessage()]);

                return;
            }

            $markedFailed = $this->markFailedIfEditable($updates);
            if (! $markedFailed && ! $this->matchesCurrentVideo()) {
                $this->logStale('before_failure_commit', ['message' => $exception->getMessage()]);

                return;
            }
            Log::error('[ConvertContentUpdateVideoToHLS] Conversion failed.', [
                'update_id' => $updateId,
                'lesson_version_id' => $candidate?->id,
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
        if (! $this->hasCapturedVideoIdentity) {
            return;
        }
        if (! $this->matchesCurrentVideo()) {
            $this->logStale('after_retries_exhausted', ['message' => $exception?->getMessage()]);

            return;
        }

        $this->markFailedIfEditable(app(ContentUpdateService::class));
        Cache::forget('video_processing_update_'.$this->contentUpdate->getKey());

        Log::error('[ConvertContentUpdateVideoToHLS] Job exhausted all attempts.', [
            'update_id' => $this->contentUpdate->getKey(),
            'message' => $exception?->getMessage(),
        ]);
    }

    private function draftCandidate(ContentUpdate $update): ?LessonVersion
    {
        if ($update->type !== ContentUpdate::TYPE_LESSON
            || $update->action !== ContentUpdate::ACTION_UPDATE
            || ! $update->entity_id) {
            return null;
        }

        $candidate = LessonVersion::query()
            ->where('content_update_id', $update->id)
            ->where('lesson_id', $update->entity_id)
            ->where('status', LessonVersion::STATUS_DRAFT)
            ->first();
        if (! $candidate) {
            throw new RuntimeException("ContentUpdate {$update->id} has no editable lesson candidate.");
        }

        $draftVersionId = $candidate->lesson()->value('draft_version_id');
        if ((int) $draftVersionId !== (int) $candidate->id) {
            throw new RuntimeException("ContentUpdate {$update->id} is not the active lesson candidate.");
        }

        return $candidate;
    }

    private function markFailedIfEditable(ContentUpdateService $updates): bool
    {
        try {
            $failed = DB::transaction(function () use ($updates): ?ContentUpdate {
                $fresh = ContentUpdate::query()->lockForUpdate()->find($this->contentUpdate->getKey());
                if (! $fresh?->isDraft() || ! $this->matchesExpectedVideo($fresh)) {
                    return null;
                }

                return $updates->updateDraft($fresh, ['processing_status' => 'failed']);
            });
            if ($failed) {
                $this->contentUpdate = $failed;

                return true;
            }

            return false;
        } catch (Throwable $exception) {
            Log::warning('[ConvertContentUpdateVideoToHLS] Could not mark editable draft as failed.', [
                'update_id' => $this->contentUpdate->getKey(),
                'message' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    private function beginProcessingIfCurrent(ContentUpdateService $updates): ?ContentUpdate
    {
        return DB::transaction(function () use ($updates): ?ContentUpdate {
            $fresh = ContentUpdate::query()->lockForUpdate()->findOrFail($this->contentUpdate->getKey());
            if (! $fresh->isDraft()) {
                throw new RuntimeException("ContentUpdate {$fresh->id} is no longer an editable draft.");
            }
            if (! $this->matchesExpectedVideo($fresh)) {
                return null;
            }

            return $updates->updateDraft($fresh, ['processing_status' => 'processing']);
        });
    }

    /** @param array<string, mixed> $changes */
    private function commitResultIfCurrent(
        ContentUpdateService $updates,
        ContentVersionService $versions,
        ?LessonVersion $candidate,
        array $changes,
    ): ?ContentUpdate {
        return DB::transaction(function () use ($updates, $versions, $candidate, $changes): ?ContentUpdate {
            $fresh = ContentUpdate::query()->lockForUpdate()->findOrFail($this->contentUpdate->getKey());
            if (! $fresh->isDraft() || ! $this->matchesExpectedVideo($fresh)) {
                return null;
            }

            $saved = $updates->updateDraft($fresh, $changes);
            if ($candidate) {
                $prepared = $versions->prepareDraftCandidate($saved, $saved->creator()->firstOrFail());
                if (! $prepared || (int) $prepared->id !== (int) $candidate->id) {
                    throw new RuntimeException("ContentUpdate {$saved->id} no longer points to the expected lesson candidate.");
                }
            }

            return $saved->fresh();
        });
    }

    private function matchesCurrentVideo(): bool
    {
        $fresh = ContentUpdate::query()->find($this->contentUpdate->getKey());

        return $fresh ? $this->matchesExpectedVideo($fresh) : false;
    }

    private function matchesExpectedVideo(ContentUpdate $update): bool
    {
        $payload = $update->payload ?? [];
        $currentOriginalKey = $this->normalizeSource($payload['original_video_key'] ?? null);
        if ($this->expectedOriginalVideoKey !== null || $currentOriginalKey !== null) {
            return $currentOriginalKey === $this->expectedOriginalVideoKey;
        }

        return $this->normalizeSource($payload['video_path'] ?? null) === $this->expectedVideoPath;
    }

    private function videoRevisionHash(): string
    {
        $identity = $this->expectedOriginalVideoKey !== null
            ? 's3:'.$this->expectedOriginalVideoKey
            : 'local:'.($this->expectedVideoPath ?? 'missing');

        return substr(hash('sha256', $identity), 0, 32);
    }

    private function normalizeSource(mixed $source): ?string
    {
        if (! is_string($source) || trim($source) === '') {
            return null;
        }

        return trim($source);
    }

    /** @param array<string, mixed> $context */
    private function logStale(string $stage, array $context = []): void
    {
        Log::info('[ConvertContentUpdateVideoToHLS] Stale video revision skipped.', [
            'update_id' => $this->contentUpdate->getKey(),
            'stage' => $stage,
            'expected_original_video_key' => $this->expectedOriginalVideoKey,
            'revision' => $this->videoRevisionHash(),
            ...$context,
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
}
