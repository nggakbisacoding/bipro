<?php

namespace Modules\Post\Transformers;

use App\Http\Resources\PaginationCollection;
use Modules\Post\Entities\Post;

class PostDetailResource extends PaginationCollection
{
    /**
     * Converts the collection to an array.
     *
     * @param  mixed  $request The request parameter.
     * @return array The array representation of the collection.
     */
    public function toArray($request): array
    {
        return [
            'data' => $this->collection->map(function (Post $post) {
                $attachments = $post->medias ?? collect([]);
                $attachments = $attachments->map(function ($media) {
                    return [
                        'id' => $media->id,
                        'path' => $media->path,
                    ];
                });

                return [
                    'id' => $post->hashId,
                    'message' => $post->message,
                    'source' => ucfirst($post->postable->source ?? ''),
                    'stats' => $post->stats,
                    'date' => carbon($post->date)->diffForHumans(),
                    'attachments' => $attachments,
                    'link' => getOriginalUrl(
                        $post->postable->source,
                        $post->username,
                        $post->post_id
                    ),
                    'user' => [
                        'name' => $post->name,
                        'username' => $post->username,
                        'avatar' => $post->avatar,
                        'link' => getProfileLink(
                            source: $post->postable->source,
                            username: $post->username
                        ),
                    ],
                ];
            }),
            'pagination' => [
                'total' => $this->total(),
                'per_page' => $this->perPage(),
                'current_page' => $this->currentPage(),
            ],
        ];
    }
}
