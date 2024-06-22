<?php

namespace Modules\Project\Http\Controllers\Backend;

use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Modules\Keyword\Services\KeywordService;
use Modules\Post\Services\PostService;
use Modules\Project\Entities\Project;
use Modules\Project\Http\Requests\StoreProjectRequest;
use Modules\Project\Http\Requests\UpdateProjectRequest;
use Modules\Project\Services\ProjectService;
use Modules\Project\Transformers\ProjectDetailResource;
use Modules\Project\Transformers\ProjectResource;

class ProjectController extends Controller
{
    public function __construct(
        private ProjectService $projectService,
        private PostService $postService,
        private KeywordService $keywordService
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

        return inertia('project::backend.index', [
            'data' => new ProjectResource($data),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return inertia('project::backend.create');
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
        $posts = $this->postService->getLatestPosts($project->id);
        $totalPost = $this->postService->getTotalPost($project->id);

        $totalTargetCrawl = $this->keywordService->getTotalTargetKeyword($project->id);
        $totalActiveTargetCrawl = $this->keywordService->getTotalActiveTargetKeyword($project->id);

        return inertia('project::backend.show', [
            'project' => new ProjectDetailResource($project),
            'posts' => fn () => $posts,
            'totalPost' => fn () => $totalPost,
            'totalKeyword' => fn () => $totalTargetCrawl,
            'totalActiveKeyword' => fn () => $totalActiveTargetCrawl,
            'keywords' => fn () => $this->keywordService->getLatestKeywords(),

            'sentiments' => Inertia::lazy(function () {
                $sentiments = $this->postService->getSentiments();

                return $sentiments;
            }),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Inertia\Response
     */
    public function edit(Project $project)
    {
        return inertia('project::backend.edit', compact('project'));
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

    public function activate(Project $project)
    {
        $this->projectService->activate($project);
    }
}
