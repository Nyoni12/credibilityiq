<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketMessage extends Model
{
    protected $fillable = ['ticket_id', 'user_id', 'message', 'is_staff'];

    protected $casts = ['is_staff' => 'boolean'];

    public function user()   { return $this->belongsTo(User::class); }
    public function ticket() { return $this->belongsTo(SupportTicket::class, 'ticket_id'); }
}
