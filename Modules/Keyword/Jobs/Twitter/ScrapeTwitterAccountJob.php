<?php

namespace Modules\Keyword\Jobs\Twitter;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;
use Illuminate\Queue\SerializesModels;
use Modules\Keyword\Entities\Keyword;
use Modules\Keyword\Services\TwitterService;

class ScrapeTwitterAccountJob implements ShouldBeEncrypted, ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     *
     * @var int
     */
    public $maxExceptions = 3;

    public function middleware(): array
    {
        return [new SkipIfBatchCancelled];
    }

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        public Keyword $keyword,
        private string $nextUrl = ''
    ) {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(TwitterService $twitterService)
    {
        $tweets = $twitterService->getTweets(
            keyword: $this->keyword,
            nextUrl: $this->nextUrl
        );

        $posts = $tweets->getPosts();
        $postMedias = $tweets->getPostMedias();

        if (count($posts) > 0) {
            $twitterService->store($posts, $postMedias);
        }

        if ($tweets->getHasMore() && $this->keyword->status) {
            $this->batch()->add(
                new ScrapeTwitterAccountJob($this->keyword, $tweets->getNextUrl())
            );
        }

        // Update last crawled date
        $this->keyword->update([
            'last_crawled' => carbon(now())->format('Y-m-d H:i:s'),
        ]);
    }
}
