<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BackupController extends Controller
{
    public function index()
    {
        $this->authorize('backups.manage');

        $disk = config('backup.backup.destination.disks')[0] ?? 'local';
        $files = collect(Storage::disk($disk)->files(config('backup.backup.name')))
            ->filter(fn ($path) => Str::endsWith($path, '.zip'))
            ->map(fn ($path) => [
                'path' => $path,
                'name' => basename($path),
                'size' => Storage::disk($disk)->size($path),
                'size_for_humans' => $this->humanSize(Storage::disk($disk)->size($path)),
                'last_modified' => Storage::disk($disk)->lastModified($path),
                'date' => date('Y-m-d H:i:s', Storage::disk($disk)->lastModified($path)),
            ])
            ->sortByDesc('last_modified')
            ->values();

        $settings = Cache::remember('backup.settings', 60, function () {
            return \App\Models\Setting::whereIn('key', [
                'backup_disk', 'backup_retention_days', 'backup_auto_enabled',
                'backup_frequency', 'backup_include_media',
            ])->pluck('value', 'key');
        });

        return view('admin.backups.index', compact('files', 'settings'));
    }

    public function create()
    {
        $this->authorize('backups.manage');

        $includeMedia = \App\Models\Setting::where('key', 'backup_include_media')->value('value') === '1';

        $run = Artisan::queue('backup:run', [
            '--only-db' => ! $includeMedia,
        ]);

        Log::info('Backup initiated', ['user_id' => auth()->id()]);

        return back()->with('success', 'Backup started in the background. Refresh the page in a moment to see the new file.');
    }

    public function download($fileName)
    {
        $this->authorize('backups.manage');

        $disk = config('backup.backup.destination.disks')[0] ?? 'local';
        $path = config('backup.backup.name') . '/' . $fileName;

        abort_unless(Storage::disk($disk)->exists($path), 404);

        Log::info('Backup downloaded', ['user_id' => auth()->id(), 'file' => $fileName]);

        return Storage::disk($disk)->download($path);
    }

    public function destroy($fileName)
    {
        $this->authorize('backups.manage');

        $disk = config('backup.backup.destination.disks')[0] ?? 'local';
        $path = config('backup.backup.name') . '/' . $fileName;

        abort_unless(Storage::disk($disk)->exists($path), 404);

        Storage::disk($disk)->delete($path);

        Log::info('Backup deleted', ['user_id' => auth()->id(), 'file' => $fileName]);

        return back()->with('success', "Backup \"{$fileName}\" deleted.");
    }

    private function humanSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
