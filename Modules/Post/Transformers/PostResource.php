<?php

namespace Modules\Post\Transformers;

use App\Http\Resources\PaginationCollection;
use Modules\Post\Entities\Post;

class PostResource extends PaginationCollection
{
    /**
     * Converts the object to an array.
     *
     * @param  mixed  $request The request object.
     * @return array The converted array.
     */
    public function toArray($request): array
    {
        return [
            'data' => $this->collection->map(function (Post $post) {
                return [
                    'id' => $post->id,
                    'avatar' => $post->avatar ?? "https://picsum.photos/seed/{$post->username}/150/150",
                    'username' => $post->username,
                    'name' => $post->name,
                    'message' => $post->message,
                    'source' => ucfirst($post->postable->source ?? ''),
                    'stats' => $post->stats,
                    'date' => carbon($post->date)->diffForHumans(),
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
