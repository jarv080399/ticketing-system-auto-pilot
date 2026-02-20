# Functional Requirements Document (FRD)

## 1. Introduction

This document expands on the Business Requirements Document (BRD) and defines the concrete functional behavior of the **NextGen IT Ticketing System**. It is organized to support a lightweight, “autopilot” experience while remaining simple to develop and maintain.

---

## 2. Success Metrics (mirrored from BRD)

- **First‑response time:** ≤ 15 minutes for 90 % of tickets.
- **Average resolution time:** ≤ 4 hours for 80 % of tickets.
- **User satisfaction (CSAT):** ≥ 4.5 / 5.
- **Ticket‑creation clicks:** ≤ 3 clicks for end‑users.
- **Admin overhead reduction:** 50 % fewer manual routing rules.

---

## 3. Traceability Matrix

| BRD Objective                | FRD Module / Feature                           |
| ---------------------------- | ---------------------------------------------- |
| Improve User Experience (UX) | Module 2 – End‑User Self‑Service Portal (F2.x) |
| Simplify Management          | Module 4 – Automation Engine (F4.x)            |
| Enhance Resolution Time      | Module 3 – Agent Workspace (F3.x)              |
| Centralize IT Operations     | Modules 1‑6 (overall integration)              |

---

## 4. Core Modules & Features

### Module 1: Authentication & User Management

- **F1.1 Single Sign‑On (SSO)** – Google Workspace, Microsoft Entra ID, generic SAML 2.0.
- **F1.2 Role‑Based Access Control (RBAC)** – End‑User, Agent, Manager/Admin roles.
- **F1.3 User Profile Auto‑populate** – Name, email, department, manager from IdP.
- **F1.4 Provisioning Workflow** – New‑hire onboarding automatically assigns the appropriate role and adds the user to the Self‑Service Portal.

### Module 2: End‑User Self‑Service Portal ("Storefront")

- **F2.1 Universal Search Bar** – Live search across KB and the user’s past tickets (response ≤ 1 s).
- **F2.2 Service Catalog Templates** – Tile‑based forms for common requests (e.g., New Mouse, Password Reset).
- **F2.3 Smart Form Routing** – Dynamically hide irrelevant fields based on selected category.
- **F2.4 Ticket Status Dashboard** – Domino‑style progress bar (Submitted → Reviewed → In Progress → Resolved).
- **F2.5 Click‑Limit Enforcement** – UI flow limited to ≤ 3 clicks (validated via automated UI tests).

### Module 3: Agent Workspace ("Cockpit")

- **F3.1 Unified Inbox/Queue** – Real‑time WebSocket updates, no manual refresh.
- **F3.2 Kanban Board View** – Drag‑and‑drop status changes.
- **F3.3 Quick‑Action Macros** – One‑click actions (e.g., "Resolve & Close", "Assign to Me").
- **F3.4 Threaded Communications** – Public replies vs internal notes, rich‑text editor.
- **F3.5 SLA Indicators** – Color‑coded timers, auto‑escalation triggers.
- **F3.6 Collision Detection** – Warn if another agent is editing the same ticket.
- **F3.7 Bulk Operations** – Select multiple tickets for assign/close/export.

### Module 4: Automation Engine ("Autopilot")

- **F4.1 Rule‑Based Ticket Routing** – IF [Category=Network] THEN Assign to Network Team.
- **F4.2 Auto‑Responses** – Immediate acknowledgment with dynamic variables.
- **F4.3 State‑Based Triggers** – SLA breach alerts, auto‑close after 72 h, nudges after 48 h.
- **F4.4 Simple Rule‑Builder UI** – Drag‑and‑drop condition/action editor.
- **F4.5 Automation Audit Log** – Immutable log of all automated actions for compliance.

### Module 5: Knowledge Base (KB)

- **F5.1 Article Editor** – WYSIWYG with markdown support.
- **F5.2 Visibility Controls** – Public (end‑user) vs Internal (agent‑only).
- **F5.3 In‑Ticket Article Suggestion** – Up to 3 relevant KB articles displayed before ticket submit.
- **F5.4 Article Feedback** – Thumbs‑up/down, usefulness rating.
- **F5.5 Review Cycle** – Articles flagged for review every 6 months.

### Module 6: Asset Tracking (Lite)

- **F6.1 Asset Database** – Manual entry or CSV import of laptops, monitors, licenses.
- **F6.2 User‑Asset Assignment** – Link asset serial number to employee.
- **F6.3 Ticket‑Asset Linkage** – Auto‑display assigned assets in ticket view.
- **F6.4 Capacity** – Supports up to 10 k assets without performance degradation.

### Module 7: Reporting & Analytics

- **F7.1 Agent Performance Dashboard** – Avg. response time, resolution time, tickets closed.
- **F7.2 Volume Heatmap** – Ticket volume by hour/day for staffing.
- **F7.3 Category Breakdown** – Pie chart of ticket categories.
- **F7.4 Custom Export** – CSV/Excel export of raw ticket data.
- **F7.5 Scheduled Reports** – Daily/weekly email PDFs to managers.

---

## 5. Integration Requirements

- **Email Parsing** – Inbound/outbound email creates or updates tickets (SMTP/IMAP). Idempotent handling to avoid duplicates.
- **Instant Messaging** – Slack/Teams webhook for critical alerts.
- **Webhooks** – External systems can subscribe to ticket events (created, updated, closed).

---

## 6. Performance Acceptance Criteria

| Criterion                    | Target            |
| ---------------------------- | ----------------- |
| Page Load (first view)       | ≤ 2 seconds on 3G |
| Global Search (10 k records) | ≤ 1 second        |
| Real‑time Queue Refresh      | ≤ 500 ms latency  |
| Bulk Action (100 tickets)    | ≤ 2 seconds       |
| API Response (GET ticket)    | ≤ 200 ms          |

---

## 7. Error Handling & Edge Cases

- **SSO Failure** – Fallback to local admin login with audit log.
- **Routing Rule Conflict** – System selects highest‑priority rule and logs warning.
- **Email Duplicate** – Detect identical subject+timestamp, ignore duplicate creation.
- **Asset Not Found** – Show placeholder and allow manual entry.
- **Automation Loop Prevention** – Max 5 automated actions per ticket per hour.
- **Collision During Bulk Edit** – Abort bulk operation if any ticket is locked by another agent.

---

## 8. Non‑Functional Requirements (re‑stated for completeness)

- **Usability** – Clean typography, responsive design, ≤ 3 clicks for ticket creation.
- **Security** – SSO, RBAC, TLS, data‑at‑rest encryption.
- **Scalability** – Support 500 concurrent users, 10 k tickets.
- **Maintainability** – CI/CD pipeline, automated unit & integration tests, versioned API.

---

## 9. Glossary

- **SLA** – Service Level Agreement.
- **KB** – Knowledge Base.
- **SSO** – Single Sign‑On.
- **RBAC** – Role‑Based Access Control.
- **API** – Application Programming Interface.

---

## 10. Approval Sign‑Off (Versioned)

| Name | Role                         | Signature | Date |
| ---- | ---------------------------- | --------- | ---- |
|      | IT Manager / Project Sponsor |           |      |
|      | Lead IT Technician           |           |      |
|      | Product Owner                |           |      |

_Document version: v1.1 (2026‑02‑20)_
