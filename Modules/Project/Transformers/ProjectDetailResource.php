<?php

namespace Modules\Project\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectDetailResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'latest_keywords' => $this->keywords,
        ];
    }
}
