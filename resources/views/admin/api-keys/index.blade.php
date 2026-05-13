@extends('admin.layouts.app')
@section('title', 'API Keys')
@section('page-title', 'API Keys')

@section('content')
@if(session('api_key'))
    <div class="mb-6 rounded-2xl border border-amber-200 bg-amber-50 p-5">
        <div class="flex items-center gap-2 text-amber-800 font-bold mb-2">
            <i class="fas fa-key"></i> New API Key Created
        </div>
        <p class="text-sm text-amber-700 mb-2">Copy this key now. It will not be shown again.</p>
        <div class="bg-white border border-amber-300 rounded-xl px-4 py-3 font-mono text-sm select-all break-all">
            {{ session('api_key') }}
        </div>
    </div>
@endif

<div class="flex justify-end mb-6">
    <a href="{{ route('admin.api-keys.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-semibold text-sm flex items-center gap-2">
        <i class="fas fa-plus"></i> Create API Key
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Name</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Prefix</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Scopes</th>
                <th class="text-left px-6 py-3.5 font-semibold text-gray-600">Owner</th>
                <th class="text-center px-6 py-3.5 font-semibold text-gray-600">Status</th>
                <th class="text-right px-6 py-3.5 font-semibold text-gray-600">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($keys as $key)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $key->name }}</td>
                    <td class="px-6 py-4 font-mono text-xs text-gray-500">{{ $key->key_prefix }}...</td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-1">
                            @foreach($key->scopes ?? [] as $scope)
                                <span class="inline-flex rounded-full bg-blue-50 text-blue-700 px-2 py-0.5 text-xs font-semibold">{{ $scope }}</span>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $key->user?->name ?? '—' }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold {{ $key->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $key->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        @if($key->expires_at)
                            <div class="text-xs text-gray-400 mt-1">Exp: {{ $key->expires_at->format('M d, Y') }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <form method="POST" action="{{ route('admin.api-keys.toggle', $key) }}" class="inline">
                                @csrf
                                <button class="text-amber-600 hover:text-amber-800 p-1.5 rounded-lg hover:bg-amber-50">
                                    <i class="fas {{ $key->is_active ? 'fa-eye-slash' : 'fa-eye' }} text-xs"></i>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.api-keys.destroy', $key) }}" class="inline" onsubmit="return confirm('Revoke this API key? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:text-red-700 p-1.5 rounded-lg hover:bg-red-50">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-6 py-10 text-center text-gray-400">No API keys yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if(method_exists($keys, 'links'))
        <div class="border-t border-gray-100 px-6 py-4">{{ $keys->links() }}</div>
    @endif
</div>
@endsection
