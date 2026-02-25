<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Ticket\StoreTicketRequest;
use App\Http\Resources\Api\V1\TicketResource;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Notifications\TicketCreatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    /**
     * Display a listing of the user's tickets.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $query = Ticket::with(['category', 'requester', 'agent']);

        // Scope to own tickets if regular user
        if ($user->role === 'user') {
            $query->where('requester_id', $user->id);
        }

        // Filters
        $query->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->priority, fn($q) => $q->where('priority', $request->priority))
            ->when($request->agent_id, function($q) use ($request) {
                if ($request->agent_id === 'unassigned') {
                    return $q->whereNull('agent_id');
                }
                return $q->where('agent_id', $request->agent_id);
            });

        $tickets = $query->latest()->paginate($request->per_page ?? 15);

        return TicketResource::collection($tickets);
    }

    /**
     * Store a newly created ticket.
     */
    public function store(StoreTicketRequest $request)
    {
        // 1. Duplicate Detection (Warning logic handled via API check if needed, 
        // but here we just process the submission)
        
        $ticket = Ticket::create([
            'title' => $request->title,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'requester_id' => $request->user()->id,
            'priority' => $request->priority ?? 'medium',
            'source' => $request->source ?? 'web',
            'tags' => $request->tags ?? [],
        ]);

        // 2. Handle Attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                try {
                    $path = $file->store('tickets/' . $ticket->id, 'public');

                    TicketAttachment::create([
                        'ticket_id' => $ticket->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning(
                        "Failed to store attachment for ticket #{$ticket->id}: " . $e->getMessage()
                    );
                }
            }
        }

        // 3. Notify the requester (queued — won't block response)
        try {
            $ticket->requester->notify(new TicketCreatedNotification($ticket));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Ticket notification failed: ' . $e->getMessage());
        }

        return (new TicketResource($ticket->refresh()->load('category')))
            ->additional(['message' => 'Ticket created successfully']);
    }

    /**
     * Display the specified ticket.
     */
    public function show(Request $request, $ticketNumber)
    {
        $ticket = Ticket::withoutGlobalScope('archived')
            ->with(['category', 'requester', 'agent', 'attachments', 'comments.user'])
            ->where('ticket_number', $ticketNumber)
            ->firstOrFail();

        Gate::authorize('view', $ticket);

        return new TicketResource($ticket);
    }

    /**
     * Check for duplicate tickets (potential warning for frontend).
     */
    public function checkDuplicate(Request $request)
    {
        $request->validate(['title' => 'required|string']);

        $duplicate = Ticket::where('requester_id', $request->user()->id)
            ->where('title', 'LIKE', '%' . $request->title . '%')
            ->where('created_at', '>=', now()->subHours(24))
            ->first();

        return response()->json([
            'is_duplicate' => !is_null($duplicate),
            'duplicate_ticket' => $duplicate ? [
                'ticket_number' => $duplicate->ticket_number,
                'status' => $duplicate->status,
            ] : null,
        ]);
    }
}
