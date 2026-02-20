# Module 1: Authentication & User Management

**Objective:** Set up secure login via SSO, establish Role-Based Access Control (RBAC), and define the foundational `User` model with complete identity lifecycle management.

---

## Step 1: Database & Models (Data Engineer)

- [ ] Migrate `users` table: add `role` (enum or string), `department`, `manager_id` (foreign key to `users`), `avatar_url`, `timezone`, `locale`, and `deleted_at` (SoftDeletes).
- [ ] Create `password_reset_tokens` table (Laravel default, verify exists).
- [ ] Create `personal_access_tokens` table (Sanctum, verify exists).
- [ ] Create `user_sessions` table to track active login sessions (`user_id`, `ip_address`, `user_agent`, `last_active_at`).
- [ ] Implement `UserFactory` and `UserSeeder` with sample End-Users, Agents, and Admins.
- [ ] Define relationships: `$user->manager()`, `$user->directReports()`, `$user->sessions()`.

## Step 2: Authentication API (Full-Stack Engineer)

- [ ] Install **Laravel Sanctum** for SPA token authentication.
- [ ] Install **Laravel Socialite** for SSO (Google Workspace / Microsoft Entra ID).
- [ ] Create `AuthController` with endpoints:
  - `POST /api/v1/auth/login` (email/password)
  - `POST /api/v1/auth/logout` (revoke current token)
  - `POST /api/v1/auth/forgot-password` (send reset email)
  - `POST /api/v1/auth/reset-password` (validate token + set new password)
  - `GET /api/v1/auth/me` (return authenticated user, roles, permissions)
- [ ] SSO callback routes: `GET /api/v1/auth/sso/{provider}/redirect`, `GET /api/v1/auth/sso/{provider}/callback`.
- [ ] Implement **token revocation** (logout all devices): `POST /api/v1/auth/revoke-all`.

## Step 3: Authorization & RBAC (Full-Stack Engineer)

- [ ] Define Gate definitions or standard Laravel Policies (`UserPolicy`, `TicketPolicy` (stubbed)).
- [ ] Implement role-based middleware (`role:agent,admin`) to route guard endpoints.
- [ ] Create `PermissionSeeder` seeding a basic permission matrix:
      | Permission | End-User | Agent | Admin |
      |---|---|---|---|
      | Create own ticket | ✅ | ✅ | ✅ |
      | View all tickets | ❌ | ✅ | ✅ |
      | Manage users | ❌ | ❌ | ✅ |
      | Configure system | ❌ | ❌ | ✅ |

## Step 4: User Provisioning & Bulk Import (Full-Stack Engineer)

- [ ] Create Artisan command `users:import {csv_file}` for bulk user creation from a CSV (Spiceworks migration).
- [ ] Expose `POST /api/v1/admin/users` for manual user creation (Admin only).
- [ ] Expose `PATCH /api/v1/admin/users/{id}` for role/department updates.
- [ ] Expose `DELETE /api/v1/admin/users/{id}` (soft-delete).
- [ ] Implement `GET /api/v1/admin/users` with search, filter (role, department), and pagination.

## Step 5: Frontend Auth State (Vue 3)

- [ ] Setup Pinia `useAuthStore` to securely store the active user and handle route guards (`beforeEach`).
- [ ] Build `LoginPage.vue` matching the application's design system (Tailwind), including:
  - Email/password form with validation.
  - "Sign in with Google" / "Sign in with Microsoft" SSO buttons.
  - "Forgot Password?" link.
- [ ] Build `ForgotPasswordPage.vue` and `ResetPasswordPage.vue`.
- [ ] Build `ProfilePage.vue` with avatar, name, email, department, timezone, and session management (view active sessions, revoke others).
- [ ] Implement **idle timeout** (auto-logout after configurable inactivity).

## Step 6: Testing (QA Engineer)

- [ ] **Backend (Pest):** Secure route access; verify non-admins get `403 Forbidden` when accessing admin endpoints.
- [ ] **Backend (Pest):** Socialite authentication mock test (Google, Microsoft).
- [ ] **Backend (Pest):** Password reset flow end-to-end (request → email → reset → login).
- [ ] **Backend (Pest):** Token revocation removes all sessions.
- [ ] **Backend (Pest):** Bulk user import from CSV (valid rows succeed, invalid rows logged).
- [ ] **Frontend (Vitest):** Auth store state changes on login/logout.
- [ ] **Frontend (Vitest):** Route guard redirects unauthenticated users to `/login`.
- [ ] **E2E (Cypress):** US006 - Admin configures SSO (or standard auth failure flows).
- [ ] **E2E (Cypress):** Full login → profile → logout flow.
