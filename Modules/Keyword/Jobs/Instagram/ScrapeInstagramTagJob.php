<?php

namespace Modules\Keyword\Jobs\Instagram;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;
use Illuminate\Queue\SerializesModels;
use Modules\Keyword\Entities\Keyword;

class ScrapeInstagramTagJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function middleware(): array
    {
        return [new SkipIfBatchCancelled];
    }

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->onQueue(Keyword::SOURCE_INSTAGRAM);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Search using tag on instagram not yet supported
    }
}
