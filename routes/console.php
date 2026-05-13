<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

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
