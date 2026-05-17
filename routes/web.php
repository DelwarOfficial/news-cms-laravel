<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Support\Monitoring\HealthCheck;

Route::get('/vendor/rich-text-laravel/{file}', function (string $file) {
    abort_if(str_contains($file, '..') || str_contains($file, '/'), 404);

    $path = public_path('vendor/rich-text-laravel/'.$file);

    abort_unless(is_file($path), 404);

    $contentType = str_ends_with($file, '.js')
        ? 'application/javascript; charset=utf-8'
        : (str_ends_with($file, '.css') ? 'text/css; charset=utf-8' : 'application/octet-stream');

    return response(file_get_contents($path), 200)
        ->header('Content-Type', $contentType)
        ->header('Cache-Control', 'public, max-age=31536000');
})->where('file', '[A-Za-z0-9._-]+');

Route::get('/storage/{path}', function (string $path) {
    abort_if(str_contains($path, '..'), 404);

    abort_unless(Storage::disk('public')->exists($path), 404);

    return response(Storage::disk('public')->get($path), 200)
        ->header('Content-Type', Storage::disk('public')->mimeType($path) ?: 'application/octet-stream')
        ->header('Cache-Control', 'public, max-age=31536000');
})->where('path', '.*');

// Frontend article preview (used by admin panel "View" links)
Route::get('/article/{postId}/{slug}', function (int $postId, string $slug) {
    $frontendUrl = rtrim((string) config('app.frontend_url', 'http://localhost:3000'), '/');

    return redirect()->to($frontendUrl.'/article/'.rawurlencode($slug));
})->name('article.id_slug');

// Admin authentication
Route::get('/login', [\App\Http\Controllers\Admin\AuthController::class, 'login'])->name('login');
Route::post('/login', [\App\Http\Controllers\Admin\AuthController::class, 'authenticate'])->name('login.post');
Route::post('/logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');

// Infrastructure health check for load balancers/uptime monitors.
Route::get('/healthz', function () {
    $token = config('monitoring.health_token');
    if ($token && request()->header('X-Health-Token') !== $token) {
        abort(404);
    }

    $result = HealthCheck::run();

    return response()->json([
        'status' => $result['ok'] ? 'ok' : 'degraded',
        'checks' => $result['checks'],
        'errors' => $result['errors'],
        'request_id' => request()->attributes->get('request_id'),
    ], $result['ok'] ? 200 : 503);
});
