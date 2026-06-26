@extends('layouts.app')
@section('title', 'Companies')
@section('page-title', 'Companies')

@section('content')
<div class="space-y-4">

    {{-- Search bar --}}
    <div class="flex items-center justify-between gap-4">
        <form method="GET" class="flex items-center gap-2 flex-1 max-w-sm">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search companies…"
                       class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 bg-white">
            </div>
            <button type="submit" class="bg-brand-500 text-white px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-brand-600 transition-colors">Search</button>
        </form>
        <div class="text-xs text-gray-500">{{ $companies->total() }} companies</div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left text-xs font-semibold text-gray-500 px-6 py-3">Company</th>
                        <th class="text-left text-xs font-semibold text-gray-500 px-6 py-3">Industry</th>
                        <th class="text-left text-xs font-semibold text-gray-500 px-6 py-3">Users</th>
                        <th class="text-left text-xs font-semibold text-gray-500 px-6 py-3">Assessments</th>
                        <th class="text-left text-xs font-semibold text-gray-500 px-6 py-3">Latest Score</th>
                        <th class="text-left text-xs font-semibold text-gray-500 px-6 py-3">Status</th>
                        <th class="text-left text-xs font-semibold text-gray-500 px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($companies as $company)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-brand-50 flex items-center justify-center text-brand-600 font-bold text-sm shrink-0">
                                    {{ strtoupper(substr($company->name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $company->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $company->domain ?? '—' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $company->industry ?? '—' }}</td>
                        <td class="px-6 py-4 text-gray-700 font-medium">{{ $company->users_count }}</td>
                        <td class="px-6 py-4 text-gray-700 font-medium">{{ $company->assessments_count }}</td>
                        <td class="px-6 py-4">
                            @if($company->latestAssessment?->overall_score !== null)
                            <span class="font-bold" style="color: {{ \App\Services\ScoringService::gradeColor($company->latestAssessment->overall_score) }}">
                                {{ number_format($company->latestAssessment->overall_score, 1) }}
                            </span>
                            @else
                            <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium
                                 {{ $company->is_active ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $company->is_active ? 'bg-green-500' : 'bg-red-400' }}"></span>
                                {{ $company->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.companies.show', $company) }}"
                               class="text-brand-500 hover:text-brand-700 font-medium text-xs">
                                Manage →
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-400 text-sm">No companies found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($companies->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $companies->withQueryString()->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
