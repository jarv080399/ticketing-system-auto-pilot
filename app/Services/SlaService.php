<?php

namespace App\Services;

use App\Models\SlaPolicy;
use App\Models\Ticket;
use Carbon\Carbon;

class SlaService
{
    /**
     * Calculate and assign the SLA due date based on ticket priority and creation time.
     */
    public function calculateSla(Ticket $ticket): void
    {
        $policy = SlaPolicy::where('priority', $ticket->priority)->first();

        if (!$policy) {
            return;
        }

        $now = now();
        $dueAt = $now->copy();

        if ($policy->business_hours_only) {
            // Simplified business hours logic for MVP (Monday-Friday, 9 AM - 5 PM)
            // A truly robust implementation would likely need a dedicated package or much more complex logic.
            $minutesToAdd = $policy->resolution_time_minutes;
            
            while ($minutesToAdd > 0) {
                $dueAt->addMinute();
                
                // If it's a weekend, skip to Monday 9 AM
                if ($dueAt->isWeekend()) {
                    $dueAt->next(\Carbon\Carbon::MONDAY)->setTime(9, 0);
                    continue;
                }
                
                // If it's outside business hours (before 9 AM), skip to 9 AM
                if ($dueAt->hour < 9) {
                    $dueAt->setTime(9, 0);
                }
                
                // If it's outside business hours (after 5 PM), skip to tomorrow 9 AM
                if ($dueAt->hour >= 17) {
                    $dueAt->addDay()->setTime(9, 0);
                    if ($dueAt->isWeekend()) {
                        $dueAt->next(\Carbon\Carbon::MONDAY)->setTime(9, 0);
                    }
                }
                
                $minutesToAdd--;
            }
        } else {
            // 24/7 SLA calculation
            $dueAt->addMinutes($policy->resolution_time_minutes);
        }

        $ticket->sla_due_at = $dueAt;
    }
}
