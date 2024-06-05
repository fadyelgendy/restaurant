<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ResponseTrait
{
    public function failResponseJson(string|array $errors = [], int $code = 400): JsonResponse
    {
        return response()->json($this->failResponse($errors, $code), $code);
    }

    public function successResponseJson(string|array $data = [], int $code = 200): JsonResponse
    {
        return response()->json($this->successResponse($data, $code), $code);
    }

    public function failResponse(string|array $errors = [], int $code = 400): array
    {
        return [
            'status' => $code,
            'errors' => is_string($errors) ? ['error' => $errors] : $errors
        ];
    }

    public function successResponse(string|array $data = [], int $code = 200): array
    {
        return [
            'status' => $code,
            'data' => is_string($data) ? ['message' => $data] : $data
        ];
    }
}
