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
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Modules\Keyword\Entities\Keyword;

class ScrapeTwitterImportedJob implements ShouldBeEncrypted, ShouldQueue
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
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $getProfileJobs = $this->keywords
            ->map(fn ($keyword) => new GetTwitterProfileJob($keyword['name'], $keyword['type']))
            ->toArray();

        $keywords = $this->keywords->toArray();
        Bus::batch($getProfileJobs)
            ->then(function () use ($keywords) {
                dispatch(function () use ($keywords) {
                    Keyword::whereIn('id', array_column($keywords, 'id'))
                        ->orderByDesc('last_post')->chunk(100, function ($keywords) {
                            $keywords->each(function ($keyword) {
                                dispatch(new ScrapeTwitterJob($keyword));
                            });
                        });
                });
            })
            ->onQueue(Keyword::SOURCE_TWITTER)
            ->dispatch();
    }
}
