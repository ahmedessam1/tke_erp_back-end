<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        // STORE THE IMPORTANT ACTIONS THAT HAPPENS
        'App\Events\ActionHappened' => [
            'App\Listeners\StoreAction',
        ],
        'App\Events\TransactionHappened' => [
            'App\Listeners\StoreTransaction',
        ],
        'App\Events\InitiatoryHappened' => [
            'App\Listeners\StoreInitiatory',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
