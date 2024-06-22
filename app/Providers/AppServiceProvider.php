<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // if ($this->app->environment('local')) {
        //     $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
        //     $this->app->register(TelescopeServiceProvider::class);
        // }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! $this->app->environment('local')) {
            \URL::forceScheme('https');
        }
        Model::preventLazyLoading(! app()->isProduction());

        RateLimiter::for('scrape_account', function ($job) {
            return Limit::perMinute(60)->by($job->account->username);
        });
    }
}
