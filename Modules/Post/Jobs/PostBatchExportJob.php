<?php

namespace Modules\Post\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Modules\Auth\Entities\User;
use Modules\Keyword\Entities\Keyword;
use Modules\Post\Services\PostService;

class PostBatchExportJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Initializes a new instance of the class.
     *
     * @param  array  $data The data to be passed to the constructor.
     */
    public function __construct(
        private array $data,
        private User $user,
        private string $projectId
    ) {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(PostService $postService)
    {
        if ($this->batch()->cancelled()) {
            // Determine if the batch has been cancelled...

            return;
        }

        $startDate = $this->data['start_date'];
        $endDate = $this->data['end_date'];
        $usernames = $this->data['username'] ?? [];
        $sources = $this->data['source'] ?? [];
        $sentiments = $this->data['sentiment'] ?? [];

        $postService->select([
            'id',
            'postable_id',
            'postable_type',
            'post_id',
            'username',
            'name',
            'message',
            'date',
            'stats',
        ])
            ->when($startDate, function (Builder $query) use ($startDate) {
                $query->where('date', '>=', $startDate);
            })
            ->when($endDate, function (Builder $query) use ($endDate) {
                $query->where('date', '<=', $endDate);
            })
            ->when(count($usernames) > 0, function (Builder $query) use ($usernames) {
                $query->whereIn('username', $usernames);
            })
            ->when(count($sources) > 0, function (Builder $query) use ($sources) {
                $query->whereHasMorph('postable', [Keyword::class], function (Builder $query) use ($sources) {
                    $query->whereIn('source', $sources);
                });
            })
            ->when(count($sentiments) > 0, function (Builder $query) use ($sentiments) {
                $query->whereIn('stats->sentiment', $sentiments);
            })
            ->whereHasMorph('postable', [Keyword::class], function ($query) {
                if (! $this->user->isAdmin()) {
                    $query->where('created_by', '=', $this->user->id);
                }

                $query->where('project_id', '=', $this->projectId);
            })
            ->chunk(500, function (Collection $posts) {
                $this->batch()->add(new PostExportJob($posts->toArray()));
            });

    }
}
