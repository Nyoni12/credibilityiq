<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\TicketMessage;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    public function index()
    {
        $company = auth()->user()->company;
        $tickets = SupportTicket::where('company_id', $company->id)
            ->withCount('messages')
            ->latest()
            ->get();

        return view('support.index', compact('tickets'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject'     => ['required', 'string', 'max:200'],
            'description' => ['required', 'string', 'max:2000'],
            'priority'    => ['required', 'in:low,medium,high'],
        ]);

        $ticket = SupportTicket::create([
            'company_id'  => auth()->user()->company_id,
            'user_id'     => auth()->id(),
            'subject'     => $data['subject'],
            'description' => $data['description'],
            'priority'    => $data['priority'],
            'status'      => 'open',
            'sla_due_at'  => now()->addHours(24),
        ]);

        // First message = the description itself
        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id'   => auth()->id(),
            'message'   => $data['description'],
            'is_staff'  => false,
        ]);

        return redirect()->route('support.show', $ticket)
            ->with('success', 'Ticket raised. Our team will respond within 24 hours.');
    }

    public function show(SupportTicket $ticket)
    {
        $this->authorizeTicket($ticket);
        $ticket->load(['messages.user', 'user', 'resolver']);
        return view('support.show', compact('ticket'));
    }

    public function addMessage(Request $request, SupportTicket $ticket)
    {
        $this->authorizeTicket($ticket);

        if ($ticket->status === 'resolved') {
            return back()->with('error', 'This ticket is already resolved.');
        }

        $data = $request->validate(['message' => ['required', 'string', 'max:2000']]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id'   => auth()->id(),
            'message'   => $data['message'],
            'is_staff'  => false,
        ]);

        return back()->with('success', 'Message sent.');
    }

    private function authorizeTicket(SupportTicket $ticket): void
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && $ticket->company_id !== $user->company_id) {
            abort(403);
        }
    }
}
