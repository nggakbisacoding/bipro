<?php

namespace Modules\Keyword\Services;

use App\Exceptions\GeneralException;
use App\Models\JobBatch;
use App\Services\BaseService;
use Exception;
use Illuminate\Support\Facades\DB;
use Modules\Keyword\Entities\Keyword;
use Modules\Keyword\Events\KeywordCreated;
use Modules\Keyword\Events\KeywordDeleted;
use Modules\Keyword\Events\KeywordUpdated;

/**
 * Class KeywordService.
 */
class KeywordService extends BaseService
{
    /**
     * KeywordService constructor.
     */
    public function __construct(Keyword $keyword)
    {
        $this->model = $keyword;
    }

    /**
     * @throws GeneralException
     * @throws \Throwable
     */
    public function store(array $data = []): Keyword
    {
        DB::beginTransaction();

        try {
            $keyword = $this->createKeyword($data);
        } catch (Exception $e) {
            DB::rollBack();

            throw new GeneralException(__('There was a problem creating this keyword. Please try again.'));
        }

        event(new KeywordCreated($keyword));

        DB::commit();

        return $keyword;
    }

    public function bulkStore(array $data)
    {
        DB::beginTransaction();

        try {
            $keywords = [];

            $lastKeywordId = Keyword::max('id') ?? 0;
            foreach ($data as $keyword) {
                $keywordId = $lastKeywordId + 1;

                $keywords[] = [
                    'id' => $keywordId,
                    ...$this->createRow([
                        'type' => $keyword['type'],
                        'name' => $keyword['username'],
                        'source' => $keyword['source'],
                        'status' => 1,
                        'date' => [
                            carbon($keyword['start_date'])->toDateTimeString(),
                            carbon($keyword['end_date'])->toDateTimeString(),
                        ],
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $lastKeywordId = $keywordId;
            }

            $this->model::insert($keywords);
        } catch (Exception $e) {
            DB::rollBack();

            throw new GeneralException(__('There was a problem creating this keyword. Please try again.'));
        }

        event(new KeywordCreated($keywords));

        DB::commit();
    }

    /**
     * @throws \Throwable
     */
    public function update(Keyword $keyword, array $data = []): Keyword
    {
        DB::beginTransaction();

        try {
            // If previously status is 1 set reserved at to current time + 5 years (hack for pause)
            // If previously status is 0 set reserved at to current time

            $keyword->update([
                'type' => $data['type'],
                'name' => $data['name'],
                'status' => $data['status'] ? 1 : 0,
                'source' => $data['source'],
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            throw new GeneralException(__('There was a problem updating this keyword. Please try again.'));
        }

        event(new KeywordUpdated($keyword));

        DB::commit();

        return $keyword;
    }

    /**
     * @throws GeneralException
     */
    public function delete(Keyword $keyword): Keyword
    {
        if ($this->deleteById($keyword->id)) {
            DB::table('jobs')->where('payload', 'like', "%{$keyword->batch_id}%")->delete();
            JobBatch::where('id', '=', $keyword->batch_id)->delete();

            event(new KeywordDeleted($keyword));

            return $keyword;
        }

        throw new GeneralException('There was a problem deleting this keyword. Please try again.');
    }

    public function getTotalTargetKeyword(int $projectId)
    {
        return $this->select(['id'])->where('project_id', $projectId)->count();
    }

    public function getTotalActiveTargetKeyword(int $projectId)
    {
        return $this->select(['id'])
            ->where('status', true)
            ->where('project_id', $projectId)->count();
    }

    public function getLatestKeywords()
    {
        return $this->select(['id', 'name', 'status', 'type', 'created_at'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    protected function createKeyword(array $data = []): Keyword
    {
        return $this->model::create($this->createRow($data));
    }

    protected function createRow(array $data = []): array
    {
        return [
            'type' => $data['type'],
            'name' => $data['name'] ?? null,
            'source' => $data['source'] ?? null,
            'status' => $data['status'] ?? 0,
            'since' => isset($data['date']) ? head($data['date']) : null,
            'until' => isset($data['date']) ? last($data['date']) : null,
            'project_id' => getActiveProjectId('int'),
        ];
    }
}
