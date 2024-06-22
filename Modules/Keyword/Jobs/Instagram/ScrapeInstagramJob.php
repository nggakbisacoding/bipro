<?php

namespace Modules\Keyword\Jobs\Instagram;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Modules\Keyword\Entities\Keyword;

class ScrapeInstagramJob implements ShouldQueue
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
    public function __construct(
        public Keyword $keyword
    ) {
        $this->onQueue(Keyword::SOURCE_INSTAGRAM);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $keyword = $this->keyword;
        $batch = Bus::batch([
            $this->keyword->type === 'account'
            ? new ScrapeInstagramAccountJob($this->keyword)
            : new ScrapeInstagramTagJob(),
        ])
            ->then(function () use ($keyword) {
                $keyword->update([
                    'is_first' => false,
                ]);
            })
            ->name($this->keyword->type.'-ig-'.$this->keyword->name)
            ->onQueue(Keyword::SOURCE_INSTAGRAM)
            ->dispatch();

        $this->keyword->update([
            'batch_id' => $batch->id,
        ]);
    }
}
