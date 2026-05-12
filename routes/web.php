<?php

use App\Http\Controllers\Front\CategoryController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\NewsController;
use App\Http\Controllers\Front\PostController;
use Illuminate\Support\Facades\Route;

// Frontend
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/latest', [NewsController::class, 'latest'])->name('news.latest');
Route::get('/api/photo-story', [HomeController::class, 'photoStoryData'])->name('photo-story.data');
Route::get('/category/{parentSlug}', [CategoryController::class, 'showParent'])->name('category.parent');
Route::get('/category/{parentSlug}/{childSlug}', [CategoryController::class, 'showChild'])->name('category.child');
Route::get('/sitemap.xml', [CategoryController::class, 'sitemap'])->name('sitemap');
Route::get('/article/{slug}', [PostController::class, 'show'])->name('article.show');
Route::get('/post/{slug}', [PostController::class, 'show'])->name('post.show');
Route::get('/author/{username}', [\App\Http\Controllers\Front\AuthorController::class, 'show'])->name('author.show');
Route::get('/api/saradesh/districts', [CategoryController::class, 'districts'])->name('saradesh.districts');
Route::get('/api/saradesh/upazilas', [CategoryController::class, 'upazilas'])->name('saradesh.upazilas');
Route::post('/posts/{post}/view', [PostController::class, 'incrementView'])->name('posts.view');
Route::get('/{slug}', [PostController::class, 'show'])
    ->where('slug', '^(?!admin$|api$|login$|logout$|latest$|category$|article$|post$|posts$|author$|sitemap\.xml$).+')
    ->name('article.slug');

// Auth
Route::get('/login', [\App\Http\Controllers\Admin\AuthController::class, 'login'])->name('login');
Route::post('/login', [\App\Http\Controllers\Admin\AuthController::class, 'authenticate'])->name('login.post');
Route::post('/logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');
