@extends('layouts.auth')
@section('title', 'Create Account')

@section('form')
<div>
    <h2 class="text-2xl font-extrabold text-gray-900 mb-1">Create your account</h2>
    <p class="text-gray-500 text-sm mb-8">Start measuring your company's credibility today</p>

    <form method="POST" action="{{ route('signup.post') }}" class="space-y-4">
        @csrf

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                <input type="text" name="first_name" value="{{ old('first_name') }}" required
                       class="w-full px-4 py-3 rounded-xl border {{ $errors->has('first_name') ? 'border-red-400' : 'border-gray-300' }} text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all"
                       placeholder="John">
                @error('first_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                <input type="text" name="last_name" value="{{ old('last_name') }}" required
                       class="w-full px-4 py-3 rounded-xl border {{ $errors->has('last_name') ? 'border-red-400' : 'border-gray-300' }} text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all"
                       placeholder="Moyo">
                @error('last_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Work Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full px-4 py-3 rounded-xl border {{ $errors->has('email') ? 'border-red-400' : 'border-gray-300' }} text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all"
                   placeholder="you@company.com">
            @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Company Name</label>
            <input type="text" name="company_name" value="{{ old('company_name') }}" required
                   class="w-full px-4 py-3 rounded-xl border {{ $errors->has('company_name') ? 'border-red-400' : 'border-gray-300' }} text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all"
                   placeholder="Acme Corp">
            @error('company_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Industry</label>
                <select name="industry" class="w-full px-4 py-3 rounded-xl border border-gray-300 text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all bg-white">
                    <option value="">Select…</option>
                    @foreach(['Banking & Finance','Insurance','Mining','Manufacturing','Retail','Technology','Healthcare','Agriculture','Real Estate','Other'] as $ind)
                    <option value="{{ $ind }}" {{ old('industry') === $ind ? 'selected' : '' }}>{{ $ind }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Annual Revenue (USD)</label>
                <input type="number" name="annual_revenue" value="{{ old('annual_revenue') }}" min="0" step="1000"
                       class="w-full px-4 py-3 rounded-xl border border-gray-300 text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all"
                       placeholder="1000000">
            </div>
        </div>

        <div x-data="{ show: false }">
            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <div class="relative">
                <input :type="show ? 'text' : 'password'" name="password" required minlength="8"
                       class="w-full px-4 py-3 pr-11 rounded-xl border {{ $errors->has('password') ? 'border-red-400' : 'border-gray-300' }} text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all"
                       placeholder="Min 8 characters">
                <button type="button" @click="show=!show" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </button>
            </div>
            @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
            <input type="password" name="password_confirmation" required
                   class="w-full px-4 py-3 rounded-xl border border-gray-300 text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all"
                   placeholder="Repeat password">
        </div>

        <button type="submit"
                class="w-full bg-cfa-500 hover:bg-cfa-600 text-white font-bold py-3 rounded-xl text-sm transition-all hover:shadow-lg hover:shadow-cfa-500/30 mt-2">
            Create Account
        </button>

        <p class="text-center text-xs text-gray-400">
            By creating an account you agree to our
            <a href="#" class="text-brand-500 hover:underline">Terms of Service</a>.
        </p>
    </form>

    <p class="mt-5 text-center text-sm text-gray-500">
        Already have an account?
        <a href="{{ route('login') }}" class="text-brand-600 font-semibold hover:text-brand-500">Sign in</a>
    </p>
</div>
@endsection
