<?php

namespace App\Policies;

use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonComment;
use App\Models\User;

class LessonCommentPolicy
{
    /**
     * Determine whether the user can create a comment on the lesson.
     */
    public function create(User $user, Lesson $lesson): bool
    {
        if (in_array($lesson->type, ['quiz', 'assignment'], true)) {
            return false;
        }

        if (! $user->is_active) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isInstructor() && (int) $lesson->course->instructor_id === (int) $user->id) {
            return true;
        }

        if ($user->isStudent()) {
            return Enrollment::where('user_id', $user->id)
                ->where('course_id', $lesson->course_id)
                ->withLearningAccess()
                ->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can reply to a specific comment.
     */
    public function reply(User $user, LessonComment $comment): bool
    {
        if ($comment->is_hidden) {
            return false;
        }

        return $this->create($user, $comment->lesson);
    }

    /**
     * Determine whether the user can toggle hide on a comment.
     */
    public function toggleHide(User $user, LessonComment $comment): bool
    {
        if (! $user->is_active) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isInstructor() && (int) $comment->lesson->course->instructor_id === (int) $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the comment.
     */
    public function update(User $user, LessonComment $comment): bool
    {
        if (! $user->is_active) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return (int) $comment->user_id === (int) $user->id;
    }

    /**
     * Determine whether the user can delete the comment.
     */
    public function delete(User $user, LessonComment $comment): bool
    {
        if (! $user->is_active) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ((int) $comment->user_id === (int) $user->id) {
            return true;
        }

        if ($user->isInstructor() && (int) $comment->lesson->course->instructor_id === (int) $user->id) {
            return true;
        }

        return false;
    }
}
