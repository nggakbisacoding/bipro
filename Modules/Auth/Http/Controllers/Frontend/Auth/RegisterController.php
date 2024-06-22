<?php

namespace Modules\Auth\Http\Controllers\Frontend\Auth;

use  Modules\Auth\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Modules\Auth\Http\Requests\Frontend\Auth\RegisterRequest;

/**
 * Class RegisterController.
 */
class RegisterController
{
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * RegisterController constructor.
     *
     * @param  UserService  $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        abort_unless(config('boilerplate.access.user.registration'), 404);

        return inertia('auth::register');
    }

    protected function store(RegisterRequest $request)
    {
        abort_unless(config('boilerplate.access.user.registration'), 404);
        
        $data = $request->validated();

        $user = $this->userService->registerUser($data);

        Auth::login($user);

        return redirect(route(homeRoute()));
    }
}
