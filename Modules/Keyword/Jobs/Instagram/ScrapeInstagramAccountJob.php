<?php

namespace Modules\Keyword\Jobs\Instagram;

use App\Exceptions\GeneralException;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;
use Illuminate\Queue\SerializesModels;
use InstagramScraper;
use Modules\Keyword\Entities\Keyword;
use Modules\Keyword\Services\InstagramService;

class ScrapeInstagramAccountJob implements ShouldQueue
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
        public Keyword $keyword,
        private string $nextUrl = ''
    ) {
        $this->onQueue(Keyword::SOURCE_INSTAGRAM);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(InstagramService $instagramService)
    {
        // TODO:
        // - add crawler for hashtag
        $response = InstagramScraper::getByUsername($this->keyword->name, $this->nextUrl);

        if (is_null($response)) {
            throw new GeneralException("Failed to get response from instagram for {$this->keyword->name}");
        }

        if (isset($response['error']) && $response['error']) {
            // Stop when error
            // Send notification or set status?
            return;
        }

        $instagram = $instagramService->store($this->keyword, $response);

        if ($instagram['has_more'] && $this->keyword->status) {
            $this->batch()->add(
                new ScrapeInstagramAccountJob(
                    keyword: $this->keyword,
                    nextUrl: $instagram['next_url']
                )
            );
        }

        $this->keyword->update([
            'last_crawled' => \Carbon\Carbon::now()->format('Y-m-d H:i:s'),
        ]);
    }
}
