<?php

namespace Modules\Keyword\Jobs\Twitter;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Keyword\Entities\Keyword;
use TwitterScraper;

class GetTwitterProfileJob implements ShouldBeEncrypted, ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 2;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(private string $username, private string $type)
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
        if ($this->batch()->cancelled()) {
            Log::info("Cancelled get profile for {$this->username}");

            return;
        }

        if ($this->type === 'account') {
            $profile = TwitterScraper::getProfile(
                username: $this->username,
            );
            $lastPostDate = \Carbon\Carbon::createFromTimestamp($profile['latest_post_at'])
                ->toDateTimeString();
        } else {
            $tweets = TwitterScraper::getBySearch(
                keyword: $this->username,
            );

            $lastPostDate = collect($tweets['data'])
                ->sortByDesc('tweet_date')
                ->first()['tweet_date'] ?? null;

            if (! is_null($lastPostDate)) {
                $lastPostDate = \Carbon\Carbon::createFromTimestamp($lastPostDate)
                    ->toDateTimeString();
            }
        }

        if (! is_null($lastPostDate)) {
            Keyword::whereName($this->username)
                ->whereSource(Keyword::SOURCE_TWITTER)
                ->first()
                ->update([
                    'last_post' => $lastPostDate,
                ]);
        }
    }
}
