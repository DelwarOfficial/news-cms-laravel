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
            return response()->json(['error' => 'Unauthorized', 'message' => 'API key context missing.'], 401);
        }

        $apiKey = \App\Models\ApiKey::find($apiKeyId);

        if (! $apiKey || ! $apiKey->hasScope($scope)) {
            return response()->json(['error' => 'Forbidden', 'message' => "Scope '{$scope}' required."], 403);
        }

        return $next($request);
    }
}
