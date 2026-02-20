<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\AutomationRule;
use App\Models\Ticket;
use Illuminate\Support\Facades\Log;

class AutomationService
{
    /**
     * Run automation rules against a given ticket.
     */
    public function runRules(Ticket $ticket, string $triggerEvent): void
    {
        $rules = AutomationRule::where('is_active', true)
            ->where('trigger_event', $triggerEvent)
            ->orderBy('priority', 'desc')
            ->get();

        foreach ($rules as $rule) {
            if ($this->evaluateCondition($ticket, $rule->condition_json)) {
                $this->executeAction($ticket, $rule->action_json, $rule->id);
            }
        }
    }

    /**
     * Recursively evaluate JSON conditions.
     */
    protected function evaluateCondition(Ticket $ticket, array $conditions): bool
    {
        if (empty($conditions)) {
            return false;
        }

        // Simplistic evaluation strategy for MVP
        // Structure: ['field' => 'priority', 'op' => 'equals', 'value' => 'critical']
        if (isset($conditions['field'])) {
            return $this->evaluateSingleCondition($ticket, $conditions);
        }

        // Nested conditions: ['group' => 'AND', 'rules' => [...]]
        if (isset($conditions['group']) && isset($conditions['rules'])) {
            $isAnd = strtoupper($conditions['group']) === 'AND';
            
            foreach ($conditions['rules'] as $rule) {
                $result = $this->evaluateCondition($ticket, $rule);
                
                if ($isAnd && !$result) return false;
                if (!$isAnd && $result) return true;
            }

            return $isAnd; // If AND, all passed. If OR, none passed.
        }

        return false;
    }

    protected function evaluateSingleCondition(Ticket $ticket, array $condition): bool
    {
        $field = $condition['field'];
        $op = $condition['op'];
        $value = $condition['value'];

        // Get actual ticket value. Handle relationships (e.g., category.name) if needed later.
        // For now, flat attributes.
        $actualValue = $ticket->{$field};

        switch ($op) {
            case 'equals': return $actualValue == $value;
            case 'not_equals': return $actualValue != $value;
            case 'contains': return strpos((string)$actualValue, (string)$value) !== false;
            case 'greater_than': return $actualValue > $value;
            case 'less_than': return $actualValue < $value;
            case 'in': return in_array($actualValue, (array)$value);
            case 'not_in': return !in_array($actualValue, (array)$value);
            default: return false;
        }
    }

    /**
     * Perform the defined actions if condition evaluates to TRUE.
     */
    protected function executeAction(Ticket $ticket, array $actions, int $ruleId): void
    {
        $oldValues = $ticket->getOriginal();
        $changesMade = false;

        foreach ($actions as $action) {
            $type = $action['type'];
            $payload = $action['payload'];

            switch ($type) {
                case 'assign_to':
                    $ticket->agent_id = $payload['user_id'];
                    $changesMade = true;
                    break;
                case 'change_status':
                    $ticket->status = $payload['status'];
                    $changesMade = true;
                    break;
                case 'change_priority':
                    $ticket->priority = $payload['priority'];
                    $changesMade = true;
                    break;
                case 'add_tag':
                    $tags = $ticket->tags ?? [];
                    if (!in_array($payload['tag'], $tags)) {
                        $tags[] = $payload['tag'];
                        $ticket->tags = $tags;
                        $changesMade = true;
                    }
                    break;
                case 'request_approval':
                    // In a full implementation, we'd trigger an ApprovalService
                    Log::info("Automation rule triggered approval request for Ticket {$ticket->ticket_number}");
                    // Put ticket into a pending state
                    $ticket->status = 'waiting_on_customer'; 
                    $changesMade = true;
                    break;
            }
        }

        if ($changesMade) {
            // Save without triggering model events (to prevent infinite loops)
            $ticket->saveQuietly();

            $newValues = $ticket->getAttributes();

            AuditLog::create([
                'action' => "automation_rule_{$ruleId}_fired",
                'auditable_type' => get_class($ticket),
                'auditable_id' => $ticket->id,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'user_id' => null, // System action
            ]);
        }
    }
}
