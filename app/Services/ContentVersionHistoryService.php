<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\AssignmentVersion;
use App\Models\ContentUpdate;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\CourseSectionVersion;
use App\Models\CourseVersion;
use App\Models\Lesson;
use App\Models\LessonVersion;
use App\Models\Quiz;
use App\Models\QuizVersion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ContentVersionHistoryService
{
    public const TYPES = [
        ContentUpdate::TYPE_COURSE,
        ContentUpdate::TYPE_CHAPTER,
        ContentUpdate::TYPE_LESSON,
        ContentUpdate::TYPE_ASSIGNMENT,
        ContentUpdate::TYPE_QUIZ,
    ];

    public function __construct(private readonly ContentVersionComparisonService $comparison) {}

    public function timeline(Course $course, ?string $filter = null, int $perPage = 25): LengthAwarePaginator
    {
        $types = in_array($filter, self::TYPES, true) ? [$filter] : self::TYPES;
        $items = collect();
        foreach ($types as $type) {
            $items = $items->concat($this->versionsForCourse($course, $type)->map(fn (Model $version): array => $this->timelineItem($course, $type, $version)));
        }
        $siblingCounts = $items->countBy(fn (array $item): string => $item['type'].':'.$item['entity_id']);
        $items = $items->map(function (array $item) use ($siblingCounts): array {
            $item['comparison_available'] = ($siblingCounts[$item['type'].':'.$item['entity_id']] ?? 0) > 1;

            return $item;
        });
        $items = $items->sortByDesc(fn (array $item) => [$item['created_at']?->getTimestamp() ?? 0, $item['version_number']])->values();
        $page = LengthAwarePaginator::resolveCurrentPage();

        return new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    public function resolve(Course $course, string $type, int $versionId): Model
    {
        abort_unless(in_array($type, self::TYPES, true), 404);

        return $this->versionsForCourse($course, $type)->firstWhere('id', $versionId) ?? abort(404);
    }

    /** @return Collection<int, Model> */
    public function siblings(Course $course, string $type, Model $version): Collection
    {
        $identityId = $this->identityId($type, $version);

        return $this->versionsForCourse($course, $type)
            ->filter(fn (Model $candidate): bool => $this->identityId($type, $candidate) === $identityId)
            ->sortByDesc(fn (Model $candidate): int => $this->number($type, $candidate))
            ->values();
    }

    public function comparisonTarget(Collection $siblings, Model $from, ?int $requestedId = null): Model
    {
        $options = $siblings
            ->reject(fn (Model $candidate): bool => (int) $candidate->id === (int) $from->id)
            ->values();
        abort_if($options->isEmpty(), 422, 'Nội dung này chưa có phiên bản khác để so sánh.');

        if ($requestedId) {
            $requested = $options->firstWhere('id', $requestedId);
            abort_unless($requested, 404);

            return $requested;
        }

        $fromNumber = (int) ($from->version_number ?? $from->version);

        $previous = $options
            ->filter(fn (Model $candidate): bool => (int) ($candidate->version_number ?? $candidate->version) < $fromNumber)
            ->sortByDesc(fn (Model $candidate): int => (int) ($candidate->version_number ?? $candidate->version))
            ->first();
        if ($previous) {
            return $previous;
        }

        return $options->firstWhere('status', 'published')
            ?? $options->sortBy(fn (Model $candidate): int => (int) ($candidate->version_number ?? $candidate->version))->first();
    }

    /** @return array<string, mixed> */
    public function detail(Course $course, string $type, Model $version): array
    {
        return [
            ...$this->timelineItem($course, $type, $version),
            'fields' => $this->comparison->values($course, $type, $version),
            'content_update' => $version->relationLoaded('contentUpdate') ? $version->contentUpdate : null,
        ];
    }

    public function statusLabel(string $status): string
    {
        return match ($status) {
            'published' => 'Đang xuất bản',
            'superseded' => 'Đã thay thế',
            'draft' => 'Bản nháp',
            'pending' => 'Chờ duyệt',
            'rejected' => 'Bị từ chối',
            default => $status,
        };
    }

    /** @return Collection<int, Model> */
    private function versionsForCourse(Course $course, string $type): Collection
    {
        return match ($type) {
            ContentUpdate::TYPE_COURSE => CourseVersion::query()->with(['course', 'creator', 'publisher', 'sourceVersion', 'contentUpdate.reviewer'])->where('course_id', $course->id)->get(),
            ContentUpdate::TYPE_CHAPTER => CourseSectionVersion::query()->with(['creator', 'publisher', 'sourceVersion', 'contentUpdate.reviewer', 'section' => fn ($query) => $query->withoutGlobalScopes()])
                ->whereIn('course_section_id', CourseSection::withoutGlobalScopes()->where('course_id', $course->id)->select('id'))->get(),
            ContentUpdate::TYPE_LESSON => LessonVersion::query()->with(['creator', 'publisher', 'sourceVersion', 'contentUpdate.reviewer', 'lesson' => fn ($query) => $query->withoutGlobalScopes()])
                ->whereIn('lesson_id', Lesson::withoutGlobalScopes()->where('course_id', $course->id)->select('id'))->get(),
            ContentUpdate::TYPE_ASSIGNMENT => AssignmentVersion::query()->with(['assignment', 'creator', 'publisher', 'sourceVersion', 'contentUpdate.reviewer'])
                ->whereIn('assignment_id', Assignment::query()->where('course_id', $course->id)->select('id'))->get(),
            ContentUpdate::TYPE_QUIZ => QuizVersion::query()->with(['createdBy', 'quiz', 'questionMappings'])
                ->whereIn('quiz_id', Quiz::query()->whereIn('lesson_id', Lesson::withoutGlobalScopes()->where('course_id', $course->id)->select('id'))->select('id'))->get(),
            default => collect(),
        };
    }

    /** @return array<string, mixed> */
    private function timelineItem(Course $course, string $type, Model $version): array
    {
        $identity = $this->identity($type, $version);
        $contentUpdate = method_exists($version, 'contentUpdate') ? $version->contentUpdate : null;
        $isRollback = ($contentUpdate?->metadata['operation_origin'] ?? null) === 'rollback';
        $effectiveStatus = $version->status === 'draft' && $contentUpdate?->isPending() ? 'pending' : $version->status;

        return [
            'type' => $type,
            'type_label' => $this->typeLabel($type),
            'version' => $version,
            'version_number' => $this->number($type, $version),
            'status' => $effectiveStatus,
            'status_label' => $this->statusLabel($effectiveStatus),
            'entity_id' => $this->identityId($type, $version),
            'entity_label' => $version->title ?? $identity?->title ?? $this->typeLabel($type),
            'is_current' => $this->isCurrent($type, $identity, $version),
            'is_archived' => (bool) ($identity?->archived_at ?? false),
            'created_at' => $version->created_at,
            'published_at' => $version->published_at,
            'superseded_at' => $version->superseded_at ?? null,
            'creator_name' => ($type === ContentUpdate::TYPE_QUIZ ? $version->createdBy?->name : $version->creator?->name) ?? 'Không xác định',
            'publisher_name' => ($type === ContentUpdate::TYPE_QUIZ ? null : $version->publisher?->name) ?? 'Không xác định',
            'origin' => $isRollback ? 'Khôi phục phiên bản' : ($contentUpdate ? 'Cập nhật nội dung' : 'Dữ liệu ban đầu'),
            'source_version_number' => $type === ContentUpdate::TYPE_QUIZ ? null : ($version->sourceVersion?->version_number ?? ($contentUpdate?->metadata['source_version_number'] ?? null)),
            'rollback_eligible' => $type !== ContentUpdate::TYPE_QUIZ && $version->status === 'superseded' && ! ($identity?->archived_at ?? false),
        ];
    }

    private function identity(string $type, Model $version): ?Model
    {
        return match ($type) {
            ContentUpdate::TYPE_COURSE => $version->course,
            ContentUpdate::TYPE_CHAPTER => $version->section,
            ContentUpdate::TYPE_LESSON => $version->lesson,
            ContentUpdate::TYPE_ASSIGNMENT => $version->assignment,
            ContentUpdate::TYPE_QUIZ => $version->quiz,
            default => null,
        };
    }

    private function identityId(string $type, Model $version): int
    {
        return (int) match ($type) {
            ContentUpdate::TYPE_COURSE => $version->course_id,
            ContentUpdate::TYPE_CHAPTER => $version->course_section_id,
            ContentUpdate::TYPE_LESSON => $version->lesson_id,
            ContentUpdate::TYPE_ASSIGNMENT => $version->assignment_id,
            ContentUpdate::TYPE_QUIZ => $version->quiz_id,
            default => 0,
        };
    }

    private function isCurrent(string $type, ?Model $identity, Model $version): bool
    {
        $pointer = $type === ContentUpdate::TYPE_QUIZ ? $identity?->current_published_version_id : $identity?->published_version_id;

        return (int) $pointer === (int) $version->id;
    }

    private function number(string $type, Model $version): int
    {
        return (int) ($type === ContentUpdate::TYPE_QUIZ ? $version->version : $version->version_number);
    }

    private function typeLabel(string $type): string
    {
        return match ($type) {
            ContentUpdate::TYPE_COURSE => 'Khóa học',
            ContentUpdate::TYPE_CHAPTER => 'Chương',
            ContentUpdate::TYPE_LESSON => 'Bài học',
            ContentUpdate::TYPE_ASSIGNMENT => 'Bài tập',
            ContentUpdate::TYPE_QUIZ => 'Quiz',
            default => $type,
        };
    }
}
