<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResponseResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class Controller
{
    protected function success(
        mixed $data = null,
        string $message = 'Request successful.',
        int $status = 200
    ): JsonResponse {
        if ($data instanceof JsonResource) {
            $data = $data->resolve(request());
        }

        return (new ApiResponseResource([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ]))
            ->response()
            ->setStatusCode($status);
    }

    protected function error(string $message, int $status): JsonResponse
    {
        return (new ApiResponseResource([
            'status' => 'error',
            'message' => $message,
            'data' => null,
        ]))
            ->response()
            ->setStatusCode($status);
    }
}
