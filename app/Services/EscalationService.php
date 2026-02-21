<?php

namespace App\Services;

use App\Models\EscalationTier;
use App\Models\Ticket;
use Illuminate\Support\Facades\Log;

class EscalationService
{
    /**
     * Check if a ticket has breached its SLA and escalate to the appropriate tier.
     */
    public function checkAndEscalate(Ticket $ticket)
    {
        if (!$ticket->sla_due_at || $ticket->status === 'resolved' || $ticket->status === 'closed') {
            return;
        }

        if (now()->isAfter($ticket->sla_due_at)) {
            // Find the current tier based on... for MVP let's assume all start at Tier 1 (Level 1)
            // Or we check the current assignee's team.
            // For simplicity, let's just trigger a generic "Escalated" status or assign to L2.
            
            $nextTier = EscalationTier::where('level', 2)->first();

            if ($nextTier) {
                // In a full implementation, find a user in $nextTier->assigned_team to assign to
                Log::warning("SLA breached for Ticket {$ticket->ticket_number}. Escalating to {$nextTier->name}.");
                
                $ticket->priority = 'high'; // Bump priority
                $tags = $ticket->tags ?? [];
                if (!in_array('escalated', $tags)) {
                    $tags[] = 'escalated';
                    $ticket->tags = $tags;
                }
                $ticket->saveQuietly();
                
                // Here we would dispatch an email/slack notification to $nextTier->notification_channels
            }
        }
    }
}
