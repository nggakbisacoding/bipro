<?php

namespace Modules\Keyword\Jobs\Instagram;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Modules\Keyword\Entities\Keyword;

class ScrapeInstagramImportedJob implements ShouldQueue
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
    public function __construct(private Collection $keywords)
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
        $getProfileJobs = $this->keywords
            ->map(fn ($keyword) => $keyword['type'] === 'account'
                ? new ScrapeInstagramProfileJob($keyword['name'])
                : new ScrapeInstagramTagJob())
            ->toArray();

        $keywords = $this->keywords->toArray();
        Bus::batch($getProfileJobs)
            ->then(function () use ($keywords) {
                dispatch(function () use ($keywords) {
                    Keyword::whereIn('id', array_column($keywords, 'id'))
                        ->where('source', '=', Keyword::SOURCE_INSTAGRAM)
                        ->orderByDesc('last_post')->chunk(100, function ($keywords) {
                            $keywords->each(function ($keyword) {
                                dispatch(new ScrapeInstagramJob($keyword));
                            });
                        });
                });
            })
            ->name('keyword.instagram.imported')
            ->onQueue(Keyword::SOURCE_INSTAGRAM)
            ->dispatch();
    }
}
