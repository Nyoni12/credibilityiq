<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'CredibilityIQ') — CredibilityIQ</title>
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
    theme: {
        extend: {
            colors: {
                brand: {
                    50:'#EEEFFE', 100:'#C8CCF5', 200:'#9DA5EC',
                    300:'#717FE3', 400:'#4659DA', 500:'#1F2192',
                    600:'#191B7A', 700:'#131562', 800:'#0D0E4A', 900:'#070831'
                },
                accent: { 400:'#BE2CBA', 500:'#A329CC', 600:'#8821A8' },
                cfa: { 400:'#01AF50', 500:'#00A651', 600:'#008040' }
            },
            fontFamily: { sans: ['Inter','system-ui','sans-serif'] }
        }
    }
}
</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<style>
  [x-cloak] { display: none !important; }
  .sidebar-link { @apply flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium text-brand-200 hover:bg-white/10 hover:text-white transition-all duration-150; }
  .sidebar-link.active { @apply bg-white/15 text-white; }
</style>
</head>
<body class="h-full bg-gray-50 font-sans" x-data="{ sidebarOpen: false }">

{{-- Mobile overlay --}}
<div x-show="sidebarOpen" x-cloak @click="sidebarOpen=false"
     class="fixed inset-0 bg-black/50 z-20 lg:hidden"></div>

{{-- ─── Sidebar ─────────────────────────────────────────────── --}}
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
       class="fixed inset-y-0 left-0 z-30 w-64 bg-brand-900 flex flex-col transition-transform duration-300 lg:translate-x-0">

    {{-- Logo --}}
    <div class="flex items-center gap-3 px-6 py-5 border-b border-white/10">
        <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-brand-400 to-accent-500 flex items-center justify-center text-white font-black text-sm">CIQ</div>
        <div>
            <div class="text-white font-bold text-sm leading-tight">CredibilityIQ</div>
            <div class="text-brand-300 text-xs">{{ auth()->user()->company->name ?? 'SuperAdmin' }}</div>
        </div>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">

        @if(auth()->user()->isSuperAdmin())
        <p class="px-4 py-1 text-xs font-semibold text-brand-400 uppercase tracking-wider">SuperAdmin</p>
        <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Dashboard
        </a>
        <a href="{{ route('admin.companies.index') }}" class="sidebar-link {{ request()->routeIs('admin.companies.*') ? 'active' : '' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            Companies
        </a>
        <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            Users
        </a>
        @else
        <p class="px-4 py-1 text-xs font-semibold text-brand-400 uppercase tracking-wider">Main</p>
        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Dashboard
        </a>
        <a href="{{ route('assessments.index') }}" class="sidebar-link {{ request()->routeIs('assessments.*') ? 'active' : '' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            Assessments
        </a>
        <p class="px-4 py-1 mt-3 text-xs font-semibold text-brand-400 uppercase tracking-wider">Setup</p>
        <a href="{{ route('values.index') }}" class="sidebar-link {{ request()->routeIs('values.*') ? 'active' : '' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            Company Values
        </a>
        @endif

    </nav>

    {{-- User footer --}}
    <div class="border-t border-white/10 p-4">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-accent-400 to-brand-400 flex items-center justify-center text-white text-xs font-bold">
                {{ auth()->user()->initials }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-white text-xs font-medium truncate">{{ auth()->user()->full_name }}</div>
                <div class="text-brand-400 text-xs truncate">{{ auth()->user()->email }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="mt-3">
            @csrf
            <button type="submit" class="w-full text-left sidebar-link text-red-300 hover:text-red-100 hover:bg-red-900/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Sign out
            </button>
        </form>
    </div>
</aside>

{{-- ─── Main content ────────────────────────────────────────── --}}
<div class="lg:pl-64 flex flex-col min-h-screen">

    {{-- Top bar --}}
    <header class="sticky top-0 z-10 bg-white border-b border-gray-200 flex items-center justify-between px-4 lg:px-8 h-14">
        <button @click="sidebarOpen=!sidebarOpen" class="lg:hidden p-2 rounded-md text-gray-500 hover:bg-gray-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <h1 class="text-base font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h1>
        <div class="flex items-center gap-3">
            <a href="{{ route('profile') }}" class="w-8 h-8 rounded-full bg-gradient-to-br from-accent-400 to-brand-400 flex items-center justify-center text-white text-xs font-bold hover:opacity-90 transition-opacity">
                {{ auth()->user()->initials }}
            </a>
        </div>
    </header>

    {{-- Flash messages --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(()=>show=false,4000)"
         class="mx-4 lg:mx-8 mt-4 flex items-center gap-3 px-4 py-3 bg-cfa-50 border border-cfa-200 text-cfa-800 rounded-lg text-sm">
        <svg class="w-4 h-4 text-cfa-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(()=>show=false,5000)"
         class="mx-4 lg:mx-8 mt-4 flex items-center gap-3 px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
        <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Page content --}}
    <main class="flex-1 p-4 lg:p-8">
        @yield('content')
    </main>

    <footer class="px-8 py-4 text-xs text-gray-400 border-t border-gray-100 text-center">
        © {{ date('Y') }} Credibility Factory Afrique · CredibilityIQ Platform
    </footer>
</div>

@stack('scripts')
</body>
</html>
