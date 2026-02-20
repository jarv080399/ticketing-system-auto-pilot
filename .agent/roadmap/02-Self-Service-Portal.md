# Module 2: End-User Self-Service Portal

**Objective:** Create an intuitive, fast ("Storefront") UI for end-users to search for help, view their ticket statuses, and submit new requests in ≤ 3 clicks. Multi-channel-ready from day one.

---

## Step 1: Database & Models (Data Engineer)

- [ ] Design and migrate `tickets` table (`ticket_number`, `title`, `description`, `status`, `priority`, `category`, `requester_id`, `source` (enum: web, email, slack, teams), `tags` (JSON nullable)).
- [ ] Create `ticket_attachments` table (`ticket_id`, `file_name`, `file_path`, `mime_type`, `file_size`).
- [ ] Build `Ticket` model with a boot method generating the `TKT-XXXXX` identifier.
- [ ] Build `TicketAttachment` model with relationship `$ticket->attachments()`.
- [ ] Create `TicketFactory` and seed sample user-generated tickets (various statuses, priorities).
- [ ] Implement status enum/constants: `New`, `In Progress`, `Waiting on Customer`, `Resolved`, `Closed`.
- [ ] Implement priority enum/constants: `Low`, `Medium`, `High`, `Critical`.

## Step 2: Backend API (Full-Stack Engineer)

- [ ] Implement `StoreTicketRequest` with strict validation rules (title required, max length, category required, file size/type constraints).
- [ ] Expose `POST /api/v1/tickets` (User ticket creation with file uploads via `multipart/form-data`).
- [ ] Expose `GET /api/v1/tickets/my-tickets` (User's tickets with status and pagination, filterable by status).
- [ ] Expose `GET /api/v1/tickets/{ticket_number}` (Single ticket detail with comments and attachments).
- [ ] Implement Universal Search API `GET /api/v1/search?q=` (Stub KB search, implement Ticket search).
- [ ] Implement **auto-acknowledgement**: dispatch a `TicketCreatedNotification` (email + in-app) immediately when a ticket is submitted.
- [ ] Implement file upload to local disk (or S3 in production) with virus-scan hook (placeholder).
- [ ] Expose `GET /api/v1/categories` to return the service catalog categories dynamically.
- [ ] Add **duplicate detection**: before saving, check for recent tickets with similar titles from the same requester and warn the user.

## Step 3: Frontend Portal (Vue 3)

- [ ] Build `PortalLayout.vue` with a clean dashboard showing:
  - Open tickets count badge.
  - Quick actions ("New Request", "My Devices", "KB").
- [ ] Implement Universal Search Bar component (with debounce + loading skeleton) for instantaneous lookup.
- [ ] Build Tile-based "Service Catalog" component (`CategoryTiles.vue`) mapping to ticket categories, with icons per category.
- [ ] Implement `TicketSubmissionForm.vue`:
  - Dynamic fields based on category (smart form routing).
  - **File attachment** drag-and-drop zone (multi-file, max 5, validated types).
  - Priority selector (defaulting to "Medium").
  - "Similar tickets found" warning banner if duplicate detected.
- [ ] Build `TicketStatusTracker.vue` (Domino-style visual progress bar with step labels).
- [ ] Build `TicketDetailView.vue` for end-users to view their own ticket thread and add replies (public only).
- [ ] Implement **toast notifications** (Vue Toastification or similar) for ticket submission confirmation.

## Step 4: Testing (QA Engineer)

- [ ] **Backend (Pest):** End-users can only view/read/update their _own_ tickets. Test `StoreTicketRequest` validation.
- [ ] **Backend (Pest):** File upload stores file correctly and `ticket_attachments` record is created.
- [ ] **Backend (Pest):** Auto-acknowledgement notification is dispatched on ticket creation.
- [ ] **Backend (Pest):** Duplicate detection triggers warning for similar recent tickets.
- [ ] **Frontend (Vitest):** Form dynamic routing based on category tile click.
- [ ] **Frontend (Vitest):** File attachment component accepts valid files and rejects oversized/invalid types.
- [ ] **E2E (Cypress):** US001 - Assert an end-user can submit a ticket in ≤ 3 clicks. Validate UI limit enforcement.
- [ ] **E2E (Cypress):** Submit a ticket with 3 attachments, verify they appear in the detail view.
