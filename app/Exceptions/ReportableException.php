<?php

namespace App\Exceptions;

use Exception;
use Throwable;

/**
 * Class ReportableException.
 */
class ReportableException extends Exception
{
    public $message;

    /**
     * GeneralException constructor.
     *
     * @param  string  $message
     * @param  int  $code
     */
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Report the exception.
     */
    public function report()
    {
        //
    }

    /**
     * Renders the page and redirects back with a flash message.
     *
     * @param  mixed  $request The request object.
     * @return \Illuminate\Http\RedirectResponse The redirect response.
     */
    public function render($request)
    {
        // All instances of ReportableException redirect back with a flash message to show a bootstrap alert-error
        return redirect()
            ->back()
            ->withInput()
            ->withFlashDanger($this->message);
    }
}
