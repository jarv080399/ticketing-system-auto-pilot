# Module 3: Agent Workspace

**Objective:** Build the robust "Cockpit" for Agents to efficiently manage the queue, update statuses, respond to users, and track SLAs.

## Step 1: Database & Models (Data Engineer)

- [ ] Create `ticket_comments` table (`ticket_id`, `user_id`, `body`, `is_internal`).
- [ ] Add `assignee_id`, `sla_due_at`, `resolved_at` columns to `tickets` table (if not done in Mod 2).
- [ ] Implement `TicketComment` model and relationship (`$ticket->comments()`).
- [ ] Seed test tickets with a rich thread of comments.

## Step 2: Backend API (Full-Stack Engineer)

- [ ] Protect all endpoints under the `role:agent` middleware.
- [ ] Expose `GET /api/v1/agent/tickets` (with filtering, sorting, pagination).
- [ ] Implement `PATCH /api/v1/tickets/{id}` for assignment, status changes, manual SLA overrides.
- [ ] Expose `POST /api/v1/tickets/{id}/comments` (Internal vs. Public replies).
- [ ] Setup basic WebSocket broadcasting (Laravel Reverb / Pusher) for real-time ticket updates (`TicketUpdated` event).

## Step 3: Frontend Agent Dashboard (Vue 3)

- [ ] Build `AgentLayout.vue` and `QueueSidebar.vue`.
- [ ] **Unified Inbox:** Integrate Laravel Echo/Pusher client for real-time list updates (no manual refresh).
- [ ] **Kanban Board:** Construct drag-and-drop columns corresponding to ticket statuses (New, In Progress, Waiting, Resolved).
- [ ] **Ticket Detail View:** Threaded conversation view, rich-text editor for replies, internal note toggle.
- [ ] **Quick Macros:** Add one-click preset actions ("Assign to Me", "Resolve & Close").
- [ ] **SLA Indicators:** Color-code rows/cards based on time remaining to `sla_due_at`.

## Step 4: Testing (QA Engineer)

- [ ] **Backend (Pest):** Agent updates ticket status, asserts `TicketUpdated` socket event fires.
- [ ] **Backend (Pest):** Prevent users from posting internal comments.
- [ ] **Frontend (Vitest):** Test that the Kanban component emits status change requests cleanly.
- [ ] **E2E (Cypress):** US003 - Agent claims a ticket from the queue. US004 - Agent uses a macro.
