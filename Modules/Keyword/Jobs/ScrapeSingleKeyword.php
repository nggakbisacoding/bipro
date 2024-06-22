<?php

namespace Modules\Keyword\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;
// use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InstagramScraper;
use Modules\Keyword\Entities\Keyword;
use Modules\Post\Entities\Post;
use Modules\Post\Entities\PostMedia;
use TiktokScraper;
use TwitterScraper;

class ScrapeSingleKeyword implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     *
     * @var int
     */
    public $maxExceptions = 3;

    /**
     * Indicate if the job should be marked as failed on timeout.
     *
     * @var bool
     */
    public $failOnTimeout = true;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        public Keyword $keyword,
        public bool $isFirst = true,
        private string $nextUrl = '',
    ) {
    }

    public function middleware()
    {
        return [
            (new RateLimited('scrape_keyword'))->dontRelease(),
            // (new WithoutOverlapping($this->keyword->username))->releaseAfter(60)
        ];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->keyword->source === Keyword::SOURCE_TWITTER) {
            $this->_scrapeTwitter();
        } elseif ($this->keyword->source === Keyword::SOURCE_TIKTOK) {
            $this->_scrapeTiktok();
        } elseif ($this->keyword->source === Keyword::SOURCE_INSTAGRAM) {
            $this->_scrapeInstagram();
        }
    }

    public function failed($exception)
    {
        // usually would send new notification to admin/user
        info($exception);
    }

    private function _scrapeTwitter()
    {
        $isKeyword = $this->keyword->type === Keyword::TYPE_KEYWORD;

        if ($isKeyword) {
            $tweets = TwitterScraper::getBySearch($this->keyword->name, $this->nextUrl);
        } else {
            $tweets = TwitterScraper::getByUsernameV2($this->keyword->name, $this->nextUrl);
        }

        if (isset($tweets['data'])) {

            $data = isset($tweets['data']) ? collect($tweets['data'])
                ->when(! $this->keyword->is_first && ! is_null($this->keyword->last_post), function (Collection $post) {
                    return $post->filter(function ($post) {
                        $tweetDate = \Carbon\Carbon::createFromTimestamp($post['tweet_date']);
                        $isNew = $tweetDate->gt($this->keyword->last_post);

                        return $isNew;
                    });
                }) : collect([]);

            $hasMore = $tweets['has_more'] && $data->count() > 0;

            DB::beginTransaction();

            $data->every(function ($tweet, $index) {
                $post = Post::updateOrCreate([
                    'post_id' => 't-'.$tweet['id'],
                    'postable_id' => $this->keyword->id,
                ], [
                    'postable_id' => $this->keyword->id,
                    'postable_type' => get_class($this->keyword),
                    'post_id' => 't-'.$tweet['id'],
                    'name' => $tweet['name'],
                    'username' => $tweet['username'],
                    'message' => $tweet['message'],
                    'hashtags' => collect($tweet['hashtag'])->join(','),
                    'date' => \Carbon\Carbon::createFromTimestamp($tweet['tweet_date'])
                        ->toDateTimeString(),
                    'stats' => [
                        'like' => $tweet['like_count'],
                        'reply' => $tweet['reply_count'],
                        'retweet' => $tweet['retweet_count'],
                        'quote_retweet' => $tweet['quote_retweet_count'],
                        'is_retweet' => $tweet['is_retweet'],
                    ],
                ]);

                // dispatch(new ExtractSentiment($post))
                //     ->onQueue('extract_sentiment')
                //     ->delay($index * rand(10, 15));

                // if ($tweet['attachments'] && count($tweet['attachments']) > 0) {
                //     $isNew = $post->date->gt($this->keyword->last_post);
                //     if ($this->keyword->is_first || $isNew) {
                //         $postMedias = collect($tweet['attachments'])->map(function ($media) use ($post) {
                //             dispatch(
                //                 new DownloadImage(
                //                     TwitterScraper::getMediaUrl($media['url']),
                //                     "twitter/{$this->keyword->name}",
                //                     $media['url']
                //                 )
                //             )->delay(rand(10, 30));

                //             return new PostMedia([
                //                 'post_id' => $post->id,
                //                 'type' => $media['type'],
                //                 'path' => uploadFilename(
                //                     "twitter/{$this->keyword->name}",
                //                     $media['url']
                //                 ),
                //             ]);
                //         });

                //         $post->medias()->saveMany($postMedias);
                //     }
                // }

                return $post;
            });

            DB::commit();

            $nextUrl = $tweets['next_url'];
            if ($hasMore && $this->keyword->status) {
                dispatch(new ScrapeSingleKeyword($this->keyword, false, $nextUrl))
                    ->delay(rand(10, 15));
            } else {
                $latestPostDate = Post::select('date', 'id')
                    ->where('postable_id', $this->keyword->id)
                    ->where('postable_type', get_class($this->keyword))
                    ->limit(1)
                    ->orderBy('date', 'desc')
                    ->first();

                $this->keyword->update([
                    'is_first' => 0,
                    'last_post' => $latestPostDate?->date->format('Y-m-d H:i:s'),
                ]);
            }
        }

    }

    private function _scrapeTiktok()
    {

        $response = TiktokScraper::getByUsername($this->keyword->name, $this->nextUrl);

        if (is_null($response)) {
            throw new \Exception('Response cant be null!');
        }

        if (isset($response['error']) && $response['error']) {
            throw new \Exception($response['message']);
        }

        $data = collect($response['data'])
            ->when(! $this->keyword->is_first, function (Collection $post) {
                return $post->filter(function ($post) {
                    $tweetDate = \Carbon\Carbon::createFromTimestamp($post['date']);
                    $isNew = $tweetDate->gt($this->keyword->last_post);

                    return $isNew;
                });
            });

        $hasMore = $response['has_more'] && $data->count() > 0;

        DB::beginTransaction();

        $data->every(function ($tweet) {
            $post = Post::updateOrCreate([
                'post_id' => 'tiktok-'.$tweet['id'],
                'postable_id' => $this->keyword->id,
            ], [
                'postable_id' => $this->keyword->id,
                'postable_type' => get_class($this->keyword),
                'post_id' => 'tiktok-'.$tweet['id'],
                'name' => $tweet['name'],
                'username' => $tweet['username'],
                'message' => $tweet['message'],
                'hashtags' => collect($tweet['hashtags'])->join(','),
                'date' => \Carbon\Carbon::createFromTimestamp($tweet['date'])
                    ->toDateTimeString(),
                'stats' => [
                    'like' => $tweet['like_count'],
                    'reply' => $tweet['reply_count'],
                    'share' => $tweet['share_count'],
                    'view' => $tweet['view_count'],
                ],
            ]);

            // if ($tweet['attachments'] && count($tweet['attachments']) > 0) {
            //     $isNew = $post->date->gt($this->keyword->last_post);
            //     if ($this->keyword->is_first || $isNew) {
            //         $postMedias = collect($tweet['attachments'])
            //             ->map(function ($media) use ($post) {
            //                 if ($media['type'] === 'video') {
            //                     dispatch(
            //                         new DownloadVideo(
            //                             $media['url'],
            //                             "tiktok/{$this->keyword->name}"
            //                         )
            //                     )->delay(rand(10, 30));
            //                 } elseif ($media['type'] === 'image') {
            //                     dispatch(
            //                         new DownloadImage(
            //                             $media['url'],
            //                             "tiktok/{$this->keyword->name}",
            //                         )
            //                     )->delay(rand(10, 30));
            //                 }

            //                 return new PostMedia([
            //                     'post_id' => $post->id,
            //                     'type' => $media['type'],
            //                     'path' => uploadFilename(
            //                         "tiktok/{$this->keyword->name}",
            //                         $media['url'],
            //                         $media['type'] === 'video' ? 'mp4' : 'jpg'
            //                     ),
            //                 ]);
            //             });

            //         $post->medias()->saveMany($postMedias);
            //     }
            // }

            return $post;
        });

        DB::commit();

        $nextUrl = $response['next_url'];
        if ($hasMore && $this->keyword->status) {
            dispatch(new ScrapeSingleKeyword($this->keyword, false, $nextUrl))
                ->delay(rand(10, 30));
        } else {
            $latestPostDate = Post::select('date', 'id')
                ->where('postable_id', $this->keyword->id)
                ->where('postable_type', get_class($this->keyword))
                ->limit(1)
                ->orderBy('date', 'desc')
                ->first();

            $this->keyword->update([
                'is_first' => 0,
                'last_post' => $latestPostDate?->date->format('Y-m-d H:i:s'),
            ]);
        }
    }

    private function _scrapeInstagram()
    {
        $tweets = InstagramScraper::getByUsername($this->keyword->name, $this->nextUrl);

        if (isset($tweets['error']) && $tweets['error']) {
            // Stop when error
            // Send notification or set status?
            return;
        }

        $data = collect($tweets['data'])
            ->when(! $this->keyword->is_first, function (Collection $post) {
                // Filter out posts that are older than the last post
                return $post->filter(function ($post) {
                    $tweetDate = \Carbon\Carbon::createFromTimestamp($post['date']);
                    $isNew = $tweetDate->gt($this->keyword->last_post);

                    return $isNew;
                });
            });

        $hasMore = $tweets['has_more'] && $data->count() > 0;

        DB::beginTransaction();

        try {
            $data->every(function ($tweet) {
                /** @var Post $post */
                $post = Post::updateOrCreate([
                    'post_id' => 'ig-'.$tweet['id'],
                    'postable_id' => $this->keyword->id,
                ], [
                    'postable_id' => $this->keyword->id,
                    'postable_type' => get_class($this->keyword),
                    'post_id' => 'ig-'.$tweet['id'],
                    'name' => $tweet['name'],
                    'username' => $tweet['username'],
                    'message' => $tweet['message'],
                    'hashtags' => collect($tweet['hashtags'])->join(','),
                    'date' => \Carbon\Carbon::createFromTimestamp($tweet['date'])
                        ->toDateTimeString(),
                    'stats' => [
                        'like' => $tweet['like_count'],
                        'reply' => $tweet['reply_count'],
                    ],
                ]);

                // $isNewAttachment = $post->date->gt($this->keyword->last_post);
                // if ($this->keyword->is_first || $isNewAttachment) {
                //     if ($tweet['is_multi']) {
                //         // Dispatch another job to download all images
                //         dispatch(
                //             new GetInstagramPost(
                //                 $post,
                //                 $tweet,
                //                 $this->keyword->name
                //             )
                //         )->delay(rand(5, 20));
                //     } else {
                //         $dispatchable = $tweet['is_video']
                //             ? new DownloadVideo(
                //                 $tweet['video'],
                //                 "instagram/{$this->keyword->name}",
                //                 $tweet['video']
                //             ) : new DownloadImage(
                //                 $tweet['image'],
                //                 "instagram/{$this->keyword->name}",
                //                 $tweet['image']
                //             );

                //         dispatch($dispatchable)->delay(rand(10, 30));

                //         $mediaPath = uploadFilename(
                //             "instagram/{$this->keyword->name}",
                //             $tweet['is_video'] ? $tweet['video'] : $tweet['image'],
                //             $tweet['is_video'] ? 'mp4' : 'jpg'
                //         );
                //         $post->medias()->updateOrCreate([
                //             'post_id' => $post->id,
                //             'path' => $mediaPath,
                //         ], [
                //             'post_id' => $post->id,
                //             'type' => $tweet['is_video'] ? 'video' : 'image',
                //             'path' => $mediaPath,
                //         ]);
                //     }
                // }

                return $post;
            });
        } catch (\Throwable $th) {
            report($th);
            DB::rollBack();
        }

        DB::commit();

        $nextUrl = $tweets['next_url'];
        if ($hasMore && $this->keyword->status) {
            dispatch(
                new ScrapeSingleKeyword($this->keyword, false, $nextUrl)
            )->delay(rand(10, 30));
        } else {
            $latestPostDate = Post::select('date', 'id')
                ->where('postable_id', $this->keyword->id)
                ->where('postable_type', get_class($this->keyword))
                ->limit(1)
                ->orderBy('date', 'desc')
                ->first();

            $this->keyword->update([
                'is_first' => 0,
                'last_post' => $latestPostDate?->date->format('Y-m-d H:i:s'),
            ]);
        }
    }
}
