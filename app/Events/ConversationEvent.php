<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConversationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $userId;

    /**
     * ConversationEvent constructor.
     * @param $message
     * @param $userId
     */
    public function __construct($message, $userId)
    {
        $this->message = $message;
        $this->userId = $userId;
    }

    /**
     * @return PrivateChannel
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('conversation.' . $this->userId);
    }

    /**
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'conversation-event';
    }
}
