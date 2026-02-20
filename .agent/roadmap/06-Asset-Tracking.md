# Module 6: Asset Tracking (Lite)

**Objective:** Map physical IT devices to users, provide lifecycle tracking, and give agents immediate hardware context when handling a ticket.

---

## Step 1: Database & Models (Data Engineer)

- [ ] Migrate `assets` table:
  - `serial_number` (string, unique), `asset_tag` (string, unique nullable for custom internal identifier).
  - `type` (enum/string: laptop, desktop, monitor, printer, phone, license, peripheral, other).
  - `manufacturer`, `model`, `description` (text).
  - `status` (enum: active, in_repair, retired, disposed, in_stock).
  - `owner_user_id` (nullable FK to `users`).
  - `purchase_date`, `warranty_expires_at` (date nullable).
  - `purchase_cost` (decimal nullable).
  - `location` (string nullable, e.g., "Building A, Floor 3").
  - `notes` (text nullable).
  - `deleted_at`.
- [ ] Create `asset_history` table (`asset_id`, `action` (assigned, unassigned, status_changed, edited), `performed_by`, `old_value`, `new_value`, `created_at`).
- [ ] Build `Asset`, `AssetHistory` models and mapping relationships:
  - `$user->assets()`, `$asset->owner()`, `$asset->history()`.
- [ ] Update `tickets` table to include `asset_id` (foreign key, nullable).
- [ ] Implement CSV import logic (Artisan command `import:assets {csv_file}`) to migrate 10k+ assets performantly (chunked inserts with progress bar).

## Step 2: Backend API (Full-Stack Engineer)

- [ ] Expose CRUD for assets (`role:agent` protected):
  - `GET /api/v1/assets` (list with filtering by type, status, owner; search by serial/model; paginated).
  - `POST /api/v1/assets` (create).
  - `PUT /api/v1/assets/{id}` (update).
  - `DELETE /api/v1/assets/{id}` (soft-delete).
- [ ] Expose `POST /api/v1/assets/{id}/assign` to assign/reassign to a user (creates `asset_history` entry).
- [ ] Expose `POST /api/v1/assets/{id}/unassign` to remove current owner.
- [ ] Expose `GET /api/v1/users/{id}/assets` for fetching a specific user's assigned hardware.
- [ ] Expose `GET /api/v1/assets/{id}/history` for audit trail of ownership and status changes.
- [ ] Expose `POST /api/v1/assets/import` for admin CSV bulk upload via API (processes in queue).
- [ ] Implement **warranty expiry alerts**: scheduled job checks `warranty_expires_at` within 30 days and sends notification.

## Step 3: Frontend Integration (Vue 3)

- [ ] **Asset Registry UI (Admin/Agent):**
  - Datatable for viewing and managing all assets (virtual scrolling / server-side pagination).
  - Inline filtering by type, status, location, warranty status.
  - "Add Asset" form modal and "Edit Asset" drawer.
  - Bulk CSV upload component with progress indicator and error report.
  - Status badge chips (🟢 Active, 🟡 In Repair, 🔴 Retired).
- [ ] **Asset Detail Page:**
  - Full asset information card.
  - Ownership history timeline.
  - Linked tickets list.
  - Warranty countdown indicator (warning if < 30 days).
- [ ] **Storefront Tile (User):** Display "My Devices" section on the Self-Service Portal dashboard with asset type icons.
- [ ] **Ticket Cockpit (Agent):** When viewing a ticket, auto-display a sidebar card with the Requester's assigned `Assets` (type, model, S/N, warranty status) to eliminate back-and-forth queries.

## Step 4: Testing (QA Engineer)

- [ ] **Backend (Pest):** Memory limit / performance verification on paginated fetch of 10k+ assets.
- [ ] **Backend (Pest):** Soft-delete integrity (deleted asset shouldn't crash historical tickets).
- [ ] **Backend (Pest):** Assign/unassign creates correct `asset_history` records.
- [ ] **Backend (Pest):** CSV import handles valid rows, logs invalid rows, and returns an error report.
- [ ] **Backend (Pest):** Warranty expiry notification fires for assets expiring within 30 days.
- [ ] **Frontend (Vitest):** Ticket view properly displays assigned asset information.
- [ ] **Frontend (Vitest):** Asset detail page renders ownership timeline correctly.
- [ ] **E2E (Cypress):** Agent assigns an asset to a user, verifies it appears in user's portal.
- [ ] **E2E (Cypress):** Admin bulk-uploads a CSV of assets, verifies count increased.
