<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * PaginationCollection
 *
 * @method string|int total()
 * @method string|int perPage()
 * @method string|int currentPage()
 **/
class PaginationCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection,
            'pagination' => [
                'total' => $this->total(),
                'per_page' => $this->perPage(),
                'current_page' => $this->currentPage(),
            ],
        ];
    }

    public function withResponse($request, $response)
    {
        $originalContent = json_decode($response->getContent(), true);
        unset($originalContent['links'],$originalContent['meta']);
        $response->setData($originalContent);
    }
}
