<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('tenancy.enabled', false)) {
            return $next($request);
        }

        $host = $request->getHost();
        $tenant = Tenant::identify($host);

        if ($tenant === null) {
            $centralDomains = config('tenancy.central_domains', []);

            $isCentral = collect($centralDomains)->contains(function ($domain) use ($host) {
                return str_contains($host, $domain);
            });

            if (! $isCentral) {
                return response()->view('errors.404', [], 404);
            }

            app()->instance('currentTenant', null);
            $request->attributes->set('tenant', null);

            return $next($request);
        }

        if (! $tenant->isActive()) {
            return response()->view('errors.503', ['message' => 'This site is temporarily unavailable.'], 503);
        }

        app()->instance('currentTenant', $tenant);
        $request->attributes->set('tenant', $tenant);

        config([
            'app.url' => "https://{$tenant->subdomain}." . config('tenancy.central_domains.0'),
            'app.name' => $tenant->name,
        ]);

        return $next($request);
    }
}
