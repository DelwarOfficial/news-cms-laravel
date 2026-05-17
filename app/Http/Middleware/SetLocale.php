<?php

namespace App\Http\Middleware;

use App\Support\Locale;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolveLocale($request);

        App::setLocale($locale);

        if ($request->hasSession()) {
            $request->session()->put('locale', $locale);
        }

        return $next($request);
    }

    private function resolveLocale(Request $request): string
    {
        $segmentLocale = $request->segment(1);
        if (Locale::isSupported($segmentLocale) && $segmentLocale !== Locale::default()) {
            return Locale::normalize($segmentLocale);
        }

        $queryLocale = $request->query('locale');
        if (Locale::isSupported($queryLocale)) {
            return Locale::normalize($queryLocale);
        }

        $headerLocale = $request->header('X-Locale');
        if (Locale::isSupported($headerLocale)) {
            return Locale::normalize($headerLocale);
        }

        return Locale::default();
    }
}
