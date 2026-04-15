<?php
namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $e): JsonResponse
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            $statusCode = $this->getStatusCode($e);
            $errorData = $this->getErrorData($e);

            return response()->json([
                'success' => false,
                'data'    => $errorData,
            ], $statusCode);
        }

        return parent::render($request, $e);
    }

    private function getStatusCode(Throwable $e): int
    {
        if (method_exists($e, 'getStatusCode')) {
            return $e->getStatusCode();
        }

        return match (true) {
            $e instanceof NotFoundHttpException,
            $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException => 404,
            $e instanceof ValidationException => 422,
            $e instanceof \Illuminate\Auth\AuthenticationException => 401,
            $e instanceof \Illuminate\Auth\Access\AuthorizationException => 403,
            default => 500,
        };
    }

    private function getErrorData(Throwable $e): mixed
    {
        $statusCode = $this->getStatusCode($e);

        if ($e instanceof ValidationException) {
            return $e->errors();
        }

        if ($statusCode == 500) {
            return [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ];
        }
        
        return $e->getMessage() ?: 'Server Error';
    }
}