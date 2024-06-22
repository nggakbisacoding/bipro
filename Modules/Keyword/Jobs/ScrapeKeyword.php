<?php

namespace Modules\Keyword\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Keyword\Entities\Keyword;
use TwitterScraper;

class ScrapeKeyword implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     *
     * @var int
     */
    public $maxExceptions = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $keyword = Keyword::where('last_post', '!=', null)
            ->whereSource('twitter')
            ->first();

        $dates = TwitterScraper::getPostDates(
            $keyword->name,
            $keyword->last_post,
            null,
            $keyword->type
        );
        dump($dates);
        // TwitterScraper::getByUsernameV2($keywords);

        // if (! is_null($this->keyword)) {
        //     $dates = TwitterScraper::getPostDates(
        //         $this->keyword->name,
        //         $this->keyword->since,
        //         $this->keyword->until,
        //         $this->keyword->type
        //     );
        //     $keywords = $dates->map(function ($chunk) {
        //         return new ScrapeSingleKeyword($this->keyword, true, $chunk);
        //     });
        // } else {
        //     // run multiple ScrapeSingleKeyword job
        //     // read all keyword with criteria active, not first time, not deleted
        //     $keywords = Keyword::where('is_first', 0)
        //         ->where('status', 1)
        //         ->where('last_post', '!=', null)
        //         ->get();
        //     $keywords = $keywords->map(function ($chunk) {
        //         $delayTime = rand(10, 15);

        //         return (new ScrapeSingleKeyword($chunk, true, ''))
        //             ->delay($delayTime);
        //     });
        // }
    }
}
