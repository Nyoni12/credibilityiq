@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- ── Stat cards ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
        $latestScore = $latestClosed?->overall_score ?? null;
        $latestLeakage = $latestClosed?->total_leakage ?? null;
        @endphp

        <div class="bg-white rounded-2xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Credibility Score</p>
                    <p class="text-3xl font-black {{ $latestScore ? ($latestScore >= 70 ? 'text-cfa-500' : ($latestScore >= 50 ? 'text-yellow-500' : 'text-red-500')) : 'text-gray-300' }}">
                        {{ $latestScore ? number_format($latestScore, 1) : '—' }}
                    </p>
                    @if($latestScore)
                    <p class="text-xs text-gray-400 mt-1">{{ \App\Services\ScoringService::gradeLabel($latestScore) }}</p>
                    @else
                    <p class="text-xs text-gray-400 mt-1">No completed assessments</p>
                    @endif
                </div>
                <div class="w-10 h-10 rounded-xl bg-brand-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Est. Financial Leakage</p>
                    <p class="text-2xl font-black text-red-500">
                        {{ $latestLeakage ? '$' . number_format($latestLeakage) : '—' }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">Revenue at risk</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Assessments</p>
                    <p class="text-3xl font-black text-gray-900">{{ $totalAssessments }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $openAssessments }} open</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-accent-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-accent-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Company Values</p>
                    <p class="text-3xl font-black text-gray-900">{{ $valuesCount }}</p>
                    <p class="text-xs text-gray-400 mt-1">
                        @if($valuesCount === 0)
                        <a href="{{ route('values.index') }}" class="text-brand-500 hover:underline">Configure now →</a>
                        @else
                        Configured
                        @endif
                    </p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-cfa-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-cfa-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">

        {{-- ── Latest scorecard summary ── --}}
        @if($latestClosed)
        <div class="lg:col-span-1 bg-white rounded-2xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-bold text-gray-900 text-sm">Latest Scorecard</h3>
                <a href="{{ route('scorecard.show', $latestClosed) }}" class="text-brand-500 text-xs font-medium hover:underline">View full →</a>
            </div>

            {{-- SVG Score Ring --}}
            <div class="flex items-center gap-4 mb-5">
                <div class="relative w-20 h-20 shrink-0">
                    @php $pct = $latestClosed->overall_score / 100; @endphp
                    <svg viewBox="0 0 100 100" class="w-full h-full -rotate-90">
                        <circle cx="50" cy="50" r="40" fill="none" stroke="#f3f4f6" stroke-width="10"/>
                        <circle cx="50" cy="50" r="40" fill="none"
                                stroke="{{ \App\Services\ScoringService::gradeColor($latestClosed->overall_score) }}"
                                stroke-width="10" stroke-linecap="round"
                                stroke-dasharray="{{ 251.2 * $pct }} {{ 251.2 * (1 - $pct) }}"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-lg font-black text-gray-900">{{ round($latestClosed->overall_score) }}</span>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900">{{ $latestClosed->title }}</p>
                    <p class="text-xs text-gray-400">{{ $latestClosed->closed_at?->format('d M Y') }}</p>
                    <span class="mt-1 inline-block px-2 py-0.5 rounded-full text-xs font-medium"
                          style="background: {{ \App\Services\ScoringService::gradeColor($latestClosed->overall_score) }}20; color: {{ \App\Services\ScoringService::gradeColor($latestClosed->overall_score) }}">
                        {{ \App\Services\ScoringService::gradeLabel($latestClosed->overall_score) }}
                    </span>
                </div>
            </div>

            {{-- Value bars --}}
            <div class="space-y-2">
                @foreach($latestClosed->valueRatings->sortByDesc('avg_score')->take(4) as $vr)
                <div>
                    <div class="flex items-center justify-between text-xs text-gray-500 mb-0.5">
                        <span class="truncate max-w-[120px]">{{ $vr->companyValue->name }}</span>
                        <span class="font-semibold text-gray-700">{{ number_format($vr->avg_score * 10, 0) }}%</span>
                    </div>
                    <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full"
                             style="width: {{ $vr->avg_score * 10 }}%; background: {{ \App\Services\ScoringService::gradeColor($vr->avg_score * 10) }}"></div>
                    </div>
                </div>
                @endforeach
            </div>

            <a href="{{ route('report.download', $latestClosed) }}"
               class="mt-4 flex items-center justify-center gap-2 w-full border border-brand-200 text-brand-600 text-xs font-semibold px-4 py-2.5 rounded-xl hover:bg-brand-50 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Download PDF Report
            </a>
        </div>
        @else
        <div class="lg:col-span-1 bg-white rounded-2xl border border-dashed border-gray-300 p-6 flex flex-col items-center justify-center text-center">
            <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mb-3">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <p class="text-sm font-medium text-gray-700 mb-1">No scorecard yet</p>
            <p class="text-xs text-gray-400 mb-4">Create and close an assessment to see your credibility score here.</p>
            <a href="{{ route('assessments.index') }}" class="text-brand-500 text-sm font-semibold hover:underline">Start Assessment →</a>
        </div>
        @endif

        {{-- ── Score trend chart ── --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-bold text-gray-900 text-sm">Score Trend</h3>
                <a href="{{ route('assessments.index') }}" class="text-brand-500 text-xs font-medium hover:underline">All assessments →</a>
            </div>
            @if($trendData->count() >= 2)
            <canvas id="trendChart" height="160"></canvas>
            @elseif($trendData->count() === 1)
            <div class="flex items-center justify-center h-40 text-gray-400 text-sm">
                Complete at least 2 assessments to see the trend chart.
            </div>
            @else
            <div class="flex flex-col items-center justify-center h-40 text-center">
                <p class="text-gray-400 text-sm">No completed assessments yet.</p>
                <a href="{{ route('assessments.index') }}" class="mt-3 bg-brand-500 hover:bg-brand-600 text-white text-xs font-semibold px-5 py-2 rounded-lg transition-all">
                    Create First Assessment
                </a>
            </div>
            @endif
        </div>
    </div>

    {{-- ── Recent assessments table ── --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-900 text-sm">Recent Assessments</h3>
            <a href="{{ route('assessments.index') }}" class="text-brand-500 text-xs font-medium hover:underline">View all →</a>
        </div>
        @if($assessments->isEmpty())
        <div class="p-8 text-center text-gray-400 text-sm">No assessments yet.</div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left text-xs font-semibold text-gray-500 px-6 py-3">Assessment</th>
                        <th class="text-left text-xs font-semibold text-gray-500 px-6 py-3">Status</th>
                        <th class="text-left text-xs font-semibold text-gray-500 px-6 py-3">Score</th>
                        <th class="text-left text-xs font-semibold text-gray-500 px-6 py-3">Date</th>
                        <th class="text-left text-xs font-semibold text-gray-500 px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($assessments->take(5) as $a)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $a->title }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium
                                 {{ $a->status === 'open' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $a->status === 'open' ? 'bg-green-500 animate-pulse' : 'bg-gray-400' }}"></span>
                                {{ ucfirst($a->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($a->overall_score !== null)
                            <span class="font-bold" style="color: {{ \App\Services\ScoringService::gradeColor($a->overall_score) }}">
                                {{ number_format($a->overall_score, 1) }}
                            </span>
                            @else
                            <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-500">{{ $a->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4">
                            @if($a->status === 'closed')
                            <a href="{{ route('scorecard.show', $a) }}" class="text-brand-500 hover:text-brand-700 font-medium text-xs">View Scorecard</a>
                            @else
                            <a href="{{ route('assessments.index') }}" class="text-gray-400 hover:text-gray-600 text-xs">Manage</a>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
@if($trendData->count() >= 2)
const ctx = document.getElementById('trendChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! $trendData->pluck('label')->toJson() !!},
        datasets: [{
            label: 'Credibility Score',
            data: {!! $trendData->pluck('score')->toJson() !!},
            borderColor: '#1F2192',
            backgroundColor: 'rgba(31,33,146,0.08)',
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#1F2192',
            pointRadius: 5,
            pointHoverRadius: 7,
            borderWidth: 2.5,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: {
                min: 0, max: 100,
                ticks: { font: { size: 11 }, color: '#9ca3af' },
                grid: { color: '#f3f4f6' }
            },
            x: { ticks: { font: { size: 11 }, color: '#9ca3af' }, grid: { display: false } }
        }
    }
});
@endif
</script>
@endpush
