@extends('admin.layouts.app')

@section('title', 'Bulk Translation')
@section('header', 'Bulk Translation')

@section('content')
<div class="max-w-5xl mx-auto space-y-8">

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle text-green-500"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Translate Selected --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Translate Selected Posts</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Select posts below and choose your translation method.</p>

        <form method="POST" action="{{ route('admin.translations.bulk.process') }}" id="bulk-form">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">From</label>
                    <select name="from" class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 px-4 py-2.5 text-sm">
                        <option value="bn">Bengali</option>
                        <option value="en">English</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">To</label>
                    <select name="to" class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 px-4 py-2.5 text-sm">
                        <option value="en">English</option>
                        <option value="bn">Bengali</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Method</label>
                    <select name="method" class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 px-4 py-2.5 text-sm">
                        <option value="ai">AI (current provider)</option>
                        <option value="google">Google Translate</option>
                        <option value="ai_then_google" selected>AI → Google Fallback (queued)</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600">
                        <span class="text-gray-700 dark:text-gray-300 font-medium">Select All</span>
                    </label>
                    <span class="text-xs text-gray-400" id="selected-count">0 selected</span>
                </div>
                <button type="submit" class="btn-primary px-5 py-2 text-sm font-semibold" id="translate-btn" disabled>
                    <i class="fas fa-language mr-2"></i> Translate Selected
                </button>
            </div>

            <div class="overflow-x-auto max-h-96 overflow-y-auto border border-gray-100 dark:border-gray-700 rounded-xl">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr class="text-left text-gray-500 dark:text-gray-400">
                            <th class="p-3 w-10"></th>
                            <th class="p-3 font-semibold">Title (BN)</th>
                            <th class="p-3 font-semibold">EN Status</th>
                            <th class="p-3 font-semibold">Published</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($posts as $post)
                            <tr class="border-t border-gray-50 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="p-3">
                                    <input type="checkbox" name="post_ids[]" value="{{ $post->id }}"
                                           class="post-checkbox rounded border-gray-300 text-blue-600">
                                </td>
                                <td class="p-3 text-gray-700 dark:text-gray-300 max-w-xs truncate">
                                    {{ $post->title_bn ?: $post->title }}
                                </td>
                                <td class="p-3">
                                    @if($post->title_en)
                                        <span class="badge badge-green">Translated</span>
                                    @else
                                        <span class="badge badge-amber">Pending</span>
                                    @endif
                                </td>
                                <td class="p-3 text-xs text-gray-400">
                                    {{ $post->published_at?->format('d M Y') ?: '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="p-6 text-center text-sm text-gray-400">No posts pending translation.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>
    </div>

    {{-- Recently Translated --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Recently Translated</h3>

        @if($recentlyTranslated->isEmpty())
            <p class="text-sm text-gray-400 py-4 text-center">No translations yet.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700">
                            <th class="pb-3 font-semibold">Post</th>
                            <th class="pb-3 font-semibold">Direction</th>
                            <th class="pb-3 font-semibold">Chars</th>
                            <th class="pb-3 font-semibold">Status</th>
                            <th class="pb-3 font-semibold">When</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentlyTranslated as $entry)
                            <tr class="border-b border-gray-50 dark:border-gray-700/50">
                                <td class="py-2.5 text-gray-700 dark:text-gray-300 max-w-xs truncate">
                                    {{ $entry->post?->title_bn ?: $entry->post?->title ?: '—' }}
                                </td>
                                <td class="py-2.5 text-xs font-mono">{{ $entry->from_locale }} → {{ $entry->to_locale }}</td>
                                <td class="py-2.5 font-mono text-xs">{{ number_format($entry->character_count) }}</td>
                                <td class="py-2.5">
                                    @if($entry->status === 'completed')
                                        <span class="badge badge-green">Done</span>
                                    @else
                                        <span class="badge badge-red">Failed</span>
                                    @endif
                                </td>
                                <td class="py-2.5 text-xs text-gray-400">{{ $entry->created_at->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('select-all')?.addEventListener('change', function() {
        document.querySelectorAll('.post-checkbox').forEach(cb => cb.checked = this.checked);
        updateSelectedCount();
    });
    document.querySelectorAll('.post-checkbox').forEach(cb => {
        cb.addEventListener('change', updateSelectedCount);
    });
    function updateSelectedCount() {
        const count = document.querySelectorAll('.post-checkbox:checked').length;
        document.getElementById('selected-count').textContent = count + ' selected';
        document.getElementById('translate-btn').disabled = count === 0;
    }
</script>
@endpush
@endsection
