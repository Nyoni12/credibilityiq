@extends('layouts.app')
@section('title', 'Scorecard — ' . $assessment->title)
@section('page-title', 'Credibility Scorecard')

@section('content')
<div class="space-y-6">

    {{-- Header + actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ $assessment->title }}</h2>
            <p class="text-sm text-gray-500">{{ $assessment->company->name }} · Closed {{ $assessment->closed_at?->format('d M Y') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('report.download', $assessment) }}"
               class="inline-flex items-center gap-2 bg-brand-500 hover:bg-brand-600 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-all hover:shadow-lg hover:shadow-brand-500/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Download PDF
            </a>
            <a href="{{ route('assessments.index') }}" class="text-gray-500 hover:text-gray-700 text-sm font-medium">← Back</a>
        </div>
    </div>

    {{-- Hero score card --}}
    <div class="bg-gradient-to-br from-brand-900 to-brand-700 rounded-3xl p-8 text-white"
         x-data="{ visible: false }" x-intersect.once="visible=true">
        <div class="flex flex-col md:flex-row md:items-center gap-8">

            {{-- Score ring --}}
            <div class="flex flex-col items-center">
                <div class="relative w-40 h-40">
                    @php $pct = ($assessment->overall_score ?? 0) / 100; @endphp
                    <svg viewBox="0 0 100 100" class="w-full h-full -rotate-90">
                        <circle cx="50" cy="50" r="40" fill="none" stroke="rgba(255,255,255,0.15)" stroke-width="8"/>
                        <circle cx="50" cy="50" r="40" fill="none"
                                stroke="{{ $scoreColor }}" stroke-width="8" stroke-linecap="round"
                                :style="visible ? 'stroke-dasharray: {{ 251.2 * $pct }} {{ 251.2 * (1 - $pct) }}' : 'stroke-dasharray: 0 251.2'"
                                style="transition: stroke-dasharray 1.5s ease; stroke-dasharray: {{ 251.2 * $pct }} {{ 251.2 * (1 - $pct) }}"/>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-4xl font-black text-white">{{ round($assessment->overall_score ?? 0) }}</span>
                        <span class="text-white/60 text-xs">/100</span>
                    </div>
                </div>
                <div class="mt-3 text-center">
                    <span class="inline-block px-4 py-1.5 rounded-full text-sm font-bold"
                          style="background: {{ $scoreColor }}30; color: {{ $scoreColor }}; border: 1px solid {{ $scoreColor }}50">
                        {{ $scoreLabel }}
                    </span>
                </div>
            </div>

            {{-- Summary stats --}}
            <div class="flex-1 grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white/10 rounded-2xl p-4 text-center">
                    <div class="text-2xl font-black text-white">{{ $assessment->surveyResponses()->count() }}</div>
                    <div class="text-white/60 text-xs mt-1">Responses</div>
                </div>
                <div class="bg-white/10 rounded-2xl p-4 text-center">
                    <div class="text-2xl font-black text-white">{{ $valueRatings->count() }}</div>
                    <div class="text-white/60 text-xs mt-1">Values Rated</div>
                </div>
                <div class="bg-white/10 rounded-2xl p-4 text-center">
                    <div class="text-xl font-black text-red-400">
                        ${{ $assessment->total_leakage ? number_format($assessment->total_leakage) : '0' }}
                    </div>
                    <div class="text-white/60 text-xs mt-1">Est. Leakage</div>
                </div>
                <div class="bg-white/10 rounded-2xl p-4 text-center">
                    <div class="text-2xl font-black" style="color: {{ $scoreColor }}">{{ $scoreLabel }}</div>
                    <div class="text-white/60 text-xs mt-1">Grade</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">

        {{-- Value scores --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 p-6">
            <h3 class="font-bold text-gray-900 mb-5">Value Scores</h3>
            <div class="space-y-4" x-data="{ visible: false }" x-intersect.once="visible=true">
                @foreach($valueRatings as $vr)
                @php
                    $pct = $vr->avg_score * 10;
                    $color = \App\Services\ScoringService::gradeColor($pct);
                @endphp
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <div class="flex items-center gap-2">
                            <span class="font-medium text-gray-900 text-sm">{{ $vr->companyValue->name }}</span>
                            @if($vr->training_recommended)
                            <span class="px-1.5 py-0.5 rounded text-xs bg-red-50 text-red-600 font-medium">Training recommended</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-3 text-sm">
                            <span class="text-gray-400 text-xs">{{ $vr->companyValue->weight_percentage }}% weight</span>
                            <span class="font-bold" style="color: {{ $color }}">{{ number_format($pct, 1) }}%</span>
                        </div>
                    </div>
                    <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-1000 ease-out"
                             style="width: {{ $pct }}%; background: {{ $color }}"></div>
                    </div>
                    <div class="flex items-center justify-between mt-1">
                        <span class="text-xs text-gray-400">Avg score: {{ number_format($vr->avg_score, 1) }}/10</span>
                        @if($vr->financial_impact > 0)
                        <span class="text-xs text-red-500">Leakage: ${{ number_format($vr->financial_impact) }}</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Radar-style breakdown --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <h3 class="font-bold text-gray-900 mb-5">Score Distribution</h3>
            <canvas id="radarChart" height="280"></canvas>
        </div>
    </div>

    {{-- Financial leakage table --}}
    @if($assessment->total_leakage > 0)
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <h3 class="font-bold text-gray-900 text-sm">Financial Leakage Analysis</h3>
                <p class="text-xs text-gray-400">Estimated revenue at risk due to credibility gaps</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left text-xs font-semibold text-gray-500 px-6 py-3">Value</th>
                        <th class="text-right text-xs font-semibold text-gray-500 px-6 py-3">Avg Score</th>
                        <th class="text-right text-xs font-semibold text-gray-500 px-6 py-3">Weight</th>
                        <th class="text-right text-xs font-semibold text-gray-500 px-6 py-3">Leakage</th>
                        <th class="text-left text-xs font-semibold text-gray-500 px-6 py-3">Action Needed</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($valueRatings->sortByDesc('financial_impact') as $vr)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 font-medium text-gray-900">{{ $vr->companyValue->name }}</td>
                        <td class="px-6 py-3 text-right">
                            <span class="font-bold" style="color: {{ \App\Services\ScoringService::gradeColor($vr->avg_score * 10) }}">
                                {{ number_format($vr->avg_score, 1) }}/10
                            </span>
                        </td>
                        <td class="px-6 py-3 text-right text-gray-600">{{ $vr->companyValue->weight_percentage }}%</td>
                        <td class="px-6 py-3 text-right">
                            @if($vr->financial_impact > 0)
                            <span class="text-red-600 font-bold">${{ number_format($vr->financial_impact) }}</span>
                            @else
                            <span class="text-cfa-500 font-bold">Minimal</span>
                            @endif
                        </td>
                        <td class="px-6 py-3">
                            @if($vr->training_recommended)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs bg-red-50 text-red-700 font-medium">
                                🎯 Training required
                            </span>
                            @elseif($vr->avg_score < 7)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs bg-yellow-50 text-yellow-700 font-medium">
                                ⚠ Needs attention
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs bg-green-50 text-green-700 font-medium">
                                ✓ On track
                            </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 border-t border-gray-200">
                    <tr>
                        <td colspan="3" class="px-6 py-3 font-bold text-gray-900 text-sm">Total Estimated Leakage</td>
                        <td class="px-6 py-3 text-right font-black text-red-600 text-base">${{ number_format($assessment->total_leakage) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('radarChart').getContext('2d');
new Chart(ctx, {
    type: 'radar',
    data: {
        labels: {!! $chartLabels->toJson() !!},
        datasets: [{
            label: 'Score (%)',
            data: {!! $chartScores->toJson() !!},
            backgroundColor: 'rgba(31,33,146,0.12)',
            borderColor: '#1F2192',
            borderWidth: 2,
            pointBackgroundColor: '#1F2192',
            pointRadius: 4,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            r: {
                min: 0, max: 100,
                ticks: { stepSize: 25, font: { size: 9 }, color: '#9ca3af' },
                grid: { color: '#f3f4f6' },
                pointLabels: { font: { size: 10 }, color: '#6b7280' }
            }
        }
    }
});
</script>
@endpush
