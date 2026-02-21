# Module 8: Notifications & Multi-Channel Communication

**Objective:** Build a unified notification and communication layer that supports email-to-ticket creation, Slack/Teams integration, in-app real-time notifications, and ensures users are always kept informed about ticket progress.

---

## Step 1: Database & Models (Data Engineer) ✅

- [x] Create `notifications` table (Laravel's built-in `database` notification channel).
- [x] Create `notification_preferences` table.
- [ ] Create `email_logs` table (Planned for audit trails).
- [x] Implement `NotificationPreference` model and relationships.
- [x] Seed default notification preferences.

## Step 2: Email-to-Ticket Integration (Full-Stack Engineer) ✅

- [x] Configure **Laravel Mailbox** for inbound emails (threaded replies supported).
- [x] Configure **outbound emails** for ticket events with dynamic Reply-To.
- [x] Handle threading via `ticket-TKT-XXXX@domain.com` pattern.

## Step 3: Slack / Microsoft Teams Integration (Full-Stack Engineer) 🚧

- [x] Implement `SlackNotificationChannel` with Block Kit support.
- [ ] Expose slash commands and interactive buttons (Future enhancement).
- [ ] Implement `TeamsNotificationChannel`.

## Step 4: In-App Notification System (Full-Stack Engineer) ✅

- [x] Build Laravel Notification class hierarchy (Assigned, Updated, Resolved, Created).
- [x] Expose Notification API endpoints.
- [x] Broadcast notifications in real-time via WebSocket.

## Step 5: Frontend Notification UI (Vue 3) ✅

- [x] Build `NotificationBell.vue` component with unread badges and deep linking.
- [x] Build `NotificationSettingsPage.vue` with a matrix UI.
- [ ] Build `IntegrationSettings.vue` (Admin only).

## Step 6: Testing (QA Engineer)

- [ ] **Backend (Pest):** Inbound email creates a new ticket with correct title, body, and source="email".
- [ ] **Backend (Pest):** Reply email (with `In-Reply-To` header) appends a comment to the correct existing ticket.
- [ ] **Backend (Pest):** Idempotency: same email processed twice does not create duplicate tickets.
- [ ] **Backend (Pest):** Notification preferences are respected (disabled channel does not fire).
- [ ] **Backend (Pest):** Slack notification sends correctly formatted Block Kit payload.
- [ ] **Backend (Pest):** All notification types dispatch to the correct channels.
- [ ] **Frontend (Vitest):** NotificationBell shows correct unread count and updates in real-time.
- [ ] **Frontend (Vitest):** Notification preferences toggle saves correctly.
- [ ] **E2E (Cypress):** Agent receives an in-app notification when a ticket is assigned, clicks it, navigates to the ticket.
