<?php

namespace App\Services;

use Aws\CommandPool;
use Aws\S3\S3Client;
use FFMpeg\FFProbe;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;
use Throwable;

class HlsVideoService
{
    /**
     * @return array{duration_seconds: int|null, file_count: int, segment_count: int}
     */
    public function transcode(string $inputPath, string $outputDirectory): array
    {
        if (! is_file($inputPath)) {
            throw new RuntimeException('HLS source video does not exist.');
        }

        File::ensureDirectoryExists($outputDirectory);

        $ffmpegConfig = $this->ffmpegConfig();
        $probe = $this->probe($inputPath, $ffmpegConfig);
        $segmentSeconds = max(2, (int) config('video.hls.segment_seconds', 10));
        $preset = $this->validatedPreset((string) config('video.hls.preset', 'veryfast'));
        $crf = max(0, min(51, (int) config('video.hls.crf', 23)));
        $playlistPath = $outputDirectory.'/playlist.m3u8';

        $command = [
            $ffmpegConfig['ffmpeg.binaries'],
            '-hide_banner',
            '-y',
            '-i', $inputPath,
            '-map', '0:v:0',
            '-map', '0:a:0?',
            '-c:v', 'libx264',
            '-preset', $preset,
            '-crf', (string) $crf,
            '-threads', (string) $ffmpegConfig['ffmpeg.threads'],
            '-pix_fmt', 'yuv420p',
            '-c:a', 'aac',
            '-b:a', '128k',
            '-ac', '2',
            '-sc_threshold', '0',
            '-force_key_frames', 'expr:gte(t,n_forced*'.$segmentSeconds.')',
            '-hls_time', (string) $segmentSeconds,
            '-hls_list_size', '0',
            '-hls_flags', 'independent_segments',
            '-hls_segment_filename', $outputDirectory.'/segment_%05d.ts',
            '-f', 'hls',
            $playlistPath,
        ];

        $process = new Process($command);
        $process->setTimeout((float) $ffmpegConfig['timeout']);
        $process->mustRun();

        $segments = array_values(File::glob($outputDirectory.'/segment_*.ts') ?: []);
        if (! is_file($playlistPath) || filesize($playlistPath) === 0 || $segments === []) {
            throw new RuntimeException('FFmpeg did not produce a complete HLS playlist.');
        }

        $masterContent = $this->masterPlaylist(
            $probe['width'],
            $probe['height'],
            $probe['bandwidth'],
        );
        file_put_contents($outputDirectory.'/master.m3u8', $masterContent);

        return [
            'duration_seconds' => $probe['duration_seconds'],
            'file_count' => count(File::files($outputDirectory)),
            'segment_count' => count($segments),
        ];
    }

    /**
     * Publish the new files first, then remove only objects that are not part of
     * the successful output. Existing playback therefore remains available if
     * encoding or upload fails before completion.
     *
     * @return array{use_s3: bool, mirrored_locally: bool, file_count: int, obsolete_s3_files_removed: int}
     */
    public function publish(string $outputDirectory, string $s3Directory, string $localDirectory): array
    {
        $files = File::files($outputDirectory);
        if ($files === []) {
            throw new RuntimeException('There are no HLS files to publish.');
        }

        $useS3 = $this->isS3Configured();
        $obsoleteRemoved = 0;

        if ($useS3) {
            $this->uploadToS3($outputDirectory, $s3Directory, $files);
            $obsoleteRemoved = $this->removeObsoleteS3Files($s3Directory, $files);
        }

        $mirrorLocal = ! $useS3 || (bool) config('video.hls.mirror_local', true);
        if ($mirrorLocal) {
            $localPath = Storage::disk('local')->path($localDirectory);
            if (File::isDirectory($localPath)) {
                File::deleteDirectory($localPath);
            }
            File::ensureDirectoryExists($localPath);
            if (! File::copyDirectory($outputDirectory, $localPath)) {
                throw new RuntimeException('Could not publish the local HLS mirror.');
            }
        }

        return [
            'use_s3' => $useS3,
            'mirrored_locally' => $mirrorLocal,
            'file_count' => count($files),
            'obsolete_s3_files_removed' => $obsoleteRemoved,
        ];
    }

    /** @return array{ffmpeg.binaries: string, ffprobe.binaries: string, timeout: int, ffmpeg.threads: int} */
    public function ffmpegConfig(): array
    {
        return [
            'ffmpeg.binaries' => $this->binaryPath('ffmpeg'),
            'ffprobe.binaries' => $this->binaryPath('ffprobe'),
            'timeout' => max(60, (int) config('video.ffmpeg.timeout_seconds', 3600)),
            'ffmpeg.threads' => max(1, (int) config('video.ffmpeg.threads', 12)),
        ];
    }

    /**
     * @param  array{ffmpeg.binaries: string, ffprobe.binaries: string, timeout: int, ffmpeg.threads: int}  $ffmpegConfig
     * @return array{duration_seconds: int|null, width: int|null, height: int|null, bandwidth: int}
     */
    private function probe(string $inputPath, array $ffmpegConfig): array
    {
        $result = [
            'duration_seconds' => null,
            'width' => null,
            'height' => null,
            'bandwidth' => 2_500_000,
        ];

        try {
            $ffprobe = FFProbe::create($ffmpegConfig);
            $format = $ffprobe->format($inputPath);
            $duration = (int) round((float) $format->get('duration'));
            $sourceBitrate = (int) $format->get('bit_rate');
            $video = $ffprobe->streams($inputPath)->videos()->first();

            if ($duration > 0) {
                $result['duration_seconds'] = $duration;
            }
            if ($video) {
                $width = (int) $video->get('width');
                $height = (int) $video->get('height');
                $result['width'] = $width > 0 ? $width : null;
                $result['height'] = $height > 0 ? $height : null;
            }
            if ($sourceBitrate > 0) {
                $result['bandwidth'] = max(500_000, min(12_000_000, (int) ceil($sourceBitrate * 1.2)));
            }
        } catch (Throwable $exception) {
            Log::warning('[HlsVideoService] Could not probe source metadata.', [
                'message' => $exception->getMessage(),
            ]);
        }

        return $result;
    }

    private function masterPlaylist(?int $width, ?int $height, int $bandwidth): string
    {
        $attributes = ['BANDWIDTH='.$bandwidth];
        if ($width && $height) {
            $attributes[] = "RESOLUTION={$width}x{$height}";
        }

        return "#EXTM3U\n"
            ."#EXT-X-VERSION:3\n"
            ."#EXT-X-INDEPENDENT-SEGMENTS\n"
            .'#EXT-X-STREAM-INF:'.implode(',', $attributes)."\n"
            ."playlist.m3u8\n";
    }

    /** @param array<int, SplFileInfo> $files */
    private function uploadToS3(string $outputDirectory, string $s3Directory, array $files): void
    {
        $orderedFiles = $this->publicationOrder($files);

        try {
            $client = new S3Client($this->s3ClientConfig());
            $bucket = (string) config('filesystems.disks.s3.bucket');
            $segments = array_values(array_filter(
                $orderedFiles,
                fn ($file): bool => ! str_ends_with(strtolower($file->getFilename()), '.m3u8'),
            ));
            $commands = array_map(
                fn ($file) => $client->getCommand('PutObject', [
                    'Bucket' => $bucket,
                    'Key' => $s3Directory.'/'.$file->getFilename(),
                    'SourceFile' => $file->getRealPath(),
                    'ContentType' => $this->contentType($file->getFilename()),
                ]),
                $segments,
            );

            foreach (CommandPool::batch($client, $commands, ['concurrency' => 20]) as $result) {
                if ($result instanceof Throwable) {
                    throw new RuntimeException('Could not upload an HLS segment to S3.', previous: $result);
                }
            }

            // Publish media playlist and master manifest only after every segment
            // exists. This avoids exposing a manifest whose segments are missing.
            foreach (array_slice($orderedFiles, count($segments)) as $file) {
                $client->putObject([
                    'Bucket' => $bucket,
                    'Key' => $s3Directory.'/'.$file->getFilename(),
                    'SourceFile' => $file->getRealPath(),
                    'ContentType' => $this->contentType($file->getFilename()),
                ]);
            }
        } catch (Throwable $exception) {
            Log::warning('[HlsVideoService] S3 transfer pool failed; using sequential upload.', [
                'message' => $exception->getMessage(),
            ]);

            foreach ($orderedFiles as $file) {
                $filename = $file->getFilename();
                $stream = fopen($file->getRealPath(), 'rb');
                if (! is_resource($stream)) {
                    throw new RuntimeException('Could not read HLS file for S3 upload: '.$filename);
                }

                try {
                    if (! Storage::disk('s3')->put(
                        $s3Directory.'/'.$filename,
                        $stream,
                        ['ContentType' => $this->contentType($filename)],
                    )) {
                        throw new RuntimeException('Could not upload HLS file to S3: '.$filename);
                    }
                } finally {
                    fclose($stream);
                }
            }
        }
    }

    /**
     * Segments are published first, then the media playlist, then the master
     * manifest. The last two files are the playback commit point.
     *
     * @param  array<int, SplFileInfo>  $files
     * @return array<int, SplFileInfo>
     */
    private function publicationOrder(array $files): array
    {
        usort($files, function ($left, $right): int {
            $weight = static function (string $filename): int {
                $filename = strtolower($filename);

                if ($filename === 'master.m3u8') {
                    return 2;
                }

                return str_ends_with($filename, '.m3u8') ? 1 : 0;
            };

            return [$weight($left->getFilename()), $left->getFilename()]
                <=> [$weight($right->getFilename()), $right->getFilename()];
        });

        return array_values($files);
    }

    /**
     * @param  array<int, SplFileInfo>  $publishedFiles
     */
    private function removeObsoleteS3Files(string $s3Directory, array $publishedFiles): int
    {
        try {
            $expected = array_map(
                fn ($file): string => $s3Directory.'/'.$file->getFilename(),
                $publishedFiles,
            );
            $obsolete = array_values(array_diff(Storage::disk('s3')->files($s3Directory), $expected));

            if ($obsolete !== []) {
                Storage::disk('s3')->delete($obsolete);
            }

            return count($obsolete);
        } catch (Throwable $exception) {
            // Cleanup is best-effort. A cleanup outage must not turn a valid,
            // already-uploaded HLS rendition into a failed lesson.
            Log::warning('[HlsVideoService] Could not remove obsolete S3 HLS objects.', [
                'directory' => $s3Directory,
                'message' => $exception->getMessage(),
            ]);

            return 0;
        }
    }

    /** @return array<string, mixed> */
    private function s3ClientConfig(): array
    {
        $disk = (array) config('filesystems.disks.s3', []);
        $config = [
            'version' => 'latest',
            'region' => $disk['region'] ?? 'ap-southeast-1',
            'credentials' => [
                'key' => $disk['key'],
                'secret' => $disk['secret'],
            ],
            'use_path_style_endpoint' => (bool) ($disk['use_path_style_endpoint'] ?? false),
        ];

        if (! empty($disk['token'])) {
            $config['credentials']['token'] = $disk['token'];
        }
        if (! empty($disk['endpoint'])) {
            $config['endpoint'] = $disk['endpoint'];
        }

        return $config;
    }

    private function isS3Configured(): bool
    {
        return filled(config('filesystems.disks.s3.key'))
            && filled(config('filesystems.disks.s3.secret'))
            && filled(config('filesystems.disks.s3.bucket'));
    }

    private function contentType(string $filename): string
    {
        return str_ends_with(strtolower($filename), '.m3u8')
            ? 'application/vnd.apple.mpegurl'
            : 'video/mp2t';
    }

    private function binaryPath(string $binary): string
    {
        $configured = $binary === 'ffmpeg'
            ? config('video.ffmpeg.binary')
            : config('video.ffmpeg.probe_binary');
        if (is_string($configured) && trim($configured) !== '') {
            return $configured;
        }

        $executable = PHP_OS_FAMILY === 'Windows' ? $binary.'.exe' : $binary;
        $candidates = [
            base_path('bin/ffmpeg/'.$executable),
            'C:/laragon/bin/ffmpeg/bin/'.$executable,
            'C:/ffmpeg/bin/'.$executable,
        ];
        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return $binary;
    }

    private function validatedPreset(string $preset): string
    {
        $allowed = ['ultrafast', 'superfast', 'veryfast', 'faster', 'fast', 'medium', 'slow', 'slower', 'veryslow'];

        return in_array($preset, $allowed, true) ? $preset : 'veryfast';
    }
}
