<?php

namespace Modules\Keyword\Jobs\Twitter;

use App\Exceptions\GeneralException;
use Bus;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Keyword\Entities\Keyword;
use TwitterScraper;

class ScrapeTwitterJob implements ShouldBeEncrypted, ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(private mixed $keyword)
    {
        if (! ($this->keyword instanceof Keyword) && ! is_array($this->keyword)) {
            throw new GeneralException('The $keyword parameter must be an instance of Keyword or an array.');
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $dates = TwitterScraper::getPostDates(
            $this->keyword->name,
            $this->keyword->since,
            $this->keyword->until,
            $this->keyword->type
        );

        $keywords = $dates
            ->map(fn ($date) => new ScrapeTwitterAccountJob(
                keyword: $this->keyword,
                nextUrl: $date
            ))
            ->toArray();

        $keyword = $this->keyword;
        $batchName = $this->keyword->name.'-'.Keyword::SOURCE_TWITTER;
        $batch = Bus::batch($keywords)
            ->then(function () use ($keyword) {
                $keyword->update([
                    'is_first' => false,
                ]);
            })
            ->name($batchName)
            ->onQueue(Keyword::SOURCE_TWITTER)
            ->dispatch();

        $this->keyword->update([
            'batch_id' => $batch->id,
        ]);
    }
}
