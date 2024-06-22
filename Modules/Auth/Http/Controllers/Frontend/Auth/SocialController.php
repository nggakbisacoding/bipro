<?php

namespace Modules\Auth\Http\Controllers\Frontend\Auth;

use Modules\Auth\Events\User\UserLoggedIn;
use  Modules\Auth\Services\UserService;
use Laravel\Socialite\Facades\Socialite;

/**
 * Class SocialController.
 */
class SocialController
{
    /**
     * @param $provider
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * @param $provider
     * @param  UserService  $userService
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \App\Exceptions\GeneralException
     */
    public function callback($provider, UserService $userService)
    {
        $user = $userService->registerProvider(Socialite::driver($provider)->user(), $provider);

        if (! $user->isActive()) {
            auth()->logout();

            return redirect()->route('frontend.auth.login')->withFlashDanger(__('Your account has been deactivated.'));
        }

        auth()->login($user);

        event(new UserLoggedIn($user));

        return redirect()->route(homeRoute());
    }
}
