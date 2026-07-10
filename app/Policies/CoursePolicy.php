<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isInstructor();
    }

    public function view(User $user, Course $course): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($course->isOwnedBy($user)) {
            return true;
        }

        return $course->isPublished();
    }

    public function create(User $user): bool
    {
        return $user->isInstructor();
    }

    public function update(User $user, Course $course): bool
    {
        return $course->isOwnedBy($user) && $course->isEditable();
    }

    public function delete(User $user, Course $course): bool
    {
        return $course->isOwnedBy($user);
    }

    public function submit(User $user, Course $course): bool
    {
        return $course->isOwnedBy($user) && $course->isEditable();
    }

    public function review(User $user, Course $course): bool
    {
        return $user->isAdmin();
    }

    public function approve(User $user, Course $course): bool
    {
        return $user->isAdmin();
    }

    public function reject(User $user, Course $course): bool
    {
        return $user->isAdmin();
    }
}
