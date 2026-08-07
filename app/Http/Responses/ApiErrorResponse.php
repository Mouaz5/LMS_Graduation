<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiErrorResponse
{
    public static function make(string $message, int $status, array $errors = [], array $headers = []): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors ?: null,
            'status' => $status,
        ], $status, $headers);
    }

    public static function normalize(JsonResponse $response): JsonResponse
    {
        if ($response->isSuccessful() || $response->getStatusCode() < 400) {
            return $response;
        }

        $payload = $response->getData(true);

        if (is_array($payload) && ($payload['success'] ?? null) === false) {
            return $response;
        }

        return self::make(
            is_array($payload) && isset($payload['message']) ? $payload['message'] : 'Request failed.',
            $response->getStatusCode(),
            is_array($payload) && isset($payload['errors']) && is_array($payload['errors']) ? $payload['errors'] : [],
            $response->headers->all(),
        );
    }
}
