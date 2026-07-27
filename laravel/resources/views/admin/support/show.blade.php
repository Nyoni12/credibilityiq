@extends('layouts.app')
@section('title', 'Ticket #' . $ticket->id)

@section('content')
<div class="max-w-3xl mx-auto space-y-4">

    {{-- Back + header --}}
    <div class="flex items-start gap-4">
        <a href="{{ route('admin.support.index') }}" class="text-gray-400 hover:text-gray-600 mt-1 shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div class="flex-1">
            <div class="flex items-center gap-2 flex-wrap">
                <h1 class="text-lg font-bold text-gray-900">{{ $ticket->subject }}</h1>
                @if($ticket->status === 'resolved')
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Resolved
                </span>
                @elseif($ticket->isOverdue())
                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">SLA Breached</span>
                @else
                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">Open</span>
                @endif
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold"
                      style="background:{{ $ticket->priorityBg() }};color:{{ $ticket->priorityColor() }}">
                    {{ ucfirst($ticket->priority) }} Priority
                </span>
            </div>
            <p class="text-xs text-gray-400 mt-1">
                Ticket #{{ $ticket->id }}
                &middot; <span class="font-medium text-gray-600">{{ $ticket->company->name }}</span>
                &middot; Raised by {{ $ticket->user->full_name }} {{ $ticket->created_at->diffForHumans() }}
                @if($ticket->status === 'resolved' && $ticket->resolver)
                &middot; Resolved by {{ $ticket->resolver->full_name }} {{ $ticket->resolved_at->diffForHumans() }}
                @elseif($ticket->sla_due_at && $ticket->status === 'open')
                &middot;
                @if($ticket->isOverdue())
                <span class="text-red-500 font-semibold">SLA breached {{ $ticket->sla_due_at->diffForHumans() }}</span>
                @else
                <span class="text-amber-500">Due {{ $ticket->sla_due_at->diffForHumans() }}</span>
                @endif
                @endif
            </p>
        </div>

        {{-- Resolve / Reopen button --}}
        @if($ticket->status === 'open')
        <form method="POST" action="{{ route('admin.support.resolve', $ticket) }}" class="shrink-0">
            @csrf
            <button type="submit"
                    onclick="return confirm('Mark this ticket as resolved?')"
                    class="inline-flex items-center gap-1.5 bg-green-500 hover:bg-green-600 text-white font-semibold px-4 py-2 rounded-xl text-sm transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Resolve
            </button>
        </form>
        @else
        <form method="POST" action="{{ route('admin.support.reopen', $ticket) }}" class="shrink-0">
            @csrf
            <button type="submit"
                    class="inline-flex items-center gap-1.5 border border-amber-400 text-amber-700 hover:bg-amber-50 font-semibold px-4 py-2 rounded-xl text-sm transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Reopen
            </button>
        </form>
        @endif
    </div>

    {{-- Resolution banner --}}
    @if($ticket->status === 'resolved')
    <div class="flex items-center gap-3 bg-green-50 border border-green-200 rounded-2xl px-5 py-4">
        <div class="w-10 h-10 rounded-xl bg-green-500 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        </div>
        <div>
            <p class="text-sm font-bold text-green-800">Ticket resolved</p>
            <p class="text-xs text-green-600">
                Closed by {{ $ticket->resolver?->full_name ?? 'Support Team' }} on {{ $ticket->resolved_at?->format('d M Y \a\t H:i') }}
                @php $mins = $ticket->resolutionMinutes(); @endphp
                @if($mins)
                , resolved in {{ $mins < 60 ? $mins.'m' : round($mins/60,1).'h' }}
                @if($ticket->sla_due_at && $ticket->resolved_at->lte($ticket->sla_due_at))
                <span class="text-green-500 font-semibold">(within SLA ✓)</span>
                @else
                <span class="text-red-500 font-semibold">(SLA breached)</span>
                @endif
                @endif
            </p>
        </div>
    </div>
    @endif

    {{-- Chat thread --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
            Conversation
        </div>
        <div class="divide-y divide-gray-50 max-h-[480px] overflow-y-auto" id="chat-thread">
            @foreach($ticket->messages as $msg)
            @php $isStaff = $msg->is_staff; @endphp
            <div class="flex gap-3 px-5 py-4 {{ $isStaff ? 'bg-brand-50/40' : '' }}">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shrink-0 mt-0.5"
                     style="background:{{ $isStaff ? 'linear-gradient(135deg,#A329CC,#1F2192)' : '#e5e7eb' }};color:{{ $isStaff ? 'white' : '#374151' }}">
                    {{ $msg->user->initials }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs font-bold text-gray-900">{{ $msg->user->full_name }}</span>
                        @if($isStaff)
                        <span class="text-xs bg-accent-100 text-accent-700 px-1.5 py-0.5 rounded font-semibold">Support Team</span>
                        @endif
                        <span class="text-xs text-gray-400 ml-auto shrink-0">{{ $msg->created_at->format('d M H:i') }}</span>
                    </div>
                    <p class="text-sm text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $msg->message }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Reply form --}}
    @if($ticket->status === 'open')
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <form method="POST" action="{{ route('admin.support.reply', $ticket) }}">
            @csrf
            <label class="block text-xs font-semibold text-gray-600 mb-2">Reply to Client</label>
            <textarea name="message" required rows="3" maxlength="2000"
                      class="w-full px-3 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none"
                      placeholder="Write a response to the client…"></textarea>
            <div class="flex items-center gap-3 mt-3">
                <button type="submit"
                        class="bg-brand-500 hover:bg-brand-600 text-white font-semibold px-6 py-2.5 rounded-xl text-sm transition-all">
                    Send Reply
                </button>
                <span class="text-xs text-gray-400">Your reply will be visible to the client.</span>
            </div>
        </form>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
    const t = document.getElementById('chat-thread');
    if (t) t.scrollTop = t.scrollHeight;
</script>
@endpush
