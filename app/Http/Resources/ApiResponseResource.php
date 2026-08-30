<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApiResponseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = $this->resource['data'] ?? null;

        if ($data instanceof JsonResource) {
            $data = $data->resolve($request);
        }

        return [
            'status' => $this->resource['status'],
            'message' => $this->resource['message'],
            'data' => $data,
            'errors' => $this->when(
                array_key_exists('errors', $this->resource),
                $this->resource['errors'] ?? null
            ),
        ];
    }
}
