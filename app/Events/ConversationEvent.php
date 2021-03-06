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

    public $conversation;
    public $fromUser;
    public $toUserId;

    /**
     * ConversationEvent constructor.
     *
     * @param $conversation
     * @param $fromUser
     * @param $toUserId
     */
    public function __construct($conversation, $fromUser, $toUserId)
    {
        $this->conversation = $conversation;
        $this->fromUser = $fromUser;
        $this->toUserId = $toUserId;
    }

    /**
     * @return PrivateChannel
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('conversation.' . $this->toUserId);
    }

    /**
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'conversation-event';
    }
}
