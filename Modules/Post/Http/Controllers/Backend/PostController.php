<?php

namespace Modules\Post\Http\Controllers\Backend;

use Illuminate\Routing\Controller;
use Modules\Post\Services\PostService;
use Modules\Post\Transformers\PostDetailResource;
use Modules\Post\Transformers\PostResource;

class PostController extends Controller
{
    public function __construct(
        private PostService $postService
    ) {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $data = $this->postService->getPosts();

        return inertia('post::backend.index', [
            'data' => new PostResource($data),
        ]);
    }

    /**
     * Show the specified resource.
     *
     * @return \Inertia\Response
     */
    public function show(string $postId)
    {
        $data = $this->postService->getPostBy($postId);
        $hashtags = $this->postService
            ->select(['hashtags'])
            ->where('hashtags', '', '!=')
            ->getRandom(10);
        $hashtags = $hashtags->pluck('hashtags')->join(',');
        $hashtags = collect(explode(',', $hashtags));
        $hashtags = $hashtags->unique()->toArray();

        return inertia('post::backend.show', [
            'data' => fn () => new PostDetailResource($data),
            'postId' => fn () => $postId,
            'hashtags' => fn () => [],
        ]);
    }
}
