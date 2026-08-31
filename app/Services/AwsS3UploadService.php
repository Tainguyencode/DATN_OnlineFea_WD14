<?php

namespace App\Services;

use Aws\S3\S3Client;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class AwsS3UploadService
{
    private ?S3Client $s3Client = null;

    public function isS3Configured(): bool
    {
        return ! empty(Config::get('filesystems.disks.s3.key'))
            && ! empty(Config::get('filesystems.disks.s3.secret'))
            && ! empty(Config::get('filesystems.disks.s3.bucket'));
    }

    public function getS3Client(): S3Client
    {
        if ($this->s3Client) {
            return $this->s3Client;
        }

        $config = Config::get('filesystems.disks.s3');

        $clientConfig = [
            'version' => 'latest',
            'region' => $config['region'] ?? env('AWS_DEFAULT_REGION', 'ap-southeast-1'),
            'credentials' => [
                'key' => $config['key'] ?? env('AWS_ACCESS_KEY_ID'),
                'secret' => $config['secret'] ?? env('AWS_SECRET_ACCESS_KEY'),
            ],
            'use_path_style_endpoint' => (bool) ($config['use_path_style_endpoint'] ?? false),
        ];

        if (! empty($config['endpoint'])) {
            $clientConfig['endpoint'] = $config['endpoint'];
        }

        $this->s3Client = new S3Client($clientConfig);

        return $this->s3Client;
    }

    public function getBucket(): string
    {
        $bucket = Config::get('filesystems.disks.s3.bucket') ?? env('AWS_BUCKET');
        if (empty($bucket)) {
            throw new RuntimeException('AWS S3 Bucket is not configured in environment.');
        }

        return $bucket;
    }

    public function generateVideoObjectKey(int|string $courseId, int|string|null $lessonId, string $filename): string
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION)) ?: 'mp4';
        $allowedExtensions = ['mp4', 'mov', 'avi', 'webm', 'mkv', 'm4v'];
        if (! in_array($extension, $allowedExtensions, true)) {
            $extension = 'mp4';
        }

        $uuid = (string) Str::uuid();
        $lessonSegment = $lessonId ? (string) $lessonId : 'temp_'.Str::random(10);

        return "originals/courses/{$courseId}/lessons/{$lessonSegment}/{$uuid}.{$extension}";
    }

    public function generateDraftVideoObjectKey(
        int|string $courseId,
        int|string $lessonId,
        int|string $contentUpdateId,
        int|string $versionNumber,
        string $filename
    ): string {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION)) ?: 'mp4';
        $allowedExtensions = ['mp4', 'mov', 'avi', 'webm', 'mkv', 'm4v'];
        if (! in_array($extension, $allowedExtensions, true)) {
            $extension = 'mp4';
        }

        return sprintf(
            'originals/courses/%s/lessons/%s/content-updates/%s/versions/v%s/%s.%s',
            $courseId,
            $lessonId,
            $contentUpdateId,
            $versionNumber,
            Str::uuid(),
            $extension
        );
    }

    /**
     * Khởi tạo S3 Multipart Upload
     */
    public function createMultipartUpload(string $key, string $contentType = 'video/mp4'): string
    {
        $client = $this->getS3Client();
        $bucket = $this->getBucket();

        $result = $client->createMultipartUpload([
            'Bucket' => $bucket,
            'Key' => $key,
            'ContentType' => $contentType,
        ]);

        return (string) $result['UploadId'];
    }

    /**
     * Ký presigned URL cho từng part
     */
    public function createPresignedPartUrl(string $key, string $uploadId, int $partNumber, int $expiresInMinutes = 20): string
    {
        $client = $this->getS3Client();
        $bucket = $this->getBucket();

        $command = $client->getCommand('UploadPart', [
            'Bucket' => $bucket,
            'Key' => $key,
            'UploadId' => $uploadId,
            'PartNumber' => $partNumber,
        ]);

        $request = $client->createPresignedRequest($command, "+{$expiresInMinutes} minutes");

        return (string) $request->getUri();
    }

    /**
     * Hoàn tất Multipart Upload
     *
     * @param  array<int, array{PartNumber: int, ETag: string}>  $parts
     */
    public function completeMultipartUpload(string $key, string $uploadId, array $parts): array
    {
        $client = $this->getS3Client();
        $bucket = $this->getBucket();

        // Sort parts by PartNumber ascending (S3 requirement)
        usort($parts, fn ($a, $b) => ((int) $a['PartNumber']) <=> ((int) $b['PartNumber']));

        $result = $client->completeMultipartUpload([
            'Bucket' => $bucket,
            'Key' => $key,
            'UploadId' => $uploadId,
            'MultipartUpload' => [
                'Parts' => $parts,
            ],
        ]);

        return [
            'location' => $result['Location'] ?? null,
            'bucket' => $result['Bucket'] ?? $bucket,
            'key' => $result['Key'] ?? $key,
            'etag' => $result['ETag'] ?? null,
        ];
    }

    /**
     * Hủy Multipart Upload
     */
    public function abortMultipartUpload(string $key, string $uploadId): void
    {
        try {
            $client = $this->getS3Client();
            $bucket = $this->getBucket();

            $client->abortMultipartUpload([
                'Bucket' => $bucket,
                'Key' => $key,
                'UploadId' => $uploadId,
            ]);
        } catch (\Throwable $e) {
            Log::warning("Abort multipart upload failed for key {$key}: ".$e->getMessage());
        }
    }

    /**
     * Tạo Signed URL để xem/stream S3 object trực tiếp
     */
    public function createPresignedViewUrl(string $key, int $expiresInMinutes = 60): string
    {
        // Hỗ trợ CloudFront URL nếu có cấu hình
        $cloudFrontUrl = Config::get('filesystems.disks.s3.cloudfront_url') ?? env('AWS_CLOUDFRONT_URL');
        if (! empty($cloudFrontUrl)) {
            $baseUrl = rtrim($cloudFrontUrl, '/');
            $cleanKey = ltrim($key, '/');

            return "{$baseUrl}/{$cleanKey}";
        }

        $client = $this->getS3Client();
        $bucket = $this->getBucket();

        $command = $client->getCommand('GetObject', [
            'Bucket' => $bucket,
            'Key' => $key,
        ]);

        $request = $client->createPresignedRequest($command, "+{$expiresInMinutes} minutes");

        return (string) $request->getUri();
    }

    /**
     * Kiểm tra xem một S3 object key đã thực sự tồn tại hoàn chỉnh trên S3 chưa
     */
    public function doesObjectExist(string $key): bool
    {
        try {
            $client = $this->getS3Client();
            $bucket = $this->getBucket();

            return $client->doesObjectExist($bucket, $key);
        } catch (\Throwable $e) {
            Log::warning("S3 doesObjectExist check error for key {$key}: ".$e->getMessage());

            return false;
        }
    }
}
