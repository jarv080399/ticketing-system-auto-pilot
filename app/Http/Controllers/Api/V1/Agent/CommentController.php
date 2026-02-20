<?php

namespace App\Http\Controllers\Api\V1\Agent;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    /**
     * Store a newly created comment in storage.
     */
    public function store(Request $request, Ticket $ticket)
    {
        // For now, anyone authenticated can comment if they can view the ticket
        // But internal notes are restricted to agents/admins in the validation
        Gate::authorize('view', $ticket);

        $validated = $request->validate([
            'body' => 'required|string',
            'is_internal' => 'boolean',
        ]);

        // Security check: Only agents/admins can post internal notes
        if ($request->is_internal && $request->user()->role === 'user') {
            $validated['is_internal'] = false;
        }

        $comment = $ticket->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $validated['body'],
            'is_internal' => $validated['is_internal'] ?? false,
        ]);

        // Update first_response_at if this is the first public reply from an agent
        if (!$comment->is_internal && $request->user()->role !== 'user' && !$ticket->first_response_at) {
            $ticket->update(['first_response_at' => now()]);
        }

        $comment->load('user');
        broadcast(new \App\Events\TicketCommentCreated($comment))->toOthers();

        return response()->json([
            'data' => $comment,
            'message' => 'Comment posted successfully',
        ], 201);
    }
}
