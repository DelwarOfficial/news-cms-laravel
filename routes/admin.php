<?php

use App\Http\Controllers\Admin\ContentPlacementController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\TranslationAdminController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['web', 'auth', 'must.change.password'])->group(function () {
    Route::get('password/change', [\App\Http\Controllers\Admin\PasswordController::class, 'changeForm'])->withoutMiddleware('must.change.password')->name('password.change');
    Route::post('password/update', [\App\Http\Controllers\Admin\PasswordController::class, 'update'])->withoutMiddleware('must.change.password')->name('password.update');
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->middleware('permission:dashboard.view')->name('dashboard');
    Route::post('tinymce/upload', \App\Http\Controllers\Admin\TinyMceUploadController::class)
        ->middleware('permission:posts.create')
        ->name('tinymce.upload');

    Route::resource('posts', \App\Http\Controllers\Admin\PostController::class)->middleware('permission:posts.create');
    Route::post('posts/{post}/clone', [\App\Http\Controllers\Admin\PostController::class, 'clone'])->middleware('permission:posts.create')->name('posts.clone');
    Route::post('posts/translate', [\App\Http\Controllers\Admin\PostTranslationController::class, 'translate'])->middleware('permission:posts.create')->name('posts.translate');
    Route::post('posts/{post}/translate-google', [\App\Http\Controllers\Admin\PostController::class, 'translateWithGoogle'])->middleware('permission:posts.create')->name('posts.translate-google');
    Route::post('categories/reorder', [\App\Http\Controllers\Admin\CategoryController::class, 'reorder'])->middleware('permission:categories.manage')->name('categories.reorder');
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class)->middleware('permission:categories.manage');
    Route::resource('members', \App\Http\Controllers\Admin\MemberController::class)->middleware('permission:users.create');
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->middleware('permission:users.manage');
    Route::get('roles/create', [\App\Http\Controllers\Admin\RoleController::class, 'create'])->middleware('permission:roles.create')->name('roles.create');
    Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class)->except(['create', 'show'])->middleware('permission:roles.manage');
    // Media: allow authors to delete their own uploads via policy; keep listing gated by permission.
    Route::get('media', [\App\Http\Controllers\Admin\MediaController::class, 'index'])
        ->middleware('permission:media.manage')
        ->name('media.index');
    Route::post('media', [\App\Http\Controllers\Admin\MediaController::class, 'store'])
        ->middleware('permission:media.manage')
        ->name('media.store');
    Route::delete('media/{media}', [\App\Http\Controllers\Admin\MediaController::class, 'destroy'])
        ->name('media.destroy');
    Route::resource('tags', \App\Http\Controllers\Admin\TagController::class)->only(['index', 'create', 'store', 'destroy'])->middleware('permission:tags.manage');
    Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->middleware('permission:settings.manage')->name('settings.index');
    Route::post('settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->middleware('permission:settings.manage')->name('settings.update');
    Route::get('settings/export', [\App\Http\Controllers\Admin\SettingController::class, 'export'])->middleware('permission:settings.manage')->name('settings.export');
    Route::post('settings/import', [\App\Http\Controllers\Admin\SettingController::class, 'import'])->middleware('permission:settings.manage')->name('settings.import');
    Route::resource('comments', \App\Http\Controllers\Admin\CommentController::class)->only(['index', 'destroy'])->middleware('permission:comments.manage');
    Route::post('comments/{comment}/approve', [\App\Http\Controllers\Admin\CommentController::class, 'approve'])->middleware('permission:comments.manage')->name('comments.approve');
    Route::post('comments/{comment}/mark-spam', [\App\Http\Controllers\Admin\CommentController::class, 'markSpam'])->middleware('permission:comments.manage')->name('comments.markSpam');

    Route::post('sitemap/generate', [\App\Http\Controllers\Admin\SitemapController::class, 'generate'])->middleware('permission:settings.manage')->name('sitemap.generate');

    Route::get('api-docs', [\App\Http\Controllers\Admin\ApiDocsController::class, 'index'])->middleware('permission:users.manage')->name('api-docs.index');

    Route::prefix('backups')->name('backups.')->middleware('permission:backups.manage')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\BackupController::class, 'index'])->name('index');
        Route::post('/create', [\App\Http\Controllers\Admin\BackupController::class, 'create'])->name('create');
        Route::get('/download/{fileName}', [\App\Http\Controllers\Admin\BackupController::class, 'download'])->name('download');
        Route::match(['delete', 'post'], '/{fileName}', [\App\Http\Controllers\Admin\BackupController::class, 'destroy'])->name('destroy');
    });

    Route::get('api-keys', [\App\Http\Controllers\Admin\ApiKeyController::class, 'index'])->middleware('permission:users.manage')->name('api-keys.index');
    Route::get('api-keys/create', [\App\Http\Controllers\Admin\ApiKeyController::class, 'create'])->middleware('permission:users.manage')->name('api-keys.create');
    Route::post('api-keys', [\App\Http\Controllers\Admin\ApiKeyController::class, 'store'])->middleware('permission:users.manage')->name('api-keys.store');
    Route::post('api-keys/{api_key}/toggle', [\App\Http\Controllers\Admin\ApiKeyController::class, 'toggle'])->middleware('permission:users.manage')->name('api-keys.toggle');
    Route::delete('api-keys/{api_key}', [\App\Http\Controllers\Admin\ApiKeyController::class, 'destroy'])->middleware('permission:users.manage')->name('api-keys.destroy');

    Route::resource('widgets', \App\Http\Controllers\Admin\WidgetController::class)->middleware('permission:menus.manage');
    Route::post('widgets/{widget}/toggle', [\App\Http\Controllers\Admin\WidgetController::class, 'toggle'])->middleware('permission:menus.manage')->name('widgets.toggle');
    Route::resource('advertisements', \App\Http\Controllers\Admin\AdvertisementController::class)->middleware('permission:ads.manage');

    Route::prefix('translations')->name('translations.')->middleware('permission:posts.create')->group(function () {
        Route::get('settings', [TranslationAdminController::class, 'settings'])->name('settings');
        Route::post('settings', [TranslationAdminController::class, 'updateSettings'])->name('settings.update');
        Route::get('bulk', [TranslationAdminController::class, 'bulkTranslateForm'])->name('bulk');
        Route::post('bulk', [TranslationAdminController::class, 'bulkTranslate'])->name('bulk.process');
        Route::post('translate', [TranslationAdminController::class, 'singleTranslate'])->name('translate');
        Route::get('usage', [TranslationAdminController::class, 'usage'])->name('usage');
    });

    Route::prefix('translation')->name('translation.')->middleware('permission:translations.manage')->group(function () {
        Route::resource('providers', \App\Http\Controllers\Admin\Translation\AiProviderController::class)
            ->except(['show']);
        Route::post('providers/{provider}/toggle', [\App\Http\Controllers\Admin\Translation\AiProviderController::class, 'toggle'])
            ->name('providers.toggle');

        Route::resource('prompts', \App\Http\Controllers\Admin\Translation\TranslationPromptController::class)
            ->only(['index', 'edit', 'update']);
    });

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
