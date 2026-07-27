<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    protected $fillable = [
        'company_id', 'user_id', 'subject', 'description',
        'priority', 'status', 'resolved_at', 'resolved_by', 'sla_due_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'sla_due_at'  => 'datetime',
    ];

    public function company()   { return $this->belongsTo(Company::class); }
    public function user()      { return $this->belongsTo(User::class); }
    public function resolver()  { return $this->belongsTo(User::class, 'resolved_by'); }
    public function messages()  { return $this->hasMany(TicketMessage::class, 'ticket_id')->oldest(); }

    public function isOverdue(): bool
    {
        return $this->status === 'open'
            && $this->sla_due_at
            && now()->isAfter($this->sla_due_at);
    }

    public function hoursUntilDue(): float
    {
        if (!$this->sla_due_at) return 0;
        return round(now()->diffInMinutes($this->sla_due_at, false) / 60, 1);
    }

    public function resolutionMinutes(): ?int
    {
        if (!$this->resolved_at) return null;
        return (int) $this->created_at->diffInMinutes($this->resolved_at);
    }

    public function priorityColor(): string
    {
        return match($this->priority) {
            'high'   => '#dc2626',
            'medium' => '#ca8a04',
            default  => '#2563eb',
        };
    }

    public function priorityBg(): string
    {
        return match($this->priority) {
            'high'   => '#fee2e2',
            'medium' => '#fef9c3',
            default  => '#dbeafe',
        };
    }
}
