<?php

namespace Modules\Keyword\Providers;

use Modules\Keyword\Listeners\KeywordEventListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{

    /**
     * Class event subscribers.
     *
     * @var array
     */
    protected $subscribe = [
        KeywordEventListener::class
    ];
}
