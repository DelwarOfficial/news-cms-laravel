<?php

use App\Http\Controllers\Front\CategoryController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\NewsController;
use App\Http\Controllers\Front\PostController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

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

// Frontend

$frontend = function (string $suffix = '') {
    $n = $suffix ? fn ($name) => $name . '.' . $suffix : fn ($name) => $name;
    Route::get('/', [HomeController::class, 'index'])->name($n('home'));
    Route::get('/latest', [NewsController::class, 'latest'])->name($n('news.latest'));
    Route::get('/api/photo-story', [HomeController::class, 'photoStoryData'])->name($n('photo-story.data'));
    Route::get('/category/{parentSlug}', [CategoryController::class, 'showParent'])->name($n('category.parent'));
    Route::get('/category/{parentSlug}/{childSlug}', [CategoryController::class, 'showChild'])->name($n('category.child'));
    Route::get('/sitemap.xml', [CategoryController::class, 'sitemap'])->name($n('sitemap'));
    foreach (['article', 'video', 'live', 'gallery', 'opinion'] as $fmt) {
        Route::get("/{$fmt}/{postId}/{slug}", [PostController::class, 'showIdSlug'])
            ->whereNumber('postId')->name($n("{$fmt}.id_slug"));
        Route::get("/{$fmt}/{postId}", [PostController::class, 'showId'])
            ->whereNumber('postId')->name($n("{$fmt}.id"));
        Route::get("/{$fmt}/{slug}/amp", [PostController::class, 'amp'])->name($n("{$fmt}.amp"));
        Route::get("/{$fmt}/{slug}", [PostController::class, 'show'])->name($n("{$fmt}.show"));
    }
    Route::get('/post/{slug}', [PostController::class, 'show'])->name($n('post.show'));
    Route::get('/author/{username}', [\App\Http\Controllers\Front\AuthorController::class, 'show'])->name($n('author.show'));
    Route::get('/api/saradesh/districts', [CategoryController::class, 'districts'])->name($n('saradesh.districts'));
    Route::get('/api/saradesh/upazilas', [CategoryController::class, 'upazilas'])->name($n('saradesh.upazilas'));
    Route::post('/posts/{post}/view', [PostController::class, 'incrementView'])->name($n('posts.view'));
    Route::get('/{postId}', [PostController::class, 'showRootId'])
        ->whereNumber('postId')->name($n('article.root_id'));
    Route::get('/{slug}', [PostController::class, 'show'])
        ->where('slug', '^(?!(admin|api|login|logout|latest|category|article|video|live|gallery|opinion|post|posts|author|en)(/|$)|sitemap\.xml$).+')
        ->name($n('article.slug'));
};

// Bengali (root) — original names
Route::middleware('set.locale')->group(function () use ($frontend) {
    Route::redirect('/en', '/en/', 301);
    $frontend('');
});

// English (/en/) — names suffixed with .en
Route::prefix('en')->middleware('set.locale')->group(function () use ($frontend) {
    $frontend('en');
});

// Auth (outside locale groups — admin handles its own locale)
Route::get('/login', [\App\Http\Controllers\Admin\AuthController::class, 'login'])->name('login');
Route::post('/login', [\App\Http\Controllers\Admin\AuthController::class, 'authenticate'])->name('login.post');
Route::post('/logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');
