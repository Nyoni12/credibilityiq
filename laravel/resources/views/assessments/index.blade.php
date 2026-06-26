@extends('layouts.app')
@section('title', 'Assessments')
@section('page-title', 'Assessments')

@section('content')
<div class="space-y-6" x-data="{ showCreate: false }">

    {{-- Header row --}}
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">
            Run surveys, collect stakeholder ratings, and generate your credibility scorecard.
        </p>
        @if($company->values()->count() > 0)
        <button @click="showCreate=true"
                class="inline-flex items-center gap-2 bg-brand-500 hover:bg-brand-600 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-all hover:shadow-lg hover:shadow-brand-500/30">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            New Assessment
        </button>
        @else
        <a href="{{ route('values.index') }}"
           class="inline-flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            Set Up Values First
        </a>
        @endif
    </div>

    {{-- Survey link banner --}}
    @if($assessments->where('status','open')->isNotEmpty())
    @php $openAssmt = $assessments->where('status','open')->first(); @endphp
    <div class="bg-cfa-50 border border-cfa-200 rounded-2xl p-4 flex flex-col md:flex-row md:items-center gap-3">
        <div class="flex items-center gap-3 flex-1">
            <div class="w-9 h-9 rounded-lg bg-cfa-500 flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-cfa-800">Active Survey: {{ $openAssmt->title }}</p>
                <p class="text-xs text-cfa-600">Share this link with your stakeholders:</p>
                <code class="text-xs text-cfa-700 bg-white px-2 py-0.5 rounded border border-cfa-200 mt-1 inline-block break-all">
                    {{ url('/survey/' . $company->survey_token) }}
                </code>
            </div>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <button onclick="navigator.clipboard.writeText('{{ url('/survey/' . $company->survey_token) }}')"
                    class="bg-white border border-cfa-300 text-cfa-700 text-xs font-medium px-3 py-2 rounded-lg hover:bg-cfa-50 transition-all">
                Copy Link
            </button>
            <form method="POST" action="{{ route('assessments.close', $openAssmt) }}">
                @csrf
                <button type="submit"
                        onclick="return confirm('Close this assessment and generate the scorecard?')"
                        class="bg-cfa-500 hover:bg-cfa-600 text-white text-xs font-semibold px-4 py-2 rounded-lg transition-all">
                    Close & Generate Score
                </button>
            </form>
        </div>
    </div>
    @endif

    {{-- Assessment list --}}
    @if($assessments->isEmpty())
    <div class="bg-white rounded-2xl border border-dashed border-gray-300 p-12 text-center">
        <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-brand-50 flex items-center justify-center">
            <svg class="w-7 h-7 text-brand-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
        </div>
        <h3 class="font-semibold text-gray-700 mb-2">No assessments yet</h3>
        <p class="text-gray-400 text-sm mb-5">Create your first assessment to start collecting stakeholder ratings.</p>
        <button @click="showCreate=true" class="bg-brand-500 hover:bg-brand-600 text-white font-semibold px-6 py-2.5 rounded-xl text-sm transition-all">
            Create First Assessment
        </button>
    </div>
    @else
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left text-xs font-semibold text-gray-500 px-6 py-3">Assessment</th>
                        <th class="text-left text-xs font-semibold text-gray-500 px-6 py-3">Status</th>
                        <th class="text-left text-xs font-semibold text-gray-500 px-6 py-3">Responses</th>
                        <th class="text-left text-xs font-semibold text-gray-500 px-6 py-3">Score</th>
                        <th class="text-left text-xs font-semibold text-gray-500 px-6 py-3">Created</th>
                        <th class="text-left text-xs font-semibold text-gray-500 px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($assessments as $a)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $a->title }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium
                                 {{ $a->status === 'open' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $a->status === 'open' ? 'bg-green-500 animate-pulse' : 'bg-gray-400' }}"></span>
                                {{ ucfirst($a->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-700 font-medium">{{ $a->survey_responses_count }}</td>
                        <td class="px-6 py-4">
                            @if($a->overall_score !== null)
                            <span class="font-bold" style="color: {{ \App\Services\ScoringService::gradeColor($a->overall_score) }}">
                                {{ number_format($a->overall_score, 1) }}
                            </span>
                            @else <span class="text-gray-400">—</span> @endif
                        </td>
                        <td class="px-6 py-4 text-gray-500 text-xs">{{ $a->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($a->status === 'closed')
                                <a href="{{ route('scorecard.show', $a) }}" class="text-brand-500 hover:text-brand-700 font-medium text-xs">Scorecard</a>
                                <a href="{{ route('report.download', $a) }}" class="text-gray-500 hover:text-gray-700 font-medium text-xs">PDF</a>
                                @else
                                @if($a->survey_responses_count > 0)
                                <form method="POST" action="{{ route('assessments.close', $a) }}">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Close and generate scorecard?')"
                                            class="text-cfa-600 hover:text-cfa-800 font-medium text-xs">Close</button>
                                </form>
                                @endif
                                @endif
                                <form method="POST" action="{{ route('assessments.destroy', $a) }}"
                                      onsubmit="return confirm('Delete this assessment?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-600 font-medium text-xs">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Create modal --}}
    <div x-show="showCreate" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
        <div @click="showCreate=false" class="absolute inset-0 bg-black/50"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md mx-4">
            <h3 class="font-bold text-gray-900 mb-4">New Assessment</h3>
            <form method="POST" action="{{ route('assessments.store') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Assessment Title</label>
                    <input type="text" name="title" required
                           value="{{ 'Q' . ceil(now()->month/3) . ' ' . now()->year . ' Credibility Assessment' }}"
                           class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
                <p class="text-xs text-gray-500 mb-5">
                    After creating, a survey link will be generated. Share it with your stakeholders to collect ratings.
                </p>
                <div class="flex items-center gap-3">
                    <button type="submit" class="flex-1 bg-brand-500 hover:bg-brand-600 text-white font-semibold py-3 rounded-xl text-sm transition-all">
                        Create Assessment
                    </button>
                    <button type="button" @click="showCreate=false"
                            class="flex-1 border border-gray-300 text-gray-700 font-medium py-3 rounded-xl text-sm hover:bg-gray-50 transition-all">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
