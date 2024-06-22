<?php

namespace Modules\Post\Http\Controllers\Frontend;

use App\Http\Resources\PaginationCollection;
use Illuminate\Routing\Controller;
use Modules\Post\Entities\Post;
use Modules\Post\Services\PostService;

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

        return inertia('post::frontend.index', [
            'data' => new PaginationCollection($data),
        ]);
    }

    /**
     * Show the specified resource.
     *
     * @return \Inertia\Response
     */
    public function show(Post $post)
    {
        return inertia('post::frontend.show');
    }
}
