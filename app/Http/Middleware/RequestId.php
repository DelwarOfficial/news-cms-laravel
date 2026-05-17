<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RequestId
{
    public function handle(Request $request, Closure $next): Response
    {
        $header = config('monitoring.request_id_header', 'X-Request-Id');

        $requestId = (string) ($request->headers->get($header) ?: Str::uuid());
        $request->attributes->set('request_id', $requestId);

        Log::withContext([
            'request_id' => $requestId,
        ]);

        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = $next($request);
        $response->headers->set($header, $requestId);

        return $response;
    }
}
