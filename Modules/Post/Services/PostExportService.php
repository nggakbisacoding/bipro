<?php

namespace Modules\Post\Services;

use App\Exceptions\GeneralException;
use App\Services\BaseService;
use Bus;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Post\Entities\PostExport;
use Modules\Post\Jobs\PostBatchExportJob;

class PostExportService extends BaseService
{
    /**
     * PostExportService constructor.
     */
    public function __construct(PostExport $postExport)
    {
        $this->model = $postExport;
    }

    /**
     * Stores the given data and returns a PostExport object.
     *
     * @param  array  $data the data to be stored
     * @return PostExport the created PostExport object
     *
     * @throws GeneralException when there is a problem exporting the keyword
     */
    public function store(array $data): PostExport
    {
        DB::beginTransaction();

        try {
            $filename = 'posts_'.now()->format('Y_m_d_His');
            $filePath = sha1($filename).'.csv';
            $batch = Bus::batch([
                new PostBatchExportJob(
                    data: $data,
                    user: auth()->user(),
                    projectId: getActiveProjectId('int')
                ),
            ])
                ->then(function (Batch $batch) use ($filePath) {
                    $targetDirectory = storage_path('framework/cache/exports');
                    $targetFilename = $targetDirectory.DIRECTORY_SEPARATOR.$batch->id.'.csv';
                    rename($targetFilename, Storage::path('exports/'.$filePath));
                })
                ->dispatch();

            $export = $this->model::create([
                'name' => $filename,
                'path' => $filePath,
                'batch_id' => $batch->id,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            throw new GeneralException(__('There was a problem exporting this keyword. Please try again.'));
        }

        DB::commit();

        return $export;
    }

    public function getDownloadPath(string $hashId): array
    {
        $postExport = $this->getByHashId($hashId);
        $filePath = storage_path('app/exports/'.$postExport->path);

        return [$postExport->name.'.csv', $filePath];
    }
}
