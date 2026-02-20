# Module 3: Agent Workspace

**Objective:** Build the robust "Cockpit" for Agents to efficiently manage the queue, update statuses, respond to users, track SLAs, and collaborate with other agents.

---

## Step 1: Database & Models (Data Engineer)

- [ ] Create `ticket_comments` table (`ticket_id`, `user_id`, `body`, `is_internal`, `created_at`, `updated_at`).
- [ ] Create `canned_responses` table (`title`, `body`, `category`, `created_by`, `is_shared`).
- [ ] Create `ticket_time_logs` table (`ticket_id`, `agent_id`, `minutes`, `description`, `logged_at`).
- [ ] Create `ticket_merges` table (`primary_ticket_id`, `merged_ticket_id`, `merged_by`, `merged_at`).
- [ ] Add `assignee_id`, `sla_due_at`, `resolved_at`, `first_response_at`, `closed_at` columns to `tickets` table (if not done in Mod 2).
- [ ] Implement `TicketComment`, `CannedResponse`, `TicketTimeLog` models and relationships.
- [ ] Seed test tickets with a rich thread of comments (mix of public and internal notes).
- [ ] Seed sample canned responses for common replies.

## Step 2: Backend API (Full-Stack Engineer)

- [ ] Protect all endpoints under the `role:agent` middleware.
- [ ] Expose `GET /api/v1/agent/tickets` (with **filtering** by status, priority, assignee, category, date range; **sorting**; **pagination**).
- [ ] Implement `PATCH /api/v1/tickets/{id}` for assignment, status changes, priority changes, manual SLA overrides.
- [ ] Expose `POST /api/v1/tickets/{id}/comments` (Internal vs. Public replies, with optional file attachments).
- [ ] Implement **Collision Detection**: `GET /api/v1/tickets/{id}/viewers` returns agents currently viewing/editing the ticket (via cache key with TTL).
- [ ] Implement **Ticket Merge**: `POST /api/v1/tickets/{id}/merge` merges a secondary ticket into a primary, moving all comments and attachments.
- [ ] Implement **Ticket Linking**: `POST /api/v1/tickets/{id}/link` creates a parent-child or related relationship between tickets.
- [ ] Expose CRUD for canned responses: `GET/POST/PUT/DELETE /api/v1/agent/canned-responses`.
- [ ] Expose time logging: `POST /api/v1/tickets/{id}/time-logs`, `GET /api/v1/tickets/{id}/time-logs`.
- [ ] Implement **Bulk Operations**: `POST /api/v1/agent/tickets/bulk` for mass-assign, mass-close, mass-change-priority.
- [ ] Setup WebSocket broadcasting (Laravel Reverb / Pusher) for real-time ticket updates (`TicketUpdated`, `NewTicketAssigned`, `CollisionWarning` events).
- [ ] Record `first_response_at` timestamp when an agent first replies publicly (critical for SLA tracking).

## Step 3: Frontend Agent Dashboard (Vue 3)

- [ ] Build `AgentLayout.vue` and `QueueSidebar.vue` with live ticket counts per status.
- [ ] **Unified Inbox:** Integrate Laravel Echo/Pusher client for real-time list updates (no manual refresh).
- [ ] **Kanban Board:** Construct drag-and-drop columns corresponding to ticket statuses (New, In Progress, Waiting, Resolved).
- [ ] **Ticket Detail View:**
  - Threaded conversation view, rich-text editor (TipTap or Trix) for replies.
  - Internal note toggle (highlighted differently).
  - File attachment preview pane.
  - Collision detection banner ("Agent Jane is also viewing this ticket").
  - Requester sidebar card (name, department, assets, past ticket count).
- [ ] **Quick Macros:** Add one-click preset actions ("Assign to Me", "Resolve & Close", "Escalate to L2").
- [ ] **Canned Responses:** Dropdown/modal to insert saved reply templates into the editor.
- [ ] **SLA Indicators:** Color-code rows/cards based on time remaining to `sla_due_at`:
  - 🟢 Green: > 50% time remaining.
  - 🟡 Yellow: 25–50% time remaining.
  - 🔴 Red: < 25% or breached.
- [ ] **Time Tracker:** Small widget to log time spent on a ticket.
- [ ] **Bulk Actions Bar:** Checkbox selection + floating action bar for multi-ticket operations.
- [ ] **Ticket Merge UI:** "Merge into" modal that searches for a primary ticket.

## Step 4: Testing (QA Engineer)

- [ ] **Backend (Pest):** Agent updates ticket status, asserts `TicketUpdated` socket event fires.
- [ ] **Backend (Pest):** Prevent end-users from posting internal comments.
- [ ] **Backend (Pest):** Collision detection returns correct list of active viewers.
- [ ] **Backend (Pest):** Ticket merge moves all comments, attachments, and sets merged ticket to "Closed".
- [ ] **Backend (Pest):** Bulk operations update all targeted tickets atomically.
- [ ] **Backend (Pest):** First-response timestamp is set only on the first public reply.
- [ ] **Backend (Pest):** Canned response CRUD (create, update, delete, list shared vs. personal).
- [ ] **Frontend (Vitest):** Kanban component emits status change requests cleanly.
- [ ] **Frontend (Vitest):** Canned response inserts text into the editor.
- [ ] **E2E (Cypress):** US003 - Agent claims a ticket from the queue. US004 - Agent uses a macro.
- [ ] **E2E (Cypress):** Agent merges two tickets and verifies the thread consolidation.
