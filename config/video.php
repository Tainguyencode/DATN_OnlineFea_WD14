<?php

return [
    'upload' => [
        // Shared limit for direct S3 uploads and lesson request validation (5 GiB).
        'max_bytes' => (int) env('VIDEO_UPLOAD_MAX_BYTES', 5 * 1024 * 1024 * 1024),
        'allowed_extensions' => ['mp4', 'm4v', 'mov', 'avi', 'webm', 'mkv'],
    ],

    // Isolated from the lesson-upload limit and HLS configuration.
    'course_preview' => [
        'max_bytes' => (int) env('COURSE_PREVIEW_MAX_BYTES', 250 * 1024 * 1024),
        'max_duration_seconds' => (int) env('COURSE_PREVIEW_MAX_DURATION_SECONDS', 180),
        'allowed_extensions' => ['mp4'],
    ],

    'ffmpeg' => [
        // Environment access stays inside config so `php artisan config:cache` is safe.
        'binary' => env('FFMPEG_BINARIES', env('FFMPEG_BIN')),
        'probe_binary' => env('FFPROBE_BINARIES', env('FFPROBE_BIN')),
        'threads' => (int) env('FFMPEG_THREADS', 12),
        'timeout_seconds' => (int) env('FFMPEG_TIMEOUT_SECONDS', 3600),
    ],

    'hls' => [
        // Favor shorter processing time; superfast produces larger files than veryfast.
        'preset' => env('HLS_FFMPEG_PRESET', 'superfast'),
        'crf' => (int) env('HLS_FFMPEG_CRF', 23),
        'segment_seconds' => (int) env('HLS_SEGMENT_SECONDS', 10),

        // Keep a local fallback only when explicitly needed. S3 remains the source of truth.
        'mirror_local' => (bool) env('HLS_MIRROR_LOCAL', true),
    ],
];
