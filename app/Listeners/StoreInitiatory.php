<?php

namespace App\Listeners;

use App\Events\InitiatoryHappened;
use App\Models\InitiatoryEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class StoreInitiatory
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
     * @param  InitiatoryHappened  $event
     * @return void
     */
    public function handle(InitiatoryHappened $event)
    {
        $action_data   = $event -> data;

        InitiatoryEvent::create([
            'initiatory_type_id' => $action_data['initiatory_type_id'],
            'description'        => $action_data['description'],
            'model_type'         => $action_data['model_type'],
            'model_id'           => $action_data['model_id'],
            'created_by'         => $action_data['created_by'],
        ]);
    }
}
