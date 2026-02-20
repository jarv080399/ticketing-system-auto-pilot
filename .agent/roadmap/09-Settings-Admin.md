# Module 9: Settings & System Administration

**Objective:** Provide a centralised admin panel for managing system-wide configuration, branding, business hours, custom fields, and overall system health — enabling non-developer administrators to customise the platform without touching code.

---

## Step 1: Database & Models (Data Engineer)

- [ ] Create `system_settings` table (`key` (string unique), `value` (text), `group` (string, e.g., "general", "branding", "email", "integrations"), `type` (string, boolean, integer, json), `updated_by`, `updated_at`).
- [ ] Create `business_hours` table (`day_of_week` (0-6), `is_working_day` (boolean), `start_time` (TIME), `end_time` (TIME)).
- [ ] Create `holidays` table (`name`, `date` (DATE), `is_recurring` (boolean)).
- [ ] Create `custom_fields` table (`entity_type` (ticket, asset, user), `field_name`, `field_label`, `field_type` (text, number, select, date, checkbox), `options` (JSON nullable for select), `is_required` (boolean), `sort_order`).
- [ ] Create `custom_field_values` table (`custom_field_id`, `entity_type`, `entity_id`, `value` (text)).
- [ ] Implement models: `SystemSetting`, `BusinessHour`, `Holiday`, `CustomField`, `CustomFieldValue`.
- [ ] Seed default system settings:
  - `app_name` = "IT Helpdesk"
  - `app_logo_url` = null
  - `default_ticket_priority` = "Medium"
  - `auto_close_hours` = 72
  - `nudge_hours` = 48
  - `max_file_upload_mb` = 20
  - `allowed_file_types` = "jpg,png,pdf,doc,docx,xlsx,csv,zip"
- [ ] Seed default business hours (Mon-Fri 9am-5pm).

## Step 2: Backend API (Full-Stack Engineer)

- [ ] Expose `GET /api/v1/admin/settings` (grouped by `group`).
- [ ] Expose `PATCH /api/v1/admin/settings` (batch-update multiple settings, Admin only, logs changes to `audit_logs`).
- [ ] Expose CRUD for Business Hours: `GET, PUT /api/v1/admin/business-hours`.
- [ ] Expose CRUD for Holidays: `GET, POST, PUT, DELETE /api/v1/admin/holidays`.
- [ ] Expose CRUD for Custom Fields: `GET, POST, PUT, DELETE /api/v1/admin/custom-fields`.
- [ ] Create a `SettingsService` cached singleton — reads settings from DB on boot, caches in Redis/file. Invalidate on update.
- [ ] Expose `GET /api/v1/admin/system-health`:
  - Database connectivity check.
  - Redis connectivity check.
  - Queue worker status.
  - Disk usage.
  - Last migration status.
- [ ] Expose `GET /api/v1/admin/activity-log` (paginated audit log viewer with filters: user, action, entity type, date range).

## Step 3: Frontend Admin Panel (Vue 3)

- [ ] Build `AdminLayout.vue` with sidebar navigation:
  - General Settings, Branding, Email Configuration, Business Hours, Holidays, Custom Fields, Integrations, System Health, Activity Log.
- [ ] **General Settings Page:**
  - Form fields for `app_name`, default priority, auto-close hours, nudge hours.
  - File upload for company logo.
  - Allowed file types / max upload size.
- [ ] **Business Hours Page:**
  - Weekly schedule grid (7 days × start/end time pickers).
  - Toggle for working/non-working day.
- [ ] **Holidays Page:**
  - Calendar view showing holidays.
  - "Add Holiday" modal with name, date, and recurring toggle.
- [ ] **Custom Fields Page:**
  - List of custom fields grouped by entity type (ticket, asset, user).
  - "Add Custom Field" form (label, type, options for select, required toggle).
  - Drag-and-drop reordering.
- [ ] **System Health Page:**
  - Dashboard with service status cards (DB, Redis, Queue, Disk).
  - Green/Red indicators for each service.
  - Quick actions: clear cache, restart queue worker (if permissions allow).
- [ ] **Activity Log Page:**
  - Filterable data table showing admin actions with timestamps, user, action, and entity.
- [ ] **User Management Page (Admin):**
  - Datatable of all users with role, department, and status.
  - Inline role change.
  - Invite / Create user modal.
  - Deactivate / Reactivate toggle.

## Step 4: Testing (QA Engineer)

- [ ] **Backend (Pest):** Settings update persists correct values and invalidates cache.
- [ ] **Backend (Pest):** Non-admin users receive `403` for all admin endpoints.
- [ ] **Backend (Pest):** Business hours correctly returned for SLA calculations (integration with Module 4's `SlaService`).
- [ ] **Backend (Pest):** Custom field CRUD creates/updates/deletes fields correctly.
- [ ] **Backend (Pest):** Custom field values are stored and retrievable for tickets.
- [ ] **Backend (Pest):** System health endpoint returns accurate status for all services.
- [ ] **Backend (Pest):** Activity log records all admin actions with correct data.
- [ ] **Frontend (Vitest):** Settings form submits correct payload.
- [ ] **Frontend (Vitest):** Business hours grid renders correctly with saved data.
- [ ] **Frontend (Vitest):** Custom fields reorder updates sort_order.
- [ ] **E2E (Cypress):** Admin updates the app name, verifies it reflects in the header.
- [ ] **E2E (Cypress):** Admin creates a custom field for tickets, creates a ticket with that field, verifies the value persists.
