<?php

use App\Support\ViewCounter;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Sync Redis view counters to database every 5 minutes
Schedule::call(function () {
    app(ViewCounter::class)->syncAll();
})->everyFiveMinutes()->name('view-counter-sync')->onOneServer();

// Warm critical caches every 15 minutes
Schedule::command('cache:warm')
    ->everyFifteenMinutes()
    ->name('cache-warm')
    ->onOneServer();

// Publish scheduled posts every minute
Schedule::command('posts:publish-scheduled')->everyMinute()->withoutOverlapping();

// Scheduled database backups
Schedule::call(function () {
    $enabled = \App\Models\Setting::where('key', 'backup_auto_enabled')->value('value');
    if ($enabled !== '1') {
        return;
    }

    $includeMedia = \App\Models\Setting::where('key', 'backup_include_media')->value('value') === '1';
    $flags = ['--only-db' => ! $includeMedia];

    Artisan::queue('backup:run', $flags);
})->when(function () {
    $frequency = \App\Models\Setting::where('key', 'backup_frequency')->value('value') ?: 'daily';
    $enabled = \App\Models\Setting::where('key', 'backup_auto_enabled')->value('value');

    if ($enabled !== '1') {
        return false;
    }

    return match ($frequency) {
        'weekly' => now()->isMonday(),
        'monthly' => now()->day === 1,
        default => true, // daily
    };
})->dailyAt('02:00')->name('scheduled-backup')->onOneServer();
