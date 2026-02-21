# Module 3: Agent Workspace Component Testing Scenarios

## Overview

This document outlines the specific end-to-end component and integration testing scenarios required for the `Agent Workspace` module to verify the implementation matches the defined roadmap architecture.

---

## 1. Inbox Real-Time Synchronization (`AgentLayout.vue` & Echo)

- [ ] **Unified Ticket Counting:** Generate 12 "New" tickets. Log in as an Agent. Validate the `QueueSidebar.vue` reads exactly "12". Verify navigating between tabs natively drops/decreases the counter on the frontend as tickets update.
- [ ] **Websocket Broadcasting:** Keep Agent A's browser open to the Kanban. As an End-User, submit a brand new ticket. Validate Agent A receives the `NewTicketAssigned` payload immediately dynamically pushing a card to the "New" column without a page refresh block.
- [ ] **Live Collision Awareness:** Log in as Agent A and view `TKT-00100`. Log in as Agent B and view `TKT-00100`. Validate the TTL cache executes correctly tossing a yellow Vue UI banner stating "Agent A is currently viewing this ticket", and then tearing down completely 30 seconds after Agent A navigates away.

## 2. Cockpit Action Logic (`TicketDetailView.vue` and API)

- [ ] **Private Commentary Constraints:** Post an "Internal Note" with a `.pdf` file. Switch user tokens to the ticket requester. Validate the requester's `/my-tickets` thread completely completely scrubs the `is_internal = true` payload array guaranteeing zero exposure.
- [ ] **SLA Timestamp Mechanics:** Take a "New" ticket. As an agent, post a public reply comment. Trigger the database refresh. Verify the `first_response_at` parameter instantly popups with a raw timestamp object halting the First Response SLA timer. Post a second reply and verify the timestamp does not maliciously overwrite the first entry.
- [ ] **Canned Template Dropdowns:** Boot a ticket. Hit the "Canned Response" dropdown. Select the "Reset VPN" pre-seeded reply. Validate the text injects into the Trix/TipTap editor precisely and retains rich text line breaks.

## 3. Bulk & Merge Architecture (`TicketMerge UI` & `QueueSidebar.vue`)

- [ ] **Ticket Multi-Merge Handling:** Grab 3 active identical tickets from an End-User (`TKT-100`, `TKT-101`, `TKT-102`). As an agent, execute the `POST /api/v1/tickets/{id}/merge` array. Verify `101` and `102` soft delete/close, routing all comment threads chronologically into the specific `primary_ticket_id = 100` thread natively.
- [ ] **Batch Processing Checks:** Click the "Select All" Kanban checkbox highlighting 5 In Progress tickets. Pull the Floating Bar and hit "Assign to Me" + "Status: Waiting on Customer". Send the `POST /api/v1/agent/tickets/bulk` mapping to the backend payload. Verify all 5 structurally change in parallel and emit real-time WebSocket updates back to the UI smoothly.

## 4. Time Tracking Logs

- [ ] **Time Aggregation:** Activate the "Time Tracker" widget. Track "15m" and submit. Track "120m" and submit. Verify the `TicketTimeLog` model aggregates cleanly returning `135 Minutes` spent to the specific `agent_id` parameter map natively inside the sidebar UI.
