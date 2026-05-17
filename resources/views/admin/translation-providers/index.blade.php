@extends('admin.layouts.app')

@section('title', 'AI Providers')
@section('header', 'AI Providers')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    @if(session('success'))
        <div class="alert alert-success"><i class="fas fa-check-circle text-green-500"></i><span>{{ session('success') }}</span></div>
    @endif

    <div class="flex justify-between items-center">
        <p class="text-sm text-gray-500 dark:text-gray-400">Manage AI translation provider configurations.</p>
        <a href="{{ route('admin.translation.providers.create') }}" class="btn-primary px-4 py-2 text-sm font-semibold">
            <i class="fas fa-plus mr-2"></i> Add Provider
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                    <th class="p-4 font-semibold">Name</th>
                    <th class="p-4 font-semibold">Driver</th>
                    <th class="p-4 font-semibold">Model</th>
                    <th class="p-4 font-semibold">Key Status</th>
                    <th class="p-4 font-semibold">Status</th>
                    <th class="p-4 font-semibold">Order</th>
                    <th class="p-4 font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($providers as $provider)
                    <tr class="border-b border-gray-50 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="p-4 font-medium text-gray-900 dark:text-white">{{ $provider->name }}</td>
                        <td class="p-4 text-xs font-mono text-gray-500">{{ class_basename($provider->driver_class) }}</td>
                        <td class="p-4 text-gray-600 dark:text-gray-300">{{ $provider->model ?: '—' }}</td>
                        <td class="p-4">
                            @if($provider->api_key)
                                <span class="badge badge-green">Configured</span>
                            @else
                                <span class="badge badge-amber">Missing</span>
                            @endif
                        </td>
                        <td class="p-4">
                            @if($provider->is_active)
                                <span class="badge badge-green">Active</span>
                            @else
                                <span class="badge badge-red">Inactive</span>
                            @endif
                        </td>
                        <td class="p-4 text-gray-500">{{ $provider->sort_order }}</td>
                        <td class="p-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.translation.providers.edit', $provider) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.translation.providers.toggle', $provider) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-{{ $provider->is_active ? 'amber' : 'green' }}-600 hover:text-{{ $provider->is_active ? 'amber' : 'green' }}-700 text-sm">
                                        <i class="fas fa-{{ $provider->is_active ? 'pause' : 'play' }}"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.translation.providers.destroy', $provider) }}" class="inline" onsubmit="return confirm('Delete this provider?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700 text-sm"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="p-6 text-center text-sm text-gray-400">No AI providers configured. Add one to get started.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Env Fallback</h4>
        <p class="text-xs text-gray-400">When a provider has no API key stored in the database, the system falls back to <code class="text-blue-600">.env</code> variables: <code>DEEPSEEK_API_KEY</code>, <code>OPENAI_API_KEY</code>, <code>CLAUDE_API_KEY</code>, <code>GEMINI_API_KEY</code>.</p>
    </div>
</div>
@endsection
