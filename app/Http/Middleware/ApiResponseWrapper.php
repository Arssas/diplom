<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiResponseWrapper
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response instanceof JsonResponse) {
            $originData = $response->getData(true);
            $statusCode = $response->getStatusCode();

            $isSuccess = $statusCode >= 200 && $statusCode < 300;

            if (isset($originData['success']) && array_key_exists('data', $originData)) {
                return $response;
            }

            $wrapped = [
                'success' => $isSuccess,
                'data' => $originData
            ];

            $response->setData($wrapped);
        }

        return $response;
    }
}
