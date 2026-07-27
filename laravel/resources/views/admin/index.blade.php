@extends('layouts.app')
@section('title', 'SuperAdmin Dashboard')
@section('page-title', 'SuperAdmin Dashboard')

@section('content')
@php
function clcBand(float $score): string {
    if ($score > 89) return 'CC';
    if ($score > 65) return 'GW';
    if ($score > 50) return 'HDU';
    return 'ICU';
}
@endphp

<div class="space-y-5">

{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     ROW 1 Â· Core platform counts
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
<div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3">
    @foreach([
        ['Total Companies',    $stats['companies'],          '#EEEFFE','#1F2192','M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5'],
        ['Active',             $stats['active_companies'],   '#d1fae5','#059669','M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['Users',              $stats['users'],              '#f3e8ff','#A329CC','M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197'],
        ['Assessments',        $stats['assessments'],        '#dbeafe','#2563eb','M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2'],
        ['Closed',             $stats['closed_assessments'], '#d1fae5','#059669','M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['Open',               $stats['open_assessments'],   '#fef9c3','#ca8a04','M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['Responses',          $stats['total_responses'],    '#ffedd5','#ea580c','M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z'],
    ] as [$label, $value, $bg, $color, $icon])
    <div class="bg-white rounded-2xl border border-gray-100 p-4 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-2">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wider leading-tight">{{ $label }}</p>
            <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0" style="background:{{ $bg }}">
                <svg class="w-3.5 h-3.5" style="color:{{ $color }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-black text-gray-900">{{ number_format($value) }}</p>
    </div>
    @endforeach
</div>

{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     ROW 2 Â· Platform impact KPIs
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
<div class="grid md:grid-cols-3 gap-4">
    <div class="rounded-2xl p-6" style="background:linear-gradient(135deg,#7f1d1d,#dc2626)">
        <p class="text-red-200 text-xs font-semibold uppercase tracking-widest mb-2">Total Leakage Identified</p>
        <p class="text-4xl font-black text-white">${{ number_format($totalLeakage) }}</p>
        <p class="text-red-300 text-xs mt-2">Across all closed assessments</p>
    </div>
    <div class="rounded-2xl p-6" style="background:linear-gradient(135deg,#1e1b4b,#1F2192)">
        <p class="text-blue-200 text-xs font-semibold uppercase tracking-widest mb-2">Platform Avg Score</p>
        <p class="text-4xl font-black text-white">{{ number_format($avgScore, 1) }}<span class="text-2xl text-blue-300">%</span></p>
        <p class="text-blue-300 text-xs mt-2">Mean credibility score â€” all closed assessments</p>
    </div>
    <div class="rounded-2xl p-6" style="background:linear-gradient(135deg,#064e3b,#059669)">
        <p class="text-green-200 text-xs font-semibold uppercase tracking-widest mb-2">New Companies This Month</p>
        <p class="text-4xl font-black text-white">{{ $newThisMonth }}</p>
        <div class="flex items-center gap-1.5 mt-2">
            @if($growthPct >= 0)
            <svg class="w-3.5 h-3.5 text-green-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7 7 7M12 3v18"/></svg>
            <span class="text-green-200 text-xs">{{ $growthPct }}% vs last month ({{ $newLastMonth }})</span>
            @else
            <svg class="w-3.5 h-3.5 text-red-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7-7-7M12 21V3"/></svg>
            <span class="text-red-200 text-xs">{{ abs($growthPct) }}% vs last month ({{ $newLastMonth }})</span>
            @endif
        </div>
    </div>
</div>

{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     ROW 3 Â· Engagement metrics
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-3">
    @foreach([
        ['Repeat Clients',       $repeatClients,  '2+ assessments run',          '#f3e8ff','#A329CC'],
        ['Completion Rate',      $completionRate.'%', 'Assessments closed vs started', '#d1fae5','#059669'],
        ['Training Flags',       $trainingFlags,  'Values flagged platform-wide', '#fee2e2','#dc2626'],
        ['Avg per Company',      $avgAssessments, 'Assessments per company',      '#dbeafe','#2563eb'],
    ] as [$label, $value, $sub, $bg, $color])
    <div class="bg-white rounded-2xl border border-gray-100 p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background:{{ $bg }}">
            <span class="text-lg font-black" style="color:{{ $color }}">{{ $value }}</span>
        </div>
        <div>
            <p class="text-sm font-bold text-gray-900">{{ $label }}</p>
            <p class="text-xs text-gray-400">{{ $sub }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     ROW 4 Â· Charts
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}

{{-- Growth & Activity + CLC Doughnut --}}
<div class="grid lg:grid-cols-3 gap-4">
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-gray-900">Growth &amp; Activity (12 months)</h3>
            <div class="flex items-center gap-4 text-xs text-gray-400">
                <span class="flex items-center gap-1.5"><span class="w-3 h-0.5 rounded bg-brand-500 inline-block"></span>New companies</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-0.5 rounded bg-cfa-500 inline-block"></span>Assessments closed</span>
            </div>
        </div>
        <div style="position:relative;height:220px">
            <canvas id="growthChart"></canvas>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <h3 class="text-sm font-bold text-gray-900 mb-1">CLC Band Distribution</h3>
        <p class="text-xs text-gray-400 mb-4">Latest score per company</p>
        <div style="position:relative;height:180px">
            <canvas id="bandChart"></canvas>
        </div>
        @php $totalBanded = array_sum($bands); @endphp
        @if($totalBanded > 0)
        <div class="mt-4 grid grid-cols-2 gap-2">
            @foreach($bands as $code => $count)
            @php $meta = $bandMeta[$code]; @endphp
            <div class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:{{ $meta['color'] }}"></span>
                <span class="text-xs text-gray-500">{{ $code }}</span>
                <span class="text-xs font-bold text-gray-900 ml-auto">{{ $count }}</span>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

{{-- Monthly Leakage + Assessment Score Distribution + Avg Score Trend --}}
<div class="grid md:grid-cols-3 gap-4">
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <h3 class="text-sm font-bold text-gray-900 mb-1">Monthly Leakage Identified</h3>
        <p class="text-xs text-gray-400 mb-4">Total $ leakage from closed assessments per month</p>
        <div style="position:relative;height:190px">
            <canvas id="leakageChart"></canvas>
        </div>
    </div>

    {{-- NEW: Assessment score distribution doughnut --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <h3 class="text-sm font-bold text-gray-900 mb-1">Score Distribution</h3>
        <p class="text-xs text-gray-400 mb-3">All closed assessments by CLC band</p>
        <div style="position:relative;height:160px">
            <canvas id="assessmentDistChart"></canvas>
        </div>
        @php $totalAssessmentBanded = array_sum($assessmentBands); @endphp
        @if($totalAssessmentBanded > 0)
        <div class="mt-3 space-y-1.5">
            @foreach($assessmentBands as $code => $count)
            @php $meta = $bandMeta[$code]; $pct = round($count / $totalAssessmentBanded * 100); @endphp
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $meta['color'] }}"></span>
                <span class="text-xs text-gray-500 flex-1 truncate">{{ $code }}</span>
                <span class="text-xs font-bold text-gray-800">{{ $count }}</span>
                <span class="text-xs text-gray-400 w-8 text-right">{{ $pct }}%</span>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-xs text-gray-400 text-center mt-4">No closed assessments yet</p>
        @endif
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <h3 class="text-sm font-bold text-gray-900 mb-1">Average Credibility Score Trend</h3>
        <p class="text-xs text-gray-400 mb-4">Mean overall score across closed assessments</p>
        <div style="position:relative;height:190px">
            <canvas id="scoreChart"></canvas>
        </div>
    </div>
</div>

{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     ROW 5 Â· Industry Distribution
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
<div class="grid lg:grid-cols-5 gap-4">

    {{-- Horizontal bar chart: company count per industry --}}
    <div class="lg:col-span-3 bg-white rounded-2xl border border-gray-100 p-6">
        <h3 class="text-sm font-bold text-gray-900 mb-1">Industry Distribution</h3>
        <p class="text-xs text-gray-400 mb-5">Number of client companies by sector</p>
        @if($industryDist->isEmpty())
        <p class="text-sm text-gray-400 text-center py-10">No industry data yet.</p>
        @else
        <div style="position:relative;height:{{ max(180, $industryDist->count() * 34) }}px">
            <canvas id="industryChart"></canvas>
        </div>
        @endif
    </div>

    {{-- Score league table per industry --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 p-6">
        <h3 class="text-sm font-bold text-gray-900 mb-1">Avg Score by Industry</h3>
        <p class="text-xs text-gray-400 mb-4">Mean credibility score across closed assessments</p>
        @if($industryScores->isEmpty())
        <p class="text-sm text-gray-400 text-center py-10">No scored assessments yet.</p>
        @else
        <div class="space-y-3">
            @foreach($industryScores->sortByDesc('avg_score')->take(10) as $row)
            @php
                $s   = (float) $row->avg_score;
                $col = $s > 89 ? '#059669' : ($s > 65 ? '#ca8a04' : ($s > 50 ? '#ea580c' : '#dc2626'));
                $bg  = $s > 89 ? '#d1fae5' : ($s > 65 ? '#fef9c3' : ($s > 50 ? '#ffedd5' : '#fee2e2'));
            @endphp
            <div class="flex items-center gap-3">
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-gray-800 truncate">{{ $row->industry }}</p>
                    <p class="text-xs text-gray-400">{{ $row->scored_count }} {{ Str::plural('company', $row->scored_count) }}</p>
                </div>
                <div class="shrink-0 flex items-center gap-2">
                    <div class="w-24 h-1.5 rounded-full bg-gray-100 overflow-hidden">
                        <div class="h-full rounded-full" style="width:{{ $s }}%;background:{{ $col }}"></div>
                    </div>
                    <span class="text-xs font-black w-10 text-right" style="color:{{ $col }}">{{ $s }}%</span>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     ROW 6 Â· Onboarding funnel
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
<div class="bg-white rounded-2xl border border-gray-100 p-6">
    <h3 class="text-sm font-bold text-gray-900 mb-5">Client Onboarding Funnel</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php $funnelColors = ['#1F2192','#A329CC','#ca8a04','#059669']; @endphp
        @foreach($funnel as $i => $step)
        @php
            $pct     = $funnel[0]['count'] > 0 ? round($step['count'] / $funnel[0]['count'] * 100) : 0;
            $col     = $funnelColors[$i];
            $prev    = $i > 0 ? $funnel[$i-1]['count'] : null;
            $convPct = ($prev && $prev > 0) ? round($step['count'] / $prev * 100) : null;
        @endphp
        <div>
            <div class="flex items-center gap-2 mb-2">
                <div class="w-6 h-6 rounded-full flex items-center justify-center text-white text-xs font-black flex-shrink-0"
                     style="background:{{ $col }}">{{ $i + 1 }}</div>
                <p class="text-xs text-gray-500 font-medium">{{ $step['label'] }}</p>
            </div>
            <p class="text-3xl font-black text-gray-900 mb-1">{{ number_format($step['count']) }}</p>
            <div class="w-full bg-gray-100 rounded-full h-1.5 mb-1">
                <div class="h-1.5 rounded-full" style="width:{{ $pct }}%;background:{{ $col }}"></div>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-400">{{ $pct }}% of total</span>
                @if($convPct !== null)
                <span class="text-xs font-semibold" style="color:{{ $col }}">{{ $convPct }}% conv.</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     ROW 5 Â· Recently closed + CLC band distribution
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
<div class="grid lg:grid-cols-3 gap-4">

    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Recently Closed Assessments</h3>
            <span class="text-xs text-gray-400">{{ $stats['closed_assessments'] }} total</span>
        </div>
        @if($recentClosed->isEmpty())
        <div class="px-6 py-10 text-center text-gray-400 text-sm">No closed assessments yet.</div>
        @else
        <div class="divide-y divide-gray-50">
            @foreach($recentClosed as $a)
            @php $band = clcBand($a->overall_score ?? 0); $bm = $bandMeta[$band]; @endphp
            <div class="flex items-center justify-between px-6 py-3 hover:bg-gray-50 transition-colors">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white font-black text-xs flex-shrink-0"
                         style="background:{{ $bm['color'] }}">{{ $band }}</div>
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-900 text-sm truncate">{{ $a->company->name ?? 'â€”' }}</p>
                        <p class="text-xs text-gray-400">{{ $a->title }} &middot; {{ $a->closed_at?->format('d M Y') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-4 flex-shrink-0 ml-3">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-black" style="color:{{ $bm['color'] }}">{{ number_format($a->overall_score ?? 0, 1) }}%</p>
                        <p class="text-xs text-gray-400">${{ number_format($a->total_leakage ?? 0) }}</p>
                    </div>
                    <a href="{{ route('admin.companies.show', $a->company) }}" class="text-xs text-brand-500 hover:text-brand-700 font-medium">View â†’</a>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <h3 class="text-sm font-bold text-gray-900 mb-5">CLC Band Distribution</h3>
        @php $totalBanded = array_sum($bands); @endphp
        @if($totalBanded === 0)
        <p class="text-xs text-gray-400 text-center py-8">No closed assessments yet.</p>
        @else
        <div class="space-y-4">
            @foreach($bands as $code => $count)
            @php $meta = $bandMeta[$code]; $pct = $totalBanded > 0 ? round($count / $totalBanded * 100) : 0; @endphp
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-black px-2 py-0.5 rounded-full" style="color:{{ $meta['color'] }};background:{{ $meta['bg'] }}">{{ $code }}</span>
                        <span class="text-xs text-gray-500">{{ $meta['label'] }}</span>
                    </div>
                    <span class="text-sm font-black text-gray-900">{{ $count }}</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2">
                    <div class="h-2 rounded-full" style="width:{{ $pct }}%;background:{{ $meta['color'] }}"></div>
                </div>
                <p class="text-xs text-gray-400 mt-0.5 text-right">{{ $pct }}%</p>
            </div>
            @endforeach
        </div>
        <div class="mt-5 pt-4 border-t border-gray-50 text-center">
            <p class="text-xs text-gray-400">{{ $totalBanded }} companies scored</p>
        </div>
        @endif
    </div>
</div>

{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     ROW 6 Â· Top leakage companies + Most flagged values
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
<div class="grid md:grid-cols-2 gap-4">

    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Top Financial Leakage</h3>
            <span class="text-xs text-gray-400">Highest leakage per assessment</span>
        </div>
        @if($topLeakage->isEmpty())
        <div class="px-6 py-8 text-center text-gray-400 text-sm">No leakage data yet.</div>
        @else
        <div class="divide-y divide-gray-50">
            @foreach($topLeakage as $i => $a)
            <div class="flex items-center justify-between px-6 py-3 hover:bg-gray-50 transition-colors">
                <div class="flex items-center gap-3">
                    <span class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 text-xs font-black flex-shrink-0">{{ $i + 1 }}</span>
                    <div>
                        <p class="font-semibold text-gray-900 text-sm">{{ $a->company->name ?? 'â€”' }}</p>
                        <p class="text-xs text-gray-400">{{ $a->title }}</p>
                    </div>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-sm font-black text-red-600">${{ number_format($a->total_leakage) }}</p>
                    <p class="text-xs text-gray-400">{{ number_format($a->overall_score ?? 0, 1) }}% score</p>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Most Flagged Values</h3>
            <span class="text-xs text-gray-400 bg-red-50 text-red-600 px-2 py-0.5 rounded-full font-medium">Training required</span>
        </div>
        @if($mostFlaggedValues->isEmpty())
        <div class="px-6 py-8 text-center text-gray-400 text-sm">No training flags recorded yet.</div>
        @else
        <div class="p-6 space-y-3">
            @php $maxFlags = $mostFlaggedValues->max('flag_count'); @endphp
            @foreach($mostFlaggedValues as $v)
            @php $pct = $maxFlags > 0 ? round($v->flag_count / $maxFlags * 100) : 0; @endphp
            <div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-sm font-medium text-gray-700">{{ $v->name }}</span>
                    <span class="text-xs font-black text-red-600">{{ $v->flag_count }}Ã—</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2">
                    <div class="h-2 rounded-full bg-red-400" style="width:{{ $pct }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     ROW 7 Â· Stale open + Companies with no assessments
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
<div class="grid md:grid-cols-2 gap-4">

    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 bg-amber-400 rounded-full animate-pulse"></div>
                <h3 class="text-sm font-bold text-gray-900">Stale Open Assessments</h3>
            </div>
            <span class="text-xs font-semibold bg-amber-50 text-amber-600 px-2 py-0.5 rounded-full">Open &gt; 14 days</span>
        </div>
        @if($staleOpen->isEmpty())
        <div class="px-6 py-8 text-center">
            <svg class="w-8 h-8 text-green-300 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm text-gray-400">No stale assessments â€” all clients on track.</p>
        </div>
        @else
        <div class="divide-y divide-gray-50">
            @foreach($staleOpen as $a)
            <div class="flex items-center justify-between px-6 py-3 hover:bg-amber-50/40 transition-colors">
                <div>
                    <p class="font-semibold text-gray-900 text-sm">{{ $a->company->name ?? 'â€”' }}</p>
                    <p class="text-xs text-gray-400">{{ $a->title }} &middot; {{ $a->survey_responses_count }} responses</p>
                </div>
                <div class="text-right">
                    <p class="text-xs font-bold text-amber-600">{{ $a->created_at->diffForHumans() }}</p>
                    <a href="{{ route('admin.companies.show', $a->company) }}" class="text-xs text-brand-500 hover:underline">View â†’</a>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 bg-red-400 rounded-full"></div>
                <h3 class="text-sm font-bold text-gray-900">Never Ran an Assessment</h3>
            </div>
            <span class="text-xs font-semibold bg-red-50 text-red-600 px-2 py-0.5 rounded-full">
                {{ $noAssessments->count() }} shown
            </span>
        </div>
        @if($noAssessments->isEmpty())
        <div class="px-6 py-8 text-center">
            <svg class="w-8 h-8 text-green-300 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm text-gray-400">Every company has run at least one assessment.</p>
        </div>
        @else
        <div class="divide-y divide-gray-50">
            @foreach($noAssessments as $c)
            <div class="flex items-center justify-between px-6 py-3 hover:bg-red-50/30 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 font-black text-xs flex-shrink-0">
                        {{ strtoupper(substr($c->name, 0, 2)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 text-sm">{{ $c->name }}</p>
                        <p class="text-xs text-gray-400">{{ $c->users_count }} {{ Str::plural('user', $c->users_count) }} &middot; Joined {{ $c->created_at->format('d M Y') }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.companies.show', $c) }}" class="text-xs text-brand-500 hover:underline font-medium flex-shrink-0">Manage â†’</a>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     ROW 8 Â· Data quality warnings + Churn risk
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
<div class="grid md:grid-cols-2 gap-4">

    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <h3 class="text-sm font-bold text-gray-900">Data Quality Warnings</h3>
            </div>
            <span class="text-xs font-semibold bg-orange-50 text-orange-600 px-2 py-0.5 rounded-full">Closed &lt; 5 responses</span>
        </div>
        @if($lowResponseClosed->isEmpty())
        <div class="px-6 py-8 text-center">
            <svg class="w-8 h-8 text-green-300 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm text-gray-400">All closed assessments have sufficient responses.</p>
        </div>
        @else
        <div class="divide-y divide-gray-50">
            @foreach($lowResponseClosed as $a)
            <div class="flex items-center justify-between px-6 py-3 hover:bg-orange-50/30 transition-colors">
                <div>
                    <p class="font-semibold text-gray-900 text-sm">{{ $a->company->name ?? 'â€”' }}</p>
                    <p class="text-xs text-gray-400">{{ $a->title }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-black text-orange-600">{{ $a->survey_responses_count }} responses</p>
                    <p class="text-xs text-gray-400">{{ number_format($a->overall_score ?? 0, 1) }}% score</p>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                <h3 class="text-sm font-bold text-gray-900">Churn Risk</h3>
            </div>
            <span class="text-xs font-semibold bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">No activity &gt; 90 days</span>
        </div>
        @if($churnRisk->isEmpty())
        <div class="px-6 py-8 text-center">
            <svg class="w-8 h-8 text-green-300 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm text-gray-400">No at-risk clients detected.</p>
        </div>
        @else
        <div class="divide-y divide-gray-50">
            @foreach($churnRisk as $c)
            <div class="flex items-center justify-between px-6 py-3 hover:bg-gray-50 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 font-black text-xs flex-shrink-0">
                        {{ strtoupper(substr($c->name, 0, 2)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 text-sm">{{ $c->name }}</p>
                        <p class="text-xs text-gray-400">{{ $c->assessments_count }} {{ Str::plural('assessment', $c->assessments_count) }} total &middot; gone quiet</p>
                    </div>
                </div>
                <a href="{{ route('admin.companies.show', $c) }}" class="text-xs text-brand-500 hover:underline font-medium flex-shrink-0">View â†’</a>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     ROW 9 Â· Quick links
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
<div class="grid md:grid-cols-3 gap-4">
    <a href="{{ route('admin.companies.index') }}"
       class="flex items-center gap-4 bg-white rounded-xl border border-gray-100 p-4 hover:border-brand-300 hover:shadow-md transition-all group">
        <div class="w-11 h-11 rounded-xl bg-brand-50 flex items-center justify-center group-hover:bg-brand-100 transition-colors flex-shrink-0">
            <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        </div>
        <div>
            <p class="font-semibold text-gray-900 text-sm">Manage Companies</p>
            <p class="text-xs text-gray-400">View, edit, activate companies</p>
        </div>
    </a>
    <a href="{{ route('admin.users.index') }}"
       class="flex items-center gap-4 bg-white rounded-xl border border-gray-100 p-4 hover:border-purple-300 hover:shadow-md transition-all group">
        <div class="w-11 h-11 rounded-xl bg-purple-50 flex items-center justify-center group-hover:bg-purple-100 transition-colors flex-shrink-0">
            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        </div>
        <div>
            <p class="font-semibold text-gray-900 text-sm">Manage Users</p>
            <p class="text-xs text-gray-400">View and control user accounts</p>
        </div>
    </a>
    <a href="{{ route('admin.settings') }}"
       class="flex items-center gap-4 bg-white rounded-xl border border-gray-100 p-4 hover:border-gray-300 hover:shadow-md transition-all group">
        <div class="w-11 h-11 rounded-xl bg-gray-50 flex items-center justify-center group-hover:bg-gray-100 transition-colors flex-shrink-0">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <div>
            <p class="font-semibold text-gray-900 text-sm">Platform Settings</p>
            <p class="text-xs text-gray-400">Slots, registration, announcements</p>
        </div>
    </a>
</div>

{{-- â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     ROW 10 Â· Recent companies
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• --}}
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
        <h3 class="font-bold text-gray-900 text-sm">Recent Companies</h3>
        <a href="{{ route('admin.companies.index') }}" class="text-brand-500 text-xs font-medium hover:underline">View all â†’</a>
    </div>
    <div class="divide-y divide-gray-50">
        @foreach($recentCompanies as $company)
        <div class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition-colors">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-brand-50 flex items-center justify-center text-brand-600 font-bold text-sm flex-shrink-0">
                    {{ strtoupper(substr($company->name, 0, 2)) }}
                </div>
                <div>
                    <p class="font-medium text-gray-900 text-sm">{{ $company->name }}</p>
                    <p class="text-xs text-gray-400">{{ $company->users_count }} {{ Str::plural('user', $company->users_count) }} &middot; Joined {{ $company->created_at->format('d M Y') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $company->is_active ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }}">
                    {{ $company->is_active ? 'Active' : 'Inactive' }}
                </span>
                <a href="{{ route('admin.companies.show', $company) }}" class="text-brand-500 hover:text-brand-700 text-xs font-medium">Manage â†’</a>
            </div>
        </div>
        @endforeach
    </div>
</div>

</div>
@endsection

@push('scripts')
<script defer src="/js/chart.min.js"></script>
<script>
(function () {
    const labels   = @json($chartData['chartLabels']);
    const signups  = @json($chartData['chartSignups']);
    const closed   = @json($chartData['chartClosed']);
    const leakage  = @json($chartData['chartLeakage']);
    const avgScore = @json($chartData['chartAvgScore']);
    const bands           = @json(array_values($bands));
    const assessmentDist  = @json(array_values($assessmentBands));
    const bandLabels = ['CC â€” Celebrity Credibility', 'GW â€” General Ward', 'HDU â€” High Dependency Unit', 'ICU â€” Intensive Care Unit'];
    const bandColors = ['#059669','#ca8a04','#ea580c','#dc2626'];

    const gridColor  = 'rgba(0,0,0,.05)';
    const tickColor  = '#9ca3af';
    const baseFont   = { family: 'Inter, system-ui, sans-serif', size: 11 };

    const sharedScales = {
        x: { grid: { color: gridColor }, ticks: { color: tickColor, font: baseFont } },
        y: { grid: { color: gridColor }, ticks: { color: tickColor, font: baseFont }, beginAtZero: true },
    };

    // 1 â”€â”€ Growth & Activity (dual line)
    new Chart(document.getElementById('growthChart'), {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'New Companies',
                    data: signups,
                    borderColor: '#1F2192',
                    backgroundColor: 'rgba(31,33,146,.08)',
                    fill: true, tension: 0.4, pointRadius: 3, pointHoverRadius: 5, borderWidth: 2,
                },
                {
                    label: 'Assessments Closed',
                    data: closed,
                    borderColor: '#059669',
                    backgroundColor: 'rgba(5,150,105,.08)',
                    fill: true, tension: 0.4, pointRadius: 3, pointHoverRadius: 5, borderWidth: 2,
                },
            ],
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { ...sharedScales, y: { ...sharedScales.y, ticks: { ...sharedScales.y.ticks, stepSize: 1 } } },
        },
    });

    // 2 â”€â”€ CLC Band Distribution (doughnut)
    new Chart(document.getElementById('bandChart'), {
        type: 'doughnut',
        data: {
            labels: bandLabels,
            datasets: [{ data: bands, backgroundColor: bandColors, borderWidth: 0, hoverOffset: 6 }],
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ' ' + ctx.label + ': ' + ctx.parsed + ' companies',
                    },
                },
            },
        },
    });

    // 3 â”€â”€ Assessment Score Distribution (all closed assessments)
    new Chart(document.getElementById('assessmentDistChart'), {
        type: 'doughnut',
        data: {
            labels: bandLabels,
            datasets: [{ data: assessmentDist, backgroundColor: bandColors, borderWidth: 0, hoverOffset: 6 }],
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ' ' + ctx.label + ': ' + ctx.parsed + ' assessments',
                    },
                },
            },
        },
    });

    // 5 â”€â”€ Industry Distribution (horizontal bar)
    @if($industryDist->isNotEmpty())
    (function() {
        const indLabels = @json($industryDist->pluck('industry')->values());
        const indCounts = @json($industryDist->pluck('company_count')->values());
        const total     = indCounts.reduce((a, b) => a + b, 0);

        const palette = [
            '#1F2192','#A329CC','#059669','#ca8a04','#2563eb',
            '#ea580c','#0891b2','#7c3aed','#dc2626','#65a30d',
            '#c2410c','#0e7490',
        ];

        new Chart(document.getElementById('industryChart'), {
            type: 'bar',
            data: {
                labels: indLabels,
                datasets: [{
                    data: indCounts,
                    backgroundColor: indLabels.map((_, i) => palette[i % palette.length] + 'cc'),
                    borderRadius: 5,
                    borderSkipped: false,
                }],
            },
            options: {
                indexAxis: 'y',
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` ${ctx.parsed.x} companies (${Math.round(ctx.parsed.x / total * 100)}%)`,
                        },
                    },
                },
                scales: {
                    x: {
                        grid: { color: gridColor },
                        ticks: { color: tickColor, font: baseFont, stepSize: 1 },
                        beginAtZero: true,
                    },
                    y: {
                        grid: { display: false },
                        ticks: { color: '#374151', font: { ...baseFont, size: 11 } },
                    },
                },
            },
        });
    })();
    @endif

    // 7 â”€â”€ Monthly Leakage (bar)
    new Chart(document.getElementById('leakageChart'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Leakage ($)',
                data: leakage,
                backgroundColor: 'rgba(220,38,38,.70)',
                borderRadius: 5,
                borderSkipped: false,
            }],
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: sharedScales.x,
                y: {
                    ...sharedScales.y,
                    ticks: {
                        ...sharedScales.y.ticks,
                        callback: v => '$' + (v >= 1000 ? (v/1000).toFixed(0)+'k' : v),
                    },
                },
            },
        },
    });

    // 8 â”€â”€ Average Score Trend (line)
    new Chart(document.getElementById('scoreChart'), {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Avg Score (%)',
                data: avgScore,
                borderColor: '#A329CC',
                backgroundColor: 'rgba(163,41,204,.08)',
                fill: true, tension: 0.4, pointRadius: 3, pointHoverRadius: 5, borderWidth: 2,
                spanGaps: true,
            }],
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: sharedScales.x,
                y: {
                    min: 0, max: 100,
                    grid: { color: gridColor },
                    ticks: { color: tickColor, font: baseFont, callback: v => v + '%' },
                },
            },
        },
    });
})();
</script>
@endpush

