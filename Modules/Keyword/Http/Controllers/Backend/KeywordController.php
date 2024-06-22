<?php

namespace Modules\Keyword\Http\Controllers\Backend;

use Illuminate\Routing\Controller;
use Modules\Keyword\Entities\Keyword;
use Modules\Keyword\Http\Requests\BulkStoreKeywordRequest;
use Modules\Keyword\Http\Requests\StoreKeywordRequest;
use Modules\Keyword\Http\Requests\UpdateKeywordRequest;
use Modules\Keyword\Services\KeywordService;
use Modules\Keyword\Transformers\KeywordDetailResource;
use Modules\Keyword\Transformers\KeywordResource;

class KeywordController extends Controller
{
    public function __construct(private KeywordService $keywordService)
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $keywords = $this->keywordService
            ->select([
                'id',
                'name',
                'type',
                'source',
                'status',
                'updated_at',
                'last_crawled',
                'last_post',
            ])
            ->withCount('posts')
            ->where('project_id', getActiveProjectId('int'))
            ->getAll();

        return inertia('keyword::backend.index', [
            'data' => fn () => new KeywordResource($keywords),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return inertia('keyword::backend.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreKeywordRequest $request)
    {
        $this->keywordService->store($request->validated());

        return redirect()
            ->route('admin.keyword.index')
            ->withFlashSuccess(__('message.create_success', ['attribute' => 'Keyword']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Inertia\Response
     */
    public function edit(Keyword $keyword)
    {
        $keyword = new KeywordDetailResource($keyword);

        return inertia('keyword::backend.edit', compact('keyword'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateKeywordRequest $request, Keyword $keyword)
    {
        $this->keywordService->update($keyword, $request->validated());

        return redirect()
            ->route('admin.keyword.index')
            ->withFlashSuccess(__('message.update_success', ['attribute' => 'Keyword']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Keyword $keyword)
    {
        $this->keywordService->delete($keyword);

        return redirect()
            ->route('admin.keyword.index')
            ->withFlashSuccess(__('message.delete_success', ['attribute' => 'Keyword']));
    }

    public function export_template()
    {
        return response()
            ->download(storage_path('app/templates/template_add_keyword.xlsx'));
    }

    public function import(BulkStoreKeywordRequest $request)
    {
        $keywords = $request->input('keywords');

        $this->keywordService->bulkStore($keywords);

        return redirect()
            ->route('admin.keyword.index')
            ->withFlashSuccess(__('message.create_success', ['attribute' => 'Keyword']));
    }
}
