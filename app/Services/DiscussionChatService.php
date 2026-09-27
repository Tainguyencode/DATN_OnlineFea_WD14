<?php

namespace App\Services;

use App\Models\Discussion;
use App\Models\DiscussionParticipant;
use App\Models\DiscussionReply;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

class DiscussionChatService
{
    /** @return array<string, mixed> */
    public function context(Discussion $discussion, User $viewer): array
    {
        $messages = $this->messages($discussion, $viewer);

        return [
            'conversation_id' => $discussion->id,
            'messages' => $messages['data'],
            'cursor' => $messages['cursor'],
            'messages_url' => route('discussions.messages', $discussion),
            'message_url_template' => route('discussions.message', [$discussion, '__MESSAGE_KEY__']),
            'read_url' => route('discussions.read', $discussion),
            'send_url' => route('discussions.replies.store', $discussion),
        ];
    }

    public function ensureParticipants(Discussion $discussion): void
    {
        $discussion->loadMissing(['course.instructor', 'lesson.course.instructor']);
        $now = now();

        DiscussionParticipant::firstOrCreate(
            ['discussion_id' => $discussion->id, 'user_id' => $discussion->user_id],
            ['role' => 'student', 'last_read_at' => $now]
        );

        $instructor = $discussion->instructor();
        if ($instructor) {
            DiscussionParticipant::where('discussion_id', $discussion->id)
                ->where('role', 'instructor')
                ->where('user_id', '!=', $instructor->id)
                ->delete();
            DiscussionParticipant::firstOrCreate(
                ['discussion_id' => $discussion->id, 'user_id' => $instructor->id],
                ['role' => 'instructor', 'last_read_at' => null]
            );
        }
    }

    public function recordMessage(Discussion $discussion, User $sender, Discussion|DiscussionReply $message): void
    {
        $this->ensureParticipants($discussion);
        $discussion->forceFill([
            'last_message_at' => $message->created_at ?? now(),
            'last_message_user_id' => $sender->id,
        ])->save();

        DiscussionParticipant::where('discussion_id', $discussion->id)
            ->where('user_id', $sender->id)
            ->update(['last_read_at' => now(), 'unread_count' => 0]);
        DiscussionParticipant::where('discussion_id', $discussion->id)
            ->where('user_id', '!=', $sender->id)
            ->increment('unread_count');
    }

    public function refreshLastMessage(Discussion $discussion): void
    {
        $lastReply = $discussion->replies()->latest('created_at')->latest('id')->first();
        $last = $lastReply ?: $discussion;
        $discussion->forceFill([
            'last_message_at' => $last->created_at,
            'last_message_user_id' => $last->user_id,
        ])->save();
    }

    /** @return array<int, int> */
    public function participantIds(Discussion $discussion): array
    {
        $this->ensureParticipants($discussion);

        return $discussion->participants()->pluck('user_id')->map(fn ($id) => (int) $id)->all();
    }

    public function markRead(Discussion $discussion, User $user): void
    {
        $this->ensureParticipants($discussion);
        DiscussionParticipant::where('discussion_id', $discussion->id)
            ->where('user_id', $user->id)
            ->update(['last_read_at' => now(), 'unread_count' => 0]);
    }

    /** @return array{data: array<int, array<string, mixed>>, cursor: ?string} */
    public function messages(Discussion $discussion, User $viewer, ?string $cursor = null): array
    {
        $after = $this->decodeCursor($cursor);
        $discussion->loadMissing(['user', 'lesson']);

        $messages = collect();
        if (! $after || $discussion->created_at->gte($after['at'])) {
            $messages->push($discussion);
        }

        $replies = $discussion->replies()
            ->with(['user', 'lesson', 'replyTo.user', 'replyToDiscussion.user'])
            ->when($after, fn (Builder $query) => $query->where('created_at', '>=', $after['at']))
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $messages = $messages->concat($replies)
            ->sortBy(fn ($message) => $message->created_at->format('Y-m-d H:i:s.u').'|'.$this->messageOrderKey($message))
            ->values();

        if ($after) {
            $messages = $messages->filter(function ($message) use ($after) {
                if ($message->created_at->gt($after['at'])) {
                    return true;
                }

                return $message->created_at->equalTo($after['at'])
                    && strcmp($this->messageOrderKey($message), $this->messageOrderKeyFromCanonical($after['key'])) > 0;
            })->values();
        }

        $data = $messages->map(fn ($message) => $this->presentMessage($discussion, $message, $viewer))->all();
        $last = $messages->last();

        return [
            'data' => $data,
            'cursor' => $last ? $this->encodeCursor($last->created_at, $this->messageKey($last)) : $cursor,
        ];
    }

    public function message(Discussion $discussion, string $key, User $viewer): ?array
    {
        [$kind, $id] = $this->parseMessageKey($key);
        if ($kind === 'discussion') {
            return (int) $discussion->id === $id
                ? $this->presentMessage($discussion, $discussion->loadMissing(['user', 'lesson']), $viewer)
                : null;
        }

        $reply = $discussion->replies()
            ->with(['user', 'lesson', 'replyTo.user', 'replyToDiscussion.user'])
            ->find($id);

        return $reply ? $this->presentMessage($discussion, $reply, $viewer) : null;
    }

    /** @return array<string, mixed> */
    public function presentMessage(Discussion $discussion, Discussion|DiscussionReply $message, User $viewer): array
    {
        $message->loadMissing(['user', 'lesson']);
        $kind = $message instanceof Discussion ? 'discussion' : 'reply';
        $isOwner = (int) $message->user_id === (int) $viewer->id;
        $isAdmin = $viewer->isAdmin();
        $course = $discussion->course ?: $discussion->lesson?->course;
        $isCourseInstructor = $viewer->isInstructor() && $course && (int) $course->instructor_id === (int) $viewer->id;
        $canRecall = ! $message->is_recalled && ($isAdmin || ($isOwner && $message->created_at->gte(now()->subHours(24))));
        $canDelete = $isAdmin || $isOwner || $isCourseInstructor;
        $canMarkHelpful = $message instanceof DiscussionReply
            && $viewer->isStudent()
            && (int) $discussion->user_id === (int) $viewer->id
            && ! $isOwner
            && $message->is_instructor_answer
            && ! $message->is_recalled;

        $replyTo = null;
        if ($message instanceof DiscussionReply) {
            $target = $message->replyTo ?: $message->replyToDiscussion;
            if ($target) {
                $replyTo = [
                    'key' => $this->messageKey($target),
                    'content' => $target->is_recalled ? 'Tin nhắn đã được thu hồi' : $target->content,
                    'is_recalled' => (bool) $target->is_recalled,
                    'sender' => [
                        'id' => $target->user?->id,
                        'name' => $target->user?->name ?? 'Người dùng',
                        'role' => $target->user?->role,
                    ],
                ];
            }
        }

        return [
            'key' => $this->messageKey($message),
            'kind' => $kind,
            'id' => $message->id,
            'conversation_id' => $discussion->id,
            'sender' => [
                'id' => $message->user?->id,
                'name' => $message->user?->name ?? 'Người dùng',
                'avatar_url' => $message->user?->avatarUrl(),
                'role' => $message->user?->role ?? ($message instanceof DiscussionReply && $message->is_instructor_answer ? 'instructor' : 'student'),
            ],
            'content' => $message->is_recalled ? null : $message->content,
            'created_at' => $message->created_at?->toISOString(),
            'lesson' => $message->lesson ? ['id' => $message->lesson->id, 'title' => $message->lesson->title] : null,
            'attachment' => ! $message->is_recalled && $message->attachment_path ? [
                'url' => route('discussion-messages.attachment', ['kind' => $kind, 'message' => $message->id]),
                'name' => $message->attachment_name,
                'type' => $message->attachment_type,
            ] : null,
            'reply_to' => $replyTo,
            'is_recalled' => (bool) $message->is_recalled,
            'is_helpful' => (bool) ($message instanceof DiscussionReply && $message->is_helpful),
            'permissions' => [
                'can_reply' => Gate::forUser($viewer)->allows('reply', $discussion) && ! $message->is_recalled,
                'can_recall' => $canRecall,
                'can_delete' => $canDelete,
                'can_mark_helpful' => $canMarkHelpful,
            ],
            'actions' => [
                'recall_url' => $canRecall ? route($kind === 'discussion' ? 'discussions.recall' : 'discussions.replies.recall', $message) : null,
                'delete_url' => $canDelete ? route($kind === 'discussion' ? 'discussions.destroy' : 'discussions.replies.destroy', $message) : null,
                'helpful_url' => $canMarkHelpful ? route('discussions.replies.toggle-helpful', $message) : null,
            ],
        ];
    }

    public function conversations(User $viewer, int $perPage = 15): LengthAwarePaginator
    {
        $query = Discussion::query()
            ->with([
                'user:id,name,avatar,role',
                'course:id,title,instructor_id',
                'course.instructor:id,name,avatar,role',
                'lastReply.user:id,name,avatar,role',
                'lastReply.lesson:id,title',
            ])
            ->orderByDesc('last_message_at')
            ->orderByDesc('id');

        $this->scopeVisibleConversations($query, $viewer);
        $paginator = $query->paginate(min(max($perPage, 1), 50));
        $unread = $this->unreadCounts($paginator->getCollection()->pluck('id'), $viewer);

        $paginator->setCollection($paginator->getCollection()->map(
            fn (Discussion $discussion) => $this->presentConversation($discussion, $viewer, $unread[$discussion->id] ?? 0)
        ));

        return $paginator;
    }

    public function totalUnread(User $viewer): int
    {
        return (int) DiscussionParticipant::query()
            ->where('user_id', $viewer->id)
            ->whereHas('discussion', function (Builder $query) use ($viewer): void {
                $this->scopeVisibleConversations($query, $viewer);
            })
            ->sum('unread_count');
    }

    /** @return array<int, int> */
    public function unreadCountsFor(Collection $discussionIds, User $viewer): array
    {
        return $this->unreadCounts($discussionIds, $viewer);
    }

    public function pendingInstructorCount(User $instructor): int
    {
        return Discussion::query()
            ->whereHas('course', fn (Builder $query) => $query->where('instructor_id', $instructor->id))
            ->where(function (Builder $query) use ($instructor) {
                $query->whereNull('last_message_user_id')
                    ->orWhere('last_message_user_id', '!=', $instructor->id);
            })
            ->count();
    }

    /** @return array<string, mixed> */
    private function presentConversation(Discussion $discussion, User $viewer, int $unread): array
    {
        $last = $discussion->lastReply ?: $discussion;
        $other = $viewer->isInstructor() ? $discussion->user : $discussion->course?->instructor;

        return [
            'id' => $discussion->id,
            'title' => $other?->name ?? 'Cuộc trao đổi',
            'avatar_url' => $other?->avatarUrl(),
            'role' => $other?->role,
            'course' => $discussion->course ? ['id' => $discussion->course->id, 'title' => $discussion->course->title] : null,
            'last_message' => [
                'key' => $this->messageKey($last),
                'sender_name' => $last->user?->name ?? $discussion->user?->name,
                'content' => $last->is_recalled ? 'Tin nhắn đã được thu hồi' : ($last->content ?: '[Tệp đính kèm]'),
                'created_at' => ($discussion->last_message_at ?: $last->created_at)?->toISOString(),
            ],
            'unread_count' => $unread,
            'messages_url' => route('discussions.messages', $discussion),
            'message_url_template' => route('discussions.message', [$discussion, '__MESSAGE_KEY__']),
            'read_url' => route('discussions.read', $discussion),
            'send_url' => route('discussions.replies.store', $discussion),
        ];
    }

    /** @return array<int, int> */
    private function unreadCounts(Collection $discussionIds, User $viewer): array
    {
        if ($discussionIds->isEmpty()) {
            return [];
        }

        return DiscussionParticipant::query()
            ->whereIn('discussion_id', $discussionIds)
            ->where('user_id', $viewer->id)
            ->pluck('unread_count', 'discussion_id')
            ->map(fn ($count) => (int) $count)
            ->all();
    }

    private function scopeVisibleConversations(Builder $query, User $viewer): void
    {
        if ($viewer->isInstructor()) {
            $query->whereHas('course', fn (Builder $course) => $course->where('instructor_id', $viewer->id));
        } elseif ($viewer->isStudent()) {
            $courseIds = Enrollment::query()
                ->where('user_id', $viewer->id)
                ->withLearningAccess()
                ->select('course_id');
            $query->where('user_id', $viewer->id)->whereIn('course_id', $courseIds);
        } elseif (! $viewer->isAdmin()) {
            $query->whereRaw('1 = 0');
        }
    }

    private function messageKey(Discussion|DiscussionReply $message): string
    {
        return ($message instanceof Discussion ? 'discussion:' : 'reply:').$message->id;
    }

    private function messageOrderKey(Discussion|DiscussionReply $message): string
    {
        return ($message instanceof Discussion ? '0:' : '1:').str_pad((string) $message->id, 20, '0', STR_PAD_LEFT);
    }

    private function messageOrderKeyFromCanonical(string $key): string
    {
        [$kind, $id] = $this->parseMessageKey($key);

        return ($kind === 'discussion' ? '0:' : '1:').str_pad((string) $id, 20, '0', STR_PAD_LEFT);
    }

    /** @return array{0: string, 1: int} */
    private function parseMessageKey(string $key): array
    {
        [$kind, $id] = array_pad(explode(':', $key, 2), 2, null);
        abort_unless(in_array($kind, ['discussion', 'reply'], true) && ctype_digit((string) $id), 404);

        return [$kind, (int) $id];
    }

    private function encodeCursor(Carbon $at, string $key): string
    {
        return rtrim(strtr(base64_encode(json_encode(['at' => $at->toISOString(), 'key' => $key], JSON_THROW_ON_ERROR)), '+/', '-_'), '=');
    }

    /** @return array{at: Carbon, key: string}|null */
    private function decodeCursor(?string $cursor): ?array
    {
        if (! $cursor) {
            return null;
        }

        try {
            $decoded = json_decode(base64_decode(strtr($cursor, '-_', '+/')), true, flags: JSON_THROW_ON_ERROR);

            return ['at' => Carbon::parse($decoded['at']), 'key' => (string) $decoded['key']];
        } catch (\Throwable) {
            abort(422, 'Cursor tin nhắn không hợp lệ.');
        }
    }
}
