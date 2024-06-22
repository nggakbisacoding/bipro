<?php

namespace Modules\Keyword\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class KeywordDetailResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request)
    {
        return [
            'id' => $this->hashId,
            'name' => $this->name,
            'type' => $this->type,
            'status' => $this->status,
            'source' => $this->source,
            'date' => [$this->since, $this->until],
        ];
    }
}
