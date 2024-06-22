<?php

namespace Modules\Auth\Http\Controllers\Frontend\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Class VerificationController.
 */
class VerificationController
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|Response
    {
        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(route(homeRoute()))
                    : Inertia::render('Auth/VerifyEmail', ['status' => session('status')]);
    }
}
