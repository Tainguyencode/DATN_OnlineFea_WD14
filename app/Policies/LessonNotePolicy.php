<?php

namespace App\Policies;

use App\Models\LessonNote;
use App\Models\User;
use App\Services\LessonNoteAccessService;

class LessonNotePolicy
{
    public function view(User $user, LessonNote $lessonNote): bool
    {
        return $this->ownsNoteWithCurrentAccess($user, $lessonNote);
    }

    public function update(User $user, LessonNote $lessonNote): bool
    {
        return $this->ownsNoteWithCurrentAccess($user, $lessonNote);
    }

    public function delete(User $user, LessonNote $lessonNote): bool
    {
        return $this->ownsNoteWithCurrentAccess($user, $lessonNote);
    }

    private function ownsNoteWithCurrentAccess(User $user, LessonNote $lessonNote): bool
    {
        if (! $user->is_active || ! $user->isStudent() || (int) $lessonNote->user_id !== (int) $user->id) {
            return false;
        }

        $lessonNote->loadMissing('lesson');
        $courseId = app(LessonNoteAccessService::class)->lessonCourseId($lessonNote->lesson);

        return $courseId !== null
            && app(LessonNoteAccessService::class)->userHasLearningAccess($user, $courseId);
    }
}
