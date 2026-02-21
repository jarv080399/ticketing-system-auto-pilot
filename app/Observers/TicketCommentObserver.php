<?php

namespace App\Observers;

use App\Models\TicketComment;
use App\Notifications\TicketUpdatedNotification;

class TicketCommentObserver
{
    /**
     * Handle the TicketComment "created" event.
     */
    public function created(TicketComment $comment): void
    {
        // Don't notify if it's an internal comment and the user being notified is the requester
        if ($comment->is_internal) {
            return;
        }

        $ticket = $comment->ticket;
        $author = $comment->user;

        // If an agent/admin commented, notify the requester
        if (in_array($author->role, ['agent', 'admin']) && $author->id !== $ticket->requester_id) {
            $ticket->requester->notify(new TicketUpdatedNotification($ticket, "New comment from agent: " . \Illuminate\Support\Str::limit($comment->body, 50)));
        }

        // If the requester commented, notify the assigned agent
        if ($author->id === $ticket->requester_id && $ticket->agent_id) {
            $ticket->agent->notify(new TicketUpdatedNotification($ticket, "New comment from user: " . \Illuminate\Support\Str::limit($comment->body, 50)));
        }
    }
}
