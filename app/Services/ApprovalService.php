<?php

namespace App\Services;

use App\Models\ApprovalRequest;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ApprovalService
{
    /**
     * Create an approval request for a ticket.
     * Often called from Automation rules.
     */
    public function requestApproval(Ticket $ticket, string $role = 'manager')
    {
        // For MVP, find the first admin to approve if requesting role='manager'
        $approver = User::where('role', 'admin')->first();

        if (!$approver) {
            Log::warning("No approver found for ticket {$ticket->ticket_number}");
            return;
        }

        ApprovalRequest::create([
            'ticket_id' => $ticket->id,
            'requester_id' => $ticket->requester_id,
            'approver_id' => $approver->id,
            'status' => 'pending',
        ]);
        
        $ticket->status = 'waiting_on_customer'; // Representing blocked status
        $ticket->save();
        
        // In reality, emit an event here to notify the approver via email/slack
    }

    /**
     * Respond to an existing approval request.
     */
    public function respond(ApprovalRequest $approval, string $status, string $comment = null)
    {
        if (!in_array($status, ['approved', 'rejected'])) {
            throw new \InvalidArgumentException('Invalid approval status');
        }

        $approval->update([
            'status' => $status,
            'comment' => $comment,
            'responded_at' => now(),
        ]);

        $ticket = $approval->ticket;

        if ($status === 'approved') {
            $ticket->status = 'in_progress';
        } else {
            // Rejected, return back to new or close it depending on business rules
            $ticket->status = 'new';
        }

        $ticket->save();
    }
}
