<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\TicketResource;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketArchiveController extends Controller
{
    /**
     * Display a listing of archived tickets.
     */
    public function index(Request $request)
    {
        $query = Ticket::withoutGlobalScope('archived')
            ->where('is_archived', true)
            ->with(['category', 'requester', 'agent']);

        return TicketResource::collection($query->latest()->paginate(20));
    }

    /**
     * Archive a specific ticket.
     */
    public function archive(Request $request, $ticketNumber)
    {
        $ticket = Ticket::where('ticket_number', $ticketNumber)->firstOrFail();
        
        $ticket->update(['is_archived' => true]);

        // Optional log:
        \App\Models\AuditLog::log(
            $request->user(),
            'archived',
            $ticket,
            null,
            ['is_archived' => true]
        );

        return response()->json(['message' => 'Ticket archived successfully.']);
    }

    /**
     * Unarchive a specific ticket.
     */
    public function unarchive(Request $request, $ticketNumber)
    {
        $ticket = Ticket::withoutGlobalScope('archived')
            ->where('ticket_number', $ticketNumber)
            ->firstOrFail();
            
        $ticket->update(['is_archived' => false]);

        \App\Models\AuditLog::log(
            $request->user(),
            'unarchived',
            $ticket,
            null,
            ['is_archived' => false]
        );

        return response()->json(['message' => 'Ticket unarchived successfully.']);
    }
}
