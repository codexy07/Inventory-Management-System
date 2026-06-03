<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $levels = [];

    protected $dontReport = [
        //
    ];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        // Handle HTTP exceptions (404, 403, 419, 429, 500, etc.)
        if ($this->isHttpException($e)) {
            $statusCode = $e->getStatusCode();

            if (view()->exists("errors.{$statusCode}")) {
                return response()->view("errors.{$statusCode}", [
                    'exception' => $e,
                ], $statusCode);
            }
        }

        // Handle TokenMismatchException (419)
        if ($e instanceof TokenMismatchException) {
            return redirect()->route('login')
                ->with('error', 'Your session has expired. Please sign in again.');
        }

        // Handle ModelNotFoundException
        if ($e instanceof ModelNotFoundException) {
            return response()->view('errors.404', [
                'exception' => $e,
                'message' => 'The requested resource was not found.',
            ], 404);
        }

        // Handle AuthorizationException (403)
        if ($e instanceof AuthorizationException) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized.'], 403);
            }

            return response()->view('errors.403', [
                'exception' => $e,
                'message' => 'You do not have permission to perform this action.',
            ], 403);
        }

        // Handle NotFoundHttpException
        if ($e instanceof NotFoundHttpException) {
            return response()->view('errors.404', [
                'exception' => $e,
            ], 404);
        }

        // Handle MethodNotAllowedHttpException
        if ($e instanceof MethodNotAllowedHttpException) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Method not allowed.'], 405);
            }

            return redirect()->route('dashboard')
                ->with('error', 'Invalid request method.');
        }

        // Handle TooManyRequestsHttpException
        if ($e instanceof TooManyRequestsHttpException) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Too many requests. Please slow down.'], 429);
            }

            return back()->with('error', 'Too many requests. Please wait before trying again.');
        }

        // Handle QueryException (database errors)
        if ($e instanceof QueryException) {
            $errorCode = $e->errorInfo[1] ?? null;

            // Duplicate entry
            if ($errorCode == 1062) {
                return back()->with('error', 'A record with this information already exists.')->withInput();
            }

            // Foreign key constraint
            if ($errorCode == 1451) {
                return back()->with('error', 'Cannot delete this record because it is referenced by other records.');
            }

            if ($this->shouldReturnCustomError($request)) {
                return response()->view('errors.500', [
                    'message' => 'A database error occurred. Please try again.',
                ], 500);
            }
        }

        // Handle AuthenticationException
        if ($e instanceof AuthenticationException) {
            return redirect()->route('login')
                ->with('error', 'Please sign in to continue.');
        }

        // In production, show a friendly 500 page for unhandled exceptions
        if ($this->shouldReturnCustomError($request)) {
            return response()->view('errors.500', [
                'message' => 'An unexpected error occurred. Our team has been notified.',
            ], 500);
        }

        return parent::render($request, $e);
    }

    /**
     * Determine if we should return a custom error page.
     */
    protected function shouldReturnCustomError($request): bool
    {
        if (config('app.debug')) {
            return false;
        }

        if ($request->expectsJson()) {
            return false;
        }

        return true;
    }
}
