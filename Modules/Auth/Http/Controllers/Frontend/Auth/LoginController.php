<?php

namespace Modules\Auth\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Auth\Http\Requests\Frontend\Auth\LoginRequest;

/**
 * Class LoginController.
 */
class LoginController extends Controller
{
    /**
     * Display the login view.
     */
    public function index(): Response
    {
        return Inertia::render('auth::login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(homeRoute());
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->forget('activeProjectId');

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        $request->session()->flush();

        return redirect('/');
    }
}
