<?php

namespace Modules\Post\Http\Controllers\Backend;

use Illuminate\Routing\Controller;
use Modules\Post\Http\Requests\ExportPostRequest;
use Modules\Post\Services\PostExportService;
use Modules\Post\Transformers\PostExportResource;

class PostExportController extends Controller
{
    public function __construct(
        private PostExportService $postExportService
    ) {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $data = $this->postExportService->getAll();

        return inertia('post::backend.export.index', [
            'data' => new PostExportResource($data),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\RedirectResponse The redirect response after exporting the post.
     */
    public function store(ExportPostRequest $request)
    {
        $this->postExportService->store($request->validated());

        return redirect()
            ->route('admin.post.index')
            ->withFlashSuccess(__('message.create_success', ['attribute' => 'Posts']));
    }

    public function download(string $id)
    {
        [$name, $filePath] = $this->postExportService->getDownloadPath($id);

        return response()
            ->download(
                $filePath,
                $name
            );
    }
}
