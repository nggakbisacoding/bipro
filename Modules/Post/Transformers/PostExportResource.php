<?php

namespace Modules\Post\Transformers;

use App\Http\Resources\PaginationCollection;
use Modules\Post\Entities\PostExport;

class PostExportResource extends PaginationCollection
{
    public function toArray($request): array
    {
        return [
            'data' => $this->collection->map(function (PostExport $postExport) {
                $status = 'pending';
                $statusColor = 'grey';

                if (! is_null($postExport->jobBatch)) {

                    if (is_null($postExport->jobBatch->hasPendingJobs())) {
                        $status = 'processing';
                    }

                    if ($postExport->jobBatch->finished() && $postExport->jobBatch->failed()) {
                        $status = 'failed';
                        $statusColor = 'red';
                    }

                    if ($postExport->jobBatch->finished() and $postExport->jobBatch->hasFailures()) {
                        $status = 'finish_with_error';
                        $statusColor = 'yellow';
                    }

                    if ($postExport->jobBatch->finished()) {
                        $status = 'finish';
                        $statusColor = 'green';
                    }
                }

                return [
                    'id' => $postExport->hashId,
                    'name' => $postExport->name,
                    'path' => $postExport->path,
                    'progress' => $postExport->jobBatch?->progress() ?? 0,
                    'status' => $status,
                    'status_color' => $statusColor,
                    'created_at' => carbon($postExport->created_at)->format('Y-m-d H:i:s'),
                    'url' => route('admin.post.export.download', $postExport->hashId),
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
