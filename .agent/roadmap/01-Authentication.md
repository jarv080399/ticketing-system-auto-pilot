# Module 1: Authentication & User Management

**Objective:** Set up secure login via SSO, establish Role-Based Access Control (RBAC), and define the foundational `User` model.

## Step 1: Database & Models (Data Engineer)

- [ ] Migrate `users` table: add `role` (enum or string), `department`, `manager_id` (foreign key to `users`), and `deleted_at` (SoftDeletes).
- [ ] Implement `UserFactory` and `UserSeeder` with sample End-Users, Agents, and Admins.
- [ ] Define relationship: `$user->manager()`.

## Step 2: Authentication API (Full-Stack Engineer)

- [ ] Install **Laravel Socialite** (if not already via initial setup) for SSO (Google/Microsoft Entra).
- [ ] Create `AuthController` handling standard email/password fallback (via Laravel Sanctum).
- [ ] Expose `GET /api/v1/auth/me` to return authenticated user details, roles, and permissions.

## Step 3: Authorization (Full-Stack Engineer)

- [ ] Establish Gate definitions or define standard Laravel Policies (`UserPolicy`, `TicketPolicy` (stubbed)).
- [ ] Implement role-based middleware (`role:agent,admin`) to route guard endpoints.

## Step 4: Frontend Auth State (Vue 3)

- [ ] Setup Pinia `useAuthStore` to securely store the active user and handle route guards (`beforeEach`).
- [ ] Build `Login.vue` page matching the application’s design system (Tailwind).
- [ ] Implement basic profile auto-population UI (Name, Email, Department).

## Step 5: Testing (QA Engineer)

- [ ] **Backend (Pest):** Secure route access; verify non-admins get a `403 Forbidden` when accessing admin endpoints.
- [ ] **Backend (Pest):** Socialite authentication mock test.
- [ ] **Frontend (Vitest):** Auth store state changes on login.
- [ ] **E2E (Cypress):** US006 - Admin configures SSO (or standard auth failure flows).
