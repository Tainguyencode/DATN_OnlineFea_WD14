<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseVersion;
use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EnrollmentVersionService
{
    /** The caller must keep this lock through enrollment insertion. */
    public function resolvePublishedVersionForEnrollment(Course $course): CourseVersion
    {
        if (DB::transactionLevel() < 1) {
            throw new \LogicException('Release selection requires an enrollment transaction.');
        }
        $course = app(CourseReleaseLock::class)->course($course->id);
        $version = $course->published_version_id ? CourseVersion::query()->lockForUpdate()->find($course->published_version_id) : null;
        if (! $version || (int) $version->course_id !== (int) $course->id
            || ! $version->isPublished() || ! $version->published_at || ! $version->manifest_built_at) {
            throw ValidationException::withMessages(['course_version' => 'Khóa học chưa có bản phát hành hợp lệ để ghi danh.']);
        }

        return $version;
    }

    /** Existing rows, including cancelled/legacy enrollments, keep their pin. */
    public function firstOrCreate(Course $course, int $userId, array $attributes = []): Enrollment
    {
        return DB::transaction(function () use ($course, $userId, $attributes): Enrollment {
            $course = app(CourseReleaseLock::class)->course($course->id);
            $existing = Enrollment::query()->where('course_id', $course->id)->where('user_id', $userId)->lockForUpdate()->first();
            if ($existing) {
                return $existing;
            }
            $version = $this->resolvePublishedVersionForEnrollment($course);

            return Enrollment::create(array_merge([
                'status' => Enrollment::STATUS_ACTIVE, 'progress_percent' => 0, 'enrolled_at' => now(),
            ], $attributes, ['user_id' => $userId, 'course_id' => $course->id, 'course_version_id' => $version->id]));
        });
    }

    public function resolveEnrollmentRelease(Enrollment $enrollment): CourseVersion
    {
        $release = $enrollment->courseVersion;
        if (! $release || (int) $release->course_id !== (int) $enrollment->course_id
            || ! in_array($release->status, ['published', 'superseded'], true) || ! $release->manifest_built_at) {
            throw ValidationException::withMessages(['course_version' => 'Enrollment release is unresolved.']);
        }

        return $release->loadMissing(['sectionMappings.sectionVersion', 'lessonMappings.lessonVersion', 'lessonMappings.quizVersion.questionMappings.questionVersion', 'lessonMappings.assignmentVersion']);
    }
}
