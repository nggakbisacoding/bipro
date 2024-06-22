<?php

namespace Modules\Keyword\Transformers;

use App\Http\Resources\PaginationCollection;
use Modules\Keyword\Entities\Keyword;

class KeywordResource extends PaginationCollection
{
    /**
     * Converts the object to an array representation.
     *
     * @param  mixed  $request The request object.
     * @return array The array representation of the object.
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function (Keyword $keyword) {
                return [
                    'id' => $keyword->hashId,
                    'name' => $keyword->name,
                    'type' => ucfirst($keyword->type),
                    'status' => $keyword->status,
                    'source' => ucfirst($keyword->source),
                    'total_post' => $keyword->posts_count,
                    'updated_at' => carbon($keyword->updated_at)->diffForHumans(),
                    'last_crawled' => $keyword->last_crawled
                        ? carbon($keyword->last_crawled)->diffForHumans()
                        : 'Never',
                    'last_post' => is_null($keyword->last_post)
                        ? '-'
                        : carbon($keyword->last_post)->diffForHumans(),
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
