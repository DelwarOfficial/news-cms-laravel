<?php

namespace App\Http\Middleware;

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
            if (in_array($locale, ['en', 'bn'])) {
                session(['admin_locale' => $locale]);
            }
        }

        $locale = session('admin_locale', config('app.locale', 'bn'));
        App::setLocale($locale);

        return $next($request);
    }
}
