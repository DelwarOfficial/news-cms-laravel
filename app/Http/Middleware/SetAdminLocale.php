<?php

namespace App\Http\Middleware;

use App\Support\Locale;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetAdminLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->has('admin_locale')) {
            $locale = $request->get('admin_locale');
            if (Locale::isSupported($locale)) {
                session(['admin_locale' => Locale::normalize($locale)]);
            }
        }

        $locale = Locale::normalize(session('admin_locale', Locale::default()));
        App::setLocale($locale);

        return $next($request);
    }
}
