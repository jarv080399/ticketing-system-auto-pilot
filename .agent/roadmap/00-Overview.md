# Development Roadmap: NextGen Ticketing System

This directory contains the step-by-step implementation plan for the **NextGen IT Ticketing System**, broken down into independent modules that correspond to the Functional Requirements Document (FRD) and industry best practices (ITIL, Zendesk/Freshdesk/Jira SM feature parity).

## Modules Overview

- [Module 1: Authentication & User Management](./01-Authentication.md)
- [Module 2: End-User Self-Service Portal](./02-Self-Service-Portal.md)
- [Module 3: Agent Workspace](./03-Agent-Workspace.md)
- [Module 4: Automation Engine](./04-Automation-Engine.md)
- [Module 5: Knowledge Base (KB)](./05-Knowledge-Base.md)
- [Module 6: Asset Tracking](./06-Asset-Tracking.md)
- [Module 7: Reporting & Analytics](./07-Reporting-Analytics.md)
- [Module 8: Notifications & Multi-Channel](./08-Notifications-Multichannel.md) ← **NEW**
- [Module 9: Settings & System Administration](./09-Settings-Admin.md) ← **NEW**

## Build Sequence Strategy

1. **Foundation Phase:** Module 1 (Auth, Users, Roles) + Module 9 (System settings scaffold). The base for everything else.
2. **Core Operations Phase:** Module 2 (Self-Service Portal) and Module 3 (Agent Workspace). These provide the MVP ticket lifecycle (create, assign, resolve).
3. **Communication Phase:** Module 8 (Notifications, Email-to-Ticket, Slack/Teams). Connects the system to the outside world.
4. **Enhancement Phase:** Module 5 (KB) and Module 6 (Assets). Ties data into the ticketing flows.
5. **Automation & Insights Phase:** Module 4 (Engine) and Module 7 (Reports + CSAT). Maximises efficiency and tracks success metrics.

## Engineering Workflow for Each Module

1. Data Engineer drafts migrations & factories/seeders.
2. Full-Stack Engineer builds Eloquent models, controllers, and FormRequests.
3. QA Engineer writes backend Feature/Unit Tests for API validation.
4. Full-Stack Engineer develops the Vue components and Pinia state.
5. QA Engineer finishes Cypress/Dusk E2E UI Tests.
6. Module is merged. CI/CD runs. Developer moves to the next module.

## Cross-Cutting Concerns (applied to every module)

- **Audit Logging:** Every write operation must create an `audit_logs` entry.
- **Rate Limiting:** All public-facing API endpoints use Laravel's `throttle` middleware.
- **Input Sanitisation:** All user-submitted HTML/Markdown is sanitised server-side.
- **Accessibility (a11y):** Every Vue component must pass WCAG 2.1 AA checks.
- **Internationalisation (i18n):** Use Laravel's `__()` helper and Vue I18n for all user-facing strings (English as default, structure for future locales).
