<?php

namespace Modules\Project\Http\Controllers\Frontend;

use App\Http\Resources\PaginationCollection;
use Illuminate\Routing\Controller;
use Modules\Project\Entities\Project;
use Modules\Project\Http\Requests\StoreProjectRequest;
use Modules\Project\Http\Requests\UpdateProjectRequest;
use Modules\Project\Services\ProjectService;

class ProjectController extends Controller
{
    public function __construct(
        private ProjectService $projectService
    ) {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $data = $this->projectService->getAll();

        return inertia('project::frontend.index', [
            'data' => new PaginationCollection($data),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return inertia('project::frontend.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreProjectRequest $request)
    {
        $this->projectService->store($request->validated());

        return redirect()
            ->route('admin.project.index')
            ->withFlashSuccess(__('message.create_success', ['attribute' => 'Project']));
    }

    /**
     * Show the specified resource.
     *
     * @return \Inertia\Response
     */
    public function show(Project $project)
    {
        return inertia('project::frontend.show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Inertia\Response
     */
    public function edit(Project $project)
    {
        return inertia('project::frontend.edit', compact('project'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $this->projectService->update($project, $request->validated());

        return redirect()
            ->route('admin.project.index')
            ->withFlashSuccess(__('message.update_success', ['attribute' => 'Project']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Project $project)
    {
        $this->projectService->delete($project);

        return redirect()
            ->route('admin.project.index')
            ->withFlashSuccess(__('message.delete_success', ['attribute' => 'Project']));
    }
}
