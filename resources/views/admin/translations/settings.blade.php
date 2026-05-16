@extends('admin.layouts.app')

@section('title', 'AI Translation Settings')
@section('header', 'AI Translation Settings')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">

    {{-- AI Provider Settings --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">AI Translation Provider</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Configure which AI service powers your Bengali→English translations.</p>

        <form method="POST" action="{{ route('admin.translations.settings.update') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Provider</label>
                <select name="provider" class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 px-4 py-2.5 text-sm">
                    @foreach($providers as $p)
                        <option value="{{ $p }}" @selected($p === $currentProvider)>{{ ucfirst($p) }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-400 mt-1">DeepSeek, OpenAI, Grok (xAI), Claude (Anthropic), or Gemini (Google)</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">API Key <span class="text-gray-400 font-normal">(shared/default)</span></label>
                    <input type="password" name="api_key" placeholder="sk-..." value="{{ old('api_key') }}"
                           class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Claude API Key</label>
                    <input type="password" name="claude_api_key" placeholder="sk-ant-..." value="{{ old('claude_api_key') }}"
                           class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 px-4 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Gemini API Key</label>
                    <input type="password" name="gemini_api_key" placeholder="AIza..." value="{{ old('gemini_api_key') }}"
                           class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 px-4 py-2.5 text-sm">
                </div>
            </div>

            <div class="pt-4 border-t border-gray-100 dark:border-gray-700">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Google Translate (Fallback)</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Project ID</label>
                        <input type="text" name="google_project_id" value="{{ old('google_project_id', config('google_translate.project_id')) }}"
                               class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 px-4 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Key File Path</label>
                        <input type="text" name="google_key_path" value="{{ old('google_key_path', config('google_translate.key_file_path')) }}"
                               class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 px-4 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Monthly Character Limit</label>
                        <input type="number" name="monthly_limit" value="{{ old('monthly_limit', $monthlyLimit) }}"
                               class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 px-4 py-2.5 text-sm">
                        <p class="text-xs text-gray-400 mt-1">Used: {{ number_format($monthlyChars) }} / {{ $monthlyLimit > 0 ? number_format($monthlyLimit) : '∞' }}</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="btn-primary px-6 py-2.5 text-sm font-semibold">
                    <i class="fas fa-save mr-2"></i> Save Settings
                </button>
            </div>
        </form>
    </div>

    {{-- Recent Translation Usage --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Recent Translation Activity</h3>
            <a href="{{ route('admin.translations.usage') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                View All <i class="fas fa-arrow-right ml-1 text-xs"></i>
            </a>
        </div>

        @if($usage->isEmpty())
            <p class="text-sm text-gray-400 py-4 text-center">No translation activity yet.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700">
                            <th class="pb-3 font-semibold">Date</th>
                            <th class="pb-3 font-semibold">Direction</th>
                            <th class="pb-3 font-semibold">Chars</th>
                            <th class="pb-3 font-semibold">Jobs</th>
                            <th class="pb-3 font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($usage as $row)
                            <tr class="border-b border-gray-50 dark:border-gray-700/50">
                                <td class="py-2.5 text-gray-700 dark:text-gray-300">{{ $row->date }}</td>
                                <td class="py-2.5">{{ $row->from_locale }} → {{ $row->to_locale }}</td>
                                <td class="py-2.5 font-mono text-xs">{{ number_format($row->total_chars) }}</td>
                                <td class="py-2.5">{{ $row->total_jobs }}</td>
                                <td class="py-2.5">
                                    @if($row->status === 'completed')
                                        <span class="badge badge-green">Completed</span>
                                    @else
                                        <span class="badge badge-red">Failed</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
