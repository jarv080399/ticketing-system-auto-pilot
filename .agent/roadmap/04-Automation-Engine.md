# Module 4: Automation Engine

**Objective:** Implement the "Autopilot" rule engine that dynamically handles ticket routing, SLA escalations, approval workflows, and auto-responses, significantly reducing manual agent overhead.

---

## Step 1: Database & Models (Data Engineer)

- [ ] Create `automation_rules` table (`name`, `description`, `trigger_event` (enum: ticket_created, ticket_updated, sla_approaching, schedule), `condition_json`, `action_json`, `priority`, `is_active`).
- [ ] Create `audit_logs` table (`user_id`, `action`, `auditable_type`, `auditable_id`, `old_values`, `new_values`, `ip_address`, `user_agent`).
- [ ] Create `approval_requests` table (`ticket_id`, `requester_id`, `approver_id`, `status` (pending, approved, rejected), `comment`, `responded_at`).
- [ ] Create `escalation_tiers` table (`name`, `level` (1, 2, 3), `assigned_team`, `sla_minutes`, `notification_channels` (JSON)).
- [ ] Create `sla_policies` table (`name`, `priority`, `response_time_minutes`, `resolution_time_minutes`, `business_hours_only`).
- [ ] Implement `AutomationRule` model with explicit casting (`$casts = ['condition_json' => 'array', 'action_json' => 'array']`).
- [ ] Implement `ApprovalRequest`, `EscalationTier`, `SlaPolicy` models and relationships.
- [ ] Seed with default routing rules (e.g., "Category = Network → Assign Network Team").
- [ ] Seed default SLA policies per priority (Critical: 30 min response / 2 hr resolve, High: 1 hr / 4 hr, etc.).
- [ ] Seed escalation tiers (L1 → L2 → L3).

## Step 2: Backend Rule Execution (Full-Stack Engineer)

- [ ] Write the `AutomationService` class. It parses `condition_json` against newly created or updated tickets.
  - Support operators: `equals`, `not_equals`, `contains`, `greater_than`, `less_than`, `in`, `not_in`.
  - Support compound conditions: `AND` / `OR` grouping.
- [ ] Implement `TicketObserver` hooked to `created` and `updated` events. Pass models to `AutomationService`.
- [ ] Execute actions: `assign_to`, `change_status`, `change_priority`, `send_auto_response`, `add_tag`, `trigger_webhook`, `request_approval`.
- [ ] Inject immutable records into the `AuditLog` for compliance (who/what/when/old/new).
- [ ] **SLA Engine:**
  - `SlaService` calculates `sla_due_at` based on the ticket's priority and the matching `SlaPolicy`.
  - Support **business-hours-only** calculation (skip weekends/holidays – configurable).
  - On ticket creation, auto-set `sla_due_at`.
  - On priority change, recalculate `sla_due_at`.
- [ ] **Escalation Engine:**
  - `EscalationService` checks approaching/breached SLA tickets and auto-escalates to the next tier.
  - Sends notifications to the escalation tier's team and managers.
- [ ] **Approval Workflow:**
  - `ApprovalService` creates an `approval_request` when an automation rule action is `request_approval`.
  - Expose `POST /api/v1/approvals/{id}/respond` (approve/reject with comment).
  - Block ticket progression until approval is received (status = "Pending Approval").
- [ ] Set up Laravel Scheduled Tasks (cron via `php artisan schedule:run`):
  - Every 5 min: check SLA breaches → fire `SlaBreached` event.
  - Every 15 min: auto-close tickets in "Resolved" status for > 72 hours.
  - Every 30 min: send "nudge" reminders for tickets "Waiting on Customer" for > 48 hours.

## Step 3: Frontend Rule Builder (Vue 3)

- [ ] Add `AutomationSettings.vue` page accessible only to `admin` roles.
- [ ] Build `RuleBuilder.vue` component:
  - Drag-and-drop or row-based UI connecting Condition blocks (`IF field OP value`) with Action blocks (`THEN action`).
  - Support `AND`/`OR` condition groups visually.
  - Preview mode: "Test this rule against the last 10 tickets" dry-run endpoint.
- [ ] Build `SlaSettings.vue` to configure SLA policies per priority.
- [ ] Build `EscalationSettings.vue` to configure escalation tiers and assigned teams.
- [ ] Build `ApprovalQueue.vue` for managers to view and respond to pending approval requests.
- [ ] Include `AuditLogViewer.vue` for admins to trace why an automation rule fired on a specific ticket.

## Step 4: Testing (QA Engineer)

- [ ] **Backend (Pest):** Unit test the `AutomationService` against complex nested JSON rules (AND/OR groups, all operators).
- [ ] **Backend (Pest):** Observer test—stub a ticket save, ensure rule fires, assert assignee was updated and audit log created.
- [ ] **Backend (Pest):** SLA calculation with business hours (ticket created Friday 5pm → due Monday 9am).
- [ ] **Backend (Pest):** Escalation fires when SLA is breached, moves ticket to next tier.
- [ ] **Backend (Pest):** Approval workflow blocks ticket until approved; rejection returns ticket to requester.
- [ ] **Backend (Pest):** Auto-close scheduled task resolves stale tickets.
- [ ] **Frontend (Vitest):** JSON payload structured correctly when saving a new rule via UI.
- [ ] **Frontend (Vitest):** SLA policy form validates required fields.
- [ ] **E2E (Cypress):** US005 - Manager/Admin confirms automatic ticket assignment works.
- [ ] **E2E (Cypress):** Manager responds to an approval request and ticket progresses.
