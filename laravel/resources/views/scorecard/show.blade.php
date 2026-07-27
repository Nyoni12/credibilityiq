@extends('layouts.app')
@section('title', 'Scorecard: ' . $assessment->title)

@section('content')
@php
    $size  = 200; $r = $size / 2 - 16;
    $circ  = 2 * M_PI * $r;
    $fill  = (($assessment->overall_score ?? 0) / 100) * $circ;

    $leakageSorted  = $valueRatings->sortByDesc('financial_impact');
    $top3Ids        = $top3Gaps->pluck('id')->toArray();
    $chartHeight    = max(240, $valueRatings->count() * 52);
    $lowResponse    = $totalResponses < 5;
    $noWeights      = $valueRatings->every(fn ($vr) => ($vr->companyValue->financial_weight ?? 0) == 0);
@endphp

<div class="space-y-6"
     x-data="{ tab: 'overview' }"
     x-init="$watch('tab', function(val) {
         if (val === 'breakdown') {
             $nextTick(function() { requestAnimationFrame(function() { window.__initBarChart && window.__initBarChart(); }); });
         } else if (val === 'risk') {
             $nextTick(function() { requestAnimationFrame(function() { window.__initRiskChart && window.__initRiskChart(); }); });
         } else {
             if (window.__barChartInst)  { window.__barChartInst.destroy();  window.__barChartInst  = null; }
             if (window.__riskChartInst) { window.__riskChartInst.destroy(); window.__riskChartInst = null; }
         }
     })">

    {{-- ── Page header ── --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <h1 class="text-2xl font-bold text-gray-900">{{ $assessment->company->name }}</h1>
                <span class="text-xs px-2 py-1 rounded-full font-semibold bg-gray-100 text-gray-500">Closed</span>
            </div>
            <p class="text-gray-500 text-sm">
                {{ $assessment->title }}, {{ $totalResponses }} respondents,
                {{ $assessment->created_at->toFormattedDateString() }}
            </p>
        </div>
        <a href="{{ route('report.download', $assessment) }}"
           x-data="{ loading: false }"
           @click="loading = true; setTimeout(() => loading = false, 15000)"
           :class="loading ? 'opacity-70 pointer-events-none' : ''"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-500 text-white rounded-lg text-sm font-semibold hover:bg-brand-600 transition-colors whitespace-nowrap">
            <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
            </svg>
            <span x-show="!loading">⬇</span>
            <span x-text="loading ? 'Generating PDF…' : 'Download PDF'">Download PDF</span>
        </a>
    </div>

    {{-- ── Missing value_ratings warning ── --}}
    @if($valueRatings->isEmpty())
    <div class="rounded-xl border border-red-200 bg-red-50 p-5">
        <div class="flex items-start gap-4">
            <span class="text-2xl flex-shrink-0">🚨</span>
            <div class="flex-1">
                <p class="font-semibold text-red-800 mb-2">Survey data lost: per-value breakdown unavailable</p>
                <p class="text-sm text-red-700 leading-relaxed mb-3">
                    The individual staff ratings for this assessment were deleted when the Values Setup was re-saved.
                    The stored <strong>Overall Score ({{ number_format($assessment->overall_score ?? 0, 1) }}%)</strong> and
                    <strong>Total Leakage (${{ number_format($assessment->total_leakage ?? 0) }})</strong> are from the original
                    calculation and are still valid, but the per-value breakdown cannot be rebuilt.
                </p>
                <div class="bg-red-100 rounded-lg p-3 text-xs text-red-800 mb-4">
                    <strong>To get a full scorecard going forward:</strong>
                    <ol class="list-decimal ml-4 mt-1 space-y-1">
                        <li>Go to <strong>Values Setup</strong> and set the correct financial weight for each value</li>
                        <li>Create a <strong>new assessment</strong> and share the survey link with staff again</li>
                        <li>Close the new assessment to generate the complete scorecard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Low response warning ── --}}
    @if($lowResponse)
    <div class="flex items-start gap-3 px-4 py-3 bg-amber-50 border border-amber-200 rounded-xl text-sm text-amber-800">
        <span class="text-lg flex-shrink-0">⚠️</span>
        <div>
            <strong>Low response count ({{ $totalResponses }}).</strong>
            Results may not be statistically reliable. A minimum of 5 respondents is recommended before drawing conclusions.
        </div>
    </div>
    @endif

    {{-- ── Missing financial weights warning ── --}}
    @if($noWeights && !$valueRatings->isEmpty())
    <div class="flex items-start gap-3 px-4 py-4 bg-orange-50 border border-orange-200 rounded-xl text-sm text-orange-900">
        <span class="text-xl flex-shrink-0">💰</span>
        <div>
            <p class="font-semibold mb-1">Financial leakage shows $0 — no annual amounts at risk have been set.</p>
            <p class="text-orange-700 leading-relaxed">
                Go to <strong>Values Setup</strong> and enter the <strong>annual $ at risk</strong> for each building block
                (e.g., enter <em>$50,000</em> if $50k of budget or revenue is at risk for that value).
                Then come back and click <strong>Recalculate</strong> to see real leakage and recovery figures.
            </p>
            <a href="{{ route('values.index') }}" class="inline-block mt-2 px-4 py-1.5 bg-orange-600 text-white rounded-lg text-xs font-semibold hover:bg-orange-700 transition-colors">
                Go to Values Setup →
            </a>
        </div>
    </div>
    @endif

    {{-- ── Tab bar ── --}}
    <div class="flex gap-1 bg-gray-100 p-1 rounded-xl w-fit flex-wrap">
        @foreach(['overview' => 'Overview', 'breakdown' => 'Value Breakdown', 'leakage' => 'Financial Leakage', 'risk' => 'Risk Matrix', 'training' => 'Training'] as $key => $label)
        <button @click="tab='{{ $key }}'"
                :class="tab==='{{ $key }}' ? 'bg-white text-brand-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                class="px-4 py-2 rounded-lg text-sm font-medium transition-all">
            {{ $label }}
            @if($key === 'training' && $trainingCount > 0)
            <span class="ml-1 bg-orange-100 text-orange-700 text-xs px-1.5 py-0.5 rounded-full">{{ $trainingCount }}</span>
            @endif
        </button>
        @endforeach
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- ── OVERVIEW TAB ── --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div x-show="tab==='overview'" class="space-y-6">

        {{-- Executive summary strip --}}
        <div class="rounded-2xl p-5 grid grid-cols-2 md:grid-cols-4 gap-4"
             style="background:linear-gradient(135deg,#07093c 0%,#1F2192 100%)">
            @foreach([
                ['Overall Score',        number_format($assessment->overall_score ?? 0, 1) . '%', $bm['color']],
                ['CLC Band',             $bm['short'] . ': ' . $bm['label'],                    $bm['color']],
                ['Total Leakage',        '$' . number_format($totalLeakage),                       '#ef4444'],
                ['Recovery Potential',   '$' . number_format($totalRecovery),                     '#22c55e'],
            ] as [$lbl, $val, $clr])
            <div class="text-center">
                <p class="text-white/50 text-xs uppercase tracking-wider mb-1">{{ $lbl }}</p>
                <p class="font-black text-base md:text-lg" style="color:{{ $clr }}">{{ $val }}</p>
            </div>
            @endforeach
        </div>

        <div class="grid md:grid-cols-3 gap-6">

            {{-- CredibilityRing --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm flex flex-col items-center justify-center py-8 px-4">
                <svg width="{{ $size }}" height="{{ $size }}" style="transform:rotate(-90deg)">
                    <circle cx="{{ $size/2 }}" cy="{{ $size/2 }}" r="{{ $r }}"
                            fill="none" stroke="#E5E7EB" stroke-width="14"/>
                    <circle cx="{{ $size/2 }}" cy="{{ $size/2 }}" r="{{ $r }}"
                            fill="none" stroke="{{ $bm['color'] }}" stroke-width="14"
                            stroke-dasharray="{{ round($fill,2) }} {{ round($circ,2) }}"
                            stroke-linecap="round"
                            style="transition:stroke-dasharray 0.8s ease"/>
                    <text x="50%" y="45%" text-anchor="middle" dominant-baseline="middle"
                          style="transform:rotate(90deg);transform-origin:center;font-size:{{ round($size*0.13) }}px;font-weight:800;fill:{{ $bm['color'] }}">
                        {{ number_format($assessment->overall_score ?? 0, 1) }}%
                    </text>
                    <text x="50%" y="63%" text-anchor="middle" dominant-baseline="middle"
                          style="transform:rotate(90deg);transform-origin:center;font-size:{{ round($size*0.11) }}px;font-weight:700;fill:{{ $bm['color'] }}">
                        {{ $bm['short'] }}
                    </text>
                </svg>
                <div class="text-center mt-2">
                    <p class="text-sm font-medium text-gray-600">Overall Credibility Score</p>
                    <span class="text-xs font-bold mt-0.5 px-3 py-0.5 rounded-full inline-block"
                          style="color:{{ $bm['color'] }};background:{{ $bm['color'] }}22">
                        {{ $bm['label'] }}
                    </span>
                    <p class="text-xs text-gray-400 mt-2">{{ $totalResponses }} respondents</p>
                </div>
            </div>

            {{-- Metric cards --}}
            <div class="md:col-span-2 grid grid-cols-2 gap-4 content-start">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                    <div class="flex items-start gap-3">
                        <span class="text-2xl">💸</span>
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Total Financial Leakage</p>
                            <p class="text-lg font-bold mt-0.5 text-red-600">${{ number_format($totalLeakage, 2) }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">Estimated budget at risk</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                    <div class="flex items-start gap-3">
                        <span class="text-2xl">💡</span>
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Recovery Potential</p>
                            <p class="text-lg font-bold mt-0.5 text-green-600">${{ number_format($totalRecovery, 2) }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">If all values reach {{ $targetScore }}/10</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                    <div class="flex items-start gap-3">
                        <span class="text-2xl">⚠️</span>
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Training Flags</p>
                            <p class="text-lg font-bold mt-0.5 {{ $trainingCount > 0 ? 'text-amber-600' : 'text-gray-900' }}">
                                {{ $trainingCount }}
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">Recommended interventions</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                    <div class="flex items-start gap-3">
                        <span class="text-2xl">🏆</span>
                        <div>
                            <p class="text-xs text-gray-500 font-medium">Top Performing Value</p>
                            <p class="text-lg font-bold mt-0.5 text-green-600 truncate">{{ $topValueName }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">Highest average score</p>
                        </div>
                    </div>
                </div>

                {{-- Top 3 gaps --}}
                <div class="col-span-2 bg-red-50 rounded-xl border border-red-100 p-4">
                    <h3 class="text-sm font-semibold text-red-800 mb-3">Top 3 Costliest Gaps</h3>
                    <div class="space-y-2">
                        @foreach($top3Gaps as $gap)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-700 font-medium">{{ $gap->companyValue->name }}</span>
                            <span class="text-red-600 font-semibold">${{ number_format($gap->financial_impact, 2) }}</span>
                        </div>
                        @endforeach
                        @if($top3Gaps->isEmpty())
                        <p class="text-red-600 text-sm">No financial leakage recorded.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Narrative summary --}}
        <div class="bg-amber-50 border-l-4 border-amber-400 rounded-r-xl p-5">
            <p class="text-xs font-bold text-amber-700 uppercase tracking-wide mb-2">📋 Report Summary</p>
            <p class="text-sm text-amber-900 leading-relaxed">{!! $narrative !!}</p>
        </div>

        {{-- Priority action plan --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-base font-semibold text-gray-900 mb-4">Priority Action Plan</h2>
            <div class="space-y-3">
                @foreach($actions as $i => $action)
                @php
                    $badges = [
                        'critical' => ['bg-red-100 text-red-700 border border-red-200',  'CRITICAL'],
                        'high'     => ['bg-orange-100 text-orange-700 border border-orange-200', 'HIGH'],
                        'medium'   => ['bg-amber-100 text-amber-700 border border-amber-200',  'MONITOR'],
                        'low'      => ['bg-green-100 text-green-700 border border-green-200',   'MAINTAIN'],
                    ];
                    [$badgeCls, $badgeTxt] = $badges[$action['priority']] ?? $badges['low'];
                @endphp
                <div class="flex items-start gap-3 p-3 rounded-lg bg-gray-50 border border-gray-100">
                    <span class="w-6 h-6 rounded-full bg-brand-500 text-white text-xs font-black flex items-center justify-center flex-shrink-0 mt-0.5">{{ $i + 1 }}</span>
                    <span class="text-xs px-2 py-0.5 rounded-full font-bold flex-shrink-0 {{ $badgeCls }}">{{ $badgeTxt }}</span>
                    <p class="text-sm text-gray-700 leading-relaxed">{!! $action['label'] !!}</p>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Trend (only if previous assessments exist) --}}
        @if($trendAssessments->isNotEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-base font-semibold text-gray-900 mb-4">Score Trend Across Assessments</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left border-b border-gray-100">
                            <th class="pb-2 font-medium text-gray-500">Assessment</th>
                            <th class="pb-2 font-medium text-gray-500 text-right">Date</th>
                            <th class="pb-2 font-medium text-gray-500 text-right">Score</th>
                            <th class="pb-2 font-medium text-gray-500 text-right">Leakage</th>
                            <th class="pb-2 font-medium text-gray-500 text-right">vs Current</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($trendAssessments as $past)
                        @php
                            $diff = round(($assessment->overall_score ?? 0) - ($past->overall_score ?? 0), 1);
                        @endphp
                        <tr class="border-b border-gray-50">
                            <td class="py-2.5 text-gray-700">{{ $past->title }}</td>
                            <td class="py-2.5 text-gray-400 text-right">{{ $past->closed_at?->format('d M Y') }}</td>
                            <td class="py-2.5 text-right font-semibold"
                                style="color:{{ \App\Services\ScoringService::gradeColor($past->overall_score ?? 0) }}">
                                {{ number_format($past->overall_score ?? 0, 1) }}%
                            </td>
                            <td class="py-2.5 text-right text-red-500">${{ number_format($past->total_leakage) }}</td>
                            <td class="py-2.5 text-right font-semibold {{ $diff >= 0 ? 'text-green-600' : 'text-red-500' }}">
                                {{ $diff >= 0 ? '+' : '' }}{{ $diff }}%
                            </td>
                        </tr>
                        @endforeach
                        <tr class="font-semibold bg-brand-50">
                            <td class="py-2.5 px-1 text-brand-700">{{ $assessment->title }} <span class="text-xs font-normal text-brand-400">(current)</span></td>
                            <td class="py-2.5 text-brand-500 text-right">{{ $assessment->closed_at?->format('d M Y') }}</td>
                            <td class="py-2.5 text-right font-bold" style="color:{{ $bm['color'] }}">{{ number_format($assessment->overall_score ?? 0, 1) }}%</td>
                            <td class="py-2.5 text-right text-red-600">${{ number_format($totalLeakage) }}</td>
                            <td class="py-2.5 text-right text-gray-400">—</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- ── VALUE BREAKDOWN TAB ── --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div x-show="tab==='breakdown'" class="space-y-6">

        {{-- Bar chart --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-1">Average Score per Value (out of 10)</h2>
            <p class="text-xs text-gray-400 mb-5">Bars represent the average of all {{ $totalResponses }} respondents' ratings.</p>
            <div style="position:relative;width:100%;height:{{ $chartHeight }}px">
                <canvas id="barChart"></canvas>
            </div>
            <div class="mt-5 flex gap-4 text-xs flex-wrap">
                @foreach([
                    ['#059669', 'CC: Celebrity Credibility (>9.0/10)'],
                    ['#ca8a04', 'GW: General Ward (6.9–9.0/10)'],
                    ['#ea580c', 'HDU: High Dependency Unit (5.5–6.8/10)'],
                    ['#dc2626', 'ICU: Intensive Care Unit (≤5.5/10)'],
                ] as [$color, $label])
                <span class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-sm inline-block" style="background:{{ $color }}"></span>
                    {{ $label }}
                </span>
                @endforeach
            </div>
        </div>

        {{-- Score distribution table --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-base font-semibold text-gray-900 mb-1">Score Distribution per Value</h2>
            <p class="text-xs text-gray-400 mb-5">Shows the spread of individual staff ratings, not just the average.</p>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left border-b border-gray-100">
                            <th class="pb-2 font-medium text-gray-500">Value</th>
                            <th class="pb-2 font-medium text-gray-500 text-center">Avg</th>
                            <th class="pb-2 font-medium text-gray-500 text-center">Min</th>
                            <th class="pb-2 font-medium text-gray-500 text-center">Max</th>
                            <th class="pb-2 font-medium text-gray-500 text-center">Std Dev</th>
                            <th class="pb-2 font-medium text-gray-500 text-center">≤ 5</th>
                            <th class="pb-2 font-medium text-gray-500">Spread</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($valueRatings as $vr)
                        @php
                            $dist   = $distribution[$vr->company_value_id] ?? null;
                            $minS   = $dist?->min_score ?? 0;
                            $maxS   = $dist?->max_score ?? 10;
                            $stdDev = $dist?->std_dev ?? 0;
                            $below  = $dist?->below_threshold ?? 0;
                            $clr    = \App\Services\ScoringService::gradeColor(($vr->avg_score - 1) / 9 * 100);
                            // bar widths as % of 0-10 scale
                            $minPct = $minS * 10;
                            $maxPct = $maxS * 10;
                            $avgPct = $vr->avg_score * 10;
                        @endphp
                        <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                            <td class="py-3 font-medium text-gray-900">{{ $vr->companyValue->name }}</td>
                            <td class="py-3 text-center font-bold" style="color:{{ $clr }}">
                                {{ number_format($vr->avg_score, 1) }}
                            </td>
                            <td class="py-3 text-center text-gray-500">{{ $minS }}</td>
                            <td class="py-3 text-center text-gray-500">{{ $maxS }}</td>
                            <td class="py-3 text-center text-gray-500">
                                <span class="{{ $stdDev > 2.5 ? 'text-red-500 font-semibold' : '' }}">
                                    {{ number_format($stdDev, 1) }}
                                </span>
                            </td>
                            <td class="py-3 text-center">
                                @if($below > 0)
                                <span class="bg-red-100 text-red-700 text-xs font-semibold px-2 py-0.5 rounded-full">
                                    {{ $below }}
                                </span>
                                @else
                                <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="py-3 w-32">
                                {{-- Range bar: min→max with avg marker --}}
                                <div class="relative h-3 bg-gray-100 rounded-full">
                                    <div class="absolute h-3 rounded-full opacity-30"
                                         style="background:{{ $clr }};left:{{ $minPct }}%;width:{{ $maxPct - $minPct }}%"></div>
                                    <div class="absolute top-0 w-0.5 h-3 rounded-full"
                                         style="background:{{ $clr }};left:{{ $avgPct }}%"></div>
                                </div>
                                <div class="flex justify-between text-xs text-gray-300 mt-0.5">
                                    <span>0</span><span>10</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="text-xs text-gray-400 mt-3">
                <strong>Std Dev &gt; 2.5</strong> (highlighted in red) indicates high disagreement; some staff rate the value very differently from others.
            </p>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- ── FINANCIAL LEAKAGE TAB ── --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div x-show="tab==='leakage'" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-start justify-between mb-6 gap-4 flex-wrap">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Financial Leakage Report</h2>
                <p class="text-xs text-gray-400 mt-0.5">
                    Recovery potential assumes all values improve to {{ $targetScore }}/10.
                </p>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-400">Total at risk</p>
                <p class="text-2xl font-bold text-red-600">${{ number_format($totalLeakage, 2) }}</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left" style="background:#1F2192;color:#fff">
                        <th class="px-4 py-3 font-semibold rounded-tl-lg">Value</th>
                        <th class="px-4 py-3 font-semibold text-right">Financial Weight</th>
                        <th class="px-4 py-3 font-semibold text-right">Avg Score</th>
                        <th class="px-4 py-3 font-semibold text-right">Gap %</th>
                        <th class="px-4 py-3 font-semibold text-right">$ Leakage</th>
                        <th class="px-4 py-3 font-semibold text-right rounded-tr-lg">Recovery Potential</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leakageSorted as $idx => $vr)
                    @php
                        $isTop3     = in_array($vr->id, $top3Ids);
                        $gap        = round((10 - $vr->avg_score) / 9 * 100, 1);
                        $fw         = $vr->effective_weight ?? 0;
                        $rowBg      = $isTop3 ? 'background:#fff5f5' : ($idx % 2 === 0 ? 'background:#fff' : 'background:#f9fafb');
                        $recovery   = $vr->recovery_potential ?? 0;
                    @endphp
                    <tr class="border-b border-gray-100" style="{{ $rowBg }}">
                        <td class="px-4 py-3 font-medium text-gray-900">
                            @if($isTop3)
                            <span class="inline-block w-2 h-2 rounded-full mr-2" style="background:#ef4444;vertical-align:middle"></span>
                            @endif
                            {{ $vr->companyValue->name }}
                        </td>
                        <td class="px-4 py-3 text-right text-gray-600">${{ number_format($fw) }}</td>
                        <td class="px-4 py-3 text-right font-semibold"
                            style="color:{{ \App\Services\ScoringService::gradeColor(($vr->avg_score - 1) / 9 * 100) }}">
                            {{ number_format($vr->avg_score, 1) }}/10
                        </td>
                        <td class="px-4 py-3 text-right font-semibold
                            {{ $gap >= 60 ? 'text-red-600' : ($gap >= 40 ? 'text-amber-600' : 'text-green-600') }}">
                            {{ $gap }}%
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-red-600">
                            ${{ number_format($vr->financial_impact, 2) }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if($recovery > 0)
                            <span class="font-semibold text-green-600">+${{ number_format($recovery) }}</span>
                            @else
                            <span class="text-gray-300 text-xs">Already at target</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background:#fffbeb;border-top:2px solid #fbbf24">
                        <td colspan="4" class="px-4 py-3 font-bold text-gray-900">Total</td>
                        <td class="px-4 py-3 text-right font-black text-red-700">${{ number_format($totalLeakage, 2) }}</td>
                        <td class="px-4 py-3 text-right font-black text-green-700">+${{ number_format($totalRecovery) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <p class="text-xs text-gray-400 mt-3">
            <span class="inline-block w-2 h-2 rounded-full mr-1" style="background:#ef4444;vertical-align:middle"></span>
            Top 3 costliest gaps highlighted.
            Recovery potential = savings achievable if each value reaches {{ $targetScore }}/10.
        </p>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- ── RISK MATRIX TAB ── --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div x-show="tab==='risk'" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-1">Risk Priority Matrix</h2>
        <p class="text-xs text-gray-400 mb-6">
            Values in the <strong>top-left quadrant</strong> (high financial weight, low score) are your highest-priority interventions.
        </p>

        {{-- Quadrant labels --}}
        <div class="grid grid-cols-2 gap-2 mb-4 text-xs font-semibold">
            <div class="text-center py-1.5 rounded-lg bg-red-50 text-red-700">HIGH PRIORITY: High $ + Low Score</div>
            <div class="text-center py-1.5 rounded-lg bg-amber-50 text-amber-700">MONITOR: High $ + High Score</div>
            <div class="text-center py-1.5 rounded-lg bg-blue-50 text-blue-700">ADDRESS: Low $ + Low Score</div>
            <div class="text-center py-1.5 rounded-lg bg-green-50 text-green-700">MAINTAIN: Low $ + High Score</div>
        </div>

        <div style="position:relative;width:100%;height:420px">
            <canvas id="riskChart"></canvas>
        </div>
        <p class="text-xs text-gray-400 mt-3 text-center">
            X-axis: Average Score (0–10) &nbsp;|&nbsp; Y-axis: Financial Weight ($) &nbsp;|&nbsp; Bubble size: Financial Leakage
        </p>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- ── TRAINING TAB ── --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div x-show="tab==='training'" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">
            Training Recommendations
            @if($trainingCount > 0)
            <span class="ml-2 bg-orange-100 text-orange-700 text-sm px-2 py-0.5 rounded-full font-medium">{{ $trainingCount }}</span>
            @endif
        </h2>

        @if($valueRatings->where('training_recommended', true)->isEmpty())
        <div class="text-center py-10 text-gray-400">
            <div class="text-4xl mb-2">🎉</div>
            <p>No training flags — all values are performing above threshold.</p>
        </div>
        @else
        <div class="grid gap-4 sm:grid-cols-2">
            @foreach($valueRatings->where('training_recommended', true)->sortByDesc('financial_impact') as $vr)
            @php
                $fw = $vr->effective_weight ?? 0;
                $rec = $vr->recovery_potential ?? 0;
            @endphp
            <div class="bg-white border border-orange-200 rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-orange-100 flex items-center justify-center text-orange-600 flex-shrink-0 text-xl">📚</div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-semibold text-gray-900">{{ $vr->companyValue->name }}</h4>
                        <p class="text-sm text-gray-500 mt-1">
                            Average score: <strong>{{ number_format($vr->avg_score, 1) }}/10</strong>, at or below 5.0 threshold
                        </p>
                        <div class="mt-3 grid grid-cols-2 gap-2 text-xs">
                            <div class="bg-red-50 rounded-lg p-2 text-center">
                                <p class="text-gray-400">$ Leakage</p>
                                <p class="font-bold text-red-600">${{ number_format($vr->financial_impact) }}</p>
                            </div>
                            <div class="bg-green-50 rounded-lg p-2 text-center">
                                <p class="text-gray-400">Recovery if fixed</p>
                                <p class="font-bold text-green-600">+${{ number_format($rec) }}</p>
                            </div>
                        </div>
                        @php $dist = $distribution[$vr->company_value_id] ?? null; @endphp
                        @if($dist && $dist->std_dev > 2.5)
                        <p class="text-xs text-amber-600 mt-2 font-medium">
                            High score variance (σ={{ number_format($dist->std_dev,1) }}): inconsistent perception across staff
                        </p>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script defer src="/js/chart.min.js"></script>
<script>
(function () {
    // ── Value Breakdown bar chart ─────────────────────────────────────
    var barLabels = {!! $chartLabels->values()->toJson() !!};
    var barScores = {!! $chartScores->values()->toJson() !!};
    var barColors = {!! $chartColors->values()->toJson() !!};

    window.__barChartInst = null;
    window.__initBarChart = function () {
        var el = document.getElementById('barChart');
        if (!el) return;
        if (window.__barChartInst) { window.__barChartInst.destroy(); window.__barChartInst = null; }
        window.__barChartInst = new Chart(el, {
            type: 'bar',
            data: {
                labels: barLabels,
                datasets: [{ data: barScores, backgroundColor: barColors, borderRadius: 4, borderSkipped: false }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 700, easing: 'easeOutQuart' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                var gap = ((10 - ctx.raw) / 9 * 100).toFixed(1);
                                return ' ' + ctx.raw + '/10  (' + gap + '% gap)';
                            }
                        }
                    }
                },
                scales: {
                    x: { min: 0, max: 10, ticks: { stepSize: 1, font: { size: 11 }, color: '#6b7280' }, grid: { color: '#f3f4f6' } },
                    y: { ticks: { font: { size: 11 }, color: '#374151' } }
                }
            }
        });
    };

    // ── Risk Matrix bubble chart ──────────────────────────────────────
    var riskBubbles = {!! $riskBubbles->toJson() !!};

    window.__riskChartInst = null;
    window.__initRiskChart = function () {
        var el = document.getElementById('riskChart');
        if (!el) return;
        if (window.__riskChartInst) { window.__riskChartInst.destroy(); window.__riskChartInst = null; }

        // Compute max weight for bubble sizing
        var maxY = Math.max.apply(null, riskBubbles.map(function(b){ return b.y; })) || 1;

        var datasets = riskBubbles.map(function(b) {
            return {
                label: b.label,
                data: [{ x: b.x, y: b.y, r: Math.max(8, Math.round((b.y / maxY) * 28)) }],
                backgroundColor: b.color + 'bb',
                borderColor: b.color,
                borderWidth: 2,
            };
        });

        // Find midpoints for quadrant lines
        var maxWeight = maxY;
        var midWeight = maxWeight / 2;

        window.__riskChartInst = new Chart(el, {
            type: 'bubble',
            data: { datasets: datasets },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 700 },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                var d = ctx.raw;
                                return ctx.dataset.label + ':  Score ' + d.x + '/10 | Weight $' + d.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        min: 0, max: 10,
                        title: { display: true, text: 'Average Score (0–10)', font: { size: 11 }, color: '#6b7280' },
                        ticks: { stepSize: 1, font: { size: 10 }, color: '#6b7280' },
                        grid: { color: '#f3f4f6' }
                    },
                    y: {
                        title: { display: true, text: 'Financial Weight ($)', font: { size: 11 }, color: '#6b7280' },
                        ticks: {
                            font: { size: 10 }, color: '#6b7280',
                            callback: function(v) { return '$' + (v >= 1000000 ? (v/1000000).toFixed(1)+'M' : v >= 1000 ? (v/1000).toFixed(0)+'k' : v); }
                        },
                        grid: { color: '#f3f4f6' }
                    }
                }
            },
            plugins: [{
                // Draw quadrant divider lines
                id: 'quadrantLines',
                afterDraw: function(chart) {
                    var ctx2 = chart.ctx;
                    var xScale = chart.scales.x;
                    var yScale = chart.scales.y;
                    var midX   = xScale.getPixelForValue(5);
                    var midY   = yScale.getPixelForValue(midWeight);
                    var top    = chart.chartArea.top;
                    var bottom = chart.chartArea.bottom;
                    var left   = chart.chartArea.left;
                    var right  = chart.chartArea.right;

                    ctx2.save();
                    ctx2.setLineDash([6, 4]);
                    ctx2.strokeStyle = '#d1d5db';
                    ctx2.lineWidth   = 1.5;

                    // Vertical line at x=5
                    ctx2.beginPath();
                    ctx2.moveTo(midX, top);
                    ctx2.lineTo(midX, bottom);
                    ctx2.stroke();

                    // Horizontal line at mid weight
                    ctx2.beginPath();
                    ctx2.moveTo(left, midY);
                    ctx2.lineTo(right, midY);
                    ctx2.stroke();

                    ctx2.restore();
                }
            }]
        });
    };
})();
</script>
@endpush
