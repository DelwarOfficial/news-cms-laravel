<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function success(mixed $data, int $code = 200, array $extra = []): JsonResponse
    {
        $response = array_merge(['data' => $data], $extra);

        return response()->json($response, $code);
    }

    protected function successWithMeta(mixed $data, array $meta, int $code = 200): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'meta' => $meta,
        ], $code);
    }

    protected function error(string $error, string $message, int $code = 400, mixed $details = null): JsonResponse
    {
        $response = [
            'error' => $error,
            'message' => $message,
        ];

        if ($details !== null) {
            $response['details'] = $details;
        }

        return response()->json($response, $code);
    }

    protected function errorWithMeta(string $error, string $message, int $code = 400, ?array $meta = null, mixed $details = null): JsonResponse
    {
        $response = [
            'error' => $error,
            'message' => $message,
            'meta' => $meta ?? [],
        ];

        if ($details !== null) {
            $response['details'] = $details;
        }

        return response()->json($response, $code);
    }

    protected function paginated(mixed $paginator): JsonResponse
    {
        $resource = $paginator instanceof \Illuminate\Http\Resources\Json\ResourceCollection
            ? $paginator->response()->getData()
            : $paginator;

        $data = $resource->data ?? $resource->items() ?? $resource;

        return response()->json([
            'data' => $data,
            'meta' => [
                'page' => $paginator->currentPage(),
                'limit' => (int) $paginator->perPage(),
                'total' => $paginator->total(),
                'totalPages' => $paginator->lastPage(),
            ],
        ]);
    }

    protected function created(mixed $data = null): JsonResponse
    {
        return $this->success($data, 201);
    }

    protected function noContent(): JsonResponse
    {
        return response()->json(null, 204);
    }
}
