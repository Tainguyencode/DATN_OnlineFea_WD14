<?php

namespace App\Services;

use App\Jobs\ConvertContentUpdateVideoToHLS;
use App\Jobs\ConvertVideoToHLS;
use App\Models\ContentUpdate;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\User;
use DomainException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CurriculumLessonService
{
    public function __construct(private readonly ContentUpdateService $contentUpdates) {}

    /**
     * Manual curriculum entry point. Published content keeps using ContentUpdate.
     */
    public function createForManual(
        Course $course,
        CourseSection|int $section,
        array $data,
        User $actor
    ): Lesson|ContentUpdate {
        if ($this->canCreateDirectly($course)) {
            if (! $section instanceof CourseSection) {
                throw new DomainException('A persisted course section is required for direct lesson creation.');
            }

            return $this->create($course, $section, $data);
        }

        if (! $course->isContentApproved()) {
            throw new DomainException('Lessons cannot be created for the current course status.');
        }

        return $this->createPendingUpdate(
            $course,
            $section,
            $data,
            $actor,
        );
    }

    /**
     * Shared direct-write entry point for manual creation and a future importer.
     */
    public function create(Course $course, CourseSection $section, array $data): Lesson
    {
        if (! $this->canCreateDirectly($course)) {
            throw new DomainException('Direct lesson creation is only allowed for draft or rejected courses.');
        }

        if ((int) $section->course_id !== (int) $course->id) {
            throw new DomainException('The lesson section does not belong to the course.');
        }

        [$lessonData, $storedFiles, $shouldDispatchHls] = $this->prepareData(
            $data,
            $section->id,
            $section->lessons()->count(),
        );

        try {
            $lesson = DB::transaction(function () use ($course, $lessonData, $data): Lesson {
                $lesson = Lesson::create([
                    ...$lessonData,
                    'course_id' => $course->id,
                    'chapter_id' => null,
                ]);

                $this->syncAssignment($lesson, $data);

                return $lesson;
            });
        } catch (Throwable $exception) {
            $this->cleanupStoredFiles($storedFiles);

            throw $exception;
        }

        if ($shouldDispatchHls) {
            ConvertVideoToHLS::dispatch($lesson);
        }

        return $lesson;
    }

    public function canCreateDirectly(Course $course): bool
    {
        return ! $course->isContentApproved()
            && in_array($course->status, [Course::STATUS_DRAFT, Course::STATUS_REJECTED], true);
    }

    public function canCreateForManual(Course $course): bool
    {
        return $this->canCreateDirectly($course) || $course->isContentApproved();
    }

    public function syncAssignment(Lesson $lesson, array $data): void
    {
        if ($lesson->type !== Lesson::TYPE_ASSIGNMENT) {
            return;
        }

        $lesson->loadMissing('assignment');
        $description = trim((string) ($data['content'] ?? ''));
        $existing = $lesson->assignment;

        $lesson->assignment()->updateOrCreate(
            ['lesson_id' => $lesson->id],
            [
                'course_id' => $lesson->course_id,
                'title' => $lesson->title,
                'description' => $description !== '' ? $description : $lesson->title,
                'instructions' => $description !== '' ? $description : null,
                'max_score' => $data['assignment_max_score'] ?? $existing?->max_score ?? 100,
                'passing_score' => $data['assignment_passing_score'] ?? $existing?->passing_score ?? 70,
                'due_days' => $data['assignment_due_days'] ?? $existing?->due_days,
                'is_required' => true,
                'allowed_file_types' => $existing?->allowed_file_types ?? 'pdf,doc,docx,ppt,pptx,xls,xlsx,txt,zip,rar',
                'maximum_file_size' => $existing?->maximum_file_size ?? 10240,
            ],
        );
    }

    private function createPendingUpdate(
        Course $course,
        CourseSection|int $section,
        array $data,
        User $actor
    ): ContentUpdate {
        $sectionId = $section instanceof CourseSection ? $section->id : $section;
        $defaultSortOrder = $section instanceof CourseSection ? $section->lessons()->count() : 0;
        [$lessonData, $storedFiles, $shouldDispatchHls] = $this->prepareData($data, $sectionId, $defaultSortOrder);

        $payload = array_merge(
            $lessonData,
            array_intersect_key($data, array_flip([
                'assignment_due_days',
                'assignment_max_score',
                'assignment_passing_score',
            ])),
        );

        try {
            $contentUpdate = $this->contentUpdates->recordPendingUpdate(
                ContentUpdate::TYPE_LESSON,
                ContentUpdate::ACTION_CREATE,
                $course->id,
                null,
                $payload,
                $actor,
                ContentUpdate::STATUS_DRAFT,
            );
        } catch (Throwable $exception) {
            $this->cleanupStoredFiles($storedFiles);

            throw $exception;
        }

        if ($shouldDispatchHls) {
            ConvertContentUpdateVideoToHLS::dispatch($contentUpdate);
        }

        return $contentUpdate;
    }

    /**
     * @return array{0: array<string, mixed>, 1: array<int, array{disk: string, path: string}>, 2: bool}
     */
    private function prepareData(array $data, int $sectionId, int $defaultSortOrder): array
    {
        $videoFile = $data['video_file'] ?? null;
        $documentFile = $data['document_file'] ?? null;
        $s3Key = filled($data['s3_key'] ?? null) ? (string) $data['s3_key'] : null;
        $storedFiles = [];
        $type = $data['type'] ?? null;
        $duration = max(0, (int) ($data['duration'] ?? $data['duration_seconds'] ?? 0));

        $data = array_intersect_key($data, array_flip([
            'title',
            'type',
            'video_url',
            'video_original_name',
            'video_mime',
            'video_size',
            'content',
            'duration',
            'duration_seconds',
            'is_preview',
            'sort_order',
            'status',
        ]));

        unset(
            $data['video_file'],
            $data['s3_key'],
            $data['document_file'],
            $data['assignment_due_days'],
            $data['assignment_max_score'],
            $data['assignment_passing_score'],
        );

        if ($type !== Lesson::TYPE_VIDEO) {
            unset(
                $data['video_url'],
                $data['video_path'],
                $data['original_video_key'],
                $data['hls_manifest_key'],
                $data['video_original_name'],
                $data['video_mime'],
                $data['video_size'],
                $data['upload_status'],
                $data['processing_status'],
            );
        }

        if (! in_array($type, [Lesson::TYPE_VIDEO, Lesson::TYPE_DOCUMENT, Lesson::TYPE_ASSIGNMENT], true)) {
            unset($data['content']);
        }

        if (in_array($type, [Lesson::TYPE_DOCUMENT, Lesson::TYPE_ASSIGNMENT], true)
            && $documentFile instanceof UploadedFile) {
            $path = $documentFile->store('lesson-documents', 'public');
            $data['document_file'] = $path;
            $storedFiles[] = ['disk' => 'public', 'path' => $path];
        }

        if ($type === Lesson::TYPE_VIDEO && $s3Key !== null) {
            $data = array_merge($data, [
                'original_video_key' => $s3Key,
                'video_original_name' => (string) (($data['video_original_name'] ?? null) ?: basename($s3Key)),
                'video_mime' => (string) (($data['video_mime'] ?? null) ?: 'video/mp4'),
                'video_size' => (int) ($data['video_size'] ?? 0),
                'upload_status' => 'uploaded',
                'processing_status' => 'pending',
            ]);
        } elseif ($type === Lesson::TYPE_VIDEO && $videoFile instanceof UploadedFile) {
            $path = $videoFile->store('lesson-videos-mp4', 'local');
            $data = array_merge($data, [
                'video_path' => $path,
                'video_original_name' => $videoFile->getClientOriginalName(),
                'video_mime' => $videoFile->getClientMimeType(),
                'video_size' => $videoFile->getSize(),
                'upload_status' => 'uploaded',
                'processing_status' => 'pending',
            ]);
            $storedFiles[] = ['disk' => 'local', 'path' => $path];
        }

        $data = array_merge($data, [
            'section_id' => $sectionId,
            'duration' => $duration,
            'duration_seconds' => $duration,
            'is_preview' => (bool) ($data['is_preview'] ?? false),
            'sort_order' => $data['sort_order'] ?? $defaultSortOrder,
            'status' => $data['status'] ?? Lesson::STATUS_DRAFT,
        ]);

        return [$data, $storedFiles, $type === Lesson::TYPE_VIDEO && ($s3Key !== null || $videoFile instanceof UploadedFile)];
    }

    /**
     * @param  array<int, array{disk: string, path: string}>  $storedFiles
     */
    private function cleanupStoredFiles(array $storedFiles): void
    {
        foreach ($storedFiles as $storedFile) {
            Storage::disk($storedFile['disk'])->delete($storedFile['path']);
        }
    }
}
