# Project Setup – NextGen IT Ticketing System

---

## 1. Vision & Success Metrics (from BRD)

- **First‑response time:** ≤ 15 minutes for 90 % of tickets.
- **Average resolution time:** ≤ 4 hours for 80 % of tickets.
- **User satisfaction (CSAT):** ≥ 4.5 / 5.
- **Ticket‑creation clicks:** ≤ 3 clicks for end‑users.
- **Admin overhead reduction:** 50 % fewer manual routing rules compared to Spiceworks.

---

## 2. High‑Level Architecture Overview

> **Note:** Replace the placeholder image with a diagram when ready.

```
+-------------------+      +-------------------+      +-------------------+
|   Web Front‑End   | ---> |   Backend API     | ---> | MySQL Percona DB  |
|   (Vue3/Vite)    |      |   (Laravel/PHP)   |      |   (Tickets, KB,   |
+-------------------+      +-------------------+      |    Assets, Users) |
        ^   |                     ^   |                +-------------------+
        |   |                     |   |
        |   |                     |   |
        |   |                     |   |
        |   |                     |   |
        |   |                     |   |
        |   |                     |   |
        |   |                     |   |
        |   |                     |   |
        |   |                     |   |
        |   |                     |   |
        |   |                     |   |
        |   |                     |   |
        |   |                     |   |
        |   |                     |   |
        |   |                     |   |
        |   |                     |   |
        |   |                     |   |
        |   |                     |   |
        |   |                     |   |
        |   |                     |   |
        |   |                     |   |
        v   v                     v   v
+-------------------+      +-------------------+      +-------------------+
|   Auth Service    |      |   Knowledge Base  |      |   Email Service   |
|   (SSO SAML/OAuth)|      |   (KB API)        |      |   (SMTP/IMAP)     |
+-------------------+      +-------------------+      +-------------------+
```

---

## 3. Technology Stack

| Layer             | Technology                                                         |
| ----------------- | ------------------------------------------------------------------ | --- |
| Front‑end         | **Vue.js 3** + **Vite** (Composition API, hot‑module reload)       |
| UI Library        | **TailwindCSS** (optional – can be swapped for vanilla CSS)        |
| Backend           | **Laravel 11+** (PHP 8.2+)                                         |
| Database          | **MySQL 8+ (Percona Server)**                                      |     |
| Auth              | **Laravel Socialite / SSO** (Google Workspace, Microsoft Entra ID) |
| CI/CD             | **GitHub Actions** (lint, test, build, deploy)                     |
| Containerisation  | **Docker** (multi‑stage build, Laravel Sail for local)             |
| Email Integration | **Laravel Mail** (SMTP)                                            |
| Messaging         | **Slack / Teams** via Laravel Notifications                        |

---

## 4. Data Model (simplified ER diagram description)

| Table              | Primary Key | Important Columns                                                                                                               |
| ------------------ | ----------- | ------------------------------------------------------------------------------------------------------------------------------- |
| `users`            | `id`        | `email`, `name`, `role`, `department`, `manager_id`                                                                             |
| `tickets`          | `id`        | `title`, `description`, `status`, `priority`, `category`, `created_at`, `updated_at`, `requester_id`, `assignee_id`, `asset_id` |
| `assets`           | `id`        | `serial_number`, `type`, `owner_user_id`, `description`                                                                         |
| `kb_articles`      | `id`        | `title`, `content`, `visibility` (`public`/`internal`), `created_at`, `updated_at`                                              |
| `automation_rules` | `id`        | `name`, `condition_json`, `action_json`, `priority`                                                                             |

> **Tip:** Use a tool like **draw.io** or **dbdiagram.io** to turn the table list into a visual ER diagram and store it under `docs/ER-diagram.png`.

---

## 5. API Specification (OpenAPI placeholder)

Create a file `api/openapi.yaml` with the following top‑level skeleton:

```yaml
openapi: 3.0.3
info:
  title: NextGen Ticketing System API
  version: 1.0.0
servers:
  - url: https://api.example.com/v1
paths:
  /tickets:
    get:
      summary: List tickets
      responses:
        "200":
          description: A list of tickets
    post:
      summary: Create a new ticket
      requestBody:
        required: true
        content:
          application/json:
            schema:
              $ref: "#/components/schemas/TicketCreate"
      responses:
        "201":
          description: Ticket created
  /tickets/{id}:
    get:
      summary: Get ticket details
    patch:
      summary: Update ticket fields
    delete:
      summary: Delete a ticket
components:
  schemas:
    TicketCreate:
      type: object
      required: [title, description, category]
      properties:
        title:
          type: string
        description:
          type: string
        category:
          type: string
        priority:
          type: string
          enum: [Low, Medium, High]
```

Add the remaining endpoints (users, assets, KB, automation) as the project evolves.

---

## 6. User Stories (MVP) – derived from BRD/FRD

| ID    | As a…        | I want…                                     | So that…                                | Acceptance Criteria                                                                                             |
| ----- | ------------ | ------------------------------------------- | --------------------------------------- | --------------------------------------------------------------------------------------------------------------- |
| US001 | End‑User     | Submit a ticket in ≤ 3 clicks               | I can get help quickly                  | • Form has only required fields<br>• Confirmation email sent<br>• Ticket appears in my portal with status _New_ |
| US002 | End‑User     | Search the Knowledge Base before submitting | I can self‑solve if possible            | • Search returns results in ≤ 1 s<br>• Top‑3 articles displayed as suggestions                                  |
| US003 | IT Agent     | Claim a ticket from a real‑time queue       | I can start working immediately         | • Queue updates via WebSocket<br>• Claim button assigns ticket to me and changes status to _In Progress_        |
| US004 | IT Agent     | Use a macro to resolve common requests      | I save time on repetitive work          | • Macro sends a templated reply and sets status to _Resolved_                                                   |
| US005 | IT Manager   | View a dashboard of SLA compliance          | I can spot breaches early               | • Dashboard shows tickets approaching SLA in red                                                                |
| US006 | System Admin | Configure SSO integration                   | Users log in with corporate credentials | • SSO login redirects to IdP and returns a JWT token                                                            |
| US007 | System Admin | Import historic Spiceworks data             | Migration is painless                   | • CSV import wizard maps fields and creates tickets/KB articles                                                 |

---

## 7. Backlog Prioritisation (MVP vs. Future)

- **MVP (must‑have for launch)**: US001‑US006.
- **Future Enhancements**: Asset‑ticket auto‑linking, advanced reporting, multi‑tenant support, AI‑suggested responses, mobile‑native app.

---

## 8. Wireframes / UI Mockups

> Place low‑fidelity sketches or generated images in `docs/wireframes/`.

- Self‑Service Portal home screen
- Ticket creation modal
- Agent Kanban board
- Admin settings page

---

## 9. Definition of Done (DoD)

- Code merged to `main` with **peer review**.
- **Unit tests** ≥ 80 % coverage for new modules.
- **Integration tests** for API endpoints (using Pest/PHPUnit).
- **Lint** passes (PHP_CodeSniffer/Pint for backend, ESLint/Prettier for frontend).
- **Documentation** updated (README, API spec, user guide).
- **CI pipeline** reports green.
- **Deploy** to staging environment for stakeholder demo.

---

## 10. CI/CD Pipeline Blueprint (GitHub Actions)

Create `.github/workflows/ci.yml` with these jobs:

1.  **lint** – `composer lint` & `npm run lint`
2.  **test** – `php artisan test` & `npm run test`
3.  **build** – Docker multi‑stage build (`docker build -t ticketing-system .`)
4.  **deploy‑staging** – on push to `develop` (run `docker compose up -d` on staging server).
5.  **release** – on tag `v*` → push Docker image to registry and deploy to production.

---

## 11. Environment & Secrets Plan

| Variable                              | Description                      | Source                 |
| ------------------------------------- | -------------------------------- | ---------------------- |
| `DB_CONNECTION`, `DB_HOST`, ...       | MySQL Percona connection details | GitHub Secrets         |
| `SSO_CLIENT_ID` / `SSO_CLIENT_SECRET` | SSO credentials                  | Vault / GitHub Secrets |
| `MAIL_HOST`, `MAIL_USERNAME`, ...     | Email service                    | GitHub Secrets         |
| `SLACK_WEBHOOK_URL`                   | Alerts channel                   | GitHub Secrets         |
| `APP_KEY`                             | Laravel App Key                  | Vault                  |

---

## 12. Testing Strategy

- **Unit Tests** – Vitest/Jest for frontend, Pest/PHPUnit for backend.
- **API Integration Tests** – Pest/PHPUnit HTTP tests with a test database.
- **End‑to‑End UI Tests** – Laravel Dusk or Cypress covering ticket creation flow.
- **Performance Tests** – k6 script for search latency (< 1 s).
- **Security Tests** – OWASP ZAP scan on staging.

---

## 13. Security & Compliance Checklist

- ✅ Enforce **HTTPS** everywhere.
- ✅ Store passwords only as **hashed (bcrypt)**.
- ✅ Use **RBAC** for every endpoint.
- ✅ Validate all inputs (schema validation via `zod`).
- ✅ Regular **dependency audit** (`npm audit`).
- ✅ Log all authentication events for audit.

---

## 14. Release & Roll‑out Plan

1. **Alpha** – internal QA on a sandbox environment.
2. **Beta** – limited group of employees (invite‑only) for real‑world feedback.
3. **Production Launch** – after sign‑off, enable SSO for all users.
4. **Rollback** – keep previous Docker image tag; `docker compose down && docker compose up -d` with old tag if critical bug.

---

## 15. Documentation Plan

- `README.md` – quick start, dev setup, build & run instructions.
- `docs/architecture.md` – description + diagram.
- `docs/api.md` – generated from OpenAPI (Swagger UI).
- `docs/user‑guide.md` – how end‑users submit tickets, use KB.
- `docs/admin‑guide.md` – SSO config, import process, automation rule editor.

---

_All placeholders (diagrams, OpenAPI file, wireframes) should be added as the project progresses._
