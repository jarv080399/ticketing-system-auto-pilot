# Module 8: Notifications & Multi-Channel Communication

**Objective:** Build a unified notification and communication layer that supports email-to-ticket creation, Slack/Teams integration, in-app real-time notifications, and ensures users are always kept informed about ticket progress.

---

## Step 1: Database & Models (Data Engineer)

- [ ] Create `notifications` table (Laravel's built-in `database` notification channel):
  - `id` (UUID), `type`, `notifiable_type`, `notifiable_id`, `data` (JSON), `read_at`, `created_at`.
- [ ] Create `notification_preferences` table (`user_id`, `channel` (email, in_app, slack, teams), `event_type` (ticket_created, ticket_updated, ticket_assigned, sla_warning, ticket_resolved, ticket_closed), `is_enabled` (boolean)).
- [ ] Create `email_logs` table (`ticket_id`, `direction` (inbound, outbound), `from_address`, `to_address`, `subject`, `body_text`, `message_id`, `in_reply_to`, `processed_at`).
- [ ] Implement `NotificationPreference`, `EmailLog` models and relationships.
- [ ] Seed default notification preferences (all enabled for all users).

## Step 2: Email-to-Ticket Integration (Full-Stack Engineer)

- [ ] Configure **Laravel Mailbox** (or `beyondcode/laravel-mailbox` package) to receive inbound emails:
  - Parse `To:` address to determine target (e.g., `support@company.com` → new ticket, `ticket-TKT-00123@company.com` → reply to existing ticket).
  - Extract subject, body (plain text fallback from HTML), and attachments.
  - Create a new `Ticket` (source = "email") or append a `TicketComment` if replying.
  - Handle **idempotency**: check `Message-ID` header to prevent duplicate processing.
- [ ] Configure **outbound emails** for ticket events:
  - `TicketCreatedMail` (to requester with ticket number and link).
  - `TicketUpdatedMail` (to requester when status changes).
  - `TicketCommentMail` (to requester when agent posts a public reply; include reply-to-ticket address for threading).
  - `TicketAssignedMail` (to agent when assigned a ticket).
- [ ] Set all outbound emails with proper reply-to headers so users can reply directly to the email and it threads back to the ticket.

## Step 3: Slack / Microsoft Teams Integration (Full-Stack Engineer)

- [ ] Implement `SlackNotificationChannel`:
  - Send formatted Slack messages (using Block Kit) for critical events: new ticket assigned, SLA warning, ticket escalated.
  - Expose `/api/v1/integrations/slack/webhook` for interactive Slack actions (e.g., "Claim Ticket" button in the Slack message).
- [ ] Implement `TeamsNotificationChannel`:
  - Send Adaptive Cards to Teams channels via incoming webhook.
  - Support similar interactive actions.
- [ ] Expose `POST /api/v1/integrations/slack/create-ticket` for Slack slash command (`/ticket "My laptop is broken"`) to create tickets directly from Slack.
- [ ] Store integration configuration (webhook URLs, channel IDs) in a `system_settings` table (Module 9).

## Step 4: In-App Notification System (Full-Stack Engineer)

- [ ] Build a Laravel Notification class hierarchy:
  - `TicketCreatedNotification`, `TicketAssignedNotification`, `TicketUpdatedNotification`, `SlaWarningNotification`, `TicketResolvedNotification`.
  - Each notification supports multiple channels: `['database', 'mail', 'slack']` (respecting user preferences).
- [ ] Expose `GET /api/v1/notifications` (paginated, filterable by read/unread).
- [ ] Expose `PATCH /api/v1/notifications/{id}/read` (mark as read).
- [ ] Expose `POST /api/v1/notifications/mark-all-read`.
- [ ] Broadcast notifications in real-time via WebSocket (`NotificationSent` event) for the in-app bell icon.

## Step 5: Frontend Notification UI (Vue 3)

- [ ] Build `NotificationBell.vue` component:
  - Icon in the header with unread count badge.
  - Dropdown panel listing recent notifications with timestamps.
  - Click a notification → navigate to the relevant ticket.
  - "Mark all as read" button.
  - Real-time updates via Laravel Echo (new notification pops in without refresh).
- [ ] Build `NotificationPreferences.vue` page (accessible from Profile/Settings):
  - Matrix of event types × channels (email, in-app, slack) with toggle switches.
  - Save preferences via `PATCH /api/v1/users/me/notification-preferences`.
- [ ] Build `IntegrationSettings.vue` (Admin only):
  - Configure Slack webhook URL, Teams webhook URL.
  - Test connection button.
  - Channel mapping (which Slack channel gets which event type).

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
