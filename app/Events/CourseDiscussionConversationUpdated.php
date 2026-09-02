<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class CourseDiscussionConversationUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    /** @param array<int, int> $userIds */
    public function __construct(
        private array $userIds,
        public int $conversationId,
        public string $action,
        public ?string $messageKey = null,
    ) {}

    public function broadcastOn(): array
    {
        return array_map(fn (int $id) => new PrivateChannel("App.Models.User.{$id}"), $this->userIds);
    }

    public function broadcastAs(): string
    {
        return 'course-conversation.updated';
    }
}
