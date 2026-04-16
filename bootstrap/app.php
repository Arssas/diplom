<?php

use Illuminate\Validation\ValidationException;
use App\Http\Middleware\ApiResponseWrapper;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->appendToGroup('api', ApiResponseWrapper::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                $statusCode = match (true) {
                    method_exists($e, 'getStatusCode') => $e->getStatusCode(),
                    $e instanceof NotFoundHttpException,
                    $e instanceof ModelNotFoundException => 404,
                    $e instanceof ValidationException => 422,
                    default => 500,
                };
                
                if ($e instanceof ValidationException) {
                    $errorData = $e->errors();
                } elseif ($statusCode === 500 && config('app.debug')) {
                    $errorData = [
                        'message'   => $e->getMessage(),
                        'exception' => get_class($e),
                        'file'      => $e->getFile(),
                        'line'      => $e->getLine(),
                        'trace'     => $e->getTraceAsString(),
                    ];
                } else {
                    $errorData = $e->getMessage() ?: 'Server Error';
                }

                return response()->json([
                    'success' => false,
                    'data'    => $errorData,
                ], $statusCode);    
            }

            return null;
        });
    })->create();
