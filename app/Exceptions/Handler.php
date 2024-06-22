<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            if (app()->bound('sentry')) {
                app('sentry')->captureException($e);
            }
        });
    }

    /**
     * Report or log an exception.
     *
     * @return void
     *
     * @throws \Throwable
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof UnauthorizedException) {
            return redirect()
                ->route(homeRoute())
                ->withFlashDanger(__('You do not have access to do that.'));
        }

        if ($exception instanceof AuthorizationException) {
            $message = __('You do not have access to do that.');

            if ($exception->getMessage()) {
                $message = $exception->getMessage();
            }

            return redirect()
                ->back()
                ->withFlashDanger($message);
        }

        if ($exception instanceof ModelNotFoundException) {
            return redirect()
                ->route(homeRoute())
                ->withFlashDanger(__('The requested resource was not found.'));
        }

        return parent::render($request, $exception);
    }
}
