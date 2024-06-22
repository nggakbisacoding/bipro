<?php

namespace Modules\Project\Transformers;

use App\Http\Resources\PaginationCollection;
use Modules\Project\Entities\Project;

class ProjectResource extends PaginationCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function (Project $project) {
                return [
                    'id' => $project->hashId,
                    'name' => $project->name,
                    'is_complete' => $project->is_complete,
                    'created_at' => carbon($project->created_at)->diffForHumans(),
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
