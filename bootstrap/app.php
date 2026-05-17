<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            require __DIR__.'/../routes/admin.php';
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\SetAdminLocale::class,
            \App\Http\Middleware\SecurityHeaders::class,
            \App\Http\Middleware\RequestId::class,
            \App\Http\Middleware\IdentifyTenant::class,
        ]);
        $middleware->api(append: [
            \App\Http\Middleware\RequestId::class,
            \App\Http\Middleware\IdentifyTenant::class,
        ]);
        $middleware->alias([
            'set.locale' => \App\Http\Middleware\SetLocale::class,
            'tenant' => \App\Http\Middleware\IdentifyTenant::class,
            'request.id' => \App\Http\Middleware\RequestId::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'api.key' => \App\Http\Middleware\ApiKeyMiddleware::class,
            'api.scope' => \App\Http\Middleware\CheckApiScope::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Forbidden',
                    'message' => $e->getMessage() ?: 'You are not allowed to perform this action.',
                ], 403);
            }

            $fallback = url('/admin');
            $target = url()->previous() ?: $fallback;

            return redirect()->to($target)->with('error', $e->getMessage() ?: 'You are not allowed to perform this action.');
        });
    })->create();
