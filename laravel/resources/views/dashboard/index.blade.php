@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="space-y-8">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Welcome back, {{ explode(' ', auth()->user()->full_name)[0] }}
            </h1>
            <p class="text-gray-500 text-sm mt-1">{{ $company->name }}</p>
        </div>
        <div class="flex gap-3 flex-wrap">
            <a href="{{ route('assessments.export') }}"
               class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                Export CSV
            </a>
            <a href="{{ route('values.index') }}"
               class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                Setup Values
            </a>
            <a href="{{ route('assessments.index') }}"
               class="px-4 py-2 bg-brand-500 text-white rounded-lg text-sm font-medium hover:bg-brand-600 transition-colors">
                + New Assessment
            </a>
        </div>
    </div>

    {{-- Onboarding guide for new companies --}}
    @if($valuesCount === 0 || $totalAssessments === 0)
    <div class="bg-gradient-to-r from-brand-500 to-brand-600 rounded-xl p-6 text-white">
        <h2 class="font-bold text-lg mb-1">Get started — 3 quick steps</h2>
        <p class="text-brand-100 text-sm mb-4">Complete setup to run your first credibility assessment.</p>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            @php $step1 = $valuesCount > 0; $step2 = $totalAssessments > 0; $step3 = false; @endphp
            <div class="bg-white/10 rounded-lg p-4 {{ $step1 ? 'opacity-70' : '' }}">
                <div class="flex items-center gap-2 mb-2">
                    @if($step1)
                    <span class="w-6 h-6 rounded-full bg-cfa-500 flex items-center justify-center text-xs">✓</span>
                    @else
                    <span class="w-6 h-6 rounded-full border-2 border-white/50 flex items-center justify-center text-xs font-bold">1</span>
                    @endif
                    <span class="font-semibold text-sm">Configure Values</span>
                </div>
                <p class="text-brand-100 text-xs">Select your 12 CFA Credibility Building Blocks and set financial weights.</p>
                @if(!$step1)
                <a href="{{ route('values.index') }}" class="mt-3 inline-block px-3 py-1.5 bg-white text-brand-600 rounded-lg text-xs font-semibold hover:bg-brand-50 transition-colors">
                    Set up values →
                </a>
                @endif
            </div>
            <div class="bg-white/10 rounded-lg p-4 {{ $step2 ? 'opacity-70' : '' }}">
                <div class="flex items-center gap-2 mb-2">
                    @if($step2)
                    <span class="w-6 h-6 rounded-full bg-cfa-500 flex items-center justify-center text-xs">✓</span>
                    @elseif($step1)
                    <span class="w-6 h-6 rounded-full border-2 border-white/50 flex items-center justify-center text-xs font-bold">2</span>
                    @else
                    <span class="w-6 h-6 rounded-full border-2 border-white/20 flex items-center justify-center text-xs font-bold text-white/40">2</span>
                    @endif
                    <span class="font-semibold text-sm {{ !$step1 ? 'text-white/50' : '' }}">Create Assessment</span>
                </div>
                <p class="text-brand-100 text-xs {{ !$step1 ? 'opacity-50' : '' }}">Start a new assessment and get your unique survey link to share with staff.</p>
                @if($step1 && !$step2)
                <a href="{{ route('assessments.index') }}" class="mt-3 inline-block px-3 py-1.5 bg-white text-brand-600 rounded-lg text-xs font-semibold hover:bg-brand-50 transition-colors">
                    Create assessment →
                </a>
                @endif
            </div>
            <div class="bg-white/10 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-6 h-6 rounded-full border-2 border-white/{{ $step2 ? '50' : '20' }} flex items-center justify-center text-xs font-bold {{ !$step2 ? 'text-white/40' : '' }}">3</span>
                    <span class="font-semibold text-sm {{ !$step2 ? 'text-white/50' : '' }}">Collect Responses</span>
                </div>
                <p class="text-brand-100 text-xs {{ !$step2 ? 'opacity-50' : '' }}">Share the survey link with your stakeholders, then close it to generate your scorecard.</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Stat cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php $latestScore = $latestClosed?->overall_score ?? null; @endphp

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-sm text-gray-500 font-medium">Overall Score</p>
            <p class="text-3xl font-bold mt-1 text-brand-600">{{ $latestScore !== null ? number_format($latestScore, 1).'%' : '—' }}</p>
            <p class="text-xs text-gray-400 mt-1">Latest assessment</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-sm text-gray-500 font-medium">Total Responses</p>
            <p class="text-3xl font-bold mt-1 text-green-600">{{ $totalResponses }}</p>
            <p class="text-xs text-gray-400 mt-1">All assessments</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-sm text-gray-500 font-medium">Assessments</p>
            <p class="text-3xl font-bold mt-1 text-blue-600">{{ $totalAssessments }}</p>
            <p class="text-xs text-gray-400 mt-1">All time</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-sm text-gray-500 font-medium">Values Configured</p>
            <p class="text-3xl font-bold mt-1 text-purple-600">{{ $valuesCount }}</p>
            <p class="text-xs text-gray-400 mt-1">Max 12</p>
        </div>
    </div>

    {{-- Survey link --}}
    @if($surveyToken)
    <div class="bg-white rounded-xl border border-brand-200 shadow-sm p-5">
        <h2 class="font-semibold text-gray-900 mb-2">Staff Survey Link</h2>
        <p class="text-sm text-gray-500 mb-3">Share this link with your staff for anonymous assessment</p>
        <div class="flex gap-3 items-center flex-wrap">
            <code class="flex-1 text-xs bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-gray-700 truncate">
                {{ url('/survey/' . $surveyToken) }}
            </code>
            <button onclick="navigator.clipboard.writeText('{{ url('/survey/' . $surveyToken) }}').then(()=>alert('Survey link copied!'))"
                    class="px-4 py-2 bg-brand-500 text-white text-sm rounded-lg hover:bg-brand-600 transition-colors whitespace-nowrap">
                Copy Link
            </button>
        </div>
    </div>
    @endif

    {{-- Assessments table --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between flex-wrap gap-2">
            <h2 class="font-semibold text-gray-900">Assessments</h2>
            <a href="{{ route('assessments.index') }}"
               class="px-3 py-1.5 bg-brand-50 text-brand-600 rounded-lg text-xs font-semibold hover:bg-brand-100 transition-colors">
                + New
            </a>
        </div>

        @if($assessments->isEmpty())
        <div class="p-10 text-center text-gray-400">
            <p class="text-lg mb-2">No assessments yet</p>
            <a href="{{ route('assessments.index') }}"
               class="mt-2 inline-block px-4 py-2 bg-brand-500 text-white rounded-lg text-sm">
                Create your first assessment
            </a>
        </div>
        @else
        <div class="overflow-x-auto -webkit-overflow-scrolling-touch">
        <table class="w-full text-sm min-w-[540px]">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-5 py-3 font-medium text-gray-600">Title</th>
                    <th class="text-left px-5 py-3 font-medium text-gray-600">Status</th>
                    <th class="text-left px-5 py-3 font-medium text-gray-600">Responses</th>
                    <th class="text-left px-5 py-3 font-medium text-gray-600">Score</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($assessments->take(10) as $a)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3 font-medium text-gray-900">{{ $a->title }}</td>
                    <td class="px-5 py-3">
                        <span class="text-xs px-2 py-1 rounded-full font-medium
                            {{ $a->status === 'open' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $a->status === 'open' ? 'Active' : 'Closed' }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-gray-600">{{ $a->survey_responses_count }}</td>
                    <td class="px-5 py-3">
                        @if($a->overall_score !== null)
                        <span class="text-sm font-bold"
                              style="color: {{ \App\Services\ScoringService::gradeColor($a->overall_score) }}">
                            {{ number_format($a->overall_score, 1) }}%
                        </span>
                        @else
                        <span class="text-xs text-gray-400">
                            {{ $a->survey_responses_count === 0 ? 'Awaiting responses' : '—' }}
                        </span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-right">
                        @if($a->status === 'closed')
                        <a href="{{ route('scorecard.show', $a) }}"
                           class="px-3 py-1.5 bg-brand-500 text-white rounded-lg text-xs font-semibold hover:bg-brand-600 transition-colors">
                            View Scorecard →
                        </a>
                        @elseif($a->survey_responses_count > 0)
                        <a href="{{ route('assessments.index') }}"
                           class="px-3 py-1.5 bg-brand-500 text-white rounded-lg text-xs font-semibold hover:bg-brand-600 transition-colors">
                            Calculate Score →
                        </a>
                        @else
                        <span class="text-xs text-gray-300 px-3 py-1.5">No responses yet</span>
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
<script defer src="/js/chart.min.js"></script>
<script>
@if($trendData->count() >= 2)
new Chart(document.getElementById('trendChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: {!! $trendData->pluck('label')->toJson() !!},
        datasets: [{
            label: 'Credibility Score',
            data: {!! $trendData->pluck('score')->toJson() !!},
            borderColor: '#1F2192',
            backgroundColor: 'rgba(31,33,146,0.08)',
            fill: true, tension: 0.4,
            pointBackgroundColor: '#1F2192',
            pointRadius: 5, pointHoverRadius: 7, borderWidth: 2.5,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { min: 0, max: 100, ticks: { font: { size: 11 }, color: '#9ca3af' }, grid: { color: '#f3f4f6' } },
            x: { ticks: { font: { size: 11 }, color: '#9ca3af' }, grid: { display: false } }
        }
    }
});
@endif
</script>
@endpush
