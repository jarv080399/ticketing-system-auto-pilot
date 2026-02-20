# Module 6: Asset Tracking (Lite)

**Objective:** Map physical IT devices to users, providing agents with immediate hardware context when handling a ticket.

## Step 1: Database & Models (Data Engineer)

- [ ] Migrate `assets` table (`serial_number` string unique, `type` enum/string, `description` text, `owner_user_id` nullable (FK to `users`), `deleted_at`).
- [ ] Build `Asset` model and mapping relationships: `$user->assets()`, `$asset->owner()`.
- [ ] Update `tickets` table to include `asset_id` (foreign key, nullable).
- [ ] Implement CSV import logic (Artisan command `import:spiceworks assets` or similar) to migrate 10k assets performantly.

## Step 2: Backend API (Full-Stack Engineer)

- [ ] Expose CRUD for assets (`role:agent` protected): `GET /api/v1/assets`, `POST /api/v1/assets`.
- [ ] Update user profile endpoint (`GET /api/v1/auth/me`) or Ticket fetch endpoints to eager-load `assets`.
- [ ] Establish `GET /api/v1/users/{id}/assets` for fetching a specific user's assigned hardware.

## Step 3: Frontend Integration (Vue 3)

- [ ] **Asset Registry UI (Admin):** Datatable for viewing and manually curating all 10,000+ assets (Virtual scrolling / pagination). Include bulk CSV upload component.
- [ ] **Storefront Tile (User):** Display "My Devices" section on the Self-Service Portal dashboard.
- [ ] **Ticket Cockpit (Agent):** When viewing a ticket, auto-display a sidebar card with the Requester's assigned `Assets` (Laptop model, S/N) to eliminate back-and-forth query chatter over specs.

## Step 4: Testing (QA Engineer)

- [ ] **Backend (PHPUnit):** Memory limit/Performance verification on fetching 10k assets through pagination.
- [ ] **Backend (Pest):** Soft-delete integrity (deleted asset shouldn't crash historical tickets).
- [ ] **Frontend (Vitest):** Ticket view properly displays assigned asset information.
- [ ] **E2E (Cypress):** Agent ties an asset manually to a user profile, verifies it appears in user's side of the portal.
