<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponse
{
    protected function success(mixed $data, int $code = 200, array $extra = []): JsonResponse
    {
        $response = $this->envelope(
            data: $data,
            meta: $extra['meta'] ?? [],
            error: null,
        );

        unset($extra['meta']);
        $response = array_merge($response, $extra);

        return response()->json($response, $code);
    }

    protected function successWithMeta(mixed $data, array $meta, int $code = 200): JsonResponse
    {
        return response()->json($this->envelope($data, $meta), $code);
    }

    protected function error(string $error, string $message, int $code = 400, mixed $details = null): JsonResponse
    {
        return response()->json($this->envelope(null, [], [
            'code' => $error,
            'message' => $message,
            'details' => $details,
        ]), $code);
    }

    protected function errorWithMeta(string $error, string $message, int $code = 400, ?array $meta = null, mixed $details = null): JsonResponse
    {
        return response()->json($this->envelope(null, $meta ?? [], [
            'code' => $error,
            'message' => $message,
            'details' => $details,
        ]), $code);
    }

    protected function paginated(mixed $paginator): JsonResponse
    {
        $resourceData = null;
        $source = $paginator;

        if ($paginator instanceof ResourceCollection) {
            $resourceData = $paginator->response()->getData(true);
            $source = $paginator->resource;
        }

        $data = $resourceData['data'] ?? (
            $source instanceof LengthAwarePaginator ? $source->items() : $source
        );

        if (! $source instanceof LengthAwarePaginator) {
            return $this->success($data);
        }

        return response()->json($this->envelope($data, [
            'page' => $source->currentPage(),
            'limit' => (int) $source->perPage(),
            'total' => $source->total(),
            'totalPages' => $source->lastPage(),
        ]));
    }

    protected function created(mixed $data = null): JsonResponse
    {
        return $this->success($data, 201);
    }

    protected function noContent(): JsonResponse
    {
        return response()->json(null, 204);
    }

    private function envelope(mixed $data = null, array $meta = [], mixed $error = null): array
    {
        if (is_array($error)) {
            $error = array_filter($error, fn ($value) => $value !== null);
        }

        return [
            'data' => $data,
            'meta' => $meta,
            'error' => $error,
        ];
    }
}
