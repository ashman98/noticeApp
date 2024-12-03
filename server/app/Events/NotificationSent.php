<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Log;

class NotificationSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    // Channel to which this event will broadcast
    public function broadcastOn()
    {
        Log::info("Broadcasting notification to channel: notifications.1", ['message' => $this->message]);


        return new Channel('notifications.' . 1);
    }

    // Event name
    public function broadcastAs()
    {
        Log::info("Broadcasting event: notification.sent");


        return 'notification.sent';
    }
}
