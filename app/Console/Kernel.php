<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Modules\Keyword\Jobs\ScrapeKeyword;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->job(new ScrapeKeyword)
        //     ->name('monitor keyword')
        //     // ->everyTenMinutes()
        //     ->everyFiveSeconds();
        $schedule->command('horizon:snapshot')->everyFiveMinutes();

        $schedule->command('queue:retry', [
            'all',
        ])
            ->name('Run Retry Queue')
            ->everyThirtyMinutes();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
