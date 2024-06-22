<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Routing\Controller;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Renderable
     */
    public function index()
    {
        return view('auth::index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Renderable
     */
    public function create()
    {
        return view('auth::create');
    }

    /**
     * Show the specified resource.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('auth::show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('auth::edit');
    }
}
