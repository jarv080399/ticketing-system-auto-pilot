<?php

namespace App\Http\Controllers\Api\V1\Agent;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\TicketResource;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AgentTicketController extends Controller
{
    /**
     * Update the specified ticket's meta-data.
     */
    public function update(Request $request, Ticket $ticket)
    {
        Gate::authorize('update', $ticket);

        $validated = $request->validate([
            'status' => 'sometimes|string|in:new,in_progress,waiting_on_customer,resolved,closed',
            'priority' => 'sometimes|string|in:low,medium,high,critical',
            'agent_id' => 'sometimes|nullable|exists:users,id',
            'category_id' => 'sometimes|exists:categories,id',
            'sla_due_at' => 'sometimes|nullable|date',
        ]);

        // Track when ticket is resolved or closed
        if (isset($validated['status'])) {
            if ($validated['status'] === 'resolved' && $ticket->status !== 'resolved') {
                $validated['resolved_at'] = now();
            }
            if ($validated['status'] === 'closed' && $ticket->status !== 'closed') {
                $validated['closed_at'] = now();
            }
        }

        $ticket->update($validated);

        return new TicketResource($ticket->load(['category', 'requester', 'agent']));
    }
}
