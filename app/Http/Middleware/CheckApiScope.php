<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiScope
{
    public function handle(Request $request, Closure $next, string $scope): Response
    {
        $apiKeyId = $request->get('api_key_id');

        if (! $apiKeyId) {
            return $this->error('Unauthorized', 'API key context missing.', 401);
        }

        $apiKey = \App\Models\ApiKey::find($apiKeyId);

        if (! $apiKey || ! $apiKey->hasScope($scope)) {
            return $this->error('Forbidden', "Scope '{$scope}' required.", 403);
        }

        return $next($request);
    }

    private function error(string $code, string $message, int $status): Response
    {
        return response()->json([
            'data' => null,
            'meta' => [],
            'error' => [
                'code' => $code,
                'message' => $message,
            ],
        ], $status);
    }
}
