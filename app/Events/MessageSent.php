<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    // القناة الخاصة بين المرسل والمستقبل
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.' . $this->message->sender_id . '.' . $this->message->recipient_id),
            new PrivateChannel('chat.' . $this->message->recipient_id . '.' . $this->message->sender_id),
        ];
    }

    public function broadcastAs()
    {
        return 'message.sent';
    }
}