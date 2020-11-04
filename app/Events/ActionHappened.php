<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ActionHappened
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $action_title, $action_details, $action_emitter;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($action_title, $action_details, $action_emitter)
    {
        $this -> action_title   = $action_title;
        $this -> action_details = $action_details;
        $this -> action_emitter = $action_emitter;
    }
}
