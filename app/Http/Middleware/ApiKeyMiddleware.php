<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next, ?string $scope = null): Response
    {
        $key = $request->header('X-API-Key')
            ?: $request->bearerToken();

        if (! $key || ! Str::startsWith($key, 'nh_')) {
            return $this->error('Unauthorized', 'Valid API key required.', 401);
        }

        $prefix = ApiKey::prefixFromKey($key);
        $hash = hash('sha256', $key);

        $apiKey = ApiKey::active()
            ->where('key_prefix', $prefix)
            ->where('key_hash', $hash)
            ->first();

        if (! $apiKey) {
            return $this->error('Unauthorized', 'Invalid or inactive API key.', 401);
        }

        if ($scope && ! $apiKey->hasScope($scope)) {
            return $this->error('Forbidden', "Scope '{$scope}' required.", 403);
        }

        $touchKey = "api-key:last-used:{$apiKey->id}";

        if (Cache::add($touchKey, true, now()->addMinute())) {
            $apiKey->forceFill(['last_used_at' => now()])->saveQuietly();
        }

        $request->merge(['api_key_id' => $apiKey->id, 'api_key_owner' => $apiKey->user_id]);

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
