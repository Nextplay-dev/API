<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WorkflowUpdated implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public array $payload,
    ) {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('workflows'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'WorkflowUpdated';
    }

    public function broadcastWith(): array
    {
        return $this->payload;
    }
}
