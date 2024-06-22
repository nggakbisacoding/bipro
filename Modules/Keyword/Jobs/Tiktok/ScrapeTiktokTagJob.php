<?php

namespace Modules\Keyword\Jobs\Tiktok;

use App\Exceptions\GeneralException;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;
use Illuminate\Queue\SerializesModels;
use Modules\Keyword\Entities\Keyword;
use Modules\Keyword\Services\TiktokService;
use TiktokScraper;

class ScrapeTiktokTagJob implements ShouldQueue
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
        private Keyword $keyword,
        private string $nextUrl = ''
    ) {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(TiktokService $tiktokService)
    {
        $response = TiktokScraper::getByTag($this->keyword->name, $this->nextUrl);

        if (is_null($response)) {
            throw new GeneralException("Failed to get response from tiktok for {$this->keyword->name}");
        }

        if (isset($response['error']) && $response['error']) {
            throw new GeneralException($response['message']);
        }

        $tiktok = $tiktokService->store($this->keyword, $response);

        if ($tiktok['has_more'] && $this->keyword->status) {
            $this->batch()->add(
                new ScrapeTiktokTagJob(
                    keyword: $this->keyword,
                    nextUrl: $tiktok['next_url']
                )
            );
        }

        $this->keyword->update([
            'last_crawled' => \Carbon\Carbon::now()->format('Y-m-d H:i:s'),
        ]);
    }
}
