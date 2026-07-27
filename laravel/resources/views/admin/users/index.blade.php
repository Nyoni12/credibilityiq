@extends('layouts.app')
@section('title', 'Users & Onboarding')

@section('content')
<div class="space-y-6"
     x-data="{
        showAddAdmin:    false,
        showOnboard:     false,
        showPassword:    false,
        pwUserId:        null,
        pwUserName:      '',
        openPw(id, name) { this.pwUserId = id; this.pwUserName = name; this.showPassword = true; }
     }">

{{-- ── Header ─────────────────────────────────────────────────────── --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Users &amp; Onboarding</h1>
        <p class="text-sm text-gray-500 mt-0.5">Manage super admins, onboard clients, and control user access.</p>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
        <button @click="showOnboard=true"
                class="inline-flex items-center gap-2 bg-cfa-500 hover:bg-cfa-600 text-white font-semibold px-4 py-2.5 rounded-xl text-sm transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            Onboard Company
        </button>
        <button @click="showAddAdmin=true"
                class="inline-flex items-center gap-2 bg-accent-500 hover:bg-accent-600 text-white font-semibold px-4 py-2.5 rounded-xl text-sm transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            Add Super Admin
        </button>
    </div>
</div>

{{-- ── Super Admins panel ──────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-accent-500 inline-block"></span>
            <h2 class="text-sm font-bold text-gray-900">Super Admins</h2>
        </div>
        <span class="text-xs bg-accent-100 text-accent-700 font-semibold px-2.5 py-0.5 rounded-full">{{ $superadmins->count() }}</span>
    </div>
    <div class="divide-y divide-gray-50">
        @foreach($superadmins as $sa)
        <div class="flex items-center justify-between px-6 py-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0"
                     style="background:linear-gradient(135deg,#A329CC,#1F2192)">
                    {{ $sa->initials }}
                </div>
                <div>
                    <p class="font-semibold text-gray-900 text-sm">{{ $sa->full_name }}
                        @if($sa->id === auth()->id())
                        <span class="ml-1.5 text-xs text-accent-500 font-medium">(you)</span>
                        @endif
                    </p>
                    <p class="text-xs text-gray-400">{{ $sa->email }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button @click="openPw({{ $sa->id }}, '{{ addslashes($sa->full_name) }}')"
                        class="text-xs text-brand-500 hover:text-brand-700 font-medium">
                    Change Password
                </button>
                @if($sa->id !== auth()->id())
                <form method="POST" action="{{ route('admin.users.destroy', $sa) }}"
                      onsubmit="return confirm('Remove super admin {{ addslashes($sa->full_name) }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs text-red-400 hover:text-red-600 font-medium">Remove</button>
                </form>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- ── Search bar ──────────────────────────────────────────────────── --}}
<div class="flex items-center gap-3">
    <form method="GET" class="flex items-center gap-2 flex-1 max-w-sm">
        <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" value="{{ $search }}" placeholder="Search company users…"
                   class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 bg-white">
        </div>
        <button type="submit" class="bg-brand-500 text-white px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-brand-600 transition-colors">Search</button>
    </form>
    <span class="text-xs text-gray-500">{{ $users->total() }} company users</span>
</div>

{{-- ── Company users table ─────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h2 class="text-sm font-bold text-gray-900">Company Users</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left text-xs font-semibold text-gray-500 px-6 py-3">User</th>
                    <th class="text-left text-xs font-semibold text-gray-500 px-6 py-3">Company</th>
                    <th class="text-left text-xs font-semibold text-gray-500 px-6 py-3">Role</th>
                    <th class="text-left text-xs font-semibold text-gray-500 px-6 py-3">Status</th>
                    <th class="text-left text-xs font-semibold text-gray-500 px-6 py-3">Joined</th>
                    <th class="text-left text-xs font-semibold text-gray-500 px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-brand-300 to-brand-600 flex items-center justify-center text-white text-xs font-bold shrink-0">
                                {{ $user->initials }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $user->full_name }}</p>
                                <p class="text-xs text-gray-400">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-600 text-sm">{{ $user->company?->name ?? '—' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-brand-50 text-brand-700">{{ ucfirst($user->role) }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium
                             {{ $user->is_active ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $user->is_active ? 'bg-green-500' : 'bg-red-400' }}"></span>
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-500 text-xs">{{ $user->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3 flex-wrap">
                            <button @click="openPw({{ $user->id }}, '{{ addslashes($user->full_name) }}')"
                                    class="text-xs text-brand-500 hover:text-brand-700 font-medium">
                                Password
                            </button>
                            <form method="POST" action="{{ route('admin.users.toggle', $user) }}">
                                @csrf
                                <button type="submit"
                                        class="text-xs {{ $user->is_active ? 'text-red-500 hover:text-red-700' : 'text-green-600 hover:text-green-800' }} font-medium">
                                    {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                  onsubmit="return confirm('Delete {{ addslashes($user->full_name) }}? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-400 hover:text-red-600 font-medium">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400 text-sm">No company users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $users->withQueryString()->links() }}
    </div>
    @endif
</div>

{{-- ════════════════════════════════════════════════════════════════
     MODAL: Add Super Admin
════════════════════════════════════════════════════════════════ --}}
<div x-show="showAddAdmin" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div @click="showAddAdmin=false" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md" @click.stop>
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-accent-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-accent-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                </div>
                <h3 class="font-bold text-gray-900">Add Super Admin</h3>
            </div>
            <button @click="showAddAdmin=false" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.users.superadmin') }}" class="px-6 py-5 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">First Name</label>
                    <input type="text" name="first_name" required
                           class="w-full px-3 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-accent-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Last Name</label>
                    <input type="text" name="last_name" required
                           class="w-full px-3 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-accent-500">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Email Address</label>
                <input type="email" name="email" required
                       class="w-full px-3 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-accent-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Password</label>
                <input type="password" name="password" required minlength="8"
                       class="w-full px-3 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-accent-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation" required
                       class="w-full px-3 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-accent-500">
            </div>
            <div class="flex gap-3 pt-1">
                <button type="submit"
                        class="flex-1 bg-accent-500 hover:bg-accent-600 text-white font-semibold py-2.5 rounded-xl text-sm transition-all">
                    Create Super Admin
                </button>
                <button type="button" @click="showAddAdmin=false"
                        class="flex-1 border border-gray-300 text-gray-700 font-medium py-2.5 rounded-xl text-sm hover:bg-gray-50 transition-all">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════════════
     MODAL: Onboard Company
════════════════════════════════════════════════════════════════ --}}
<div x-show="showOnboard" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div @click="showOnboard=false" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto" @click.stop>
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 sticky top-0 bg-white z-10">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-cfa-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-cfa-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <h3 class="font-bold text-gray-900">Onboard New Company</h3>
            </div>
            <button @click="showOnboard=false" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.companies.onboard') }}" class="px-6 py-5 space-y-5">
            @csrf
            {{-- Company details --}}
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Company Details</p>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Company Name <span class="text-red-400">*</span></label>
                        <input type="text" name="company_name" required
                               class="w-full px-3 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-cfa-500">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Industry</label>
                            <input type="text" name="industry" placeholder="e.g. Finance, NGO"
                                   class="w-full px-3 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-cfa-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Annual Revenue (USD)</label>
                            <input type="number" name="annual_revenue" min="0" placeholder="0"
                                   class="w-full px-3 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-cfa-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Subscription Tier</label>
                        <select name="subscription_tier"
                                class="w-full px-3 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-cfa-500 bg-white">
                            <option value="basic">Basic</option>
                            <option value="standard" selected>Standard</option>
                            <option value="premium">Premium</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-5">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Admin User Login</p>
                <div class="space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">First Name <span class="text-red-400">*</span></label>
                            <input type="text" name="first_name" required
                                   class="w-full px-3 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-cfa-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Last Name <span class="text-red-400">*</span></label>
                            <input type="text" name="last_name" required
                                   class="w-full px-3 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-cfa-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Email Address <span class="text-red-400">*</span></label>
                        <input type="email" name="email" required
                               class="w-full px-3 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-cfa-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Password <span class="text-red-400">*</span></label>
                        <input type="password" name="password" required minlength="8"
                               class="w-full px-3 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-cfa-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Confirm Password <span class="text-red-400">*</span></label>
                        <input type="password" name="password_confirmation" required
                               class="w-full px-3 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-cfa-500">
                    </div>
                </div>
            </div>

            <div class="flex gap-3 pt-1 sticky bottom-0 bg-white pb-1">
                <button type="submit"
                        class="flex-1 bg-cfa-500 hover:bg-cfa-600 text-white font-semibold py-2.5 rounded-xl text-sm transition-all">
                    Onboard Company
                </button>
                <button type="button" @click="showOnboard=false"
                        class="flex-1 border border-gray-300 text-gray-700 font-medium py-2.5 rounded-xl text-sm hover:bg-gray-50 transition-all">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════════════
     MODAL: Change Password
════════════════════════════════════════════════════════════════ --}}
<div x-show="showPassword" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div @click="showPassword=false" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm" @click.stop>
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-brand-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 text-sm">Change Password</h3>
                    <p class="text-xs text-gray-400" x-text="pwUserName"></p>
                </div>
            </div>
            <button @click="showPassword=false" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" :action="'/admin/users/' + pwUserId + '/password'" class="px-6 py-5 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">New Password</label>
                <input type="password" name="password" required minlength="8"
                       class="w-full px-3 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Confirm New Password</label>
                <input type="password" name="password_confirmation" required
                       class="w-full px-3 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>
            <div class="flex gap-3 pt-1">
                <button type="submit"
                        class="flex-1 bg-brand-500 hover:bg-brand-600 text-white font-semibold py-2.5 rounded-xl text-sm transition-all">
                    Update Password
                </button>
                <button type="button" @click="showPassword=false"
                        class="flex-1 border border-gray-300 text-gray-700 font-medium py-2.5 rounded-xl text-sm hover:bg-gray-50 transition-all">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

</div>
@endsection
