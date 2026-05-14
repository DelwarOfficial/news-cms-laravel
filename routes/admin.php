<?php

use App\Http\Controllers\Admin\ContentPlacementController;
use App\Http\Controllers\Admin\LocationController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['web', 'auth'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->middleware('permission:dashboard.view')->name('dashboard');

    Route::resource('posts', \App\Http\Controllers\Admin\PostController::class)->middleware('permission:posts.create');
    Route::post('posts/{post}/clone', [\App\Http\Controllers\Admin\PostController::class, 'clone'])->middleware('permission:posts.create')->name('posts.clone');
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
    Route::get('settings/export', [\App\Http\Controllers\Admin\SettingController::class, 'export'])->middleware('permission:settings.manage')->name('settings.export');
    Route::post('settings/import', [\App\Http\Controllers\Admin\SettingController::class, 'import'])->middleware('permission:settings.manage')->name('settings.import');
    Route::resource('comments', \App\Http\Controllers\Admin\CommentController::class)->only(['index'])->middleware('permission:comments.manage');
    Route::post('comments/{comment}/approve', [\App\Http\Controllers\Admin\CommentController::class, 'approve'])->middleware('permission:comments.manage')->name('comments.approve');

    Route::post('sitemap/generate', [\App\Http\Controllers\Admin\SitemapController::class, 'generate'])->middleware('permission:settings.manage')->name('sitemap.generate');

    Route::get('api-docs', [\App\Http\Controllers\Admin\ApiDocsController::class, 'index'])->middleware('permission:users.manage')->name('api-docs.index');

    Route::prefix('backups')->name('backups.')->middleware('permission:backups.manage')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\BackupController::class, 'index'])->name('index');
        Route::post('/create', [\App\Http\Controllers\Admin\BackupController::class, 'create'])->name('create');
        Route::get('/download/{fileName}', [\App\Http\Controllers\Admin\BackupController::class, 'download'])->name('download');
        Route::delete('/{fileName}', [\App\Http\Controllers\Admin\BackupController::class, 'destroy'])->name('destroy');
    });

    Route::get('api-keys', [\App\Http\Controllers\Admin\ApiKeyController::class, 'index'])->middleware('permission:users.manage')->name('api-keys.index');
    Route::get('api-keys/create', [\App\Http\Controllers\Admin\ApiKeyController::class, 'create'])->middleware('permission:users.manage')->name('api-keys.create');
    Route::post('api-keys', [\App\Http\Controllers\Admin\ApiKeyController::class, 'store'])->middleware('permission:users.manage')->name('api-keys.store');
    Route::post('api-keys/{api_key}/toggle', [\App\Http\Controllers\Admin\ApiKeyController::class, 'toggle'])->middleware('permission:users.manage')->name('api-keys.toggle');
    Route::delete('api-keys/{api_key}', [\App\Http\Controllers\Admin\ApiKeyController::class, 'destroy'])->middleware('permission:users.manage')->name('api-keys.destroy');

    Route::resource('widgets', \App\Http\Controllers\Admin\WidgetController::class)->middleware('permission:menus.manage');
    Route::post('widgets/{widget}/toggle', [\App\Http\Controllers\Admin\WidgetController::class, 'toggle'])->middleware('permission:menus.manage')->name('widgets.toggle');
    Route::resource('advertisements', \App\Http\Controllers\Admin\AdvertisementController::class)->middleware('permission:ads.manage');

    Route::post('locale/switch', function (\Illuminate\Http\Request $request) {
        $locale = $request->input('locale');
        if (in_array($locale, ['en', 'bn'])) {
            session(['admin_locale' => $locale]);
        }
        return redirect()->back();
    })->name('locale.switch');

    Route::resource('placements', ContentPlacementController::class)
        ->only(['index', 'store', 'edit', 'update', 'destroy'])
        ->middleware('permission:posts.create');

    Route::get('locations', [LocationController::class, 'index'])
        ->middleware('permission:categories.manage')
        ->name('locations.index');

    Route::post('locations/divisions', [LocationController::class, 'storeDivision'])
        ->middleware('permission:categories.manage')
        ->name('locations.divisions.store');
    Route::delete('locations/divisions/{division}', [LocationController::class, 'destroyDivision'])
        ->middleware('permission:categories.manage')
        ->name('locations.divisions.destroy');

    Route::post('locations/districts', [LocationController::class, 'storeDistrict'])
        ->middleware('permission:categories.manage')
        ->name('locations.districts.store');
    Route::delete('locations/districts/{district}', [LocationController::class, 'destroyDistrict'])
        ->middleware('permission:categories.manage')
        ->name('locations.districts.destroy');

    Route::post('locations/upazilas', [LocationController::class, 'storeUpazila'])
        ->middleware('permission:categories.manage')
        ->name('locations.upazilas.store');
    Route::delete('locations/upazilas/{upazila}', [LocationController::class, 'destroyUpazila'])
        ->middleware('permission:categories.manage')
        ->name('locations.upazilas.destroy');
});
