<?php

namespace Modules\Project\Services;

use App\Exceptions\GeneralException;
use App\Services\BaseService;
use DB;
use Exception;
use Modules\Project\Entities\Project;
use Modules\Project\Events\ProjectCreated;
use Modules\Project\Events\ProjectDeleted;
use Modules\Project\Events\ProjectUpdated;

class ProjectService extends BaseService
{
    /**
     * ProjectService constructor.
     */
    public function __construct(Project $project)
    {
        $this->model = $project;
    }

    /**
     * Store a new project in the database.
     *
     * @param  array  $data The data for creating the project.
     * @return Project The newly created project.
     *
     * @throws GeneralException If there is a problem creating the keyword.
     */
    public function store(array $data = []): Project
    {
        DB::beginTransaction();

        try {
            $keyword = $this->createRecord($data);
        } catch (Exception $e) {
            DB::rollBack();

            throw new GeneralException(__('There was a problem creating this keyword. Please try again.'));
        }

        event(new ProjectCreated($keyword));

        DB::commit();

        return $keyword;
    }

    /**
     * Updates a project keyword with the given data.
     *
     * @param  Project  $keyword The project keyword to update.
     * @param  array  $data An array of data to update the keyword with.
     * @return Project The updated project keyword.
     *
     * @throws GeneralException If there was a problem updating the keyword.
     */
    public function update(Project $keyword, array $data = []): Project
    {
        DB::beginTransaction();

        try {
            $keyword->update($this->createRow($data));
        } catch (Exception $e) {
            DB::rollBack();

            throw new GeneralException(__('There was a problem updating this keyword. Please try again.'));
        }

        event(new ProjectUpdated($keyword));

        DB::commit();

        return $keyword;
    }

    /**
     * Deletes a project.
     *
     * @param  Project  $project The project to be deleted.
     * @return Project The deleted project.
     *
     * @throws GeneralException If there was a problem deleting the project.
     */
    public function delete(Project $project): Project
    {
        if ($this->deleteById($project->id)) {

            $activeProjectId = getActiveProjectId();
            if (! is_null($activeProjectId) && $activeProjectId == $project->id) {
                session()->forget('activeProjectId');
            }

            event(new ProjectDeleted($project));

            return $project;
        }

        throw new GeneralException('There was a problem deleting this project. Please try again.');
    }

    public function activate(Project $project)
    {
        session([
            'activeProjectId' => $project->hashId,
            'activeProjectIdInt' => $project->id,
        ]);

        return $project;
    }

    /**
     * Creates a keyword in the project.
     *
     * @param  array  $data The data for creating the keyword.
     * @return Project The created project.
     */
    protected function createRecord(array $data = []): Project
    {
        return $this->model::create($this->createRow($data));
    }

    protected function createRow(array $data = []): array
    {
        return [
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_complete' => $data['is_complete'] ?? false,
        ];
    }
}
