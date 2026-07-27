@extends('layouts.app')
@section('title', 'Support Tickets')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-xl font-bold text-gray-900">Support Tickets</h1>
        <p class=”text-sm text-gray-500 mt-0.5”>All client tickets. 24-hour SLA.</p>
    </div>

    {{-- KPI row --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        @php
        $kpis = [
            ['Total', $totalTickets, '#1F2192', '#e0e7ff'],
            ['Open', $openTickets, '#ca8a04', '#fef9c3'],
            ['Resolved', $resolvedTickets, '#16a34a', '#dcfce7'],
            ['Overdue', $overdueTickets, '#dc2626', '#fee2e2'],
            ['Avg Resolution', ($avgResolutionHours !== null ? $avgResolutionHours.'h' : '—'), '#7c3aed', '#f3e8ff'],
            ['SLA Rate', ($slaRate !== null ? $slaRate.'%' : '—'), '#0891b2', '#e0f2fe'],
        ];
        @endphp
        @foreach($kpis as [$label, $value, $color, $bg])
        <div class="bg-white rounded-2xl border border-gray-100 px-4 py-4 text-center">
            <p class="text-2xl font-bold" style="color:{{ $color }}">{{ $value }}</p>
            <p class="text-xs text-gray-500 mt-0.5 font-medium">{{ $label }}</p>
        </div>
        @endforeach
    </div>

    {{-- Charts row: daily volume + priority breakdown --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Daily volume bar chart --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 p-5">
            <h3 class="text-sm font-bold text-gray-700 mb-4">Ticket Volume: Last 14 Days</h3>
            <canvas id="dailyChart" height="90"></canvas>
        </div>

        {{-- Priority breakdown --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <h3 class="text-sm font-bold text-gray-700 mb-4">By Priority</h3>
            <canvas id="priorityChart" height="180"></canvas>
            <div class="mt-4 space-y-2">
                @foreach(['high' => ['High','#dc2626','#fee2e2'], 'medium' => ['Medium','#ca8a04','#fef9c3'], 'low' => ['Low','#2563eb','#dbeafe']] as $key => [$label, $col, $bg])
                <div class="flex items-center justify-between text-xs">
                    <span class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full" style="background:{{ $col }}"></span>
                        <span class="text-gray-600">{{ $label }}</span>
                    </span>
                    <span class="font-bold text-gray-800">{{ $byPriority[$key] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Tickets table --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-800">All Tickets</h3>
            <span class="text-xs text-gray-400">{{ $totalTickets }} total</span>
        </div>

        @if($tickets->isEmpty())
        <div class="text-center py-14 text-gray-400 text-sm">No tickets raised yet.</div>
        @else
        <div class="overflow-x-auto">
        <table class="w-full min-w-[640px] text-sm">
            <thead>
                <tr class="border-b border-gray-100 text-xs text-gray-500 uppercase tracking-wide">
                    <th class="text-left px-5 py-3 font-semibold">#</th>
                    <th class="text-left px-4 py-3 font-semibold">Subject</th>
                    <th class="text-left px-4 py-3 font-semibold">Company</th>
                    <th class="text-left px-4 py-3 font-semibold">Priority</th>
                    <th class="text-left px-4 py-3 font-semibold">Status</th>
                    <th class="text-left px-4 py-3 font-semibold">SLA / Resolved</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($tickets as $ticket)
                @php
                    $overdue  = $ticket->isOverdue();
                    $resolved = $ticket->status === 'resolved';
                @endphp
                <tr class="hover:bg-gray-50/70 transition-colors {{ $overdue ? 'bg-red-50/30' : '' }}">
                    <td class="px-5 py-3 text-gray-400 text-xs font-mono">{{ $ticket->id }}</td>
                    <td class="px-4 py-3">
                        <p class="font-semibold text-gray-900 truncate max-w-[200px]">{{ $ticket->subject }}</p>
                        <p class="text-xs text-gray-400">{{ $ticket->messages_count }} {{ Str::plural('message', $ticket->messages_count) }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <p class="text-gray-700 font-medium truncate max-w-[150px]">{{ $ticket->company->name }}</p>
                        <p class="text-xs text-gray-400">{{ $ticket->user->full_name }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold"
                              style="background:{{ $ticket->priorityBg() }};color:{{ $ticket->priorityColor() }}">
                            {{ ucfirst($ticket->priority) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        @if($resolved)
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Resolved
                        </span>
                        @elseif($overdue)
                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Overdue</span>
                        @else
                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Open</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500">
                        @if($resolved && $ticket->resolved_at)
                            {{ $ticket->resolved_at->format('d M H:i') }}
                            @php $mins = $ticket->resolutionMinutes(); @endphp
                            @if($mins)
                            <span class="{{ $ticket->sla_due_at && $ticket->resolved_at->lte($ticket->sla_due_at) ? 'text-green-600' : 'text-red-500' }}">
                                ({{ $mins < 60 ? $mins.'m' : round($mins/60,1).'h' }})
                            </span>
                            @endif
                        @elseif($ticket->sla_due_at)
                            @if($overdue)
                            <span class="text-red-500 font-semibold">Breached {{ $ticket->sla_due_at->diffForHumans() }}</span>
                            @else
                            <span class="text-amber-600">Due {{ $ticket->sla_due_at->diffForHumans() }}</span>
                            @endif
                        @else
                            &mdash;
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.support.show', $ticket) }}"
                           class="text-xs font-semibold text-brand-500 hover:text-brand-700 hover:underline whitespace-nowrap">
                            View &rarr;
                        </a>
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

@push('scripts')
<script src="/js/chart.min.js"></script>
<script>
(function(){
    const dailyCtx = document.getElementById('dailyChart');
    if (dailyCtx) {
        new Chart(dailyCtx, {
            type: 'bar',
            data: {
                labels: @json($dailyLabels),
                datasets: [{
                    label: 'Tickets',
                    data: @json($dailyCounts),
                    backgroundColor: '#A329CC22',
                    borderColor: '#A329CC',
                    borderWidth: 2,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                    y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 11 } }, grid: { color: '#f3f4f6' } }
                }
            }
        });
    }

    const priCtx = document.getElementById('priorityChart');
    if (priCtx) {
        new Chart(priCtx, {
            type: 'doughnut',
            data: {
                labels: ['High', 'Medium', 'Low'],
                datasets: [{
                    data: [{{ $byPriority['high'] }}, {{ $byPriority['medium'] }}, {{ $byPriority['low'] }}],
                    backgroundColor: ['#dc2626', '#ca8a04', '#2563eb'],
                    borderWidth: 0,
                }]
            },
            options: {
                cutout: '65%',
                plugins: { legend: { display: false }, tooltip: { callbacks: {
                    label: ctx => ` ${ctx.label}: ${ctx.parsed} tickets`
                }}}
            }
        });
    }
})();
</script>
@endpush

