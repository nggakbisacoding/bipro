<?php

namespace Modules\Keyword\Jobs\Tiktok;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;
use Illuminate\Queue\SerializesModels;
use Modules\Keyword\Entities\Keyword;
use Modules\Keyword\Services\TiktokService;

class ScrapeTiktokProfileJob implements ShouldQueue
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
    public function __construct(private string $username)
    {
        $this->onQueue(Keyword::SOURCE_TIKTOK);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(TiktokService $tiktokService)
    {
        $lastPostDate = $tiktokService->getLatestPostFromProfile($this->username);
        Keyword::whereName($this->username)
            ->whereSource(Keyword::SOURCE_TIKTOK)
            ->first()
            ->update([
                'last_post' => $lastPostDate,
            ]);
    }
}
