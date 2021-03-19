<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $toUserId;

    /**
     * MessageEvent constructor.
     *
     * @param $message
     * @param $toUserId
     */
    public function __construct($message, $toUserId)
    {
        $this->message = $message;
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
        return 'message-event';
    }
}
