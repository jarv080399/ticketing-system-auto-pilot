# Standard Development Rules & Best Practices

## 1. Environment & Secrets (`.env`)

- **Never commit** `.env` or any file containing secrets to version control.
- Keep a **`.env.example`** with all required keys (no values) at the repository root.
- Use **GitHub Secrets** (or a vault) for CI/CD pipelines; inject them into the container at runtime.
- Required keys for this project:

  ```dotenv
  APP_NAME=TicketingSystem
  APP_ENV=local
  APP_KEY=base64:GENERATE_WITH_ARTISAN
  APP_DEBUG=true
  APP_URL=http://localhost

  DB_CONNECTION=mysql
  DB_HOST=db
  DB_PORT=3306
  DB_DATABASE=ticketing
  DB_USERNAME=ticketing_user
  DB_PASSWORD=secure_password

  SSO_CLIENT_ID=
  SSO_CLIENT_SECRET=
  MAIL_HOST=
  MAIL_USERNAME=
  MAIL_PASSWORD=
  SLACK_WEBHOOK_URL=
  ```

- **Regenerate** `APP_KEY` after cloning: `php artisan key:generate`.
- Use **dotenv** validation libraries (e.g., `vlucas/phpdotenv` with `required` flag) to fail fast on missing vars.

## 2. Laravel MVC & Architecture

| Layer                         | Responsibility                                                                                         |
| ----------------------------- | ------------------------------------------------------------------------------------------------------ |
| **Models**                    | Eloquent entities, relationships, casts, accessors/mutators. No business logic.                        |
| **Controllers**               | Thin – delegate to **Form Requests** for validation and **Service/Action** classes for business logic. |
| **Requests**                  | Validation rules, authorize methods.                                                                   |
| **Resources**                 | Transform models to API JSON shape.                                                                    |
| **Policies**                  | Centralised authorization (RBAC).                                                                      |
| **Services / Actions**        | Re‑usable business logic, injected via constructor or container.                                       |
| **Repositories** _(optional)_ | Abstract DB access for complex queries; keep queries out of controllers.                               |

### Naming Conventions

- **Models**: Singular, PascalCase (`Ticket`, `User`).
- **Tables**: Plural, snake_case (`tickets`, `users`).
- **Controllers**: Resource name + `Controller` (`TicketController`).
- **Form Requests**: `<Resource>Request` (`StoreTicketRequest`).
- **Resources**: `<Resource>Resource` (`TicketResource`).
- **Policies**: `<Resource>Policy` (`TicketPolicy`).
- **Service/Action**: `<Verb><Resource>Service` (`CreateTicketService`).

## 3. Vue.js 3 Framework Strategy

- **Composition API** only (`<script setup>` preferred).
- **Pinia** for global state; one store per domain (e.g., `useTicketStore`).
- **Vue Router** with lazy‑loaded routes (`defineAsyncComponent`).
- **Component Naming**: PascalCase, file name matches component (`TicketList.vue`).
- **Folder Structure**:
  ```
  src/
  ├─ components/          # Re‑usable UI pieces
  ├─ composables/         # Re‑usable hooks (useFetch, useForm)
  ├─ layouts/             # Page layouts (AppLayout, AuthLayout)
  ├─ pages/               # Route‑level components
  ├─ router/              # router/index.js
  ├─ stores/              # Pinia stores
  └─ assets/              # Images, icons
  ```
- **Props vs Emits**: Use `defineProps` for data in, `defineEmits` for events out.
- **Styling**: Tailwind utility classes; avoid custom CSS unless needed.
- **Testing**: Vue Test Utils + Vitest; mock API calls with `vi.mock('axios')`.
- **Accessibility**: Add `aria-*` attributes, ensure focus management.

## 4. Git Workflow & Branching

- **Branch naming**: `feature/<JIRA‑ID>-short-description`, `bugfix/<JIRA‑ID>-desc`, `hotfix/<desc>`.
- **Commit messages** follow Conventional Commits:
  - `feat:` new feature
  - `fix:` bug fix
  - `refactor:` code change without functional impact
  - `docs:` documentation updates
- **Pull Requests** must have:
  - Description of changes
  - Linked issue number
  - Checklist: lint passes, tests pass, migrations run, documentation updated.
- **Squash‑merge** into `main` to keep linear history.

## 5. Linting & Formatting

- **PHP**: `Laravel Pint` (PSR‑12) – run via `composer lint`.
- **JavaScript/TypeScript**: `ESLint` + `Prettier` – run via `npm run lint`.
- **Git Hooks**: Use `husky` to run lint & tests on `pre‑commit`.

## 6. CI/CD Pipeline (GitHub Actions)

- **Jobs**: `lint`, `test`, `build`, `docker‑push`, `deploy‑staging`.
- **Secrets**: Inject via `${{ secrets.VAR_NAME }}`.
- **Cache**: Composer and npm caches to speed up builds.
- **Artifacts**: Store test coverage reports.
- **Branch protection**: Require status checks and PR reviews before merging.

## 7. Docker & Containerisation

- **Multi‑stage Dockerfile**:
  - `builder` stage installs PHP extensions, Composer, runs `npm install && npm run build`.
  - `production` stage copies compiled assets, runs `php artisan config:cache`.
- **docker‑compose.yml** services:
  - `app` (Laravel PHP-FPM)
  - `nginx` (reverse proxy)
  - `db` (Percona MySQL)
  - `redis` (optional cache)
- **Environment variables** passed via `.env` file mounted as a volume (never baked into the image).

## 8. Documentation & API Spec

- Keep **OpenAPI** spec in `docs/openapi.yaml`.
- Generate API docs automatically via `php artisan l5-swagger`.
- Update README with setup steps, Docker commands, and testing instructions.

---

_These rules are intended to enforce a **consistent, secure, and maintainable** development experience across the whole ticketing‑system project._
