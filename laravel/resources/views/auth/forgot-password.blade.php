@extends('layouts.auth')
@section('title', 'Forgot Password')

@section('form')
<div>
    <h2 class="text-2xl font-extrabold text-gray-900 mb-1">Reset your password</h2>
    <p class="text-gray-500 text-sm mb-8">Enter your email address and we'll send you a reset link if it exists in our system.</p>

    @if(session('status'))
    <div class="mb-6 flex items-center gap-3 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        {{ session('status') }}
    </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email address</label>
            <input type="email" name="email" value="{{ old('email', $email ?? '') }}" required autofocus
                   class="w-full px-4 py-3 rounded-xl border {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-all"
                   placeholder="you@company.com">
            @error('email')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
                class="w-full bg-brand-500 hover:bg-brand-600 text-white font-bold py-3 rounded-xl text-sm transition-all hover:shadow-lg hover:shadow-brand-500/30 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
            Send Reset Link
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-500">
        Remember your password?
        <a href="{{ route('login') }}" class="text-brand-600 font-medium hover:underline">Sign in</a>
    </p>
</div>
@endsection
