@extends('layouts.app')
@section('title', 'SuperAdmin Dashboard')
@section('page-title', 'SuperAdmin Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Stat cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        @foreach([
            ['Total Companies',   $stats['companies'],        'bg-brand-50',  'text-brand-500',  'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
            ['Active Companies',  $stats['active_companies'], 'bg-cfa-50',    'text-cfa-500',    'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['Total Users',       $stats['users'],            'bg-accent-50', 'text-accent-500', 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
            ['Total Assessments', $stats['assessments'],      'bg-blue-50',   'text-blue-500',   'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
            ['Open Assessments',  $stats['open_assessments'], 'bg-yellow-50', 'text-yellow-600', 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
        ] as [$label, $value, $bg, $color, $icon])
        <div class="bg-white rounded-2xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">{{ $label }}</p>
                    <p class="text-3xl font-black text-gray-900">{{ $value }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl {{ $bg }} flex items-center justify-center">
                    <svg class="w-5 h-5 {{ $color }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/>
                    </svg>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Quick links --}}
    <div class="grid md:grid-cols-3 gap-4">
        <a href="{{ route('admin.companies.index') }}"
           class="flex items-center gap-4 bg-white rounded-xl border border-gray-200 p-4 hover:border-brand-300 hover:shadow-md transition-all group">
            <div class="w-12 h-12 rounded-xl bg-brand-50 flex items-center justify-center group-hover:bg-brand-100 transition-colors">
                <svg class="w-6 h-6 text-brand-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <div>
                <p class="font-semibold text-gray-900 text-sm">Manage Companies</p>
                <p class="text-xs text-gray-400">View, edit, activate companies</p>
            </div>
        </a>
        <a href="{{ route('admin.users.index') }}"
           class="flex items-center gap-4 bg-white rounded-xl border border-gray-200 p-4 hover:border-accent-300 hover:shadow-md transition-all group">
            <div class="w-12 h-12 rounded-xl bg-accent-50 flex items-center justify-center group-hover:bg-accent-100 transition-colors">
                <svg class="w-6 h-6 text-accent-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <div>
                <p class="font-semibold text-gray-900 text-sm">Manage Users</p>
                <p class="text-xs text-gray-400">View and control user accounts</p>
            </div>
        </a>
        <div class="flex items-center gap-4 bg-white rounded-xl border border-gray-200 p-4">
            <div class="w-12 h-12 rounded-xl bg-gray-50 flex items-center justify-center">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <p class="font-semibold text-gray-500 text-sm">Platform Settings</p>
                <p class="text-xs text-gray-400">Coming soon</p>
            </div>
        </div>
    </div>

    {{-- Recent companies --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-900 text-sm">Recent Companies</h3>
            <a href="{{ route('admin.companies.index') }}" class="text-brand-500 text-xs font-medium hover:underline">View all →</a>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($recentCompanies as $company)
            <div class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-brand-50 flex items-center justify-center text-brand-600 font-bold text-sm">
                        {{ strtoupper(substr($company->name, 0, 2)) }}
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 text-sm">{{ $company->name }}</p>
                        <p class="text-xs text-gray-400">{{ $company->industry ?? 'No industry' }} · {{ $company->users_count }} users</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $company->is_active ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }}">
                        {{ $company->is_active ? 'Active' : 'Inactive' }}
                    </span>
                    <a href="{{ route('admin.companies.show', $company) }}" class="text-brand-500 hover:text-brand-700 text-xs font-medium">Manage →</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
