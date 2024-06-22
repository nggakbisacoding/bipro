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
use TiktokScraper;

/**
 * Class TiktokService.
 */
class TiktokService
{
    public function store(Keyword $keyword, array $response)
    {
        [$posts, $postMedias] = $this->getPost($keyword, $response['data']);

        DB::beginTransaction();

        try {
            if (count($posts)) {
                DB::table('posts')
                    ->upsert($posts, [
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
                    $tweetDate = \Carbon\Carbon::createFromTimestamp($post['date']);
                    $isNew = $tweetDate->gt($keyword->last_post);

                    return $isNew;
                });
            });

        $posts = [];
        $postMedias = [];

        $latestPostId = Post::max('id') ?? 0;
        foreach ($postData as $post) {
            $postId = $latestPostId + 1;
            if ($post['avatar']) {

                $avatarFilename = "tiktok/{$post['username']}/".sha1($post['username']).'.jpeg';
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
                'post_id' => 'tt-'.$post['id'],
                'name' => $post['name'],
                'username' => $post['username'],
                'avatar' => isset($avatarFilename) ? basename($avatarFilename) : null,
                'message' => $post['message'],
                'hashtags' => collect($post['hashtags'])->join(','),
                'date' => \Carbon\Carbon::createFromTimestamp($post['date'])
                    ->toDateTimeString(),
                'stats' => json_encode([
                    'like' => $post['like_count'],
                    'reply' => $post['reply_count'],
                    'share' => $post['share_count'],
                    'view' => $post['view_count'],
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            dispatch(new ExtractSentiment($postId, $post['message']));

            if ($post['attachments'] && count($post['attachments']) > 0) {
                $isNew = carbon($post['date'])->gt($keyword->last_post);
                if ($keyword->is_first || $isNew) {
                    foreach ($post['attachments'] as $media) {
                        $mediaUrl = $media['url'];
                        $filePath = "{$keyword->source}/{$keyword->name}/{$mediaUrl}";
                        $extension = $media['type'] === 'video' ? 'mp4' : 'jpeg';

                        $fileName = uploadFilename($filePath.'.'.$extension);

                        if (! Storage::exists($fileName)) {
                            dispatch(new DownloadMediaJob(
                                url: $mediaUrl,
                                path: $fileName,
                            ));
                        }

                        $postMedias[] = [
                            'post_id' => $postId,
                            'type' => $media['type'],
                            'path' => basename($fileName),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            }

            $latestPostId = $postId;
        }

        return [$posts, $postMedias];
    }

    public function getLatestPostFromProfile(string $username)
    {
        $posts = TiktokScraper::getByUsername($username);
        $post = collect($posts['data'])
            ->sortByDesc('date')
            ->first();

        if (is_null($post)) {
            return null;
        }

        return \Carbon\Carbon::createFromTimestamp($post['date'])
            ->toDateTimeString();
    }

    public function getLatestPostFromTag(string $tag)
    {
        $posts = TiktokScraper::getByTag($tag);
        $post = collect($posts['data'])
            ->sortByDesc('date')
            ->first();

        if (is_null($post)) {
            return null;
        }

        return \Carbon\Carbon::createFromTimestamp($post['date'])
            ->toDateTimeString();
    }
}
