<?php

namespace Modules\Project\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Project\Listeners\ProjectListener;

class ProjectEventServiceProvider extends ServiceProvider
{
    /**
     * Class event subscribers.
     *
     * @var array
     */
    protected $subscribe = [
        ProjectListener::class,
    ];
}
