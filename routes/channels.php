<?php

use App\Models\Discussion;
use App\Models\StudyGroup;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Gate;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('course-discussion.{discussionId}', function ($user, int $discussionId): bool {
    $discussion = Discussion::with(['course', 'lesson.course'])->find($discussionId);
    if (! $discussion) {
        return false;
    }

    return Gate::forUser($user)->allows('view', $discussion);
});

Broadcast::channel('study-group.{studyGroupId}', function ($user, int $studyGroupId): bool {
    if ($user->role === 'admin') {
        return true;
    }

    return StudyGroup::query()
        ->whereKey($studyGroupId)
        ->whereHas('members', fn ($query) => $query->whereKey($user->id))
        ->exists();
});
