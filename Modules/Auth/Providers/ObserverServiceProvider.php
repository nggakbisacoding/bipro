<?php

namespace Modules\Auth\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Auth\Entities\User;
use Modules\Auth\Observers\UserObserver;

class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        User::observe(UserObserver::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
