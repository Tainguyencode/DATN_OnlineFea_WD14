<?php

namespace App\Events;

use App\Models\StudyGroupMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class StudyGroupMessageBroadcasted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    /** @var array<string, mixed> */
    public array $message;

    public function __construct(
        StudyGroupMessage $message,
        public readonly string $action = 'created',
    ) {
        $this->message = $message->toArray();
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('study-group.'.$this->message['study_group_id']);
    }

    public function broadcastAs(): string
    {
        return 'study-group.message.'.$this->action;
    }

    /** @return array{message: array<string, mixed>} */
    public function broadcastWith(): array
    {
        return ['message' => $this->message];
    }
}
