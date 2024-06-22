<?php

namespace Modules\Auth\Http\Controllers\Frontend\Auth;

use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Modules\Auth\Http\Requests\Frontend\Auth\ForgotPasswordRequest;


/**
 * Class ForgotPasswordController.
 */
class ForgotPasswordController
{
    /**
     * Display the password reset link request view.
     */
    public function index(): Response
    {
        return Inertia::render('Auth/ForgotPassword', [
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(ForgotPasswordRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $data['email']
        );

        if ($status == Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }
}
