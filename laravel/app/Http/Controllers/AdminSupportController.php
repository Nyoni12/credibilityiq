<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\TicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminSupportController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::with(['company', 'user'])
            ->withCount('messages')
            ->latest()
            ->get();

        // ── Analytics ────────────────────────────────────────────────
        $totalTickets    = $tickets->count();
        $openTickets     = $tickets->where('status', 'open')->count();
        $resolvedTickets = $tickets->where('status', 'resolved')->count();
        $overdueTickets  = $tickets->filter(fn($t) => $t->isOverdue())->count();

        $resolutionTimes = $tickets->where('status', 'resolved')
            ->map(fn($t) => $t->resolutionMinutes())
            ->filter()
            ->values();

        $avgResolutionHours = $resolutionTimes->count()
            ? round($resolutionTimes->avg() / 60, 1)
            : null;

        $withinSla = $tickets->where('status', 'resolved')->filter(function ($t) {
            return $t->resolved_at && $t->sla_due_at && $t->resolved_at->lte($t->sla_due_at);
        })->count();

        $slaRate = $resolvedTickets > 0 ? round($withinSla / $resolvedTickets * 100) : null;

        $byPriority = [
            'high'   => $tickets->where('priority', 'high')->count(),
            'medium' => $tickets->where('priority', 'medium')->count(),
            'low'    => $tickets->where('priority', 'low')->count(),
        ];

        // Daily ticket volume last 14 days
        $daily = SupportTicket::selectRaw('DATE(created_at) as day, COUNT(*) as cnt')
            ->where('created_at', '>=', now()->subDays(13)->startOfDay())
            ->groupBy('day')
            ->orderBy('day')
            ->get()->keyBy('day');

        $dailyLabels = $dailyCounts = [];
        for ($i = 13; $i >= 0; $i--) {
            $d = now()->subDays($i)->toDateString();
            $dailyLabels[] = now()->subDays($i)->format('d M');
            $dailyCounts[] = (int) ($daily[$d]->cnt ?? 0);
        }

        return view('admin.support.index', compact(
            'tickets', 'totalTickets', 'openTickets', 'resolvedTickets',
            'overdueTickets', 'avgResolutionHours', 'slaRate', 'byPriority',
            'dailyLabels', 'dailyCounts'
        ));
    }

    public function show(SupportTicket $ticket)
    {
        $ticket->load(['messages.user', 'company', 'user', 'resolver']);
        return view('admin.support.show', compact('ticket'));
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        $data = $request->validate(['message' => ['required', 'string', 'max:2000']]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id'   => auth()->id(),
            'message'   => $data['message'],
            'is_staff'  => true,
        ]);

        return back()->with('success', 'Reply sent.');
    }

    public function resolve(SupportTicket $ticket)
    {
        if ($ticket->status === 'resolved') {
            return back()->with('error', 'Already resolved.');
        }

        $ticket->update([
            'status'      => 'resolved',
            'resolved_at' => now(),
            'resolved_by' => auth()->id(),
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id'   => auth()->id(),
            'message'   => 'This ticket has been marked as resolved.',
            'is_staff'  => true,
        ]);

        return back()->with('success', 'Ticket resolved.');
    }

    public function reopen(SupportTicket $ticket)
    {
        $ticket->update([
            'status'      => 'open',
            'resolved_at' => null,
            'resolved_by' => null,
            'sla_due_at'  => now()->addHours(24),
        ]);

        return back()->with('success', 'Ticket reopened. New 24-hour SLA set.');
    }
}
