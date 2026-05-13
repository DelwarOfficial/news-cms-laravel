<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next, ?string $scope = null): Response
    {
        $key = $request->header('X-API-Key')
            ?: $request->bearerToken()
            ?: $request->query('api_key');

        if (! $key || ! Str::startsWith($key, 'nh_')) {
            return response()->json(['error' => 'Unauthorized', 'message' => 'Valid API key required.'], 401);
        }

        $prefix = ApiKey::prefixFromKey($key);
        $hash = hash('sha256', $key);

        $apiKey = ApiKey::active()
            ->where('key_prefix', $prefix)
            ->where('key_hash', $hash)
            ->first();

        if (! $apiKey) {
            return response()->json(['error' => 'Unauthorized', 'message' => 'Invalid or inactive API key.'], 401);
        }

        if ($scope && ! $apiKey->hasScope($scope)) {
            return response()->json(['error' => 'Forbidden', 'message' => "Scope '{$scope}' required."], 403);
        }

        $apiKey->update(['last_used_at' => now()]);

        $request->merge(['api_key_id' => $apiKey->id, 'api_key_owner' => $apiKey->user_id]);

        return $next($request);
    }
}
