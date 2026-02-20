<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Ticket;
use App\Services\AutomationService;
use App\Services\SlaService;

class TicketObserver
{
    public function __construct(
        protected AutomationService $automationService,
        protected SlaService $slaService
    ) {}

    /**
     * Handle the Ticket "created" event.
     */
    public function created(Ticket $ticket): void
    {
        // 1. Calculate SLA
        $this->slaService->calculateSla($ticket);
        
        // 2. Run Creation Automation Rules
        $this->automationService->runRules($ticket, 'ticket_created');

        // 3. Log Creation
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'ticket_created',
            'auditable_type' => get_class($ticket),
            'auditable_id' => $ticket->id,
            'new_values' => $ticket->toArray(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Handle the Ticket "updated" event.
     */
    public function updated(Ticket $ticket): void
    {
        // Re-calculate SLA if priority changed
        if ($ticket->isDirty('priority')) {
            $this->slaService->calculateSla($ticket);
            $ticket->saveQuietly(); // Ensure changes are saved without triggering endless loops
        }

        // Run Update Automation Rules
        $this->automationService->runRules($ticket, 'ticket_updated');

        // Log Update
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'ticket_updated',
            'auditable_type' => get_class($ticket),
            'auditable_id' => $ticket->id,
            'old_values' => $ticket->getOriginal(),
            'new_values' => $ticket->getAttributes(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
