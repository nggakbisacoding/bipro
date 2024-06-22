<?php

namespace Modules\Keyword\Services;

use App\Exceptions\GeneralException;
use DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use InstagramScraper;
use Modules\Keyword\Entities\Keyword;
use Modules\Keyword\Jobs\DownloadMediaJob;
use Modules\Keyword\Jobs\ExtractSentiment;
use Modules\Keyword\Jobs\GetInstagramPost;
use Modules\Post\Entities\Post;
use Modules\Post\Entities\PostMedia;

/**
 * Class InstagramService.
 */
class InstagramService
{
    public function store(Keyword $keyword, array $response)
    {
        [$posts, $postMedias] = $this->getPost($keyword, $response['data']);

        DB::beginTransaction();

        try {
            if (count($posts)) {
                Post::upsert($posts, [
                    'post_id',
                ], ['stats']);
            }

            if (count($postMedias)) {
                PostMedia::insert($postMedias);
            }
        } catch (\Throwable $th) {
            report($th);
            DB::rollBack();

            throw new GeneralException("Failed to store posts for {$keyword->name}".$th->getMessage());
        }

        DB::commit();

        return [
            'next_url' => $response['next_url'],
            'has_more' => $response['has_more'] && count($response['data']) > 0,
        ];
    }

    public function getPost(Keyword $keyword, array $data)
    {
        $postData = collect($data)
            ->when(! $keyword->is_first, function (Collection $post) use ($keyword) {
                return $post->filter(function ($post) use ($keyword) {
                    $date = \Carbon\Carbon::createFromTimestamp($post['date']);

                    return $date->gt($keyword->last_post);
                });
            });

        $posts = [];
        $postMedias = [];

        $latestPostId = Post::max('id') ?? 0;
        foreach ($postData as $post) {
            $postId = $latestPostId + 1;
            if ($post['avatar']) {

                $filePath = "{$keyword->source}/{$keyword->name}/{$post['avatar']}.jpeg";
                $avatarFilename = uploadFilename($filePath);

                $isExists = Storage::exists($avatarFilename);
                if (! $isExists) {
                    dispatch(new DownloadMediaJob(
                        url: $post['avatar'],
                        path: $avatarFilename,
                    ));
                }
            }

            $posts[] = [
                'id' => $postId,
                'postable_id' => $keyword->id,
                'postable_type' => get_class($keyword),
                'post_id' => 'ig-'.$post['id'],
                'name' => $post['name'],
                'username' => $post['username'],
                'avatar' => isset($avatarFilename) ? basename($avatarFilename) : null,
                'message' => $post['message'],
                'hashtags' => collect($post['hashtags'])->join(','),
                'date' => carbon($post['date'])->toDateTimeString(),
                'stats' => json_encode([
                    'like' => $post['like_count'],
                    'reply' => $post['reply_count'],
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            dispatch(
                new ExtractSentiment($postId, $post['message'])
            );

            $isNewAttachment = carbon($post['date'])->gt($keyword->last_post);
            if ($keyword->is_first || $isNewAttachment) {
                if ($post['is_multi']) {
                    // Dispatch another job to download all images
                    dispatch(new GetInstagramPost(
                        $postId,
                        $post,
                        $keyword->name
                    ));
                } else {
                    $mediaUrl = $post['is_video'] ? $post['video'] : $post['image'];
                    $extension = $post['is_video'] ? 'mp4' : 'jpeg';
                    $filePath = "{$keyword->source}/{$keyword->name}/{$mediaUrl}.{$extension}";

                    $fileName = uploadFilename($filePath);
                    Log::info($fileName);
                    Log::info($filePath);

                    if (! Storage::exists($fileName)) {
                        dispatch(new DownloadMediaJob(
                            url: $mediaUrl,
                            path: $fileName,
                        ));
                    }

                    $postMedias[] = [
                        'post_id' => $postId,
                        'type' => $post['is_video'] ? 'video' : 'image',
                        'path' => basename($fileName),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            $latestPostId = $postId;
        }

        return [$posts, $postMedias];
    }

    public function getLatestPostFromProfile(string $username)
    {
        $posts = InstagramScraper::getByUsername($username);

        if (isset($posts['error']) && $posts['error']) {
            throw new GeneralException("Failed to get posts on instagram for {$username}");
        }

        $post = collect($posts['data'])
            ->sortByDesc('date')
            ->first();

        if (is_null($post)) {
            return null;
        }

        return \Carbon\Carbon::createFromTimestamp($post['date'])
            ->toDateTimeString();
    }

    public function getLatestPostFromTag()
    {
        // on development
    }
}
