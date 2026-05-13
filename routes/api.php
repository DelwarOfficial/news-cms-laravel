<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\TagApiController;
use App\Http\Controllers\Api\SearchApiController;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\Admin\AdminPostApiController;
use App\Http\Controllers\Api\Admin\AdminMediaApiController;
use App\Http\Controllers\Api\Admin\AdminCommentApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// PUBLIC ENDPOINTS
Route::get('/posts', [PostApiController::class, 'index']);
Route::get('/posts/{slug}', [PostApiController::class, 'show']);
Route::get('/categories', [CategoryApiController::class, 'index']);
Route::get('/categories/{slug}/posts', [CategoryApiController::class, 'posts']);
Route::get('/tags/{slug}/posts', [TagApiController::class, 'posts']);
Route::get('/search', [SearchApiController::class, 'index']);
Route::get('/trending', [PostApiController::class, 'trending']);
Route::get('/breaking', [PostApiController::class, 'breaking']);
Route::get('/featured', [PostApiController::class, 'featured']);

// V1 PUBLIC API - with PostResource and throttle
Route::prefix('v1')->middleware('throttle:60,1')->group(function () {
    Route::get('/posts', [PostApiController::class, 'index']);
    Route::get('/posts/breaking', [PostApiController::class, 'breaking']);
    Route::get('/posts/trending', [PostApiController::class, 'trending']);
    Route::get('/posts/popular', [PostApiController::class, 'popular']);
    Route::get('/posts/{slug}', [PostApiController::class, 'show']);
    Route::post('/posts/{id}/view', [PostApiController::class, 'view'])->middleware('throttle:1,60');
    Route::get('/categories', [CategoryApiController::class, 'index']);
    Route::get('/categories/{slug}/posts', [CategoryApiController::class, 'posts']);
});

// AUTH
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthApiController::class, 'login']);
});

// AUTHENTICATED API
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth actions
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthApiController::class, 'logout']);
        Route::get('/me', [AuthApiController::class, 'me']);
    });
    
    // Admin actions
    Route::prefix('admin')->group(function () {
        
        // Posts
        Route::post('/posts', [AdminPostApiController::class, 'store']);
        Route::put('/posts/{id}', [AdminPostApiController::class, 'update']);
        Route::delete('/posts/{id}', [AdminPostApiController::class, 'destroy']);
        Route::patch('/posts/{id}/status', [AdminPostApiController::class, 'status']);
        
        // Media
        Route::get('/media', [AdminMediaApiController::class, 'index']);
        Route::post('/media/upload', [AdminMediaApiController::class, 'store']);
        Route::delete('/media/{id}', [AdminMediaApiController::class, 'destroy']);
        
        // Comments
        Route::get('/comments', [AdminCommentApiController::class, 'index']);
        Route::patch('/comments/{id}', [AdminCommentApiController::class, 'status']);
        
    });
});