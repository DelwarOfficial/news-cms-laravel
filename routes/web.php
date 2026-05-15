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
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/latest', [NewsController::class, 'latest'])->name('news.latest');
Route::get('/api/photo-story', [HomeController::class, 'photoStoryData'])->name('photo-story.data');
Route::get('/category/{parentSlug}', [CategoryController::class, 'showParent'])->name('category.parent');
Route::get('/category/{parentSlug}/{childSlug}', [CategoryController::class, 'showChild'])->name('category.child');
Route::get('/sitemap.xml', [CategoryController::class, 'sitemap'])->name('sitemap');
foreach (['article', 'video', 'live', 'gallery', 'opinion'] as $fmt) {
    Route::get("/{$fmt}/{postId}/{slug}", [PostController::class, 'showIdSlug'])
        ->whereNumber('postId')
        ->name("{$fmt}.id_slug");
    Route::get("/{$fmt}/{postId}", [PostController::class, 'showId'])
        ->whereNumber('postId')
        ->name("{$fmt}.id");
    Route::get("/{$fmt}/{slug}/amp", [PostController::class, 'amp'])->name("{$fmt}.amp");
    Route::get("/{$fmt}/{slug}", [PostController::class, 'show'])->name("{$fmt}.show");
}
Route::get('/post/{slug}', [PostController::class, 'show'])->name('post.show');
Route::get('/author/{username}', [\App\Http\Controllers\Front\AuthorController::class, 'show'])->name('author.show');
Route::get('/api/saradesh/districts', [CategoryController::class, 'districts'])->name('saradesh.districts');
Route::get('/api/saradesh/upazilas', [CategoryController::class, 'upazilas'])->name('saradesh.upazilas');
Route::post('/posts/{post}/view', [PostController::class, 'incrementView'])->name('posts.view');
Route::get('/{postId}', [PostController::class, 'showRootId'])
    ->whereNumber('postId')
    ->name('article.root_id');
Route::get('/{slug}', [PostController::class, 'show'])
    ->where('slug', '^(?!(admin|api|login|logout|latest|category|article|video|live|gallery|opinion|post|posts|author)(/|$)|sitemap\.xml$).+')
    ->name('article.slug');

// Auth
Route::get('/login', [\App\Http\Controllers\Admin\AuthController::class, 'login'])->name('login');
Route::post('/login', [\App\Http\Controllers\Admin\AuthController::class, 'authenticate'])->name('login.post');
Route::post('/logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');
