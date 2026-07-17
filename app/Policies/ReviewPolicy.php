<?php

namespace App\Policies;

use App\Enums\ReviewStatus;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    public function create(User $user, Course $course): bool
    {
        if (! $user->is_active || ! $user->isStudent() || $course->isOwnedBy($user) || ! $course->isPublished()) {
            return false;
        }

        $enrollment = Enrollment::query()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->withLearningAccess()
            ->first();

        if (! $enrollment) {
            return false;
        }

        if ((float) $enrollment->progress_percent >= (float) config('reviews.minimum_progress_percent', 0.01)
            || $enrollment->last_accessed_at
            || $enrollment->completed_at) {
            return true;
        }

        return LessonProgress::query()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();
    }

    public function update(User $user, Review $review): bool
    {
        return $user->is_active
            && (int) $review->user_id === (int) $user->id
            && $this->hasCurrentEnrollment($user, $review->course_id);
    }

    public function delete(User $user, Review $review): bool
    {
        return $user->is_active && (int) $review->user_id === (int) $user->id;
    }

    public function markHelpful(User $user, Review $review): bool
    {
        return $user->is_active
            && (int) $review->user_id !== (int) $user->id
            && $review->status === ReviewStatus::Approved;
    }

    public function reply(User $user, Review $review): bool
    {
        return $user->is_active
            && $user->isInstructor()
            && $review->course()->where('instructor_id', $user->id)->exists();
    }

    public function moderate(User $user, Review $review): bool
    {
        return $user->hasPermissionTo('course_reviews.moderate');
    }

    public function deleteAsModerator(User $user, Review $review): bool
    {
        return $user->hasPermissionTo('course_reviews.delete');
    }

    private function hasCurrentEnrollment(User $user, int $courseId): bool
    {
        return Enrollment::query()
            ->where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->withLearningAccess()
            ->exists();
    }
}
