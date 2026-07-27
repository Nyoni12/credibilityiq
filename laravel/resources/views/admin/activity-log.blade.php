@extends('layouts.app')
@section('title', 'Activity Log')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Activity Log</h1>
            <p class="text-gray-500 text-sm mt-1">All platform events across all tenants</p>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Company</label>
            <select name="company_id" class="px-3 py-2 rounded-lg border border-gray-300 text-sm">
                <option value="">All companies</option>
                @foreach($companies as $c)
                <option value="{{ $c->id }}" {{ $companyId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Action filter</label>
            <input type="text" name="action" value="{{ $action }}" placeholder="e.g. assessment, user"
                   class="px-3 py-2 rounded-lg border border-gray-300 text-sm w-48">
        </div>
        <button type="submit"
                class="px-4 py-2 bg-brand-500 text-white text-sm rounded-lg hover:bg-brand-600 transition-colors">
            Filter
        </button>
        @if($companyId || $action)
        <a href="{{ route('admin.activity-log') }}"
           class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
            Clear
        </a>
        @endif
    </form>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <p class="text-sm text-gray-500">{{ $logs->total() }} events total</p>
        </div>

        @if($logs->isEmpty())
        <div class="p-10 text-center text-gray-400">No activity yet.</div>
        @else
        <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[700px]">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-500 text-xs uppercase tracking-wide">When</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-500 text-xs uppercase tracking-wide">Action</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-500 text-xs uppercase tracking-wide">Target</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-500 text-xs uppercase tracking-wide">User</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-500 text-xs uppercase tracking-wide">IP</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-500 text-xs uppercase tracking-wide">Meta</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($logs as $log)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-gray-400 whitespace-nowrap text-xs">
                        {{ $log->created_at->format('d M Y H:i') }}
                    </td>
                    <td class="px-4 py-3">
                        @php
                            $color = match(true) {
                                str_contains($log->action, '.deleted') => 'bg-red-100 text-red-700',
                                str_contains($log->action, '.created') || str_contains($log->action, '.onboard') => 'bg-green-100 text-green-700',
                                str_contains($log->action, '.updated') || str_contains($log->action, '.saved') => 'bg-blue-100 text-blue-700',
                                str_contains($log->action, 'login') || str_contains($log->action, 'logout') => 'bg-gray-100 text-gray-600',
                                default => 'bg-amber-100 text-amber-700',
                            };
                        @endphp
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $color }}">
                            {{ $log->action }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-700 text-xs max-w-[200px] truncate">
                        @if($log->target_type)
                        <span class="text-gray-400">{{ $log->target_type }}: </span>
                        @endif
                        {{ $log->target_label ?: '—' }}
                    </td>
                    <td class="px-4 py-3 text-gray-600 text-xs whitespace-nowrap">
                        {{ $log->user?->full_name ?? 'System' }}
                    </td>
                    <td class="px-4 py-3 text-gray-400 text-xs font-mono whitespace-nowrap">
                        {{ $log->ip_address ?? '—' }}
                    </td>
                    <td class="px-4 py-3 text-gray-400 text-xs max-w-[160px] truncate">
                        @if($log->meta)
                        <span title="{{ json_encode($log->meta) }}">{{ Str::limit(json_encode($log->meta), 60) }}</span>
                        @else
                        —
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>

        @if($logs->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $logs->links() }}
        </div>
        @endif
        @endif
    </div>

</div>
@endsection
