# Module 2: End-User Self-Service Portal

**Objective:** Create an intuitive, fast ("Storefront") UI for end-users to search for help, view their ticket statuses, and submit new requests in ≤ 3 clicks.

## Step 1: Database & Models (Data Engineer)

- [ ] Design and migrate `tickets` table (`ticket_number`, `title`, `description`, `status`, `priority`, `category`, `requester_id`).
- [ ] Build `Ticket` model with a boot method generating the `TKT-XXXXX` identifier.
- [ ] Create `TicketFactory` and seed sample user-generated tickets.

## Step 2: Backend API (Full-Stack Engineer)

- [ ] Implement `StoreTicketRequest` for validation rules.
- [ ] Expose `POST /api/v1/tickets` (User ticket creation).
- [ ] Expose `GET /api/v1/tickets/my-tickets` (User's tickets with status and pagination).
- [ ] Implement Universal Search API `GET /api/v1/search?q=` (Stub KB search, implement Ticket search).

## Step 3: Frontend Portal (Vue 3)

- [ ] Build `PortalLayout.vue`.
- [ ] Implement Universal Search Bar component (with debounce) for instantaneous lookup.
- [ ] Build Tile-based "Service Catalog" component (`CategoryTiles.vue`) mapping to ticket categories.
- [ ] Implement `TicketSubmissionForm.vue` (Dynamic fields based on category).
- [ ] Build `TicketStatusTracker.vue` (Domino-style visual progress).

## Step 4: Testing (QA Engineer)

- [ ] **Backend (Pest):** End-users can only view/read/update their _own_ tickets. Test `StoreTicketRequest` validation.
- [ ] **Frontend (Vitest):** Form dynamic routing based on category tile click.
- [ ] **E2E (Cypress):** US001 - Assert an end-user can submit a ticket in ≤ 3 clicks. Validate UI limit enforcement.
