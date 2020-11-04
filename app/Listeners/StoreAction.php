<?php

namespace App\Listeners;

use App\Events\ActionHappened;
use App\Models\Event;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class StoreAction
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ActionHappened  $event
     * @return void
     */
    public function handle(ActionHappened $event)
    {
        $action_title   = $event -> action_title;
        $action_emitter = $event -> action_emitter;
        $action_details = $event -> action_details;
        Event::create([
            'title'     => $action_title,
            'emitter'   => $action_emitter,
            'details'   => $action_details
        ]);
    }
}
