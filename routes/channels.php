<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('ticket.{ticketId}', function ($user, $ticketId) {
    $ticket = \App\Models\Ticket::find($ticketId);
    if (!$ticket) return false;
    
    // Agents and Admins can see all tickets. Users can only see their own.
    return $user->role !== 'user' || (int) $user->id === (int) $ticket->requester_id;
});
