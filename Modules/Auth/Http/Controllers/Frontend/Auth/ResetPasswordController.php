<?php

namespace Modules\Auth\Http\Controllers\Frontend\Auth;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Modules\Auth\Http\Requests\Frontend\Auth\ResetPasswordRequest;

/**
 * Class ResetPasswordController.
 */
class ResetPasswordController
{
    /**
     * Display the password reset link request view.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('Auth/ResetPassword', [
            'email' => $request->email,
            'token' => $request->route('token'),
        ]);
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(ResetPasswordRequest $request): RedirectResponse
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
