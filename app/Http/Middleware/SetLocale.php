<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->segment(1) === 'en' ? 'en' : 'bn';

        // ?lang=en fallback — redirect to /en/ prefix
        if ($request->has('lang') && in_array($request->get('lang'), ['en', 'bn'])) {
            $targetLocale = $request->get('lang');
            if ($targetLocale !== $locale) {
                $path = $request->path();
                $query = $request->except('lang');
                $url = $targetLocale === 'en' ? url('/en/' . $path) : url('/' . $path);
                if (! empty($query)) {
                    $url .= '?' . http_build_query($query);
                }
                return redirect($url, 302);
            }
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
