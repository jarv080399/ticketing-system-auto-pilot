# Business Requirements Document (BRD)

## 1. Project Overview

**Project Name:** NextGen IT Ticketing System  
**Document Version:** v1.1  
**Current System:** Spiceworks

### 1.1 Executive Summary

The goal is to replace Spiceworks with a lightweight, intuitive ticketing system that feels like an "autopilot" experience for both end‑users and IT staff. The new platform will eliminate clutter, simplify management, and accelerate issue resolution.

### 1.2 Success Metrics (how we will measure success)

- **First‑response time:** ≤ 15 minutes for 90 % of tickets.
- **Average resolution time:** ≤ 4 hours for 80 % of tickets.
- **User satisfaction (CSAT):** ≥ 4.5 / 5.
- **Ticket‑creation clicks:** ≤ 3 clicks for end‑users.
- **Admin overhead reduction:** 50 % fewer manual routing rules compared to Spiceworks.

---

## 2. Objectives (prioritized)

1. **Improve User Experience (UX)** – frictionless, modern UI for ticket creation and tracking.
2. **Simplify Management** – easy‑to‑configure automation, routing, and reporting.
3. **Enhance Resolution Time** – reduce triage and assignment latency.
4. **Centralize IT Operations** – integrate ticketing, basic asset tracking, and knowledge base in one platform.

---

## 3. Scope

### 3.1 In‑Scope (Phase 1 – Minimum Viable Product)

- **Ticketing Module** – full lifecycle management.
- **Self‑Service Portal** – end‑user request submission, status view, FAQs.
- **Routing & Automation** – rule‑based assignment and auto‑responses.
- **Knowledge Base (KB)** – searchable SOPs and guides.
- **Asset Linkage** – associate tickets with user devices.
- **Reporting & Analytics** – real‑time dashboards and export.

### 3.2 Out‑of‑Scope (Phase 1)

- Deep network discovery/monitoring.
- Multi‑tenant billing systems.
- Advanced HR integrations beyond SSO/AD.

---

## 4. User Personas & Pain Points

| Persona                   | Pain Point (Spiceworks)                                    |
| ------------------------- | ---------------------------------------------------------- |
| **End User (Employee)**   | Too many clicks, confusing UI, no clear status.            |
| **IT Technician (Agent)** | Cluttered dashboard, manual routing, no quick actions.     |
| **IT Manager**            | Lack of visibility into SLA breaches and team performance. |
| **System Administrator**  | Complex role management, difficult integration setup.      |

---

## 5. Functional Requirements (high‑level)

_(Detailed functional specs are captured in the FRD.)_

- **Ticket Lifecycle Management** – creation, categorization, auto‑routing, threaded communication, status tracking.
- **Self‑Service Portal** – search‑first UI, template catalog, real‑time status.
- **Agent Workspace** – kanban/list views, quick‑action macros, SLA indicators.
- **Automation & Workflows** – triggers, canned responses, auto‑closure.
- **Reporting & Dashboards** – live metrics, historical reports, CSV/PDF export.

---

## 6. Non‑Functional Requirements (quantified)

### 6.1 Usability

- Modern UI with clean typography, ample whitespace, responsive design (desktop & mobile).
- Ticket creation ≤ 3 clicks.

### 6.2 Performance

- Page load ≤ 2 seconds (3G connection).
- Global search ≤ 1 second for up to 10 k records.

### 6.3 Security

- SSO via SAML/OAuth (Google Workspace, Microsoft Entra ID).
- RBAC with strict data visibility.
- Encryption at rest and in transit (HTTPS/TLS).

---

## 7. Migration from Spiceworks (Checklist)

- **Export** tickets, users, KB articles to CSV.
- **Validate** data integrity (field mapping, duplicate handling).
- **Import** using provided migration script.
- **Verify** sample tickets and KB articles in the new system.
- **Retire** Spiceworks after sign‑off.

---

## 8. Glossary

- **SLA** – Service Level Agreement.
- **KB** – Knowledge Base.
- **SSO** – Single Sign‑On.
- **RBAC** – Role‑Based Access Control.

---

## 9. Approval Sign‑Off (Versioned)

| Name | Role                         | Signature | Date |
| ---- | ---------------------------- | --------- | ---- |
|      | IT Manager / Project Sponsor |           |      |
|      | Lead IT Technician           |           |      |
|      | Product Owner                |           |      |

_Document version: v1.1 (2026‑02‑20)_
