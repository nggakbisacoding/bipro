<?php

namespace Modules\Insight\Http\Controllers\Frontend;

use Illuminate\Routing\Controller;

class InsightController extends Controller
{
    public function __construct(
    ) {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        return inertia('insight::frontend.index', [
        ]);
    }
}
