<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use phpDocumentor\Reflection\Type;

class NewUser
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    const TYPE_OFFICIAL = '微信公众号';

    public $type;
    public $params;

    /**
     * Create a new event instance.
     * @param $type
     * @param $params
     * @return void
     */
    public function __construct($type, $params)
    {
        $this->type   = $type;
        $this->params = $params;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
