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
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->middleware('permission:dashboard.view')->name('dashboard');
    
    Route::resource('posts', \App\Http\Controllers\Admin\PostController::class)->middleware('permission:posts.create');
    Route::post('categories/reorder', [\App\Http\Controllers\Admin\CategoryController::class, 'reorder'])->middleware('permission:categories.manage')->name('categories.reorder');
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class)->middleware('permission:categories.manage');
    Route::resource('members', \App\Http\Controllers\Admin\MemberController::class)->middleware('permission:users.create');
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->middleware('permission:users.manage');
    Route::get('roles/create', [\App\Http\Controllers\Admin\RoleController::class, 'create'])->middleware('permission:roles.create')->name('roles.create');
    Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class)->except(['create', 'show'])->middleware('permission:roles.manage');
    Route::resource('media', \App\Http\Controllers\Admin\MediaController::class)->only(['index', 'store', 'destroy'])->middleware('permission:media.manage');
    Route::resource('tags', \App\Http\Controllers\Admin\TagController::class)->only(['index', 'create', 'store', 'destroy'])->middleware('permission:tags.manage');
    Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->middleware('permission:settings.manage')->name('settings.index');
    Route::post('settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->middleware('permission:settings.manage')->name('settings.update');
    Route::resource('comments', \App\Http\Controllers\Admin\CommentController::class)->only(['index'])->middleware('permission:comments.manage');
    Route::post('comments/{comment}/approve', [\App\Http\Controllers\Admin\CommentController::class, 'approve'])->middleware('permission:comments.manage')->name('comments.approve');
    
    // Sitemap Generation
    Route::post('sitemap/generate', [\App\Http\Controllers\Admin\SitemapController::class, 'generate'])->middleware('permission:settings.manage')->name('sitemap.generate');
    
    // Widgets & Advertisements
    Route::resource('widgets', \App\Http\Controllers\Admin\WidgetController::class)->only(['index', 'store'])->middleware('permission:menus.manage');
    Route::resource('advertisements', \App\Http\Controllers\Admin\AdvertisementController::class)->only(['index', 'store'])->middleware('permission:ads.manage');
});
