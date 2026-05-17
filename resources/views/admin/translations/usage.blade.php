@extends('admin.layouts.app')

@section('title', 'Translation Usage')
@section('header', 'Translation Usage')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-1">Total Cost</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($totalCost, 4) }}</p>
            <p class="text-xs text-gray-400 mt-1">All time</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-1">Total Characters</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalChars) }}</p>
            <p class="text-xs text-gray-400 mt-1">All time</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-1">Total Jobs</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalJobs }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $completedJobs }} completed · {{ $failedJobs }} failed</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-1">Avg Cost/Job</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($totalJobs > 0 ? $totalCost / $totalJobs : 0, 6) }}</p>
            <p class="text-xs text-gray-400 mt-1">Per translation job</p>
        </div>
    </div>

    {{-- Cost by Provider --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Cost by Provider</h3>
        @if($costByProvider->isEmpty())
            <p class="text-sm text-gray-400 py-4 text-center">No data yet.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700">
                            <th class="pb-3 font-semibold">Provider</th>
                            <th class="pb-3 font-semibold">Jobs</th>
                            <th class="pb-3 font-semibold">Total Tokens</th>
                            <th class="pb-3 font-semibold">Total Cost</th>
                            <th class="pb-3 font-semibold">Avg Cost/Job</th>
                            <th class="pb-3 font-semibold">Success Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($costByProvider as $stat)
                            <tr class="border-b border-gray-50 dark:border-gray-700/50">
                                <td class="py-2.5 font-medium text-gray-900 dark:text-white">{{ $stat->provider_name }}</td>
                                <td class="py-2.5">{{ $stat->total }}</td>
                                <td class="py-2.5 font-mono text-xs">{{ number_format($stat->total_tokens) }}</td>
                                <td class="py-2.5 font-mono text-xs">${{ number_format($stat->total_cost, 6) }}</td>
                                <td class="py-2.5 font-mono text-xs">${{ number_format($stat->total > 0 ? $stat->total_cost / $stat->total : 0, 6) }}</td>
                                <td class="py-2.5">
                                    @php $rate = $stat->total > 0 ? ($stat->completed / $stat->total) * 100 : 0; @endphp
                                    @if($rate >= 90)
                                        <span class="badge badge-green">{{ number_format($rate, 1) }}%</span>
                                    @elseif($rate >= 50)
                                        <span class="badge badge-amber">{{ number_format($rate, 1) }}%</span>
                                    @else
                                        <span class="badge badge-red">{{ number_format($rate, 1) }}%</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Translation Log --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Translation Log</h3>

        @if($logs->isEmpty())
            <p class="text-sm text-gray-400 py-4 text-center">No translations recorded.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700">
                            <th class="pb-3 font-semibold">ID</th>
                            <th class="pb-3 font-semibold">Type</th>
                            <th class="pb-3 font-semibold">Direction</th>
                            <th class="pb-3 font-semibold">Provider</th>
                            <th class="pb-3 font-semibold">Model</th>
                            <th class="pb-3 font-semibold">Input Tokens</th>
                            <th class="pb-3 font-semibold">Output Tokens</th>
                            <th class="pb-3 font-semibold">Chars</th>
                            <th class="pb-3 font-semibold">Cost</th>
                            <th class="pb-3 font-semibold">Duration</th>
                            <th class="pb-3 font-semibold">Status</th>
                            <th class="pb-3 font-semibold">Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr class="border-b border-gray-50 dark:border-gray-700/50">
                                <td class="py-2.5 text-xs text-gray-400">{{ $log->id }}</td>
                                <td class="py-2.5 text-xs font-mono text-gray-600">{{ class_basename($log->translatable_type) }}#{{ $log->translatable_id }}</td>
                                <td class="py-2.5 text-xs font-mono">{{ $log->from_locale }} → {{ $log->to_locale }}</td>
                                <td class="py-2.5 text-xs">{{ $log->provider_name }}</td>
                                <td class="py-2.5 text-xs text-gray-500">{{ $log->model ?: '—' }}</td>
                                <td class="py-2.5 font-mono text-xs">{{ number_format($log->input_tokens) }}</td>
                                <td class="py-2.5 font-mono text-xs">{{ number_format($log->output_tokens) }}</td>
                                <td class="py-2.5 font-mono text-xs">{{ number_format($log->total_chars) }}</td>
                                <td class="py-2.5 font-mono text-xs">${{ number_format($log->cost_usd, 6) }}</td>
                                <td class="py-2.5 text-xs text-gray-500">{{ $log->duration_ms ? ($log->duration_ms > 1000 ? number_format($log->duration_ms / 1000, 1) . 's' : $log->duration_ms . 'ms') : '—' }}</td>
                                <td class="py-2.5">
                                    @if($log->status === 'completed')
                                        <span class="badge badge-green">Done</span>
                                    @elseif($log->status === 'failed')
                                        <span class="badge badge-red" title="{{ $log->error_message }}">Failed</span>
                                    @elseif($log->status === 'fallback')
                                        <span class="badge badge-amber">Fallback</span>
                                    @else
                                        <span class="badge badge-amber">{{ $log->status }}</span>
                                    @endif
                                </td>
                                <td class="py-2.5 text-xs text-gray-400">{{ $log->created_at->format('d M H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $logs->links() }}</div>
        @endif
    </div>
</div>
@endsection
