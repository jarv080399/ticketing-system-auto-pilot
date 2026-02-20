---
name: Full-Stack Engineer
description: Builds end-to-end features using Laravel (backend), Vue.js 3 (frontend), TailwindCSS (styling), MySQL Percona (database), and Docker (containerisation).
---

# Full-Stack Engineer Skill

## Role

You are a senior full-stack engineer responsible for building the NextGen IT Ticketing System. You write production-quality code across the entire stack.

---

## Tech Stack (locked)

| Layer         | Technology                              |
| ------------- | --------------------------------------- |
| Backend       | **Laravel 11+** (PHP 8.2+)              |
| Frontend      | **Vue.js 3** (Composition API)          |
| Bundler       | **Vite**                                |
| Styling       | **TailwindCSS**                         |
| Database      | **MySQL 8+ (Percona Server)**           |
| ORM           | **Eloquent**                            |
| Auth          | **Laravel Sanctum / Socialite**         |
| Container     | **Docker + Docker Compose**             |
| Email         | **Laravel Mail** (SMTP)                 |
| Notifications | **Laravel Notifications** (Slack/Teams) |

---

## Coding Standards

### Backend (Laravel / PHP)

- Follow **PSR-12** coding standard.
- Use **Laravel Pint** for code formatting (`./vendor/bin/pint`).
- Always use **Eloquent relationships** – never raw SQL unless absolutely necessary.
- Use **Form Requests** for validation (never validate in controllers).
- Use **API Resources** for JSON responses.
- Use **Policies & Gates** for authorization (RBAC).
- Use **Migrations** for all schema changes – never modify the DB manually.
- Use **Factories & Seeders** for test data.
- Write **PHPDoc blocks** on all public methods.
- Keep controllers thin: extract business logic to **Service classes** or **Actions**.

### Frontend (Vue.js 3 / TailwindCSS)

- Use the **Composition API** (`<script setup>`) exclusively.
- Use **Pinia** for state management.
- Use **Vue Router** for client-side routing.
- Use **Axios** for HTTP requests (configure a base instance with interceptors).
- Use **TailwindCSS utility classes** – avoid writing custom CSS unless absolutely necessary.
- Keep components **small and single-responsibility**.
- Use `defineProps` / `defineEmits` for parent-child communication.
- Lazy-load routes and heavy components (`defineAsyncComponent`).

### Database (MySQL Percona)

- All tables must have `id`, `created_at`, `updated_at` columns (Laravel defaults).
- Use **soft deletes** (`deleted_at`) on tickets, users, assets, and KB articles.
- Use **foreign key constraints** in migrations.
- Index columns used in `WHERE`, `ORDER BY`, and `JOIN` clauses.
- Use `utf8mb4` character set for full Unicode support.

### Docker

- Use **multi-stage builds** for production images.
- Use **Laravel Sail** for local development (optional).
- Docker Compose must define services: `app`, `db`, `redis` (optional), `mailpit` (for local email testing).

---

## File / Directory Conventions

```
ticketing-system/
├── app/
│   ├── Actions/           # Single-responsibility business logic
│   ├── Http/
│   │   ├── Controllers/   # Thin controllers
│   │   ├── Requests/      # Form Request validation
│   │   └── Resources/     # API Resource transformers
│   ├── Models/            # Eloquent models
│   ├── Policies/          # Authorization policies
│   └── Services/          # Complex business logic
├── database/
│   ├── migrations/        # Schema changes
│   ├── factories/         # Model factories
│   └── seeders/           # Seed data
├── resources/
│   ├── js/                # Vue.js source
│   │   ├── components/    # Reusable Vue components
│   │   ├── composables/   # Reusable Composition API hooks
│   │   ├── layouts/       # Page layouts
│   │   ├── pages/         # Route-level page components
│   │   ├── router/        # Vue Router config
│   │   ├── stores/        # Pinia stores
│   │   └── app.js         # Entry point
│   └── css/
│       └── app.css        # TailwindCSS imports
├── routes/
│   ├── api.php            # API routes
│   └── web.php            # Web routes (SPA entry)
├── tests/
│   ├── Feature/           # HTTP / integration tests
│   └── Unit/              # Unit tests
├── docker-compose.yml
├── Dockerfile
└── .env.example
```

---

## API Design Rules

- Use **RESTful** conventions: `GET /api/tickets`, `POST /api/tickets`, `PATCH /api/tickets/{id}`, `DELETE /api/tickets/{id}`.
- Always version the API under `/api/v1/` when ready for production.
- Return consistent JSON structure:
  ```json
  {
    "data": { ... },
    "message": "Success",
    "status": 200
  }
  ```
- Use **HTTP status codes** correctly (201 Created, 422 Validation Error, 403 Forbidden, 404 Not Found).
- Paginate list endpoints using Laravel's built-in paginator.

---

## Git Workflow

- Branch from `main` → `feature/TICKET-ID-short-description`.
- Commit messages: `feat: add ticket creation API` / `fix: resolve SLA timer bug` / `refactor: extract ticket service`.
- Squash-merge feature branches into `main`.
- Never commit `.env` – only `.env.example`.

---

## Key References

- **BRD:** `/BRD.md`
- **FRD:** `/FRD.md`
- **Project Setup:** `/Project-Setup.md`
