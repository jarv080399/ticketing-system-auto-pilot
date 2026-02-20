<?php

namespace Database\Seeders;

use App\Models\AutomationRule;
use App\Models\EscalationTier;
use App\Models\SlaPolicy;
use Illuminate\Database\Seeder;

class AutomationSeeder extends Seeder
{
    public function run(): void
    {
        // 1. SLA Policies
        SlaPolicy::create([
            'name' => 'Critical Priority SLA (24/7)',
            'priority' => 'critical',
            'response_time_minutes' => 30,
            'resolution_time_minutes' => 120, // 2 hours
            'business_hours_only' => false,
        ]);

        SlaPolicy::create([
            'name' => 'High Priority SLA',
            'priority' => 'high',
            'response_time_minutes' => 60,
            'resolution_time_minutes' => 240, // 4 hours
            'business_hours_only' => true,
        ]);

        SlaPolicy::create([
            'name' => 'Medium Priority SLA',
            'priority' => 'medium',
            'response_time_minutes' => 240, // 4 hours
            'resolution_time_minutes' => 1440, // 24 hours
            'business_hours_only' => true,
        ]);

        SlaPolicy::create([
            'name' => 'Low Priority SLA',
            'priority' => 'low',
            'response_time_minutes' => 1440, // 24 hours
            'resolution_time_minutes' => 4320, // 72 hours
            'business_hours_only' => true,
        ]);

        // 2. Escalation Tiers
        EscalationTier::create([
            'name' => 'L1 Support Desk',
            'level' => 1,
            'assigned_team' => 'Service Desk',
            'sla_minutes' => 120,
        ]);

        EscalationTier::create([
            'name' => 'L2 Infrastructure/Network',
            'level' => 2,
            'assigned_team' => 'NOC',
            'sla_minutes' => 240,
        ]);

        EscalationTier::create([
            'name' => 'L3 Engineering',
            'level' => 3,
            'assigned_team' => 'SysOps',
            'sla_minutes' => 480,
        ]);

        // 3. Automation Rules
        AutomationRule::create([
            'name' => 'Route Hardware Issues to L2',
            'description' => 'Automatically tag and assign hardware requests.',
            'trigger_event' => 'ticket_created',
            'condition_json' => [
                'field' => 'title',
                'op' => 'contains',
                'value' => 'laptop',
            ],
            'action_json' => [
                [
                    'type' => 'add_tag',
                    'payload' => ['tag' => 'hardware'],
                ],
                [
                    'type' => 'change_priority',
                    'payload' => ['priority' => 'high'],
                ]
            ],
            'priority' => 10,
            'is_active' => true,
        ]);

        AutomationRule::create([
            'name' => 'Require Approval for Access Requests',
            'description' => 'Send ticket to manager if requesting VPN or DB access.',
            'trigger_event' => 'ticket_created',
            'condition_json' => [
                'field' => 'title',
                'op' => 'contains',
                'value' => 'access',
            ],
            'action_json' => [
                [
                    'type' => 'request_approval',
                    'payload' => ['role' => 'manager'],
                ]
            ],
            'priority' => 20,
            'is_active' => true,
        ]);
    }
}
