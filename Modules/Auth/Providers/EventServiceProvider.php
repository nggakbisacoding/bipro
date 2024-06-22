<?php

namespace Modules\Auth\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Modules\Auth\Listeners\RoleEventListener;
use Modules\Auth\Listeners\UserEventListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Class event subscribers.
     *
     * @var array
     */
    protected $subscribe = [
        RoleEventListener::class,
        UserEventListener::class
    ];
}
