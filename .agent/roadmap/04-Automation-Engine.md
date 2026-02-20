# Module 4: Automation Engine

**Objective:** Implement the "Autopilot" rule engine that dynamically handles ticket routing, SLA escalations, and auto-responses, significantly reducing manual agent overhead.

## Step 1: Database & Models (Data Engineer)

- [ ] Create `automation_rules` table (`name`, `condition_json`, `action_json`, `priority`, `is_active`).
- [ ] Create `audit_logs` table (`user_id`, `action`, `auditable_type`, `auditable_id`, `old_values`, `new_values`).
- [ ] Implement `AutomationRule` model with explicit casting (`$casts = ['condition_json' => 'array', 'action_json' => 'array']`).
- [ ] Seed with default routing rules (e.g., "Category = Network -> Assign Network Team").

## Step 2: Backend Rule Execution (Full-Stack Engineer)

- [ ] Write the `AutomationService` class. It parses `condition_json` (e.g., `["field" => "category", "op" => "equals", "value" => "network"]`) against newly created or updated tickets.
- [ ] Implement `TicketObserver` (or Dispatch events) hooked to `created` and `updated` hooks. Pass models to the `AutomationService`.
- [ ] Execute actions (`action_json`) like "assign_to", "change_status", "send_auto_response".
- [ ] Inject immutable records into the `AuditLog` for compliance.
- [ ] Set up a Laravel Scheduled Task (cron via `php artisan schedule:run`) for State-Based Triggers (auto-close after 72h, SLA breaches).

## Step 3: Frontend Rule Builder (Vue 3)

- [ ] Add `AutomationSettings.vue` page accessible only to `admin` roles.
- [ ] Build `RuleBuilder.vue` component: a drag-and-drop or simple row-based UI connecting Condition blocks (`IF _ OP _`) with Action blocks (`THEN _`).
- [ ] Include an `AuditLogViewer.vue` for admins to trace why an automation rule fired on a specific ticket.

## Step 4: Testing (QA Engineer)

- [ ] **Backend (Pest):** Unit test the `AutomationService` deeply against complex nested JSON rules.
- [ ] **Backend (Pest):** Observer test—stub a ticket save, ensure rule fires, asserts assignee was updated and log created.
- [ ] **Frontend (Vitest):** JSON payload structured correctly when saving a new rule via UI.
- [ ] **E2E (Cypress):** US005 - Manager/Admin confirms automatic ticket assignment works.
