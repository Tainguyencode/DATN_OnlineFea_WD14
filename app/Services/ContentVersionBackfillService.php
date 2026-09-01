<?php

namespace App\Services;

use App\Models\Course;

class ContentVersionBackfillService
{
    public function __construct(private readonly ContentVersionService $versions) {}

    /** @return array<string, int> */
    public function backfillPublished(bool $dryRun = false): array
    {
        $result = ['courses_created' => 0, 'courses_skipped' => 0, 'sections_created' => 0, 'sections_skipped' => 0, 'lessons_created' => 0, 'lessons_skipped' => 0, 'assignments_created' => 0, 'assignments_skipped' => 0];

        Course::query()
            ->where(function ($query): void {
                $query->where('is_published', true)
                    ->orWhereIn('status', [Course::STATUS_APPROVED, Course::STATUS_PUBLISHED, Course::STATUS_PENDING_UPDATE, Course::STATUS_REJECTED_UPDATE]);
            })
            ->with(['courseSections.lessons.assignment'])
            ->orderBy('id')
            ->each(function (Course $course) use (&$result, $dryRun): void {
                $this->snapshot($course, 'published_version_id', 'courses', $result, $dryRun, fn () => $this->versions->createInitialCourseVersion($course));
                foreach ($course->courseSections as $section) {
                    $this->snapshot($section, 'published_version_id', 'sections', $result, $dryRun, fn () => $this->versions->createInitialSectionVersion($section));
                    foreach ($section->lessons as $lesson) {
                        $this->snapshot($lesson, 'published_version_id', 'lessons', $result, $dryRun, fn () => $this->versions->createInitialLessonVersion($lesson));
                        if ($lesson->assignment) {
                            $this->snapshot($lesson->assignment, 'published_version_id', 'assignments', $result, $dryRun, fn () => $this->versions->createInitialAssignmentVersion($lesson->assignment));
                        }
                    }
                }
            });

        return $result;
    }

    /** @param array<string, int> $result */
    private function snapshot(object $identity, string $pointer, string $key, array &$result, bool $dryRun, callable $create): void
    {
        if ($identity->{$pointer}) {
            $result[$key.'_skipped']++;

            return;
        }
        $result[$key.'_created']++;
        if (! $dryRun) {
            $create();
        }
    }
}
