@extends('layouts.app')
@section('title', $company->name)
@section('page-title', $company->name)

@section('content')
<div class="space-y-6" x-data="{ tab: 'info' }">

    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.companies.index') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-brand-50 flex items-center justify-center text-brand-600 font-black text-lg">
                {{ strtoupper(substr($company->name, 0, 2)) }}
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $company->name }}</h2>
                <div class="flex items-center gap-2 mt-0.5">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $company->is_active ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }}">
                        {{ $company->is_active ? 'Active' : 'Inactive' }}
                    </span>
                    <span class="text-xs text-gray-400">{{ ucfirst($company->subscription_tier) }} Plan</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex items-center gap-1 bg-gray-100 rounded-xl p-1 w-fit">
        @foreach(['info' => 'Company Info', 'values' => 'Values ('.count($company->values).')', 'assessments' => 'Assessments ('.count($company->assessments).')', 'users' => 'Users ('.count($company->users).')'] as $t => $label)
        <button @click="tab='{{ $t }}'" :class="tab==='{{ $t }}' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-700'"
                class="px-4 py-2 rounded-lg text-sm font-medium transition-all">{{ $label }}</button>
        @endforeach
    </div>

    {{-- Info tab --}}
    <div x-show="tab==='info'">
        <form method="POST" action="{{ route('admin.companies.update', $company) }}" enctype="multipart/form-data"
              class="bg-white rounded-2xl border border-gray-200 p-6">
            @csrf @method('PUT')
            <div class="grid md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Company Name</label>
                    <input type="text" name="name" value="{{ old('name', $company->name) }}" required
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Industry</label>
                    <input type="text" name="industry" value="{{ old('industry', $company->industry) }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Domain</label>
                    <input type="url" name="domain" value="{{ old('domain', $company->domain) }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500" placeholder="https://example.com">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Annual Revenue (USD)</label>
                    <input type="number" name="annual_revenue" value="{{ old('annual_revenue', $company->annual_revenue) }}" min="0"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subscription Tier</label>
                    <select name="subscription_tier" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 bg-white">
                        @foreach(['basic','standard','premium'] as $tier)
                        <option value="{{ $tier }}" {{ $company->subscription_tier === $tier ? 'selected' : '' }}>{{ ucfirst($tier) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ $company->is_active ? 'checked' : '' }}
                           class="w-4 h-4 text-brand-500 border-gray-300 rounded">
                    <label for="is_active" class="text-sm font-medium text-gray-700">Company is Active</label>
                </div>
            </div>
            <div class="flex items-center gap-3 mt-6">
                <button type="submit" class="bg-brand-500 hover:bg-brand-600 text-white font-semibold px-6 py-2.5 rounded-xl text-sm transition-all">Save Changes</button>
            </div>
        </form>

        {{-- Survey token --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 mt-4">
            <h4 class="font-bold text-gray-900 text-sm mb-3">Survey Link</h4>
            <div class="flex items-center gap-3">
                <code class="flex-1 bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-xs text-gray-600 break-all">
                    {{ url('/survey/' . $company->survey_token) }}
                </code>
                <form method="POST" action="{{ route('admin.companies.token', $company) }}">
                    @csrf
                    <button type="submit" class="shrink-0 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-semibold px-4 py-2.5 rounded-xl transition-all border border-red-200">
                        Regenerate Token
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Values tab --}}
    <div x-show="tab==='values'" x-cloak>
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left text-xs font-semibold text-gray-500 px-6 py-3">Value</th>
                        <th class="text-left text-xs font-semibold text-gray-500 px-6 py-3">Weight %</th>
                        <th class="text-left text-xs font-semibold text-gray-500 px-6 py-3">Description</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($company->values as $v)
                    <tr><td class="px-6 py-3 font-medium text-gray-900">{{ $v->name }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $v->weight_percentage }}%</td>
                        <td class="px-6 py-3 text-gray-500 text-xs">{{ $v->description ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="px-6 py-8 text-center text-gray-400 text-sm">No values configured.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Assessments tab --}}
    <div x-show="tab==='assessments'" x-cloak>
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left text-xs font-semibold text-gray-500 px-6 py-3">Title</th>
                        <th class="text-left text-xs font-semibold text-gray-500 px-6 py-3">Status</th>
                        <th class="text-left text-xs font-semibold text-gray-500 px-6 py-3">Score</th>
                        <th class="text-left text-xs font-semibold text-gray-500 px-6 py-3">Date</th>
                        <th class="text-left text-xs font-semibold text-gray-500 px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($company->assessments as $a)
                    <tr>
                        <td class="px-6 py-3 font-medium text-gray-900">{{ $a->title }}</td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs {{ $a->status === 'open' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-600' }}">{{ ucfirst($a->status) }}</span>
                        </td>
                        <td class="px-6 py-3">
                            @if($a->overall_score !== null)
                            <span class="font-bold" style="color:{{ \App\Services\ScoringService::gradeColor($a->overall_score) }}">{{ number_format($a->overall_score,1) }}</span>
                            @else <span class="text-gray-400">—</span> @endif
                        </td>
                        <td class="px-6 py-3 text-gray-500">{{ $a->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-3">
                            @if($a->status==='closed')
                            <a href="{{ route('scorecard.show', $a) }}" class="text-brand-500 text-xs font-medium">Scorecard →</a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-8 text-center text-gray-400 text-sm">No assessments.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Users tab --}}
    <div x-show="tab==='users'" x-cloak>
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden mb-4">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left text-xs font-semibold text-gray-500 px-6 py-3">Name</th>
                        <th class="text-left text-xs font-semibold text-gray-500 px-6 py-3">Email</th>
                        <th class="text-left text-xs font-semibold text-gray-500 px-6 py-3">Role</th>
                        <th class="text-left text-xs font-semibold text-gray-500 px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($company->users as $u)
                    <tr>
                        <td class="px-6 py-3 font-medium text-gray-900">{{ $u->full_name }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $u->email }}</td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs bg-brand-50 text-brand-700">{{ ucfirst($u->role) }}</span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs {{ $u->is_active ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }}">
                                {{ $u->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-6 py-8 text-center text-gray-400 text-sm">No users.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Add user form --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <h4 class="font-bold text-gray-900 text-sm mb-4">Add User to Company</h4>
            <form method="POST" action="{{ route('admin.companies.users.store', $company) }}" class="grid md:grid-cols-3 gap-4">
                @csrf
                <input type="text" name="first_name" placeholder="First name" required class="px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                <input type="text" name="last_name" placeholder="Last name" required class="px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                <input type="email" name="email" placeholder="Email" required class="px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                <input type="password" name="password" placeholder="Password" required minlength="8" class="px-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                <select name="role" class="px-4 py-2.5 rounded-xl border border-gray-300 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <option value="admin">Admin</option>
                    <option value="viewer">Viewer</option>
                </select>
                <button type="submit" class="bg-brand-500 hover:bg-brand-600 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-all">Add User</button>
            </form>
        </div>
    </div>

</div>
@endsection
