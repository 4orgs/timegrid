<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Foundation\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param \Exception $exception
     *
     * @return void
     */
    public function report(Exception $exception)
    {
        Mail::raw($exception, function ($message) {
            $message->subject(config('root.report.exceptions_subject'));
            $message->from(config('root.report.from_address'), config('root.appname'));
            $message->to(config('root.report.to_mail'));
        });

        return parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $exception
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if (app()->environment('production') || app()->environment('demo')) {
            // Catch TokenMismatchException to show a friendly error message
            if ($exception instanceof \Illuminate\Session\TokenMismatchException) {
                return redirect($request->fullUrl())->withErrors(trans('app.msg.invalid_token'));
            }

            if ($exception instanceof \Illuminate\Http\Exception\HttpResponseException) {
                return redirect(route('user.directory.list'))->withErrors(trans('app.msg.invalid_url'));
            }

            // Catch General exceptios to show a friendly error message
            if (!app()->isDownForMaintenance() && $exception instanceof Exception) {
                return redirect(route('user.dashboard'))->withErrors(trans('app.msg.general_exception'));
            }
        }

        // Handle any other case
        return parent::render($request, $exception);
    }
}
