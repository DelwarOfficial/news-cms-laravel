@extends('admin.layouts.app')
@section('title', 'Database Backups')
@section('page-title', 'Database Backups')

@section('header-actions')
    <form method="POST" action="{{ route('admin.backups.create') }}" class="inline">
        @csrf
        <button type="submit" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition-colors" onclick="this.disabled=true; this.innerHTML='<i class=\'fas fa-spinner fa-spin\'></i> Backing up...'; this.form.submit();">
            <i class="fas fa-database"></i> Backup Now
        </button>
    </form>
@endsection

@section('content')
<div class="space-y-6">
    {{-- Info Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-database text-blue-600 text-xl"></i>
            </div>
            <div>
                <div class="text-2xl font-bold">{{ $files->count() }}</div>
                <div class="text-sm text-gray-500">Total Backups</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-clock text-green-600 text-xl"></i>
            </div>
            <div>
                <div class="text-sm font-bold text-green-700">
                    {{ $settings->get('backup_auto_enabled') === '1' ? 'Enabled' : 'Disabled' }}
                </div>
                <div class="text-sm text-gray-500">Auto Backup</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-calendar-alt text-amber-600 text-xl"></i>
            </div>
            <div>
                <div class="text-sm font-bold">{{ $settings->get('backup_frequency', 'daily') }}</div>
                <div class="text-sm text-gray-500">Frequency</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="fas fa-trash-alt text-purple-600 text-xl"></i>
            </div>
            <div>
                <div class="text-sm font-bold">{{ $settings->get('backup_retention_days', '7') }} days</div>
                <div class="text-sm text-gray-500">Retention</div>
            </div>
        </div>
    </div>

    {{-- Backup Files Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-base font-bold text-gray-900">Backup Archives</h2>
            <span class="text-xs text-gray-400">{{ $files->count() }} file(s)</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-6 py-3.5 font-semibold text-gray-600">File Name</th>
                        <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Size</th>
                        <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Date</th>
                        <th class="text-right px-6 py-3.5 font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($files as $file)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center">
                                        <i class="fas fa-file-archive text-blue-500"></i>
                                    </div>
                                    <div class="font-medium text-gray-900 text-sm break-all">{{ $file['name'] }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-500">{{ $file['size_for_humans'] }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ $file['date'] }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.backups.download', $file['name']) }}"
                                       class="text-blue-600 hover:text-blue-800 p-2 rounded-lg hover:bg-blue-50 transition-colors" title="Download">
                                        <i class="fas fa-download text-xs"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.backups.destroy', $file['name']) }}"
                                          onsubmit="return confirm('Delete backup {{ $file['name'] }}?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 p-2 rounded-lg hover:bg-red-50 transition-colors" title="Delete">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-14 text-center">
                                <div class="mx-auto w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center text-gray-400 mb-3">
                                    <i class="fas fa-file-archive"></i>
                                </div>
                                <div class="font-semibold text-gray-700">No backups yet</div>
                                <div class="text-sm text-gray-400 mt-1">Click "Backup Now" to create your first database backup.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
