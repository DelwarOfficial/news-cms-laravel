<?php

use Illuminate\Support\Facades\Route;

// Frontend
Route::get('/', [\App\Http\Controllers\Front\HomeController::class, 'index'])->name('home');
Route::get('/post/{slug}', [\App\Http\Controllers\Front\PostController::class, 'show'])->name('post.show');
Route::get('/author/{username}', [\App\Http\Controllers\Front\AuthorController::class, 'show'])->name('author.show');

// Auth
Route::get('/login', [\App\Http\Controllers\Admin\AuthController::class, 'login'])->name('login');
Route::post('/login', [\App\Http\Controllers\Admin\AuthController::class, 'authenticate'])->name('login.post');
Route::post('/logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');
// Admin
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('posts', \App\Http\Controllers\Admin\PostController::class);
    Route::post('categories/reorder', [\App\Http\Controllers\Admin\CategoryController::class, 'reorder'])->name('categories.reorder');
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::resource('media', \App\Http\Controllers\Admin\MediaController::class)->only(['index', 'store', 'destroy']);
    Route::resource('tags', \App\Http\Controllers\Admin\TagController::class)->only(['index', 'store']);
    Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
    Route::resource('comments', \App\Http\Controllers\Admin\CommentController::class)->only(['index']);
    Route::post('comments/{comment}/approve', [\App\Http\Controllers\Admin\CommentController::class, 'approve'])->name('comments.approve');
    
    // Sitemap Generation
    Route::post('sitemap/generate', [\App\Http\Controllers\Admin\SitemapController::class, 'generate'])->name('sitemap.generate');
    
    // Widgets & Advertisements
    Route::resource('widgets', \App\Http\Controllers\Admin\WidgetController::class)->only(['index', 'store']);
    Route::resource('advertisements', \App\Http\Controllers\Admin\AdvertisementController::class)->only(['index', 'store']);
});