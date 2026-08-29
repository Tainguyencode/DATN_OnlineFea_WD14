<?php

use App\Models\StudyGroup;
use App\Models\Discussion;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('course-discussion.{discussionId}', function ($user, int $discussionId): bool {
    $discussion = Discussion::with('course')->find($discussionId);
    if (! $discussion) return false;

    return $user->role === 'admin'
        || (int) $discussion->user_id === (int) $user->id
        || (int) $discussion->course?->instructor_id === (int) $user->id;
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
