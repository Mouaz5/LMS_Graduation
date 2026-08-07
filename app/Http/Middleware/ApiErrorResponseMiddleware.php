<?php

namespace App\Http\Middleware;

use App\Http\Responses\ApiErrorResponse;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiErrorResponseMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        return $response instanceof JsonResponse
            ? ApiErrorResponse::normalize($response)
            : $response;
    }
}
