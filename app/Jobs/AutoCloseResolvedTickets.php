<?php

namespace App\Jobs;

use App\Models\AuditLog;
use App\Models\Ticket;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class AutoCloseResolvedTickets implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Find tickets in "Resolved" state that haven't been touched in 72 hours
        $threshold = now()->subHours(72);
        
        $tickets = Ticket::where('status', 'resolved')
            ->where('updated_at', '<', $threshold)
            ->get();

        foreach ($tickets as $ticket) {
            $oldValues = $ticket->getOriginal();
            
            $ticket->status = 'closed';
            $ticket->closed_at = now();
            $ticket->saveQuietly();

            AuditLog::create([
                'action' => 'auto_closed',
                'auditable_type' => get_class($ticket),
                'auditable_id' => $ticket->id,
                'old_values' => $oldValues,
                'new_values' => $ticket->getAttributes(),
                'user_id' => null, // System Action
            ]);

            Log::info("Ticket {$ticket->ticket_number} auto-closed after 72 hours.");
        }
    }
}
