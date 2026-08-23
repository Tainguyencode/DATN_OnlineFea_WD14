<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\Discussion;
use App\Models\Enrollment;
use App\Models\User;

class DiscussionPolicy
{
    /**
     * Determine whether the user can view the discussion.
     */
    public function view(User $user, Discussion $discussion): bool
    {
        if (! $user->is_active) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        $courseId = $discussion->course_id ?: $discussion->lesson?->course_id;
        $instructorId = $discussion->course?->instructor_id ?: $discussion->lesson?->course?->instructor_id;

        // If instructor, must own the course
        if ($user->isInstructor()) {
            return (int) $instructorId === (int) $user->id;
        }

        // If student, must own the discussion and be enrolled in the course
        if ($user->isStudent()) {
            if ((int) $discussion->user_id !== (int) $user->id) {
                return false;
            }

            return Enrollment::where('user_id', $user->id)
                ->where('course_id', $courseId)
                ->withLearningAccess()
                ->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can create discussions.
     */
    public function create(User $user, Course $course): bool
    {
        if (! $user->is_active) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        // Only student enrolled in the course can ask a question
        if ($user->isStudent()) {
            return Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->withLearningAccess()
                ->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can reply to the discussion.
     */
    public function reply(User $user, Discussion $discussion): bool
    {
        if (! $user->is_active) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        $courseId = $discussion->course_id ?: $discussion->lesson?->course_id;
        $instructorId = $discussion->course?->instructor_id ?: $discussion->lesson?->course?->instructor_id;

        // If instructor, must own the course
        if ($user->isInstructor()) {
            return (int) $instructorId === (int) $user->id;
        }

        // If student, must own the discussion and be enrolled
        if ($user->isStudent()) {
            if ((int) $discussion->user_id !== (int) $user->id) {
                return false;
            }

            return Enrollment::where('user_id', $user->id)
                ->where('course_id', $courseId)
                ->withLearningAccess()
                ->exists();
        }

        return false;
    }
}
