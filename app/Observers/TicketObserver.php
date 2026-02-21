<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Ticket;
use App\Services\AutomationService;
use App\Services\SlaService;

use App\Notifications\TicketCreatedNotification;
use App\Notifications\TicketAssignedNotification;
use App\Notifications\TicketResolvedNotification;

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

        // 4. Send Auto-Acknowledgement
        $ticket->requester->notify(new TicketCreatedNotification($ticket));
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

        // Trigger CSAT Survey if closed and doesn't already have one
        if ($ticket->wasChanged('status') && $ticket->status === 'closed' && !$ticket->satisfactionSurvey) {
            $survey = \App\Models\SatisfactionSurvey::create([
                'ticket_id' => $ticket->id,
                'user_id' => $ticket->requester_id,
                'token' => \Illuminate\Support\Str::uuid(),
                'rating' => 0, // Initial state
            ]);

            \Illuminate\Support\Facades\Mail::to($ticket->requester->email)
                ->send(new \App\Mail\SatisfactionSurveyMail($ticket, $survey));
        }

        // Send Assignment Notification
        if ($ticket->wasChanged('agent_id') && $ticket->agent_id) {
            $ticket->agent->notify(new TicketAssignedNotification($ticket));
        }

        // Send Resolution Notification
        if ($ticket->wasChanged('status') && $ticket->status === 'resolved') {
            $ticket->requester->notify(new TicketResolvedNotification($ticket));
        }
    }
}
