---
name: Data Engineer
description: Designs, maintains, and optimises the MySQL Percona database schema, writes migrations, seeds data, handles the Spiceworks CSV migration, and ensures query performance for the ticketing system.
---

# Data Engineer Skill

## Role

You are a senior data engineer responsible for all database design, migration, data integrity, and performance optimisation work on the NextGen IT Ticketing System.

---

## Tech Stack

| Layer      | Technology                                            |
| ---------- | ----------------------------------------------------- |
| Database   | **MySQL 8+ (Percona Server)**                         |
| ORM        | **Eloquent** (Laravel)                                |
| Migrations | **Laravel Migrations** (`php artisan make:migration`) |
| Seeding    | **Laravel Factories & Seeders**                       |
| Admin Tool | **phpMyAdmin** or **TablePlus** (local)               |
| Monitoring | **Percona Monitoring & Management (PMM)**             |
| Container  | **Docker** (Percona 8 image)                          |

---

## Database Design Standards

### Naming Conventions

- **Tables:** plural, snake_case (`tickets`, `kb_articles`, `automation_rules`).
- **Columns:** singular, snake_case (`created_at`, `assignee_id`, `serial_number`).
- **Foreign Keys:** `{related_table_singular}_id` (e.g., `requester_id`, `assignee_id`, `asset_id`).
- **Pivot Tables:** alphabetical order, singular (`asset_ticket`, `role_user`).
- **Indexes:** `idx_{table}_{column}` (e.g., `idx_tickets_status`).

### Column Rules

- Every table must have: `id` (BIGINT UNSIGNED AUTO_INCREMENT), `created_at`, `updated_at`.
- Use **soft deletes** (`deleted_at` TIMESTAMP NULL) on: `users`, `tickets`, `assets`, `kb_articles`.
- Use `utf8mb4` charset and `utf8mb4_unicode_ci` collation on all tables.
- Use `ENUM` sparingly – prefer a **lookup/reference table** or a `VARCHAR` with application-level validation for extensibility.
- Store JSON data in `JSON` columns (e.g., `automation_rules.condition_json`) – always validate structure in the application layer.

### Foreign Key & Referential Integrity

- Always define foreign key constraints in migrations.
- Use `onDelete('cascade')` for child records that make no sense without a parent (e.g., `ticket_comments` when a ticket is deleted).
- Use `onDelete('set null')` for optional relationships (e.g., `tickets.assignee_id` when an agent is removed).
- Use `onDelete('restrict')` to prevent accidental deletion of referenced records (e.g., a user with open tickets).

---

## Core Schema (Entity Relationship)

### Tables & Key Relationships

```
users
  ├── id (PK)
  ├── name, email, password, role, department, manager_id (FK → users.id)
  ├── created_at, updated_at, deleted_at
  │
  ├── tickets (as requester)   → tickets.requester_id
  ├── tickets (as assignee)    → tickets.assignee_id
  └── assets (as owner)        → assets.owner_user_id

tickets
  ├── id (PK)
  ├── ticket_number (UNIQUE, auto-generated, e.g., TKT-00001)
  ├── title, description, status, priority, category
  ├── requester_id (FK → users.id)
  ├── assignee_id  (FK → users.id, NULLABLE)
  ├── asset_id     (FK → assets.id, NULLABLE)
  ├── sla_due_at   (TIMESTAMP, NULLABLE)
  ├── resolved_at  (TIMESTAMP, NULLABLE)
  ├── created_at, updated_at, deleted_at
  │
  └── ticket_comments → ticket_comments.ticket_id

ticket_comments
  ├── id (PK)
  ├── ticket_id (FK → tickets.id)
  ├── user_id   (FK → users.id)
  ├── body (TEXT)
  ├── is_internal (BOOLEAN, default false)
  ├── created_at, updated_at

assets
  ├── id (PK)
  ├── serial_number (UNIQUE), type, description
  ├── owner_user_id (FK → users.id, NULLABLE)
  ├── created_at, updated_at, deleted_at

kb_articles
  ├── id (PK)
  ├── title, content (LONGTEXT), visibility (public/internal)
  ├── author_id (FK → users.id)
  ├── created_at, updated_at, deleted_at

automation_rules
  ├── id (PK)
  ├── name, condition_json (JSON), action_json (JSON)
  ├── priority (INT), is_active (BOOLEAN)
  ├── created_at, updated_at

audit_logs
  ├── id (PK)
  ├── user_id (FK → users.id, NULLABLE)
  ├── action (VARCHAR), auditable_type, auditable_id
  ├── old_values (JSON, NULLABLE), new_values (JSON, NULLABLE)
  ├── created_at
```

---

## Migration Best Practices

- One migration per logical change (e.g., `create_tickets_table`, `add_sla_due_at_to_tickets`).
- Never edit a migration that has already been run in staging/production – create a new one.
- Always provide a `down()` method for rollback.
- Run `php artisan migrate:fresh --seed` in CI to validate the full migration chain.

---

## Seeder Strategy

- **DatabaseSeeder** calls individual seeders in dependency order:
  1. `UserSeeder` (admin, agents, sample end-users)
  2. `AssetSeeder` (sample laptops/monitors)
  3. `KbArticleSeeder` (5–10 sample articles)
  4. `TicketSeeder` + `TicketCommentSeeder` (20–50 sample tickets with threaded comments)
  5. `AutomationRuleSeeder` (default routing rules)
- Use **Factories** for randomised data (`Ticket::factory()->count(50)->create()`).
- Seed sensible defaults for `status` (New, In Progress, Waiting on Customer, Resolved, Closed) and `priority` (Low, Medium, High, Critical).

---

## Spiceworks Migration Plan

### Step 1: Export from Spiceworks

- Export tickets, users, and KB articles to CSV files.
- Expected files: `spiceworks_tickets.csv`, `spiceworks_users.csv`, `spiceworks_kb.csv`.

### Step 2: Field Mapping

| Spiceworks Field | System Field           | Notes                                    |
| ---------------- | ---------------------- | ---------------------------------------- |
| `summary`        | `tickets.title`        | Direct mapping                           |
| `description`    | `tickets.description`  | HTML → plain text conversion             |
| `status`         | `tickets.status`       | Map "Open" → "New", "Closed" → "Closed"  |
| `priority`       | `tickets.priority`     | Map 1→Critical, 2→High, 3→Medium, 4→Low  |
| `created_on`     | `tickets.created_at`   | Parse Spiceworks date format             |
| `assigned_to`    | `tickets.assignee_id`  | Match by email, set NULL if not found    |
| `creator_email`  | `tickets.requester_id` | Match by email, create user if not found |

### Step 3: Import Command

- Create an Artisan command: `php artisan import:spiceworks {file} {--type=tickets|users|kb}`
- Validate each row before insert.
- Log skipped/failed rows to `storage/logs/import.log`.
- Run inside a database transaction for atomicity.

### Step 4: Verification

- Compare row counts: Spiceworks export vs. imported records.
- Spot-check 10 random tickets for data accuracy.
- Verify foreign key integrity (no orphaned records).

---

## Performance Optimisation

### Indexing Strategy

```sql
-- Tickets (most queried table)
CREATE INDEX idx_tickets_status ON tickets(status);
CREATE INDEX idx_tickets_priority ON tickets(priority);
CREATE INDEX idx_tickets_category ON tickets(category);
CREATE INDEX idx_tickets_assignee ON tickets(assignee_id);
CREATE INDEX idx_tickets_requester ON tickets(requester_id);
CREATE INDEX idx_tickets_created ON tickets(created_at);
CREATE INDEX idx_tickets_sla ON tickets(sla_due_at);

-- KB Articles (search)
CREATE FULLTEXT INDEX idx_kb_search ON kb_articles(title, content);

-- Audit Logs (lookup)
CREATE INDEX idx_audit_auditable ON audit_logs(auditable_type, auditable_id);
```

### Query Guidelines

- Use **Eager Loading** (`Ticket::with(['requester', 'assignee', 'asset'])->get()`) to avoid N+1 queries.
- Paginate all list endpoints (default 25 per page, max 100).
- Use `SELECT` only the columns you need (avoid `SELECT *`).
- Cache frequently-read, rarely-changed data (e.g., automation rules, KB articles) using Laravel Cache (Redis or file driver).

### Monitoring

- Enable **slow query log** (queries > 500ms) in Percona.
- Use `EXPLAIN` on any new query that touches > 1,000 rows.
- Set up alerts for: connection pool exhaustion, deadlocks, and replication lag (if applicable).

---

## Data Retention Policy

- **Tickets:** Retain for 2 years after closure, then archive.
- **Audit Logs:** Retain for 1 year, then purge.
- **Soft-Deleted Records:** Permanently purge after 90 days via scheduled Artisan command.

---

## Key References

- **BRD:** `/BRD.md`
- **FRD:** `/FRD.md`
- **Project Setup:** `/Project-Setup.md`
- **Full-Stack Skill:** `.agent/skills/fullstack-engineer/SKILL.md`
