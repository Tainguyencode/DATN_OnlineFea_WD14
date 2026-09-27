<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseVersion;
use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;

class EnrollmentReleaseBackfillService
{
    /** No snapshots or manifests are invented here. Every decision is reportable. */
    public function inspect(Enrollment $enrollment): array
    {
        $result = ['course' => $enrollment->course_id, 'enrollment' => $enrollment->id, 'selected_version' => null, 'reason' => '', 'unresolved_reason' => ''];
        $unresolved = fn (string $reason) => array_merge($result, ['unresolved_reason' => $reason]);
        $course = Course::find($enrollment->course_id);
        if (! $course) {
            return $unresolved('course_missing');
        }
        if ($enrollment->course_version_id) {
            $version = CourseVersion::find($enrollment->course_version_id);
            if (! $version || (int) $version->course_id !== (int) $course->id || ! $version->manifest_built_at
                || ! in_array($version->status, ['published', 'superseded'], true)) {
                return $unresolved('existing_pin_invalid_manual_review_required');
            }

            return array_merge($result, ['selected_version' => $version->id, 'reason' => 'already_pinned']);
        }
        if (! $course->published_version_id) {
            return $unresolved('legacy_missing_published_pointer');
        }
        $pointer = CourseVersion::find($course->published_version_id);
        if (! $pointer || (int) $pointer->course_id !== (int) $course->id || ! $pointer->isPublished()) {
            return $unresolved('invalid_published_pointer');
        }
        if (! $enrollment->enrolled_at) {
            return $unresolved('missing_enrolled_at');
        }
        // Do not filter out metadata-only snapshots and fall back past them.
        $version = CourseVersion::where('course_id', $course->id)
            ->whereIn('status', ['published', 'superseded'])
            ->whereNotNull('published_at')->where('published_at', '<=', $enrollment->enrolled_at)
            ->orderByDesc('published_at')->orderByDesc('version_number')->first();
        if (! $version) {
            return $unresolved('enrollment_predates_release_history');
        }
        if (! $version->manifest_built_at || $version->manifest_built_at->gt($enrollment->enrolled_at)) {
            return $unresolved('historical_manifest_not_proven');
        }
        if (CourseVersion::where('course_id', $course->id)->where('published_at', $version->published_at)
            ->whereIn('status', ['published', 'superseded'])->count() > 1) {
            return $unresolved('ambiguous_release_timestamp');
        }
        $quizPins = DB::table('quiz_attempts')->join('quizzes', 'quizzes.id', '=', 'quiz_attempts.quiz_id')
            ->join('lessons', 'lessons.id', '=', 'quizzes.lesson_id')
            ->where('lessons.course_id', $course->id)->where('quiz_attempts.user_id', $enrollment->user_id)
            ->pluck('quiz_attempts.quiz_version_id');
        $assignmentPins = DB::table('submissions')->join('assignments', 'assignments.id', '=', 'submissions.assignment_id')
            ->where('assignments.course_id', $course->id)->where('submissions.user_id', $enrollment->user_id)
            ->pluck('submissions.assignment_version_id');
        if ($quizPins->contains(null) || $assignmentPins->contains(null)) {
            return $unresolved('unversioned_assessment_history');
        }
        if ($quizPins->diff($version->lessonMappings()->pluck('quiz_version_id'))->isNotEmpty()
            || $assignmentPins->diff($version->lessonMappings()->pluck('assignment_version_id'))->isNotEmpty()) {
            return $unresolved('assessment_history_conflicts_with_release');
        }

        return array_merge($result, ['selected_version' => $version->id, 'reason' => 'latest_proven_release_at_enrollment']);
    }

    public function run(bool $execute = false, ?callable $report = null, ?int $courseId = null): array
    {
        $totals = ['selected' => 0, 'pinned' => 0, 'already_pinned' => 0, 'unresolved' => 0];
        Enrollment::query()->when($courseId !== null, fn ($query) => $query->where('course_id', $courseId))
            ->orderBy('id')->chunkById(200, function ($enrollments) use ($execute, $report, &$totals): void {
                foreach ($enrollments as $enrollment) {
                    $row = DB::transaction(function () use ($enrollment, $execute): array {
                        if ($execute) {
                            app(CourseReleaseLock::class)->course($enrollment->course_id);
                            $enrollment = Enrollment::lockForUpdate()->findOrFail($enrollment->id);
                        }
                        $row = $this->inspect($enrollment);
                        if ($execute && $row['reason'] === 'latest_proven_release_at_enrollment') {
                            $enrollment->forceFill(['course_version_id' => $row['selected_version']])->save();
                        }

                        return $row;
                    });
                    if ($row['unresolved_reason']) {
                        $totals['unresolved']++;
                    } elseif ($row['reason'] === 'already_pinned') {
                        $totals['already_pinned']++;
                    } else {
                        $totals['selected']++;
                        $totals['pinned'] += (int) $execute;
                    }
                    if ($report) {
                        $report($row);
                    }
                }
            });

        return $totals;
    }
}
