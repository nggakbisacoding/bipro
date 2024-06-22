<?php

namespace Modules\Keyword\Jobs\Tiktok;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Modules\Keyword\Entities\Keyword;

class ScrapeTiktokJob implements ShouldQueue
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
    public function __construct(private Keyword $keyword)
    {
        //
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
            ? new ScrapeTiktokAccountJob($this->keyword)
            : new ScrapeTiktokTagJob($this->keyword),
        ])
            ->then(function () use ($keyword) {
                $keyword->update([
                    'is_first' => false,
                ]);
            })
            ->name($this->keyword->type.'-tiktok-'.$this->keyword->name)
            ->onQueue(Keyword::SOURCE_TIKTOK)
            ->dispatch();

        $this->keyword->update([
            'batch_id' => $batch->id,
        ]);
    }
}
