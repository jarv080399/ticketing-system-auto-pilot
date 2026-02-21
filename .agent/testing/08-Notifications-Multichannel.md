# Module 8: Notifications & Multi-Channel Communication Component Testing Scenarios

## Overview

This document outlines the specific end-to-end component and integration testing scenarios required for the `Notifications & Multi-Channel Communication` module to verify the implementation matches the defined roadmap architecture.

---

## 1. WebSockets & Real-Time Broadcast Architecture (`NotificationBell.vue`)

- [ ] **Instant Badge Counters:** Authenticate Agent X in browser 1 and browser 2. Log in as an End-User. Create a ticket implicitly targeted to Agent X. In both browser 1 and 2 concurrently verify the UI updates without any `HTTP GET` refreshing and sets the bell badge mapping to `1`.
- [ ] **Badge Clear State:** In browser 1, click the dropdown, review the ticket, and click the item rendering it "Read". Check the database `read_at` tag timestamp, verifying it synchronized seamlessly to browser 2's notification badge removing the unread state counter completely.

## 2. Inbound/Outbound Email Threading Router

- [ ] **Initialization Parsing:** Transmit an email to the Laravel Mailbox queue directly (e.g., `support@ticketing.internal`). Validate the controller extracts the Title, Sender `email_address`, and maps the multi-line Body accurately. Execute the ticket `created_at` log, confirming no errors occur, the ticket is instantly visible inside the active Agent Queue, and the `source` is tagged `email`.
- [ ] **Re-threading `In-Reply-To` Processing:** In the previous test, copy the exact ticket's dynamic Reply-To ID (e.g. `ticket-TKT-10023@domain.com`). Draft a secondary reply via email targeting that precise string. Validate the system rejects duplicate ticket creation and accurately maps the body into a standard Internal/Public Timeline comment for `TKT-10023`.
- [ ] **Idempotent Collision Filter:** Simulate sending the exact same incoming raw email string twice via Postman. Confirm the system natively rejects and gracefully disposes the secondary process preventing twin-ticket bloat in the queue via hash or ID tracking.

## 3. Communication Channel Delivery Controls (`NotificationSettingsPage.vue`)

- [ ] **User Preference Override Matrix:** Authenticate an End User. Traverse to `/user/notification-preferences`. Disable the specific "Email" toggle for "Ticket Replied". Post a new public comment via Agent Cockpit on that user's active ticket. Assert the system filters out the `Mail::send()` process stringently while continuing to broadcast securely to the `App` component channels.

## 4. Third-Party Integrations Engine (Slack/Teams)

- [ ] **Block Kit UI Formatting:** Dispatch a `SlackNotificationChannel` message manually mapping to a critical `ticket_created` workflow. Assess the payload object format matching Slack's Block Kit styling (Icons, Title, Assignee Buttons). Validate the mock payload parses back `201 Accepted` accurately representing clean code transmission.
