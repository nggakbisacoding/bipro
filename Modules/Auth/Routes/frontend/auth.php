<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Modules\Auth\Http\Controllers\Frontend\Auth\LoginController;
use Modules\Auth\Http\Controllers\Frontend\Auth\ConfirmPasswordController;
use Modules\Auth\Http\Controllers\Frontend\Auth\DisableTwoFactorAuthenticationController;
use Modules\Auth\Http\Controllers\Frontend\Auth\EmailVerificationNotificationController;
use Modules\Auth\Http\Controllers\Frontend\Auth\ForgotPasswordController;
use Modules\Auth\Http\Controllers\Frontend\Auth\PasswordExpiredController;
use Modules\Auth\Http\Controllers\Frontend\Auth\RegisterController;
use Modules\Auth\Http\Controllers\Frontend\Auth\ResetPasswordController;
use Modules\Auth\Http\Controllers\Frontend\Auth\SocialController;
use Modules\Auth\Http\Controllers\Frontend\Auth\TwoFactorAuthenticationController;
use Modules\Auth\Http\Controllers\Frontend\Auth\UpdatePasswordController;
use Modules\Auth\Http\Controllers\Frontend\Auth\VerificationController;
use Modules\Auth\Http\Controllers\Frontend\Auth\VerifyEmailController;

Route::prefix('auth')->as('auth.')->group(function() {
    Route::group(['middleware' => 'auth'], function () {
        // Authentication
        Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

        // Password expired routes
        Route::get('password/expired', [PasswordExpiredController::class, 'expired'])->name('password.expired');
        Route::patch('password/expired', [PasswordExpiredController::class, 'update'])->name('password.expired.update');

        // These routes can not be hit if the password is expired
        Route::group(['middleware' => 'password.expires'], function () {
            // E-mail Verification
            Route::get('email/verify', VerificationController::class)
                ->name('verification.notice');
            Route::get('email/verify/{id}/{hash}', VerifyEmailController::class)
                ->name('verification.verify')
                ->middleware(['signed', 'throttle:6,1']);
            Route::post('email/resend', [EmailVerificationNotificationController::class, 'store'])
                ->name('verification.resend')
                ->middleware('throttle:6,1');

            // These routes require the users email to be verified
            Route::group(['middleware' => config('boilerplate.access.middleware.verified')], function () {
                // Passwords
                Route::get('password/confirm', [ConfirmPasswordController::class, 'index'])->name('password.confirm');
                Route::post('password/confirm', [ConfirmPasswordController::class, 'store']);

                Route::patch('password/update', [UpdatePasswordController::class, 'update'])->name('password.change');

                // Two-factor Authentication
                Route::group(['prefix' => 'account/2fa', 'as' => 'account.2fa.'], function () {
                    Route::group(['middleware' => '2fa:disabled'], function () {
                        Route::get('enable', [TwoFactorAuthenticationController::class, 'create'])
                            ->name('create');
                    });

                    Route::group(['middleware' => '2fa:enabled'], function () {
                        Route::get('recovery', [TwoFactorAuthenticationController::class, 'show'])
                            ->name('show');

                        Route::patch('recovery/generate', [TwoFactorAuthenticationController::class, 'update'])->name('update');

                        Route::get('disable', [DisableTwoFactorAuthenticationController::class, 'show'])
                            ->name('disable');

                        Route::delete('/', [DisableTwoFactorAuthenticationController::class, 'destroy'])->name('destroy');
                    });
                });
            });
        });
    });

    Route::group(['middleware' => 'guest'], function () {
        // Authentication
        Route::get('login', [LoginController::class, 'index'])->name('login');
        Route::post('login', [LoginController::class, 'store']);

        // Registration
        Route::get('register', [RegisterController::class, 'index'])->name('register');
        Route::post('register', [RegisterController::class, 'store']);

        // Password Reset
        Route::get('password/reset', [ForgotPasswordController::class, 'index'])->name('password.request');
        Route::post('password/email', [ForgotPasswordController::class, 'store'])->name('password.email');
        Route::get('password/reset/{token}', [ResetPasswordController::class, 'index'])->name('password.reset');
        Route::post('password/reset', [ResetPasswordController::class, 'store'])->name('password.update');

        // Socialite Routes
        Route::get('login/{provider}', [SocialController::class, 'redirect'])->name('social.login');
        Route::get('login/{provider}/callback', [SocialController::class, 'callback']);
    });
});
