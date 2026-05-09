<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::apiResource('posts', \App\Http\Controllers\Api\PostApiController::class)->only(['index', 'show']);
});