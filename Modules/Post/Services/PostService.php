<?php

namespace Modules\Post\Services;

use App\Services\BaseService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Modules\Keyword\Entities\Keyword;
use Modules\Post\Entities\Post;

class PostService extends BaseService
{
    /**
     * PostService constructor.
     */
    public function __construct(Post $post)
    {
        $this->model = $post;
        $this->with = ['postable', 'medias'];
    }

    public function getPosts()
    {
        $data = $this->getData()->getAll('date');

        return $data;
    }

    /**
     * Retrieves a post by the given username.
     *
     * @param  string  $username The username to search for
     */
    public function getPostBy(string $username)
    {
        $isGetByTag = Str::contains(Route::currentRouteName(), ['post.show.tag']);
        $isGetByUsername = Str::contains(Route::currentRouteName(), ['post.show.user']);
        $isGetByKeyword = Str::contains(Route::currentRouteName(), ['post.show.keyword']);

        $query = $this->getData();

        $query
            // Get post by username
            ->when($isGetByUsername, function ($query) use ($username) {
                $query->where('username', $username);
            })
            // Get post by keyword
            ->when($isGetByKeyword, function ($query) use ($username) {
                $keyword = Keyword::firstWhere('name', '=', $username);
                $query->where('postable_id', '=', $keyword->id);
            })
            ->when($isGetByTag, function ($query) use ($username) {
                $hashtags = explode(',', $username);

                foreach ($hashtags as $hashtag) {
                    $query->whereRaw('LOWER(hashtags) LIKE ? ', ['%'.strtolower($hashtag).'%']);
                }
            })
            ->orderBy('date', 'desc');

        return $query->getAll('date');
    }

    public function getLatestPosts(int $projectId)
    {
        return $this->getData(
            projectId: $projectId
        )
            ->select(['id', 'username', 'message', 'date'])
            ->with([])
            ->limit(10)
            ->orderBy('date', 'desc')
            ->get();
    }

    public function getTotalPost(int $projectId)
    {
        return $this
            ->getData(
                projectId: $projectId
            )
            ->select('id')
            ->count();
    }

    public function getSentiments()
    {
        $filterDate = request('sf') ?? '7_days';
        $filterDayNumber = (int) str_replace('_days', '', $filterDate);

        $dates = Collection::times($filterDayNumber, function ($index) use ($filterDayNumber) {
            return Carbon::today()->addDays($index - $filterDayNumber)->toDateString();
        });

        $startDate = $dates->first();
        $endDate = $dates->last();

        $data = $this->select(['stats', 'date'])
            ->whereRaw('date(date) >= ?', [$startDate])
            ->whereRaw('date(date) <= ?', [$endDate])
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item['date']->format('Y-m-d'),
                    'sentiment' => ucfirst($item['stats']['sentiment'] ?? ''),
                ];
            })
            ->groupBy('date');

        $sentiments = $dates
            ->sortByDesc(function ($item) {
                return $item;
            })
            ->map(function ($date) use ($data) {
                if (! isset($data[$date])) {
                    // @phpstan-ignore-next-line
                    return collect(['negative', 'positive', 'neutral'])
                        ->map(function ($sentiment) use ($date): array {
                            return [
                                'date' => $date,
                                'label' => ucfirst($sentiment),
                                'value' => 0,
                            ];
                        });
                }

                /** @var Collection $labelCounts */
                $labelCounts = $data[$date];

                $labelCounts = $labelCounts->countBy('sentiment');
                $labels = $labelCounts->keys()->toArray();
                if (! in_array('positive', $labels)) {
                    $labelCounts->put('positive', 0);
                }
                if (! in_array('neutral', $labels)) {
                    $labelCounts->put('neutral', 0);
                }
                if (! in_array('negative', $labels)) {
                    $labelCounts->put('negative', 0);
                }
                // @phpstan-ignore-next-line
                return $labelCounts->map(function ($count, $sentiment) use ($date): array {
                    return [
                        'date' => $date,
                        'label' => ucfirst($sentiment),
                        'value' => $count,
                    ];
                });
            })
            ->flatten(1)
            ->filter(function ($item) {
                return $item['label'];
            })
            ->values()
            ->toArray();

        return $sentiments;
    }

    /**
     * Retrieves data based on the given start and end dates.
     *
     * @param  string|null  $startDate The start date in YYYY-MM-DD format. If null, it will be retrieved from the request parameters.
     * @param  string|null  $endDate   The end date in YYYY-MM-DD format. If null, it will be retrieved from the request parameters.
     * @return \Modules\Post\Services\PostService The query object used to retrieve the data.
     */
    private function getData(string $startDate = null, string $endDate = null, int $projectId = null)
    {
        $startDate = $startDate ?? request()->get('start_date');
        $endDate = $endDate ?? request()->get('end_date');

        $query = $this
            ->select([
                'id',
                'postable_id',
                'postable_type',
                'post_id',
                'username',
                'name',
                'message',
                'avatar',
                'date',
                'stats',
            ])
            ->when($startDate, function (Builder $query) use ($startDate) {
                $query->where('date', '>=', $startDate);
            })
            ->when($endDate, function (Builder $query) use ($endDate) {
                $query->where('date', '<=', $endDate);
            })
            ->whereHasMorph('postable', [Keyword::class], function ($query) use ($projectId) {
                /** @var \Modules\Auth\Entities\User $user */
                $user = auth()->user();

                if (! $user->isAdmin()) {
                    $query->where('created_by', '=', $user->id);
                }

                $query->where('project_id', '=', $projectId ?? getActiveProjectId('int'));
            });

        return $query;
    }
}
