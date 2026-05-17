<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\SetAdminLocale::class,
            \App\Http\Middleware\SecurityHeaders::class,
            \App\Http\Middleware\RequestId::class,
            \App\Http\Middleware\IdentifyTenant::class,
        ]);
        $middleware->api(append: [
            \App\Http\Middleware\SetLocale::class,
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
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthorizationException $e, Request $request): Response {
            if (isV1ApiRequest($request)) {
                return apiErrorResponse('Forbidden', $e->getMessage() ?: 'You are not allowed to perform this action.', 403);
            }

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

        $exceptions->render(function (AuthenticationException $e, Request $request): ?Response {
            if (! isV1ApiRequest($request)) {
                return null;
            }

            return apiErrorResponse('Unauthenticated', 'Authentication is required.', 401);
        });

        $exceptions->render(function (ValidationException $e, Request $request): ?Response {
            if (! isV1ApiRequest($request)) {
                return null;
            }

            return apiErrorResponse('Validation Error', 'The given data was invalid.', $e->status, $e->errors());
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request): ?Response {
            if (! isV1ApiRequest($request)) {
                return null;
            }

            return apiErrorResponse('Not Found', 'Resource not found.', 404);
        });

        $exceptions->render(function (Throwable $e, Request $request): ?Response {
            if (! isV1ApiRequest($request)) {
                return null;
            }

            $status = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;
            $message = $status >= 500
                ? 'Server Error'
                : ($e->getMessage() ?: Response::$statusTexts[$status] ?? 'Error');

            return apiErrorResponse(Response::$statusTexts[$status] ?? 'Error', $message, $status);
        });
    })->create();

function isV1ApiRequest(Request $request): bool
{
    return $request->is('api/v1') || $request->is('api/v1/*');
}

function apiErrorResponse(string $code, string $message, int $status, mixed $details = null): Response
{
    $error = [
        'code' => $code,
        'message' => $message,
    ];

    if ($details !== null) {
        $error['details'] = $details;
    }

    return response()->json([
        'data' => null,
        'meta' => [],
        'error' => $error,
    ], $status);
}
