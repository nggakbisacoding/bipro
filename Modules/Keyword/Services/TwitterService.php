<?php

namespace Modules\Keyword\Services;

use App\Exceptions\GeneralException;
use DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Modules\Keyword\Entities\Keyword;
use Modules\Keyword\Jobs\DownloadMediaJob;
use Modules\Keyword\Jobs\ExtractSentiment;
use Modules\Post\Entities\Post;
use Modules\Post\Entities\PostMedia;
use TwitterScraper;

/**
 * Class TwitterService.
 */
class TwitterService
{
    private string $nextUrl = '';

    private bool $hasMore = false;

    private array $posts = [];

    private array $postMedias = [];

    public function store(array $posts, array $postMedias)
    {
        DB::beginTransaction();

        try {
            Post::upsert($posts, ['post_id'], ['stats']);
            PostMedia::insert($postMedias);
        } catch (\Throwable $th) {
            DB::rollBack();

            throw new GeneralException("Failed to store posts with error: {$th->getMessage()}");
        }

        DB::commit();
    }

    public function getTweets(Keyword $keyword, string $nextUrl = '')
    {
        if ($keyword->type !== 'account') {
            $tweets = TwitterScraper::getBySearch($keyword->name, $nextUrl);
        } else {
            $tweets = TwitterScraper::getByUsernameV2($keyword->name, $nextUrl);
        }

        if (isset($tweets['data'])) {
            $data = collect($tweets['data'])
                ->when(! $keyword->is_first, function (Collection $post) use ($keyword) {
                    return $post->filter(function ($post) use ($keyword) {
                        $tweetDate = \Carbon\Carbon::createFromTimestamp($post['tweet_date']);

                        return $tweetDate->gt($keyword->last_post);
                    });
                });

            $this->hasMore = $tweets['has_more'] && $data->count() > 0;
            $this->nextUrl = $tweets['next_url'];

            $this->prepareData($keyword, $data->toArray());
        }

        return $this;
    }

    public function getPosts()
    {
        return $this->posts;
    }

    public function getPostMedias()
    {
        return $this->postMedias;
    }

    public function getHasMore()
    {
        return $this->hasMore;
    }

    public function getNextUrl()
    {
        return $this->nextUrl;
    }

    private function prepareData(Keyword $keyword, array $data)
    {
        $maxId = Post::max('id') ?? 0;
        foreach ($data as $index => $tweet) {
            $postId = $maxId + 1;

            dispatch(new ExtractSentiment(
                $postId, $tweet['message'])
            );

            if ($tweet['avatar']) {

                $avatarFilename = "twitter/{$tweet['username']}/".sha1($tweet['username']).'.jpeg';
                $isExists = Storage::exists($avatarFilename);

                if (! $isExists) {
                    dispatch(new DownloadMediaJob(
                        url: $tweet['avatar'],
                        path: $avatarFilename,
                    ));
                }
            }

            $this->posts[] = [
                'id' => $postId,
                'postable_id' => $keyword->id,
                'postable_type' => get_class($keyword),
                'post_id' => 't-'.$tweet['id'],
                'name' => $tweet['name'],
                'username' => $tweet['username'],
                'avatar' => isset($avatarFilename) ? basename($avatarFilename) : null,
                'message' => $tweet['message'],
                'hashtags' => collect($tweet['hashtag'])->join(','),
                'date' => \Carbon\Carbon::createFromTimestamp($tweet['tweet_date'])
                    ->toDateTimeString(),
                'stats' => json_encode([
                    'like' => $tweet['like_count'],
                    'reply' => $tweet['reply_count'],
                    'retweet' => $tweet['retweet_count'],
                    'quote_retweet' => $tweet['quote_retweet_count'],
                    'is_retweet' => $tweet['is_retweet'],
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $this->postMedias = collect($tweet['attachments'])
                ->map(function ($media, $index) use ($postId, $keyword) {
                    $mediaUrl = TwitterScraper::getMediaUrl($media['url']);
                    $filePath = "{$keyword->source}/{$keyword->name}/{$mediaUrl}";
                    $extension = $media['type'] === 'video' ? 'mp4' : 'jpeg';

                    $fileName = uploadFilename($filePath.'.'.$extension);

                    if (! Storage::exists($fileName)) {
                        dispatch(new DownloadMediaJob(
                            url: $mediaUrl,
                            path: $fileName
                        ));
                    }

                    return [
                        'post_id' => $postId,
                        'type' => $media['type'],
                        'path' => basename($fileName),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                })->toArray();

            $maxId = $postId;
        }
    }
}
