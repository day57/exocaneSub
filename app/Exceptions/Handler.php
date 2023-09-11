<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
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
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        return parent::render($request, $exception);
    }
}


/*
 
    This code snippet is defining a custom exception handler for a Laravel application, extending Laravel's base ExceptionHandler class.

    Here's what each part does:

    protected $dontReport: This property defines an array of exception types that should not be logged or reported. It's currently empty, so no exceptions are being suppressed.

    protected $dontFlash: This property defines a list of input fields that should never be flashed to the session. In this case, it includes 'password' and 'password_confirmation'. This is important for security as it ensures that sensitive information like passwords is not accidentally exposed.

    public function report(Throwable $exception): This method is used to log or report an exception. Here, it's simply calling the parent's report method, so it's using Laravel's default behavior for reporting exceptions.

    public function render($request, Throwable $exception): This method is used to convert an exception into an HTTP response that's sent back to the user. Again, it's calling the parent's render method, so it's using Laravel's default behavior for rendering exceptions.

    This class could be customized further to handle specific exception types in a custom way, log additional information, etc. However, in its current state, it's mainly defining some basic configurations and relying on Laravel's built-in exception handling for the rest.

*/