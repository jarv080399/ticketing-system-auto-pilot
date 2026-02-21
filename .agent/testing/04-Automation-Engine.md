# Module 4: Automation Engine Component Testing Scenarios

## Overview

This document outlines the specific end-to-end component and integration testing scenarios required for the `Automation Engine` module to verify the implementation matches the defined roadmap architecture.

---

## 1. Rule Execution Logic (`AutomationService`)

- [ ] **Condition Parsing (JSON):** Create an Automation Rule with `trigger_event = ticket_created` and a compound condition (`field: category, op: equals, value: Network` AND `field: priority, op: equals, value: High`). Submit a matching ticket. Validate the observer hooks send the ticket array to the `AutomationService`, the conditions resolve to `true`, and the action executes successfully.
- [ ] **Action Execution (Assignment & Tagging):** Define a rule action to `assign_to: {user_id: 5}` and `add_tag: {tag: hardware}`. Trigger the rule. Check the database `tickets` and `tags` tables to verify the properties were updated without manual agent input.
- [ ] **Audit Trailing:** Check the `audit_logs` table after an automation rule fires. Verify a record exists containing the `auditable_id` of the ticket, the `action: automation_triggered`, and a JSON dump of `old_values` vs `new_values` confirming transparency.

## 2. SLA Engine Constraints (`SlaService`)

- [ ] **Creation Timestamping:** Create a High Priority ticket. Validate that `SlaService` calculates the exact `sla_due_at` timestamp based on the matching `SlaPolicy` definition (e.g. +4 hours from `created_at`).
- [ ] **Business Hours Math:** Configure the environment Business Hours to exclude weekends. Create a Medium Priority ticket on Friday at 4:30 PM (with a 4-hour SLA). Assert the `sla_due_at` correctly skips Saturday and Sunday, landing on Monday morning.
- [ ] **Dynamic Priority Shifting:** Update an existing "Low" priority ticket to "Critical". Verify the system observer intercepts the change and instantly recalculates and shortens the `sla_due_at` parameter.

## 3. Escalation and Approval Mechanisms

- [ ] **Approaching SLA Cron:** Manipulate a ticket's `sla_due_at` to be 5 minutes from now. Run `php artisan schedule:run` to simulate the background cron. Validate the `EscalationService` catches the ticket and dispatches warning notifications to the assigned owner.
- [ ] **Escalation Tier Movement:** Let a ticket fully breach its SLA. Run the cron. Verify the ticket `assignee_id` or `assigned_team` correctly drops into the L2 `EscalationTier` mapping.
- [ ] **Manager Approval Blocking:** Trigger a rule calling the `request_approval` action. Verify the ticket status locks forcibly to "Pending Approval". Verify the Manager receives the request, submits a `POST /approvals/{id}/respond` containing "Approved", and the ticket unlocks back into "In Progress".

## 4. Frontend Builders (`AdminAutomationPage.vue` & `ApprovalQueue.vue`)

- [ ] **JSON State Serialization:** Edit an automation rule from the Vue UI. Type invalid JSON into the Action Payload box. Click Save. Validate the front-end string parsing tosses a Vue Toastification error "Invalid JSON structure" instead of submitting a 500 error to the backend.
- [ ] **Approval Grid Rendering:** Log in as a Manager. Navigate to `ApprovalQueue.vue`. Validate the datatable renders pending requests accurately, and clicking "Reject" fires the endpoint correctly, returning the UI state to empty.
