<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

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
            //
        });
    }
    public function render($request, Throwable $exception)
    {
      // Handle unauthorized errors
        if ($exception instanceof AuthenticationException) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $headers = $request->headers->all();
                // If the request expects a JSON response (API request)
                return response()->json([
                    'error' => 'Session expired, please log in again',
                    'headers' => $headers 
                ], Response::HTTP_UNAUTHORIZED); // 401 status code
            } else {
                // If the request is a web request, redirect to the login page
                return redirect()->guest(route('login'));
            }
        }
       return parent::render($request, $exception);
    }
}
