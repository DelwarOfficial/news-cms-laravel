@extends('admin.layouts.app')

@section('title', 'Translation Usage')
@section('header', 'Translation Usage')

@section('content')
<div class="max-w-5xl mx-auto space-y-8">

    {{-- Monthly Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-1">Monthly Characters</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalMonthlyChars) }}</p>
        </div>
        @foreach($monthlyStats as $stat)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
                <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-1">
                    {{ $stat->from_locale }} → {{ $stat->to_locale }}
                </p>
                <p class="text-2xl font-bold {{ $stat->status === 'completed' ? 'text-green-600' : 'text-red-500' }}">
                    {{ number_format($stat->total_chars) }}
                </p>
                <p class="text-xs text-gray-400 mt-1">{{ $stat->total }} jobs · {{ $stat->status }}</p>
            </div>
        @endforeach
    </div>

    {{-- Usage Log --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Translation Log</h3>

        @if($usage->isEmpty())
            <p class="text-sm text-gray-400 py-4 text-center">No translations recorded.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700">
                            <th class="pb-3 font-semibold">Post</th>
                            <th class="pb-3 font-semibold">Direction</th>
                            <th class="pb-3 font-semibold">Chars</th>
                            <th class="pb-3 font-semibold">Cost</th>
                            <th class="pb-3 font-semibold">Status</th>
                            <th class="pb-3 font-semibold">Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($usage as $entry)
                            <tr class="border-b border-gray-50 dark:border-gray-700/50">
                                <td class="py-2.5 text-gray-700 dark:text-gray-300 max-w-xs truncate">
                                    {{ $entry->post?->title_bn ?: $entry->post?->title ?: '—' }}
                                </td>
                                <td class="py-2.5 text-xs font-mono">{{ $entry->from_locale }} → {{ $entry->to_locale }}</td>
                                <td class="py-2.5 font-mono text-xs">{{ number_format($entry->character_count) }}</td>
                                <td class="py-2.5 font-mono text-xs">${{ number_format($entry->cost_estimate, 6) }}</td>
                                <td class="py-2.5">
                                    @if($entry->status === 'completed')
                                        <span class="badge badge-green">Completed</span>
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
            <div class="mt-4">
                {{ $usage->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
