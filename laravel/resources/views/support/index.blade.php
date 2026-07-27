@extends('layouts.app')
@section('title', 'Support')

@section('content')
<div class="space-y-6" x-data="{ showNew: false }">

    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Support Tickets</h1>
            <p class="text-sm text-gray-500 mt-0.5">Our team responds within 24 hours.</p>
        </div>
        <button @click="showNew=true"
                class="inline-flex items-center gap-2 bg-brand-500 hover:bg-brand-600 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            New Ticket
        </button>
    </div>

    {{-- Ticket list --}}
    @if($tickets->isEmpty())
    <div class="bg-white rounded-2xl border border-dashed border-gray-300 p-14 text-center">
        <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-brand-50 flex items-center justify-center">
            <svg class="w-7 h-7 text-brand-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
        </div>
        <h3 class="font-semibold text-gray-700 mb-1">No support tickets yet</h3>
        <p class="text-gray-400 text-sm mb-5">Need help? Raise a ticket and we'll respond within 24 hours.</p>
        <button @click="showNew=true" class="bg-brand-500 hover:bg-brand-600 text-white font-semibold px-6 py-2.5 rounded-xl text-sm transition-all">
            Raise First Ticket
        </button>
    </div>
    @else
    <div class="space-y-3">
        @foreach($tickets as $ticket)
        @php
            $overdue  = $ticket->isOverdue();
            $resolved = $ticket->status === 'resolved';
        @endphp
        <a href="{{ route('support.show', $ticket) }}"
           class="flex items-center gap-4 bg-white rounded-2xl border {{ $overdue ? 'border-red-200' : 'border-gray-100' }} p-4 hover:shadow-md transition-all group">

            {{-- Priority dot --}}
            <div class="w-2.5 h-2.5 rounded-full shrink-0 mt-0.5"
                 style="background:{{ $ticket->priorityColor() }}"></div>

            {{-- Main info --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <p class="font-semibold text-gray-900 text-sm truncate">{{ $ticket->subject }}</p>
                    @if($resolved)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-green-50 text-green-700">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Resolved
                    </span>
                    @elseif($overdue)
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-600">Overdue</span>
                    @else
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700">Open</span>
                    @endif
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium"
                          style="background:{{ $ticket->priorityBg() }};color:{{ $ticket->priorityColor() }}">
                        {{ ucfirst($ticket->priority) }}
                    </span>
                </div>
                <p class="text-xs text-gray-400 mt-0.5">
                    {{ $ticket->messages_count }} {{ Str::plural('message', $ticket->messages_count) }}
                    &middot; Opened {{ $ticket->created_at->diffForHumans() }}
                    @if(!$resolved && $ticket->sla_due_at)
                        &middot;
                        @if($overdue)
                        <span class="text-red-500 font-medium">SLA breached {{ $ticket->sla_due_at->diffForHumans() }}</span>
                        @else
                        <span class="text-amber-600">Due {{ $ticket->sla_due_at->diffForHumans() }}</span>
                        @endif
                    @endif
                    @if($resolved && $ticket->resolved_at)
                        &middot; Resolved {{ $ticket->resolved_at->diffForHumans() }}
                    @endif
                </p>
            </div>

            <svg class="w-4 h-4 text-gray-300 group-hover:text-brand-400 transition-colors shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </a>
        @endforeach
    </div>
    @endif

    {{-- Modal: New Ticket --}}
    <div x-show="showNew" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div @click="showNew=false" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg" @click.stop>
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                <h3 class="font-bold text-gray-900">Raise a Support Ticket</h3>
                <button @click="showNew=false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('support.store') }}" class="px-6 py-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Subject <span class="text-red-400">*</span></label>
                    <input type="text" name="subject" required maxlength="200"
                           class="w-full px-3 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                           placeholder="Brief description of the issue">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Priority</label>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach(['low' => ['Low','#2563eb','#dbeafe'], 'medium' => ['Medium','#ca8a04','#fef9c3'], 'high' => ['High','#dc2626','#fee2e2']] as $val => [$label, $col, $bg])
                        <label class="cursor-pointer">
                            <input type="radio" name="priority" value="{{ $val }}" {{ $val === 'medium' ? 'checked' : '' }} class="sr-only peer">
                            <div class="text-center text-xs font-semibold py-2 rounded-xl border-2 border-transparent peer-checked:border-current transition-all"
                                 style="background:{{ $bg }};color:{{ $col }}">{{ $label }}</div>
                        </label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Description <span class="text-red-400">*</span></label>
                    <textarea name="description" required rows="4" maxlength="2000"
                              class="w-full px-3 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none"
                              placeholder="Describe your issue in detail…"></textarea>
                </div>
                <div class="flex gap-3 pt-1">
                    <button type="submit" class="flex-1 bg-brand-500 hover:bg-brand-600 text-white font-semibold py-2.5 rounded-xl text-sm transition-all">
                        Submit Ticket
                    </button>
                    <button type="button" @click="showNew=false"
                            class="flex-1 border border-gray-300 text-gray-700 font-medium py-2.5 rounded-xl text-sm hover:bg-gray-50 transition-all">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
