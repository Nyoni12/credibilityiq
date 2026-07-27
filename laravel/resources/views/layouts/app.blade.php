<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'CredibilityIQ') | CredibilityIQ</title>
<link rel="icon" type="image/png" href="/images/logo.png">
<link rel="preload" href="/fonts/inter-var.woff2" as="font" type="font/woff2" crossorigin>
<link rel="stylesheet" href="/css/app.css">
<script defer src="/js/alpine.min.js"></script>
</head>
<body class="min-h-screen bg-gray-50 font-sans" x-data="{ mobileMenu: false }">

{{-- ── Header ── --}}
<header class="bg-white border-b border-gray-100 shadow-sm sticky top-0 z-30">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo + desktop nav --}}
            <div class="flex items-center gap-8">
                <a href="{{ auth()->user()->isSuperAdmin() ? route('admin.dashboard') : route('dashboard') }}">
                    <img src="{{ asset('images/logo.png') }}" alt="Credibility Factory Afrique" class="h-10 w-auto">
                </a>

                <nav class="hidden md:flex gap-1">
                    @if(auth()->user()->isSuperAdmin())
                        @foreach([['Dashboard', route('admin.dashboard'), 'admin.dashboard'], ['Companies', route('admin.companies.index'), 'admin.companies.*'], ['Users', route('admin.users.index'), 'admin.users.*'], ['Support', route('admin.support.index'), 'admin.support.*'], ['Activity Log', route('admin.activity-log'), 'admin.activity-log']] as [$label, $href, $pattern])
                        <a href="{{ $href }}"
                           class="px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs($pattern) ? 'bg-brand-500 text-white' : 'text-brand-700 hover:bg-brand-50 hover:text-brand-900' }}">
                            {{ $label }}
                        </a>
                        @endforeach
                    @else
                        @foreach([['Dashboard', route('dashboard'), 'dashboard'], ['Values Setup', route('values.index'), 'values.*'], ['Assessments', route('assessments.index'), 'assessments.*'], ['Support', route('support.index'), 'support.*'], ['Activity Log', route('activity-log'), 'activity-log']] as [$label, $href, $pattern])
                        <a href="{{ $href }}"
                           class="px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs($pattern) ? 'bg-brand-500 text-white' : 'text-brand-700 hover:bg-brand-50 hover:text-brand-900' }}">
                            {{ $label }}
                        </a>
                        @endforeach
                    @endif
                </nav>
            </div>

            {{-- Right: user info + logout --}}
            <div class="flex items-center gap-3">
                <span class="text-gray-600 text-sm hidden sm:block">
                    {{ auth()->user()->full_name }}
                    @if(auth()->user()->isSuperAdmin())
                    <span class="ml-2 bg-accent-100 text-accent-700 text-xs px-2 py-0.5 rounded-full font-semibold">Super Admin</span>
                    @endif
                </span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="text-sm text-brand-600 hover:text-brand-800 px-3 py-1.5 rounded border border-brand-200 hover:border-brand-400 transition-colors">
                        Logout
                    </button>
                </form>

                {{-- Mobile hamburger --}}
                <button @click="mobileMenu=!mobileMenu" class="md:hidden p-2 rounded-md text-gray-500 hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path x-show="!mobileMenu" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="mobileMenu" x-cloak stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div x-show="mobileMenu" x-cloak class="md:hidden border-t border-gray-100 bg-white px-4 py-3 space-y-1">
        @if(auth()->user()->isSuperAdmin())
            @foreach([['Dashboard', route('admin.dashboard'), 'admin.dashboard'], ['Companies', route('admin.companies.index'), 'admin.companies.*'], ['Users', route('admin.users.index'), 'admin.users.*'], ['Support', route('admin.support.index'), 'admin.support.*'], ['Activity Log', route('admin.activity-log'), 'admin.activity-log']] as [$label, $href, $pattern])
            <a href="{{ $href }}" class="block px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs($pattern) ? 'bg-brand-500 text-white' : 'text-brand-700 hover:bg-brand-50' }}">{{ $label }}</a>
            @endforeach
        @else
            @foreach([['Dashboard', route('dashboard'), 'dashboard'], ['Values Setup', route('values.index'), 'values.*'], ['Assessments', route('assessments.index'), 'assessments.*'], ['Support', route('support.index'), 'support.*'], ['Activity Log', route('activity-log'), 'activity-log']] as [$label, $href, $pattern])
            <a href="{{ $href }}" class="block px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs($pattern) ? 'bg-brand-500 text-white' : 'text-brand-700 hover:bg-brand-50' }}">{{ $label }}</a>
            @endforeach
        @endif
    </div>
</header>

{{-- Platform announcement banner --}}
@php $announcement = \App\Models\Setting::get('platform_announcement', ''); @endphp
@if($announcement)
<div x-data="{ show: true }" x-show="show" x-cloak
     class="bg-yellow-50 border-b border-yellow-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2.5 flex items-center justify-between gap-4">
        <div class="flex items-center gap-2 text-sm text-yellow-800">
            <svg class="w-4 h-4 shrink-0 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
            <span>{{ $announcement }}</span>
        </div>
        <button @click="show=false" class="text-yellow-500 hover:text-yellow-700 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
</div>
@endif

{{-- Flash messages --}}
@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(()=>show=false,4000)" x-cloak
     class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
    <div class="flex items-center gap-3 px-4 py-3 bg-cfa-50 border border-cfa-200 text-cfa-800 rounded-lg text-sm">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
</div>
@endif
@if(session('error'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(()=>show=false,5000)" x-cloak
     class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
    <div class="flex items-center gap-3 px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('error') }}
    </div>
</div>
@endif

{{-- Main content --}}
<main class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8">
    @yield('content')
</main>

<footer class="flex items-center justify-center gap-4 text-xs text-gray-400 py-4 border-t border-gray-100 mt-8">
    <img src="{{ asset('images/logo.png') }}" alt="" class="h-5 w-auto opacity-40">
    <span>&copy; {{ date('Y') }} Corporate Credibility Scorecard Platform</span>
    <a href="{{ route('privacy-policy') }}" class="hover:text-gray-600 hover:underline transition-colors">Privacy Policy</a>
</footer>

@stack('scripts')
</body>
</html>
