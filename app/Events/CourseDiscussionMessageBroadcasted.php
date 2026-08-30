<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class CourseDiscussionMessageBroadcasted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(
        public int $discussionId,
        public string $action,
        public array $message,
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel("course-discussion.{$this->discussionId}")];
    }

    public function broadcastAs(): string
    {
        return "course-discussion.message.{$this->action}";
    }
}
