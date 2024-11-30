<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class BRTNotification implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $brt;

    /**
     * Create a new event instance.
     *
     * @param $brt
     */
    public function __construct($brt)
    {
        $this->brt = $brt;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('brt-updates');
    }

    /**
     * The data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        
        return [
            'brt' => $this->brt
        ];
    }
}
