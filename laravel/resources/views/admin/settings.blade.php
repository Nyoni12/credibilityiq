@extends('layouts.app')
@section('title', 'Platform Settings')

@section('content')
<div class="space-y-6 max-w-3xl">

    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Platform Settings</h1>
            <p class="text-sm text-gray-500 mt-0.5">Global configuration for the CredibilityIQ platform.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-5">
        @csrf

        {{-- ── Onboarding ─────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-brand-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <h2 class="text-sm font-bold text-gray-900">Client Onboarding</h2>
            </div>
            <div class="px-6 py-5 space-y-5">

                {{-- Default assessment slots --}}
                <div class="flex items-start justify-between gap-6">
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-800 mb-0.5">Default Assessment Slots</label>
                        <p class="text-xs text-gray-500">How many assessments a newly onboarded company is allowed to run before needing approval for more.</p>
                    </div>
                    <input type="number" name="default_assessment_slots" min="1" max="50"
                           value="{{ old('default_assessment_slots', $settings['default_assessment_slots'] ?? 1) }}"
                           class="w-24 px-3 py-2 rounded-xl border border-gray-300 text-sm text-center font-semibold focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>

                {{-- Self-registration --}}
                <div class="flex items-start justify-between gap-6 pt-4 border-t border-gray-50">
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-800 mb-0.5">Allow Self-Registration</label>
                        <p class="text-xs text-gray-500">When enabled, companies can sign up themselves via the public registration page. Disable to restrict onboarding to superadmin only.</p>
                    </div>
                    <div x-data="{ on: {{ ($settings['allow_self_registration'] ?? '1') === '1' ? 'true' : 'false' }} }"
                         class="shrink-0">
                        <input type="hidden" name="allow_self_registration" :value="on ? '1' : '0'">
                        <button type="button" @click="on=!on"
                                :class="on ? 'bg-brand-500' : 'bg-gray-200'"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none">
                            <span :class="on ? 'translate-x-6' : 'translate-x-1'"
                                  class="inline-block w-4 h-4 rounded-full bg-white shadow transition-transform"></span>
                        </button>
                        <span x-text="on ? 'Enabled' : 'Disabled'"
                              :class="on ? 'text-green-600' : 'text-gray-400'"
                              class="ml-2 text-xs font-semibold"></span>
                    </div>
                </div>

            </div>
        </div>

        {{-- ── Support & Communication ──────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-accent-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-accent-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <h2 class="text-sm font-bold text-gray-900">Support &amp; Communication</h2>
            </div>
            <div class="px-6 py-5">
                <label class="block text-sm font-semibold text-gray-800 mb-1">Support Email Address</label>
                <p class="text-xs text-gray-500 mb-3">Shown to clients when they need help or reach an assessment limit.</p>
                <input type="email" name="support_email"
                       value="{{ old('support_email', $settings['support_email'] ?? '') }}"
                       class="w-full max-w-sm px-3 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                       placeholder="support@example.com">
            </div>
        </div>

        {{-- ── Platform Announcement ───────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-yellow-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                </div>
                <h2 class="text-sm font-bold text-gray-900">Platform Announcement</h2>
            </div>
            <div class="px-6 py-5">
                <label class="block text-sm font-semibold text-gray-800 mb-1">Banner Message</label>
                <p class="text-xs text-gray-500 mb-3">Shown as a notice banner to all logged-in users. Leave blank to hide it.</p>
                <textarea name="platform_announcement" rows="2" maxlength="300"
                          class="w-full px-3 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none"
                          placeholder="e.g. Scheduled maintenance on Saturday 28 Jun, 02:00–04:00 UTC.">{{ old('platform_announcement', $settings['platform_announcement'] ?? '') }}</textarea>
                <p class="text-xs text-gray-400 mt-1">Max 300 characters.</p>
            </div>
        </div>

        {{-- Save --}}
        <div class="flex items-center gap-4">
            <button type="submit"
                    class="bg-brand-500 hover:bg-brand-600 text-white font-semibold px-8 py-2.5 rounded-xl text-sm transition-all hover:shadow-lg hover:shadow-brand-500/30">
                Save Settings
            </button>
            <a href="{{ route('admin.dashboard') }}"
               class="text-sm text-gray-500 hover:text-gray-700 font-medium">Cancel</a>
        </div>

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3">
            <ul class="text-sm text-red-700 space-y-0.5 list-disc list-inside">
                @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
        @endif
    </form>

</div>
@endsection
