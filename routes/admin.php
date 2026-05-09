<?php

use Illuminate\Support\Facades\Route;

// Admin specific routes (if needed separately)
Route::middleware(['auth'])->group(function () {
    // Additional admin routes can be added here
});