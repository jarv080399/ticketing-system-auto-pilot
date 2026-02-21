<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Services\EscalationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CheckSlaBreaches implements ShouldQueue
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
    public function handle(EscalationService $escalationService): void
    {
        // Find tickets that are approaching or breached SLA
        // Active tickets only.
        $tickets = Ticket::whereNotIn('status', ['resolved', 'closed'])
            ->whereNotNull('sla_due_at')
            // Get tickets due in the past or in the next 15 minutes
            ->where('sla_due_at', '<=', now()->addMinutes(15))
            ->get();

        foreach ($tickets as $ticket) {
            if (now()->isAfter($ticket->sla_due_at)) {
                $escalationService->checkAndEscalate($ticket);
            } else {
                // Here we would fire an `SlaApproachingEvent`
                \Illuminate\Support\Facades\Log::info("Ticket {$ticket->ticket_number} SLA is approaching.");
            }
        }
    }
}
