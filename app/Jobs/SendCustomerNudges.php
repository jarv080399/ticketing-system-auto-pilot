<?php

namespace App\Jobs;

use App\Models\AuditLog;
use App\Models\Ticket;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendCustomerNudges implements ShouldQueue
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
        // Find tickets waiting on customer for 48 hours without a comment
        $threshold = now()->subHours(48);
        
        $tickets = Ticket::where('status', 'waiting_on_customer')
            ->where('updated_at', '<', $threshold)
            ->get();

        foreach ($tickets as $ticket) {
            // Check if we've already nudged today
            $lastNudge = AuditLog::where('auditable_id', $ticket->id)
                ->where('action', 'customer_nudge')
                ->where('created_at', '>=', now()->subHours(24))
                ->first();

            if (!$lastNudge) {
                // Here we would dispatch an email notification
                Log::info("Ticket {$ticket->ticket_number} waiting on customer for 48+ hours. Sent nudge.");
                
                AuditLog::create([
                     'action' => 'customer_nudge',
                     'auditable_type' => get_class($ticket),
                     'auditable_id' => $ticket->id,
                     'user_id' => null, // System Action
                 ]);
            }
        }
    }
}
