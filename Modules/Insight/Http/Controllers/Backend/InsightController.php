<?php

namespace Modules\Insight\Http\Controllers\Backend;

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
        return inertia('insight::backend.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return inertia('insight::backend.create');
    }
}
