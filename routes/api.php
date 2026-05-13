<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\TagApiController;
use App\Http\Controllers\Api\SearchApiController;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\Admin\AdminPostApiController;
use App\Http\Controllers\Api\Admin\AdminMediaApiController;
use App\Http\Controllers\Api\Admin\AdminCommentApiController;
use App\Http\Controllers\Api\V1\Public\PostController;
use App\Http\Controllers\Api\V1\Public\CategoryController;
use App\Http\Controllers\Api\V1\Public\SearchController;

// ===========================================================================
// LEGACY PUBLIC API (backward compatible)
// ===========================================================================
Route::get('/posts', [PostApiController::class, 'index']);
Route::get('/posts/{slug}', [PostApiController::class, 'show']);
Route::get('/categories', [CategoryApiController::class, 'index']);
Route::get('/categories/{slug}/posts', [CategoryApiController::class, 'posts']);
Route::get('/tags/{slug}/posts', [TagApiController::class, 'posts']);
Route::get('/search', [SearchApiController::class, 'index']);
Route::get('/trending', [PostApiController::class, 'trending']);
Route::get('/breaking', [PostApiController::class, 'breaking']);
Route::get('/featured', [PostApiController::class, 'featured']);

// ===========================================================================
// V1 API — versioned, consistent {data, meta} responses
// ===========================================================================
Route::prefix('v1')->group(function () {

    // V1 — API Key authenticated (any valid scope) — registered FIRST to avoid {slug} capture
    Route::middleware(['api.key', 'throttle:120,1'])->group(function () {
        Route::get('/posts/manage', [\App\Http\Controllers\Api\V1\PostManageController::class, 'index']);
        Route::post('/posts/manage', [\App\Http\Controllers\Api\V1\PostManageController::class, 'store']);
        Route::get('/posts/manage/{id}', [\App\Http\Controllers\Api\V1\PostManageController::class, 'show']);
        Route::put('/posts/manage/{id}', [\App\Http\Controllers\Api\V1\PostManageController::class, 'update']);
        Route::delete('/posts/manage/{id}', [\App\Http\Controllers\Api\V1\PostManageController::class, 'destroy']);
        Route::apiResource('/categories/manage', \App\Http\Controllers\Api\V1\CategoryManageController::class);
        Route::apiResource('/tags/manage', \App\Http\Controllers\Api\V1\TagManageController::class);
        Route::get('/media', [\App\Http\Controllers\Api\V1\MediaController::class, 'index']);
        Route::post('/media', [\App\Http\Controllers\Api\V1\MediaController::class, 'store']);
        Route::delete('/media/{id}', [\App\Http\Controllers\Api\V1\MediaController::class, 'destroy']);
        Route::get('/dashboard', [\App\Http\Controllers\Api\V1\DashboardController::class, 'index']);

        // Additional modules
        Route::apiResource('/menus', \App\Http\Controllers\Api\V1\MenuController::class);
        Route::post('/menus/{menu}/items', [\App\Http\Controllers\Api\V1\MenuController::class, 'storeItem'])->name('menus.items.store');
        Route::put('/menus/{menu}/items/{item}', [\App\Http\Controllers\Api\V1\MenuController::class, 'updateItem'])->name('menus.items.update');
        Route::delete('/menus/{menu}/items/{item}', [\App\Http\Controllers\Api\V1\MenuController::class, 'destroyItem'])->name('menus.items.destroy');
        Route::apiResource('/widgets', \App\Http\Controllers\Api\V1\WidgetController::class);
        Route::post('/widgets/{widget}/toggle', [\App\Http\Controllers\Api\V1\WidgetController::class, 'toggle'])->name('widgets.toggle');
        Route::apiResource('/advertisements', \App\Http\Controllers\Api\V1\AdvertisementController::class);
        Route::get('/settings', [\App\Http\Controllers\Api\V1\SettingsController::class, 'index']);
        Route::post('/settings', [\App\Http\Controllers\Api\V1\SettingsController::class, 'update']);
        Route::get('/sitemap', [\App\Http\Controllers\Api\V1\SitemapController::class, 'index']);
        Route::get('/revisions', [\App\Http\Controllers\Api\V1\RevisionController::class, 'index']);
    });

    // V1 — public (no auth, read-only)
    Route::middleware('throttle:60,1')->group(function () {
        Route::get('/posts', [PostController::class, 'index']);
        Route::get('/posts/breaking', [PostController::class, 'breaking']);
        Route::get('/posts/trending', [PostController::class, 'trending']);
        Route::get('/posts/popular', [PostController::class, 'popular']);
        Route::get('/posts/featured', [PostController::class, 'featured']);
        Route::get('/posts/editors-pick', [PostController::class, 'editorsPick']);
        Route::get('/posts/{slug}', [PostController::class, 'show']);
        Route::post('/posts/{id}/view', [PostController::class, 'view'])->middleware('throttle:1,60');
        Route::get('/categories', [CategoryController::class, 'index']);
        Route::get('/categories/{slug}/posts', [CategoryController::class, 'posts']);
        Route::get('/search', [SearchController::class, 'index']);
    });

    // V1 — CMS API (requires cms scope — for external push/sync)
    Route::middleware(['api.key:cms', 'throttle:60,1'])->group(function () {
        Route::get('/cms/status', [\App\Http\Controllers\Api\V1\Cms\StatusController::class, 'index']);
        Route::post('/cms/posts', [\App\Http\Controllers\Api\V1\Cms\PostController::class, 'store']);
        Route::put('/cms/posts/{id}', [\App\Http\Controllers\Api\V1\Cms\PostController::class, 'update']);
        Route::delete('/cms/posts/{id}', [\App\Http\Controllers\Api\V1\Cms\PostController::class, 'destroy']);
        Route::get('/cms/categories', [\App\Http\Controllers\Api\V1\Cms\CategoryController::class, 'index']);
        Route::post('/cms/categories', [\App\Http\Controllers\Api\V1\Cms\CategoryController::class, 'store']);
        Route::get('/cms/tags', [\App\Http\Controllers\Api\V1\Cms\TagController::class, 'index']);
        Route::post('/cms/tags', [\App\Http\Controllers\Api\V1\Cms\TagController::class, 'store']);
        Route::post('/cms/media', [\App\Http\Controllers\Api\V1\Cms\MediaController::class, 'store']);
    });
});

// ===========================================================================
// AUTH (Sanctum — admin login)
// ===========================================================================
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthApiController::class, 'login']);
});

// ===========================================================================
// AUTHENTICATED API (Sanctum session — admin use)
// ===========================================================================
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthApiController::class, 'logout']);
        Route::get('/me', [AuthApiController::class, 'me']);
    });

    Route::prefix('admin')->group(function () {
        Route::post('/posts', [AdminPostApiController::class, 'store']);
        Route::put('/posts/{id}', [AdminPostApiController::class, 'update']);
        Route::delete('/posts/{id}', [AdminPostApiController::class, 'destroy']);
        Route::patch('/posts/{id}/status', [AdminPostApiController::class, 'status']);

        Route::get('/media', [AdminMediaApiController::class, 'index']);
        Route::post('/media/upload', [AdminMediaApiController::class, 'store']);
        Route::delete('/media/{id}', [AdminMediaApiController::class, 'destroy']);

        Route::get('/comments', [AdminCommentApiController::class, 'index']);
        Route::patch('/comments/{id}', [AdminCommentApiController::class, 'status']);
    });
});
