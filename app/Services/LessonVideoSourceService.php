<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Lesson;
use Illuminate\Support\Facades\Storage;

class LessonVideoSourceService
{
    /** Project video fields in memory; never change the live lesson or the release. */
    public function forViewer(Lesson $lesson, ?int $userId): Lesson
    {
        $courseId = $lesson->course_id ?? $lesson->section?->course_id ?? $lesson->chapter?->course_id;
        $enrollment = $userId ? Enrollment::query()->where('user_id', $userId)
            ->where('course_id', $courseId)->withLearningAccess()->first() : null;

        // Historical enrollments without a known release retain the legacy policy.
        if (! $enrollment?->course_version_id) {
            return $lesson;
        }

        $release = $enrollment->courseVersion;
        abort_unless($release && (int) $release->course_id === (int) $courseId
            && in_array($release->status, ['published', 'superseded'], true)
            && $release->manifest_built_at, 404);
        $version = $release->lessonMappings()->where('lesson_id', $lesson->id)->first()?->lessonVersion;
        abort_unless($version && (int) $version->lesson_id === (int) $lesson->id
            && $version->type === Lesson::TYPE_VIDEO
            && in_array($version->status, ['published', 'superseded'], true), 404);

        $video = clone $lesson;
        $video->forceFill($version->only([
            'video_url', 'video_path', 'original_video_key', 'hls_manifest_key',
            'hls_playlist', 'hls_path', 'video_original_name', 'video_mime', 'video_size',
            'duration_seconds', 'is_preview',
        ]));
        $video->duration = $version->duration_seconds;
        $video->setRelation('playbackVersion', $version);

        return $video;
    }

    /** Only explicit media locations may override a legacy directory. */
    public function directories(Lesson $lesson): array
    {
        $manifest = $lesson->hls_manifest_key;
        $localPlaylist = $lesson->video_path;
        $legacyS3 = 'hls/lessons/'.$lesson->id;
        $legacyLocal = 'lesson-hls/'.$lesson->id;

        if (filled($lesson->video_url)) {
            return ['s3' => null, 'local' => null];
        }

        $local = filled($localPlaylist) && str_ends_with($localPlaylist, '.m3u8')
            ? dirname($localPlaylist) : null;
        $s3 = filled($manifest) ? dirname($manifest) : null;

        if (! $manifest && ! $localPlaylist) {
            // Old imports may only have files at the conventional location.
            return ['s3' => $legacyS3, 'local' => $legacyLocal];
        }
        if ($s3 === $legacyS3 && ! $localPlaylist) {
            $local = $legacyLocal;
        }
        if ($local === $legacyLocal && ! $manifest) {
            $s3 = $legacyS3;
        }

        return ['s3' => $s3, 'local' => $local];
    }

    public function hasHls(Lesson $lesson): bool
    {
        $directories = $this->directories($lesson);

        return ($directories['s3'] && filled($lesson->hls_manifest_key))
            || ($directories['local'] && (str_ends_with((string) $lesson->video_path, '.m3u8')
                || Storage::disk('local')->exists($directories['local'].'/playlist.m3u8')
                || Storage::disk('local')->exists($directories['local'].'/master.m3u8')));
    }
}
